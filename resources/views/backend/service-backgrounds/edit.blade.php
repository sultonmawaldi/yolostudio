@extends('adminlte::page')

@section('title', 'Edit Latar Layanan')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">

            {{-- Judul --}}
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold">
                    <i class="fas fa-edit text-primary me-2"></i> Edit Latar Layanan
                </h1>
            </div>

            {{-- Breadcrumb --}}
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('service-backgrounds.index') }}">Latar Layanan</a>
                    </li>
                    <li class="breadcrumb-item active">Edit Latar Layanan</li>
                </ol>
            </div>

        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <form action="{{ route('service-backgrounds.update', $background) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">

                {{-- KIRI --}}
                <div class="col-md-8">

                    <div class="card card-light">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Latar Layanan</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="card-body">

                            {{-- SERVICE --}}
                            <div class="form-group">
                                <label>Layanan</label>

                                <select name="service_id" class="form-select @error('service_id') is-invalid @enderror"
                                    required>

                                    <option value="">-- Pilih Layanan --</option>

                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}"
                                            {{ old('service_id', $background->service_id ?? '') == $service->id ? 'selected' : '' }}>
                                            {{ $service->title }}
                                        </option>
                                    @endforeach

                                </select>

                                @error('service_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- NAME --}}
                            <div class="form-group">
                                <label>Nama Latar</label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $background->name) }}" placeholder="Masukkan nama latar">

                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- VALUE --}}
                            <div class="form-group">
                                <label>Warna Latar (Kode Warna)</label>
                                <input type="color" name="value" id="bgValue"
                                    class="form-control form-control-color @error('value') is-invalid @enderror"
                                    value="{{ old('value', $background->value) }}">

                                @error('value')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- PREVIEW --}}
                            <div class="form-group">
                                <label>Preview</label>
                                <div id="previewBox"
                                    style="width:100%;height:60px;border-radius:12px;border:1px solid #ddd;
                                    background: {{ old('value', $background->value) }};">
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                {{-- KANAN --}}
                <div class="col-md-4">
                    <div class="sticky-top">

                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Detail Latar Layanan</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="card-body pb-0">

                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="is_active" class="form-select">
                                        <option value="1"
                                            {{ old('is_active', $background->is_active) == 1 ? 'selected' : '' }}>
                                            Aktif
                                        </option>
                                        <option value="0"
                                            {{ old('is_active', $background->is_active) == 0 ? 'selected' : '' }}>
                                            Nonaktif
                                        </option>
                                    </select>
                                </div>

                                <div class="form-group mt-4 d-flex justify-content-end">
                                    <a href="{{ route('service-backgrounds.index') }}" class="btn btn-secondary mr-2">
                                        Batal
                                    </a>

                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-save me-1"></i> Perbarui
                                    </button>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </form>
    </div>
@stop

@section('js')
    <script>
        // Preview color
        const bgInput = document.getElementById('bgValue');
        const preview = document.getElementById('previewBox');

        bgInput.addEventListener('input', () => {
            preview.style.background = bgInput.value;
        });

        // Toast success/error
        @if (session('success') || session('error'))
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });

            @if (session('success'))
                Toast.fire({
                    icon: 'success',
                    title: "{{ session('success') }}"
                });
            @endif

            @if (session('error'))
                Toast.fire({
                    icon: 'error',
                    title: "{{ session('error') }}"
                });
            @endif
        @endif

        // Validation error
        @if ($errors->any())
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    html: `<ul style="text-align:left;">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                    </ul>`,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#d33'
                });
            });
        @endif
    </script>
@stop
