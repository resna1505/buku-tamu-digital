@extends('layouts.app')

@section('title', 'Check-In Berhasil')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Success Animation -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-32 h-32 bg-green-100 rounded-full mb-6">
            <i class="fas fa-check-circle text-green-600 text-6xl"></i>
        </div>

        <h2 class="text-3xl font-bold text-gray-800 mb-2">Check-In Berhasil!</h2>
        <p class="text-gray-600">Selamat datang di acara kami</p>
    </div>

    <!-- Guest Information Card -->
    <div class="bg-white rounded-3xl p-8 card-shadow mb-6">
        <!-- Guest Name -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-purple-100 rounded-full mb-4">
                <i class="fas fa-user text-purple-600 text-3xl"></i>
            </div>

            <h3 class="text-2xl font-bold text-gray-800 mb-1">{{ $guest->name }}</h3>

            @if($guest->is_vip)
            <span class="inline-block px-4 py-2 bg-yellow-100 text-yellow-800 text-sm font-semibold rounded-full">
                <i class="fas fa-crown mr-1"></i>VIP Guest
            </span>
            @endif
        </div>

        <!-- Guest Details -->
        <div class="space-y-4">
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-map-marker-alt text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Alamat</p>
                        <p class="font-semibold text-gray-800">{{ $guest->address }}</p>
                    </div>
                </div>
            </div>

            @if($guest->table_number)
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-chair text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Nomor Meja</p>
                        <p class="font-semibold text-gray-800">{{ $guest->table_number }}</p>
                    </div>
                </div>
            </div>
            @endif

            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-users text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Jumlah Tamu</p>
                        <p class="font-semibold text-gray-800">{{ $guest->guests_count }} Orang</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-tag text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Grup</p>
                        <p class="font-semibold text-gray-800">{{ $guest->group->name ?? 'Tidak ada grup' }}</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between p-4 bg-green-50 rounded-xl border-2 border-green-200">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Waktu Check-In</p>
                        <p class="font-semibold text-gray-800">
                            {{ $guest->attendance->checked_in_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="space-y-3">
        <a href="{{ route('checkin.index') }}" class="block w-full btn-primary text-white py-4 rounded-xl font-semibold text-center">
            <i class="fas fa-qrcode mr-2"></i>Scan QR Code Lagi
        </a>

        <a href="{{ route('attendance.index') }}" class="block w-full bg-white text-gray-700 py-4 rounded-xl font-semibold text-center border-2 border-gray-300 hover:bg-gray-50 transition">
            <i class="fas fa-list mr-2"></i>Lihat Daftar Kehadiran
        </a>

        <a href="{{ route('home') }}" class="block w-full bg-gray-200 text-gray-700 py-4 rounded-xl font-semibold text-center hover:bg-gray-300 transition">
            <i class="fas fa-home mr-2"></i>Kembali ke Home
        </a>
    </div>
</div>

@push('styles')
<style>
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .max-w-2xl {
        animation: fadeInUp 0.5s ease;
    }
</style>
@endpush
@endsection
