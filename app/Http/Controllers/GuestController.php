<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Event;
use App\Models\GuestGroup;
use App\Services\QRCodeService;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    protected $qrCodeService;

    public function __construct(QRCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    public function index()
    {
        $event = Event::where('is_active', true)->first();

        $guests = Guest::where('event_id', $event->id)
            ->with(['group', 'attendance'])
            ->latest()
            ->paginate(20);

        $stats = [
            'invited' => Guest::where('event_id', $event->id)->where('is_invited', true)->count(),
            'attended' => $guests->where('attendance')->count(),
            'total' => Guest::where('event_id', $event->id)->count(),
        ];

        return view('guests.index', compact('guests', 'stats'));
    }

    public function create()
    {
        $groups = GuestGroup::all();
        return view('guests.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'whatsapp' => 'nullable|string',
            'table_number' => 'nullable|string',
            'is_vip' => 'required|boolean',
            'group_id' => 'required|exists:guest_groups,id',
            'guests_count' => 'nullable|integer|min:1',
        ]);

        $event = Event::where('is_active', true)->first();
        $validated['event_id'] = $event->id;

        // Generate QR Code
        $qrCode = uniqid('GUEST-');
        $validated['qr_code'] = $qrCode;

        $guest = Guest::create($validated);

        // Generate QR Code Image
        $this->qrCodeService->generateQRCode($guest);

        return redirect()->route('guests.index')
            ->with('success', 'Tamu berhasil ditambahkan!');
    }
}
