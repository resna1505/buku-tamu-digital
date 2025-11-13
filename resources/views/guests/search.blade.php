@extends('layouts.app')

@section('title', 'Cari Tamu')

@section('content')
<!-- Search Button Trigger -->
<div class="mb-6">
    <button id="openSearchModal" class="w-full bg-white rounded-2xl p-4 card-shadow flex items-center justify-between hover:bg-gray-50 transition">
        <div class="flex items-center space-x-3">
            <i class="fas fa-search text-purple-600 text-xl"></i>
            <span class="text-gray-600">Ketikan nama tamu</span>
        </div>
        <i class="fas fa-chevron-right text-gray-400"></i>
    </button>
</div>

<!-- Statistics Card -->
<div class="bg-white rounded-2xl p-6 card-shadow mb-6">
    <div class="grid grid-cols-2 gap-4">
        <div class="text-center">
            <p class="text-sm text-gray-600 mb-1">Belum Check-in</p>
            <p class="text-3xl font-bold text-purple-600">{{ $guests->total() }}</p>
        </div>
        <div class="text-center">
            <p class="text-sm text-gray-600 mb-1">Total Tamu</p>
            <p class="text-3xl font-bold text-gray-800">{{ \App\Models\Guest::where('event_id', $event->id)->count() }}</p>
        </div>
    </div>
</div>

<!-- Guest List - Only Not Checked In -->
<div class="bg-white rounded-2xl p-6 card-shadow">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold text-gray-800">Tamu Terdaftar</h3>
        <span class="text-sm text-gray-500">{{ $guests->total() }} tamu</span>
    </div>

    <!-- Search Info -->
    @if($searchTerm)
    <div class="mb-4 p-3 bg-purple-50 border border-purple-200 rounded-lg">
        <div class="flex items-center justify-between">
            <p class="text-sm text-purple-800">
                <i class="fas fa-search mr-2"></i>Hasil pencarian: <strong>"{{ $searchTerm }}"</strong>
            </p>
            <a href="{{ route('guests.search') }}" class="text-sm text-purple-600 hover:text-purple-800 font-semibold">
                <i class="fas fa-times mr-1"></i>Reset
            </a>
        </div>
    </div>
    @endif

    <div id="guestList" class="space-y-3">
        @forelse($guests as $index => $guest)
        <div class="guest-item p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition cursor-pointer"
             data-guest-id="{{ $guest->id }}"
             data-guest-name="{{ $guest->name }}"
             data-guest-address="{{ $guest->faculty }} | {{ $guest->study_program }}"
             data-guest-table="{{ $guest->table_number ?? '-' }}"
             data-guest-count="{{ $guest->guests_count }}"
             data-guest-vip="{{ $guest->is_vip ? '1' : '0' }}"
             data-guest-qr="{{ $guest->qr_code }}">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center space-x-2 mb-1">
                        <h4 class="font-semibold text-gray-800">{{ $guest->name }}</h4>
                        @if($guest->is_vip)
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">
                            VIP
                        </span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600">{{ $guest->faculty }} | {{ $guest->study_program }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-users mr-1"></i>{{ $guest->guests_count }} Orang
                        @if($guest->npm)
                        <span class="ml-2"><i class="fas fa-id-card mr-1"></i>{{ $guest->npm }}</span>
                        @endif
                    </p>
                </div>
                <i class="fas fa-chevron-right text-gray-400"></i>
            </div>
        </div>
        @empty
        <div class="text-center py-12 text-gray-500">
            @if($searchTerm)
                <i class="fas fa-search text-6xl mb-4 opacity-50"></i>
                <p class="text-lg">Tidak ada hasil untuk "{{ $searchTerm }}"</p>
                <p class="text-sm mt-2">Coba kata kunci lain</p>
                <a href="{{ route('guests.search') }}" class="mt-4 inline-block btn-primary text-white px-6 py-3 rounded-xl">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali ke Semua Tamu
                </a>
            @else
                <i class="fas fa-user-check text-6xl mb-4 opacity-50"></i>
                <p class="text-lg">Semua tamu sudah check-in</p>
                <p class="text-sm mt-2">Tidak ada tamu yang menunggu</p>
            @endif
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($guests->hasPages())
    <div class="mt-6">
        {{ $guests->appends(['q' => $searchTerm])->links() }}
    </div>
    @endif
