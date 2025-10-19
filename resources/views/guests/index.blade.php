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
    <div class="flex space-x-2 mb-4">
        <button class="filter-tab active px-4 py-2 rounded-lg text-sm font-medium">
            Tamu Hadir
        </button>
        <button class="filter-tab px-4 py-2 rounded-lg text-sm font-medium">
            Tidak Hadir
        </button>
    </div>

    <!-- Export Options -->
    <div class="flex space-x-2">
        <button class="export-btn bg-red-500 text-white px-4 py-2 rounded-lg text-sm flex items-center hover:bg-red-600 transition">
            <i class="fas fa-file-pdf mr-2"></i>PDF
        </button>
        <button class="export-btn bg-green-500 text-white px-4 py-2 rounded-lg text-sm flex items-center hover:bg-green-600 transition">
            <i class="fas fa-file-excel mr-2"></i>Excel
        </button>
        <button class="export-btn gradient-bg text-white px-4 py-2 rounded-lg text-sm flex items-center hover:opacity-90 transition">
            <i class="fas fa-users mr-2"></i>All Group
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
        <div class="guest-item flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition">
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

                <!-- Dropdown Menu (Hidden by default) -->
                <div class="action-menu hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg z-10 border border-gray-200">
                    <a href="#" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 rounded-t-xl">
                        <i class="fas fa-whatsapp text-green-600 mr-2"></i>Kirim WhatsApp
                    </a>
                    <a href="#" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-qrcode text-purple-600 mr-2"></i>Cetak QR Code
                    </a>
                    <a href="#" class="block px-4 py-3 text-sm text-red-600 hover:bg-red-50 rounded-b-xl">
                        <i class="fas fa-trash mr-2"></i>Hapus Tamu
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-12 text-gray-500">
            <i class="fas fa-users text-6xl mb-4 opacity-50"></i>
            <p class="text-lg">Belum ada data tamu</p>
            <button class="mt-4 btn-primary text-white px-6 py-3 rounded-xl">
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
</style>
@endpush

@push('scripts')
<script>
    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const guestItems = document.querySelectorAll('.guest-item');

        guestItems.forEach(item => {
            const name = item.querySelector('h4').textContent.toLowerCase();
            if (name.includes(searchTerm)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // Action menu toggle
    document.querySelectorAll('.action-menu-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const menu = this.nextElementSibling;

            // Close all other menus
            document.querySelectorAll('.action-menu').forEach(m => {
                if (m !== menu) m.classList.add('hidden');
            });

            menu.classList.toggle('hidden');
        });
    });

    // Close menu when clicking outside
    document.addEventListener('click', function() {
        document.querySelectorAll('.action-menu').forEach(menu => {
            menu.classList.add('hidden');
        });
    });

    // Filter tabs
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Add guest button
    document.getElementById('addGuestBtn').addEventListener('click', function() {
        window.location.href = '{{ route("guests.create") }}';
    });
</script>
@endpush
@endsection
