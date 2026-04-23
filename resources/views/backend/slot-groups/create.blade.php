@extends('adminlte::page')

@section('title', 'Tambah Grup Slot')

@section('content_header')

    <div class="container-fluid">
        <div class="row mb-3 align-items-center">

            {{-- Judul --}}
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold">
                    <i class="fas fa-clock text-primary mr-2"></i>
                    Tambah Grup Slot
                </h1>
            </div>

            {{-- Breadcrumb --}}
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('slot-group.index') }}">Grup Slot</a>
                    </li>
                    <li class="breadcrumb-item active">Tambah Grup Slot</li>
                </ol>
            </div>

        </div>
    </div>

@stop

@section('content')

    <div class="container-fluid">
        <div class="justify-content-between pb-5">

            <form role="form" method="post" action="{{ route('slot-group.store') }}">
                @csrf

                <div class="row">

                    {{-- KIRI --}}
                    <div class="col-md-8">

                        {{-- CARD UTAMA --}}
                        <div class="card card-light">
                            <div class="card-header">
                                <h3 class="card-title">Tambah Grup Slot</h3>

                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="card-body">

                                {{-- EMPLOYEE --}}
                                <div class="mb-3">
                                    <label class="form-label">Karyawan</label>

                                    <select name="employee_id"
                                        class="form-select @error('employee_id') is-invalid @enderror">

                                        <option value="">-- Pilih Karyawan --</option>

                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}"
                                                {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->user->name ?? '-' }}
                                            </option>
                                        @endforeach

                                    </select>

                                    @error('employee_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- NAME --}}
                                <div class="form-group">
                                    <label>Nama Grup Slot</label>

                                    <input type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                        placeholder="Contoh: Wide Box Maroon Cilegon">

                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- SLOT DURATION --}}
                                <div class="form-group">
                                    <label>Durasi Slot (menit)</label>

                                    <input type="number" name="slot_duration"
                                        class="form-control @error('slot_duration') is-invalid @enderror"
                                        value="{{ old('slot_duration') }}" placeholder="Contoh: 60">

                                    @error('slot_duration')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- BREAK --}}
                                <div class="form-group">
                                    <label>Istirahat (menit)</label>

                                    <input type="number" name="break_duration"
                                        class="form-control @error('break_duration') is-invalid @enderror"
                                        value="{{ old('break_duration', 0) }}">

                                    @error('break_duration')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- START TIME --}}
                                <div class="form-group">
                                    <label>Jam Mulai</label>

                                    <input type="time" name="start_time"
                                        class="form-control @error('start_time') is-invalid @enderror"
                                        value="{{ old('start_time') }}">

                                    @error('start_time')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                {{-- END TIME --}}
                                <div class="form-group">
                                    <label>Jam Selesai</label>

                                    <input type="time" name="end_time"
                                        class="form-control @error('end_time') is-invalid @enderror"
                                        value="{{ old('end_time') }}">

                                    @error('end_time')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                            </div>
                        </div>

                        {{-- WORKING HOURS --}}
                        <div class="card card-light mt-3">

                            {{-- HEADER CARD (COLLAPSE FIX DI UJUNG KANAN) --}}
                            <div class="card-header">
                                <h3 class="card-title">Jam Kerja</h3>

                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="card-body">

                                @php
                                    $days = [
                                        'monday' => 'Senin',
                                        'tuesday' => 'Selasa',
                                        'wednesday' => 'Rabu',
                                        'thursday' => 'Kamis',
                                        'friday' => 'Jumat',
                                        'saturday' => 'Sabtu',
                                        'sunday' => 'Minggu',
                                    ];
                                @endphp

                                @foreach ($days as $key => $label)
                                    <div class="border rounded p-3 mb-3">

                                        {{-- HEADER DAY --}}
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <strong>{{ $label }}</strong>

                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                onclick="addRow('{{ $key }}')">
                                                + Tambah Jam
                                            </button>
                                        </div>

                                        {{-- LABEL --}}
                                        <div class="row mb-1">
                                            <div class="col-md-5">
                                                <small class="text-muted">Mulai</small>
                                            </div>
                                            <div class="col-md-5">
                                                <small class="text-muted">Selesai</small>
                                            </div>
                                            <div class="col-md-2"></div>
                                        </div>

                                        {{-- CONTAINER --}}
                                        <div id="container-{{ $key }}">

                                            <div class="row mb-2 time-row align-items-center">
                                                <div class="col-md-5">
                                                    <input type="time"
                                                        name="working_hours[{{ $key }}][0][start]"
                                                        class="form-control">
                                                </div>

                                                <div class="col-md-5">
                                                    <input type="time" name="working_hours[{{ $key }}][0][end]"
                                                        class="form-control">
                                                </div>

                                                <div class="col-md-2 text-center">
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        onclick="removeRow(this)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                @endforeach

                            </div>
                        </div>

                    </div>


                    {{-- SIDEBAR --}}
                    <div class="col-md-4">

                        <div class="sticky-top">

                            {{-- DETAIL --}}
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Detail Grup Slot</h3>
                                </div>

                                <div class="card-body pb-0">

                                    {{-- BUTTON --}}
                                    <div class="form-group mt-3 d-flex justify-content-end">

                                        <a href="{{ route('slot-group.index') }}" class="btn btn-secondary mr-2">
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
    </div>

@stop

@section('css')

@stop


@section('js')

    <script>
        $('#name').on("change keyup paste click", function() {
            var Text = $(this).val().trim();
            Text = Text.toLowerCase();
            Text = Text.replace(/[^a-zA-Z0-9]+/g, '-');
        });
    </script>

    {{-- SWEETALERT ERROR --}}
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
                    confirmButtonColor: '#d33'
                });
            });
        </script>
    @endif

    <script>
        function addRow(day) {
            let container = document.getElementById('container-' + day);
            let index = container.querySelectorAll('.time-row').length;

            let row = document.createElement('div');
            row.className = "row mb-2 time-row align-items-center";

            row.innerHTML = `
        <div class="col-md-5">
            <input type="time"
                name="working_hours[${day}][${index}][start]"
                class="form-control">
        </div>

        <div class="col-md-5">
            <input type="time"
                name="working_hours[${day}][${index}][end]"
                class="form-control">
        </div>

        <div class="col-md-2 text-center">
            <button type="button"
                class="btn btn-sm btn-danger"
                onclick="removeRow(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;

            container.appendChild(row);
        }

        function removeRow(button) {
            button.closest('.time-row').remove();
        }
    </script>

@stop
