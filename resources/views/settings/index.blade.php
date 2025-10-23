@extends('layouts.app')

@section('title', 'Settings - YOLO Action Dashboard')

@section('content')
<div class="page-header">
    <h2>⚙️ System Settings</h2>
    <p>Configure system parameters, Telegram notifications, and detection thresholds</p>
</div>

<section style="padding: 20px;">
    <form method="POST" action="{{ route('settings.update') }}">
        @csrf

        <!-- Telegram Settings -->
        <div style="background: white; border-radius: 8px; padding: 25px; margin-bottom: 20px;">
            <h3 style="color: #2e5d2e; margin-bottom: 20px; font-size: 20px; border-bottom: 2px solid #2e5d2e; padding-bottom: 10px;">
                📱 Telegram Notification Settings
            </h3>

            @foreach($telegramSettings as $setting)
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 600;">
                    {{ ucwords(str_replace('_', ' ', str_replace('telegram_', '', $setting->key))) }}
                    @if(in_array($setting->key, ['telegram_bot_token', 'telegram_chat_id']))
                        <span style="color: red;">*</span>
                    @endif
                </label>
                
                @if($setting->type === 'number')
                    <input type="number" 
                           name="settings[{{ $setting->key }}]" 
                           value="{{ old('settings.' . $setting->key, $setting->value) }}"
                           step="any"
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                @else
                    <input type="text" 
                           name="settings[{{ $setting->key }}]" 
                           value="{{ old('settings.' . $setting->key, $setting->value) }}"
                           placeholder="{{ $setting->key === 'telegram_bot_token' ? '7988072949:AAGT5ekoXZy-gFRESMu...' : ($setting->key === 'telegram_chat_id' ? '-1001234567890' : '') }}"
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; {{ $setting->key === 'telegram_bot_token' ? 'font-family: monospace;' : '' }}">
                @endif

                @if($setting->key === 'telegram_bot_token')
                    <small style="color: #6c757d; font-size: 12px; display: block; margin-top: 5px;">
                        Get your bot token from <a href="https://t.me/BotFather" target="_blank" style="color: #2e5d2e;">@BotFather</a> on Telegram
                    </small>
                @elseif($setting->key === 'telegram_chat_id')
                    <small style="color: #6c757d; font-size: 12px; display: block; margin-top: 5px;">
                        For groups: Use negative ID (e.g., -1001234567890). Get from <a href="https://t.me/userinfobot" target="_blank" style="color: #2e5d2e;">@userinfobot</a>
                    </small>
                @elseif($setting->key === 'telegram_cooldown')
                    <small style="color: #6c757d; font-size: 12px; display: block; margin-top: 5px;">
                        Minimum seconds between notifications to prevent spam
                    </small>
                @endif
            </div>
            @endforeach

            <button type="button" 
                    onclick="testTelegram()" 
                    style="padding: 10px 20px; background: #17a2b8; color: white; border: none; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; margin-top: 10px;">
                📤 Send Test Message
            </button>
            <span id="telegramTestResult" style="margin-left: 15px; font-weight: 600;"></span>
        </div>

        <!-- Detection Settings -->
        <div style="background: white; border-radius: 8px; padding: 25px; margin-bottom: 20px;">
            <h3 style="color: #2e5d2e; margin-bottom: 20px; font-size: 20px; border-bottom: 2px solid #2e5d2e; padding-bottom: 10px;">
                🎯 Detection Threshold Settings
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                @foreach($detectionSettings as $setting)
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 600;">
                        {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                    </label>
                    <input type="number" 
                           name="settings[{{ $setting->key }}]" 
                           value="{{ old('settings.' . $setting->key, $setting->value) }}"
                           step="any"
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                    
                    @if($setting->key === 'dist_thresh')
                        <small style="color: #6c757d; font-size: 12px; display: block; margin-top: 5px;">
                            Maximum normalized distance between hands for LAPOR detection (default: 0.20)
                        </small>
                    @elseif($setting->key === 'hand_height_above_head')
                        <small style="color: #6c757d; font-size: 12px; display: block; margin-top: 5px;">
                            Minimum height ratio of hands above head (default: 0.06)
                        </small>
                    @elseif($setting->key === 'proximity_thresh')
                        <small style="color: #6c757d; font-size: 12px; display: block; margin-top: 5px;">
                            Maximum distance for people proximity detection (default: 0.35)
                        </small>
                    @elseif($setting->key === 'iou_overlap_thresh')
                        <small style="color: #6c757d; font-size: 12px; display: block; margin-top: 5px;">
                            IoU threshold for detecting overlapping people (default: 0.12)
                        </small>
                    @elseif($setting->key === 'smooth_frames')
                        <small style="color: #6c757d; font-size: 12px; display: block; margin-top: 5px;">
                            Number of consecutive frames needed to confirm detection (default: 3)
                        </small>
                    @elseif($setting->key === 'img_size')
                        <small style="color: #6c757d; font-size: 12px; display: block; margin-top: 5px;">
                            YOLO input image size in pixels (default: 640)
                        </small>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        <!-- General Settings -->
        <div style="background: white; border-radius: 8px; padding: 25px; margin-bottom: 20px;">
            <h3 style="color: #2e5d2e; margin-bottom: 20px; font-size: 20px; border-bottom: 2px solid #2e5d2e; padding-bottom: 10px;">
                🔧 General Settings
            </h3>

            @foreach($generalSettings as $setting)
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 600;">
                    {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                </label>
                <input type="text" 
                       name="settings[{{ $setting->key }}]" 
                       value="{{ old('settings.' . $setting->key, $setting->value) }}"
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; {{ $setting->key === 'flask_api_url' ? 'font-family: monospace;' : '' }}">
                
                @if($setting->key === 'flask_api_url')
                    <small style="color: #6c757d; font-size: 12px; display: block; margin-top: 5px;">
                        URL of the Flask YOLO API server (default: http://localhost:5000)
                    </small>
                @elseif($setting->key === 'save_directory')
                    <small style="color: #6c757d; font-size: 12px; display: block; margin-top: 5px;">
                        Directory path for saving event images and videos
                    </small>
                @endif
            </div>
            @endforeach
        </div>

        <!-- Save Button -->
        <div style="text-align: center; padding: 20px;">
            <button type="submit" 
                    style="padding: 15px 40px; background: #2e5d2e; color: white; border: none; border-radius: 6px; font-size: 18px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                💾 Save All Settings
            </button>
        </div>
    </form>
</section>

<!-- Info Panel -->
<section style="padding: 20px; max-width: 1200px; margin: 0 auto;">
    <div style="background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 8px; padding: 20px; color: #0c5460;">
        <h3 style="margin-bottom: 15px; font-size: 16px;">ℹ️ Important Information</h3>
        <ul style="margin: 0; padding-left: 20px; line-height: 1.8;">
            <li><strong>Detection Thresholds:</strong> Lower values make detection more sensitive but may increase false positives</li>
            <li><strong>Smooth Frames:</strong> Higher values reduce false detections but may delay event notification</li>
            <li><strong>Telegram Setup:</strong> Make sure to add your bot to the group and give it permission to send messages</li>
            <li><strong>Flask API:</strong> Ensure the Flask YOLO API is running before activating cameras</li>
            <li><strong>Changes:</strong> Some settings may require restarting active cameras to take effect</li>
        </ul>
    </div>
</section>
@endsection

@push('scripts')
<script>
function testTelegram() {
    const resultSpan = document.getElementById('telegramTestResult');
    resultSpan.textContent = '⏳ Sending...';
    resultSpan.style.color = '#6c757d';
    
    fetch('{{ route('settings.test-telegram') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resultSpan.textContent = '✅ ' + data.message;
            resultSpan.style.color = '#28a745';
        } else {
            resultSpan.textContent = '❌ ' + data.message;
            resultSpan.style.color = '#dc3545';
        }
        
        setTimeout(() => {
            resultSpan.textContent = '';
        }, 5000);
    })
    .catch(error => {
        resultSpan.textContent = '❌ Error: ' + error.message;
        resultSpan.style.color = '#dc3545';
        
        setTimeout(() => {
            resultSpan.textContent = '';
        }, 5000);
    });
}
</script>
@endpush