</div>

<!-- Search Modal -->
<div id="searchModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-start justify-center pt-4">
    <div class="bg-white rounded-3xl w-full max-w-2xl mx-4 shadow-2xl animate-slide-down max-h-[90vh] flex flex-col">
        <!-- Modal Header -->
        <div class="gradient-bg text-white p-6 rounded-t-3xl flex items-center justify-between flex-shrink-0">
            <h3 class="text-xl font-bold">Cari Tamu Terdaftar</h3>
            <button id="closeSearchModal" class="text-white text-2xl hover:opacity-80">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Search Input -->
        <div class="p-6 border-b border-gray-200 flex-shrink-0">
            <form id="searchForm" method="GET" action="{{ route('guests.search') }}">
                <div class="relative">
                    <input
                        type="text"
                        id="modalSearchInput"
                        name="q"
                        class="w-full px-4 py-3 pl-12 border-2 border-purple-300 rounded-xl focus:outline-none focus:border-purple-500"
                        placeholder="Ketikan nama tamu, NPM, atau fakultas..."
                        value="{{ $searchTerm }}"
                        autofocus
                    >
                    <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <div id="searchLoading" class="hidden absolute right-4 top-1/2 transform -translate-y-1/2">
                        <i class="fas fa-spinner fa-spin text-purple-600"></i>
                    </div>
                </div>
            </form>
            <p class="text-sm text-gray-500 mt-2">
                <span id="searchInfoText">Tekan Enter untuk mencari di seluruh data</span>
            </p>
        </div>

        <!-- Quick Search Results (Live) -->
        <div id="quickResults" class="flex-1 overflow-y-auto p-6">
            <p class="text-center text-gray-500">Ketik nama tamu untuk preview hasil...</p>
        </div>

        <!-- Modal Footer -->
        <div class="p-6 border-t border-gray-200 flex-shrink-0">
            <div class="flex space-x-3">
                <button type="submit" form="searchForm" class="flex-1 bg-purple-600 text-white py-3 rounded-xl font-semibold hover:bg-purple-700 transition">
                    <i class="fas fa-search mr-2"></i>Cari
                </button>
                <button id="closeSearchModalBtn" class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-xl font-semibold hover:bg-gray-300 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Ticket Modal -->
