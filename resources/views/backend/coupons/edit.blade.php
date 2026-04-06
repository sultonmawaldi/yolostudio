@extends('adminlte::page')

@section('title', 'Edit Kupon')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">

            {{-- Judul --}}
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold">
                    <i class="fas fa-ticket-alt text-primary mr-2"></i>
                    Edit Kupon
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
                        <a href="{{ route('coupons.index') }}">Kupon</a>
                    </li>
                    <li class="breadcrumb-item active">Edit Kupon</li>
                </ol>
            </div>

        </div>
    </div>
@stop


@section('content')

    <div class="container-fluid">

        <form action="{{ route('coupons.update', $coupon) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">

                {{-- FORM UTAMA --}}
                <div class="col-md-8">

                    <div class="card card-light">

                        <div class="card-header">
                            <h3 class="card-title">Informasi Kupon</h3>

                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>

                        </div>

                        <div class="card-body">

                            {{-- Kode --}}
                            <div class="form-group">
                                <label>Kode Kupon</label>

                                <input type="text" name="code"
                                    class="form-control @error('code') is-invalid @enderror"
                                    value="{{ old('code', $coupon->code) }}" placeholder="Masukkan kode kupon">

                                @error('code')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>


                            {{-- Jenis --}}
                            <div class="form-group">
                                <label>Jenis Kupon</label>

                                <select name="type" class="form-control">

                                    <option value="fixed" {{ old('type', $coupon->type) == 'fixed' ? 'selected' : '' }}>
                                        Fixed (Rp)
                                    </option>

                                    <option value="percentage"
                                        {{ old('type', $coupon->type) == 'percentage' ? 'selected' : '' }}>
                                        Percentage (%)
                                    </option>

                                </select>
                            </div>


                            {{-- Nilai --}}
                            <div class="form-group">
                                <label>Nilai</label>

                                <input type="number" step="1" min="0" name="value" class="form-control"
                                    value="{{ old('value', $coupon->value) }}" placeholder="Masukkan nilai kupon">

                            </div>


                            {{-- Minimal transaksi --}}
                            <div class="form-group">
                                <label>Minimal Transaksi</label>

                                <input type="number" step="1" min="0" name="minimum_cart_value"
                                    class="form-control"
                                    value="{{ old('minimum_cart_value', $coupon->minimum_cart_value) }}">

                                <small class="text-muted">
                                    Kosongkan jika tidak ada minimal transaksi
                                </small>

                            </div>


                            {{-- Expiry --}}
                            <div class="form-group">
                                <label>Tanggal Kadaluarsa</label>

                                <input type="date" name="expiry_date" class="form-control"
                                    value="{{ old('expiry_date', $coupon->expiry_date ? $coupon->expiry_date->format('Y-m-d') : '') }}">
                            </div>


                            {{-- User --}}
                            <div class="form-group">
                                <label>User</label>

                                <select name="user_id" id="user_id"
                                    class="select2 form-control @error('user_id') is-invalid @enderror"
                                    data-placeholder="Pilih User" style="width:100%;">

                                    <option></option> {{-- WAJIB untuk allowClear --}}

                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ old('user_id', $coupon->user_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach

                                </select>

                                <small class="text-muted">
                                    Kosongkan jika berlaku untuk semua user
                                </small>

                            </div>

                            <div class="form-group">
                                <label>Service</label>

                                <select name="service_id[]" id="services" class="form-control select2" multiple
                                    style="width:100%">

                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}"
                                            {{ in_array($service->id, old('service_id', $coupon->services->pluck('id')->toArray())) ? 'selected' : '' }}>
                                            {{ $service->title }}
                                        </option>
                                    @endforeach

                                </select>

                                <small class="text-muted">
                                    Kosongkan jika berlaku untuk semua service
                                </small>
                            </div>

                        </div>
                    </div>
                </div>



                {{-- SIDEBAR --}}
                <div class="col-md-4">

                    <div class="sticky-top">

                        <div class="card card-primary">

                            <div class="card-header">
                                <h3 class="card-title">Detail Kupon</h3>

                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>

                            </div>

                            <div class="card-body pb-0">

                                {{-- Aktif --}}
                                <div class="form-group">
                                    <label>Status Aktif</label>

                                    <select name="active" class="form-control">

                                        <option value="1" {{ old('active', $coupon->active) == 1 ? 'selected' : '' }}>
                                            Aktif
                                        </option>

                                        <option value="0" {{ old('active', $coupon->active) == 0 ? 'selected' : '' }}>
                                            Nonaktif
                                        </option>

                                    </select>

                                </div>


                                {{-- Status --}}
                                <div class="form-group">
                                    <label>Status Kupon</label>

                                    <select name="status" class="form-control">

                                        <option value="unused"
                                            {{ old('status', $coupon->status) == 'unused' ? 'selected' : '' }}>
                                            Belum Digunakan
                                        </option>

                                        <option value="used"
                                            {{ old('status', $coupon->status) == 'used' ? 'selected' : '' }}>
                                            Sudah Digunakan
                                        </option>

                                    </select>

                                </div>


                                <div class="form-group mt-4 d-flex justify-content-end">

                                    <a href="{{ route('coupons.index') }}" class="btn btn-secondary mr-2">
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

