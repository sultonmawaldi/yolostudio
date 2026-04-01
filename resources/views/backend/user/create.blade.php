@extends('adminlte::page')

@section('title', 'Tambah User')

@section('content_header')

    <div class="container-fluid">
        <div class="row mb-3 align-items-center">

            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold">
                    <i class="fas fa-user-plus text-primary mr-2"></i>
                    Tambah User
                </h1>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">
                            <i class="fas fa-home"></i> Beranda
                        </a>
                    </li>
                    <li class="breadcrumb-item active">Tambah User</li>
                </ol>
            </div>

        </div>
    </div>

@stop

@section('content')

    <div class="container-fluid">
        <div class="justify-content-between pb-5">

            <form action="{{ route('user.store') }}" method="post">
                @csrf

                <div class="row">

                    {{-- KIRI --}}
                    <div class="col-md-8">

                        {{-- INFORMASI USER --}}
                        <div class="card card-light">
                            <div class="card-header">
                                <h3 class="card-title">Informasi User</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="card-body">

                                <div class="form-group">
                                    <label>Nama</label>
                                    <input type="text" name="name" value="{{ old('name') }}"
                                        class="form-control @error('name') is-invalid @enderror"
                                        placeholder="Masukkan nama lengkap">
                                    @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" value="{{ old('email') }}"
                                        class="form-control @error('email') is-invalid @enderror"
                                        placeholder="Masukkan email">
                                    @error('email')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>No. HP</label>
                                    <input type="tel" name="phone" value="{{ old('phone') }}"
                                        class="form-control @error('phone') is-invalid @enderror"
                                        placeholder="Masukkan nomor HP">
                                    @error('phone')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Password</label>
                                    <input type="password" name="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="Masukkan password">
                                    @error('password')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Konfirmasi Password</label>
                                    <input type="password" name="password_confirmation"
                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                        placeholder="Ulangi password">
                                    @error('password_confirmation')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Role User</label>
                                    <select name="roles" class="form-control select2">
                                        <option value="">-- Pilih Role --</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}"
                                                {{ old('roles') == $role->name ? 'selected' : '' }}>
                                                {{ ucfirst($role->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="hidden" name="is_employee" value="0">
                                        <input type="checkbox" class="custom-control-input" id="is_employee"
                                            name="is_employee" value="1" {{ old('is_employee') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_employee">
                                            Jadikan sebagai Employee
                                        </label>
                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- EMPLOYEE SETTINGS --}}
                        <div id="employee">
                            <div class="card card-light">
                                <div class="card-header">
                                    <h3 class="card-title">Pengaturan Employee</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="card-body">

                                    <div class="form-group">
                                        <label>Pilih Service</label>
                                        <select name="service[]" id="service" class="form-control select2" multiple>
                                            @foreach ($services as $service)
                                                <option value="{{ $service->id }}"
                                                    {{ in_array($service->id, old('service', [])) ? 'selected' : '' }}>
                                                    {{ $service->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div id="service-details-container"></div>

                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- KANAN --}}
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

                                    {{-- STATUS USER --}}
                                    <div class="form-group">
                                        <label>Status Pengguna</label>
                                        <select name="status" class="form-control @error('status') is-invalid @enderror">
                                            <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>
                                                Aktif
                                            </option>
                                            <option value="0" {{ old('status') === '0' ? 'selected' : '' }}>
                                                Nonaktif
                                            </option>
                                        </select>

                                        @error('status')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror

                                        <small class="text-muted">
                                            Tentukan apakah pengguna dapat mengakses sistem
                                        </small>
                                    </div>

                                    {{-- ACTION BUTTON --}}
                                    <div class="form-group mt-4 d-flex justify-content-end">
                                        <a href="{{ route('user.index') }}" class="btn btn-secondary mr-2">
                                            Batal
                                        </a>

                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save mr-1"></i> Simpan
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
        $(document).ready(function() {
            function toggleDayFields(dayId) {
                var isChecked = $('#' + dayId).prop('checked');
                $('#' + dayId + 'From, #' + dayId + 'To').prop('disabled', !isChecked);

                // Show or hide the "Add More" button based on the checkbox state
                if (isChecked) {
                    $('#' + dayId + 'AddMore').removeClass('d-none');
                } else {
                    $('#' + dayId + 'AddMore').addClass('d-none');
                    // Remove all additional fields for the day if unchecked
                    $('.additional-' + dayId).remove();
                }
            }

            function addMoreFields(dayId) {
                // Clone the original row for the specific day
                var originalRow = $('#' + dayId + 'AddMore').closest('.row');
                var clonedRow = originalRow.clone();

                // Reset the values in the cloned row (but don't enable the fields yet)
                clonedRow.find('input').each(function() {
                    $(this).val(''); // Clear the value
                });

                // Replace the col-md-2 section with a blank div for the cloned row
                clonedRow.find('.col-md-2').replaceWith('<div class="col-md-2"></div>');

                // Update "Add More" to "Remove" for the cloned row
                clonedRow.find(`#${dayId}AddMore`).text('Remove').attr('id', '').addClass(
                    'remove-field text-danger');

                // Add a unique class to the cloned row for targeting specific day rows
                clonedRow.addClass('additional-' + dayId);

                // Append the cloned row after the original row or the last cloned row
                if (originalRow.closest('.row').siblings('.additional-' + dayId).length === 0) {
                    originalRow.after(clonedRow);
                } else {
                    originalRow.closest('.row').siblings('.additional-' + dayId).last().after(clonedRow);
                }
            }

            // Remove cloned rows
            $(document).on('click', '.remove-field', function() {
                $(this).closest('.row').remove();
            });

            // Bind change and add-more events to all days
            ['monday',
                'tuesday',
                'wednesday',
                'thursday',
                'friday',
                'saturday',
                'sunday',
            ].forEach(function(day) {
                $('#' + day).on('change', function() {
                    toggleDayFields(day);
                }).trigger('change');

                $('#' + day + 'AddMore').on('click', function() {
                    addMoreFields(day);
                });
            });
        });
    </script>


    <script>
        // In your Javascript (external .js resource or <script> tag)
        $(document).ready(function() {
            $('.select2').select2({
                allowClear: true,
                search: true,
                //maximumSelectionLength: 2
            });
        });
    </script>


    <script>
        $(document).ready(function() {
            // Initially hide the row with id 'employee' when the page loads
            // Check if the checkbox is checked on page load and toggle visibility accordingly
            if ($('#is_employee').prop('checked')) {
                $('#employee').show(); // Show the row if checkbox is checked
            } else {
                $('#employee').hide(); // Hide the row if checkbox is unchecked
            }

            // When the 'Is Employee' checkbox is changed, toggle the row visibility
            $('#is_employee').change(function() {
                if ($(this).prop('checked')) {
                    $('#employee').show(); // Show the row if checkbox is checked
                } else {
                    $('#employee').hide(); // Hide the row if checkbox is unchecked
                }
            });
        });
    </script>

    @push('js')
        <script>
            $(document).ready(function() {
                const serviceDetailsContainer = $('#service-details-container');
                const services = @json($services);
                const slotGroups = @json($slotGroups);

                // Ketika user memilih service
                $('#service').on('change', function() {
                    const selectedServices = $(this).val() || [];
                    serviceDetailsContainer.empty();

                    selectedServices.forEach(serviceId => {
                        const service = services.find(s => s.id == serviceId);
                        if (!service) return;

                        // Template HTML untuk setiap service
                        const serviceBlock = `
<div class="card card-outline card-primary mb-3">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-concierge-bell mr-1"></i> ${service.title}
        </h3>

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
            ${[5,10,15,20,30,45,60].map(v => `<option value="${v}">${v} menit</option>`).join('')}
        </select>
    </div>

    <div class="col-md-4 mb-3">
        <label>Waktu Istirahat</label>
        <select name="service_break_duration[${service.id}]" class="form-control">
            <option value="">Tanpa Istirahat</option>
            ${[5,10,15,20,25,30].map(v => `<option value="${v}">${v} menit</option>`).join('')}
        </select>
    </div>

    <div class="col-md-4 mb-3">
        <label>
    Grup Waktu <small class="text-muted">(Opsional)</small>
</label>
        <select name="slot_group_id[${service.id}]" class="form-control">
            <option value="">Pilih Slot Group</option>
            ${slotGroups.map(sg => `
                                                        <option value="${sg.id}">
                                                            ${sg.name ?? 'Slot Group #' + sg.id}
                                                        </option>
                                                    `).join('')}
        </select>
        <small class="text-muted">
            Grup slot waktu untuk layanan ini
        </small>
    </div>

</div>

    </div>
</div>
`;

                        serviceDetailsContainer.append(serviceBlock);
                    });
                });
            });
        </script>
    @endpush





@stop
