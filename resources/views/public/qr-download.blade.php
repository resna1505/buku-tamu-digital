<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code - {{ $guest->name }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        @php
            // Logic warna berdasarkan fakultas (hanya untuk footer)
            $faculty = strtoupper($guest->faculty ?? '');
            $footerBg = '#1e3a8a'; // Default biru tua
            $footerTextColor = '#ffffff'; // Default putih

            if (strpos($faculty, 'HUKUM') !== false) {
                $footerBg = '#dc2626'; // Merah
                $footerTextColor = '#ffffff';
            } elseif (strpos($faculty, 'EKONOMI') !== false || strpos($faculty, 'BISNIS') !== false) {
                $footerBg = '#f59e0b'; // Kuning/Orange
                $footerTextColor = '#000000';
            } elseif (strpos($faculty, 'KEDOKTERAN') !== false) {
                $footerBg = '#16a34a'; // Hijau
                $footerTextColor = '#ffffff';
            } elseif (strpos($faculty, 'ILMU KESEHATAN') !== false) {
                $footerBg = '#e5e7eb'; // Abu-abu muda (putih)
                $footerTextColor = '#000000';
            } elseif (strpos($faculty, 'TEKNIK') !== false) {
                $footerBg = '#1e3a8a'; // Biru tua
                $footerTextColor = '#ffffff';
            }
        @endphp

        .header-bg {
            background-color: #1e3a8a; /* Selalu biru tua */
        }

        .footer-bg {
            background-color: {{ $footerBg }};
        }

        .header-text {
            color: #ffffff; /* Selalu putih */
        }

        .footer-text {
            color: {{ $footerTextColor }};
        }

        .qr-border {
            border: 4px solid #a78bfa;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        #barcodeCanvas {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 10px;
            background: white;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }

            body {
                margin: 0;
                padding: 0;
                background: white !important;
            }

            /* Hide everything first */
            body * {
                visibility: hidden !important;
            }

            /* Show only QR card */
            #qrCardPrint,
            #qrCardPrint * {
                visibility: visible !important;
            }

            /* Center card on page with precise positioning */
            #qrCardPrint {
                position: fixed !important;
                left: 50% !important;
                top: 50% !important;
                transform: translate(-50%, -50%) !important;
                width: 180mm !important;
                max-width: 180mm !important;
                box-shadow: none !important;
                page-break-inside: avoid !important;
                margin: 0 !important;
            }

            /* Optimize font sizes for print */
            #qrCardPrint .text-5xl {
                font-size: 2.25rem !important;
            }

            #qrCardPrint .text-2xl {
                font-size: 1.5rem !important;
            }

            #qrCardPrint .text-xl {
                font-size: 1.125rem !important;
            }

            #qrCardPrint .text-lg {
                font-size: 1rem !important;
            }

            #qrCardPrint .text-base {
                font-size: 0.875rem !important;
            }

            #qrCardPrint .text-sm {
                font-size: 0.75rem !important;
            }

            /* Optimize padding for print */
            #qrCardPrint .p-8 {
                padding: 1.5rem !important;
            }

            #qrCardPrint .p-6 {
                padding: 1.25rem !important;
            }

            #qrCardPrint .mb-8 {
                margin-bottom: 1.25rem !important;
            }

            #qrCardPrint .mb-6 {
                margin-bottom: 1rem !important;
            }

            /* Logo sizing */
            #qrCardPrint .h-20 {
                height: 4rem !important;
            }

            /* QR Code container */
            #qrcodeCanvas {
                padding: 12px !important;
            }

            #qrcodeCanvas canvas {
                width: 220px !important;
                height: 220px !important;
            }

            /* Barcode sizing */
            #barcodeCanvas {
                max-width: 100% !important;
            }

            #barcodeCanvas svg {
                max-width: 350px !important;
                height: auto !important;
            }

            /* Hide print buttons */
            .no-print {
                display: none !important;
                visibility: hidden !important;
            }

            /* Ensure colors print correctly */
            .header-bg,
            .footer-bg {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-2xl w-full">
            <!-- QR Card -->
            <div id="qrCardPrint" class="bg-white shadow-xl overflow-hidden border border-gray-200">
                <!-- Logo Header -->
                <div class="bg-white p-6 border-b border-gray-200">
                    <img src="{{ asset('images/logo-blue.png') }}" alt="UNIBA Logo" class="h-20 mx-auto object-contain">
                </div>

                <!-- Header with Event Info (Selalu Biru) -->
                <div class="header-bg header-text p-8 text-center rounded-3xl mx-6 mt-6 shadow-md">
                    <h2 class="text-lg font-bold mb-2">Selamat Datang di</h2>
                    <h3 class="text-2xl font-bold leading-tight">Acara Wisuda-XXII, T/A: 2024/2025</h3>
                </div>

                <!-- Guest Info -->
                <div class="p-8">
                    <div class="text-center mb-8">
                        <p class="text-gray-500 mb-3 text-lg">Salam, Dear</p>
                        <h4 class="text-5xl font-bold text-gray-800 mb-6 uppercase tracking-wide">{{ $guest->name }}</h4>
                        <p class="text-base text-gray-600 uppercase tracking-wide">
                            {{ $guest->faculty ? $guest->faculty . ', ' : '' }}{{ $guest->study_program ?? '' }}
                        </p>
                    </div>

                    <!-- QR Code -->
                    <div class="flex justify-center mb-6">
                        <div id="qrcodeCanvas" class="p-5 bg-white qr-border rounded-2xl"></div>
                    </div>

                    <!-- Barcode Scanner Support -->
                    <div class="mb-6">
                        <div class="flex justify-center mb-3">
                            <svg id="barcodeCanvas"></svg>
                        </div>
                        <p class="text-center text-sm text-gray-600 font-mono font-bold">{{ $guest->qr_code }}</p>
                    </div>
                </div>

                <!-- Footer (Berubah Warna Sesuai Fakultas) -->
                <div class="footer-bg footer-text p-6 text-center">
                    <p class="text-xl font-bold mb-3">HARAP TUNJUKKAN QR CODE INI</p>
                    <p class="text-sm mb-4">Sebagai akses masuk lokasi acara {{ strtolower($event->name ?? 'wisuda uniba') }}</p>

                    <div class="text-sm space-y-1">
                        @if($event)
                        <p>Waktu: {{ $event->start_time ?? '07:00' }} - {{ $event->end_time ?? '16:15' }} WIB - Tanggal: {{ $event->date ? $event->date->format('d F Y') : '29 November 2025' }}</p>
                        <p>Lokasi: {{ $event->location ?? 'Universitas Batam - Gedung: Graha Bintang' }}</p>
                        @else
                        <p>Waktu: 07:00 - 16:15 WIB - Tanggal: 29 November 2025</p>
                        <p>Lokasi: Universitas Batam - Gedung: Graha Bintang</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-3 no-print mb-4 mt-6">
                <button onclick="window.print()" class="flex-1 bg-purple-600 text-white py-4 px-6 rounded-lg font-semibold hover:bg-purple-700 transition shadow-md">
                    <i class="fas fa-print mr-2"></i>Print QR Code
                </button>
                <button onclick="downloadQRCode()" class="flex-1 bg-green-600 text-white py-4 px-6 rounded-lg font-semibold hover:bg-green-700 transition shadow-md">
                    <i class="fas fa-download mr-2"></i>Download QR
                </button>
                <button onclick="shareWhatsApp()" class="flex-1 bg-blue-600 text-white py-4 px-6 rounded-lg font-semibold hover:bg-blue-700 transition shadow-md">
                    <i class="fab fa-whatsapp mr-2"></i>Share
                </button>
            </div>

            <!-- Info Note -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 no-print mb-4">
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-qrcode text-3xl text-purple-600"></i>
                        <div>
                            <p class="font-bold text-gray-800 mb-1">QR Code</p>
                            <p class="text-sm text-gray-600">Scan dengan kamera HP</p>
                        </div>
                    </div>
                </div>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-barcode text-3xl text-green-600"></i>
                        <div>
                            <p class="font-bold text-gray-800 mb-1">Barcode</p>
                            <p class="text-sm text-gray-600">Scan dengan barcode scanner</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg p-4 text-center no-print shadow-sm border border-gray-200">
                <p class="text-base font-bold text-gray-800 mb-1">QR CODE INI BERLAKU UNTUK 2 (DUA) TAMU</p>
                <p class="text-sm text-gray-500">Simpan atau screenshot QR Code ini untuk akses masuk acara</p>
            </div>
        </div>
    </div>

    <!-- QR Code Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <!-- JsBarcode Library -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

    <script>
        const qrCodeText = '{{ $guest->qr_code }}';
        const guestName = '{{ $guest->name }}';

        // Generate QR Code
        new QRCode(document.getElementById('qrcodeCanvas'), {
            text: qrCodeText,
            width: 300,
            height: 300,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });

        // Generate Barcode with error handling
        function generateBarcode() {
            try {
                // Wait for library to load
                if (typeof JsBarcode !== 'undefined') {
                    JsBarcode("#barcodeCanvas", qrCodeText, {
                        format: "CODE128",
                        width: 2,
                        height: 70,
                        displayValue: false,
                        margin: 10,
                        background: "#ffffff",
                        lineColor: "#000000"
                    });
                    console.log('Barcode generated successfully');
                } else {
                    console.error('JsBarcode library not loaded');
                    // Retry after 500ms
                    setTimeout(generateBarcode, 500);
                }
            } catch (error) {
                console.error('Error generating barcode:', error);
                // Show error message
                document.getElementById('barcodeCanvas').innerHTML =
                    '<text x="150" y="30" fill="#dc2626" text-anchor="middle">Barcode gagal dimuat</text>';
            }
        }

        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', generateBarcode);
        } else {
            generateBarcode();
        }

        function downloadQRCode() {
            const canvas = document.querySelector('#qrcodeCanvas canvas');
            if (!canvas) {
                alert('QR Code sedang di-generate. Mohon tunggu sebentar.');
                return;
            }

            canvas.toBlob(function(blob) {
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.download = 'QR-Code-{{ str_replace(' ', '-', $guest->name) }}.png';
                link.href = url;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(url);
            });
        }

        function shareWhatsApp() {
            const text = 'QR Code Undangan - {{ $guest->name }}';
            const url = window.location.href;
            const waUrl = `https://wa.me/?text=${encodeURIComponent(text + '\n\n' + url)}`;
            window.open(waUrl, '_blank');
        }
    </script>
</body>
</html>
