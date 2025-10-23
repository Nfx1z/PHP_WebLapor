@extends('layouts.app')

@section('title', 'Data Pelanggaran - Ssyar\'S-Intel')

@section('content')
<div class="main-content">
    <div class="page-header">
        <h2>Data Pelanggaran</h2>
    </div>

    <div class="reports-section">
        <div class="reports-header">
            <h3>Daftar Pelanggaran</h3>
            <div class="reports-info">
                Total pelanggaran: <strong>{{ $events->total() }}</strong> | Ditampilkan: <strong>{{ $events->count() }}</strong>
            </div>
        </div>

        <div class="filter-section">
            <span style="margin-right: 10px; font-weight: 600;">Filter:</span>
            <form method="GET" action="{{ route('events.index') }}" style="display: flex; gap: 10px; flex-wrap: wrap; flex: 1;">
                <select name="camera_id" class="filter-btn" style="border: 2px solid #d4af37; padding: 8px 15px;">
                    <option value="">Semua Kamera</option>
                    @foreach($cameras as $cam)
                        <option value="{{ $cam->id }}" {{ request('camera_id') == $cam->id ? 'selected' : '' }}>
                            {{ $cam->name }}
                        </option>
                    @endforeach
                </select>

                <select name="event_type" class="filter-btn" style="border: 2px solid #d4af37; padding: 8px 15px;">
                    <option value="">Semua Jenis</option>
                    <option value="LAPOR" {{ request('event_type') == 'LAPOR' ? 'selected' : '' }}>LAPOR</option>
                    <option value="KHALWAT" {{ request('event_type') == 'KHALWAT' ? 'selected' : '' }}>KHALWAT</option>
                    <option value="IKHTILAT" {{ request('event_type') == 'IKHTILAT' ? 'selected' : '' }}>IKHTILAT</option>
                </select>

                <input type="date" name="date_from" value="{{ request('date_from') }}" class="filter-btn" style="border: 2px solid #d4af37; padding: 8px 15px;">
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="filter-btn" style="border: 2px solid #d4af37; padding: 8px 15px;">

                <button type="submit" class="btn-add" style="padding: 8px 20px;">🔍 Filter</button>
                <a href="{{ route('events.index') }}" class="btn-export" style="padding: 8px 20px; text-decoration: none;">Reset</a>
            </form>

            <div class="action-buttons">
                <button class="btn-export" onclick="exportToExcel()">📥 Export Excel</button>
            </div>
        </div>

        <table class="reports-table">
            <thead>
                <tr>
                    <th>ID Report</th>
                    <th>Jenis Pelanggaran</th>
                    <th>Kamera</th>
                    <th>Lokasi</th>
                    <th>Waktu</th>
                    <th>Gambar</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                <tr>
                    <td>
                        <strong>{{ $event->event_id }}</strong><br>
                        <small>{{ $event->detected_at->format('d-m-Y H:i') }}</small>
                    </td>
                    <td>
                        <span class="violation-type">{{ $event->event_type }}</span><br>
                        <small>{{ $event->persons_count }} orang terdeteksi</small>
                    </td>
                    <td>{{ $event->camera->name }}</td>
                    <td>{{ $event->camera->location ?? '-' }}</td>
                    <td>{{ $event->detected_at->format('d-m-Y H:i:s') }}</td>
                    <td>
                        @if($event->image_path)
                            <img src="{{ asset('storage/' . $event->image_path) }}" 
                                 alt="Event" 
                                 style="width: 80px; height: 50px; object-fit: cover; border-radius: 4px; cursor: pointer;" 
                                 onclick="showImageModal('{{ asset('storage/' . $event->image_path) }}')">
                        @else
                            <span style="color: #999; font-size: 12px;">No image</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-icons">
                            <button class="icon-btn icon-view" onclick="viewEventDetail({{ $event->id }})">👁️</button>
                            <button class="icon-btn icon-delete" onclick="deleteEvent({{ $event->id }})">🗑️</button>
                        </div>
                        <form id="delete-event-{{ $event->id }}" action="{{ route('events.destroy', $event) }}" method="POST" style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px;">
                        Tidak ada data pelanggaran
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($events->hasPages())
        <div class="pagination">
            @if($events->onFirstPage())
                <button class="page-btn" disabled>◀</button>
            @else
                <a href="{{ $events->previousPageUrl() }}" class="page-btn">◀</a>
            @endif

            @for($i = 1; $i <= $events->lastPage(); $i++)
                <a href="{{ $events->url($i) }}" class="page-btn {{ $events->currentPage() == $i ? 'active' : '' }}">{{ $i }}</a>
            @endfor

            @if($events->hasMorePages())
                <a href="{{ $events->nextPageUrl() }}" class="page-btn">▶</a>
            @else
                <button class="page-btn" disabled>▶</button>
            @endif
        </div>
        @endif
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="modal" onclick="closeImageModal()">
    <div class="modal-content" style="max-width: 90%; max-height: 90vh; padding: 0;" onclick="event.stopPropagation()">
        <button class="close-modal" onclick="closeImageModal()" style="position: absolute; top: 10px; right: 10px; z-index: 10;">×</button>
        <img id="modalImage" src="" alt="Event Image" style="width: 100%; height: auto; display: block; border-radius: 15px;">
    </div>
