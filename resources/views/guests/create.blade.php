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

        <!-- Info Data Mahasiswa -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
            <h3 class="font-semibold text-blue-900 mb-2">
                <i class="fas fa-user-graduate mr-2"></i>Data Mahasiswa
            </h3>
            <p class="text-sm text-blue-700">Isi data mahasiswa yang wisuda</p>
        </div>

        <!-- Nama Mahasiswa -->
        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2">
                Nama Lengkap Mahasiswa/i <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                name="student_name"
                value="{{ old('student_name') }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-purple-500 @error('student_name') border-red-500 @enderror"
                placeholder="Nama lengkap mahasiswa"
                required
            >
            @error('student_name')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- NPM -->
            <div>
                <label class="block text-gray-700 font-semibold mb-2">
                    NPM <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="npm"
                    value="{{ old('npm') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-purple-500 @error('npm') border-red-500 @enderror"
                    placeholder="123456789"
                    required
                >
                @error('npm')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label class="block text-gray-700 font-semibold mb-2">
                    Email
                </label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-purple-500 @error('email') border-red-500 @enderror"
                    placeholder="mahasiswa@example.com"
                >
                @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Fakultas -->
            <div>
                <label class="block text-gray-700 font-semibold mb-2">
                    Fakultas <span class="text-red-500">*</span>
                </label>
                <select
                    name="faculty"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-purple-500 @error('faculty') border-red-500 @enderror"
                    required
                >
                    <option value="">Pilih Fakultas</option>
                    <option value="Teknik" {{ old('faculty') == 'Teknik' ? 'selected' : '' }}>Teknik</option>
                    <option value="Ekonomi" {{ old('faculty') == 'Ekonomi' ? 'selected' : '' }}>Ekonomi</option>
                    <option value="Hukum" {{ old('faculty') == 'Hukum' ? 'selected' : '' }}>Hukum</option>
                    <option value="Kedokteran" {{ old('faculty') == 'Kedokteran' ? 'selected' : '' }}>Kedokteran</option>
                    <option value="Ilmu Kesehatan" {{ old('faculty') == 'Ilmu Kesehatan' ? 'selected' : '' }}>Ilmu Kesehatan</option>
                </select>
                @error('faculty')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Program Studi -->
            <div>
                <label class="block text-gray-700 font-semibold mb-2">
                    Program Studi <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="study_program"
                    value="{{ old('study_program') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-purple-500 @error('study_program') border-red-500 @enderror"
                    placeholder="Contoh: S1 SISTEM INFORMASI"
                    required
                >
                @error('study_program')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Nomor WhatsApp -->
        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2">
                Nomor WhatsApp (Aktif) <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                name="whatsapp"
                value="{{ old('whatsapp') }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-purple-500 @error('whatsapp') border-red-500 @enderror"
                placeholder="628123456789"
                required
            >
            @error('whatsapp')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
            <p class="text-sm text-gray-500 mt-1">
                <i class="fas fa-info-circle mr-1"></i>Format: 628123456789 (tanpa spasi)
            </p>
        </div>

        <!-- Divider -->
        <hr class="my-8 border-gray-200">

        <!-- Info Data Tamu -->
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
            <h3 class="font-semibold text-green-900 mb-2">
                <i class="fas fa-users mr-2"></i>Data Tamu Undangan
            </h3>
            <p class="text-sm text-green-700">Orang tua / Wali / Saudara yang akan hadir</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Tamu 1 -->
            <div>
                <label class="block text-gray-700 font-semibold mb-2">
                    Nama Tamu 1 (Ayah/Ibu/Wali) <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="guest_1_name"
                    value="{{ old('guest_1_name') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-purple-500 @error('guest_1_name') border-red-500 @enderror"
                    placeholder="Nama lengkap tamu 1"
                    required
                >
                @error('guest_1_name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tamu 2 -->
            <div>
                <label class="block text-gray-700 font-semibold mb-2">
                    Nama Tamu 2 (Ayah/Ibu/Wali)
                </label>
                <input
                    type="text"
                    name="guest_2_name"
                    value="{{ old('guest_2_name') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-purple-500 @error('guest_2_name') border-red-500 @enderror"
                    placeholder="Nama lengkap tamu 2 (opsional)"
                >
                @error('guest_2_name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Divider -->
        <hr class="my-8 border-gray-200">

        <!-- Info Pengaturan Tambahan -->
        <div class="bg-purple-50 border border-purple-200 rounded-xl p-4 mb-6">
            <h3 class="font-semibold text-purple-900 mb-2">
                <i class="fas fa-cog mr-2"></i>Pengaturan Tambahan
            </h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Grup Tamu -->
            <div>
    <label class="block text-gray-700 font-semibold mb-2">
        Grup Tamu <span class="text-red-500">*</span>
    </label>
    <select
        name="group_id"
        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-purple-500 @error('group_id') border-red-500 @enderror"
        required
    >
        @if(!empty($groups))
            <option value="">Pilih Grup</option>
            @foreach($groups as $index => $group)
                <option
                    value="{{ $group->id }}"
                    {{ old('group_id', $loop->first ? $group->id : '') == $group->id ? 'selected' : '' }}
                >
                    {{ $group->name }}
                </option>
            @endforeach
        @else
            <option value="">Tidak ada grup tersedia</option>
        @endif
    </select>

    @error('group_id')
    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>


            <!-- Tamu VIP -->
            <div>
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
        </div>

        <!-- Bukti Pembayaran (Optional) -->
        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2">
                Link Bukti Pembayaran
            </label>
            <input
                type="url"
                name="payment_proof"
                value="{{ old('payment_proof') }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-purple-500 @error('payment_proof') border-red-500 @enderror"
                placeholder="https://drive.google.com/..."
            >
            @error('payment_proof')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
            <p class="text-sm text-gray-500 mt-1">
                <i class="fas fa-info-circle mr-1"></i>Link Google Drive atau layanan cloud lainnya
            </p>
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
    <button id="importExcelBtn" class="w-full bg-green-500 text-white py-4 rounded-xl font-semibold text-lg hover:bg-green-600 transition shadow-lg">
        <i class="fas fa-file-excel mr-2"></i>Import dari Excel (Batch)
    </button>
</div>

@push('scripts')
<script>
    // Form validation
    document.getElementById('guestForm').addEventListener('submit', function(e) {
        const studentName = document.querySelector('input[name="student_name"]').value;
        const npm = document.querySelector('input[name="npm"]').value;
        const faculty = document.querySelector('select[name="faculty"]').value;
        const guest1 = document.querySelector('input[name="guest_1_name"]').value;
        const group = document.querySelector('select[name="group_id"]').value;
        const whatsapp = document.querySelector('input[name="whatsapp"]').value;

        if (!studentName || !npm || !faculty || !guest1 || !group || !whatsapp) {
            e.preventDefault();
            alert('Mohon lengkapi semua field yang wajib diisi (*)!');
            return false;
        }
    });

    // Import Excel button
    document.getElementById('importExcelBtn').addEventListener('click', function() {
        window.location.href = '{{ route("guests.import") }}';
    });

    // Auto-count guests
    document.querySelector('input[name="guest_2_name"]').addEventListener('input', function() {
        const guest2Value = this.value.trim();
        // Jika ada tamu 2, jumlah = 2, kalau tidak = 1
        // (guests_count dihandle otomatis di controller)
    });
</script>
@endpush
@endsection
