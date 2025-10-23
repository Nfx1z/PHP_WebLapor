@extends('layouts.app')

@section('title', 'Dashboard - YOLO Action Dashboard')

@section('content')
<div class="page-header">
    <h2>📊 Dashboard Overview</h2>
    <p>Monitor all cameras and recent events in real-time</p>
</div>

<div class="system-summary">
    <h2>System Summary</h2>
    <div class="summary-cards">
        <div class="summary-card">
            <h3>Total Cameras</h3>
            <p>{{ $totalCameras }}</p>
        </div>
        <div class="summary-card">
            <h3>Active Cameras</h3>
            <p>{{ $activeCameras }}</p>
        </div>
        <div class="summary-card">
            <h3>Online Cameras</h3>
            <p>{{ $onlineCameras }}</p>
        </div>
        <div class="summary-card">
            <h3>Today's Events</h3>
            <p>{{ $todayEvents }}</p>
        </div>
        <div class="summary-card">
            <h3>Total Events</h3>
            <p>{{ $totalEvents }}</p>
        </div>
        <div class="summary-card">
            <h3>LAPOR Events (7d)</h3>
            <p>{{ $eventsByType['LAPOR'] ?? 0 }}</p>
        </div>
        <div class="summary-card">
            <h3>KHALWAT Events (7d)</h3>
            <p>{{ $eventsByType['KHALWAT'] ?? 0 }}</p>
        </div>
        <div class="summary-card">
            <h3>IKHTILAT Events (7d)</h3>
            <p>{{ $eventsByType['IKHTILAT'] ?? 0 }}</p>
        </div>
    </div>
</div>

<section style="padding: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="color: white;">📹 Live Camera Feeds</h2>
        <button onclick="attachStreamToAll()" style="padding: 8px 16px; background: #ffd700; color: #1a2e1a; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">
            Load All Streams
        </button>
    </div>

    <div class="camera-grid">
        @forelse($cameras as $camera)
        <div class="camera-card">
            <div class="cam-header">
                <span style="font-weight: bold;">{{ $camera->name }}</span>
                <span class="status-badge {{ $camera->status === 'online' ? 'live' : '' }}">
                    {{ strtoupper($camera->status) }}
                </span>
            </div>
            <div class="cam-body" id="video-{{ $camera->camera_index }}">
                <div class="placeholder">
                    <div class="play-button" onclick="playStream({{ $camera->camera_index }})" style="cursor: pointer;">▶</div>
                    <p>{{ $camera->location ?? 'No location' }}</p>
                    <small>Click play to start stream</small>
                </div>
            </div>
            <div class="cam-footer">
                <button class="btn-edit" onclick="playStream({{ $camera->camera_index }})">Play</button>
                <button class="btn-delete" onclick="snapshot({{ $camera->camera_index }})">Snapshot</button>
            </div>
        </div>
        @empty
        <div class="camera-card add-card">
            <div class="add-content">
                <h3>No Cameras</h3>
                <p>Add your first camera to start monitoring</p>
                <a href="{{ route('cameras.create') }}" style="display: inline-block; margin-top: 10px; padding: 8px 16px; background: #ffd700; color: #1a2e1a; text-decoration: none; border-radius: 6px; font-weight: bold;">
                    Add Camera
                </a>
            </div>
        </div>
        @endforelse
    </div>
</section>

<section style="padding: 20px;">
    <h2 style="color: white; margin-bottom: 20px;">🚨 Recent Events</h2>
    <div style="background: white; border-radius: 8px; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #2e5d2e; color: white;">
                <tr>
                    <th style="padding: 12px; text-align: left;">Camera</th>
                    <th style="padding: 12px; text-align: left;">Location</th>
                    <th style="padding: 12px; text-align: left;">Event Type</th>
                    <th style="padding: 12px; text-align: left;">Persons</th>
                    <th style="padding: 12px; text-align: left;">Time</th>
                    <th style="padding: 12px; text-align: left;">Image</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentEvents as $event)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px;">{{ $event->camera->name }}</td>
                    <td style="padding: 12px;">{{ $event->camera->location ?? '-' }}</td>
                    <td style="padding: 12px;">
                        <span style="padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: bold; 
                            background: {{ $event->event_type === 'LAPOR' ? '#dc3545' : ($event->event_type === 'KHALWAT' ? '#ffc107' : '#17a2b8') }}; 
                            color: white;">
                            {{ $event->event_type }}
                        </span>
                    </td>
                    <td style="padding: 12px;">{{ $event->persons_count }}</td>
                    <td style="padding: 12px;">{{ $event->detected_at->format('Y-m-d H:i:s') }}</td>
                    <td style="padding: 12px;">
                        @if($event->image_path)
                            <img src="{{ asset('storage/' . $event->image_path) }}" alt="Event" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px; cursor: pointer;" onclick="window.open(this.src, '_blank')">
                        @else
                            <span style="color: #999;">No image</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding: 20px; text-align: center; color: #999;">
                        No events recorded yet
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($recentEvents->count() > 0)
        <div style="text-align: center; margin-top: 20px;">
            <a href="{{ route('events.index') }}" style="display: inline-block; padding: 10px 20px; background: #ffd700; color: #1a2e1a; text-decoration: none; border-radius: 6px; font-weight: bold;">
                View All Events
            </a>
        </div>
    @endif
