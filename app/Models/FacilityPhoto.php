<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id',
        'file_path',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class, 'facility_id');
    }

    public function getUrlAttribute(): string
    {
        if (str_starts_with($this->file_path, 'http')) {
            return $this->file_path;
        }
        return asset('storage/' . $this->file_path);
    }
}
