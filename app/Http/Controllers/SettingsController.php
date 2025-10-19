<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Guest;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display settings page.
     */
    public function index()
    {
        $event = Event::where('is_active', true)->first();

        $stats = [
            'total' => Guest::count(),
            'attended' => Attendance::count(),
        ];

        return view('settings.index', compact('event', 'stats'));
    }

    /**
     * Update user profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'email' => 'required|email|unique:users,email,' . $user->id,
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:6',
            'phone' => 'nullable|string|max:20',
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'username.required' => 'Username harus diisi',
            'username.unique' => 'Username sudah digunakan',
            'password.min' => 'Password minimal 6 karakter',
            'phone.max' => 'Nomor telepon maksimal 20 karakter',
        ]);

        // Update email and username
        $user->email = $validated['email'];
        $user->username = $validated['username'];

        // Update password if provided
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        // Update phone if provided
        if (isset($validated['phone'])) {
            $user->phone = $validated['phone'];
        }

        $user->save();

        return redirect()->route('settings.index')
            ->with('success', 'Profil berhasil diperbarui!');
    }
}
