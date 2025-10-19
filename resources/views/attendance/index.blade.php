@extends('layouts.app')

@section('title', 'Kehadiran')

@section('content')
<!-- Statistics Cards -->
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-2xl p-4 card-shadow text-center">
        <div class="text-3xl font-bold text-purple-600 mb-1">{{ $stats['invited'] ?? 0 }}</div>
        <div class="text-xs text-gray-600">Undangan</div>
    </div>

    <div class="bg-white rounded-2xl p-4 card-shadow text-center">
        <div class="text-3xl font-bold text-green-600 mb-1">{{ $stats['attended'] ?? 0 }}</div>
        <div class="text-xs text-gray-600">Hadir</div>
    </div>

    <div class="bg-white rounded-2xl p-4 card-shadow text-center">
        <div class="text-3xl font-bold text-blue-600 mb-1">{{ $stats['total'] ?? 0 }}</div>
        <div class="text-xs text-gray-600">Total Tamu</div>
    </div>
</div>

<!-- Filter Tabs -->
<div class="bg-white rounded-2xl p-4 card-shadow mb-6">
    <div class="flex space-x-2">
        <button class="filter-tab active flex-1 px-4 py-3 rounded-xl text-sm font-medium">
            Tamu Hadir
        </button>
        <button class="filter-tab flex-1 px-4 py-3 rounded-xl text-sm font-medium">
            Tidak Hadir
        </button>
    </div>
</div>

<!-- Export Options -->
<div class="bg-white rounded-2xl p-4 card-shadow mb-6">
    <div class="flex space-x-2">
        <button class="export-btn bg-red-500 text-white flex-1 px-4 py-3 rounded-xl text-sm flex items-center justify-center hover:bg-red-600 transition">
            <i class="fas fa-file-pdf mr-2"></i>PDF
        </button>
        <button class="export-btn bg-green-500 text-white flex-1 px-4 py-3 rounded-xl text-sm flex items-center justify-center hover:bg-green-600 transition">
            <i class="fas fa-file-excel mr-2"></i>Excel
        </button>
        <button class="export-btn gradient-bg text-white flex-1 px-4 py-3 rounded-xl text-sm flex items-center justify-center hover:opacity-90 transition">
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

<!-- Attendance List -->
<div class="bg-white rounded-2xl p-6 card-shadow">
    <div id="attendanceList" class="space-y-3">
        @forelse($attendances ?? [] as $index => $attendance)
        <div class="attendance-item p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center space-x-2 mb-1">
                        <span class="text-gray-500 font-semibold">{{ $index + 1 }}.</span>
                        <h4 class="font-semibold text-gray-800">{{ $attendance->guest->name }}</h4>
                        @if($attendance->guest->is_vip)
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">
                            VIP
                        </span>
                        @endif
                    </div>

                    <p class="text-sm text-gray-600 ml-6">
                        {{ $attendance->guest->address }} | {{ $attendance->guest->guests_count }} Orang
                    </p>

                    <div class="flex items-center space-x-4 ml-6 mt-2 text-xs text-gray-500">
                        <span>
                            <i class="fas fa-clock mr-1 text-green-600"></i>
                            {{ $attendance->checked_in_at->format('d/m/Y H:i') }}
                        </span>
                        @if($attendance->table_number)
                        <span>
                            <i class="fas fa-chair mr-1 text-purple-600"></i>
                            Meja {{ $attendance->table_number }}
                        </span>
                        @endif
                    </div>
                </div>

                <div class="ml-4">
                    <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                        <i class="fas fa-check mr-1"></i>Hadir
                    </span>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-12 text-gray-500">
            <i class="fas fa-clipboard-check text-6xl mb-4 opacity-50"></i>
            <p class="text-lg">Belum ada tamu yang hadir</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if(isset($attendances) && $attendances->hasPages())
    <div class="flex justify-center mt-6">
        {{ $attendances->links() }}
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
</style>
@endpush

@push('scripts')
<script>
    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const attendanceItems = document.querySelectorAll('.attendance-item');

        attendanceItems.forEach(item => {
            const name = item.querySelector('h4').textContent.toLowerCase();
            if (name.includes(searchTerm)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // Filter tabs
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            // Add your filter logic here
            const filterType = this.textContent.trim();
            console.log('Filter:', filterType);
        });
    });

    // Export buttons
    document.querySelectorAll('.export-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const type = this.querySelector('i').classList.contains('fa-file-pdf') ? 'pdf' :
                        this.querySelector('i').classList.contains('fa-file-excel') ? 'excel' : 'all';
            console.log('Export:', type);
            // Add your export logic here
        });
    });
</script>
@endpush
@endsection
