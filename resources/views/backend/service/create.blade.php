@extends('adminlte::page')

@section('title', 'Tambah Layanan')

@section('content_header')

    <div class="container-fluid">
        <div class="row mb-3 align-items-center">

            {{-- Judul --}}
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold">
                    <i class="fas fa-plus-circle text-primary mr-2"></i>
                    Tambah Layanan
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
                        <a href="/admin/service">Layanan</a>
                    </li>
                    <li class="breadcrumb-item active">Tambah Layanan</li>
                </ol>
            </div>

        </div>
    </div>

@stop

@section('content')



    <div>
        <form action="{{ route('service.store') }}" method="post" enctype="multipart/form-data">
            @csrf

            <div class="row">

                {{-- KONTEN KIRI --}}
                <div class="col-md-8">

                    {{-- INFORMASI LAYANAN --}}
                    <div class="card card-light">
                        <div class="card-header">
                            <h3 class="card-title">Tambah Layanan</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="card-body">

                            <div class="form-group">
                                <label for="title">Judul</label>

                                <input class="form-control @error('title') is-invalid @enderror" type="text"
                                    id="title" name="title" placeholder="Masukkan judul layanan"
                                    value="{{ old('title') }}">

                                <small class="text-muted d-block">
                                    Nama ini akan tampil sebagai judul layanan di website Anda.
                                </small>

                                @error('title')
                                    <small class="text-danger d-block mt-1">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="slug">Slug</label>

                                <input class="form-control bg-light @error('slug') is-invalid @enderror" type="text"
                                    id="slug" name="slug" placeholder="slug-layanan" value="{{ old('slug') }}">

                                <small class="text-muted d-block">
                                    URL unik untuk layanan.
                                </small>

                                @error('slug')
                                    <small class="text-danger d-block mt-1">
                                        {{ $message }}
                                    </small>
                                @enderror
                            </div>

                        </div>
                    </div>

                    {{-- HARGA --}}
                    <div class="card card-light">

                        <div class="card-header">
                            <h3 class="card-title">Harga</h3>
                            <small class="text-muted pl-2">Tanpa tanda mata uang dan tanpa spasi</small>

                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="card-body">

                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="mb-0" for="price">Harga</label>

                                        <input class="form-control @error('price') is-invalid @enderror" type="number"
                                            id="price" name="price" placeholder="Harga" value="{{ old('price') }}">

                                        <small class="text-muted d-block">
                                            Harga utama.
                                        </small>

                                        @error('price')
                                            <small class="text-danger d-block mt-1">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="mb-0" for="sale_price">Harga Promo</label>

                                        <input class="form-control @error('sale_price') is-invalid @enderror" type="number"
                                            id="sale_price" name="sale_price" placeholder="Harga"
                                            value="{{ old('sale_price') }}">

                                        <small class="text-muted d-block">
                                            Harga diskon.
                                        </small>

                                        @error('sale_price')
                                            <small class="text-danger d-block mt-1">
                                                {{ $message }}
                                            </small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">

                                        <label class="mb-0" for="dp_amount">Jumlah DP</label>

                                        <input class="form-control @error('dp_amount') is-invalid @enderror" type="number"
                                            name="dp_amount" id="dp_amount" min="0"
                                            placeholder="Down Payment (opsional)" value="{{ old('dp_amount') }}">

                                        <small class="text-muted d-block">
                                            Jumlah DP yang harus dibayar saat pemesanan.
                                        </small>

                                        @error('dp_amount')
                                            <small class="text-danger d-block mt-1">
                                                {{ $message }}
                                            </small>
                                        @enderror

                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">

                                        <label class="mb-0" for="reward_points">Poin Hadiah</label>

                                        <input class="form-control @error('reward_points') is-invalid @enderror"
                                            type="number" id="reward_points" name="reward_points" min="0"
                                            placeholder="Jumlah poin hadiah" value="{{ old('reward_points') }}">

                                        <small class="text-muted d-block">
                                            Poin yang didapat pelanggan setelah membeli layanan ini.
                                        </small>

                                        @error('reward_points')
                                            <small class="text-danger d-block mt-1">
                                                {{ $message }}
                                            </small>
                                        @enderror

                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>




                    {{-- JUMLAH ORANG --}}
                    <div class="card card-light">

                        <div class="card-header">
                            <h3 class="card-title">Jumlah Orang</h3>

                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="card-body">

                            <div class="row">

                                {{-- MINIMAL --}}
                                <div class="col-md-6">

                                    <div class="form-group">

                                        <label class="mb-0" for="min_people">Minimal Orang</label>

                                        <input class="form-control @error('min_people') is-invalid @enderror"
                                            type="number" name="min_people" id="min_people" min="1"
                                            placeholder="Jumlah minimal orang" value="{{ old('min_people') }}">

                                        <small class="text-muted d-block">
                                            Jumlah minimal orang untuk layanan ini.
                                        </small>

                                        @error('min_people')
                                            <small class="text-danger d-block mt-1">
                                                {{ $message }}
                                            </small>
                                        @enderror

                                    </div>

                                </div>


                                {{-- MAKSIMAL --}}
                                <div class="col-md-6">

                                    <div class="form-group">

                                        <label class="mb-0" for="max_people">Maksimal Orang</label>

                                        <input class="form-control @error('max_people') is-invalid @enderror"
                                            type="number" name="max_people" id="max_people" min="1"
                                            placeholder="Jumlah maksimal orang" value="{{ old('max_people') }}">

                                        <small class="text-muted d-block">
                                            Jumlah maksimal orang untuk layanan ini.
                                        </small>

                                        @error('max_people')
                                            <small class="text-danger d-block mt-1">
                                                {{ $message }}
                                            </small>
                                        @enderror

                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>


                    {{-- DESKRIPSI --}}
                    <div class="card card-light">

                        <div class="card-header">
                            <h3 class="card-title">Ringkasan</h3>
                            <small>&nbsp;&nbsp;Deskripsi singkat layanan</small>

                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="card-body">

                            <div class="form-group">

                                <textarea class="form-control" name="excerpt" cols="30" rows="5">{{ old('excerpt') }}</textarea>

                            </div>

                        </div>
                    </div>

                </div>


                {{-- SIDEBAR --}}
                <div class="col-md-4">

                    <div class="sticky-top">

                        {{-- DETAIL LAYANAN --}}
                        <div class="card card-primary sticky-bottom">

                            <div class="card-header">
                                <h3 class="card-title">Detail Layanan</h3>

                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>


                            <div class="card-body pb-0">

                                <div class="form-group select2-dark">

                                    <label for="category">Kategori</label>

                                    <select id="category" name="category_id"
                                        class="select2 form-control @error('category_id') is-invalid @enderror"
                                        data-placeholder="Pilih kategori" style="width:100%;">

                                        <option></option>

                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->title }}
                                            </option>
                                        @endforeach

                                    </select>

                                    <small class="text-muted d-block">
                                        Pilih kategori layanan.
                                    </small>

                                    @error('category_id')
                                        <small class="text-danger d-block mt-1">
                                            {{ $message }}
                                        </small>
                                    @enderror

                                </div>


                                <div class="form-group">

                                    <label>Status</label>

                                    <select required name="status" id="inputStatus" class="form-control custom-select">

                                        <option disabled value="">Pilih Status</option>

                                        <option value="1" selected>
                                            DITERBITKAN
                                        </option>

                                        <option value="0">
                                            DRAFT
                                        </option>

                                    </select>

                                </div>


                                <div class="form-group mt-4 d-flex justify-content-end">
                                    <a href="{{ route('service.index') }}" class="btn btn-secondary mr-2">
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
                                <h3 class="card-title">Gambar Layanan</h3>
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
                                    <input class="form-control mt-2 @error('image') is-invalid @enderror" name="image"
                                        accept="image/*" type="file" id="imgInp">

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

