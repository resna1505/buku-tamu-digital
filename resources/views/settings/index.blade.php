@extends('layouts.app')

@section('title', 'Pengaturan')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Pengaturan</h2>
        <p class="text-gray-600">Kelola profil dan pengaturan akun Anda</p>
    </div>

    <!-- Update User Form -->
    <div class="bg-white rounded-2xl p-6 card-shadow mb-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-800">Update User</h3>
            <button id="closeUpdateForm" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('settings.update') }}" id="updateUserForm">
            @csrf
            @method('PUT')

            <!-- Event Name (Read Only) -->
            <div class="mb-4">
                <input
                    type="text"
                    value="{{ $event->name ?? 'Demo Event' }}"
                    class="w-full px-4 py-3 bg-gray-100 border rounded-xl text-gray-700 cursor-not-allowed"
                    readonly
                >
            </div>

            <!-- Email -->
            <div class="mb-4">
                <input
                    type="email"
                    name="email"
                    value="{{ old('email', Auth::user()->email) }}"
                    class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:border-purple-500 @error('email') border-red-500 @enderror"
                    placeholder="Email"
                    required
                >
                @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Username -->
            <div class="mb-4">
                <input
                    type="text"
                    name="username"
                    value="{{ old('username', Auth::user()->username) }}"
                    class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:border-purple-500 @error('username') border-red-500 @enderror"
                    placeholder="Username"
                    required
                >
                @error('username')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password (Optional) -->
            <div class="mb-4">
                <input
                    type="password"
                    name="password"
                    class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:border-purple-500 @error('password') border-red-500 @enderror"
                    placeholder="Kosongkan jika tidak ubah password"
                >
                @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika tidak ingin mengubah password</p>
            </div>

            <!-- Phone -->
            <div class="mb-6">
                <input
                    type="text"
                    name="phone"
                    value="{{ old('phone', Auth::user()->phone) }}"
                    class="w-full px-4 py-3 border rounded-xl focus:outline-none focus:border-purple-500 @error('phone') border-red-500 @enderror"
                    placeholder="Nomor WA (diawali dengan 62)"
                >
                @error('phone')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">Contoh: 628123456789</p>
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                class="w-full btn-primary text-white py-4 rounded-xl font-semibold"
            >
                Update
            </button>
        </form>

        <!-- Close Button -->
        <button
            onclick="window.history.back()"
            class="w-full bg-gray-200 text-gray-700 py-4 rounded-xl font-semibold mt-3 hover:bg-gray-300 transition"
        >
            Close
        </button>
    </div>

    <!-- User Info Card -->
    <div class="bg-white rounded-2xl p-6 card-shadow mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Informasi Akun</h3>

        <div class="space-y-3">
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-user text-purple-600"></i>
                    <span class="text-sm text-gray-600">Nama</span>
                </div>
                <span class="font-semibold text-gray-800">{{ Auth::user()->name }}</span>
            </div>

            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-at text-purple-600"></i>
                    <span class="text-sm text-gray-600">Username</span>
                </div>
                <span class="font-semibold text-gray-800">{{ Auth::user()->username }}</span>
            </div>

            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-envelope text-purple-600"></i>
                    <span class="text-sm text-gray-600">Email</span>
                </div>
                <span class="font-semibold text-gray-800">{{ Auth::user()->email }}</span>
            </div>

            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-phone text-purple-600"></i>
                    <span class="text-sm text-gray-600">Nomor WA</span>
                </div>
                <span class="font-semibold text-gray-800">{{ Auth::user()->phone ?? '-' }}</span>
            </div>

            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-shield-alt text-purple-600"></i>
                    <span class="text-sm text-gray-600">Role</span>
                </div>
                <span class="px-3 py-1 bg-purple-100 text-purple-800 text-xs font-semibold rounded-full">
                    {{ ucfirst(Auth::user()->role) }}
                </span>
            </div>
        </div>
    </div>

    <!-- App Info -->
    <div class="bg-white rounded-2xl p-6 card-shadow">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Informasi Aplikasi</h3>

        <div class="space-y-3">
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                <span class="text-sm text-gray-600">Versi Aplikasi</span>
                <span class="font-semibold text-gray-800">1.0.0</span>
            </div>

            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                <span class="text-sm text-gray-600">Laravel Version</span>
                <span class="font-semibold text-gray-800">{{ app()->version() }}</span>
            </div>

            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                <span class="text-sm text-gray-600">Total Tamu</span>
                <span class="font-semibold text-gray-800">{{ $stats['total'] ?? 0 }}</span>
            </div>

            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                <span class="text-sm text-gray-600">Total Hadir</span>
                <span class="font-semibold text-gray-800">{{ $stats['attended'] ?? 0 }}</span>
            </div>
        </div>
    </div>

    <!-- Logout Button -->
    <div class="mt-6">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                type="submit"
                class="w-full bg-red-500 text-white py-4 rounded-xl font-semibold hover:bg-red-600 transition"
            >
                <i class="fas fa-sign-out-alt mr-2"></i>Logout
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Close button
    document.getElementById('closeUpdateForm')?.addEventListener('click', function() {
        window.history.back();
    });
</script>
@endpush
@endsection
