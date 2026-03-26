@extends('adminlte::page')

@section('title', 'Tambah Service Background')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">

            {{-- Judul --}}
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold">
                    <i class="fas fa-plus-circle text-primary me-2"></i> Tambah Service Background
                </h1>
            </div>

            {{-- Breadcrumb --}}
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i> Beranda</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('service-backgrounds.index') }}">Service Background</a>
                    </li>
                    <li class="breadcrumb-item active">Tambah</li>
                </ol>
            </div>

        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <form action="{{ route('service-backgrounds.store') }}" method="POST">
            @csrf
            <div class="row">

                {{-- KONTEN KIRI --}}
                <div class="col-md-8">

                    {{-- INFORMASI BACKGROUND --}}
                    <div class="card card-light">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Background</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="card-body">

                            {{-- SERVICE --}}
                            <div class="form-group">
                                <label>Pilih Service</label>
                                <select name="service_id" class="form-control" required>
                                    <option value="">-- Pilih Service --</option>

                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}"
                                            {{ old('service_id') == $service->id ? 'selected' : '' }}>

                                            {{ $service->title }} {{-- atau name --}}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            {{-- NAMA --}}
                            <div class="form-group">
                                <label>Nama Background</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                    required>
                            </div>

                            {{-- VALUE --}}
                            <div class="form-group">
                                <label>Background Value (Color)</label>
                                <input type="color" name="value" id="bgValue" class="form-control form-control-color"
                                    value="{{ old('value', '#ffffff') }}">
                            </div>

                            {{-- PREVIEW --}}
                            <div class="form-group">
                                <label>Preview</label>
                                <div id="previewBox"
                                    style="width:100%;height:60px;border-radius:12px;border:1px solid #ddd;background:#ffffff;">
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                {{-- SIDEBAR --}}
                <div class="col-md-4">
                    <div class="sticky-top">

                        {{-- DETAIL --}}
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Detail</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="card-body pb-0">

                                <div class="form-group">
                                    <label>Status Aktif</label>
                                    <select name="is_active" class="form-control">
                                        <option value="1" {{ old('is_active', 1) ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>Nonaktif
                                        </option>
                                    </select>
                                </div>

                                <div class="form-group mt-4 d-flex justify-content-end">
                                    <a href="{{ route('service-backgrounds.index') }}" class="btn btn-secondary mr-2">
                                        Batal
                                    </a>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Simpan
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
        // Preview color background
        const bgInput = document.getElementById('bgValue');
        const preview = document.getElementById('previewBox');

        bgInput.addEventListener('input', () => {
            preview.style.background = bgInput.value;
        });
    </script>
@stop
