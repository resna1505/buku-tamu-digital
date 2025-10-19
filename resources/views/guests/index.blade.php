@extends('layouts.app')

@section('title', 'Data Tamu')

@section('content')
<!-- Header -->
<div class="bg-white rounded-2xl p-6 card-shadow mb-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Data Tamu</h2>
            <p class="text-sm text-gray-600">Undangan: {{ $stats['invited'] ?? 0 }} | Hadir: {{ $stats['attended'] ?? 0 }} | Total: {{ $stats['total'] ?? 0 }}</p>
        </div>
        <button id="addGuestBtn" class="btn-primary text-white px-4 py-2 rounded-xl">
            <i class="fas fa-plus mr-2"></i>Tambah
        </button>
    </div>

    <!-- Filter Tabs -->
    <div class="flex space-x-2 mb-4 overflow-x-auto">
        <button class="filter-tab active px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap" data-filter="all">
            Semua Tamu
        </button>
        <button class="filter-tab px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap" data-filter="hadir">
            Tamu Hadir
        </button>
        <button class="filter-tab px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap" data-filter="belum">
            Belum Hadir
        </button>
        @foreach($groups ?? [] as $group)
        <button class="filter-tab px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap" data-filter="group" data-group-id="{{ $group->id }}">
            {{ $group->name }}
        </button>
        @endforeach
    </div>

    <!-- Export & Bulk Actions -->
    <div class="flex flex-wrap gap-2">
        <button id="bulkWhatsAppBtn" class="export-btn bg-green-500 text-white px-4 py-2 rounded-lg text-sm flex items-center hover:bg-green-600 transition">
            <i class="fab fa-whatsapp mr-2"></i>Kirim WA Massal
        </button>
        <button id="exportPdfBtn" class="export-btn bg-red-500 text-white px-4 py-2 rounded-lg text-sm flex items-center hover:bg-red-600 transition">
            <i class="fas fa-file-pdf mr-2"></i>PDF
        </button>
        <button id="exportExcelBtn" class="export-btn bg-blue-500 text-white px-4 py-2 rounded-lg text-sm flex items-center hover:bg-blue-600 transition">
            <i class="fas fa-file-excel mr-2"></i>Excel
        </button>
    </div>
</div>

<!-- Search Bar -->
<div class="bg-white rounded-2xl p-4 card-shadow mb-6">
    <div class="relative">
        <input
            type="text"
            id="searchInput"
            class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-xl focus:outline-none focus:border-purple-500"
            placeholder="Cari nama tamu..."
        >
        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
    </div>
</div>