</div>

<!-- Event Detail Modal -->
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
function showImageModal(imageUrl) {
    document.getElementById('modalImage').src = imageUrl;
    document.getElementById('imageModal').classList.add('active');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.remove('active');
}

function viewEventDetail(eventId) {
    fetch(`${API_BASE}/api/events/${eventId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('eventDetailTitle').textContent = `Detail ${data.event_type}`;
            document.getElementById('eventDetailImage').src = data.image_url || 'data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 400 300\'%3E%3Crect fill=\'%23333\' width=\'400\' height=\'300\'/%3E%3Ctext x=\'200\' y=\'150\' text-anchor=\'middle\' fill=\'%23fff\' font-size=\'20\'%3ENo Image%3C/text%3E%3C/svg%3E';
            document.getElementById('eventDetailTimestamp').textContent = `📅 ${data.detected_at}`;
            
            let detailsHtml = JSON.parse(data.details || '{}');
            let detailsText = '';
            if (detailsHtml.persons) {
                detailsText = `Persons detected: ${JSON.stringify(detailsHtml.persons)}`;
            } else if (detailsHtml.pairs) {
                detailsText = `Pairs detected: ${JSON.stringify(detailsHtml.pairs)}`;
            } else if (detailsHtml.clusters) {
                detailsText = `Clusters detected: ${JSON.stringify(detailsHtml.clusters)}`;
            }
            
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
                <div class="detail-field">
                    <label>Detail Deteksi</label>
                    <div class="value">${detailsText || 'N/A'}</div>
                </div>
                <div class="detail-field">
                    <label>Telegram</label>
                    <div class="value">${data.telegram_sent ? '✅ Terkirim' : '❌ Belum terkirim'}</div>
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

function deleteEvent(eventId) {
    if (confirm('Apakah Anda yakin ingin menghapus data pelanggaran ini?')) {
        document.getElementById('delete-event-' + eventId).submit();
    }
}

function exportToExcel() {
    const params = new URLSearchParams(window.location.search);
    let csv = '\ufeff'; // UTF-8 BOM
    
    csv += 'ID Report,Jenis Pelanggaran,Kamera,Lokasi,Waktu,Jumlah Orang,Telegram\n';
    
    // Get all table rows
    const rows = document.querySelectorAll('.reports-table tbody tr');
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length > 1) {
            const id = cells[0].querySelector('strong')?.textContent || '';
            const type = cells[1].querySelector('.violation-type')?.textContent || '';
            const camera = cells[2].textContent;
            const location = cells[3].textContent;
            const time = cells[4].textContent;
            const persons = cells[1].querySelector('small')?.textContent || '';
            
            csv += `"${id}","${type}","${camera}","${location}","${time}","${persons}"\n`;
        }
    });
    
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    const filename = `pelanggaran_${new Date().toISOString().split('T')[0]}.csv`;
    
    link.setAttribute('href', url);
    link.setAttribute('download', filename);
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    alert(`Data berhasil di-export: ${filename}`);
}
</script>
@endpush