@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <style>
        /* summer note */
        .modal-header .close,
        .modal-header .mailbox-attachment-close {
            padding: 0rem;
            margin: 0 auto;
        }

        .modal-header {
            display: -ms-flexbox;
            display: block;
            -ms-flex-align: start;
            align-items: flex-start;
            -ms-flex-pack: justify;
            justify-content: space-between;
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
            border-top-left-radius: calc(0.3rem - 1px);
            border-top-right-radius: calc(0.3rem - 1px);
        }

        /* container tinggi sama seperti input */
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            display: flex;
            align-items: center;
            padding: 0 35px 0 10px;
            position: relative;
        }

        /* teks di dalam select */
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding: 0 !important;
            margin: 0;
            line-height: normal;
        }

        /* posisi panah (JANGAN DIUBAH DARI SINI) */
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
            right: 4px;
            top: 0;
            position: absolute;
            display: flex;
            align-items: center;
            /* biar center vertikal */
        }

        /* tombol clear (X) */
        .select2-container--default .select2-selection--single .select2-selection__clear {
            position: absolute;
            right: 28px;
            top: 50%;
            transform: translateY(-50%);
            margin: 0;
            font-size: 16px;
        }

        /* =========================
                   FIX PANAH TANPA UBAH POSISI
                ========================== */

        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            margin: 0 !important;
            position: static !important;
            /* ini penting biar ga geser */

            border-style: solid;
            border-width: 5px 4px 0 4px !important;
            border-color: #6c757d transparent transparent transparent !important;

            transform: none !important;
            transition: none !important;
        }

        /* saat open tetap sama */
        .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
            border-width: 5px 4px 0 4px !important;
            border-color: #6c757d transparent transparent transparent !important;
            transform: none !important;
        }
    </style>

