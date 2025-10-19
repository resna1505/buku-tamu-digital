<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code - {{ $guest->name }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-2xl w-full">
            <!-- Header -->
            <div class="text-center mb-8 no-print">
                <div class="inline-flex items-center justify-center space-x-3 mb-4">
                    <i class="fas fa-qrcode text-5xl text-purple-600"></i>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">TAMU</h1>
                        <p class="text-2xl font-bold text-yellow-600">KAMI</p>
                    </div>
                </div>
                <p class="text-gray-600">E-Invitation QR Code</p>
            </div>

            <!-- QR Card -->
            <div id="qrCardPrint" class="bg-white rounded-3xl shadow-2xl overflow-hidden mb-6">
                <!-- Header Image -->
                <div class="gradient-bg p-8 text-center text-white">
                    <div class="bg-white bg-opacity-20 backdrop-blur-lg rounded-2xl p-6 mb-6 inline-block">
                        <i class="fas fa-qrcode text-6xl"></i>
                    </div>
                    <h2 class="text-3xl font-bold mb-2">TAMU KAMI</h2>
                    <p class="text-lg opacity-90">Digital Guest Book System</p>
                </div>

                <!-- Event Info -->
                <div class="p-8 bg-gradient-to-br from-purple-50 to-blue-50">
                    <div class="text-center mb-6">
                        <p class="text-sm text-gray-600 mb-2">{{ $event->type ?? 'Demo Event' }}</p>
                        <h3 class="text-4xl font-bold text-gray-800 mb-3">{{ $event->name ?? 'Demo Event' }}</h3>
                        <p class="text-lg text-gray-600">{{ $event->date ? $event->date->format('l, d F Y') : 'Kamis, 12 September 2024' }}</p>
                    </div>
                </div>

                <!-- Guest Info -->
                <div class="p-8">
                    <div class="text-center mb-8">
                        <p class="text-gray-600 mb-2 text-lg">Dear,</p>
                        <h4 class="text-5xl font-bold text-gray-800 mb-6">{{ $guest->name }}</h4>
                        <div class="inline-block bg-gray-100 rounded-xl p-4">
                            <p class="text-sm text-gray-600 mb-1">Alamat/Keterangan:</p>
                            <p class="text-xl font-semibold text-gray-800">{{ $guest->address }}</p>
                        </div>
                    </div>

                    <!-- QR Code -->
                    <div class="flex justify-center mb-8">
                        <div id="qrcodeCanvas" class="p-6 bg-white border-4 border-purple-200 rounded-2xl shadow-lg"></div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-black text-white p-8 text-center">
                    <div class="flex items-center justify-center space-x-3 mb-4">
                        <i class="fas fa-qrcode text-3xl text-yellow-500"></i>
                        <span class="text-2xl font-bold">TAMU <span class="text-yellow-500">KAMI</span></span>
                    </div>
                    <p class="text-lg mb-2">HARAP TUNJUKKAN KARTU INI</p>
                    <p class="text-sm opacity-75">SEBAGAI AKSES MASUK LOKASI ACARA!</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-3 no-print">
                <button onclick="window.print()" class="flex-1 bg-purple-600 text-white py-4 px-6 rounded-xl font-semibold hover:bg-purple-700 transition">
                    <i class="fas fa-print mr-2"></i>Print QR Code
                </button>
                <button onclick="downloadQRCode()" class="flex-1 bg-green-600 text-white py-4 px-6 rounded-xl font-semibold hover:bg-green-700 transition">
                    <i class="fas fa-download mr-2"></i>Download
                </button>
                <button onclick="shareWhatsApp()" class="flex-1 bg-blue-600 text-white py-4 px-6 rounded-xl font-semibold hover:bg-blue-700 transition">
                    <i class="fab fa-whatsapp mr-2"></i>Share
                </button>
            </div>

            <div class="mt-6 bg-white rounded-xl p-4 text-center no-print">
                <p class="text-sm text-gray-600">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                    Simpan atau screenshot QR Code ini untuk akses masuk acara
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        // Generate QR Code
        new QRCode(document.getElementById('qrcodeCanvas'), {
            text: '{{ $guest->qr_code }}',
            width: 350,
            height: 350,
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
