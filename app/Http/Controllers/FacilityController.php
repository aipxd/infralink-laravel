<?php

namespace App\Http\Controllers;

use App\Models\BlockedSlot;
use App\Models\Booking;
use App\Models\Facility;
use App\Models\FacilityCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    public function index(Request $request)
    {
        $query = Facility::with(['category', 'photos'])
            ->where('is_active', true);

        // Search by keyword
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('building', 'like', "%{$q}%")
                    ->orWhere('location_detail', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by building
        if ($request->filled('building')) {
            $query->where('building', $request->building);
        }

        // Filter by minimum capacity
        if ($request->filled('min_capacity')) {
            $query->where('capacity', '>=', (int)$request->min_capacity);
        }

        // Filter by availability (FR-13)
        if ($request->filled('available_date') && $request->filled('start_time') && $request->filled('end_time')) {
            $date = $request->available_date;
            $start = $request->start_time;
            $end = $request->end_time;

            // Fasilitas yang jam operasionalnya mencakup rentang tersebut
            $query->where('open_time', '<=', $start)
                  ->where('close_time', '>=', $end);

            // Dan tidak bentrok dengan booking DISETUJUI
            $query->whereDoesntHave('bookings', function ($b) use ($date, $start, $end) {
                $b->where('status', 'DISETUJUI')
                  ->whereDate('booking_date', $date)
                  ->where('start_time', '<', $end)
                  ->where('end_time', '>', $start);
            });

            // Dan tidak bentrok dengan BLOCKED_SLOTS
            $query->whereDoesntHave('blockedSlots', function ($bl) use ($date, $start, $end) {
                $bl->whereDate('block_date', $date)
                   ->where('start_time', '<', $end)
                   ->where('end_time', '>', $start);
            });
        }

        $facilities = $query->orderBy('name')->paginate(9)->withQueryString();
        $categories = FacilityCategory::orderBy('name')->get();
        $buildings = Facility::where('is_active', true)->distinct()->pluck('building')->sort()->values();

        return view('facilities.index', compact('facilities', 'categories', 'buildings'));
    }

    public function show($id)
    {
        $facility = Facility::with(['category', 'photos'])->findOrFail($id);

        if (!$facility->is_active && (!auth()->check() || !auth()->user()->isApprover())) {
            abort(404, 'Fasilitas tidak ditemukan atau sedang dinonaktifkan.');
        }

        return view('facilities.show', compact('facility'));
    }

    /**
     * JSON Endpoint untuk FullCalendar (FR-20 s.d FR-24)
     */
    public function calendarEvents($id, Request $request)
    {
        $facility = Facility::findOrFail($id);

        $from = $request->query('start', Carbon::now('Asia/Jakarta')->startOfMonth()->toDateString());
        $to = $request->query('end', Carbon::now('Asia/Jakarta')->endOfMonth()->toDateString());

        $events = [];

        // 1. Booking DISETUJUI (Merah / Terisi - memblokir kalender BR-01)
        $approvedBookings = Booking::with('user')
            ->where('facility_id', $facility->id)
            ->where('status', 'DISETUJUI')
            ->whereBetween('booking_date', [substr($from, 0, 10), substr($to, 0, 10)])
            ->get();

        foreach ($approvedBookings as $b) {
            $dateStr = $b->booking_date->format('Y-m-d');
            $unitName = $b->user->unit_name ?: 'Pengguna Kampus';

            $events[] = [
                'id' => 'booking-' . $b->id,
                'title' => 'Terisi: ' . $b->event_name . ' (' . $unitName . ')',
                'start' => $dateStr . 'T' . $b->start_time . ':00',
                'end' => $dateStr . 'T' . $b->end_time . ':00',
                'backgroundColor' => '#EF4444', // Red / Rose
                'borderColor' => '#DC2626',
                'textColor' => '#FFFFFF',
                'extendedProps' => [
                    'type' => 'DISETUJUI',
                    'eventName' => $b->event_name,
                    'organizer' => $unitName,
                    'time' => $b->start_time . ' - ' . $b->end_time . ' WIB',
                ],
            ];
        }

        // 2. Blokir Jadwal Admin (Abu-abu / Slate - tidak tersedia)
        $blockedSlots = BlockedSlot::where('facility_id', $facility->id)
            ->whereBetween('block_date', [substr($from, 0, 10), substr($to, 0, 10)])
            ->get();

        foreach ($blockedSlots as $bl) {
            $dateStr = $bl->block_date->format('Y-m-d');
            $events[] = [
                'id' => 'blocked-' . $bl->id,
                'title' => 'Tidak Tersedia: ' . $bl->reason,
                'start' => $dateStr . 'T' . $bl->start_time . ':00',
                'end' => $dateStr . 'T' . $bl->end_time . ':00',
                'backgroundColor' => '#475569', // Slate
                'borderColor' => '#334155',
                'textColor' => '#FFFFFF',
                'extendedProps' => [
                    'type' => 'BLOKIR',
                    'reason' => $bl->reason,
                    'time' => $bl->start_time . ' - ' . $bl->end_time . ' WIB',
                ],
            ];
        }

        // 3. Booking DIAJUKAN (Kuning / Amber - penanda menunggu BR-02)
        $pendingBookings = Booking::with('user')
            ->where('facility_id', $facility->id)
            ->where('status', 'DIAJUKAN')
            ->whereBetween('booking_date', [substr($from, 0, 10), substr($to, 0, 10)])
            ->get();

        foreach ($pendingBookings as $pb) {
            $dateStr = $pb->booking_date->format('Y-m-d');
            $unitName = $pb->user->unit_name ?: 'Pengguna Kampus';

            $events[] = [
                'id' => 'pending-' . $pb->id,
                'title' => 'Menunggu Persetujuan: ' . $pb->event_name . ' (' . $unitName . ')',
                'start' => $dateStr . 'T' . $pb->start_time . ':00',
                'end' => $dateStr . 'T' . $pb->end_time . ':00',
                'backgroundColor' => '#F59E0B', // Amber
                'borderColor' => '#D97706',
                'textColor' => '#1F2937',
                'extendedProps' => [
                    'type' => 'DIAJUKAN',
                    'eventName' => $pb->event_name,
                    'organizer' => $unitName,
                    'time' => $pb->start_time . ' - ' . $pb->end_time . ' WIB',
                ],
            ];
        }

        return response()->json($events);
    }
}