<div id="ticketModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="relative w-full max-w-md">
        <!-- Close Button -->
        <button id="closeTicketModal" class="absolute -top-4 -right-4 w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-lg z-10 hover:bg-gray-100 transition">
            <i class="fas fa-times text-xl text-gray-700"></i>
        </button>

        <!-- Ticket Design -->
        <div class="bg-gradient-to-br from-orange-400 to-orange-500 rounded-3xl p-1 shadow-2xl">
            <div class="bg-white rounded-3xl overflow-hidden">
                <!-- Ticket Top Notches -->
                <div class="flex justify-between">
                    <div class="w-8 h-8 bg-gradient-to-br from-orange-400 to-orange-500 rounded-br-full"></div>
                    <div class="w-8 h-8 bg-gradient-to-br from-orange-400 to-orange-500 rounded-bl-full"></div>
                </div>

                <!-- Ticket Content -->
                <div class="gradient-bg text-white p-8 relative">
                    <!-- Event Info -->
                    <div class="text-center mb-8">
                        <p class="text-sm opacity-90 mb-2">{{ $event->type ?? 'WISUDA' }}</p>
                        <h2 class="text-3xl font-bold mb-2">{{ $event->name ?? 'Universitas Batam' }}</h2>
                        <p class="text-sm opacity-90">{{ $event->date ? $event->date->format('l, d F Y') : now()->format('l, d F Y') }}</p>
                    </div>

                    <!-- VIP Badge -->
                    <div id="ticketVipBadge" class="hidden bg-gradient-to-r from-orange-400 to-orange-500 text-white text-center py-3 font-bold text-xl tracking-wider mb-6">
                        VIP
                    </div>

                    <!-- Guest Info -->
                    <div class="text-center mb-6">
                        <p class="text-sm opacity-90 mb-2">SELAMAT DATANG</p>
                        <h3 id="ticketGuestName" class="text-2xl font-bold mb-4">-</h3>

                        <p class="text-sm opacity-90 mb-1">ALAMAT / KETERANGAN</p>
                        <p id="ticketGuestAddress" class="font-semibold mb-6">-</p>

                        <p class="text-sm opacity-90 mb-1">NO. MEJA</p>
                        <p id="ticketTableNumber" class="text-3xl font-bold mb-6">-</p>
                    </div>

                    <!-- Guest Count Display -->
                    <div class="text-center mb-6">
                        <p class="text-sm opacity-90 mb-2">JUMLAH TAMU</p>
                        <p id="ticketGuestCount" class="text-2xl font-bold">-</p>
                    </div>

                    <!-- Check In Button -->
                    <button id="checkInBtn" class="w-full bg-gradient-to-r from-orange-400 to-orange-500 text-white py-4 rounded-xl font-bold text-xl shadow-lg hover:shadow-xl transition transform hover:scale-105">
                        CHECK IN
                    </button>
                </div>

                <!-- Ticket Bottom Notches -->
                <div class="flex justify-between">
                    <div class="w-8 h-8 bg-gradient-to-br from-orange-400 to-orange-500 rounded-tr-full"></div>
                    <div class="w-8 h-8 bg-gradient-to-br from-orange-400 to-orange-500 rounded-tl-full"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    @keyframes slide-down {
        from {
            opacity: 0;
            transform: translateY(-50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-slide-down {
        animation: slide-down 0.3s ease;
    }
</style>
@endpush

@push('scripts')
<script>
    let selectedGuest = null;
    let searchTimeout = null;

    // Open Search Modal
    document.getElementById('openSearchModal').addEventListener('click', () => {
        document.getElementById('searchModal').classList.remove('hidden');
        document.getElementById('modalSearchInput').focus();
    });

    // Close Search Modal
    function closeSearchModal() {
        document.getElementById('searchModal').classList.add('hidden');
        document.getElementById('modalSearchInput').value = '';
        document.getElementById('quickResults').innerHTML = '<p class="text-center text-gray-500">Ketik nama tamu untuk preview hasil...</p>';
    }

    document.getElementById('closeSearchModal').addEventListener('click', closeSearchModal);
    document.getElementById('closeSearchModalBtn').addEventListener('click', closeSearchModal);

    // Live preview search (client-side for visible guests only)
    document.getElementById('modalSearchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const resultsDiv = document.getElementById('quickResults');

        // Clear previous timeout
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }

        if (searchTerm.length === 0) {
            resultsDiv.innerHTML = '<p class="text-center text-gray-500">Ketik nama tamu untuk preview hasil...</p>';
            document.getElementById('searchInfoText').textContent = 'Tekan Enter untuk mencari data';
            return;
        }

        if (searchTerm.length < 2) {
            resultsDiv.innerHTML = '<p class="text-center text-gray-500">Ketik minimal 2 karakter...</p>';
            return;
        }

        // Show loading
        document.getElementById('searchLoading').classList.remove('hidden');

        // Debounce the preview search
        searchTimeout = setTimeout(() => {
            // Filter guests from current page
            const guestItems = document.querySelectorAll('.guest-item');
            let foundGuests = [];

            guestItems.forEach(item => {
                const name = item.dataset.guestName.toLowerCase();
                if (name.includes(searchTerm)) {
                    foundGuests.push({
                        id: item.dataset.guestId,
                        name: item.dataset.guestName,
                        address: item.dataset.guestAddress,
                        table: item.dataset.guestTable,
                        count: item.dataset.guestCount,
                        vip: item.dataset.guestVip === '1',
                        qr: item.dataset.guestQr
                    });
                }
            });

            document.getElementById('searchLoading').classList.add('hidden');

            if (foundGuests.length === 0) {
                resultsDiv.innerHTML = '<p class="text-center text-gray-500">Preview: Tidak ada hasil di halaman ini. Tekan Enter untuk mencari di seluruh data.</p>';
                document.getElementById('searchInfoText').innerHTML = '<span class="text-purple-600 font-semibold">Tekan Enter untuk mencari di SEMUA data</span>';
                return;
            }

            // Display preview results
            let html = '<div class="space-y-3">';
            html += '<p class="text-sm text-gray-600 mb-3"><i class="fas fa-info-circle mr-1"></i>Preview dari halaman ini. Tekan Enter untuk mencari di seluruh data.</p>';

            foundGuests.slice(0, 5).forEach(guest => {
                html += `
                    <div class="search-result-item p-4 bg-gray-50 rounded-xl hover:bg-purple-50 transition cursor-pointer border-2 border-transparent hover:border-purple-300"
                         data-guest='${JSON.stringify(guest)}'>
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="flex items-center space-x-2 mb-1">
                                    <h4 class="font-semibold text-gray-800">${guest.name}</h4>
                                    ${guest.vip ? '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">VIP</span>' : ''}
                                </div>
                                <p class="text-sm text-gray-600">${guest.address}</p>
                            </div>
                            <i class="fas fa-ticket-alt text-purple-600 text-xl"></i>
                        </div>
                    </div>
                `;
            });

            if (foundGuests.length > 5) {
                html += `<p class="text-center text-sm text-gray-600 mt-3">Dan ${foundGuests.length - 5} lagi... Tekan Enter untuk melihat semua.</p>`;
            }

            html += '</div>';

            resultsDiv.innerHTML = html;
            document.getElementById('searchInfoText').innerHTML = '<span class="text-purple-600 font-semibold">Tekan Enter untuk mencari di SEMUA data</span>';

            // Add click handlers to preview results
            document.querySelectorAll('.search-result-item').forEach(item => {
                item.addEventListener('click', function() {
                    const guest = JSON.parse(this.dataset.guest);
                    showTicket(guest);
                    closeSearchModal();
                });
            });
        }, 300);
    });

    // Submit form on Enter
    document.getElementById('modalSearchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('searchForm').submit();
        }
    });

    // Click guest from list
    document.querySelectorAll('.guest-item').forEach(item => {
        item.addEventListener('click', function() {
            const guest = {
                id: this.dataset.guestId,
                name: this.dataset.guestName,
                address: this.dataset.guestAddress,
                table: this.dataset.guestTable,
                count: this.dataset.guestCount,
                vip: this.dataset.guestVip === '1',
                qr: this.dataset.guestQr
            };
            showTicket(guest);
        });
    });

    // Show Ticket Modal
    function showTicket(guest) {
        selectedGuest = guest;

        document.getElementById('ticketGuestName').textContent = guest.name.toUpperCase();
        document.getElementById('ticketGuestAddress').textContent = guest.address || '-';
        document.getElementById('ticketTableNumber').textContent = guest.table || '-';
        document.getElementById('ticketGuestCount').textContent = guest.count + ' ORANG';

        // Show/hide VIP badge
        if (guest.vip) {
            document.getElementById('ticketVipBadge').classList.remove('hidden');
        } else {
            document.getElementById('ticketVipBadge').classList.add('hidden');
        }

        document.getElementById('ticketModal').classList.remove('hidden');
    }

    // Close Ticket Modal
    document.getElementById('closeTicketModal').addEventListener('click', () => {
        document.getElementById('ticketModal').classList.add('hidden');
        selectedGuest = null;
    });

    // Check In Button
    document.getElementById('checkInBtn').addEventListener('click', async function() {
        if (!selectedGuest) return;

        const button = this;

        // Confirm check-in
        if (!confirm(`Check-in ${selectedGuest.name}?`)) {
            return;
        }

        // Disable button to prevent double click
        button.disabled = true;
        button.textContent = 'PROCESSING...';

        try {
            const response = await fetch('{{ route("checkin.scan") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    qr_code: selectedGuest.qr,
                    actual_guests_count: parseInt(selectedGuest.count)
                })
            });

            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Server tidak mengembalikan JSON. Kemungkinan ada error PHP.');
            }

            const result = await response.json();

            if (result.success) {
                window.location.href = result.redirect_url;
            } else {
                alert(result.message || 'Check-in gagal');
                button.disabled = false;
                button.textContent = 'CHECK IN';
            }
        } catch (error) {
            console.error('Check-in error:', error);
            alert('Terjadi kesalahan saat check-in: ' + error.message);

            // Re-enable button
            button.disabled = false;
            button.textContent = 'CHECK IN';
        }
    });
</script>
@endpush
@endsection
