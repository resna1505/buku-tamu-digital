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

        @media print {
            body * {
                visibility: hidden;
            }
            #qrCardPrint, #qrCardPrint * {
                visibility: visible;
            }
            #qrCardPrint {
                position: absolute;
                left: 50%;
                top: 50%;
                transform: translate(-50%, -50%);
            }
            .no-print {
                display: none !important;
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
                    <h2 class="text-2 font-bold mb-2">Selamat Datang di</h2>
                    <h3 class="text-2xl font-bold leading-tight">Acara Wisuda-XXI, T/A: 2024/2025</h3>
                    {{-- @if($event && $event->date)
                    <p class="text-lg mt-2 font-semibold">T/A: {{ $event->date->format('Y') }}/{{ $event->date->addYear()->format('Y') }}</p>
                    @else
                    <p class="text-lg mt-2 font-semibold">T/A: 2024/2025</p>
                    @endif --}}
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
                    <div class="flex justify-center mb-8">
                        <div id="qrcodeCanvas" class="p-5 bg-white qr-border rounded-2xl"></div>
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
                <button onclick="window.print()" class="flex-1 bg-gray-700 text-white py-4 px-6 rounded-lg font-semibold hover:bg-gray-800 transition shadow-md">
                    <i class="fas fa-print mr-2"></i>Print QR Code
                </button>
                <button onclick="downloadQRCode()" class="flex-1 bg-gray-700 text-white py-4 px-6 rounded-lg font-semibold hover:bg-green-700 transition shadow-md">
                    <i class="fas fa-download mr-2"></i>Print QR Code
                </button>
                <button onclick="shareWhatsApp()" class="flex-1 bg-gray-700 text-white py-4 px-6 rounded-lg font-semibold hover:bg-blue-700 transition shadow-md">
                    <i class="fab fa-whatsapp mr-2"></i>Share
                </button>
            </div>

            <!-- Info Note -->
            <div class="bg-white rounded-lg p-4 text-center no-print shadow-sm border border-gray-200">
                <p class="text-base font-bold text-gray-800 mb-1">QR CODE INI BERLAKU UNTUK 2 (DUA) TAMU</p>
                <p class="text-sm text-gray-500">Simpan atau screenshot QR Code ini untuk akses masuk acara</p>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        // Generate QR Code
        new QRCode(document.getElementById('qrcodeCanvas'), {
            text: '{{ $guest->qr_code }}',
            width: 300,
            height: 300,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });

        function downloadQRCode() {
            const canvas = document.querySelector('#qrcodeCanvas canvas');
            const image = canvas.toDataURL("image/png").replace("image/png", "image/octet-stream");
            const link = document.createElement('a');
            link.download = 'QR-Code-{{ $guest->name }}.png';
            link.href = image;
            link.click();
        }

        function shareWhatsApp() {
            const text = 'QR Code Undangan - {{ $guest->name }}';
            const url = window.location.href;
            const waUrl = `https://wa.me/?text=${encodeURIComponent(text + '\n' + url)}`;
            window.open(waUrl, '_blank');
        }
    </script>
</body>
</html>