@section('css')
    <style>
        /* MULTIPLE SELECT - ITEM TERPILIH (TAG) */
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #000 !important;
            border: none !important;
            color: #fff !important;

            margin-top: 0 !important;
            /* INI KUNCINYA */
        }

        /* ICON X (REMOVE) */
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #fff !important;
            margin-right: 5px;
        }

        /* FIX AGAR TIDAK KE-OVERRIDE ADMINLTE */
        .select2-container--default.select2-container--focus .select2-selection--multiple .select2-selection__choice {
            background-color: #000 !important;
            color: #fff !important;
        }

        /* pastikan container punya positioning */
        .select2-container--default .select2-selection--multiple {
            position: relative;
        }

        /* CHEVRON */
        .select2-container--default .select2-selection--multiple::after {
            content: "";
            position: absolute;
            right: 8px;
            /* samakan dengan single */
            top: 50%;
            transform: translateY(-50%) rotate(0deg);
            width: 0;
            height: 0;

            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 5px solid #6c757d;

            pointer-events: none;
            transition: transform 0.2s ease;
        }


        /* SINGLE SELECT (SAMA PERSIS SERVICE) */
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            display: flex;
            align-items: center;
            padding: 0 35px 0 10px;
            position: relative;
        }

        /* text */
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding: 0 !important;
            margin: 0;
            line-height: normal;
        }

        /* arrow */
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
            right: 8px;
        }

        /* tombol X (clear) */
        .select2-container--default .select2-selection--single .select2-selection__clear {
            position: absolute;
            right: 28px;
            top: 50%;
            transform: translateY(-50%);
            margin: 0;
            font-size: 16px;
        }

        /* MATIKAN ARROW BAWAAN SELECT2 */
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            display: none !important;
        }

        /* CHEVRON */
        .select2-container--default .select2-selection--single::after {
            content: "";
            position: absolute;
            right: 8px;
            /* samakan dengan single */
            top: 50%;
            transform: translateY(-50%) rotate(0deg);
            width: 0;
            height: 0;

            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 5px solid #6c757d;

            pointer-events: none;
            transition: transform 0.2s ease;
        }

        /* container utama */
        .select2-container--default .select2-selection--multiple {
            min-height: 38px;
            padding: 2px 28px 2px 4px;
            /* lebih seimbang (kanan diperkecil) */
            display: flex;
            align-items: center;
        }

        /* isi (tag + placeholder) */
        .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 4px;
            padding: 0;
        }

        /* input placeholder */
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

            // 🔧 Helper biar konsisten
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

            // ✅ USER (single select - sama create)
            initSelect2('#user_id', {
                placeholder: "Pilih User",
                allowClear: true
            });

            // Set placeholder di kolom search saat dropdown user dibuka
            $('#user_id').on('select2:open', function() {
                // Target input search yang ada di dropdown
                $('.select2-container--open .select2-search__field')
                    .attr('placeholder', 'Cari User...');
            });

            // ✅ SERVICE (multiple - sama create)
            initSelect2('#services', {
                placeholder: "Pilih Service"
            });

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
