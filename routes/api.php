<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\YoloApiController;

// Flask API endpoints (no authentication required for Flask to communicate)
Route::post('/events/receive', [YoloApiController::class, 'receiveEvent'])->name('api.events.receive');
Route::get('/status', [YoloApiController::class, 'status'])->name('api.status');

// Authenticated API endpoints
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});