@extends('layouts.app')

@section('title', 'Dashboard - Ssyar\'S-Intel')

@section('content')
<!-- CCTV Page -->
<div id="cctvPage" class="main-content">
    <div class="page-header">
        <h2>Pusat Pemantauan CCTV</h2>
    </div>

    <div class="cctv-grid" id="cctvGrid">
        @foreach($cameras as $camera)
        <div class="cctv-card">
            <div class="cctv-header">
                <span>{{ $camera->name }}</span>
                <span class="cctv-status {{ $camera->status === 'online' ? '' : 'disconnected' }}">
                    <span class="status-indicator"></span>
                    {{ strtoupper($camera->status) }}
                </span>
            </div>
            <div class="cctv-video" id="video-{{ $camera->camera_index }}">
                <div class="play-icon" onclick="playStream({{ $camera->camera_index }})">▶</div>
                <div class="stream-label">{{ $camera->location ?? 'Stream Connection' }}</div>
            </div>
            <div class="cctv-actions">
                <button class="btn btn-detail" onclick="window.location='{{ route('cameras.edit', $camera) }}'">Edit</button>
                <button class="btn btn-action" onclick="playStream({{ $camera->camera_index }})">Play</button>
            </div>
        </div>
        @endforeach

        <!-- Add CCTV Card -->
        <div class="add-cctv-card" onclick="window.location='{{ route('cameras.create') }}'">
            <div class="add-cctv-content">
                <div class="icon">📹</div>
                <h3>Tambah CCTV</h3>
                <p>Klik untuk menghubungkan kamera baru</p>
            </div>
        </div>
    </div>

    <div class="page-header">
        <h2>System Summary</h2>
    </div>
    
    <div class="summary-grid">
        <div class="summary-card">
            <h3 id="totalCameras">{{ $totalCameras }}</h3>
            <p>Total Cameras</p>
        </div>
        <div class="summary-card">
            <h3 id="activeCameras">{{ $onlineCameras }}</h3>
            <p>Live Stream Aktif</p>
        </div>
        <div class="summary-card">
            <h3>{{ $todayEvents }}</h3>
            <p>Pelanggaran Hari Ini</p>
        </div>
        <div class="summary-card">
            <h3>24/7</h3>
            <p>Status Monitoring</p>
        </div>
    </div>

    <!-- Recent Events -->
    <div class="page-header" style="margin-top: 40px;">
        <h2>Pelanggaran Terbaru</h2>
    </div>

    <div class="reports-section">
        <table class="reports-table">
            <thead>
                <tr>
                    <th>ID Report</th>
                    <th>Jenis Pelanggaran</th>
                    <th>Kamera</th>
                    <th>Lokasi</th>
                    <th>Waktu</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentEvents as $event)
                <tr>
                    <td>
                        <strong>{{ $event->event_id }}</strong><br>
                        <small>{{ $event->detected_at->format('d-m-Y H:i') }}</small>
                    </td>
                    <td>
                        <span class="violation-type">{{ $event->event_type }}</span><br>
                        <small>{{ $event->persons_count }} orang</small>
                    </td>
                    <td>{{ $event->camera->name }}</td>
                    <td>{{ $event->camera->location ?? '-' }}</td>
                    <td>{{ $event->detected_at->format('d-m-Y H:i:s') }}</td>
                    <td>
                        <div class="action-icons">
                            <button class="icon-btn icon-view" onclick="viewEvent({{ $event->id }})">👁️</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px;">Belum ada pelanggaran terdeteksi</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($recentEvents->count() > 0)
        <div style="text-align: center; margin-top: 20px;">
            <a href="{{ route('events.index') }}" class="btn-add">Lihat Semua Pelanggaran</a>
        </div>
        @endif
    </div>
</div>

<!-- Modal for Event Detail -->
<div id="eventDetailModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="eventDetailTitle">Detail Pelanggaran</h3>
            <button class="close-modal" onclick="closeEventDetail()">×</button>
        </div>
        <div class="modal-body">
            <div class="modal-form">
                <div class="modal-left">
                    <img id="eventDetailImage" src="" alt="Event Image" class="modal-image">
                    <div class="timestamp" id="eventDetailTimestamp"></div>
                </div>
                <div class="modal-right" id="eventDetailContent">
                    <!-- Content loaded via JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
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

function viewEvent(eventId) {
    fetch(`${API_BASE}/api/events/${eventId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('eventDetailTitle').textContent = `Detail ${data.event_type}`;
            document.getElementById('eventDetailImage').src = data.image_url || 'data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 400 300\'%3E%3Crect fill=\'%23333\' width=\'400\' height=\'300\'/%3E%3Ctext x=\'200\' y=\'150\' text-anchor=\'middle\' fill=\'%23fff\' font-size=\'20\'%3ENo Image%3C/text%3E%3C/svg%3E';
            document.getElementById('eventDetailTimestamp').textContent = `📅 ${data.detected_at}`;
            
            document.getElementById('eventDetailContent').innerHTML = `
                <div class="detail-field">
                    <label>ID Event</label>
                    <div class="value">${data.event_id}</div>
                </div>
                <div class="detail-field">
                    <label>Kamera</label>
                    <div class="value">${data.camera.name}</div>
                </div>
                <div class="detail-field">
                    <label>Lokasi</label>
                    <div class="value">${data.camera.location || '-'}</div>
                </div>
                <div class="detail-field">
                    <label>Jenis Pelanggaran</label>
                    <div class="value">${data.event_type}</div>
                </div>
                <div class="detail-field">
                    <label>Jumlah Orang</label>
                    <div class="value">${data.persons_count}</div>
                </div>
                <div class="detail-field">
                    <label>Waktu Deteksi</label>
                    <div class="value">${data.detected_at}</div>
                </div>
                <button class="btn-close-detail" onclick="closeEventDetail()">Tutup</button>
            `;
            
            document.getElementById('eventDetailModal').classList.add('active');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal memuat detail event');
        });
}

function closeEventDetail() {
    document.getElementById('eventDetailModal').classList.remove('active');
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
                
                // Show notification
                if (Notification.permission === 'granted') {
                    new Notification(`Pelanggaran ${data.event} Terdeteksi!`, {
                        body: `${data.camera_name} - ${data.location}`,
                        icon: data.image_url
                    });
                }
                
                // Reload page to show new event
                setTimeout(() => window.location.reload(), 2000);
                
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

// Request notification permission
if (Notification.permission === 'default') {
    Notification.requestPermission();
}
</script>
@endpush