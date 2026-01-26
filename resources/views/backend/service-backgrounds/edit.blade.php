@extends('adminlte::page')

@section('title', 'Edit Service Background')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="fw-bold text-primary mb-0">
            <i class="fas fa-edit me-2"></i> Edit Service Background
        </h1>
        <a href="{{ route('service-backgrounds.index') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
@stop

@section('content')
    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-body p-4">
            <form action="{{ route('service-backgrounds.update', $background) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- SERVICE ID --}}
                <div class="mb-3">
                    <label class="fw-semibold">Service ID</label>
                    <input type="number" name="service_id" class="form-control rounded-pill"
                        value="{{ old('service_id', $background->service_id) }}" required>
                </div>

                {{-- NAMA --}}
                <div class="mb-3">
                    <label class="fw-semibold">Nama Background</label>
                    <input type="text" name="name" class="form-control rounded-pill"
                        value="{{ old('name', $background->name) }}" required>
                </div>

                {{-- VALUE --}}
                <div class="mb-3">
                    <label class="fw-semibold">Background Value</label>
                    <input type="color" name="value" id="bgValue" class="form-control form-control-color"
                        value="{{ old('value', $background->value) }}">
                </div>

                {{-- PREVIEW --}}
                <div class="mb-4">
                    <label class="fw-semibold">Preview</label>
                    <div id="previewBox"
                        style="width:100%;height:60px;border-radius:12px;border:1px solid #ddd;
                        background: {{ $background->value }};">
                    </div>
                </div>

                {{-- STATUS --}}
                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" name="is_active"
                        {{ $background->is_active ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold">Aktif</label>
                </div>

                {{-- BUTTON --}}
                <div class="text-end">
                    <button class="btn btn-gradient-primary rounded-pill px-4">
                        <i class="fas fa-save me-1"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
    <script>
        const bgInput = document.getElementById('bgValue');
        const preview = document.getElementById('previewBox');

        bgInput.addEventListener('input', () => {
            preview.style.background = bgInput.value;
        });
    </script>
@stop
