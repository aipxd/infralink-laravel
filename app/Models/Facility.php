<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'building',
        'location_detail',
        'capacity',
        'description',
        'usage_rules',
        'open_time',
        'close_time',
        'max_duration_minutes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity' => 'integer',
        'max_duration_minutes' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(FacilityCategory::class, 'category_id');
    }

    public function photos()
    {
        return $this->hasMany(FacilityPhoto::class, 'facility_id');
    }

    public function primaryPhoto()
    {
        return $this->hasOne(FacilityPhoto::class, 'facility_id')->where('is_primary', true);
    }

    public function getPrimaryPhotoUrlAttribute(): string
    {
        $primary = $this->photos->firstWhere('is_primary', true) ?? $this->photos->first();
        if ($primary && $primary->file_path) {
            if (str_starts_with($primary->file_path, 'http')) {
                return $primary->file_path;
            }
            return asset('storage/' . $primary->file_path);
        }
        return 'https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=800&q=80';
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'facility_id');
    }

    public function blockedSlots()
    {
        return $this->hasMany(BlockedSlot::class, 'facility_id');
    }
}
