<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CameraController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\EventController;

// Guest routes (not authenticated)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Authenticated routes
Route::middleware(['auth', App\Http\Middleware\AdminAuth::class])->group(function () {
    
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Cameras
    Route::resource('cameras', CameraController::class);
    Route::post('/cameras/{camera}/toggle', [CameraController::class, 'toggle'])->name('cameras.toggle');
    Route::get('/video_feed', [CameraController::class, 'videoFeed'])->name('video.feed');
    Route::get('/snapshot', [CameraController::class, 'snapshot'])->name('snapshot');
    
    // Events
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    Route::get('/events/stream', [EventController::class, 'stream'])->name('events.stream');
    
    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/test-telegram', [SettingController::class, 'testTelegram'])->name('settings.test-telegram');
});