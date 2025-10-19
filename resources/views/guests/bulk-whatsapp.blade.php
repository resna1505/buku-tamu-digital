@extends('layouts.app')

@section('title', 'Kirim WhatsApp Massal')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Kirim WhatsApp Massal</h2>
        <p class="text-gray-600">Total {{ $guests->count() }} tamu akan menerima pesan</p>
    </div>

    <!-- Message Preview -->
    <div class="bg-white rounded-2xl p-6 card-shadow mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Preview Pesan</h3>
        <div class="bg-green-50 border-2 border-green-200 rounded-xl p-4">
            <p class="text-sm text-gray-700 whitespace-pre-line">Haloo <strong>[Nama Tamu]</strong>,

Kamu diundang, harap tunjukan QR Code sebagai akses masuk.. 🙏

Download QR Code E-Invitation:
[Link QR Code]</p>
        </div>
    </div>

    <!-- Guest List -->
    <div class="bg-white rounded-2xl p-6 card-shadow mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Daftar Penerima</h3>
        <div class="space-y-2 max-h-96 overflow-y-auto">
            @foreach($guests as $index => $guest)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                <div class="flex items-center space-x-3">
                    <span class="text-sm font-semibold text-gray-500">{{ $index + 1 }}.</span>
                    <div>
                        <h4 class="font-semibold text-gray-800">{{ $guest->name }}</h4>
                        <p class="text-xs text-gray-500">{{ $guest->whatsapp }}</p>
                    </div>
                </div>
                <button class="send-single-wa-btn bg-green-500 text-white px-3 py-1 rounded-lg text-xs hover:bg-green-600 transition"
                        data-guest-name="{{ $guest->name }}"
                        data-guest-phone="{{ $guest->whatsapp }}"
                        data-guest-qr="{{ $guest->qr_code }}">
                    <i class="fab fa-whatsapp mr-1"></i>Kirim
                </button>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex space-x-3">
        <button id="sendAllBtn" class="flex-1 bg-green-500 text-white py-4 rounded-xl font-semibold hover:bg-green-600 transition">
            <i class="fab fa-whatsapp mr-2"></i>Kirim Semua ({{ $guests->count() }})
        </button>
        <a href="{{ route('guests.index') }}" class="flex-1 bg-gray-300 text-gray-700 py-4 rounded-xl font-semibold text-center hover:bg-gray-400 transition">
            <i class="fas fa-times mr-2"></i>Batal
        </a>
    </div>

    <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-xl p-4">
        <p class="text-sm text-yellow-800">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>Catatan:</strong> Setiap pesan akan dibuka di tab baru WhatsApp. Pastikan pop-up blocker tidak aktif.
        </p>
    </div>
</div>

@push('scripts')
<script>
    const APP_URL = '{{ url('/') }}';
    const guests = @json($guests);

    // Send single WhatsApp
    document.querySelectorAll('.send-single-wa-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const guestName = this.dataset.guestName;
            const guestPhone = this.dataset.guestPhone;
            const guestQr = this.dataset.guestQr;

            sendWhatsApp(guestName, guestPhone, guestQr);
        });
    });

    // Send all WhatsApp
    document.getElementById('sendAllBtn').addEventListener('click', function() {
        if (confirm(`Kirim WhatsApp ke ${guests.length} tamu?\n\nSetiap pesan akan dibuka di tab baru.`)) {
            guests.forEach((guest, index) => {
                setTimeout(() => {
                    sendWhatsApp(guest.name, guest.whatsapp, guest.qr_code);
                }, index * 1000); // Delay 1 second between each
            });

            setTimeout(() => {
                alert('Semua pesan WhatsApp telah dibuka!');
                window.location.href = '{{ route("guests.index") }}';
            }, guests.length * 1000 + 1000);
        }
    });

    function sendWhatsApp(name, phone, qrCode) {
        const qrUrl = `${APP_URL}/whatsapp/undangan/${qrCode}`;
        const message = `Haloo *${name}*,\n\nKamu diundang, harap tunjukan QR Code sebagai akses masuk.. 🙏\n\nDownload QR Code E-Invitation:\n${qrUrl}`;
        const waUrl = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
        window.open(waUrl, '_blank');
    }
</script>
@endpush
@endsection
