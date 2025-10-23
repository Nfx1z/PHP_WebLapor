@extends('layouts.app')

@section('title', 'Events - YOLO Action Dashboard')

@section('content')
<div class="page-header">
    <h2>🚨 Event Logs</h2>
    <p>View all detected events with detailed information</p>
</div>

<section style="padding: 20px;">
    <!-- Filters -->
    <div style="background: white; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
        <form method="GET" action="{{ route('events.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: end;">
            
            <div>
                <label style="display: block; margin-bottom: 5px; color: #333; font-weight: 600; font-size: 14px;">Camera</label>
                <select name="camera_id" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                    <option value="">All Cameras</option>
                    @foreach($cameras as $camera)
                        <option value="{{ $camera->id }}" {{ request('camera_id') == $camera->id ? 'selected' : '' }}>
                            {{ $camera->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; color: #333; font-weight: 600; font-size: 14px;">Event Type</label>
                <select name="event_type" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                    <option value="">All Types</option>
                    <option value="LAPOR" {{ request('event_type') == 'LAPOR' ? 'selected' : '' }}>LAPOR</option>
                    <option value="KHALWAT" {{ request('event_type') == 'KHALWAT' ? 'selected' : '' }}>KHALWAT</option>
                    <option value="IKHTILAT" {{ request('event_type') == 'IKHTILAT' ? 'selected' : '' }}>IKHTILAT</option>
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; color: #333; font-weight: 600; font-size: 14px;">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; color: #333; font-weight: 600; font-size: 14px;">Date To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" style="flex: 1; padding: 8px 16px; background: #2e5d2e; color: white; border: none; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer;">
                    🔍 Filter
                </button>
                <a href="{{ route('events.index') }}" style="flex: 1; padding: 8px 16px; background: #6c757d; color: white; border: none; border-radius: 6px; font-size: 14px; font-weight: 600; text-align: center; text-decoration: none; display: block;">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Events Table -->
    <div style="background: white; border-radius: 8px; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #2e5d2e; color: white;">
                <tr>
                    <th style="padding: 12px; text-align: left;">Event ID</th>
                    <th style="padding: 12px; text-align: left;">Camera</th>
                    <th style="padding: 12px; text-align: left;">Location</th>
                    <th style="padding: 12px; text-align: left;">Event Type</th>
                    <th style="padding: 12px; text-align: left;">Persons</th>
                    <th style="padding: 12px; text-align: left;">Time</th>
                    <th style="padding: 12px; text-align: left;">Image</th>
                    <th style="padding: 12px; text-align: center;">Telegram</th>
                    <th style="padding: 12px; text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody id="eventsTableBody">
                @forelse($events as $event)
                <tr style="border-bottom: 1px solid #eee;" data-event-id="{{ $event->id }}">
                    <td style="padding: 12px;">
                        <code style="background: #f4f5f7; padding: 4px 8px; border-radius: 4px; font-size: 11px;">
                            {{ $event->event_id }}
                        </code>
                    </td>
                    <td style="padding: 12px; font-weight: 600;">{{ $event->camera->name }}</td>
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
                            <img src="{{ asset('storage/' . $event->image_path) }}" 
                                 alt="Event" 
                                 style="width: 80px; height: 50px; object-fit: cover; border-radius: 4px; cursor: pointer;" 
                                 onclick="showImageModal('{{ asset('storage/' . $event->image_path) }}', '{{ $event->event_type }}')">
                        @else
                            <span style="color: #999; font-size: 12px;">No image</span>
                        @endif
                    </td>
                    <td style="padding: 12px; text-align: center;">
                        @if($event->telegram_sent)
                            <span style="color: #28a745; font-size: 20px;" title="Sent">✅</span>
                        @else
                            <span style="color: #dc3545; font-size: 20px;" title="Not sent">❌</span>
                        @endif
                    </td>
                    <td style="padding: 12px; text-align: center;">
                        <button onclick="viewEventDetails({{ $event->id }})" 
                                style="padding: 6px 12px; background: #17a2b8; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer; margin-right: 5px;">
                            👁️ View
                        </button>
                        <button onclick="deleteEvent({{ $event->id }})" 
                                style="padding: 6px 12px; background: #dc3545; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                            🗑️
                        </button>
                        <form id="delete-event-{{ $event->id }}" action="{{ route('events.destroy', $event) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="padding: 40px; text-align: center; color: #999;">
                        <p style="font-size: 18px; margin-bottom: 10px;">🔍 No events found</p>
                        <p>Events will appear here when detected by the system</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($events->hasPages())
        <div style="margin-top: 20px; display: flex; justify-content: center;">
            {{ $events->links() }}
        </div>
    @endif
</section>

<!-- Image Modal -->
<div id="imageModal" class="modal" aria-hidden="true" onclick="closeImageModal()">
    <div class="modal-content" style="max-width: 90%; max-height: 90vh; padding: 0; overflow: auto;" onclick="event.stopPropagation()">
        <button class="close" onclick="closeImageModal()" style="position: sticky; top: 10px; right: 10px; z-index: 10;">×</button>
        <img id="modalImage" src="" alt="Event Image" style="width: 100%; height: auto; display: block;">
    </div>
</div>

<!-- Event Details Modal -->
<div id="detailsModal" class="modal" aria-hidden="true">
    <div class="modal-content">
        <button class="close" onclick="closeDetailsModal()">×</button>
        <h2 id="detailsTitle">Event Details</h2>
        <div class="modal-body" id="detailsBody">
            <!-- Content loaded via JavaScript -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showImageModal(imageUrl, eventType) {
    document.getElementById('modalImage').src = imageUrl;
    document.getElementById('imageModal').setAttribute('aria-hidden', 'false');
}

function closeImageModal() {
    document.getElementById('imageModal').setAttribute('aria-hidden', 'true');
}

function viewEventDetails(eventId) {
    // This would typically make an AJAX call to get event details
    // For now, we'll just show a placeholder
    const modal = document.getElementById('detailsModal');
    const body = document.getElementById('detailsBody');
    
    body.innerHTML = '<p>Loading event details...</p>';
    modal.setAttribute('aria-hidden', 'false');
    
    // In a real implementation, you would fetch event details via AJAX here
    setTimeout(() => {
        body.innerHTML = '<p>Event details would be displayed here.</p>';
    }, 500);
}

function closeDetailsModal() {
    document.getElementById('detailsModal').setAttribute('aria-hidden', 'true');
}

function deleteEvent(eventId) {
    if (confirm('Are you sure you want to delete this event?')) {
        document.getElementById('delete-event-' + eventId).submit();
    }
}

// Real-time event updates via SSE
(function startEventSSE() {
    const tbody = document.getElementById('eventsTableBody');
    if (!tbody || tbody.querySelector('td[colspan]')) return; // Don't start if table is empty
    
    try {
        const sseUrl = '{{ route('events.stream') }}';
        const evt = new EventSource(sseUrl);
        
        evt.onmessage = function(e) {
            try {
                const data = JSON.parse(e.data);
                console.log('New event received:', data);
                
                // Add new row to top of table
                addEventRow(data);
                
            } catch (err) {
                console.error('SSE parse error', err);
            }
        };
        
        evt.onerror = function(err) {
            console.error('SSE error', err);
            evt.close();
            setTimeout(startEventSSE, 5000);
        };
    } catch (e) {
        console.error('SSE start failed', e);
    }
})();

function addEventRow(data) {
    const tbody = document.getElementById('eventsTableBody');
    if (!tbody) return;
    
    // Remove "no events" message if exists
    const emptyRow = tbody.querySelector('td[colspan]');
    if (emptyRow) {
        emptyRow.parentElement.remove();
    }
    
    const row = document.createElement('tr');
    row.style.borderBottom = '1px solid #eee';
    row.style.animation = 'highlight 2s';
    row.setAttribute('data-event-id', data.id);
    
    const eventColor = data.event === 'LAPOR' ? '#dc3545' : (data.event === 'KHALWAT' ? '#ffc107' : '#17a2b8');
    
    row.innerHTML = `
        <td style="padding: 12px;">
            <code style="background: #f4f5f7; padding: 4px 8px; border-radius: 4px; font-size: 11px;">
                ${data.id}
            </code>
        </td>
        <td style="padding: 12px; font-weight: 600;">${data.camera_name}</td>
        <td style="padding: 12px;">${data.location || '-'}</td>
        <td style="padding: 12px;">
            <span style="padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: bold; background: ${eventColor}; color: white;">
                ${data.event}
            </span>
        </td>
        <td style="padding: 12px;">${data.n_persons}</td>
        <td style="padding: 12px;">${data.ts}</td>
        <td style="padding: 12px;">
            ${data.image_url ? `<img src="${data.image_url}" alt="Event" style="width: 80px; height: 50px; object-fit: cover; border-radius: 4px; cursor: pointer;" onclick="showImageModal('${data.image_url}', '${data.event}')">` : '<span style="color: #999; font-size: 12px;">No image</span>'}
        </td>
        <td style="padding: 12px; text-align: center;">
            <span style="color: #28a745; font-size: 20px;" title="Sent">✅</span>
        </td>
        <td style="padding: 12px; text-align: center;">
            <button onclick="viewEventDetails('${data.id}')" 
                    style="padding: 6px 12px; background: #17a2b8; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                👁️ View
            </button>
        </td>
    `;
    
    tbody.insertBefore(row, tbody.firstChild);
}
</script>

<style>
@keyframes highlight {
    0% { background-color: #fff3cd; }
    100% { background-color: white; }
}
</style>
@endpush