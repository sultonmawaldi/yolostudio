@extends('adminlte::page')

@section('title', 'Edit Gallery')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">

            {{-- Judul --}}
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold">
                    <i class="fas fa-images text-primary mr-2"></i>
                    Edit Gallery
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
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>

        </div>
    </div>
@stop


@section('content')
    <div class="container-fluid">

        <form action="{{ route('gallery.update', $gallery) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">

                {{-- FORM UTAMA --}}
                <div class="col-md-8">

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
                                    class="form-control @error('title') is-invalid @enderror"
                                    value="{{ old('title', $gallery->title) }}" placeholder="Masukkan judul gallery">

                                @error('title')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- SERVICE --}}
                            <div class="form-group">
                                <label>Service</label>

                                <select name="service_id" class="form-control @error('service_id') is-invalid @enderror">

                                    <option value="">-- Pilih Service --</option>

                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}"
                                            {{ old('service_id', $gallery->service_id) == $service->id ? 'selected' : '' }}>
                                            {{ $service->title }}
                                        </option>
                                    @endforeach

                                </select>

                                @error('service_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- DESKRIPSI --}}
                            <div class="form-group">
                                <label>Deskripsi</label>

                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3"
                                    placeholder="Tambahkan deskripsi (optional)">{{ old('description', $gallery->description) }}</textarea>

                                @error('description')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- GAMBAR SAAT INI --}}
                            @if ($gallery->image)
                                <div class="form-group">
                                    <label>Gambar Saat Ini</label>
                                    <div>
                                        <img src="{{ asset('uploads/gallery/' . $gallery->image) }}"
                                            style="max-height:120px; border-radius:10px; border:1px solid #ddd;">
                                    </div>
                                </div>
                            @endif

                            {{-- UPLOAD BARU --}}
                            <div class="form-group">
                                <label>Ganti Gambar</label>

                                <input type="file" name="image"
                                    class="form-control-file @error('image') is-invalid @enderror">

                                @error('image')
                                    <small class="text-danger d-block">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- PREVIEW BARU --}}
                            <div class="form-group">
                                <label>Preview Baru</label>
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
                                        <option value="1"
                                            {{ old('status', $gallery->status) == 1 ? 'selected' : '' }}>
                                            Aktif
                                        </option>
                                        <option value="0"
                                            {{ old('status', $gallery->status) == 0 ? 'selected' : '' }}>
                                            Nonaktif
                                        </option>
                                    </select>
                                </div>

                                {{-- BUTTON --}}
                                <div class="form-group mt-4 d-flex justify-content-end">

                                    <a href="{{ route('gallery.index') }}" class="btn btn-secondary mr-2">
                                        Batal
                                    </a>

                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-save mr-1"></i>
                                        Perbarui
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
        // Preview gambar baru
        const inputImage = document.querySelector('input[name="image"]');
        const preview = document.getElementById('previewImage');

        inputImage.addEventListener('change', function(e) {
            const file = e.target.files[0];

            if (file) {
                preview.src = URL.createObjectURL(file);
                preview.style.display = 'block';
            }
        });

        // Toast
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
