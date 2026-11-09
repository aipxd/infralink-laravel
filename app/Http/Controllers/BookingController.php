<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Booking;
use App\Models\BookingStatusLog;
use App\Models\Facility;
use App\Services\BookingRuleService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class BookingController extends Controller
{
    public function __construct(
        protected BookingRuleService $ruleService
    ) {}

    /**
     * Tampilkan form pengajuan (FR-30)
     */
    public function create(Request $request, $facilityId)
    {
        $facility = Facility::where('is_active', true)->findOrFail($facilityId);

        $defaultDate = $request->query('date', Carbon::now('Asia/Jakarta')->addDay()->format('Y-m-d'));
        $defaultStart = $request->query('start', $facility->open_time);
        $defaultEnd = $request->query('end', date('H:i', strtotime($facility->open_time . ' +2 hours')));

        return view('bookings.create', compact('facility', 'defaultDate', 'defaultStart', 'defaultEnd'));
    }

    /**
     * Submit pengajuan peminjaman (FR-30, FR-31, FR-32)
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'facility_id' => ['required', 'exists:facilities,id'],
            'booking_date' => ['required', 'date_format:Y-m-d'],
            'start_time' => ['required', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
            'end_time' => ['required', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
            'event_name' => ['required', 'string', 'max:200'],
            'purpose' => ['required', 'string', 'max:1000'],
            'participants' => ['required', 'integer', 'min:1'],
            'contact_phone' => ['required', 'string', 'max:20'],
            'document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // Max 5 MB
        ], [
            'booking_date.required' => 'Tanggal peminjaman wajib diisi.',
            'start_time.required' => 'Jam mulai wajib diisi.',
            'end_time.required' => 'Jam selesai wajib diisi.',
            'event_name.required' => 'Nama kegiatan wajib diisi.',
            'purpose.required' => 'Tujuan peminjaman wajib diisi.',
            'participants.required' => 'Jumlah peserta wajib diisi.',
            'contact_phone.required' => 'Nomor HP kontak peminjam wajib diisi.',
            'document.max' => 'Ukuran dokumen pendukung maksimal 5 MB.',
            'document.mimes' => 'Dokumen hanya boleh berformat PDF, JPG, JPEG, atau PNG.',
        ]);

        $facility = Facility::findOrFail($validated['facility_id']);

        if (!$facility->is_active) {
            return back()->withInput()->with('error', 'Fasilitas ini sedang dinonaktifkan.');
        }

        try {
            // Validasi aturan bisnis BR-03 s.d BR-09
            $this->ruleService->validateNewBooking($facility, $validated);

            $booking = DB::transaction(function () use ($user, $facility, $validated, $request) {
                // Kunci baris fasilitas untuk mencegah konkurensi ganda
                DB::table('facilities')->where('id', $facility->id)->lockForUpdate()->first();

                // Double check overlap
                if ($this->ruleService->hasConflict($facility->id, $validated['booking_date'], $validated['start_time'], $validated['end_time'])) {
                    throw new InvalidArgumentException("Jadwal pada tanggal dan rentang jam tersebut baru saja terisi. Silakan pilih jadwal lain.");
                }

                $newBooking = Booking::create([
                    'user_id' => $user->id,
                    'facility_id' => $facility->id,
                    'booking_date' => $validated['booking_date'],
                    'start_time' => $validated['start_time'],
                    'end_time' => $validated['end_time'],
                    'event_name' => $validated['event_name'],
                    'purpose' => $validated['purpose'],
                    'participants' => $validated['participants'],
                    'contact_phone' => $validated['contact_phone'],
                    'status' => 'DIAJUKAN',
                ]);

                // Simpan log status awal
                BookingStatusLog::create([
                    'booking_id' => $newBooking->id,
                    'from_status' => null,
                    'to_status' => 'DIAJUKAN',
                    'changed_by' => $user->id,
                    'note' => 'Pengajuan peminjaman fasilitas berhasil diajukan.',
                    'created_at' => Carbon::now('Asia/Jakarta'),
                ]);

                // Upload dokumen pendukung jika ada
                if ($request->hasFile('document')) {
                    $file = $request->file('document');
                    $path = $file->store('attachments', 'public');

                    Attachment::create([
                        'booking_id' => $newBooking->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'mime_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                        'uploaded_at' => Carbon::now('Asia/Jakarta'),
                    ]);
                }

                return $newBooking;
            });

            return redirect()->route('bookings.mine')->with('success', 'Pengajuan peminjaman berhasil dibuat dan menunggu persetujuan pengelola.');

        } catch (InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    /**
     * Halaman Peminjaman Saya (FR-40)
     */
    public function mine(Request $request)
    {
        $user = Auth::user();

        $query = Booking::with(['facility', 'statusLogs'])
            ->where('user_id', $user->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('booking_date', $request->date);
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('bookings.mine', compact('bookings'));
    }

    /**
     * Detail pengajuan dan riwayat status (FR-41, FR-42)
     */
    public function show($id)
    {
        $booking = Booking::with(['facility', 'user', 'decider', 'statusLogs.changer', 'attachments'])->findOrFail($id);
        $user = Auth::user();

        // Cek otorisasi akses (Pemilik, Approver, Admin)
        if ($booking->user_id !== $user->id && !$user->isApprover()) {
            abort(403, 'Anda tidak memiliki hak untuk melihat detail pengajuan ini.');
        }

        return view('bookings.show', compact('booking'));
    }

    /**
     * Pemohon membatalkan pengajuan (BR-10, FR-33)
     */
    public function cancel(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $user = Auth::user();

        if ($booking->user_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Anda tidak berhak membatalkan pengajuan ini.');
        }

        // BR-10: Hanya bisa membatalkan pengajuan berstatus DIAJUKAN
        if ($booking->status !== 'DIAJUKAN') {
            return back()->with('error', 'Hanya pengajuan berstatus DIAJUKAN yang dapat dibatalkan.');
        }

        DB::transaction(function () use ($booking, $user) {
            $booking->update([
                'status' => 'DIBATALKAN',
            ]);

            BookingStatusLog::create([
                'booking_id' => $booking->id,
                'from_status' => 'DIAJUKAN',
                'to_status' => 'DIBATALKAN',
                'changed_by' => $user->id,
                'note' => 'Pengajuan dibatalkan oleh pemohon.',
                'created_at' => Carbon::now('Asia/Jakarta'),
            ]);
        });

        return back()->with('success', 'Pengajuan peminjaman berhasil dibatalkan.');
    }

    /**
     * Download dokumen lampiran dengan otorisasi
     */
    public function downloadAttachment($attachmentId)
    {
        $attachment = Attachment::with('booking')->findOrFail($attachmentId);
        $user = Auth::user();

        if ($attachment->booking->user_id !== $user->id && !$user->isApprover()) {
            abort(403, 'Anda tidak memiliki hak untuk mengunduh dokumen ini.');
        }

        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, 'File lampiran tidak ditemukan di penyimpanan server.');
        }

        return Storage::disk('public')->download($attachment->file_path, $attachment->file_name);
    }
}
