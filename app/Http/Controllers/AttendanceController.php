<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Guest;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of attendances.
     */
    public function index()
    {
        $event = Event::where('is_active', true)->first();

        if (!$event) {
            return redirect()->route('home')
                ->with('error', 'Tidak ada event aktif.');
        }

        // Get all attendances with guest information
        $attendances = Attendance::where('event_id', $event->id)
            ->with(['guest.group'])
            ->latest('checked_in_at')
            ->paginate(20);

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

        return view('attendance.index', compact('attendances', 'stats', 'event'));
    }

    /**
     * Export attendance report.
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'pdf'); // pdf, excel, or csv

        // TODO: Implement export functionality
        return redirect()->route('attendance.index')
            ->with('info', 'Fitur export ' . strtoupper($type) . ' akan segera tersedia!');
    }
}
