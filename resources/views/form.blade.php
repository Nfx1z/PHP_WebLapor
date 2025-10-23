@extends('layouts.app')

@section('title', isset($camera) ? 'Edit Camera' : 'Add Camera')

@section('content')
<div class="page-header">
    <h2>{{ isset($camera) ? '✏️ Edit Camera' : '➕ Add New Camera' }}</h2>
    <p>{{ isset($camera) ? 'Update camera configuration' : 'Configure a new CCTV camera' }}</p>
</div>

<section style="padding: 20px;">
    <div style="max-width: 800px; margin: 0 auto; background: white; border-radius: 8px; padding: 30px;">
        <form method="POST" action="{{ isset($camera) ? route('cameras.update', $camera) : route('cameras.store') }}">
            @csrf
            @if(isset($camera))
                @method('PUT')
            @endif

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 600;">
                    Camera Name <span style="color: red;">*</span>
                </label>
                <input type="text" 
                       name="name" 
                       value="{{ old('name', $camera->name ?? '') }}" 
                       required
                       placeholder="e.g., Main Gate, Parking Lot"
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                @error('name')
                    <span style="color: #dc3545; font-size: 12px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 600;">
                    Location
                </label>
                <input type="text" 
                       name="location" 
                       value="{{ old('location', $camera->location ?? '') }}"
                       placeholder="e.g., Building A - Floor 1, Entrance Hall"
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                @error('location')
                    <span style="color: #dc3545; font-size: 12px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 600;">
                    RTSP URL <span style="color: red;">*</span>
                </label>
                <input type="text" 
                       name="rtsp_url" 
                       value="{{ old('rtsp_url', $camera->rtsp_url ?? '') }}" 
                       required
                       placeholder="rtsp://username:password@192.168.1.100:554/stream"
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; font-family: monospace;">
                @error('rtsp_url')
                    <span style="color: #dc3545; font-size: 12px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
                <small style="color: #6c757d; font-size: 12px; display: block; margin-top: 5px;">
                    Format: rtsp://[username]:[password]@[ip_address]:[port]/[path]
                </small>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 600;">
                    Camera Index <span style="color: red;">*</span>
                </label>
                <input type="number" 
                       name="camera_index" 
                       value="{{ old('camera_index', $camera->camera_index ?? $availableIndex ?? 0) }}" 
                       required
                       min="0"
                       {{ isset($camera) ? 'readonly' : '' }}
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; {{ isset($camera) ? 'background: #f4f5f7;' : '' }}">
                @error('camera_index')
                    <span style="color: #dc3545; font-size: 12px; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
                <small style="color: #6c757d; font-size: 12px; display: block; margin-top: 5px;">
                    Unique identifier for this camera (0, 1, 2, 3, etc.)
                    @if(isset($camera))
                        - Cannot be changed after creation
                    @endif
                </small>
            </div>

            <div style="margin-bottom: 30px;">
                <label style="display: flex; align-items: center; cursor: pointer;">
                    <input type="checkbox" 
                           name="is_active" 
                           value="1"
                           {{ old('is_active', $camera->is_active ?? true) ? 'checked' : '' }}
                           style="margin-right: 10px; width: 18px; height: 18px; cursor: pointer;">
                    <span style="color: #333; font-weight: 600;">Active (Start monitoring immediately)</span>
                </label>
                <small style="color: #6c757d; font-size: 12px; display: block; margin-top: 5px; margin-left: 28px;">
                    When active, the system will connect to this camera and start detection
                </small>
            </div>

            <div style="display: flex; gap: 10px; padding-top: 20px; border-top: 1px solid #eee;">
                <button type="submit" 
                        style="flex: 1; padding: 12px; background: #2e5d2e; color: white; border: none; border-radius: 6px; font-size: 16px; font-weight: 600; cursor: pointer;">
                    {{ isset($camera) ? '💾 Update Camera' : '➕ Add Camera' }}
                </button>
                <a href="{{ route('cameras.index') }}" 
                   style="flex: 1; padding: 12px; background: #6c757d; color: white; border: none; border-radius: 6px; font-size: 16px; font-weight: 600; text-align: center; text-decoration: none; display: block;">
                    ❌ Cancel
                </a>
            </div>
        </form>
    </div>
</section>

<section style="padding: 20px; max-width: 800px; margin: 0 auto;">
    <div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 20px; color: #856404;">
        <h3 style="margin-bottom: 10px; font-size: 16px;">💡 RTSP URL Tips</h3>
        <ul style="margin: 0; padding-left: 20px; line-height: 1.8;">
            <li>Make sure your camera supports RTSP protocol</li>
            <li>Check camera documentation for the correct RTSP path</li>
            <li>Common formats:
                <ul style="margin-top: 5px;">
                    <li><code>rtsp://admin:password@192.168.1.64:554/stream1</code></li>
                    <li><code>rtsp://admin:password@192.168.1.64:554/cam/realmonitor?channel=1&subtype=0</code></li>
                    <li><code>rtsp://admin:password@192.168.1.64:554/h264_stream</code></li>
                </ul>
            </li>
            <li>Test your RTSP URL with VLC Media Player before adding it here</li>
            <li>Ensure the camera is accessible from this server's network</li>
        </ul>
    </div>
</section>
@endsection