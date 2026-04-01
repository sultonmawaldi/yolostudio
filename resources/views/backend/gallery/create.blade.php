@extends('adminlte::page')

@section('title', 'Tambah Gallery')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">

            {{-- Judul --}}
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold">
                    <i class="fas fa-images text-primary me-2"></i> Tambah Gallery
                </h1>
            </div>

            {{-- Breadcrumb --}}
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">
                            <i class="fas fa-home"></i> Beranda
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('gallery.index') }}">Gallery</a>
                    </li>
                    <li class="breadcrumb-item active">Tambah</li>
                </ol>
            </div>

        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <form action="{{ route('gallery.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">

                {{-- KONTEN KIRI --}}
                <div class="col-md-8">

                    {{-- INFORMASI GALLERY --}}
                    <div class="card card-light">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Gallery</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="card-body">

                            {{-- JUDUL --}}
                            <div class="form-group">
                                <label>Judul</label>
                                <input type="text" name="title"
                                    class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}"
                                    placeholder="Masukkan judul gallery">

                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- SERVICE (PENGGANTI CATEGORY) --}}
                            <div class="form-group">
                                <label>Service</label>
                                <select name="service_id" class="form-control @error('service_id') is-invalid @enderror">

                                    <option value="">-- Pilih Service --</option>

                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}"
                                            {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                            {{ $service->title }}
                                        </option>
                                    @endforeach

                                </select>

                                @error('service_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- DESKRIPSI --}}
                            <div class="form-group">
                                <label>Deskripsi</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3"
                                    placeholder="Tambahkan deskripsi (optional)">{{ old('description') }}</textarea>

                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- GAMBAR --}}
                            <div class="form-group">
                                <label>Upload Gambar</label>
                                <input type="file" name="image"
                                    class="form-control-file @error('image') is-invalid @enderror">

                                @error('image')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- PREVIEW --}}
                            <div class="form-group">
                                <label>Preview</label>
                                <div>
                                    <img id="previewImage" src="#"
                                        style="display:none; max-height:120px; border-radius:10px; border:1px solid #ddd;">
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                {{-- SIDEBAR --}}
                <div class="col-md-4">
                    <div class="sticky-top">

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

                                {{-- STATUS --}}
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="1" {{ old('status', 1) ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Nonaktif</option>
                                    </select>
                                </div>

                                {{-- BUTTON --}}
                                <div class="form-group mt-4 d-flex justify-content-end">
                                    <a href="{{ route('gallery.index') }}" class="btn btn-secondary mr-2">
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
        const inputImage = document.querySelector('input[name="image"]');
        const preview = document.getElementById('previewImage');

        inputImage.addEventListener('change', function(e) {
            const file = e.target.files[0];

            if (file) {
                preview.src = URL.createObjectURL(file);
                preview.style.display = 'block';
            }
        });

        // SweetAlert Error (samakan addon)
        @if ($errors->any())
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
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
