<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    protected $fillable = [
        'event_id', 'group_id', 'name', 'address', 'whatsapp',
        'table_number', 'guests_count', 'is_vip', 'qr_code',
        'qr_code_path', 'is_invited'
    ];

    protected $casts = [
        'is_vip' => 'boolean',
        'is_invited' => 'boolean',
        'guests_count' => 'integer',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function group()
    {
        return $this->belongsTo(GuestGroup::class);
    }

    public function attendance()
    {
        return $this->hasOne(Attendance::class);
    }

    public function greetings()
    {
        return $this->hasMany(Greeting::class);
    }

    public function souvenirs()
    {
        return $this->belongsToMany(Souvenir::class, 'guest_souvenir')
            ->withPivot('quantity', 'received_at')
            ->withTimestamps();
    }
}
