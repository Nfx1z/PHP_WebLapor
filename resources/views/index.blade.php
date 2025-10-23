@extends('layouts.app')

@section('title', 'Cameras - YOLO Action Dashboard')

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2>📹 Camera Management</h2>
            <p>Manage your CCTV cameras and RTSP streams</p>
        </div>
        <a href="{{ route('cameras.create') }}" style="padding: 10px 20px; background: #ffd700; color: #1a2e1a; text-decoration: none; border-radius: 6px; font-weight: bold;">
            ➕ Add New Camera
        </a>
    </div>
</div>

<section style="padding: 20px;">
    <div style="background: white; border-radius: 8px; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #2e5d2e; color: white;">
                <tr>
                    <th style="padding: 12px; text-align: left;">Index</th>
                    <th style="padding: 12px; text-align: left;">Name</th>
                    <th style="padding: 12px; text-align: left;">Location</th>
                    <th style="padding: 12px; text-align: left;">RTSP URL</th>
                    <th style="padding: 12px; text-align: left;">Status</th>
                    <th style="padding: 12px; text-align: left;">Last Seen</th>
                    <th style="padding: 12px; text-align: left;">Active</th>
                    <th style="padding: 12px; text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cameras as $camera)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px; font-weight: bold;">{{ $camera->camera_index }}</td>
                    <td style="padding: 12px;">{{ $camera->name }}</td>
                    <td style="padding: 12px;">{{ $camera->location ?? '-' }}</td>
                    <td style="padding: 12px;">
                        <code style="background: #f4f5f7; padding: 4px 8px; border-radius: 4px; font-size: 11px;">
                            {{ Str::limit($camera->rtsp_url, 40) }}
                        </code>
                    </td>
                    <td style="padding: 12px;">
                        <span style="padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: bold; 
                            background: {{ $camera->status === 'online' ? '#28a745' : ($camera->status === 'error' ? '#dc3545' : '#6c757d') }}; 
                            color: white;">
                            {{ strtoupper($camera->status) }}
                        </span>
                    </td>
                    <td style="padding: 12px;">
                        {{ $camera->last_seen ? $camera->last_seen->diffForHumans() : 'Never' }}
                    </td>
                    <td style="padding: 12px;">
                        <label class="switch">
                            <input type="checkbox" 
                                   {{ $camera->is_active ? 'checked' : '' }} 
                                   onchange="toggleCamera({{ $camera->id }}, this)"
                                   data-camera-id="{{ $camera->id }}">
                            <span class="slider"></span>
                        </label>
                    </td>
                    <td style="padding: 12px; text-align: center;">
                        <a href="{{ route('cameras.edit', $camera) }}" 
                           style="display: inline-block; padding: 6px 12px; background: #ffc107; color: #1a2e1a; text-decoration: none; border-radius: 4px; font-size: 12px; margin-right: 5px;">
                            ✏️ Edit
                        </a>
                        <button onclick="deleteCamera({{ $camera->id }})" 
                                style="padding: 6px 12px; background: #dc3545; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                            🗑️ Delete
                        </button>
                        <form id="delete-form-{{ $camera->id }}" action="{{ route('cameras.destroy', $camera) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="padding: 40px; text-align: center; color: #999;">
                        <p style="font-size: 18px; margin-bottom: 10px;">📹 No cameras configured yet</p>
                        <p>Add your first camera to start monitoring</p>
                        <a href="{{ route('cameras.create') }}" style="display: inline-block; margin-top: 15px; padding: 10px 20px; background: #ffd700; color: #1a2e1a; text-decoration: none; border-radius: 6px; font-weight: bold;">
                            Add Camera
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<style>
/* Toggle Switch Styles */
.switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 24px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: #28a745;
}

input:checked + .slider:before {
    transform: translateX(26px);
}
</style>
@endsection

@push('scripts')
<script>
function toggleCamera(cameraId, checkbox) {
    const isActive = checkbox.checked;
    
    fetch(`/cameras/${cameraId}/toggle`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ is_active: isActive })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Camera toggled:', data);
            // Reload page to update status
            setTimeout(() => window.location.reload(), 1000);
        } else {
            alert('Failed to toggle camera');
            checkbox.checked = !isActive;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error toggling camera');
        checkbox.checked = !isActive;
    });
}

function deleteCamera(cameraId) {
    if (confirm('Are you sure you want to delete this camera? This will also delete all associated events.')) {
        document.getElementById('delete-form-' + cameraId).submit();
    }
}
</script>
@endpush