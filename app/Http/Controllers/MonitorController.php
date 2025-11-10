<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use Illuminate\Http\Request;

class MonitorController extends Controller
{
    /**
     * Display the check-in monitor page
     */
    public function checkIn()
    {
        $event = Event::where('is_active', true)->first();

        if (!$event) {
            return redirect()->route('home')
                ->with('error', 'Tidak ada event aktif.');
        }

        // Get latest 10 check-ins
        $recentCheckIns = Attendance::with(['guest'])
            ->where('event_id', $event->id)
            ->orderBy('checked_in_at', 'desc')
            ->limit(10)
            ->get();

        return view('monitor.checkin', compact('event', 'recentCheckIns'));
    }

    /**
     * Get latest check-ins via AJAX (for auto-refresh)
     */
    public function getLatestCheckIns(Request $request)
    {
        $event = Event::where('is_active', true)->first();

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada event aktif',
            ]);
        }

        $lastId = $request->input('last_id', 0);

        // Get check-ins newer than last_id
        $checkIns = Attendance::with(['guest'])
            ->where('event_id', $event->id)
            ->where('id', '>', $lastId)
            ->orderBy('checked_in_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'checkIns' => $checkIns->map(function ($attendance) {
                return [
                    'id' => $attendance->id,
                    'guest_name' => $attendance->guest->name,
                    'guest_faculty' => $attendance->guest->faculty,
                    'guest_study_program' => $attendance->guest->study_program,
                    'guest_address' => $attendance->guest->address,
                    'is_vip' => $attendance->guest->is_vip,
                    'check_in_number' => $attendance->check_in_number ?? 1,
                    'checked_in_at' => $attendance->checked_in_at->format('H:i:s'),
                    'checked_in_at_full' => $attendance->checked_in_at->format('d/m/Y H:i:s'),
                ];
            }),
        ]);
    }
}
