<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Camera;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YoloApiController extends Controller
{
    /**
     * Receive event from Flask YOLO API
     */
    public function receiveEvent(Request $request)
    {
        try {
            $validated = $request->validate([
                'event_id' => 'required|string',
                'camera_index' => 'required|integer',
                'event_type' => 'required|string|in:LAPOR,KHALWAT,IKHTILAT',
                'persons_count' => 'required|integer',
                'details' => 'nullable|array',
                'image_base64' => 'nullable|string',
                'timestamp' => 'required|string',
            ]);

            // Find camera
            $camera = Camera::where('camera_index', $validated['camera_index'])->first();
            
            if (!$camera) {
                return response()->json(['error' => 'Camera not found'], 404);
            }

            // Update camera status
            $camera->update([
                'status' => 'online',
                'last_seen' => now(),
            ]);

            // Save image if provided
            $imagePath = null;
            if (!empty($validated['image_base64'])) {
                $imagePath = $this->saveBase64Image($validated['image_base64'], $validated['event_id']);
            }

            // Create event record
            $event = Event::create([
                'event_id' => $validated['event_id'],
                'camera_id' => $camera->id,
                'event_type' => $validated['event_type'],
                'persons_count' => $validated['persons_count'],
                'details' => json_encode($validated['details'] ?? []),
                'image_path' => $imagePath,
                'detected_at' => $validated['timestamp'],
            ]);

            // Send to Telegram
            $this->sendToTelegram($event, $camera);

            return response()->json([
                'success' => true,
                'event_id' => $event->event_id,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to receive event: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to process event'], 500);
        }
    }

    /**
     * Get status for Flask API
     */
    public function status()
    {
        $cameras = Camera::where('is_active', true)->get(['camera_index', 'rtsp_url', 'name']);
        
        return response()->json([
            'running' => true,
            'cameras' => $cameras,
            'config' => Setting::getGroup('detection'),
        ]);
    }

    /**
     * Save base64 image to storage
     */
    private function saveBase64Image($base64String, $eventId)
    {
        try {
            // Remove data:image/jpeg;base64, prefix if exists
            if (strpos($base64String, 'data:image') === 0) {
                $base64String = preg_replace('/^data:image\/\w+;base64,/', '', $base64String);
            }

            $imageData = base64_decode($base64String);
            
            if ($imageData === false) {
                return null;
            }

            $filename = "events/{$eventId}_" . time() . ".jpg";
            Storage::disk('public')->put($filename, $imageData);

            return $filename;

        } catch (\Exception $e) {
            Log::error('Failed to save image: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Send notification to Telegram
     */
    private function sendToTelegram(Event $event, Camera $camera)
    {
        try {
            $botToken = Setting::get('telegram_bot_token');
            $chatId = Setting::get('telegram_chat_id');

            if (empty($botToken) || empty($chatId)) {
                return false;
            }

            $message = "🚨 *Event Detected*\n\n";
            $message .= "📷 *Camera:* {$camera->name}\n";
            $message .= "📍 *Location:* {$camera->location}\n";
            $message .= "⚠️ *Event Type:* {$event->event_type}\n";
            $message .= "👥 *Persons:* {$event->persons_count}\n";
            $message .= "🕐 *Time:* {$event->detected_at->format('Y-m-d H:i:s')}";

            $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

            // Send text message
            $response = Http::timeout(10)->post($url, [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown',
            ]);

            // Send image if available
            if ($event->image_path && Storage::disk('public')->exists($event->image_path)) {
                $imageUrl = asset('storage/' . $event->image_path);
                $photoUrl = "https://api.telegram.org/bot{$botToken}/sendPhoto";

                Http::timeout(10)->post($photoUrl, [
                    'chat_id' => $chatId,
                    'photo' => $imageUrl,
                    'caption' => "Event Image - {$event->event_type}",
                ]);
            }

            if ($response->successful()) {
                $event->update(['telegram_sent' => true]);
                return true;
            }

        } catch (\Exception $e) {
            Log::error('Telegram send failed: ' . $e->getMessage());
        }

        return false;
    }
}