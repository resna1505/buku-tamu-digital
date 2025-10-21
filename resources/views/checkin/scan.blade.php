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
        <div id="reader" class="relative bg-black rounded-2xl overflow-hidden" style="min-height: 400px;">
            <!-- Scanner akan render di sini -->
        </div>

        <!-- Status Message -->
        <div id="statusMessage" class="mt-4 text-center py-2 px-4 rounded-lg bg-blue-100 text-blue-800">
            Memuat kamera...
        </div>
    </div>
</div>

<!-- Camera Controls -->
<div class="bg-white rounded-2xl p-6 card-shadow mb-6">
    <div class="flex justify-center space-x-4">
        <button id="stopCameraBtn" class="bg-red-500 text-white px-6 py-3 rounded-xl font-semibold hover:bg-red-600 transition">
            <i class="fas fa-stop mr-2"></i>Stop Kamera
        </button>

        <button id="restartBtn" class="bg-purple-500 text-white px-6 py-3 rounded-xl font-semibold hover:bg-purple-600 transition">
            <i class="fas fa-redo mr-2"></i>Restart
        </button>
    </div>
</div>

<!-- Manual Input (Alternative) -->
<div class="mt-6">
    <button id="manualInputBtn" class="w-full bg-gray-200 text-gray-700 py-4 rounded-xl font-semibold hover:bg-gray-300 transition">
        <i class="fas fa-keyboard mr-2"></i>Input Manual (Tanpa Scan)
    </button>
</div>

<!-- Debug Info -->
<div id="debugInfo" class="mt-6 bg-gray-100 rounded-2xl p-4 text-sm text-gray-600">
    <strong>Debug Info:</strong><br>
    <span id="debugText">Menginisialisasi...</span>
</div>

@push('styles')
<style>
    #reader {
        position: relative;
    }

    #reader video {
        width: 100% !important;
        height: auto !important;
        border-radius: 1rem;
    }

    #reader__scan_region {
        border-radius: 1rem !important;
    }

    /* Hide default controls yang tidak perlu */
    #reader__dashboard_section_csr {
        display: none !important;
    }
</style>
@endpush

@push('scripts')
<!-- QR Code Scanner Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>

