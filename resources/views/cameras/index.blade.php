@extends('layouts.app')

@section('title', 'Manajemen CCTV - Ssyar\'S-Intel')

@section('content')
<div class="main-content">
    <div class="page-header">
        <h2>Manajemen Kamera CCTV</h2>
    </div>

    <div class="reports-section">
        <div class="reports-header">
            <h3>Daftar Kamera</h3>
            <button class="btn-add" onclick="window.location='{{ route('cameras.create') }}'">+ Tambah Kamera</button>
        </div>

        <table class="reports-table">
            <thead>
                <tr>
                    <th>Index</th>
                    <th>Nama Kamera</th>
                    <th>Lokasi</th>
                    <th>RTSP URL</th>
                    <th>Status</th>
                    <th>Active</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cameras as $camera)
                <tr>
                    <td><strong>{{ $camera->camera_index }}</strong></td>
                    <td>{{ $camera->name }}</td>
                    <td>{{ $camera->location ?? '-' }}</td>
                    <td><code style="font-size: 11px;">{{ Str::limit($camera->rtsp_url, 40) }}</code></td>
                    <td>
                        <span class="cctv-status {{ $camera->status === 'online' ? '' : 'disconnected' }}">
                            {{ strtoupper($camera->status) }}
                        </span>
                    </td>
                    <td>
                        <label class="switch">
                            <input type="checkbox" 
                                   {{ $camera->is_active ? 'checked' : '' }} 
                                   onchange="toggleCamera({{ $camera->id }}, this)">
                            <span class="slider"></span>
                        </label>
                    </td>
                    <td>
                        <div class="action-icons">
                            <button class="icon-btn icon-edit" onclick="window.location='{{ route('cameras.edit', $camera) }}'">✏️</button>
                            <button class="icon-btn icon-delete" onclick="deleteCamera({{ $camera->id }})">🗑️</button>
                        </div>
                        <form id="delete-camera-{{ $camera->id }}" action="{{ route('cameras.destroy', $camera) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px;">
                        Belum ada kamera. <a href="{{ route('cameras.create') }}" style="color: #2d5016; font-weight: bold;">Tambah kamera pertama</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
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
    background-color: #4CAF50;
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
            alert(isActive ? 'Kamera diaktifkan' : 'Kamera dinonaktifkan');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            alert('Gagal mengubah status kamera');
            checkbox.checked = !isActive;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
        checkbox.checked = !isActive;
    });
}

function deleteCamera(cameraId) {
    if (confirm('Apakah Anda yakin ingin menghapus kamera ini?')) {
        document.getElementById('delete-camera-' + cameraId).submit();
    }
}
</script>
@endpush