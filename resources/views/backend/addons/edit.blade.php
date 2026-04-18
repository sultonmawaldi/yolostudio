@extends('adminlte::page')

@section('title', 'Edit Addon')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">

            {{-- Judul --}}
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold">
                    <i class="fas fa-edit text-primary mr-2"></i>
                    Edit Addon
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
                        <a href="{{ route('addons.index') }}">Addon</a>
                    </li>
                    <li class="breadcrumb-item active">Edit Addon</li>
                </ol>
            </div>

        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <form method="POST" action="{{ route('addons.update', $addon->id) }}">
            @csrf
            @method('PUT') {{-- Gunakan PUT supaya route update Laravel dikenali --}}
            <div class="row">
                {{-- Form Utama --}}
                <div class="col-md-8">
                    <div class="card card-light">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Addon</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Tutup">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">

                            <div class="form-group">
                                <label for="code">Kode Addon</label>
                                <input type="text" name="code" id="code"
                                    class="form-control @error('code') is-invalid @enderror"
                                    placeholder="Masukkan kode addon" value="{{ old('code', $addon->code) }}">
                                @error('code')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="name">Nama Addon</label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    placeholder="Masukkan nama addon" value="{{ old('name', $addon->name) }}">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="price">Harga</label>
                                <input type="number" name="price" id="price"
                                    class="form-control @error('price') is-invalid @enderror"
                                    placeholder="Masukkan harga addon" value="{{ old('price', $addon->price) }}">
                                @error('price')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group">

                                <label for="unit">Unit</label>

                                <select name="unit" id="unit"
                                    class="form-select @error('unit') is-invalid @enderror" required>

                                    <option value="person"
                                        {{ old('unit', $addon->unit ?? '') == 'person' ? 'selected' : '' }}>
                                        Orang
                                    </option>

                                    <option value="minute"
                                        {{ old('unit', $addon->unit ?? '') == 'minute' ? 'selected' : '' }}>
                                        Menit
                                    </option>

                                    <option value="item"
                                        {{ old('unit', $addon->unit ?? '') == 'item' ? 'selected' : '' }}>
                                        Item
                                    </option>

                                </select>

                                @error('unit')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror

                            </div>

                            <div class="form-group">
                                <label for="max_qty">Jumlah Maksimal</label>
                                <input type="number" name="max_qty" id="max_qty"
                                    class="form-control @error('max_qty') is-invalid @enderror"
                                    placeholder="Masukkan jumlah maksimal" value="{{ old('max_qty', $addon->max_qty) }}">
                                @error('max_qty')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="sort_order">Urutan Sortir</label>
                                <input type="number" name="sort_order" id="sort_order"
                                    class="form-control @error('sort_order') is-invalid @enderror" placeholder="Contoh: 1"
                                    value="{{ old('sort_order', $addon->sort_order ?? 0) }}">

                                <small class="text-muted">
                                    Urutan tampil addon (semakin kecil tampil lebih dulu)
                                </small>

                                @error('sort_order')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>


                            <div class="form-group">
                                <label for="services">Layanan</label>

                                <select name="services[]" id="services" class="form-control select2" multiple
                                    style="width:100%">

                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}"
                                            {{ in_array($service->id, old('services', $addon->services->pluck('id')->toArray() ?? [])) ? 'selected' : '' }}>
                                            {{ $service->title }}
                                        </option>
                                    @endforeach

                                </select>

                                <small class="text-muted">
                                    Addon ini tersedia untuk layanan yang dipilih
                                </small>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="col-md-4">
                    <div class="sticky-top">
                        {{-- Detail Addon --}}
                        <div class="card card-primary sticky-bottom">
                            <div class="card-header">
                                <h3 class="card-title">Detail Addon</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Tutup">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body pb-0">
                                <div class="form-group">

                                    <label>Status</label>

                                    <select name="is_active" id="is_active" class="form-select" required>

                                        <option value="1"
                                            {{ old('is_active', $addon->is_active) == 1 ? 'selected' : '' }}>
                                            Aktif
                                        </option>

                                        <option value="0"
                                            {{ old('is_active', $addon->is_active) == 0 ? 'selected' : '' }}>
                                            Nonaktif
                                        </option>

                                    </select>

                                </div>

                                <div class="form-group mt-4 d-flex justify-content-end">
                                    <a href="{{ route('addons.index') }}" class="btn btn-secondary mr-2">
                                        Batal
                                    </a>

                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-save mr-1"></i> Perbarui
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Gambar Addon dihapus karena tidak perlu --}}
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop


@section('css')
    <style>
        /* MULTIPLE SELECT - TAG */
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #000 !important;
            border: none !important;
            color: #fff !important;
            margin-top: 0 !important;
        }

        /* REMOVE X */
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #fff !important;
            margin-right: 5px;
        }

        /* FIX FOCUS */
        .select2-container--default.select2-container--focus .select2-selection--multiple .select2-selection__choice {
            background-color: #000 !important;
            color: #fff !important;
        }

        /* MULTIPLE WRAPPER */
        .select2-container--default .select2-selection--multiple {
            position: relative;
            min-height: 38px;
            padding: 2px 28px 2px 4px;
            display: flex;
            align-items: center;
        }

        /* CHEVRON */
        .select2-container--default .select2-selection--multiple::after {
            content: "";
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 5px solid #6c757d;
        }

        /* MULTIPLE CONTENT */
        .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 4px;
            padding: 0;
            width: 100%;
            margin: 0;
        }

        /* ========================= */
        /* 🔥 FIX PLACEHOLDER CENTER */
        /* ========================= */

        /* container search inline */
        .select2-container--default .select2-selection--multiple .select2-search--inline {
            display: flex;
            align-items: center;
        }

        .select2-container--default .select2-selection--multiple .select2-search__field {
            margin: 0 !important;
            padding: 0 !important;
            cursor: pointer !important;
        }
    </style>
@stop
@section('js')
    <script>
        $(document).ready(function() {

            // 🌐 Global bahasa Indonesia
            $.fn.select2.defaults.set("language", {
                noResults: function() {
                    return "Data tidak ditemukan";
                },
                searching: function() {
                    return "Mencari...";
                }
            });

            function initSelect2(selector, options = {}) {
                $(selector).select2({
                    width: '100%',
                    dropdownParent: $(selector).parent(),
                    language: {
                        noResults: function() {
                            return "Data tidak ditemukan";
                        },
                        searching: function() {
                            return "Mencari...";
                        }
                    },
                    ...options
                });
            }

            // ✅ SERVICES
            initSelect2('#services', {
                placeholder: "Pilih Layanan"
            });

            // 🔍 Saat buka → jadi "Cari Layanan..."
            $('#services').on('select2:open', function() {
                let selected = $(this).val();

                setTimeout(() => {
                    if (!selected || selected.length === 0) {
                        $('.select2-container--open .select2-search__field')
                            .attr('placeholder', 'Cari Layanan...');
                    } else {
                        $('.select2-container--open .select2-search__field')
                            .attr('placeholder', '');
                    }
                }, 0);
            });

            // 🔄 Reset ke awal saat ditutup
            $('#services').on('select2:close', function() {
                if (!$(this).val() || $(this).val().length === 0) {
                    $(this).val(null).trigger('change');
                }
            });

        });
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
                    title: 'Terjadi Kesalahan',
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
