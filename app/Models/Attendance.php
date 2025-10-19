<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'guest_id',        // ← PASTIKAN INI ADA!
        'event_id',
        'checked_in_at',
        'actual_guests_count',
        'checked_in_by',
        'notes',
    ];

    protected $casts = [
        'checked_in_at' => 'datetime',
        'actual_guests_count' => 'integer',
    ];

    /**
     * Get the guest that owns the attendance.
     */
    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    /**
     * Get the event that owns the attendance.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
