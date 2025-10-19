<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Event;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckInController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the QR code scanner page.
     */
    public function index()
    {
        $event = Event::where('is_active', true)->first();

        if (!$event) {
            return redirect()->route('home')
                ->with('error', 'Tidak ada event aktif.');
        }

        return view('checkin.scan', compact('event'));
    }

    /**
     * Process QR code scan and check in guest.
     */
    public function scan(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        $event = Event::where('is_active', true)->first();

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada event aktif.',
            ], 400);
        }

        // Find guest by QR code
        $guest = Guest::where('qr_code', $request->qr_code)
            ->where('event_id', $event->id)
            ->first();

        if (!$guest) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid atau tamu tidak ditemukan.',
            ], 404);
        }

        // Check if already checked in
        if ($guest->hasCheckedIn()) {
            $attendance = $guest->attendance;
            return response()->json([
                'success' => false,
                'message' => 'Tamu "' . $guest->name . '" sudah check-in pada ' .
                           $attendance->checked_in_at->format('d/m/Y H:i'),
                'guest' => $guest,
                'attendance' => $attendance,
            ], 400);
        }

        // Create attendance record
        $attendance = Attendance::create([
            'guest_id' => $guest->id,
            'event_id' => $event->id,
            'checked_in_at' => now(),
            'actual_guests_count' => $guest->guests_count,
            'checked_in_by' => Auth::user()->username,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-in berhasil! Selamat datang ' . $guest->name,
            'guest' => $guest->load('group'),
            'attendance' => $attendance,
            'redirect_url' => route('checkin.success', ['guest' => $guest->id]),
        ]);
    }

    /**
     * Show check-in success page.
     */
    public function success(Guest $guest)
    {
        $guest->load(['group', 'attendance', 'event']);

        return view('checkin.success', compact('guest'));
    }
}
