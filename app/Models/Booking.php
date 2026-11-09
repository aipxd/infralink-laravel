<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'facility_id',
        'booking_date',
        'start_time',
        'end_time',
        'event_name',
        'purpose',
        'participants',
        'contact_phone',
        'status',
        'decided_by',
        'decided_at',
        'decision_note',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'decided_at' => 'datetime',
        'participants' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class, 'facility_id');
    }

    public function decider()
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    public function statusLogs()
    {
        return $this->hasMany(BookingStatusLog::class, 'booking_id')->orderBy('created_at', 'desc');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'booking_id');
    }

    /**
     * Status tampilan: bila DISETUJUI tapi jadwal sudah lewat, tampilkan SELESAI
     */
    public function getDisplayStatusAttribute(): string
    {
        if ($this->status === 'DISETUJUI') {
            $endDateTime = Carbon::parse($this->booking_date->format('Y-m-d') . ' ' . $this->end_time, 'Asia/Jakarta');
            if (Carbon::now('Asia/Jakarta')->greaterThan($endDateTime)) {
                return 'SELESAI';
            }
        }
        return $this->status;
    }

    public function getStatusBadgeClassAttribute(): string
    {
        $status = $this->display_status;
        return match ($status) {
            'DIAJUKAN' => 'bg-amber-100 text-amber-800 border-amber-300',
            'DISETUJUI' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'SELESAI' => 'bg-blue-100 text-blue-800 border-blue-300',
            'DITOLAK' => 'bg-rose-100 text-rose-800 border-rose-300',
            'DIBATALKAN' => 'bg-slate-100 text-slate-700 border-slate-300',
            default => 'bg-gray-100 text-gray-800 border-gray-300',
        };
    }
}
