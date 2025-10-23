@extends('layouts.app')

@section('title', 'Pengaturan - Ssyar\'S-Intel')

@section('content')
<div class="main-content">
    <div class="page-header">
        <h2>⚙️ Pengaturan Sistem</h2>
    </div>

    <form method="POST" action="{{ route('settings.update') }}">
        @csrf

        <!-- Telegram Settings -->
        <div class="reports-section" style="margin-bottom: 20px;">
            <div class="reports-header">
                <h3>📱 Pengaturan Telegram</h3>
            </div>
            
            <div class="modal-form single-column">
                @foreach($telegramSettings as $setting)
                <div class="form-field">
                    <label>{{ ucwords(str_replace('_', ' ', str_replace('telegram_', '', $setting->key))) }}</label>
                    @if($setting->type === 'number')
                        <input type="number" 
                               name="settings[{{ $setting->key }}]" 
                               value="{{ old('settings.' . $setting->key, $setting->value) }}"
                               step="any">
                    @else
                        <input type="text" 
                               name="settings[{{ $setting->key }}]" 
                               value="{{ old('settings.' . $setting->key, $setting->value) }}"
                               placeholder="{{ $setting->key === 'telegram_bot_token' ? '7988072949:AAGT5ekoXZy...' : ($setting->key === 'telegram_chat_id' ? '-1001234567890' : '') }}">
                    @endif
                    
                    @if($setting->key === 'telegram_bot_token')
                        <small style="color: #666; font-size: 0.85rem;">
                            Dapatkan token dari <a href="https://t.me/BotFather" target="_blank" style="color: #2d5016;">@BotFather</a>
                        </small>
                    @elseif($setting->key === 'telegram_chat_id')
                        <small style="color: #666; font-size: 0.85rem;">
                            Untuk grup: gunakan ID negatif (e.g., -1001234567890)
                        </small>
                    @endif
                </div>
                @endforeach

                <button type="button" class="btn-add" onclick="testTelegram()" style="margin-top: 10px;">
                    📤 Test Telegram
                </button>
                <span id="telegramResult" style="margin-left: 15px; font-weight: 600;"></span>
            </div>
        </div>

        <!-- Detection Settings -->
        <div class="reports-section" style="margin-bottom: 20px;">
            <div class="reports-header">
                <h3>🎯 Pengaturan Deteksi</h3>
            </div>
            
            <div class="modal-form" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
                @foreach($detectionSettings as $setting)
                <div class="form-field">
                    <label>{{ ucwords(str_replace('_', ' ', $setting->key)) }}</label>
                    <input type="number" 
                           name="settings[{{ $setting->key }}]" 
                           value="{{ old('settings.' . $setting->key, $setting->value) }}"
                           step="any">
                    <small style="color: #666; font-size: 0.85rem;">Default: {{ $setting->value }}</small>
                </div>
                @endforeach
            </div>
        </div>

        <!-- General Settings -->
        <div class="reports-section" style="margin-bottom: 20px;">
            <div class="reports-header">
                <h3>🔧 Pengaturan Umum</h3>
            </div>
            
            <div class="modal-form single-column">
                @foreach($generalSettings as $setting)
                <div class="form-field">
                    <label>{{ ucwords(str_replace('_', ' ', $setting->key)) }}</label>
                    <input type="text" 
                           name="settings[{{ $setting->key }}]" 
                           value="{{ old('settings.' . $setting->key, $setting->value) }}">
                    
                    @if($setting->key === 'flask_api_url')
                        <small style="color: #666; font-size: 0.85rem;">
                            URL Flask YOLO API server (default: http://localhost:5000)
                        </small>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        <!-- Save Button -->
        <div style="text-align: center; padding: 20px;">
            <button type="submit" class="btn-add" style="padding: 15px 40px; font-size: 18px;">
                💾 Simpan Pengaturan
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function testTelegram() {
    const resultSpan = document.getElementById('telegramResult');
    resultSpan.textContent = '⏳ Mengirim...';
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
            resultSpan.style.color = '#4CAF50';
        } else {
            resultSpan.textContent = '❌ ' + data.message;
            resultSpan.style.color = '#d32f2f';
        }
        
        setTimeout(() => {
            resultSpan.textContent = '';
        }, 5000);
    })
    .catch(error => {
        resultSpan.textContent = '❌ Error: ' + error.message;
        resultSpan.style.color = '#d32f2f';
        
        setTimeout(() => {
            resultSpan.textContent = '';
        }, 5000);
    });
}
</script>
@endpush