<?php

namespace App\Services;

use App\Models\BlockedSlot;
use App\Models\Booking;
use App\Models\Facility;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class BookingRuleService
{
    /**
     * Validasi semua aturan bisnis sebelum pengajuan dibuat (BR-05 s.d BR-09, dan BR-03).
     *
     * @throws InvalidArgumentException
     */
    public function validateNewBooking(Facility $facility, array $data): void
    {
        $today = Carbon::now('Asia/Jakarta')->startOfDay();
        $bookingDate = Carbon::parse($data['booking_date'], 'Asia/Jakarta')->startOfDay();

        // BR-09: Minimal H+1 dan maksimal 90 hari ke depan
        $minDate = $today->copy()->addDay();
        $maxDate = $today->copy()->addDays(90);

        if ($bookingDate->lessThan($minDate)) {
            throw new InvalidArgumentException("Tanggal peminjaman minimal H+1 (mulai {$minDate->translatedFormat('d F Y')}).");
        }

        if ($bookingDate->greaterThan($maxDate)) {
            throw new InvalidArgumentException("Tanggal peminjaman maksimal 90 hari ke depan ({$maxDate->translatedFormat('d F Y')}).");
        }

        // Format waktu: HH:mm
        $startTime = $data['start_time'];
        $endTime = $data['end_time'];

        if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $startTime) || !preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $endTime)) {
            throw new InvalidArgumentException("Format jam harus HH:mm (contoh: 08:00).");
        }

        if ($startTime >= $endTime) {
            throw new InvalidArgumentException("Jam mulai ({$startTime}) harus lebih awal daripada jam selesai ({$endTime}).");
        }

        // BR-06: Di dalam jam operasional fasilitas
        if ($startTime < $facility->open_time || $endTime > $facility->close_time) {
            throw new InvalidArgumentException("Waktu peminjaman harus berada di dalam jam operasional fasilitas ({$facility->open_time} - {$facility->close_time} WIB).");
        }

        // BR-07: Durasi tidak melebihi durasi maksimal
        $startMinutes = $this->timeToMinutes($startTime);
        $endMinutes = $this->timeToMinutes($endTime);
        $durationMinutes = $endMinutes - $startMinutes;

        if ($facility->max_duration_minutes > 0 && $durationMinutes > $facility->max_duration_minutes) {
            $maxHours = $facility->max_duration_minutes / 60;
            throw new InvalidArgumentException("Durasi peminjaman ({$durationMinutes} menit) melebihi batas maksimal fasilitas ({$facility->max_duration_minutes} menit / {$maxHours} jam).");
        }

        // BR-08: Jumlah peserta tidak melebihi kapasitas
        if ((int)$data['participants'] > $facility->capacity) {
            throw new InvalidArgumentException("Jumlah peserta ({$data['participants']} orang) melebihi kapasitas fasilitas ({$facility->capacity} orang).");
        }

        // BR-03: Cek bentrok dengan booking DISETUJUI atau BLOKIR ADMIN
        if ($this->hasConflict($facility->id, $data['booking_date'], $startTime, $endTime)) {
            throw new InvalidArgumentException("Jadwal pada tanggal dan rentang jam tersebut sudah terisi atau diblokir oleh admin.");
        }
    }

    /**
     * Memeriksa apakah terjadi overlap dengan booking disetujui atau blocked slot.
     */
    public function hasConflict(int $facilityId, string $date, string $startTime, string $endTime, ?int $excludeBookingId = null): bool
    {
        $bookingConflict = Booking::where('facility_id', $facilityId)
            ->whereDate('booking_date', $date)
            ->where('status', 'DISETUJUI')
            ->when($excludeBookingId, fn($q) => $q->where('id', '!=', $excludeBookingId))
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            ->exists();

        if ($bookingConflict) {
            return true;
        }

        $blockedConflict = BlockedSlot::where('facility_id', $facilityId)
            ->whereDate('block_date', $date)
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            ->exists();

        return $blockedConflict;
    }

    /**
     * Cek apakah ada pengajuan lain yang statusnya DIAJUKAN yang bersinggungan (untuk info Approver FR-51).
     */
    public function getPendingOverlaps(Booking $booking): array
    {
        return Booking::with('user')
            ->where('facility_id', $booking->facility_id)
            ->whereDate('booking_date', $booking->booking_date)
            ->where('status', 'DIAJUKAN')
            ->where('id', '!=', $booking->id)
            ->where('start_time', '<', $booking->end_time)
            ->where('end_time', '>', $booking->start_time)
            ->get()
            ->all();
    }

    private function timeToMinutes(string $time): int
    {
        [$hours, $minutes] = explode(':', $time);
        return ((int)$hours * 60) + (int)$minutes;
    }
}
