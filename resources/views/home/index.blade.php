@extends('layouts.app')

@section('title', 'Home - TamuKami')

@section('content')
<!-- Event Info Card -->
<div class="gradient-bg text-white rounded-3xl p-6 mb-6 card-shadow">
    <div class="flex items-center justify-center mb-4">
        <div class="w-24 h-24 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center backdrop-blur-lg">
            <i class="fas fa-qrcode text-white text-4xl"></i>
        </div>
    </div>

    <div class="text-center">
        <p class="text-sm opacity-90 mb-1">{{ $event->type ?? 'Demo Event' }}</p>
        <h2 class="text-2xl font-bold mb-2">{{ $event->name ?? 'Demo Event' }}</h2>
        <p class="text-sm opacity-90">
            <i class="fas fa-calendar-alt mr-2"></i>
            {{ $event->date ?? now()->format('l, d F Y') }}
        </p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-2xl p-4 card-shadow text-center">
        <div class="text-3xl font-bold text-purple-600 mb-1">{{ $stats['invited'] ?? 0 }}</div>
        <div class="text-xs text-gray-600">Undangan</div>
    </div>

    <div class="bg-white rounded-2xl p-4 card-shadow text-center">
        <div class="text-3xl font-bold text-green-600 mb-1">{{ $stats['attended'] ?? 0 }}</div>
        <div class="text-xs text-gray-600">Hadir</div>
    </div>

    <div class="bg-white rounded-2xl p-4 card-shadow text-center">
        <div class="text-3xl font-bold text-blue-600 mb-1">{{ $stats['total'] ?? 0 }}</div>
        <div class="text-xs text-gray-600">Total Tamu</div>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-2xl p-6 card-shadow mb-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4">Menu Utama</h3>

    <div class="grid grid-cols-3 gap-4">
        <!-- Data Tamu -->
        <a href="{{ route('guests.index') }}" class="flex flex-col items-center p-4 rounded-xl hover:bg-purple-50 transition">
            <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mb-3">
                <i class="fas fa-users text-purple-600 text-2xl"></i>
            </div>
            <span class="text-sm text-center font-medium text-gray-700">Data Tamu</span>
        </a>

        <!-- Check-in -->
        <a href="{{ route('checkin.index') }}" class="flex flex-col items-center p-4 rounded-xl hover:bg-green-50 transition">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-3">
                <i class="fas fa-qrcode text-green-600 text-2xl"></i>
            </div>
            <span class="text-sm text-center font-medium text-gray-700">Check-In</span>
        </a>

        <!-- Kehadiran -->
        <a href="{{ route('attendance.index') }}" class="flex flex-col items-center p-4 rounded-xl hover:bg-blue-50 transition">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-3">
                <i class="fas fa-clipboard-check text-blue-600 text-2xl"></i>
            </div>
            <span class="text-sm text-center font-medium text-gray-700">Kehadiran</span>
        </a>

        <!-- Souvenir -->
        <a href="{{ route('souvenir.index') }}" class="flex flex-col items-center p-4 rounded-xl hover:bg-yellow-50 transition">
            <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mb-3">
                <i class="fas fa-gift text-yellow-600 text-2xl"></i>
            </div>
            <span class="text-sm text-center font-medium text-gray-700">Souvenir</span>
        </a>

        <!-- Ucapan -->
        <a href="{{ route('greeting.index') }}" class="flex flex-col items-center p-4 rounded-xl hover:bg-pink-50 transition">
            <div class="w-16 h-16 bg-pink-100 rounded-full flex items-center justify-center mb-3">
                <i class="fas fa-comments text-pink-600 text-2xl"></i>
            </div>
            <span class="text-sm text-center font-medium text-gray-700">Ucapan</span>
        </a>

        <!-- Layar Sapa -->
        <a href="#" class="flex flex-col items-center p-4 rounded-xl hover:bg-indigo-50 transition">
            <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mb-3">
                <i class="fas fa-tv text-indigo-600 text-2xl"></i>
            </div>
            <span class="text-sm text-center font-medium text-gray-700">Layar Sapa</span>
        </a>
    </div>
</div>

<!-- Recent Guests -->
<div class="bg-white rounded-2xl p-6 card-shadow">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold text-gray-800">Tamu Terbaru</h3>
        <a href="{{ route('guests.index') }}" class="text-purple-600 text-sm font-medium hover:text-purple-800">
            Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>

    @if(isset($recentGuests) && count($recentGuests) > 0)
    <div class="space-y-3">
        @foreach($recentGuests as $guest)
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl hover:bg-gray-100 transition">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-purple-600"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800">{{ $guest->name }}</h4>
                    <p class="text-sm text-gray-500">{{ $guest->address }}</p>
                </div>
            </div>

            @if($guest->is_vip)
            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">
                VIP
            </span>
            @endif
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-8 text-gray-500">
        <i class="fas fa-users text-4xl mb-3"></i>
        <p>Belum ada data tamu</p>
    </div>
    @endif
</div>
@endsection
