<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TamuKami - Digital Guest Book')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .card-shadow {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            z-index: 50;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50">
    <!-- Top Navigation -->
    <nav class="gradient-bg text-white p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                    <i class="fas fa-qrcode text-purple-600 text-xl"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold">TamuKami</h1>
                    <p class="text-xs opacity-90">Digital Guest Book</p>
                </div>
            </div>

            <button id="menuToggle" class="text-2xl">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>

    <!-- Sidebar Menu (Hidden by default) -->
    <div id="sideMenu" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="absolute right-0 top-0 h-full w-64 bg-white shadow-2xl transform transition-transform duration-300">
            <div class="p-6">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-xl font-bold text-gray-800">Menu</h2>
                    <button id="closeMenu" class="text-gray-600 text-2xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="space-y-4">
                    <a href="{{ route('home') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-purple-50 transition">
                        <i class="fas fa-home text-purple-600"></i>
                        <span>Beranda</span>
                    </a>

                    <a href="{{ route('guests.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-purple-50 transition">
                        <i class="fas fa-users text-purple-600"></i>
                        <span>Data Tamu</span>
                    </a>

                    <a href="{{ route('attendance.index') }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-purple-50 transition">
                        <i class="fas fa-clipboard-check text-purple-600"></i>
                        <span>Kehadiran</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center space-x-3 p-3 rounded-lg hover:bg-red-50 transition text-left">
                            <i class="fas fa-sign-out-alt text-red-600"></i>
                            <span class="text-red-600">Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-6 pb-24">
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4 flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 flex items-center">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
        @endif

        @yield('content')
    </main>

    <!-- Bottom Navigation -->
    <div class="bottom-nav">
        <div class="flex justify-around items-center py-3">
            <a href="{{ route('home') }}" class="flex flex-col items-center {{ request()->routeIs('home') ? 'text-purple-600' : 'text-gray-500' }}">
                <i class="fas fa-home text-xl mb-1"></i>
                <span class="text-xs">Home</span>
            </a>

            <a href="{{ route('guests.search') }}" class="flex flex-col items-center {{ request()->routeIs('guests.search') ? 'text-purple-600' : 'text-gray-500' }}">
                <i class="fas fa-search text-xl mb-1"></i>
                <span class="text-xs">Cari Tamu</span>
            </a>

            <a href="{{ route('checkin.index') }}" class="flex flex-col items-center -mt-8">
                <div class="w-16 h-16 rounded-full gradient-bg flex items-center justify-center shadow-lg">
                    <i class="fas fa-qrcode text-white text-2xl"></i>
                </div>
            </a>

            <a href="{{ route('guests.create') }}" class="flex flex-col items-center {{ request()->routeIs('guests.create') ? 'text-purple-600' : 'text-gray-500' }}">
                <i class="fas fa-user-plus text-xl mb-1"></i>
                <span class="text-xs">Tamu Baru</span>
            </a>

            <a href="#" id="settingsBtn" class="flex flex-col items-center text-gray-500">
                <i class="fas fa-cog text-xl mb-1"></i>
                <span class="text-xs">Pengaturan</span>
            </a>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Menu Toggle
        const menuToggle = document.getElementById('menuToggle');
        const sideMenu = document.getElementById('sideMenu');
        const closeMenu = document.getElementById('closeMenu');

        menuToggle.addEventListener('click', () => {
            sideMenu.classList.remove('hidden');
        });

        closeMenu.addEventListener('click', () => {
            sideMenu.classList.add('hidden');
        });

        sideMenu.addEventListener('click', (e) => {
            if (e.target === sideMenu) {
                sideMenu.classList.add('hidden');
            }
        });

        // Settings Button
        document.getElementById('settingsBtn').addEventListener('click', (e) => {
            e.preventDefault();
            menuToggle.click();
        });
    </script>

    @stack('scripts')
</body>
</html>
