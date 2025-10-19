<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'date',
        'start_time',
        'end_time',
        'location',
        'description',
        'is_active',
    ];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the event.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all guests for the event.
     */
    public function guests()
    {
        return $this->hasMany(Guest::class);
    }

    /**
     * Get all guest groups for the event.
     */
    public function guestGroups()
    {
        return $this->hasMany(GuestGroup::class);
    }

    /**
     * Get all attendances for the event.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
