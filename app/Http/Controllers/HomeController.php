<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Guest;
use App\Models\Attendance;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $event = Event::where('is_active', true)->first();

        $stats = [
            'invited' => Guest::where('event_id', $event->id)->where('is_invited', true)->count(),
            'attended' => Attendance::where('event_id', $event->id)->count(),
            'total' => Guest::where('event_id', $event->id)->count(),
        ];

        $recentGuests = Guest::where('event_id', $event->id)
            ->latest()
            ->take(5)
            ->get();

        return view('home.index', compact('event', 'stats', 'recentGuests'));
    }
}
