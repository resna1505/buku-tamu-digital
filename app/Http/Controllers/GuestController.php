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

        // Get all groups for filter
        $groups = GuestGroup::where('event_id', $event->id)->get();

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

        return view('guests.index', compact('guests', 'stats', 'event', 'groups'));
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
            // Data Mahasiswa
            'student_name' => 'required|string|max:255',
            'npm' => 'required|string|max:50',
            'faculty' => 'required|string|max:100',
            'study_program' => 'required|string|max:100',
            'email' => 'nullable|email|max:255',
            'whatsapp' => 'required|string|max:20',
            'payment_proof' => 'nullable|url',

            // Data Tamu
            'guest_1_name' => 'required|string|max:255',
            'guest_2_name' => 'nullable|string|max:255',

            // Pengaturan
            'is_vip' => 'required|boolean',
            'group_id' => 'required|exists:guest_groups,id',
        ], [
            'student_name.required' => 'Nama mahasiswa harus diisi',
            'npm.required' => 'NPM harus diisi',
            'faculty.required' => 'Fakultas harus dipilih',
            'study_program.required' => 'Program studi harus diisi',
            'whatsapp.required' => 'Nomor WhatsApp harus diisi',
            'guest_1_name.required' => 'Nama Tamu 1 harus diisi',
            'is_vip.required' => 'Status VIP harus dipilih',
            'group_id.required' => 'Grup tamu harus dipilih',
            'group_id.exists' => 'Grup tamu tidak valid',
        ]);

        $event = Event::where('is_active', true)->first();

        if (!$event) {
            return redirect()->route('home')
                ->with('error', 'Tidak ada event aktif.');
        }

        // Format nomor WhatsApp
        $phone = preg_replace('/\s+/', '', $validated['whatsapp']);
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        } elseif (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }

        // Hitung jumlah tamu
        $guestCount = !empty($validated['guest_2_name']) ? 2 : 1;

        $guestData = [
            'event_id' => $event->id,
            'name' => $validated['student_name'], // Nama mahasiswa sebagai name utama
            'student_name' => $validated['student_name'],
            'npm' => $validated['npm'],
            'faculty' => $validated['faculty'],
            'study_program' => $validated['study_program'],
            'email' => $validated['email'] ?? null,
            'whatsapp' => $phone,
            'payment_proof' => $validated['payment_proof'] ?? null,
            'guest_1_name' => $validated['guest_1_name'],
            'guest_2_name' => $validated['guest_2_name'] ?? null,
            'address' => '-', // Default
            'is_vip' => $validated['is_vip'],
            'group_id' => $validated['group_id'],
            'is_invited' => true,
            'guests_count' => $guestCount,
            'qr_code' => 'GUEST-' . strtoupper(Str::random(10)),
            'registration_date' => now(),
        ];

        $guest = Guest::create($guestData);

        // TODO: Generate QR Code image here using QRCodeService

        return redirect()->route('guests.index')
            ->with('success', 'Data wisuda "' . $guest->name . '" dengan ' . $guestCount . ' tamu berhasil ditambahkan!');
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
     * Search guests (only not checked in).
     */
    public function search(Request $request)
    {
        $event = Event::where('is_active', true)->first();

        if (!$event) {
            return redirect()->route('home')
                ->with('error', 'Tidak ada event aktif.');
        }

        $searchTerm = $request->get('q', '');

        // Get only guests who haven't checked in
        $guests = Guest::where('event_id', $event->id)
            ->whereDoesntHave('attendance')
            ->when($searchTerm, function($query) use ($searchTerm) {
                $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('address', 'like', '%' . $searchTerm . '%')
                      ->orWhere('whatsapp', 'like', '%' . $searchTerm . '%');
                });
            })
            ->with('group')
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
        if (!$guest->whatsapp) {
            return redirect()->back()
                ->with('error', 'Nomor WhatsApp tidak tersedia untuk tamu ini.');
        }

        $qrUrl = url('/whatsapp/undangan/' . $guest->qr_code);
        $message = "Haloo *{$guest->name}*,\n\nKamu diundang, harap tunjukan QR Code sebagai akses masuk.. 🙏\n\nDownload QR Code E-Invitation:\n{$qrUrl}";

        $waUrl = "https://wa.me/{$guest->whatsapp}?text=" . urlencode($message);

        return redirect()->away($waUrl);
    }

    /**
     * Bulk WhatsApp to filtered guests.
     */
    public function bulkWhatsApp(Request $request)
    {
        $event = Event::where('is_active', true)->first();
        $filter = $request->get('filter', 'all');
        $groupId = $request->get('group_id');

        $query = Guest::where('event_id', $event->id)
            ->whereNotNull('whatsapp')
            ->where('whatsapp', '!=', '');

        if ($filter === 'hadir') {
            $query->whereHas('attendance');
        } elseif ($filter === 'belum') {
            $query->whereDoesntHave('attendance');
        } elseif ($filter === 'group' && $groupId) {
            $query->where('group_id', $groupId);
        }

        $guests = $query->get();

        if ($guests->isEmpty()) {
            return redirect()->back()
                ->with('error', 'Tidak ada tamu dengan nomor WhatsApp.');
        }

        // For bulk WhatsApp, we'll open multiple tabs (or create a queue job)
        // For now, let's show a summary page
        return view('guests.bulk-whatsapp', compact('guests', 'event'));
    }
}
