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
     * Search guest by name for manual input.
     */
    public function search(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|min:2',
            ]);

            $event = Event::where('is_active', true)->first();

            if (!$event) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada event aktif.',
                ], 400);
            }

            // Search guests by name (case-insensitive, partial match)
            $guests = Guest::where('event_id', $event->id)
                ->where('name', 'LIKE', '%' . $validated['name'] . '%')
                ->select('id', 'name', 'address', 'qr_code', 'faculty', 'study_program')
                ->limit(10)
                ->get();

            if ($guests->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tamu dengan nama "' . $validated['name'] . '" tidak ditemukan.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'guests' => $guests,
                'count' => $guests->count(),
            ]);

        } catch (Exception $e) {
            Log::error('Guest search error', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari tamu.',
            ], 500);
        }
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

            // Check how many times already checked in
            $checkInCount = Attendance::where('guest_id', $guest->id)
                ->where('event_id', $event->id)
                ->count();

            // Maximum 2 check-ins allowed (untuk 2 tamu)
            if ($checkInCount >= 2) {
                $attendances = Attendance::where('guest_id', $guest->id)
                    ->where('event_id', $event->id)
                    ->orderBy('checked_in_at', 'asc')
                    ->get();

                return response()->json([
                    'success' => false,
                    'message' => 'Tamu "' . $guest->name . '" sudah check-in 2 kali (maksimal):\n' .
                               '1. ' . $attendances[0]->checked_in_at->format('d/m/Y H:i') . ' WIB\n' .
                               '2. ' . $attendances[1]->checked_in_at->format('d/m/Y H:i') . ' WIB',
                    'check_in_count' => $checkInCount,
                ], 400);
            }

            // Check for duplicate scan (prevent accidental double scan within 10 seconds)
            $recentCheckIn = Attendance::where('guest_id', $guest->id)
                ->where('event_id', $event->id)
                ->where('checked_in_at', '>', now()->subSeconds(10))
                ->first();

            if ($recentCheckIn) {
                return response()->json([
                    'success' => false,
                    'message' => 'Check-in terlalu cepat! Tunggu 10 detik sebelum scan lagi untuk tamu ke-2.',
                    'wait_seconds' => 10 - now()->diffInSeconds($recentCheckIn->checked_in_at),
                ], 400);
            }

            // Get actual guest count (default 1 per check-in)
            $actualCount = $validated['actual_guests_count'] ?? 1;

            // Get current user
            $currentUser = Auth::user();
            $checkedInBy = $currentUser ? $currentUser->username : 'System';

            // Determine check-in sequence
            $checkInSequence = $checkInCount + 1;

            // Create attendance record
            $attendance = Attendance::create([
                'guest_id' => $guest->id,
                'event_id' => $event->id,
                'checked_in_at' => now(),
                'actual_guests_count' => $actualCount,
                'checked_in_by' => $checkedInBy,
                'check_in_number' => $checkInSequence, // Add this field to track 1st or 2nd check-in
            ]);

            // Log success
            Log::info('Guest checked in successfully', [
                'guest_id' => $guest->id,
                'guest_name' => $guest->name,
                'check_in_number' => $checkInSequence,
                'actual_count' => $actualCount,
                'checked_in_by' => $checkedInBy,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Check-in berhasil! (' . $checkInSequence . ' dari 2) Selamat datang ' . $guest->name,
                'check_in_number' => $checkInSequence,
                'remaining_checkins' => 2 - $checkInSequence,
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
                    'check_in_number' => $checkInSequence,
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
        $guest->load(['group', 'event']);

        // Get the latest attendance record
        $latestAttendance = Attendance::where('guest_id', $guest->id)
            ->where('event_id', $guest->event_id)
            ->latest('checked_in_at')
            ->first();

        if (!$latestAttendance) {
            return redirect()->route('checkin.index')
                ->with('error', 'Tamu belum check-in.');
        }

        // Get total check-in count
        $checkInCount = Attendance::where('guest_id', $guest->id)
            ->where('event_id', $guest->event_id)
            ->count();

        return view('checkin.success', compact('guest', 'latestAttendance', 'checkInCount'));
    }
}
