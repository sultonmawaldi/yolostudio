@extends('adminlte::page')

@section('title', 'Edit Pengguna')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">

            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold">
                    <i class="fas fa-user-edit text-primary mr-2"></i>
                    Edit Pengguna
                </h1>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">
                            <i class="fas fa-home"></i> Beranda
                        </a>
                    </li>
                    <li class="breadcrumb-item active">Edit Pengguna</li>
                </ol>
            </div>

        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="justify-content-between pb-5">

            <form action="{{ route('user.update', $user->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">

                    {{-- KIRI --}}
                    <div class="col-md-8">

                        {{-- INFORMASI USER --}}
                        <div class="card card-light">
                            <div class="card-header">
                                <h3 class="card-title">Informasi Pengguna</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="form-group">
                                    <label>Nama</label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                        class="form-control @error('name') is-invalid @enderror"
                                        placeholder="Masukkan nama lengkap">
                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                        class="form-control @error('email') is-invalid @enderror"
                                        placeholder="Masukkan email">
                                    @error('email')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- PHONE --}}
                                <div class="form-group">
                                    <label>Nomor HP / WhatsApp</label>
                                    <div class="input-group">
                                        <span class="input-group-text d-flex align-items-center"
                                            style="border-right: 0; border-radius: .25rem 0 0 .25rem;">
                                            <img src="https://flagcdn.com/w20/id.png" style="width:20px; margin-right:6px;">
                                            +62
                                        </span>
                                        @php
                                            $phone = old('phone') ?? $user->phone;
                                            $phone = \Illuminate\Support\Str::replaceFirst('+62', '', $phone);
                                        @endphp

                                        <input type="tel" name="phone" id="phone"
                                            class="form-control @error('phone') is-invalid @enderror"
                                            placeholder="81234567890" value="{{ $phone }}" inputmode="numeric"
                                            maxlength="13" style="border-left: 0; border-radius: 0 .25rem .25rem 0;">
                                    </div>
                                    @error('phone')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Points</label>
                                    <input type="number" name="points" value="{{ old('points', $user->points) }}"
                                        class="form-control @error('points') is-invalid @enderror" min="0"
                                        placeholder="Masukkan jumlah points">

                                    @error('points')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Kata Sandi</label>
                                    <input type="password" name="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="Kosongkan jika tidak ingin diubah">
                                    @error('password')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Konfirmasi Kata Sandi</label>
                                    <input type="password" name="password_confirmation"
                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                        placeholder="Ulangi kata sandi baru">
                                    @error('password_confirmation')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- ROLE --}}
                                <div class="form-group">
                                    <label>Peran Pengguna</label>
                                    <select name="roles" id="roles"
                                        class="select2 form-control @error('roles') is-invalid @enderror"
                                        data-placeholder="Pilih Peran" style="width:100%;">
                                        <option></option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}"
                                                {{ old('roles', $userRole) == $role->name ? 'selected' : '' }}>
                                                {{ ucfirst($role->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                        </div>

                        {{-- EMPLOYEE SETTINGS --}}
                        <div id="employee" style="display: {{ $userRole == 'employee' ? 'block' : 'none' }};">
                            <div class="card card-light">
                                <div class="card-header">
                                    <h3 class="card-title">Pengaturan Karyawan</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="card-body">

                                    {{-- SERVICES --}}
                                    <div class="form-group">
                                        <label>Pilih Layanan</label>
                                        <select name="service[]" id="services" class="form-control select2" multiple
                                            style="width:100%">
                                            @foreach ($services as $service)
                                                <option value="{{ $service->id }}"
                                                    {{ in_array($service->id, old('service', $userServices)) ? 'selected' : '' }}>
                                                    {{ $service->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div id="service-details-container">

                                    </div>

                                    {{-- STUDIO --}}
                                    <div class="form-group">
                                        <label>Pilih Studio</label>

                                        <select name="studio_id" id="studio_id" class="form-control select2"
                                            style="width:100%" data-placeholder="Pilih Studio">

                                            <option></option>

                                            @foreach ($studios as $studio)
                                                <option value="{{ $studio->id }}"
                                                    {{ old('studio_id', optional($user->employee)->studio_id) == $studio->id ? 'selected' : '' }}>
                                                    {{ $studio->name }}
                                                </option>
                                            @endforeach

                                        </select>
                                    </div>

                                    {{-- HOLIDAYS --}}
                                    <div class="form-group">
                                        <label class="font-weight-bold">Hari Libur Karyawan</label>
                                        <div id="holiday-container">
                                            @if ($userHolidays->count() > 0)
                                                @foreach ($userHolidays as $holiday)
                                                    <div class="row mb-2 holiday-row align-items-center">
                                                        <div class="col-md-4">
                                                            <input type="date" name="holidays[date][]"
                                                                class="form-control" value="{{ $holiday->date }}">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="time" name="holidays[from_time][]"
                                                                class="form-control" value="{{ $holiday->from_time }}">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="time" name="holidays[to_time][]"
                                                                class="form-control" value="{{ $holiday->to_time }}">
                                                        </div>
                                                        <div class="col-md-2 text-right action-col"></div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="row mb-2 holiday-row align-items-center">
                                                    <div class="col-md-4">
                                                        <input type="date" name="holidays[date][]"
                                                            class="form-control">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="time" name="holidays[from_time][]"
                                                            class="form-control">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="time" name="holidays[to_time][]"
                                                            class="form-control">
                                                    </div>
                                                    <div class="col-md-2 text-right action-col"></div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- KANAN --}}
                    <div class="col-md-4">
                        <div class="sticky-top">
                            {{-- STATUS USER --}}
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Detail Pengguna</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body pb-0">

                                    <div class="form-group">
                                        <label>Status</label>
                                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                                            <option value="1"
                                                {{ old('status', $user->status) == '1' ? 'selected' : '' }}>Aktif</option>
                                            <option value="0"
                                                {{ old('status', $user->status) == '0' ? 'selected' : '' }}>Nonaktif
                                            </option>
                                        </select>
                                    </div>

                                    {{-- ACTION BUTTON --}}
                                    <div class="form-group mt-4 d-flex justify-content-end">
                                        <a href="{{ route('user.index') }}" class="btn btn-secondary mr-2">Batal</a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save mr-1"></i> Simpan
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- FOTO USER --}}
                            <div class="card card-primary mt-3">

                                <div class="card-header">
                                    <h3 class="card-title">Foto Pengguna</h3>

                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse"
                                            title="Tutup">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="card-body pt-0 pb-0 mt-4">
                                    <div class="form-group">

                                        <small class="text-danger d-block">
                                            Catatan : Disarankan foto persegi (1:1)
                                        </small>

                                        <label class="mt-2">Upload Foto Baru</label>
                                        <input class="form-control @error('image') is-invalid @enderror" name="image"
                                            accept="image/*" type="file" id="imgInp">

                                        @error('image')
                                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                                        @enderror

                                        {{-- Preview --}}
                                        <div class="text-center mt-3">

                                            @if ($user->image)
                                                <img class="img-fluid rounded-circle shadow-sm"
                                                    style="width:120px;height:120px;object-fit:cover;border:1px solid #ddd;padding:4px"
                                                    id="blah"
                                                    src="{{ asset('uploads/images/profile/' . $user->image) }}"
                                                    alt="Foto pengguna">

                                                {{-- HAPUS FOTO --}}
                                                <div class="mt-2">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input"
                                                            id="delete_image" name="delete_image">

                                                        <label class="custom-control-label text-danger"
                                                            for="delete_image">
                                                            Hapus foto ini
                                                        </label>
                                                    </div>
                                                </div>
                                            @else
                                                <img class="img-fluid rounded-circle shadow-sm"
                                                    style="width:120px;height:120px;object-fit:cover;border:1px solid #ddd;padding:4px"
                                                    id="blah" src="{{ asset('uploads/images/no-image.jpg') }}"
                                                    alt="Foto default">
                                            @endif

                                        </div>

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

        /* SINGLE */
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            display: flex;
            align-items: center;
            padding: 0 35px 0 10px;
            position: relative;
        }

        /* TEXT */
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding: 0 !important;
        }

        /* CLEAR BUTTON */
        .select2-container--default .select2-selection--single .select2-selection__clear {
            position: absolute;
            right: 28px;
            top: 50%;
            transform: translateY(-50%);
        }

        /* HIDE DEFAULT ARROW */
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            display: none !important;
        }

        /* CHEVRON SINGLE */
        .select2-container--default .select2-selection--single::after {
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

        .holiday-row {
            border-bottom: 1px dashed #eee;
            padding-bottom: 8px;
        }
    </style>
@stop

@section('js')

    <script>
        $(document).ready(function() {

            function renderButtons() {
                $('.holiday-row').each(function(index) {

                    let isLast = index === $('.holiday-row').length - 1;

                    let buttons = `
                <button type="button" class="btn btn-outline-danger btn-sm remove-holiday mr-1" title="Hapus">
                    <i class="fas fa-times"></i>
                </button>
            `;

                    if (isLast) {
                        buttons = `
                    <button type="button" class="btn btn-success btn-sm add-holiday mr-1" title="Tambah">
                        <i class="fas fa-plus"></i>
                    </button>
                ` + buttons;
                    }

                    $(this).find('.action-col').html(`
                <div class="btn-group">
                    ${buttons}
                </div>
            `);
                });
            }

            // ➕ TAMBAH
            $(document).on('click', '.add-holiday', function() {

                let html = `
        <div class="row mb-2 holiday-row align-items-center">

            <div class="col-md-4">
                <input type="date" name="holidays[date][]" class="form-control">
            </div>

            <div class="col-md-3">
                <input type="time" name="holidays[from_time][]" class="form-control">
            </div>

            <div class="col-md-3">
                <input type="time" name="holidays[to_time][]" class="form-control">
            </div>

            <div class="col-md-2 text-right action-col"></div>

        </div>
        `;

                $('#holiday-container').append(html);
                renderButtons();
            });

            // ❌ HAPUS
            $(document).on('click', '.remove-holiday', function() {

                $(this).closest('.holiday-row').remove();

                // kalau kosong → buat 1 row baru
                if ($('.holiday-row').length === 0) {
                    $('#holiday-container').append(`
                <div class="row mb-2 holiday-row align-items-center">

                    <div class="col-md-4">
                        <input type="date" name="holidays[date][]" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <input type="time" name="holidays[from_time][]" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <input type="time" name="holidays[to_time][]" class="form-control">
                    </div>

                    <div class="col-md-2 text-right action-col"></div>

                </div>
            `);
                }

                renderButtons();
            });

            // INIT
            renderButtons();

        });
    </script>


    <script>
        $(document).ready(function() {

            // 🌐 Global bahasa Indonesia (tetap)
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

                    // 🔥 WAJIB: paksa bahasa biar tidak balik ke English
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

            // ROLE
            initSelect2('#roles', {
                placeholder: "Pilih Peran",
                allowClear: true,
            });

            $('#roles').on('select2:open', function() {
                setTimeout(() => {
                    $('.select2-container--open .select2-search__field')
                        .attr('placeholder', 'Cari Peran...');
                }, 0);
            });

            // SERVICE
            initSelect2('#services', {
                placeholder: "Pilih Layanan"
            });

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
                }, 0); // 🔥 ini kunci biar language ga ke-reset
            });

            // 🔄 Reset ke awal saat ditutup
            $('#services').on('select2:close', function() {
                if (!$(this).val() || $(this).val().length === 0) {
                    $(this).val(null).trigger('change');
                }
            });

            initSelect2('#studio_id', {
                placeholder: "Pilih Studio",
                allowClear: true
            });

            $('#studio_id').on('select2:open', function() {
                $('.select2-container--open .select2-search__field')
                    .attr('placeholder', 'Cari Studio...');
            });

        });
    </script>


    <script>
        $(document).ready(function() {

            function toggleEmployee() {
                let role = $('select[name="roles"]').val();

                if (role === 'employee') {
                    $('#employee').slideDown(200);
                } else {
                    $('#employee').slideUp(200);
                }
            }

            toggleEmployee();

            $('select[name="roles"]').on('change', function() {
                toggleEmployee();
            });

        });
    </script>

    <script>
        imgInp.onchange = evt => {
            const [file] = imgInp.files
            if (file) {
                blah.src = URL.createObjectURL(file)
            }
        }
    </script>

    <script>
        $(document).ready(function() {
            const serviceDetailsContainer = $('#service-details-container');
            const services = @json($services);
            const slotGroups = @json($slotGroups);

            // Data lama dari old() atau data DB
            const oldServiceDurations = @json(old('service_duration', []));
            const oldServiceBreaks = @json(old('service_break_duration', []));
            const oldSlotGroups = @json(old('slot_group_id', []));
            const userServicesData = @json($userServicesArray ?? []);

            // Ambil services yang sudah dipilih (old() dulu, kalau ga ada pakai DB)
            let selectedServices = @json(old('services', $userServices ?? []));

            function renderServiceDetails(selectedServices) {
                serviceDetailsContainer.empty();

                selectedServices.forEach(serviceId => {
                    const service = services.find(s => s.id == serviceId);
                    if (!service) return;

                    // Tentukan value: old() dulu, kalau tidak pakai DB
                    const durationValue = oldServiceDurations[service.id] ??
                        (userServicesData[service.id]?.duration ?? '');
                    const breakValue = oldServiceBreaks[service.id] ??
                        (userServicesData[service.id]?.break_duration ?? '');
                    const slotGroupValue = oldSlotGroups[service.id] ??
                        (userServicesData[service.id]?.slot_group_id ?? '');

                    const serviceBlock = `
<div class="card card-outline card-primary mb-3">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-concierge-bell mr-1"></i> ${service.title}</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label>Durasi Layanan</label>
                <select name="service_duration[${service.id}]" class="form-control">
                    <option value="">Pilih Durasi</option>
                    ${[5,10,15,20,30,45,60].map(v => `<option value="${v}" ${v == durationValue ? 'selected' : ''}>${v} menit</option>`).join('')}
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label>Waktu Istirahat</label>
                <select name="service_break_duration[${service.id}]" class="form-control">
                    <option value="">Tanpa Istirahat</option>
                    ${[5,10,15,20,25,30].map(v => `<option value="${v}" ${v == breakValue ? 'selected' : ''}>${v} menit</option>`).join('')}
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label>Grup Slot</label>
                <select name="slot_group_id[${service.id}]" class="form-control">
                    <option value="">Pilih Grup Slot</option>
                    ${slotGroups.map(sg => `<option value="${sg.id}" ${sg.id == slotGroupValue ? 'selected' : ''}>${sg.name ?? 'Slot Group #' + sg.id}</option>`).join('')}
                </select>
            </div>
        </div>
    </div>
</div>
            `;
                    serviceDetailsContainer.append(serviceBlock);
                });
            }

            // Render saat load
            renderServiceDetails(selectedServices);

            // Render saat change
            $('#services').on('change', function() {
                selectedServices = $(this).val() || [];
                renderServiceDetails(selectedServices);
            });
        });
    </script>

    <script>
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
    <script>
        document.getElementById('phone').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>


@stop