</section>

<!-- Report Modal -->
<div id="reportModal" class="modal" aria-hidden="true">
    <div class="modal-content">
        <button class="close" onclick="closeReportModal()">×</button>
        <h2 id="modalTitle">Event Report</h2>
        <div class="modal-body">
            <p><strong>Event:</strong> <span id="modalEvent"></span></p>
            <p><strong>Camera:</strong> <span id="modalCamera"></span></p>
            <p><strong>Location:</strong> <span id="modalLocation"></span></p>
            <p><strong>Time:</strong> <span id="modalTime"></span></p>
            <p><strong>Persons:</strong> <span id="modalPersons"></span></p>
            <p><strong>Details:</strong> <pre id="modalDetails"></pre></p>
            <div id="modalImageWrap"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const API_BASE = '{{ url('/') }}';

function playStream(index) {
    const container = document.getElementById(`video-${index}`);
    if (!container) return;
    
    let img = container.querySelector(`#detectedStream-${index}`);
    const src = `${API_BASE}/video_feed?cam=${index}`;
    
    if (!img) {
        container.innerHTML = '';
        img = document.createElement('img');
        img.id = `detectedStream-${index}`;
        img.src = src;
        img.alt = `stream-${index}`;
        img.style.width = '100%';
        img.style.height = '100%';
        img.style.objectFit = 'cover';
        container.appendChild(img);
    } else {
        img.src = src;
    }
}

function attachStreamToAll() {
    const cameras = @json($cameras->pluck('camera_index'));
    cameras.forEach(index => playStream(index));
}

function snapshot(index) {
    const url = `${API_BASE}/snapshot?cam=${index}&_=${Date.now()}`;
    window.open(url, '_blank');
}

// SSE for real-time events
(function startSSE() {
    try {
        const sseUrl = `${API_BASE}/events/stream`;
        const evt = new EventSource(sseUrl);
        
        evt.onmessage = function(e) {
            try {
                const data = JSON.parse(e.data);
                console.log('New event received:', data);
                
                // Show modal
                openReportModal({
                    id: data.id,
                    camera: data.camera_name,
                    location: data.location,
                    datetime: data.ts,
                    type: data.event,
                    persons: data.n_persons,
                    description: JSON.stringify(data.details || {}),
                    image_url: data.image_url
                });
                
                // Play sound alert
                playAlertSound();
                
            } catch (err) {
                console.error('SSE parse error', err);
            }
        };
        
        evt.onerror = function(err) {
            console.error('SSE error', err);
            evt.close();
            setTimeout(startSSE, 5000);
        };
    } catch (e) {
        console.error('SSE start failed', e);
    }
})();

function openReportModal(data) {
    const modal = document.getElementById('reportModal');
    document.getElementById('modalTitle').textContent = `Event: ${data.type || ''}`;
    document.getElementById('modalEvent').textContent = data.type || '';
    document.getElementById('modalCamera').textContent = data.camera || '';
    document.getElementById('modalLocation').textContent = data.location || '';
    document.getElementById('modalTime').textContent = data.datetime || '';
    document.getElementById('modalPersons').textContent = data.persons || '';
    document.getElementById('modalDetails').textContent = data.description || '';
    
    const wrap = document.getElementById('modalImageWrap');
    wrap.innerHTML = '';
    if (data.image_url) {
        const img = document.createElement('img');
        img.src = data.image_url;
        img.style.maxWidth = '100%';
        img.style.borderRadius = '6px';
        img.style.marginTop = '10px';
        wrap.appendChild(img);
    }
    
    modal.setAttribute('aria-hidden', 'false');
}

function closeReportModal() {
    document.getElementById('reportModal').setAttribute('aria-hidden', 'true');
}

function playAlertSound() {
    // Create a simple beep sound
    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
    const oscillator = audioContext.createOscillator();
    const gainNode = audioContext.createGain();
    
    oscillator.connect(gainNode);
    gainNode.connect(audioContext.destination);
    
    oscillator.frequency.value = 800;
    oscillator.type = 'sine';
    
    gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
    
    oscillator.start(audioContext.currentTime);
    oscillator.stop(audioContext.currentTime + 0.5);
}
</script>
@endpush