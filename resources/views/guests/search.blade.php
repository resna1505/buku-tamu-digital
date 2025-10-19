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

<!-- Guest List - Only Not Checked In -->
<div class="bg-white rounded-2xl p-6 card-shadow">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold text-gray-800">Tamu Terdaftar</h3>
        <span class="text-sm text-gray-500">{{ $guests->total() }} tamu</span>
    </div>

    <div id="guestList" class="space-y-3">
        @forelse($guests as $index => $guest)
        <div class="guest-item p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition cursor-pointer"
             data-guest-id="{{ $guest->id }}"
             data-guest-name="{{ $guest->name }}"
             data-guest-address="{{ $guest->address }}"
             data-guest-table="{{ $guest->table_number }}"
             data-guest-count="{{ $guest->guests_count }}"
             data-guest-vip="{{ $guest->is_vip }}"
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
                    <p class="text-sm text-gray-600">{{ $guest->address }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-users mr-1"></i>{{ $guest->guests_count }} Orang
                    </p>
                </div>
                <i class="fas fa-chevron-right text-gray-400"></i>
            </div>
        </div>
        @empty
        <div class="text-center py-12 text-gray-500">
            <i class="fas fa-user-slash text-6xl mb-4 opacity-50"></i>
            <p class="text-lg">Semua tamu sudah check-in</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($guests->hasPages())
    <div class="mt-6">
        {{ $guests->links() }}
    </div>
    @endif
</div>

<!-- Search Modal -->
<div id="searchModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-start justify-center pt-4">
    <div class="bg-white rounded-3xl w-full max-w-2xl mx-4 shadow-2xl animate-slide-down">
        <!-- Modal Header -->
        <div class="gradient-bg text-white p-6 rounded-t-3xl flex items-center justify-between">
            <h3 class="text-xl font-bold">Cari Tamu Terdaftar</h3>
            <button id="closeSearchModal" class="text-white text-2xl hover:opacity-80">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Search Input -->
        <div class="p-6 border-b border-gray-200">
            <div class="relative">
                <input
                    type="text"
                    id="modalSearchInput"
                    class="w-full px-4 py-3 pl-12 border-2 border-purple-300 rounded-xl focus:outline-none focus:border-purple-500"
                    placeholder="Ketikan nama tamu"
                    autofocus
                >
                <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
            <p class="text-sm text-gray-500 mt-2">Mencari data.....</p>
        </div>

        <!-- Search Results -->
        <div id="searchResults" class="p-6 max-h-96 overflow-y-auto">
            <p class="text-center text-gray-500">Ketik nama tamu untuk mencari...</p>
        </div>

        <!-- Modal Footer -->
        <div class="p-6 border-t border-gray-200">
            <button id="closeSearchModalBtn" class="w-full bg-gray-200 text-gray-700 py-3 rounded-xl font-semibold hover:bg-gray-300 transition">
                Tutup
            </button>
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
                        <p class="text-sm opacity-90 mb-2">{{ $event->type ?? 'DEMO EVENT' }}</p>
                        <h2 class="text-3xl font-bold mb-2">{{ $event->name ?? 'DEMO EVENT' }}</h2>
                        <p class="text-sm opacity-90">{{ $event->date ? $event->date->format('l, d F Y') : 'Kamis, 12 September 2024' }}</p>
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

                    <!-- Guest Count Selector -->
                    <div class="text-center mb-6">
                        <p class="text-sm opacity-90 mb-3">JUMLAH TAMU</p>
                        <select id="guestCountSelect" class="bg-white text-purple-900 px-6 py-3 rounded-lg font-bold text-lg w-48 mx-auto">
                            <option value="1">1 TAMU</option>
                            <option value="2">2 TAMU</option>
                            <option value="3">3 TAMU</option>
                            <option value="4">4 TAMU</option>
                            <option value="5">5 TAMU</option>
                        </select>
                    </div>

                    <!-- Check In Button -->
                    <button id="checkInBtn" class="w-full bg-gradient-to-r from-orange-400 to-orange-500 text-white py-4 rounded-xl font-bold text-xl shadow-lg hover:shadow-xl transition transform hover:scale-105">
                        CHECK IN
                    </button>

                    <!-- Powered By -->
                    <div class="text-center mt-6 opacity-75">
                        <p class="text-xs mb-2">POWERED BY :</p>
                        <div class="flex items-center justify-center space-x-2">
                            <i class="fas fa-qrcode text-lg"></i>
                            <span class="font-bold">TAMU KAMI</span>
                        </div>
                    </div>
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

    // Open Search Modal
    document.getElementById('openSearchModal').addEventListener('click', () => {
        document.getElementById('searchModal').classList.remove('hidden');
        document.getElementById('modalSearchInput').focus();
    });

    // Close Search Modal
    function closeSearchModal() {
        document.getElementById('searchModal').classList.add('hidden');
        document.getElementById('modalSearchInput').value = '';
        document.getElementById('searchResults').innerHTML = '<p class="text-center text-gray-500">Ketik nama tamu untuk mencari...</p>';
    }

    document.getElementById('closeSearchModal').addEventListener('click', closeSearchModal);
    document.getElementById('closeSearchModalBtn').addEventListener('click', closeSearchModal);

    // Search functionality
    document.getElementById('modalSearchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const resultsDiv = document.getElementById('searchResults');

        if (searchTerm.length === 0) {
            resultsDiv.innerHTML = '<p class="text-center text-gray-500">Ketik nama tamu untuk mencari...</p>';
            return;
        }

        // Filter guests from page
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

        if (foundGuests.length === 0) {
            resultsDiv.innerHTML = '<p class="text-center text-gray-500">Tidak ada hasil ditemukan</p>';
            return;
        }

        // Display results
        let html = '<div class="space-y-3">';
        foundGuests.forEach(guest => {
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
        html += '</div>';

        resultsDiv.innerHTML = html;

        // Add click handlers to results
        document.querySelectorAll('.search-result-item').forEach(item => {
            item.addEventListener('click', function() {
                const guest = JSON.parse(this.dataset.guest);
                showTicket(guest);
                closeSearchModal();
            });
        });
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
        document.getElementById('guestCountSelect').value = guest.count;

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

        const guestCount = document.getElementById('guestCountSelect').value;
        const button = this;

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
                    actual_guests_count: parseInt(guestCount)
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
            alert('Terjadi kesalahan saat check-in. Cek console untuk detail.');

            // Re-enable button
            button.disabled = false;
            button.textContent = 'CHECK IN';
        }
    });
</script>
@endpush
@endsection
