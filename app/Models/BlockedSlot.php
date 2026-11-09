<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockedSlot extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'facility_id',
        'block_date',
        'start_time',
        'end_time',
        'reason',
        'created_by',
        'created_at',
    ];

    protected $casts = [
        'block_date' => 'date',
        'created_at' => 'datetime',
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class, 'facility_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