@stop

@section('js')

    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

    <script>
        $(document).ready(function() {

            /* =========================
               SUMMERNOTE EDITOR
            ========================== */
            $('#summernote').summernote({
                height: 400,
                callbacks: {
                    onImageUpload: function(files) {
                        uploadImage(files[0]);
                    },
                    onMediaDelete: function(target) {
                        deleteImage(target[0].src);
                    }
                }
            });

            function uploadImage(file) {

                let formData = new FormData();
                formData.append('image', file);

                $.ajax({
                    url: '{{ route('summer.upload.image') }}',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {

                        let imageUrl = response.url;
                        $('#summernote').summernote('editor.insertImage', imageUrl);

                    },
                    error: function(error) {
                        console.error(error);
                    }
                });
            }

            function deleteImage(imageSrc) {

                $.ajax({
                    url: '{{ route('summer.delete.image') }}',
                    type: 'POST',
                    data: {
                        imageSrc: imageSrc
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

            }

            /* =========================
               PREVIEW IMAGE UPLOAD
            ========================== */

            document.getElementById("imgInp").onchange = function(evt) {

                const [file] = this.files;

                if (file) {
                    document.getElementById("blah").src = URL.createObjectURL(file);
                }

            };

            /* =========================
               AUTO SLUG
            ========================== */

            $('#title').on("keyup change", function() {

                let Text = $(this).val().trim();

                Text = Text.toLowerCase();
                Text = Text.replace(/[^a-zA-Z0-9]+/g, '-');

                $('#slug').val(Text);

            });

            /* =========================
               SELECT2
            ========================== */

            // Untuk tags tetap sama
            $('#tags').select2();

            $('#category').select2({
                placeholder: "Pilih kategori",
                width: '100%',
                dropdownParent: $('#category').parent(),
                allowClear: true,
                minimumResultsForSearch: Infinity
            });

            /* =========================
               AUTO HIDE ALERT
            ========================== */

            $(".alert").delay(6000).slideUp(300);

        });
    </script>


    {{-- =========================
   TOAST NOTIFICATION
========================= --}}

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


    {{-- VALIDATION ERROR --}}
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
