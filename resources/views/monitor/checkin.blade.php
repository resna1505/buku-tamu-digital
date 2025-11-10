@extends('layouts.app')

@section('title', 'Monitor Check-In')

@section('content')
<!-- Header -->
<div class="bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-3xl p-6 mb-6 card-shadow">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold mb-2">
                <i class="fas fa-tv mr-2"></i>Monitor Check-In
            </h2>
            <p class="text-sm opacity-90">Real-time Guest Check-In Display</p>
        </div>
        <div class="text-right">
            <div class="text-3xl font-bold" id="currentTime">{{ now()->format('H:i') }}</div>
            <div class="text-sm opacity-90">{{ now()->format('d F Y') }}</div>
        </div>
    </div>
</div>

<!-- Statistics Bar -->
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl p-4 card-shadow text-center">
        <div class="text-2xl font-bold text-green-600" id="totalCheckIn">{{ $recentCheckIns->count() }}</div>
        <div class="text-xs text-gray-600">Check-In Hari Ini</div>
    </div>

    <div class="bg-white rounded-2xl p-4 card-shadow text-center">
        <div class="text-2xl font-bold text-blue-600" id="checkInCount1">-</div>
        <div class="text-xs text-gray-600">Check-In Pertama</div>
    </div>

    <div class="bg-white rounded-2xl p-4 card-shadow text-center">
        <div class="text-2xl font-bold text-purple-600" id="checkInCount2">-</div>
        <div class="text-xs text-gray-600">Check-In Kedua</div>
    </div>

    <div class="bg-white rounded-2xl p-4 card-shadow text-center">
        <div class="w-3 h-3 bg-green-500 rounded-full inline-block animate-pulse"></div>
        <div class="text-xs text-gray-600 mt-1">Live</div>
    </div>
</div>

