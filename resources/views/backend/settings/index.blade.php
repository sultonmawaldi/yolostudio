@extends('adminlte::page')

@section('title', 'Pengaturan Website')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold">
                    <i class="fas fa-cog text-primary mr-2"></i>
                    Pengaturan Website
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                    <li class="breadcrumb-item active">Pengaturan</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')

    <div class="container-fluid">
        <div class="row">

            {{-- SIDEBAR --}}
            <div class="col-md-3">

                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">

                        {{-- LIGHT LOGO --}}
                        <small class="text-muted d-block mb-2">Logo Mode Terang</small>

                        @if ($setting->logo)
                            <img class="img-fluid mb-3" style="max-height:80px"
                                src="{{ asset('uploads/images/logo/' . $setting->logo) }}">
                        @endif

                        <hr>

                        {{-- DARK LOGO --}}
                        <div class="bg-dark rounded p-3 mt-2">
                            <small class="text-white d-block mb-2">Logo Mode Gelap</small>

                            <img class="img-fluid" style="max-height:80px"
                                src="{{ asset('uploads/images/logo/' . ($setting->dark_logo ?? $setting->logo)) }}">
                        </div>

                        {{-- IDENTITAS --}}
                        <div class="mt-3">
                            <h5 class="font-weight-bold mb-1">{{ $setting->bname }}</h5>
                            <small class="text-muted">Identitas Website</small>
                        </div>

                    </div>
                </div>

            </div>

            {{-- MAIN --}}
            <div class="col-md-9">
                <div class="card shadow-sm border-0">
                    <form action="{{ route('setting.update', $setting->id) }}" method="post" enctype="multipart/form-data">
                        @csrf

                        {{-- TAB NAV --}}
                        <div class="card-header">
                            <ul class="nav nav-pills">
                                <li class="nav-item"><a class="nav-link active" href="#business"
                                        data-bs-toggle="tab">Informasi
                                        Bisnis</a></li>
                                <li class="nav-item"><a class="nav-link" href="#social" data-bs-toggle="tab">Media
                                        Sosial</a>
                                </li>
                                <li class="nav-item"><a class="nav-link" href="#header" data-bs-toggle="tab">Header</a></li>
                                <li class="nav-item"><a class="nav-link" href="#footer" data-bs-toggle="tab">Footer</a></li>
                                <li class="nav-item"><a class="nav-link" href="#seo" data-bs-toggle="tab">SEO</a></li>
                            </ul>
                        </div>

                        <div class="card-body">
                            <div class="tab-content">

                                {{-- BUSINESS --}}
                                <div class="active tab-pane" id="business">

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Nama Bisnis</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="bname" class="form-control"
                                                placeholder="Masukkan nama bisnis" value="{{ $setting->bname }}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Email</label>
                                        <div class="col-sm-9">
                                            <input type="email" name="email" class="form-control"
                                                placeholder="Masukkan email" value="{{ $setting->email }}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Mata Uang</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="currency" class="form-control"
                                                placeholder="Contoh: IDR" value="{{ $setting->currency }}">
                                            <small class="text-muted">Contoh: IDR, USD</small>
                                        </div>
                                    </div>

                                    {{-- TELEPON --}}
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Telepon</label>

                                        <div class="col-sm-9">
                                            <div class="input-group">

                                                {{-- Prefix bendera +62 --}}
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text d-flex align-items-center">
                                                        <img src="https://flagcdn.com/w20/id.png" alt="ID"
                                                            style="width:18px; margin-right:6px;">
                                                        +62
                                                    </span>
                                                </div>

                                                {{-- Input --}}
                                                <input type="text" name="phone" id="phone"
                                                    class="form-control @error('phone') is-invalid @enderror"
                                                    placeholder="81234567890"
                                                    value="{{ old('phone', isset($setting) ? ltrim($setting->phone, '+62') : '') }}"
                                                    inputmode="numeric">
                                            </div>

                                            @error('phone')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- WHATSAPP --}}
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">WhatsApp</label>

                                        <div class="col-sm-9">
                                            <div class="input-group">

                                                {{-- Prefix bendera +62 --}}
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text d-flex align-items-center">
                                                        <img src="https://flagcdn.com/w20/id.png" alt="ID"
                                                            style="width:18px; margin-right:6px;">
                                                        +62
                                                    </span>
                                                </div>

                                                {{-- Input --}}
                                                <input type="text" name="whatsapp" id="whatsapp"
                                                    class="form-control @error('whatsapp') is-invalid @enderror"
                                                    placeholder="81234567890"
                                                    value="{{ old('whatsapp', isset($setting) ? ltrim($setting->whatsapp, '+62') : '') }}"
                                                    inputmode="numeric">
                                            </div>

                                            <small class="text-muted">
                                                Nomor WhatsApp aktif (tanpa 0 di depan)
                                            </small>

                                            @error('whatsapp')
                                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Logo Website</label>
                                        <div class="col-sm-9">
                                            <input type="file" name="logo" class="form-control">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Logo Dark Mode</label>
                                        <div class="col-sm-9">
                                            <input type="file" name="dark_logo" class="form-control">
                                            <small class="text-muted">Gunakan logo putih / transparan untuk mode
                                                gelap</small>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Alamat</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="address" class="form-control"
                                                placeholder="Masukkan alamat" value="{{ $setting->address }}">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Google Maps</label>
                                        <div class="col-sm-9">
                                            <textarea name="map" class="form-control" rows="5" placeholder="Tempelkan embed Google Maps di sini...">{{ $setting->map }}</textarea>
                                        </div>
                                    </div>

                                </div>

                                {{-- SOCIAL --}}
                                <div class="tab-pane" id="social">
                                    @foreach (['facebook', 'instagram', 'x', 'tiktok', 'linkedin', 'youtube'] as $s)
                                        <div class="form-group row">
                                            <label
                                                class="col-sm-3 col-form-label text-capitalize">{{ $s }}</label>
                                            <div class="col-sm-9">
                                                <input type="text" name="social[{{ $s }}]"
                                                    class="form-control"
                                                    placeholder="https://{{ $s }}.com/username"
                                                    value="{{ $setting->social[$s] ?? '' }}">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- HEADER --}}
                                <div class="tab-pane" id="header">
                                    <textarea name="header" class="form-control" rows="10">{{ $setting->header }}</textarea>
                                    <small class="text-muted">Script Google Analytics / CSS</small>
                                </div>

                                {{-- FOOTER --}}
                                <div class="tab-pane" id="footer">
                                    <textarea name="footer" class="form-control" rows="10">{{ $setting->footer }}</textarea>
                                    <small class="text-muted">Script JS / kode tambahan</small>
                                </div>

                                {{-- SEO --}}
                                <div class="tab-pane" id="seo">
                                    <div class="form-group">
                                        <label>Judul Website</label>
                                        <input type="text" name="meta_title" class="form-control"
                                            placeholder="Judul website untuk SEO" value="{{ $setting->meta_title }}">
                                    </div>

                                    <div class="form-group">
                                        <label>Kata Kunci</label>
                                        <textarea name="meta_keywords" class="form-control" placeholder="keyword1, keyword2, keyword3">{{ $setting->meta_keywords }}</textarea>
                                    </div>

                                    <div class="form-group">
                                        <label>Deskripsi</label>
                                        <textarea name="meta_description" class="form-control" placeholder="Deskripsi singkat website untuk SEO">{{ $setting->meta_description }}</textarea>
                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- BUTTON --}}
                        <div class="card-footer text-right bg-white">
                            <button type="submit" class="btn btn-primary px-4 btn-confirm" data-type="update-setting">
                                <i class="fas fa-save mr-1"></i> Simpan Perubahan
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>

