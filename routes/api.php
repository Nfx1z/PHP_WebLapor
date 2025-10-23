<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\YoloApiController;
use App\Models\Event;

// Flask API endpoints
Route::post('/events/receive', [YoloApiController::class, 'receiveEvent'])->name('api.events.receive');
Route::get('/status', [YoloApiController::class, 'status'])->name('api.status');

// Add this new route for event details
Route::get('/events/{event}', function(Event $event) {
    $event->load('camera');
    return response()->json([
        'event_id' => $event->event_id,
        'event_type' => $event->event_type,
        'persons_count' => $event->persons_count,
        'details' => $event->details,
        'detected_at' => $event->detected_at->format('Y-m-d H:i:s'),
        'telegram_sent' => $event->telegram_sent,
        'image_url' => $event->image_url,
        'camera' => [
            'name' => $event->camera->name,
            'location' => $event->camera->location,
        ]
    ]);
});

// Authenticated API endpoints
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});