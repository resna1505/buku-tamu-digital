@extends('layouts.app')

@section('title', 'Tambah Tamu')

@section('content')
<!-- Header -->
<div class="flex items-center mb-6">
    <a href="{{ route('guests.index') }}" class="mr-4 text-purple-600 hover:text-purple-800">
        <i class="fas fa-arrow-left text-2xl"></i>
    </a>
    <h2 class="text-2xl font-bold text-gray-800">Tambah Data Tamu</h2>
</div>

<!-- Form Card -->
<div class="bg-white rounded-2xl p-6 card-shadow">
    <form method="POST" action="{{ route('guests.store') }}" id="guestForm">
        @csrf

        <!-- Nama Tamu -->
        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2">
                Nama Tamu <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                name="name"
                value="{{ old('name') }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-purple-500 @error('name') border-red-500 @enderror"
                placeholder="Isikan Nama Tamu"
                required
            >
            @error('name')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Alamat / Keterangan -->
        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2">
                Alamat / Keterangan Lain <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                name="address"
                value="{{ old('address') }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-purple-500 @error('address') border-red-500 @enderror"
                placeholder="Isikan (-) Jika nihil"
                required
            >
            @error('address')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Nomor WhatsApp -->
        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2">
                Nomor WhatsApp
            </label>
            <input
                type="text"
                name="whatsapp"
                value="{{ old('whatsapp') }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-purple-500 @error('whatsapp') border-red-500 @enderror"
                placeholder="Contoh: 628971851xxx"
            >
            @error('whatsapp')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
            <p class="text-sm text-gray-500 mt-1">
                <i class="fas fa-info-circle mr-1"></i>Format: 628971851xxx (tanpa tanda +)
            </p>
        </div>

        <!-- Nomor Meja -->
        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2">
                Nomor Meja
            </label>
            <input
                type="text"
                name="table_number"
                value="{{ old('table_number') }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-purple-500 @error('table_number') border-red-500 @enderror"
                placeholder="Nomor Meja"
            >
            @error('table_number')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tamu VIP -->
        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2">
                Tamu VIP? <span class="text-red-500">*</span>
            </label>
            <select
                name="is_vip"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-purple-500 @error('is_vip') border-red-500 @enderror"
                required
            >
                <option value="0" {{ old('is_vip') == '0' ? 'selected' : '' }}>Tidak</option>
                <option value="1" {{ old('is_vip') == '1' ? 'selected' : '' }}>Ya</option>
            </select>
            @error('is_vip')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Grup Tamu -->
        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2">
                Grup Tamu <span class="text-red-500">*</span>
            </label>
            <select
                name="group_id"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-purple-500 @error('group_id') border-red-500 @enderror"
                required
            >
                <option value="">Pilih Grup</option>
                @foreach($groups ?? [] as $group)
                <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>
                    {{ $group->name }}
                </option>
                @endforeach
            </select>
            @error('group_id')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
            <p class="text-sm text-gray-500 mt-1">
                Jika belum ada grup tamu, silakan
                <a href="#" id="createGroupLink" class="text-purple-600 font-semibold">buat grup tamu</a>
            </p>
        </div>

        <!-- Jumlah Tamu -->
        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2">
                Jumlah Tamu
            </label>
            <input
                type="number"
                name="guests_count"
                value="{{ old('guests_count', 1) }}"
                min="1"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-purple-500 @error('guests_count') border-red-500 @enderror"
                placeholder="1"
            >
            @error('guests_count')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Action Buttons -->
        <div class="flex space-x-3">
            <button
                type="submit"
                class="flex-1 btn-primary text-white py-4 rounded-xl font-semibold text-lg"
            >
                <i class="fas fa-save mr-2"></i>Simpan
            </button>

            <a
                href="{{ route('guests.index') }}"
                class="flex-1 bg-gray-300 text-gray-700 py-4 rounded-xl font-semibold text-lg text-center hover:bg-gray-400 transition"
            >
                <i class="fas fa-times mr-2"></i>Tutup
            </a>
        </div>
    </form>
</div>

<!-- Import Excel Button -->
<div class="mt-6">
    <button id="importExcelBtn" class="w-full bg-green-500 text-white py-4 rounded-xl font-semibold text-lg hover:bg-green-600 transition">
        <i class="fas fa-file-excel mr-2"></i>Import dari Excel
    </button>
</div>

@push('scripts')
<script>
    // Form validation
    document.getElementById('guestForm').addEventListener('submit', function(e) {
        const name = document.querySelector('input[name="name"]').value;
        const address = document.querySelector('input[name="address"]').value;
        const group = document.querySelector('select[name="group_id"]').value;

        if (!name || !address || !group) {
            e.preventDefault();
            alert('Mohon lengkapi semua field yang wajib diisi!');
            return false;
        }
    });

    // Create group link
    document.getElementById('createGroupLink').addEventListener('click', function(e) {
        e.preventDefault();
        alert('Fitur tambah grup akan segera tersedia!');
    });

    // Import Excel button
    document.getElementById('importExcelBtn').addEventListener('click', function() {
        window.location.href = '{{ route("guests.import") }}';
    });
</script>
@endpush
@endsection
