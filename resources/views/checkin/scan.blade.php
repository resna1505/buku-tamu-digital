@extends('layouts.app')

@section('title', 'Check-In QR Scan')

@section('content')
<!-- Header -->
<div class="text-center mb-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-2">Scan QR Code</h2>
    <p class="text-gray-600">Arahkan kamera ke QR Code tamu</p>
</div>

<!-- Camera Container -->
<div class="bg-white rounded-3xl p-6 card-shadow mb-6">
    <div class="relative">
        <!-- Video Preview -->
        <div id="videoContainer" class="relative bg-black rounded-2xl overflow-hidden" style="aspect-ratio: 4/3;">
            <video id="qrVideo" class="w-full h-full object-cover"></video>

            <!-- Scanning Overlay -->
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-64 h-64 border-4 border-purple-500 rounded-2xl relative">
                    <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-white"></div>
                    <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-white"></div>
                    <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-white"></div>
                    <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-white"></div>

                    <!-- Scanning Line Animation -->
                    <div class="scan-line"></div>
                </div>
            </div>

            <!-- Status Message -->
            <div id="statusMessage" class="absolute bottom-4 left-4 right-4 bg-black bg-opacity-70 text-white text-center py-2 px-4 rounded-lg">
                Menunggu QR Code...
            </div>
        </div>
    </div>
</div>

<!-- Camera Controls -->
<div class="bg-white rounded-2xl p-6 card-shadow mb-6">
    <div class="flex justify-center space-x-4">
        <button id="switchCameraBtn" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-semibold hover:bg-gray-300 transition">
            <i class="fas fa-sync-alt mr-2"></i>Ganti Kamera
        </button>

        <button id="fullscreenBtn" class="bg-purple-500 text-white px-6 py-3 rounded-xl font-semibold hover:bg-purple-600 transition">
            <i class="fas fa-expand mr-2"></i>Fullscreen
        </button>

        <button id="stopCameraBtn" class="bg-red-500 text-white px-6 py-3 rounded-xl font-semibold hover:bg-red-600 transition">
            <i class="fas fa-stop mr-2"></i>Stop
        </button>
    </div>
</div>

<!-- Camera Selection -->
<div class="bg-white rounded-2xl p-6 card-shadow">
    <label class="block text-gray-700 font-semibold mb-3">
        <i class="fas fa-camera mr-2 text-purple-600"></i>Pilih Kamera
    </label>
    <select id="cameraSelect" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-purple-500">
        <option value="">Memuat kamera...</option>
    </select>
</div>

<!-- Manual Input (Alternative) -->
<div class="mt-6">
    <button id="manualInputBtn" class="w-full bg-gray-200 text-gray-700 py-4 rounded-xl font-semibold hover:bg-gray-300 transition">
        <i class="fas fa-keyboard mr-2"></i>Input Manual (Tanpa Scan)
    </button>
</div>

@push('styles')
<style>
    .scan-line {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent, #a855f7, transparent);
        animation: scanning 2s linear infinite;
    }

    @keyframes scanning {
        0% { top: 0; }
        50% { top: 100%; }
        100% { top: 0; }
    }

    #qrVideo {
        transform: scaleX(-1); /* Mirror effect for front camera */
    }
</style>
@endpush

@push('scripts')
<!-- QR Code Scanner Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>

