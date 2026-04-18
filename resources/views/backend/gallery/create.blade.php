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
                    <li class="breadcrumb-item active">Tambah Gallery</li>
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
                            <div class="mb-3">
                                <label class="form-label">Layanan</label>

                                <select name="service_id" class="form-select @error('service_id') is-invalid @enderror">

                                    <option value="">-- Pilih Layanan --</option>

                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}"
                                            {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                            {{ $service->title }}
                                        </option>
                                    @endforeach

                                </select>

                                @error('service_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- DESKRIPSI --}}
                            <div class="form-group">
                                <label>Deskripsi</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3"
                                    placeholder="Tambahkan deskripsi (opsional)">{{ old('description') }}</textarea>

                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- GAMBAR --}}
                            <div class="mb-3">
                                <label class="form-label">Upload Gambar</label>

                                <div id="dropArea" class="drop-zone border rounded-4 p-4 text-center position-relative">

                                    <input type="file" name="image" id="imageInput"
                                        class="d-none @error('image') is-invalid @enderror">

                                    <div class="drop-content">
                                        <div class="mb-2">
                                            <i class="fa fa-cloud-upload-alt fa-2x text-primary"></i>
                                        </div>

                                        <h6 class="mb-1 fw-semibold">Seret & Lepas gambar di sini</h6>
                                        <small class="text-muted">atau klik untuk memilih file (PNG, JPG, JPEG)</small>
                                    </div>
                                </div>

                                @error('image')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- PREVIEW --}}
                            <div class="mb-3">
                                <label class="form-label">Preview</label>

                                <div>
                                    <img id="previewImage" class="img-thumbnail rounded-4 shadow-sm d-none"
                                        style="max-height: 180px;">
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
                                <h3 class="card-title">Detail Gallery</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="card-body pb-0">

                                {{-- STATUS --}}
                                <div class="mb-3">
                                    <label class="form-label">Status</label>

                                    <select name="status" class="form-select">
                                        <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>
                                            Aktif
                                        </option>
                                        <option value="0" {{ old('status', '1') == '0' ? 'selected' : '' }}>
                                            Nonaktif
                                        </option>
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

@section('css')
    <style>
        .drop-zone {
            background: #f8f9fa;
            cursor: pointer;
            transition: all 0.25s ease;
            border: 2px dashed #cfd4da;
        }

        .drop-zone:hover {
            background: #eef5ff;
            border-color: #0d6efd;
            transform: translateY(-2px);
        }

        .drop-zone.dragover {
            background: #e7f1ff;
            border-color: #0d6efd;
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.15);
        }

        .drop-content {
            pointer-events: none;
        }
    </style>
@stop

@section('js')
    <script>
        const dropArea = document.getElementById('dropArea');
        const inputImage = document.getElementById('imageInput');
        const preview = document.getElementById('previewImage');

        dropArea.addEventListener('click', () => {
            inputImage.click();
        });

        function showPreview(file) {
            if (!file) return;

            const url = URL.createObjectURL(file);
            preview.src = url;
            preview.classList.remove('d-none');
        }

        inputImage.addEventListener('change', function(e) {
            showPreview(e.target.files[0]);
        });

        dropArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            dropArea.classList.add('dragover');
        });

        dropArea.addEventListener('dragleave', function() {
            dropArea.classList.remove('dragover');
        });

        dropArea.addEventListener('drop', function(e) {
            e.preventDefault();
            dropArea.classList.remove('dragover');

            const file = e.dataTransfer.files[0];

            if (file) {
                inputImage.files = e.dataTransfer.files;
                showPreview(file);
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
