@extends('adminlte::page')

@section('title', 'Tambah Kategori')

@section('content_header')

    <div class="container-fluid">
        <div class="row mb-3 align-items-center">

            {{-- Judul --}}
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold">
                    <i class="fas fa-plus-circle text-primary mr-2"></i>
                    Tambah Kategori
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
                        <a href="/admin/category">Kategori</a>
                    </li>
                    <li class="breadcrumb-item active">Tambah Kategori</li>
                </ol>
            </div>

        </div>
    </div>

@stop

@section('content')


    <div class="container-fluid">
        <div class="justify-content-between pb-5">

            <form role="form" method="post" action="{{ route('category.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-8">

                        {{-- KARTU UTAMA --}}
                        <div class="card card-light">
                            <div class="card-header">
                                <h3 class="card-title">Tambah Kategori</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Tutup">
                                        <i class="fas fa-minus" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="form-group">
                                    <label for="title">Judul</label>

                                    <input class="form-control @error('title') is-invalid @enderror" type="text"
                                        id="title" name="title" placeholder="Masukkan judul kategori"
                                        value="{{ old('title') }}">

                                    <small class="text-muted d-block">
                                        Nama ini akan tampil di website Anda
                                    </small>

                                    @error('title')
                                        <small class="text-danger d-block mt-1">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="slug" class="mb-0">Slug</label>

                                    <input style="background-color: rgb(220, 220, 220);"
                                        class="form-control @error('slug') is-invalid @enderror" type="text"
                                        id="slug" name="slug" placeholder="slug-kategori"
                                        value="{{ old('slug') }}">

                                    <small class="text-muted d-block">
                                        Slug adalah versi URL dari nama kategori
                                        Biasanya menggunakan huruf kecil dan tanda hubung (-)
                                    </small>

                                    @error('slug')
                                        <small class="text-danger d-block mt-1">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                </div>

                            </div>
                        </div>

                        {{-- DESKRIPSI --}}
                        <div class="card card-light">
                            <div class="card-header">
                                <h3 class="card-title">Deskripsi</h3>
                                <small>&nbsp;&nbsp; Deskripsi tidak selalu ditampilkan, tergantung tema yang
                                    digunakan</small>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Tutup">
                                        <i class="fas fa-minus" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="form-group">
                                    <textarea class="form-control @error('body') is-invalid @enderror" name="body" cols="30" rows="5">{{ old('body') }}</textarea>

                                    @error('body')
                                        <small class="text-danger d-block mt-1">
                                            {{ $message }}
                                        </small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- SIDEBAR --}}
                    <div class="col-md-4">
                        <div class="sticky-top">

                            {{-- DETAIL KATEGORI --}}
                            <div class="card card-primary sticky-bottom">
                                <div class="card-header">
                                    <h3 class="card-title">Detail Kategori</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                            title="Tutup">
                                            <i class="fas fa-minus" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="card-body pb-0">

                                    <div class="form-group">
                                        <label>Status</label>

                                        <select name="status" class="form-select" required>

                                            <option value="1"
                                                {{ isset($category) && $category->status == 1 ? 'selected' : '' }}>
                                                Aktif
                                            </option>

                                            <option value="0"
                                                {{ isset($category) && $category->status == 0 ? 'selected' : '' }}>
                                                Nonaktif
                                            </option>

                                        </select>
                                    </div>

                                    <div class="form-group mt-4 d-flex justify-content-end">
                                        <a href="{{ route('category.index') }}" class="btn btn-secondary mr-2">
                                            Batal
                                        </a>

                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i> Simpan & Terbitkan
                                        </button>
                                    </div>

                                </div>
                            </div>

                            {{-- GAMBAR --}}
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Gambar Kategori</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                            title="Tutup">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="card-body pt-0 pb-0 mt-4">

                                    <div class="form-group">

                                        <small class="text-red">
                                            Catatan : Ukuran ideal Lebar 1280px dan Tinggi 720px
                                        </small>

                                        {{-- Upload gambar --}}
                                        <input class="form-control mt-2 @error('image') is-invalid @enderror"
                                            name="image" accept="image/*" type="file" id="imgInp">

                                        @error('image')
                                            <small class="text-danger d-block mt-1">
                                                {{ $message }}
                                            </small>
                                        @enderror

                                        {{-- Preview gambar --}}
                                        <div class="text-center mt-3">

                                            <img class="img-fluid rounded shadow-sm"
                                                style="width:160px;border:1px solid #ddd;padding:4px" id="blah"
                                                src="{{ asset('uploads/images/no-image.jpg') }}" alt="Preview Gambar">

                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
            </form>
        </div>
    </div>

@stop

@section('css')

@stop

@section('js')

    <script>
        $('#title').on("change keyup paste click", function() {
            var Text = $(this).val().trim();
            Text = Text.toLowerCase();
            Text = Text.replace(/[^a-zA-Z0-9]+/g, '-');
            $('#slug').val(Text);
        });
    </script>

    {{-- show image --}}
    <script>
        imgInp.onchange = evt => {
            const [file] = imgInp.files
            if (file) {
                blah.src = URL.createObjectURL(file)
            }
        }
    </script>

    @if (session('success') || session('error'))
        <script>
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
        </script>
    @endif

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
                    html: `
            <ul style="text-align:left;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        `,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#d33'
                });
            });
        </script>
    @endif

@stop