<script>
    let html5QrCode;
    let currentCamera = 'environment'; // or 'user' for front camera
    let cameras = [];

    // Initialize QR Scanner
    async function initScanner() {
        try {
            html5QrCode = new Html5Qrcode("qrVideo");

            // Get available cameras
            const devices = await Html5Qrcode.getCameras();
            cameras = devices;

            // Populate camera select
            const cameraSelect = document.getElementById('cameraSelect');
            cameraSelect.innerHTML = '';

            devices.forEach((device, index) => {
                const option = document.createElement('option');
                option.value = device.id;
                option.text = device.label || `Camera ${index + 1}`;
                cameraSelect.appendChild(option);
            });

            // Start scanning with default camera
            if (devices.length > 0) {
                startScanning(devices[0].id);
            }
        } catch (err) {
            console.error('Error initializing scanner:', err);
            updateStatus('Error: Tidak dapat mengakses kamera');
        }
    }

    // Start scanning
    function startScanning(cameraId) {
        const config = {
            fps: 10,
            qrbox: { width: 250, height: 250 }
        };

        html5QrCode.start(
            cameraId,
            config,
            (decodedText, decodedResult) => {
                // QR Code detected
                onScanSuccess(decodedText);
            },
            (errorMessage) => {
                // Scanning error (can be ignored)
            }
        ).catch(err => {
            console.error('Error starting scanner:', err);
            updateStatus('Error: Gagal memulai scanner');
        });

        updateStatus('Menunggu QR Code...');
    }

    // Handle successful scan
    function onScanSuccess(qrCodeData) {
        updateStatus('QR Code terdeteksi!', 'success');

        // Stop scanner
        html5QrCode.stop();

        // Send check-in request
        checkInGuest(qrCodeData);
    }

    // Check-in guest
    async function checkInGuest(qrData) {
        try {
            const response = await fetch('{{ route("checkin.scan") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ qr_code: qrData })
            });

            const result = await response.json();

            if (result.success) {
                updateStatus('Check-in berhasil!', 'success');
                setTimeout(() => {
                    window.location.href = result.redirect_url;
                }, 1500);
            } else {
                updateStatus(result.message || 'Check-in gagal', 'error');
                setTimeout(() => {
                    initScanner(); // Restart scanner
                }, 2000);
            }
        } catch (error) {
            console.error('Check-in error:', error);
            updateStatus('Error: Gagal melakukan check-in', 'error');
            setTimeout(() => {
                initScanner(); // Restart scanner
            }, 2000);
        }
    }

    // Update status message
    function updateStatus(message, type = 'info') {
        const statusEl = document.getElementById('statusMessage');
        statusEl.textContent = message;

        statusEl.className = 'absolute bottom-4 left-4 right-4 text-center py-2 px-4 rounded-lg';

        if (type === 'success') {
            statusEl.className += ' bg-green-500 text-white';
        } else if (type === 'error') {
            statusEl.className += ' bg-red-500 text-white';
        } else {
            statusEl.className += ' bg-black bg-opacity-70 text-white';
        }
    }

    // Switch camera
    document.getElementById('switchCameraBtn').addEventListener('click', async () => {
        if (cameras.length > 1) {
            await html5QrCode.stop();
            const currentIndex = cameras.findIndex(cam => cam.id === document.getElementById('cameraSelect').value);
            const nextIndex = (currentIndex + 1) % cameras.length;
            document.getElementById('cameraSelect').value = cameras[nextIndex].id;
            startScanning(cameras[nextIndex].id);
        }
    });

    // Camera select change
    document.getElementById('cameraSelect').addEventListener('change', async (e) => {
        await html5QrCode.stop();
        startScanning(e.target.value);
    });

    // Fullscreen
    document.getElementById('fullscreenBtn').addEventListener('click', () => {
        const container = document.getElementById('videoContainer');
        if (container.requestFullscreen) {
            container.requestFullscreen();
        } else if (container.webkitRequestFullscreen) {
            container.webkitRequestFullscreen();
        }
    });

    // Stop camera
    document.getElementById('stopCameraBtn').addEventListener('click', async () => {
        await html5QrCode.stop();
        window.location.href = '{{ route("home") }}';
    });

    // Manual input
    document.getElementById('manualInputBtn').addEventListener('click', () => {
        const guestId = prompt('Masukkan ID Tamu:');
        if (guestId) {
            checkInGuest(guestId);
        }
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', () => {
        initScanner();
    });
</script>
@endpush
@endsection
