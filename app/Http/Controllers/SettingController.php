<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function index()
    {
        $telegramSettings = Setting::where('group', 'telegram')->get();
        $detectionSettings = Setting::where('group', 'detection')->get();
        $generalSettings = Setting::where('group', 'general')->get();

        return view('settings.index', compact(
            'telegramSettings',
            'detectionSettings',
            'generalSettings'
        ));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable',
        ]);

        foreach ($validated['settings'] as $key => $value) {
            Setting::set($key, $value);
        }

        // Clear all settings cache
        Cache::flush();

        return redirect()->route('settings.index')
            ->with('success', 'Settings updated successfully');
    }

    public function testTelegram(Request $request)
    {
        $botToken = Setting::get('telegram_bot_token');
        $chatId = Setting::get('telegram_chat_id');

        if (empty($botToken) || empty($chatId)) {
            return response()->json([
                'success' => false,
                'message' => 'Please configure Telegram Bot Token and Chat ID first',
            ]);
        }

        try {
            $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
            
            $response = \Http::timeout(10)->post($url, [
                'chat_id' => $chatId,
                'text' => "✅ Test message from YOLO Action Dashboard\n\nTime: " . now()->format('Y-m-d H:i:s'),
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Test message sent successfully!',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send message: ' . $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }
}