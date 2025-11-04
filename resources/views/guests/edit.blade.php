@extends('layouts.app')

@section('title', 'Edit Data Tamu')

@section('content')
<!-- Header -->
<div class="flex items-center mb-6">
    <a href="{{ route('guests.index') }}" class="mr-4 text-purple-600 hover:text-purple-800">
        <i class="fas fa-arrow-left text-2xl"></i>
    </a>
    <h2 class="text-2xl font-bold text-gray-800">Edit Data Tamu</h2>
</div>

<!-- Form Card -->
<div class="bg-white rounded-2xl p-6 card-shadow">
    <form method="POST" action="{{ route('guests.update', $guest->id) }}" id="guestForm">
        @csrf
        @method('PUT')

        <!-- Info Data Mahasiswa -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
            <h3 class="font-semibold text-blue-900 mb-2">
                <i class="fas fa-user-graduate mr-2"></i>Data Mahasiswa
            </h3>
            <p class="text-sm text-blue-700">Data mahasiswa yang wisuda</p>
        </div>

        <!-- Nama Mahasiswa -->
        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2">
                Nama Lengkap Mahasiswa/i <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                name="student_name"
                value="{{ old('student_name', $guest->student_name) }}"
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
                    value="{{ old('npm', $guest->npm) }}"
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
                    value="{{ old('email', $guest->email) }}"
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
                    <option value="Teknik" {{ old('faculty', $guest->faculty) == 'Teknik' ? 'selected' : '' }}>Teknik</option>
                    <option value="Ekonomi" {{ old('faculty', $guest->faculty) == 'Ekonomi' ? 'selected' : '' }}>Ekonomi</option>
                    <option value="Hukum" {{ old('faculty', $guest->faculty) == 'Hukum' ? 'selected' : '' }}>Hukum</option>
                    <option value="FISIP" {{ old('faculty', $guest->faculty) == 'FISIP' ? 'selected' : '' }}>FISIP</option>
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
                    value="{{ old('study_program', $guest->study_program) }}"
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
                value="{{ old('whatsapp', $guest->whatsapp) }}"
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

        <!-- Tamu 1 -->
        <div class="bg-gray-50 rounded-xl p-4 mb-6">
            <h4 class="font-semibold text-gray-800 mb-4">
                <i class="fas fa-user mr-2"></i>Tamu 1
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Tamu 1 -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        Nama Tamu 1 <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="guest_1_name"
                        value="{{ old('guest_1_name', $guest->guest_1_name) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-purple-500 @error('guest_1_name') border-red-500 @enderror"
                        placeholder="Nama lengkap tamu 1"
                        required
                    >
                    @error('guest_1_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor Kursi Tamu 1 -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        Nomor Kursi Tamu 1 <span class="text-orange-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="seat_number_guest_1"
                        value="{{ old('seat_number_guest_1', $guest->seat_number_guest_1) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-orange-500 @error('seat_number_guest_1') border-red-500 @enderror"
                        placeholder="Contoh: A-12"
                    >
                    @error('seat_number_guest_1')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-gray-500 mt-1">
                        <i class="fas fa-chair mr-1"></i>Isi nomor kursi untuk tamu 1
                    </p>
                </div>
            </div>
        </div>

        <!-- Tamu 2 -->
        <div class="bg-gray-50 rounded-xl p-4 mb-6">
            <h4 class="font-semibold text-gray-800 mb-4">
                <i class="fas fa-user mr-2"></i>Tamu 2 (Opsional)
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Tamu 2 -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        Nama Tamu 2
                    </label>
                    <input
                        type="text"
                        name="guest_2_name"
                        value="{{ old('guest_2_name', $guest->guest_2_name) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-purple-500 @error('guest_2_name') border-red-500 @enderror"
                        placeholder="Nama lengkap tamu 2 (opsional)"
                    >
                    @error('guest_2_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor Kursi Tamu 2 -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        Nomor Kursi Tamu 2
                    </label>
                    <input
                        type="text"
                        name="seat_number_guest_2"
                        value="{{ old('seat_number_guest_2', $guest->seat_number_guest_2) }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-orange-500 @error('seat_number_guest_2') border-red-500 @enderror"
                        placeholder="Contoh: A-13"
                    >
                    @error('seat_number_guest_2')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-gray-500 mt-1">
                        <i class="fas fa-chair mr-1"></i>Isi jika ada tamu 2
                    </p>
                </div>
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
                    <option value="">Pilih Grup</option>
                    @foreach($groups ?? [] as $group)
                    <option value="{{ $group->id }}" {{ old('group_id', $guest->group_id) == $group->id ? 'selected' : '' }}>
                        {{ $group->name }}
                    </option>
                    @endforeach
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
                    <option value="0" {{ old('is_vip', $guest->is_vip) == '0' ? 'selected' : '' }}>Tidak</option>
                    <option value="1" {{ old('is_vip', $guest->is_vip) == '1' ? 'selected' : '' }}>Ya</option>
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
                value="{{ old('payment_proof', $guest->payment_proof) }}"
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

        <!-- Info Box -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-yellow-600 text-xl mr-3 mt-1"></i>
                <div>
                    <h4 class="font-semibold text-yellow-900 mb-1">Info QR Code</h4>
                    <p class="text-sm text-yellow-700">
                        QR Code: <span class="font-mono font-semibold">{{ $guest->qr_code }}</span>
                    </p>
                    <p class="text-sm text-yellow-700 mt-1">
                        Jumlah tamu saat ini: <span class="font-semibold">{{ $guest->guests_count }}</span> orang
                    </p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex space-x-3">
            <button
                type="submit"
                class="flex-1 btn-primary text-white py-4 rounded-xl font-semibold text-lg"
            >
                <i class="fas fa-save mr-2"></i>Update Data
            </button>

            <a
                href="{{ route('guests.index') }}"
                class="flex-1 bg-gray-300 text-gray-700 py-4 rounded-xl font-semibold text-lg text-center hover:bg-gray-400 transition"
            >
                <i class="fas fa-times mr-2"></i>Batal
            </a>
        </div>
    </form>
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

    // Auto-count guests based on guest_2_name
    const guest2Input = document.querySelector('input[name="guest_2_name"]');
    guest2Input.addEventListener('input', function() {
        // Info: guests_count akan di-handle otomatis di controller
        console.log('Tamu 2:', this.value ? 'Ada' : 'Tidak ada');
    });
</script>
@endpush
@endsection
