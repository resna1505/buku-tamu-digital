<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>TAMU KAMI - Demo Event</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 100vh;
            padding-bottom: 80px;
        }

        /* Header */
        .header {
            background: #1a1560;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-icon {
            width: 35px;
            height: 35px;
            background: white;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #1a1560;
            font-size: 18px;
        }

        .logo-text {
            color: white;
            font-size: 20px;
            font-weight: bold;
        }

        .logo-text span {
            color: #ffa500;
        }

        .menu-btn {
            background: none;
            border: none;
            color: white;
            font-size: 28px;
            cursor: pointer;
            padding: 5px;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)),
                        url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 600"><rect fill="%23334155" width="1200" height="600"/></svg>');
            background-size: cover;
            background-position: center;
            padding: 40px 20px;
            text-align: center;
            position: relative;
        }

        .qr-container {
            background: white;
            width: 150px;
            height: 150px;
            margin: 0 auto 20px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        }

        .qr-code {
            width: 130px;
            height: 130px;
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
        }

        .hero-logo {
            margin-bottom: 20px;
        }

        .hero-title {
            color: white;
            font-size: 48px;
            font-weight: 900;
            margin-bottom: 5px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        .hero-subtitle {
            color: #ffa500;
            font-size: 42px;
            font-weight: 900;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        /* Event Info Card */
        .event-card {
            background: linear-gradient(135deg, #2d1b69 0%, #1a1560 100%);
            margin: -30px 20px 30px;
            padding: 25px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
            position: relative;
            z-index: 1;
        }

        .event-label {
            color: #ffa500;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .event-title {
            color: white;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .event-date {
            color: #e0e0e0;
            font-size: 16px;
        }

        /* Menu Grid */
        .menu-grid {
            background: white;
            margin: 0 20px;
            padding: 30px 20px;
            border-radius: 25px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }

        .menu-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            transition: transform 0.3s;
        }

        .menu-item:active {
            transform: scale(0.95);
        }

        .menu-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #2d1b69 0%, #1a1560 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            box-shadow: 0 5px 15px rgba(29, 27, 105, 0.3);
            font-size: 35px;
        }

        .menu-label {
            color: #1a1560;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
        }

        /* Bottom Navigation */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            display: flex;
            justify-content: space-around;
            padding: 12px 0;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            z-index: 100;
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: #999;
            font-size: 11px;
            transition: color 0.3s;
        }

        .nav-item.active {
            color: #ff6b35;
        }

        .nav-icon {
            font-size: 24px;
            margin-bottom: 4px;
        }

        .qr-scan-btn {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #ffa500 0%, #ff6b35 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: -30px;
            box-shadow: 0 5px 15px rgba(255, 107, 53, 0.4);
            color: white;
            font-size: 28px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">
            <div class="logo-icon">🏛️</div>
            <div class="logo-text">TAMU <span>KAMI</span></div>
        </div>
        <button class="menu-btn">☰</button>
    </div>

    <!-- Hero Section -->
    <div class="hero">
        <div class="qr-container">
            <div class="qr-code"></div>
        </div>
        <div class="hero-logo">
            <div class="hero-title">TAMU</div>
            <div class="hero-subtitle">KAMI</div>
        </div>
    </div>

    <!-- Event Info Card -->
    <div class="event-card">
        <div class="event-label">Demo Event</div>
        <div class="event-title">Demo Event</div>
        <div class="event-date">Kamis, 12 September 2024</div>
    </div>

    <!-- Menu Grid -->
    <div class="menu-grid">
        <a href="#" class="menu-item">
            <div class="menu-icon">🗺️</div>
            <div class="menu-label">DATA TAMU</div>
        </a>
        <a href="#" class="menu-item">
            <div class="menu-icon">📱</div>
            <div class="menu-label">CHECK-IN</div>
        </a>
        <a href="#" class="menu-item">
            <div class="menu-icon">📋</div>
            <div class="menu-label">KEHADIRAN</div>
        </a>
        <a href="#" class="menu-item">
            <div class="menu-icon">🎁</div>
            <div class="menu-label">SOUVENIR</div>
        </a>
        <a href="#" class="menu-item">
            <div class="menu-icon">💬</div>
            <div class="menu-label">UCAPAN</div>
        </a>
        <a href="#" class="menu-item">
            <div class="menu-icon">📱</div>
            <div class="menu-label">LAYAR SAPA</div>
        </a>
    </div>

    <!-- Bottom Navigation -->
    <div class="bottom-nav">
        <a href="#" class="nav-item active">
            <div class="nav-icon">🏠</div>
            <div>Home</div>
        </a>
        <a href="#" class="nav-item">
            <div class="nav-icon">🔍</div>
            <div>Cari Tamu</div>
        </a>
        <a href="#" class="nav-item">
            <div class="qr-scan-btn">📷</div>
            <div>QR Scan</div>
        </a>
        <a href="#" class="nav-item">
            <div class="nav-icon">👤</div>
            <div>Tamu Baru</div>
        </a>
        <a href="#" class="nav-item">
            <div class="nav-icon">⚙️</div>
            <div>Pengaturan</div>
        </a>
    </div>
</body>
</html>
