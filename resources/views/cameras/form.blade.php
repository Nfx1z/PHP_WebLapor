@extends('layouts.app')

@section('title', isset($camera) ? 'Edit Kamera' : 'Tambah Kamera')

@section('content')
<div class="main-content">
    <div class="page-header">
        <h2>{{ isset($camera) ? 'Edit' : 'Tambah' }} Kamera CCTV</h2>
    </div>

    <div class="reports-section" style="max-width: 800px; margin: 0 auto;">
        <form method="POST" action="{{ isset($camera) ? route('cameras.update', $camera) : route('cameras.store') }}">
            @csrf
            @if(isset($camera))
                @method('PUT')
            @endif

            <div class="modal-form single-column">
                <div class="form-field">
                    <label>Nama Kamera <span style="color: red;">*</span></label>
                    <input type="text" name="name" id="cctvName" 
                           value="{{ old('name', $camera->name ?? '') }}" 
                           placeholder="Contoh: Kamera 1 - Pintu Masuk" required>
                    @error('name')
                        <span class="error-message" style="display: block; color: #d32f2f; font-size: 0.85rem; margin-top: 5px;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-field">
                    <label>Lokasi</label>
                    <input type="text" name="location" id="cctvLocation" 
                           value="{{ old('location', $camera->location ?? '') }}" 
                           placeholder="Contoh: Pintu Masuk Utama">
                    @error('location')
                        <span class="error-message" style="display: block; color: #d32f2f; font-size: 0.85rem; margin-top: 5px;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-field">
                    <label>URL Stream CCTV <span style="color: red;">*</span></label>
                    <input type="text" name="rtsp_url" id="cctvUrl" 
                           value="{{ old('rtsp_url', $camera->rtsp_url ?? '') }}" 
                           placeholder="rtsp://... atau http://..." required>
                    <small style="color: #666; font-size: 0.85rem;">Masukkan URL RTSP atau HTTP stream</small>
                    @error('rtsp_url')
                        <span class="error-message" style="display: block; color: #d32f2f; font-size: 0.85rem; margin-top: 5px;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-field">
                    <label>Camera Index <span style="color: red;">*</span></label>
                    <input type="number" name="camera_index" id="cameraIndex" 
                           value="{{ old('camera_index', $camera->camera_index ?? $availableIndex ?? 0) }}" 
                           required min="0"
                           {{ isset($camera) ? 'readonly style=background:#f5f5f5;' : '' }}>
                    <small style="color: #666; font-size: 0.85rem;">Unique identifier (0, 1, 2, 3, etc.)</small>
                    @error('camera_index')
                        <span class="error-message" style="display: block; color: #d32f2f; font-size: 0.85rem;margin-top: 5px;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-field">
                    <label style="display: flex; align-items: center;">
                        <input type="checkbox" name="is_active" value="1" 
                               {{ old('is_active', $camera->is_active ?? true) ? 'checked' : '' }}
                               style="width: auto; margin-right: 10px;">
                        <span>Aktifkan kamera (mulai monitoring otomatis)</span>
                    </label>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="window.location='{{ route('cameras.index') }}'">Batal</button>
                    <button type="submit" class="btn-submit">{{ isset($camera) ? '💾 Update' : '➕ Tambah' }} Kamera</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection