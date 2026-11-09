<?php

namespace App\Http\Controllers;

use App\Models\BlockedSlot;
use App\Models\Booking;
use App\Models\BookingStatusLog;
use App\Models\Facility;
use App\Models\FacilityCategory;
use App\Models\FacilityPhoto;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_facilities' => Facility::count(),
            'active_facilities' => Facility::where('is_active', true)->count(),
            'pending_bookings' => Booking::where('status', 'DIAJUKAN')->count(),
            'approved_bookings' => Booking::where('status', 'DISETUJUI')->count(),
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
        ];

        $recentBookings = Booking::with(['facility', 'user'])->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentBookings'));
    }

    // ==========================================
    // 1. FASILITAS
    // ==========================================
    public function facilities(Request $request)
    {
        $query = Facility::with(['category', 'photos']);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where('name', 'like', "%{$q}%")->orWhere('building', 'like', "%{$q}%");
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $facilities = $query->orderBy('name')->paginate(10)->withQueryString();
        $categories = FacilityCategory::all();

        return view('admin.facilities.index', compact('facilities', 'categories'));
    }

    public function createFacility()
    {
        $categories = FacilityCategory::orderBy('name')->get();
        return view('admin.facilities.create', compact('categories'));
    }

    public function storeFacility(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:facility_categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'building' => ['required', 'string', 'max:100'],
            'location_detail' => ['nullable', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'usage_rules' => ['nullable', 'string'],
            'open_time' => ['required', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
            'close_time' => ['required', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
            'max_duration_minutes' => ['required', 'integer', 'min:30'],
            'photos.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        $facility = Facility::create([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'building' => $validated['building'],
            'location_detail' => $validated['location_detail'] ?? null,
            'capacity' => $validated['capacity'],
            'description' => $validated['description'] ?? null,
            'usage_rules' => $validated['usage_rules'] ?? null,
            'open_time' => $validated['open_time'],
            'close_time' => $validated['close_time'],
            'max_duration_minutes' => $validated['max_duration_minutes'],
            'is_active' => true,
        ]);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $idx => $photo) {
                $path = $photo->store('facility_photos', 'public');
                FacilityPhoto::create([
                    'facility_id' => $facility->id,
                    'file_path' => $path,
                    'is_primary' => ($idx === 0),
                ]);
            }
        }

        return redirect()->route('admin.facilities.index')->with('success', 'Fasilitas baru berhasil ditambahkan.');
    }

    public function editFacility($id)
    {
        $facility = Facility::with('photos')->findOrFail($id);
        $categories = FacilityCategory::orderBy('name')->get();

        return view('admin.facilities.edit', compact('facility', 'categories'));
    }

    public function updateFacility(Request $request, $id)
    {
        $facility = Facility::findOrFail($id);

        $validated = $request->validate([
            'category_id' => ['required', 'exists:facility_categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'building' => ['required', 'string', 'max:100'],
            'location_detail' => ['nullable', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'usage_rules' => ['nullable', 'string'],
            'open_time' => ['required', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
            'close_time' => ['required', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
            'max_duration_minutes' => ['required', 'integer', 'min:30'],
            'photos.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        $facility->update([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'building' => $validated['building'],
            'location_detail' => $validated['location_detail'] ?? null,
            'capacity' => $validated['capacity'],
            'description' => $validated['description'] ?? null,
            'usage_rules' => $validated['usage_rules'] ?? null,
            'open_time' => $validated['open_time'],
            'close_time' => $validated['close_time'],
            'max_duration_minutes' => $validated['max_duration_minutes'],
        ]);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('facility_photos', 'public');
                FacilityPhoto::create([
                    'facility_id' => $facility->id,
                    'file_path' => $path,
                    'is_primary' => ($facility->photos()->count() === 0),
                ]);
            }
        }

        return redirect()->route('admin.facilities.index')->with('success', 'Data fasilitas berhasil diperbarui.');
    }

    public function toggleFacilityStatus($id)
    {
        $facility = Facility::findOrFail($id);
        $facility->update(['is_active' => !$facility->is_active]);

        $statusStr = $facility->is_active ? 'diaktifkan kembali' : 'dinonaktifkan';
        return back()->with('success', "Fasilitas {$facility->name} berhasil {$statusStr}.");
    }

    public function destroyFacility($id)
    {
        $facility = Facility::findOrFail($id);
        $facility->delete();

        return redirect()->route('admin.facilities.index')->with('success', 'Fasilitas berhasil dihapus.');
    }

    // ==========================================
    // 2. KATEGORI
    // ==========================================
    public function categories()
    {
        $categories = FacilityCategory::withCount('facilities')->orderBy('name')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:facility_categories,name'],
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.unique' => 'Kategori dengan nama ini sudah ada.',
        ]);

        FacilityCategory::create($validated);
        return back()->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    public function updateCategory(Request $request, $id)
    {
        $category = FacilityCategory::findOrFail($id);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:facility_categories,name,' . $id],
        ]);

        $category->update($validated);
        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroyCategory($id)
    {
        $category = FacilityCategory::withCount('facilities')->findOrFail($id);
        if ($category->facilities_count > 0) {
            return back()->with('error', 'Kategori ini tidak dapat dihapus karena masih memiliki fasilitas terkait.');
        }

        $category->delete();
        return back()->with('success', 'Kategori berhasil dihapus.');
    }

    // ==========================================
    // 3. BLOKIR JADWAL (FR-62)
    // ==========================================
    public function blockedSlots(Request $request)
    {
        $query = BlockedSlot::with(['facility', 'creator']);

        if ($request->filled('facility_id')) {
            $query->where('facility_id', $request->facility_id);
        }

        $blockedSlots = $query->orderBy('block_date', 'desc')->paginate(10)->withQueryString();
        $facilities = Facility::where('is_active', true)->orderBy('name')->get();

        return view('admin.blocked_slots.index', compact('blockedSlots', 'facilities'));
    }

    public function storeBlockedSlot(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'facility_id' => ['required', 'exists:facilities,id'],
            'block_date' => ['required', 'date_format:Y-m-d'],
            'start_time' => ['required', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
            'end_time' => ['required', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
            'reason' => ['required', 'string', 'max:255'],
        ], [
            'reason.required' => 'Alasan pemblokiran jadwal wajib diisi (misal: Maintenance, Libur, Event Rektorat).',
        ]);

        if ($validated['start_time'] >= $validated['end_time']) {
            return back()->withInput()->with('error', 'Jam mulai harus lebih awal daripada jam selesai.');
        }

        // FR-62: Cek apakah bentrok dengan booking Disetujui
        $conflictingBooking = Booking::where('facility_id', $validated['facility_id'])
            ->whereDate('booking_date', $validated['block_date'])
            ->where('status', 'DISETUJUI')
            ->where('start_time', '<', $validated['end_time'])
            ->where('end_time', '>', $validated['start_time'])
            ->first();

        if ($conflictingBooking) {
            return back()->withInput()->with('error', "Tidak dapat memblokir: Jadwal bentrok dengan peminjaman yang sudah Disetujui (Booking #{$conflictingBooking->id}: {$conflictingBooking->event_name}). Batalkan booking tersebut terlebih dahulu jika memang mendesak.");
        }

        BlockedSlot::create([
            'facility_id' => $validated['facility_id'],
            'block_date' => $validated['block_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'reason' => $validated['reason'],
            'created_by' => $user->id,
            'created_at' => Carbon::now('Asia/Jakarta'),
        ]);

        return back()->with('success', 'Jadwal berhasil diblokir.');
    }

    public function destroyBlockedSlot($id)
    {
        $slot = BlockedSlot::findOrFail($id);
        $slot->delete();

        return back()->with('success', 'Blokir jadwal berhasil dicabut.');
    }

    // ==========================================
    // 4. KELOLA PENGGUNA (FR-63)
    // ==========================================
    public function users(Request $request)
    {
        $query = User::query();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%")
                  ->orWhere('identity_number', 'like', "%{$q}%")
                  ->orWhere('unit_name', 'like', "%{$q}%");
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function updateUserRole(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'role' => ['required', 'in:PEMOHON,APPROVER,ADMIN'],
        ]);

        // Cegah mencabut admin sendiri jika hanya ada 1 admin
        if ($user->id === Auth::id() && $request->role !== 'ADMIN') {
            return back()->with('error', 'Anda tidak dapat mengubah peran Anda sendiri.');
        }

        $user->update(['role' => $request->role]);

        return back()->with('success', "Peran pengguna {$user->name} berhasil diubah menjadi {$user->role}.");
    }

    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $statusStr = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Akun {$user->name} berhasil {$statusStr}.");
    }
}