<!-- Latest Check-Ins Display -->
<div class="bg-white rounded-3xl p-6 card-shadow">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-bold text-gray-800">
            <i class="fas fa-list mr-2 text-purple-600"></i>
            Check-In Terbaru
        </h3>
        <div class="text-sm text-gray-500">
            <i class="fas fa-sync-alt mr-1"></i>
            <span id="lastUpdate">Baru saja</span>
        </div>
    </div>

    <!-- Check-In List -->
    <div id="checkInList" class="space-y-4">
        @forelse($recentCheckIns as $attendance)
        <div class="check-in-item bg-gradient-to-r from-green-50 to-blue-50 rounded-2xl p-5 border-l-4 border-green-500 hover:shadow-lg transition-all" data-id="{{ $attendance->id }}">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4 flex-1">
                    <!-- Avatar -->
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-user text-white text-2xl"></i>
                    </div>

                    <!-- Guest Info -->
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <h4 class="text-xl font-bold text-gray-800">{{ $attendance->guest->name }}</h4>
                            @if($attendance->guest->is_vip)
                            <span class="px-3 py-1 bg-yellow-400 text-yellow-900 text-xs font-bold rounded-full">
                                <i class="fas fa-crown mr-1"></i>VIP
                            </span>
                            @endif
                            <span class="px-3 py-1 bg-blue-500 text-white text-xs font-bold rounded-full">
                                Check-In {{ $attendance->check_in_number ?? 1 }}/2
                            </span>
                        </div>

                        <div class="text-sm text-gray-600">
                            @if($attendance->guest->faculty || $attendance->guest->study_program)
                            <div class="mb-1">
                                <i class="fas fa-university mr-1 text-purple-500"></i>
                                <span class="font-medium">
                                    {{ $attendance->guest->faculty }}{{ $attendance->guest->study_program ? ', ' . $attendance->guest->study_program : '' }}
                                </span>
                            </div>
                            @endif

                            @if($attendance->guest->address)
                            <div>
                                <i class="fas fa-map-marker-alt mr-1 text-blue-500"></i>
                                <span>{{ $attendance->guest->address }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Time -->
                    <div class="text-right flex-shrink-0">
                        <div class="bg-green-500 text-white px-4 py-2 rounded-xl">
                            <div class="text-2xl font-bold">{{ $attendance->checked_in_at->format('H:i') }}</div>
                            <div class="text-xs opacity-90">WIB</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-12 text-gray-400">
            <i class="fas fa-inbox text-6xl mb-4"></i>
            <p class="text-lg">Belum ada check-in hari ini</p>
        </div>
        @endforelse
    </div>
</div>

<audio id="checkInSound" preload="auto">
    <source src="{{ asset('sounds/success.mp3') }}" type="audio/mpeg">
</audio>

@push('styles')
<style>
    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .check-in-item-new {
        animation: slideInDown 0.5s ease-out;
    }

    @keyframes highlight {
        0%, 100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        50% { box-shadow: 0 0 20px 5px rgba(34, 197, 94, 0.5); }
    }

    .check-in-item-highlight {
        animation: highlight 1s ease-in-out 2;
    }
</style>
@endpush

@push('scripts')
<script>
    let lastCheckInId = {{ $recentCheckIns->first()->id ?? 0 }};

    function updateClock() {
        const now = new Date();
        document.getElementById('currentTime').textContent =
            now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    }

    function playSound() {
        document.getElementById('checkInSound')?.play().catch(e => console.log(e));
    }

    function createCheckInHTML(checkIn) {
        const vipBadge = checkIn.is_vip ?
            '<span class="px-3 py-1 bg-yellow-400 text-yellow-900 text-xs font-bold rounded-full"><i class="fas fa-crown mr-1"></i>VIP</span>' : '';

        const facultyInfo = (checkIn.guest_faculty || checkIn.guest_study_program) ?
            `<div class="mb-1">
                <i class="fas fa-university mr-1 text-purple-500"></i>
                <span class="font-medium">${checkIn.guest_faculty || ''}${checkIn.guest_study_program ? ', ' + checkIn.guest_study_program : ''}</span>
            </div>` : '';

        const addressInfo = checkIn.guest_address ?
            `<div>
                <i class="fas fa-map-marker-alt mr-1 text-blue-500"></i>
                <span>${checkIn.guest_address}</span>
            </div>` : '';

        return `
        <div class="check-in-item check-in-item-new check-in-item-highlight bg-gradient-to-r from-green-50 to-blue-50 rounded-2xl p-5 border-l-4 border-green-500 hover:shadow-lg transition-all" data-id="${checkIn.id}">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4 flex-1">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-user text-white text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <h4 class="text-xl font-bold text-gray-800">${checkIn.guest_name}</h4>
                            ${vipBadge}
                            <span class="px-3 py-1 bg-blue-500 text-white text-xs font-bold rounded-full">Check-In ${checkIn.check_in_number}/2</span>
                        </div>
                        <div class="text-sm text-gray-600">
                            ${facultyInfo}
                            ${addressInfo}
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <div class="bg-green-500 text-white px-4 py-2 rounded-xl">
                            <div class="text-2xl font-bold">${checkIn.checked_in_at}</div>
                            <div class="text-xs opacity-90">WIB</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
    }

    async function fetchLatest() {
        try {
            const response = await fetch(`{{ route('monitor.latest') }}?last_id=${lastCheckInId}`);
            const data = await response.json();

            if (data.success && data.checkIns.length > 0) {
                playSound();

                const list = document.getElementById('checkInList');

                data.checkIns.reverse().forEach(checkIn => {
                    const div = document.createElement('div');
                    div.innerHTML = createCheckInHTML(checkIn);
                    list.insertBefore(div.firstElementChild, list.firstChild);

                    if (checkIn.id > lastCheckInId) lastCheckInId = checkIn.id;
                });

                while (list.children.length > 10) {
                    list.removeChild(list.lastChild);
                }

                document.getElementById('lastUpdate').textContent = 'Baru saja';
            }
        } catch (error) {
            console.error('Error fetching:', error);
        }
    }

    setInterval(updateClock, 1000);
    setInterval(fetchLatest, 3000);
    updateClock();
</script>
@endpush
@endsection
