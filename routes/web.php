<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\SouvenirController;
use App\Http\Controllers\GreetingController;
use App\Http\Controllers\SettingsController;

// Guest routes
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Home
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Guests Management
    Route::resource('guests', GuestController::class);
    Route::post('guests/import', [GuestController::class, 'import'])->name('guests.import');
    Route::get('guests/export/pdf', [GuestController::class, 'exportPdf'])->name('guests.export.pdf');
    Route::get('guests/export/excel', [GuestController::class, 'exportExcel'])->name('guests.export.excel');
    Route::post('guests/{guest}/send-whatsapp', [GuestController::class, 'sendWhatsApp'])->name('guests.send-whatsapp');
    Route::get('guests-bulk-whatsapp', [GuestController::class, 'bulkWhatsApp'])->name('guests.bulk-whatsapp');

    // Check-in
    Route::get('/checkin', [CheckInController::class, 'index'])->name('checkin.index');
    Route::post('/checkin/scan', [CheckInController::class, 'scan'])->name('checkin.scan');
    Route::get('/checkin/success/{guest}', [CheckInController::class, 'success'])->name('checkin.success');

    // Attendance
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');

    // Souvenir
    Route::get('/souvenir', [SouvenirController::class, 'index'])->name('souvenir.index');

    // Greeting
    Route::get('/greeting', [GreetingController::class, 'index'])->name('greeting.index');

    // Search
    Route::get('/search', [GuestController::class, 'search'])->name('guests.search');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
});

// Public WhatsApp QR Download Page
Route::get('/whatsapp/undangan/{qrCode}', function($qrCode) {
    $guest = \App\Models\Guest::where('qr_code', $qrCode)->firstOrFail();
    $event = $guest->event;
    return view('public.qr-download', compact('guest', 'event'));
})->name('whatsapp.qr-download');
