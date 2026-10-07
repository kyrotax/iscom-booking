<?php

use App\Http\Controllers\Admin\BookingApprovalController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - ISCOM Mentoring Booking System
|--------------------------------------------------------------------------
*/

// Public Pages
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/jadwal', [HomeController::class, 'jadwal'])->name('jadwal');
Route::get('/status', [BookingController::class, 'status'])->name('status');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Booking Flow (Protected by auth)
Route::middleware(['auth'])->group(function () {
    Route::get('/booking', function () {
        return redirect()->route('booking.step1');
    })->name('booking');

    Route::get('/booking/data-diri', [BookingController::class, 'step1'])->name('booking.step1');
    Route::post('/booking/data-diri', [BookingController::class, 'postStep1'])->name('booking.step1.post');

    Route::get('/booking/pilih-jadwal', [BookingController::class, 'step2'])->name('booking.step2');
    Route::post('/booking/pilih-jadwal', [BookingController::class, 'postStep2'])->name('booking.step2.post');

    Route::get('/booking/konfirmasi', [BookingController::class, 'step3'])->name('booking.step3');
    Route::post('/booking/konfirmasi', [BookingController::class, 'confirmBooking'])->name('booking.confirm');

    Route::get('/booking/sukses', [BookingController::class, 'sukses'])->name('booking.sukses');
});

// Admin Panel Routes (Protected by auth & admin middleware)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Schedules Management
    Route::resource('schedules', ScheduleController::class);

    // Bookings Approval Management
    Route::get('/bookings', [BookingApprovalController::class, 'index'])->name('bookings.index');
    Route::patch('/bookings/{booking}/accept', [BookingApprovalController::class, 'accept'])->name('bookings.accept');
    Route::patch('/bookings/{booking}/reject', [BookingApprovalController::class, 'reject'])->name('bookings.reject');
    Route::delete('/bookings/{booking}', [BookingApprovalController::class, 'destroy'])->name('bookings.destroy');
});
