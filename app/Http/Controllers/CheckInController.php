<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Event;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

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
        try {
            // Validate input
            $validated = $request->validate([
                'qr_code' => 'required|string',
                'actual_guests_count' => 'nullable|integer|min:1|max:100',
            ]);

            // Get active event
            $event = Event::where('is_active', true)->first();

            if (!$event) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada event aktif.',
                ], 400);
            }

            // Find guest by QR code
            $guest = Guest::where('qr_code', $validated['qr_code'])
                ->where('event_id', $event->id)
                ->first();

            if (!$guest) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR Code tidak valid atau tamu tidak ditemukan.',
                ], 404);
            }

            // Check if already checked in
            $existingAttendance = Attendance::where('guest_id', $guest->id)
                ->where('event_id', $event->id)
                ->first();

            if ($existingAttendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tamu "' . $guest->name . '" sudah check-in pada ' .
                               $existingAttendance->checked_in_at->format('d/m/Y H:i'),
                ], 400);
            }

            // Get actual guest count (from input or default from guest record)
            $actualCount = $validated['actual_guests_count'] ?? $guest->guests_count;

            // Get current user
            $currentUser = Auth::user();
            $checkedInBy = $currentUser ? $currentUser->username : 'System';

            // Create attendance record
            $attendance = Attendance::create([
                'guest_id' => $guest->id,
                'event_id' => $event->id,
                'checked_in_at' => now(),
                'actual_guests_count' => $actualCount,
                'checked_in_by' => $checkedInBy,
            ]);

            // Log success
            Log::info('Guest checked in successfully', [
                'guest_id' => $guest->id,
                'guest_name' => $guest->name,
                'actual_count' => $actualCount,
                'checked_in_by' => $checkedInBy,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Check-in berhasil! Selamat datang ' . $guest->name,
                'guest' => [
                    'id' => $guest->id,
                    'name' => $guest->name,
                    'address' => $guest->address,
                    'is_vip' => $guest->is_vip,
                ],
                'attendance' => [
                    'id' => $attendance->id,
                    'checked_in_at' => $attendance->checked_in_at->format('Y-m-d H:i:s'),
                    'actual_guests_count' => $attendance->actual_guests_count,
                ],
                'redirect_url' => route('checkin.success', ['guest' => $guest->id]),
            ]);

        } catch (Exception $e) {
            // Log the error
            Log::error('Check-in error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat check-in: ' . $e->getMessage(),
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Show check-in success page.
     */
    public function success(Guest $guest)
    {
        $guest->load(['group', 'attendance', 'event']);

        if (!$guest->attendance) {
            return redirect()->route('checkin.index')
                ->with('error', 'Tamu belum check-in.');
        }

        return view('checkin.success', compact('guest'));
    }
}
