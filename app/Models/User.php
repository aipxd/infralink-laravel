<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'identity_number',
        'unit_name',
        'phone',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function isPemohon(): bool
    {
        return $this->role === 'PEMOHON';
    }

    public function isApprover(): bool
    {
        return in_array($this->role, ['APPROVER', 'ADMIN']);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'ADMIN';
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'user_id');
    }

    public function decisions()
    {
        return $this->hasMany(Booking::class, 'decided_by');
    }

    public function statusLogs()
    {
        return $this->hasMany(BookingStatusLog::class, 'changed_by');
    }

    public function blockedSlots()
    {
        return $this->hasMany(BlockedSlot::class, 'created_by');
    }
}
