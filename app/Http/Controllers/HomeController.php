<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Guest;
use App\Models\Attendance;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get active event
        $event = Event::where('is_active', true)->first();

        if (!$event) {
            return redirect()->route('login')
                ->with('error', 'Tidak ada event aktif. Silakan buat event terlebih dahulu.');
        }

        // Calculate statistics
        $stats = [
            'invited' => Guest::where('event_id', $event->id)
                ->where('is_invited', true)
                ->count(),
            'attended' => Attendance::where('event_id', $event->id)
                ->count(),
            'total' => Guest::where('event_id', $event->id)
                ->count(),
        ];

        // Get recent guests
        $recentGuests = Guest::where('event_id', $event->id)
            ->with('group')
            ->latest()
            ->take(5)
            ->get();

        return view('home.index', compact('event', 'stats', 'recentGuests'));
    }
}
