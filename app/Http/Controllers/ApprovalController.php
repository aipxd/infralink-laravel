<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingStatusLog;
use App\Models\Facility;
use App\Services\BookingRuleService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ApprovalController extends Controller
{
    public function __construct(
        protected BookingRuleService $ruleService
    ) {}

    /**
     * Antrean persetujuan pengajuan peminjaman (FR-50)
     */
    public function index(Request $request)
    {
        $query = Booking::with(['facility', 'user'])
            ->where('status', 'DIAJUKAN');

        if ($request->filled('facility_id')) {
            $query->where('facility_id', $request->facility_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('booking_date', $request->date);
        }

        $bookings = $query->orderBy('created_at', 'asc')->paginate(15)->withQueryString();
        $facilities = Facility::where('is_active', true)->orderBy('name')->get();

        return view('approvals.index', compact('bookings', 'facilities'));
    }

    /**
     * Detail pengajuan untuk Approver (FR-51)
     */
    public function show($id)
    {
        $booking = Booking::with(['facility', 'user', 'statusLogs.changer', 'attachments'])->findOrFail($id);

        // Ambil pengajuan lain yang bersinggungan / pending overlap (FR-51)
        $conflictingPendingBookings = $this->ruleService->getPendingOverlaps($booking);

        // Ambil booking yang sudah DISETUJUI dan BLOKIR pada hari yang sama untuk kalender mini
        $dayApprovedBookings = Booking::with('user')
            ->where('facility_id', $booking->facility_id)
            ->whereDate('booking_date', $booking->booking_date)
            ->where('status', 'DISETUJUI')
            ->get();

        $dayBlockedSlots = $booking->facility->blockedSlots()
            ->whereDate('block_date', $booking->booking_date)
            ->get();

        return view('approvals.show', compact('booking', 'conflictingPendingBookings', 'dayApprovedBookings', 'dayBlockedSlots'));
    }

    /**
     * Setujui pengajuan dalam transaksi dengan row locking (BR-04, FR-52, FR-53)
     */
    public function approve(Request $request, $id)
    {
        $user = Auth::user();

        $request->validate([
            'decision_note' => ['nullable', 'string', 'max:500'],
        ]);

        $booking = Booking::findOrFail($id);

        // BR-13: Status akhir tidak bisa diubah kembali
        if ($booking->status !== 'DIAJUKAN') {
            return back()->with('error', "Pengajuan ini tidak dapat diproses karena statusnya sudah {$booking->status}.");
        }

        try {
            DB::transaction(function () use ($booking, $user, $request) {
                // Kunci baris fasilitas untuk mencegah approval race-condition
                DB::table('facilities')->where('id', $booking->facility_id)->lockForUpdate()->first();

                // BR-04: Validasi ulang anti-bentrok
                if ($this->ruleService->hasConflict($booking->facility_id, $booking->booking_date->format('Y-m-d'), $booking->start_time, $booking->end_time, $booking->id)) {
                    throw new InvalidArgumentException("Gagal menyetujui: Jadwal ini bentrok dengan peminjaman lain yang sudah disetujui atau jadwal yang diblokir oleh admin.");
                }

                $booking->update([
                    'status' => 'DISETUJUI',
                    'decided_by' => $user->id,
                    'decided_at' => Carbon::now('Asia/Jakarta'),
                    'decision_note' => $request->decision_note ?: 'Pengajuan peminjaman disetujui.',
                ]);

                BookingStatusLog::create([
                    'booking_id' => $booking->id,
                    'from_status' => 'DIAJUKAN',
                    'to_status' => 'DISETUJUI',
                    'changed_by' => $user->id,
                    'note' => $request->decision_note ?: 'Pengajuan peminjaman disetujui.',
                    'created_at' => Carbon::now('Asia/Jakarta'),
                ]);
            });

            return redirect()->route('approvals.index')->with('success', "Pengajuan #{$booking->id} ({$booking->event_name}) berhasil DISETUJUI.");

        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    /**
     * Tolak pengajuan (BR-11: catatan wajib, FR-52, FR-53)
     */
    public function reject(Request $request, $id)
    {
        $user = Auth::user();

        // BR-11: Penolakan wajib disertai catatan / alasan
        $validated = $request->validate([
            'decision_note' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'decision_note.required' => 'Catatan / alasan penolakan wajib diisi agar pemohon mengetahui alasan pembatalan.',
            'decision_note.min' => 'Alasan penolakan minimal 5 karakter.',
        ]);

        $booking = Booking::findOrFail($id);

        if ($booking->status !== 'DIAJUKAN') {
            return back()->with('error', "Pengajuan ini tidak dapat diproses karena statusnya sudah {$booking->status}.");
        }

        DB::transaction(function () use ($booking, $user, $validated) {
            $booking->update([
                'status' => 'DITOLAK',
                'decided_by' => $user->id,
                'decided_at' => Carbon::now('Asia/Jakarta'),
                'decision_note' => $validated['decision_note'],
            ]);

            BookingStatusLog::create([
                'booking_id' => $booking->id,
                'from_status' => 'DIAJUKAN',
                'to_status' => 'DITOLAK',
                'changed_by' => $user->id,
                'note' => $validated['decision_note'],
                'created_at' => Carbon::now('Asia/Jakarta'),
            ]);
        });

        return redirect()->route('approvals.index')->with('success', "Pengajuan #{$booking->id} ({$booking->event_name}) telah DITOLAK.");
    }
}
