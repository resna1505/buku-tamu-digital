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
    </div>
</div>

<!-- Search Bar -->
<div class="bg-white rounded-2xl p-4 card-shadow mb-6">
    <div class="relative">
        <input
            type="text"
            id="searchInput"
            class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-xl focus:outline-none focus:border-purple-500"
            placeholder="Cari nama tamu, NPM, fakultas... (mencari di semua data)"
            value="{{ request('search') }}"
        >
        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
        <div id="searchLoading" class="hidden absolute right-4 top-1/2 transform -translate-y-1/2">
            <i class="fas fa-spinner fa-spin text-purple-600"></i>
        </div>
    </div>
    <div id="searchInfo" class="mt-2 text-sm text-gray-600 {{ request('search') ? '' : 'hidden' }}">
        Menampilkan <span id="searchResultCount">{{ $guests->total() }}</span> hasil dari <span id="totalGuests">{{ $stats['total'] }}</span> tamu
    </div>
</div>

<!-- Guest List -->
<div class="bg-white rounded-2xl p-6 card-shadow">
    <div id="noResultsMessage" class="{{ $guests->isEmpty() && request('search') ? '' : 'hidden' }} text-center py-12 text-gray-500">
        <i class="fas fa-search text-6xl mb-4 opacity-50"></i>
        <p class="text-lg">Tidak ada tamu ditemukan</p>
        <p class="text-sm mt-2">Coba kata kunci lain atau filter berbeda</p>
        <button onclick="clearSearch()" class="mt-4 btn-primary text-white px-6 py-3 rounded-xl">
            <i class="fas fa-times mr-2"></i>Reset Pencarian
        </button>
    </div>

    <div id="guestList" class="space-y-3 {{ $guests->isEmpty() && request('search') ? 'hidden' : '' }}">
        @forelse($guests ?? [] as $index => $guest)
        <div class="guest-item flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition"
             data-guest-id="{{ $guest->id }}"
             data-group-id="{{ $guest->group_id }}"
             data-has-attendance="{{ $guest->attendance ? 'true' : 'false' }}">
            <div class="flex items-center space-x-4 flex-1">
                <div class="text-gray-500 font-semibold">{{ $guests->firstItem() + $index }}.</div>
                <div class="flex-1">
                    <div class="flex items-center space-x-2">
                        <h4 class="font-semibold text-gray-800">{{ $guest->name }}</h4>
                        @if($guest->is_vip)
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">
                            VIP
                        </span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500">{{ $guest->faculty }} | {{ $guest->study_program }}</p>
                    <p class="text-sm text-gray-500">NPM: {{ $guest->npm }} | {{ $guest->guests_count }} Orang</p>
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
                       data-guest-faculty="{{ $guest->faculty }}"
                       data-guest-program="{{ $guest->study_program }}"
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
    <div class="flex justify-center mt-6" id="paginationContainer">
        {{ $guests->appends(request()->query())->links() }}
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

        <div class="flex space-x-3 mt-6 no-print">
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
        /* Hide everything on the page */
        body * {
            visibility: hidden !important;
        }

        /* Show only the QR modal content */
        #qrPrintModal {
            visibility: visible !important;
            position: fixed !important;
            inset: 0 !important;
            background-color: white !important;
            z-index: 9999 !important;
        }

        #qrPrintModal * {
            visibility: visible !important;
        }

        /* Hide modal close button and action buttons in print */
        #qrPrintModal .no-print,
        #qrPrintModal button,
        #closeQrModal {
            display: none !important;
        }

        /* Center the QR card on page */
        #qrPrintContent {
            position: absolute !important;
            left: 50% !important;
            top: 50% !important;
            transform: translate(-50%, -50%) !important;
            width: 100% !important;
            max-width: 600px !important;
        }

        #qrCardForPrint {
            page-break-inside: avoid !important;
            box-shadow: none !important;
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
    const CSRF_TOKEN = '{{ csrf_token() }}';

    let searchTimeout = null;

    // Server-side search with debouncing
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.trim();

        // Clear previous timeout
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }

        // Show loading indicator
        document.getElementById('searchLoading').classList.remove('hidden');

        // Debounce search - wait 500ms after user stops typing
        searchTimeout = setTimeout(() => {
            performServerSearch(searchTerm);
        }, 500);
    });

    function performServerSearch(searchTerm) {
        const activeTab = document.querySelector('.filter-tab.active');
        const filterType = activeTab.dataset.filter;
        const groupId = activeTab.dataset.groupId;

        // Build URL with search and filter parameters
        const url = new URL(window.location.href);

        if (searchTerm) {
            url.searchParams.set('search', searchTerm);
        } else {
            url.searchParams.delete('search');
        }

        if (filterType && filterType !== 'all') {
            url.searchParams.set('filter', filterType);
            if (groupId) {
                url.searchParams.set('group_id', groupId);
            }
        } else {
            url.searchParams.delete('filter');
            url.searchParams.delete('group_id');
        }

        // Reload page with search parameters
        window.location.href = url.toString();
    }

    function clearSearch() {
        document.getElementById('searchInput').value = '';
        const url = new URL(window.location.href);
        url.searchParams.delete('search');
        window.location.href = url.toString();
    }

    // Filter tabs - work with search
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            const filterType = this.dataset.filter;
            const groupId = this.dataset.groupId;
            const searchTerm = document.getElementById('searchInput').value.trim();

            const url = new URL(window.location.href);

            if (searchTerm) {
                url.searchParams.set('search', searchTerm);
            }

            if (filterType && filterType !== 'all') {
                url.searchParams.set('filter', filterType);
                if (groupId) {
                    url.searchParams.set('group_id', groupId);
                }
            } else {
                url.searchParams.delete('filter');
                url.searchParams.delete('group_id');
            }

            window.location.href = url.toString();
        });
    });

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
        const visibleGuests = document.querySelectorAll('.guest-item').length;

        if (visibleGuests === 0) {
            alert('Tidak ada tamu yang dipilih');
            return;
        }

        if (confirm(`Kirim WhatsApp ke ${visibleGuests} tamu yang terlihat di halaman ini?`)) {
            const activeTab = document.querySelector('.filter-tab.active');
            const url = '{{ route("guests.bulk-whatsapp") }}?filter=' + activeTab.dataset.filter;
            window.location.href = url;
        }
    });

    // Print QR Code
    let currentQRData = null;

    document.querySelectorAll('.print-qr-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const guestName = this.dataset.guestName;
            const guestFaculty = this.dataset.guestFaculty || '';
            const guestProgram = this.dataset.guestProgram || '';
            const guestQr = this.dataset.guestQr;

            // Combine faculty and program for address display
            const addressDisplay = guestFaculty + (guestProgram ? ', ' + guestProgram : '');

            currentQRData = {
                name: guestName,
                faculty: guestFaculty,
                program: guestProgram,
                qr: guestQr
            };

            generateQRCode(guestName, addressDisplay, guestQr, guestFaculty);
            document.getElementById('qrPrintModal').classList.remove('hidden');
        });
    });

    document.getElementById('closeQrModal').addEventListener('click', function() {
        document.getElementById('qrPrintModal').classList.add('hidden');
    });

    function generateQRCode(guestName, guestAddress, qrCode, faculty = '') {
        // Determine footer color based on faculty
        const facultyUpper = faculty.toUpperCase();
        let footerBg = '#1e3a8a'; // Default blue
        let footerTextColor = '#ffffff';

        if (facultyUpper.includes('HUKUM')) {
            footerBg = '#dc2626'; // Red
            footerTextColor = '#ffffff';
        } else if (facultyUpper.includes('EKONOMI') || facultyUpper.includes('BISNIS')) {
            footerBg = '#f59e0b'; // Orange/Yellow
            footerTextColor = '#000000';
        } else if (facultyUpper.includes('KEDOKTERAN')) {
            footerBg = '#16a34a'; // Green
            footerTextColor = '#ffffff';
        } else if (facultyUpper.includes('KESEHATAN')) {
            footerBg = '#e5e7eb'; // Light gray
            footerTextColor = '#000000';
        } else if (facultyUpper.includes('TEKNIK')) {
            footerBg = '#1e3a8a'; // Blue
            footerTextColor = '#ffffff';
        }

        const content = `
            <div id="qrCardForPrint" class="bg-white shadow-xl overflow-hidden border border-gray-200" style="max-width: 600px; margin: 0 auto;">
                <!-- Header with Event Info (Always Blue) -->
                <div style="background-color: #1e3a8a; color: #ffffff; padding: 32px; text-align: center; border-radius: 24px; margin: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                    <h2 style="font-size: 18px; font-weight: bold; margin-bottom: 8px;">Selamat Datang di</h2>
                    <h3 style="font-size: 24px; font-weight: bold; line-height: 1.3;">Acara Wisuda-XXII, T/A: 2024/2025</h3>
                </div>

                <!-- Guest Info -->
                <div style="padding: 32px;">
                    <div style="text-align: center; margin-bottom: 32px;">
                        <p style="color: #6b7280; margin-bottom: 12px; font-size: 18px;">Salam, Dear</p>
                        <h4 style="font-size: 40px; font-weight: bold; color: #1f2937; margin-bottom: 24px; text-transform: uppercase; letter-spacing: 0.05em;">${guestName}</h4>
                        <p style="font-size: 16px; color: #4b5563; text-transform: uppercase; letter-spacing: 0.05em;">${guestAddress}</p>
                    </div>

                    <!-- QR Code -->
                    <div style="display: flex; justify-content: center; margin-bottom: 32px;">
                        <div id="qrcodeCanvas" style="padding: 20px; background-color: white; border: 4px solid #a78bfa; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);"></div>
                    </div>
                </div>

                <!-- Footer (Changes color based on faculty) -->
                <div style="background-color: ${footerBg}; color: ${footerTextColor}; padding: 24px; text-align: center;">
                    <p style="font-size: 20px; font-weight: bold; margin-bottom: 12px;">HARAP TUNJUKKAN QR CODE INI</p>
                    <p style="font-size: 14px; margin-bottom: 16px;">Sebagai akses masuk lokasi acara wisuda uniba</p>
                    <div style="font-size: 14px;">
                        <p style="margin-bottom: 4px;">Waktu: 07:00 - 16:15 WIB - Tanggal: ${EVENT_DATE}</p>
                        <p>Lokasi: Universitas Batam - Gedung: Graha Bintang</p>
                    </div>
                </div>
            </div>
        `;

        // CRITICAL FIX: Clear ALL previous content first
        const printContent = document.getElementById('qrPrintContent');
        printContent.innerHTML = '';  // Clear completely first

        // Then set new content
        printContent.innerHTML = content;

        // Wait for DOM to settle before generating QR
        setTimeout(() => {
            const qrContainer = document.getElementById('qrcodeCanvas');

            // Ensure container is completely empty
            if (qrContainer) {
                qrContainer.innerHTML = '';

                // Generate NEW QR Code
                new QRCode(qrContainer, {
                    text: qrCode,
                    width: 300,
                    height: 300,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
            }
        }, 100);
    }

    function printQRCode() {
        window.print();
    }

    function downloadQRCode() {
        // Get the canvas element from QR Code
        const canvas = document.querySelector('#qrcodeCanvas canvas');

        if (!canvas) {
            alert('QR Code belum di-generate. Silakan tunggu sebentar dan coba lagi.');
            return;
        }

        // Convert canvas to blob and download
        canvas.toBlob(function(blob) {
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            const guestName = currentQRData ? currentQRData.name : 'Guest';
            link.download = `QR-Code-${guestName.replace(/\s+/g, '-')}.png`;
            link.href = url;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);
        });
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
                csrfToken.value = CSRF_TOKEN;

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
</script>
@endpush
@endsection
