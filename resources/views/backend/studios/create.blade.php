@extends('adminlte::page')

@section('title', 'Tambah Studio')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">

            {{-- Judul --}}
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold">
                    <i class="fas fa-camera text-primary me-2"></i> Tambah Studio
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
                        <a href="{{ route('studio.index') }}">Studio</a>
                    </li>
                    <li class="breadcrumb-item active">Tambah Studio</li>
                </ol>
            </div>

        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <form action="{{ route('studio.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">

                {{-- KONTEN KIRI --}}
                <div class="col-md-8">

                    <div class="card card-light">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Studio</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="card-body">

                            {{-- NAMA --}}
                            <div class="form-group">
                                <label>Nama Studio</label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    placeholder="Masukkan nama studio">

                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- TELEPON --}}
                            <div class="form-group">
                                <label>Telepon</label>

                                <div class="input-group">
                                    <span class="input-group-text d-flex align-items-center"
                                        style="border-right: 0; border-radius: .25rem 0 0 .25rem;">
                                        <img src="https://flagcdn.com/w20/id.png" style="width:20px; margin-right:6px;">
                                        +62
                                    </span>

                                    <input type="tel" name="phone" id="phone"
                                        class="form-control @error('phone') is-invalid @enderror" placeholder="81234567890"
                                        value="{{ old('phone') }}" inputmode="numeric" maxlength="13"
                                        style="border-left: 0; border-radius: 0 .25rem .25rem 0;">
                                </div>

                                @error('phone')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- KOTA --}}
                            <div class="form-group">
                                <label>Kota</label>
                                <input type="text" name="city"
                                    class="form-control @error('city') is-invalid @enderror" value="{{ old('city') }}"
                                    placeholder="Masukkan kota">

                                @error('city')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- ALAMAT --}}
                            <div class="form-group">
                                <label>Alamat</label>
                                <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3"
                                    placeholder="Masukkan alamat lengkap">{{ old('address') }}</textarea>

                                @error('address')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- GOOGLE MAPS --}}
                            <div class="form-group">
                                <label>Link Google Maps</label>
                                <input type="url" name="google_maps"
                                    class="form-control @error('google_maps') is-invalid @enderror"
                                    placeholder="https://maps.google.com/..." value="{{ old('google_maps') }}">

                                @error('google_maps')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- GAMBAR --}}
                            <div class="form-group">
                                <label>Upload Gambar</label>
                                <input type="file" name="image"
                                    class="form-control-file @error('image') is-invalid @enderror">

                                @error('image')
                                    <small class="text-danger d-block">{{ $message }}</small>
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
                                <h3 class="card-title">Detail Studio</h3>
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
                                        <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>
                                            Aktif
                                        </option>
                                        <option value="0" {{ old('status', 1) == 0 ? 'selected' : '' }}>
                                            Nonaktif
                                        </option>
                                    </select>
                                </div>

                                {{-- BUTTON --}}
                                <div class="form-group mt-4 d-flex justify-content-end">
                                    <a href="{{ route('studio.index') }}" class="btn btn-secondary mr-2">
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
        // Preview gambar
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
    <script>
        document.getElementById('phone').addEventListener('input', function() {
            let val = this.value;

            // hanya angka
            val = val.replace(/[^0-9]/g, '');

            // ❌ kalau diawali 0 → hapus
            if (val.startsWith('0')) {
                val = val.substring(1);
            }

            this.value = val;
        });
    </script>
@stop
