<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CameraController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\EventController;

// Redirect root to login or dashboard
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Guest routes (not authenticated) - ONLY login accessible without auth
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Protected routes - ALL require authentication
Route::middleware(['auth'])->group(function () {
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Cameras - ALL routes protected
    Route::get('/cameras', [CameraController::class, 'index'])->name('cameras.index');
    Route::get('/cameras/create', [CameraController::class, 'create'])->name('cameras.create');
    Route::post('/cameras', [CameraController::class, 'store'])->name('cameras.store');
    Route::get('/cameras/{camera}/edit', [CameraController::class, 'edit'])->name('cameras.edit');
    Route::put('/cameras/{camera}', [CameraController::class, 'update'])->name('cameras.update');
    Route::delete('/cameras/{camera}', [CameraController::class, 'destroy'])->name('cameras.destroy');
    Route::post('/cameras/{camera}/toggle', [CameraController::class, 'toggle'])->name('cameras.toggle');
    
    // Video feeds - protected
    Route::get('/video_feed', [CameraController::class, 'videoFeed'])->name('video.feed');
    Route::get('/snapshot', [CameraController::class, 'snapshot'])->name('snapshot');
    
    // Events - ALL routes protected
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    Route::get('/events/stream', [EventController::class, 'stream'])->name('events.stream');
    
    // Settings - ALL routes protected
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/test-telegram', [SettingController::class, 'testTelegram'])->name('settings.test-telegram');
});