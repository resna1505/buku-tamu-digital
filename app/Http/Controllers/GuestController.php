<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Event;
use App\Models\GuestGroup;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GuestController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $event = Event::where('is_active', true)->first();

        if (!$event) {
            return redirect()->route('home')
                ->with('error', 'Tidak ada event aktif.');
        }

        $guests = Guest::where('event_id', $event->id)
            ->with(['group', 'attendance'])
            ->latest()
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

        return view('guests.index', compact('guests', 'stats', 'event'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $event = Event::where('is_active', true)->first();

        if (!$event) {
            return redirect()->route('home')
                ->with('error', 'Tidak ada event aktif.');
        }

        $groups = GuestGroup::where('event_id', $event->id)->get();

        return view('guests.create', compact('groups', 'event'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'whatsapp' => 'nullable|string|max:20',
            'table_number' => 'nullable|string|max:50',
            'is_vip' => 'required|boolean',
            'group_id' => 'required|exists:guest_groups,id',
            'guests_count' => 'nullable|integer|min:1',
        ], [
            'name.required' => 'Nama tamu harus diisi',
            'address.required' => 'Alamat/keterangan harus diisi',
            'is_vip.required' => 'Status VIP harus dipilih',
            'group_id.required' => 'Grup tamu harus dipilih',
            'group_id.exists' => 'Grup tamu tidak valid',
        ]);

        $event = Event::where('is_active', true)->first();

        if (!$event) {
            return redirect()->route('home')
                ->with('error', 'Tidak ada event aktif.');
        }

        $validated['event_id'] = $event->id;
        $validated['qr_code'] = 'GUEST-' . strtoupper(Str::random(10));
        $validated['is_invited'] = true;
        $validated['guests_count'] = $validated['guests_count'] ?? 1;

        $guest = Guest::create($validated);

        // TODO: Generate QR Code image here using QRCodeService

        return redirect()->route('guests.index')
            ->with('success', 'Tamu "' . $guest->name . '" berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Guest $guest)
    {
        $guest->load(['group', 'attendance', 'event']);

        return view('guests.show', compact('guest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Guest $guest)
    {
        $groups = GuestGroup::where('event_id', $guest->event_id)->get();

        return view('guests.edit', compact('guest', 'groups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Guest $guest)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'whatsapp' => 'nullable|string|max:20',
            'table_number' => 'nullable|string|max:50',
            'is_vip' => 'required|boolean',
            'group_id' => 'required|exists:guest_groups,id',
            'guests_count' => 'nullable|integer|min:1',
        ]);

        $validated['guests_count'] = $validated['guests_count'] ?? 1;

        $guest->update($validated);

        return redirect()->route('guests.index')
            ->with('success', 'Data tamu "' . $guest->name . '" berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Guest $guest)
    {
        $name = $guest->name;
        $guest->delete();

        return redirect()->route('guests.index')
            ->with('success', 'Tamu "' . $name . '" berhasil dihapus!');
    }

    /**
     * Search guests.
     */
    public function search(Request $request)
    {
        $event = Event::where('is_active', true)->first();
        $searchTerm = $request->get('q', '');

        $guests = Guest::where('event_id', $event->id)
            ->where(function($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('address', 'like', '%' . $searchTerm . '%')
                    ->orWhere('whatsapp', 'like', '%' . $searchTerm . '%');
            })
            ->with(['group', 'attendance'])
            ->latest()
            ->paginate(20);

        return view('guests.search', compact('guests', 'searchTerm', 'event'));
    }

    /**
     * Import guests from Excel.
     */
    public function import(Request $request)
    {
        // TODO: Implement Excel import
        return redirect()->route('guests.index')
            ->with('info', 'Fitur import Excel akan segera tersedia!');
    }

    /**
     * Export guests to PDF.
     */
    public function exportPdf()
    {
        // TODO: Implement PDF export
        return redirect()->route('guests.index')
            ->with('info', 'Fitur export PDF akan segera tersedia!');
    }

    /**
     * Export guests to Excel.
     */
    public function exportExcel()
    {
        // TODO: Implement Excel export
        return redirect()->route('guests.index')
            ->with('info', 'Fitur export Excel akan segera tersedia!');
    }

    /**
     * Send WhatsApp message to guest.
     */
    public function sendWhatsApp(Guest $guest)
    {
        // TODO: Implement WhatsApp integration
        return redirect()->route('guests.index')
            ->with('info', 'Fitur WhatsApp akan segera tersedia!');
    }
}