@stop

@push('css')
@endpush

@section('js')
    <script>
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.startsWith('0')) value = value.substring(1);
            e.target.value = value;
        });
    </script>

    <script>
        document.getElementById('whatsapp').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.startsWith('0')) value = value.substring(1);
            e.target.value = value;
        });
    </script>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });

        // SUCCESS
        @if (session('success'))
            Toast.fire({
                icon: 'success',
                title: "{{ session('success') }}"
            });
        @endif

        // ERROR VALIDATION
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                Toast.fire({
                    icon: 'error',
                    title: "{{ $error }}"
                });
            @endforeach
        @endif
    </script>
    <script>
        $(document).ready(function() {

            $('.btn-confirm').on('click', function(e) {
                e.preventDefault();

                let form = $(this).closest('form');
                let type = $(this).data('type');

                let config = {
                    title: 'Yakin?',
                    text: 'Perubahan akan disimpan!',
                    icon: 'warning',
                };

                // custom khusus settings
                if (type === 'update-setting') {
                    config.title = 'Simpan Pengaturan?';
                    config.text = 'Perubahan pengaturan website akan disimpan!';
                }

                Swal.fire({
                    ...config,
                    showCancelButton: true,
                    confirmButtonColor: '#007bff',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, simpan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });

            });

        });
    </script>
@stop