<script>
    let html5QrCode = null;
    let isScanning = false;

    // Update status message
    function updateStatus(message, type = 'info') {
        const statusEl = document.getElementById('statusMessage');
        statusEl.textContent = message;

        statusEl.className = 'mt-4 text-center py-2 px-4 rounded-lg';

        if (type === 'success') {
            statusEl.className += ' bg-green-100 text-green-800';
        } else if (type === 'error') {
            statusEl.className += ' bg-red-100 text-red-800';
        } else if (type === 'warning') {
            statusEl.className += ' bg-yellow-100 text-yellow-800';
        } else {
            statusEl.className += ' bg-blue-100 text-blue-800';
        }
    }

    // Update debug info
    function updateDebug(message) {
        document.getElementById('debugText').innerHTML = message;
        console.log('DEBUG:', message);
    }

    // Initialize QR Scanner
    async function initScanner() {
        try {
            updateStatus('Meminta izin kamera...', 'info');
            updateDebug('Checking HTTPS...<br>Protocol: ' + location.protocol);

            // Check HTTPS
            if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                updateStatus('⚠️ HTTPS diperlukan untuk akses kamera', 'error');
                updateDebug('ERROR: Not using HTTPS<br>Current URL: ' + location.href);
                return;
            }

            updateDebug('Getting cameras...');

            // Get available cameras
            const devices = await Html5Qrcode.getCameras();

            updateDebug(`Found ${devices.length} camera(s):<br>` +
                devices.map((d, i) => `${i + 1}. ${d.label || 'Camera ' + (i + 1)}`).join('<br>'));

            if (devices.length === 0) {
                updateStatus('⚠️ Tidak ada kamera ditemukan', 'error');
                updateDebug('No cameras detected. Check:<br>1. Camera permission granted?<br>2. Camera not used by other app?<br>3. Browser supports camera?');
                return;
            }

            // Initialize scanner
            html5QrCode = new Html5Qrcode("reader");

            updateDebug('Starting camera...');

            // Prefer back camera (environment)
            let cameraId = devices[0].id;
            const backCamera = devices.find(device =>
                device.label.toLowerCase().includes('back') ||
                device.label.toLowerCase().includes('rear') ||
                device.label.toLowerCase().includes('environment')
            );

            if (backCamera) {
                cameraId = backCamera.id;
                updateDebug(`Using back camera: ${backCamera.label}`);
            } else {
                updateDebug(`Using first camera: ${devices[0].label || 'Unknown'}`);
            }

            // Start scanning
            await html5QrCode.start(
                cameraId,
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0
                },
                (decodedText, decodedResult) => {
                    // QR Code detected
                    onScanSuccess(decodedText);
                },
                (errorMessage) => {
                    // Scanning errors (can be ignored, happens frequently)
                }
            );

            isScanning = true;
            updateStatus('✓ Kamera aktif - Arahkan ke QR Code', 'success');
            updateDebug('Camera started successfully!<br>Waiting for QR code...');

        } catch (err) {
            console.error('Error initializing scanner:', err);

            // Provide specific error messages
            let errorMsg = 'Error: Tidak dapat mengakses kamera';
            let debugMsg = 'Error: ' + err.message;

            if (err.name === 'NotAllowedError') {
                errorMsg = '⚠️ Izin kamera ditolak. Klik ikon kamera di address bar browser untuk mengizinkan.';
                debugMsg = 'NotAllowedError: Camera permission denied by user';
            } else if (err.name === 'NotFoundError') {
                errorMsg = '⚠️ Kamera tidak ditemukan di perangkat ini';
                debugMsg = 'NotFoundError: No camera device found';
            } else if (err.name === 'NotReadableError') {
                errorMsg = '⚠️ Kamera sedang digunakan aplikasi lain. Tutup aplikasi lain yang menggunakan kamera.';
                debugMsg = 'NotReadableError: Camera in use by another application';
            } else if (err.name === 'OverconstrainedError') {
                errorMsg = '⚠️ Kamera tidak mendukung pengaturan yang diminta';
                debugMsg = 'OverconstrainedError: Camera constraints not satisfied';
            } else if (location.protocol !== 'https:') {
                errorMsg = '⚠️ Gunakan HTTPS untuk akses kamera (bukan HTTP)';
                debugMsg = 'SecurityError: HTTPS required for camera access';
            }

            updateStatus(errorMsg, 'error');
            updateDebug(debugMsg + '<br><br>Full error:<br>' + err.toString());
        }
    }

    // Handle successful scan
    function onScanSuccess(qrCodeData) {
        if (!isScanning) return; // Prevent multiple scans

        isScanning = false;
        updateStatus('✓ QR Code terdeteksi!', 'success');
        updateDebug('QR Code detected: ' + qrCodeData);

        // Stop scanner
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                updateDebug('Camera stopped. Processing check-in...');
            }).catch(err => {
                console.error('Error stopping scanner:', err);
            });
        }

        // Send check-in request
        checkInGuest(qrCodeData);
    }

    // Check-in guest
    async function checkInGuest(qrData) {
        try {
            updateStatus('Memproses check-in...', 'info');
            updateDebug('Sending check-in request...');

            const response = await fetch('{{ route("checkin.scan") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ qr_code: qrData })
            });

            const result = await response.json();
            updateDebug('Server response: ' + JSON.stringify(result));

            if (result.success) {
                updateStatus('✓ Check-in berhasil! Mengalihkan...', 'success');
                setTimeout(() => {
                    window.location.href = result.redirect_url;
                }, 1500);
            } else {
                updateStatus('✗ ' + (result.message || 'Check-in gagal'), 'error');
                setTimeout(() => {
                    isScanning = true;
                    initScanner(); // Restart scanner
                }, 2000);
            }
        } catch (error) {
            console.error('Check-in error:', error);
            updateStatus('✗ Error: Gagal melakukan check-in', 'error');
            updateDebug('Check-in error: ' + error.toString());
            setTimeout(() => {
                isScanning = true;
                initScanner(); // Restart scanner
            }, 2000);
        }
    }

    // Stop camera button
    document.getElementById('stopCameraBtn').addEventListener('click', async () => {
        if (html5QrCode && isScanning) {
            await html5QrCode.stop();
            isScanning = false;
            updateStatus('Kamera dihentikan', 'warning');
            updateDebug('Camera stopped by user');
        }
    });

    // Restart button
    document.getElementById('restartBtn').addEventListener('click', async () => {
        if (html5QrCode && isScanning) {
            await html5QrCode.stop();
        }
        isScanning = false;
        updateDebug('Restarting scanner...');
        setTimeout(() => {
            initScanner();
        }, 500);
    });

    // Manual input
    document.getElementById('manualInputBtn').addEventListener('click', () => {
        const guestId = prompt('Masukkan ID/Kode Tamu:');
        if (guestId) {
            if (html5QrCode && isScanning) {
                html5QrCode.stop();
                isScanning = false;
            }
            checkInGuest(guestId);
        }
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', () => {
        updateDebug('Page loaded. Initializing scanner...');

        // Delay untuk memastikan DOM ready
        setTimeout(() => {
            initScanner();
        }, 500);
    });

    // Handle page visibility (pause when hidden)
    document.addEventListener('visibilitychange', () => {
        if (document.hidden && html5QrCode && isScanning) {
            html5QrCode.pause();
            updateDebug('Camera paused (page hidden)');
        } else if (!document.hidden && html5QrCode && isScanning) {
            html5QrCode.resume();
            updateDebug('Camera resumed (page visible)');
        }
    });
</script>
@endpush
@endsection