<!-- Guest List -->
<div class="bg-white rounded-2xl p-6 card-shadow">
    <div id="guestList" class="space-y-3">
        @forelse($guests ?? [] as $index => $guest)
        <div class="guest-item flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition"
             data-guest-id="{{ $guest->id }}"
             data-guest-name="{{ $guest->name }}"
             data-group-id="{{ $guest->group_id }}"
             data-has-attendance="{{ $guest->attendance ? 'true' : 'false' }}">
            <div class="flex items-center space-x-4 flex-1">
                <div class="text-gray-500 font-semibold">{{ $index + 1 }}.</div>
                <div class="flex-1">
                    <div class="flex items-center space-x-2">
                        <h4 class="font-semibold text-gray-800">{{ $guest->name }}</h4>
                        @if($guest->is_vip)
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">
                            VIP
                        </span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500">{{ $guest->address }} | {{ $guest->guests_count }} Orang</p>
                    <p class="text-xs text-gray-400">{{ $guest->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>

            <!-- Actions Menu -->
            <div class="relative">
                <button class="action-menu-btn text-gray-600 hover:text-purple-600 p-2" data-guest-id="{{ $guest->id }}">
                    <i class="fas fa-ellipsis-v"></i>
                </button>

                <!-- Dropdown Menu -->
                <div class="action-menu hidden absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg z-10 border border-gray-200">
                    <a href="#" class="send-wa-btn block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 rounded-t-xl"
                       data-guest-id="{{ $guest->id }}"
                       data-guest-name="{{ $guest->name }}"
                       data-guest-phone="{{ $guest->whatsapp }}"
                       data-guest-qr="{{ $guest->qr_code }}">
                        <i class="fab fa-whatsapp text-green-600 mr-2"></i>Kirim WhatsApp
                    </a>
                    <a href="#" class="print-qr-btn block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50"
                       data-guest-id="{{ $guest->id }}"
                       data-guest-name="{{ $guest->name }}"
                       data-guest-address="{{ $guest->address }}"
                       data-guest-qr="{{ $guest->qr_code }}">
                        <i class="fas fa-qrcode text-purple-600 mr-2"></i>Cetak QR Code
                    </a>
                    <a href="{{ route('guests.edit', $guest->id) }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-edit text-blue-600 mr-2"></i>Edit Tamu
                    </a>
                    <a href="#" class="delete-guest-btn block px-4 py-3 text-sm text-red-600 hover:bg-red-50 rounded-b-xl"
                       data-guest-id="{{ $guest->id }}"
                       data-guest-name="{{ $guest->name }}">
                        <i class="fas fa-trash mr-2"></i>Hapus Tamu
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-12 text-gray-500">
            <i class="fas fa-users text-6xl mb-4 opacity-50"></i>
            <p class="text-lg">Belum ada data tamu</p>
            <button onclick="window.location.href='{{ route('guests.create') }}'" class="mt-4 btn-primary text-white px-6 py-3 rounded-xl">
                <i class="fas fa-plus mr-2"></i>Tambah Tamu Pertama
            </button>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if(isset($guests) && $guests->hasPages())
    <div class="flex justify-center mt-6">
        {{ $guests->links() }}
    </div>
    @endif
</div>

<!-- QR Code Print Modal -->
<div id="qrPrintModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-2xl w-full p-8 relative">
        <button id="closeQrModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
            <i class="fas fa-times text-2xl"></i>
        </button>

        <div id="qrPrintContent" class="text-center">
            <!-- QR Code will be generated here -->
        </div>

        <div class="flex space-x-3 mt-6">
            <button onclick="printQRCode()" class="flex-1 bg-purple-600 text-white py-3 rounded-xl font-semibold hover:bg-purple-700 transition">
                <i class="fas fa-print mr-2"></i>Print
            </button>
            <button onclick="downloadQRCode()" class="flex-1 bg-green-600 text-white py-3 rounded-xl font-semibold hover:bg-green-700 transition">
                <i class="fas fa-download mr-2"></i>Download
            </button>
        </div>
    </div>
</div>

@push('styles')
<style>
    .filter-tab {
        background: #f3f4f6;
        color: #6b7280;
        transition: all 0.3s ease;
    }

    .filter-tab.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .action-menu {
        animation: slideDown 0.2s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media print {
        body * {
            visibility: hidden;
        }
        #qrPrintContent, #qrPrintContent * {
            visibility: visible;
        }
        #qrPrintContent {
            position: absolute;
            left: 0;
            top: 0;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    const APP_URL = '{{ url('/') }}';
    const EVENT_NAME = '{{ $event->name ?? 'Demo Event' }}';
    const EVENT_DATE = '{{ $event->date ? $event->date->format('l, d F Y') : 'Kamis, 12 September 2024' }}';

    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        filterGuests();
    });

    // Filter tabs
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            filterGuests();
        });
    });

    function filterGuests() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const activeTab = document.querySelector('.filter-tab.active');
        const filterType = activeTab.dataset.filter;
        const groupId = activeTab.dataset.groupId;

        document.querySelectorAll('.guest-item').forEach(item => {
            const name = item.dataset.guestName.toLowerCase();
            const itemGroupId = item.dataset.groupId;
            const hasAttendance = item.dataset.hasAttendance === 'true';

            let showItem = true;

            // Search filter
            if (searchTerm && !name.includes(searchTerm)) {
                showItem = false;
            }

            // Tab filter
            if (filterType === 'hadir' && !hasAttendance) {
                showItem = false;
            } else if (filterType === 'belum' && hasAttendance) {
                showItem = false;
            } else if (filterType === 'group' && itemGroupId !== groupId) {
                showItem = false;
            }

            item.style.display = showItem ? '' : 'none';
        });
    }

    // Action menu toggle
    document.querySelectorAll('.action-menu-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const menu = this.nextElementSibling;

            document.querySelectorAll('.action-menu').forEach(m => {
                if (m !== menu) m.classList.add('hidden');
            });

            menu.classList.toggle('hidden');
        });
    });

    document.addEventListener('click', function() {
        document.querySelectorAll('.action-menu').forEach(menu => {
            menu.classList.add('hidden');
        });
    });

    // Add guest button
    document.getElementById('addGuestBtn').addEventListener('click', function() {
        window.location.href = '{{ route("guests.create") }}';
    });

    // Send WhatsApp Individual
    document.querySelectorAll('.send-wa-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const guestName = this.dataset.guestName;
            const guestPhone = this.dataset.guestPhone;
            const guestQr = this.dataset.guestQr;

            if (!guestPhone) {
                alert('Nomor WhatsApp tidak tersedia untuk tamu ini');
                return;
            }

            const qrUrl = `${APP_URL}/whatsapp/undangan/${guestQr}`;
            const message = `Haloo *${guestName}*,\n\nKamu diundang, harap tunjukan QR Code sebagai akses masuk.. 🙏\n\nDownload QR Code E-Invitation:\n${qrUrl}`;

            const waUrl = `https://wa.me/${guestPhone}?text=${encodeURIComponent(message)}`;
            window.open(waUrl, '_blank');
        });
    });

    // Bulk WhatsApp
    document.getElementById('bulkWhatsAppBtn').addEventListener('click', function() {
        const visibleGuests = [];
        document.querySelectorAll('.guest-item').forEach(item => {
            if (item.style.display !== 'none') {
                visibleGuests.push({
                    name: item.dataset.guestName,
                    id: item.dataset.guestId
                });
            }
        });

        if (visibleGuests.length === 0) {
            alert('Tidak ada tamu yang dipilih');
            return;
        }

        if (confirm(`Kirim WhatsApp ke ${visibleGuests.length} tamu?`)) {
            window.location.href = '{{ route("guests.bulk-whatsapp") }}?filter=' + document.querySelector('.filter-tab.active').dataset.filter;
        }
    });

    // Print QR Code
    let currentQRData = null;

    document.querySelectorAll('.print-qr-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const guestName = this.dataset.guestName;
            const guestAddress = this.dataset.guestAddress;
            const guestQr = this.dataset.guestQr;

            currentQRData = { name: guestName, address: guestAddress, qr: guestQr };
            generateQRCode(guestName, guestAddress, guestQr);
            document.getElementById('qrPrintModal').classList.remove('hidden');
        });
    });

    document.getElementById('closeQrModal').addEventListener('click', function() {
        document.getElementById('qrPrintModal').classList.add('hidden');
    });

    function generateQRCode(guestName, guestAddress, qrCode) {
        const content = `
            <div class="bg-white p-8 border-4 border-gray-200 rounded-2xl inline-block">
                <div class="text-center mb-6">
                    <div class="flex items-center justify-center space-x-3 mb-4">
                        <i class="fas fa-qrcode text-4xl text-purple-600"></i>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">TAMU</h2>
                            <p class="text-xl font-bold text-yellow-600">KAMI</p>
                        </div>
                    </div>
                    <div class="h-1 bg-gradient-to-r from-purple-600 to-yellow-600 rounded"></div>
                </div>

                <div class="bg-gray-50 p-6 rounded-xl mb-6">
                    <p class="text-sm text-gray-600 mb-2">${EVENT_NAME}</p>
                    <h3 class="text-3xl font-bold text-gray-800 mb-4">${EVENT_NAME}</h3>
                    <p class="text-gray-600">${EVENT_DATE}</p>
                </div>

                <div class="mb-6">
                    <p class="text-gray-600 mb-2">Dear,</p>
                    <h4 class="text-2xl font-bold text-gray-800 mb-4">${guestName}</h4>
                    <p class="text-sm text-gray-600">Alamat/Keterangan:</p>
                    <p class="text-lg font-semibold text-gray-800">${guestAddress}</p>
                </div>

                <div id="qrcodeCanvas" class="flex justify-center mb-6"></div>

                <div class="bg-black text-white p-4 rounded-lg text-center">
                    <div class="flex items-center justify-center space-x-2 mb-2">
                        <i class="fas fa-qrcode text-xl text-yellow-500"></i>
                        <span class="font-bold text-lg">TAMU <span class="text-yellow-500">KAMI</span></span>
                    </div>
                    <p class="text-sm">HARAP TUNJUKKAN KARTU INI</p>
                    <p class="text-xs">SEBAGAI AKSES MASUK LOKASI ACARA!</p>
                </div>
            </div>
        `;

        document.getElementById('qrPrintContent').innerHTML = content;

        // Generate QR Code
        new QRCode(document.getElementById('qrcodeCanvas'), {
            text: qrCode,
            width: 300,
            height: 300,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    }

    function printQRCode() {
        window.print();
    }

    function downloadQRCode() {
        // Implementation for download
        alert('Download QR Code feature coming soon!');
    }

    // Delete Guest
    document.querySelectorAll('.delete-guest-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const guestId = this.dataset.guestId;
            const guestName = this.dataset.guestName;

            if (confirm(`Hapus tamu "${guestName}"?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/guests/${guestId}`;

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';

                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        });
    });

    // Export PDF
    document.getElementById('exportPdfBtn').addEventListener('click', function() {
        window.location.href = '{{ route("guests.export.pdf") }}';
    });

    // Export Excel
    document.getElementById('exportExcelBtn').addEventListener('click', function() {
        window.location.href = '{{ route("guests.export.excel") }}';
    });
</script>
@endpush
@endsection
