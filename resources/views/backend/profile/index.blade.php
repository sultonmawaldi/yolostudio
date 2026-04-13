@extends('adminlte::page')

@section('title', 'Profil Pengguna')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold">
                    <i class="fas fa-user text-primary mr-2"></i>
                    Profil Pengguna
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">Profil Pengguna</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')


    {{-- MODAL FOTO PROFIL --}}
    <div class="modal fade" id="profileImageModal">
        <div class="modal-dialog">
            <form action="{{ route('user.profile.image.update', $user->id) }}" method="post" enctype="multipart/form-data">
                <div class="modal-content shadow">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">Ubah Foto Profil</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        @csrf
                        @method('PUT')
                        <input type="file" name="image" class="form-control">

                        @error('image')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- CONTENT --}}
    <section class="content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-md-3">

                    <div class="card card-primary card-outline shadow-sm">
                        <div class="card-body box-profile text-center">

                            <img class="profile-user-img img-circle mb-2" src="{{ $user->profileImage() }}"
                                alt="Foto Profil" style="width:120px;height:120px;object-fit:cover;">

                            <div class="mb-2">
                                <a data-toggle="modal" data-target="#profileImageModal" href="#">
                                    Ganti Foto
                                </a>

                                @if ($user->image)
                                    <form action="{{ route('delete.profile.image', $user->id) }}" method="post"
                                        class="mt-1">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button" class="btn btn-sm btn-danger btn-confirm"
                                            data-type="delete-image">
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <h3 class="profile-username">{{ $user->name }}</h3>
                            <p class="text-muted">{{ $user->email }}</p>

                            <ul class="list-group list-group-unbordered mt-3">

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="font-weight-bold">Login Terakhir</span>
                                    <span class="text-muted text-right">
                                        {{ $user->lastSuccessfulLoginAt() ? $user->lastSuccessfulLoginAt()->diffForHumans() : '-' }}
                                    </span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="font-weight-bold">Dibuat</span>
                                    <span class="text-muted text-right">
                                        {{ $user->created_at->diffForHumans() }}
                                    </span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="font-weight-bold">Peran</span>
                                    <span class="text-muted text-right">
                                        {{ ucwords($user->getRoleNames()->first()) }}
                                    </span>
                                </li>

                            </ul>

                        </div>
                    </div>

                </div>

                <div class="col-md-9">
                    <div class="card shadow-sm">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">

                                @if ($user->comments)
                                    <li class="nav-item">
                                        <a class="nav-link" href="#timeline" data-toggle="tab">
                                            <i class="fas fa-comments mr-1"></i> Komentar
                                        </a>
                                    </li>
                                @endif

                                <li class="nav-item">
                                    <a class="nav-link active" href="#settings" data-toggle="tab">
                                        <i class="fas fa-user mr-1"></i> Profil
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" href="#logs" data-toggle="tab">
                                        <i class="fas fa-history mr-1"></i> Riwayat Login
                                    </a>
                                </li>

                                @if ($user->employee)
                                    <li class="nav-item">
                                        <a class="nav-link" href="#bio" data-toggle="tab">
                                            <i class="fas fa-id-card mr-1"></i> Bio
                                        </a>
                                    </li>
                                @endif

                                <li class="nav-item">
                                    <a class="nav-link" href="#password" data-toggle="tab">
                                        <i class="fas fa-lock mr-1"></i> Ubah Kata Sandi
                                    </a>
                                </li>

                            </ul>
                        </div>

                        <div class="card-body">
                            <div class="tab-content">

                                {{-- KOMENTAR --}}
                                <div class="tab-pane" id="timeline">
                                    <div class="alert alert-info">
                                        Fitur komentar masih dalam pengembangan.
                                    </div>
                                </div>

                                {{-- LOG --}}
                                <div class="tab-pane" id="logs">
                                    <div class="card">
                                        <div class="card-body table-responsive p-0"
                                            style="max-height: 400px; overflow-y: auto;">
                                            <table class="table table-hover text-nowrap mb-0">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Login</th>
                                                        <th>Logout</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($user->authentications as $auth)
                                                        <tr>
                                                            <td>
                                                                {{ $auth->login_at ? $auth->login_at->format('H:i | d M Y') : '-' }}
                                                            </td>
                                                            <td>
                                                                {{ $auth->logout_at ? $auth->logout_at->format('H:i | d M Y') : '-' }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                {{-- PROFIL --}}
                                <div class="tab-pane show active" id="settings">
                                    <form action="{{ route('user.profile.update', $user->id) }}" method="post"
                                        class="form-horizontal">
                                        @csrf
                                        @method('PATCH')

                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Nama</label>
                                            <div class="col-sm-10">
                                                <input type="text" name="name" class="form-control"
                                                    value="{{ $user->name }}" placeholder="Masukkan nama">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Email</label>
                                            <div class="col-sm-10">
                                                <input type="email"
                                                    class="form-control @error('email') is-invalid @enderror"
                                                    name="email" value="{{ $user->email }}"
                                                    style="background-color:#eee;" placeholder="Email">

                                                @error('email')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="offset-sm-2 col-sm-10">
                                                <button type="submit" class="btn btn-danger btn-confirm"
                                                    data-type="update-profile">
                                                    Simpan Perubahan
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                {{-- BIO --}}
                                @if ($user->employee)
                                    <div class="tab-pane" id="bio">
                                        <form action="{{ route('employee.bio.update', $user->employee->id) }}"
                                            method="post" class="form-horizontal">
                                            @csrf
                                            @method('PUT')

                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label">Bio</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" rows="5" name="bio" placeholder="Tulis deskripsi diri...">{{ $user->employee->bio }}</textarea>
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label">Facebook</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" name="social[facebook]"
                                                        value="{{ $user->employee->social['facebook'] ?? '' }}"
                                                        placeholder="Link Facebook">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label">Instagram</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" name="social[instagram]"
                                                        value="{{ $user->employee->social['instagram'] ?? '' }}"
                                                        placeholder="Link Instagram">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label">Twitter</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" name="social[twitter]"
                                                        value="{{ $user->employee->social['twitter'] ?? '' }}"
                                                        placeholder="Link Twitter / X">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <label class="col-sm-2 col-form-label">LinkedIn</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" name="social[linkedin]"
                                                        value="{{ $user->employee->social['linkedin'] ?? '' }}"
                                                        placeholder="Link LinkedIn">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="offset-sm-2 col-sm-10">
                                                    <button type="submit" class="btn btn-danger btn-confirm"
                                                        data-type="update-bio">
                                                        Simpan
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                <div class="tab-pane" id="password">
                                    <form action="{{ route('user.password.update', $user->id) }}" method="post">
                                        @csrf
                                        @method('PATCH')

                                        <div class="form-group row align-items-center">
                                            <label class="col-sm-3 col-form-label font-weight-bold">
                                                Kata Sandi Lama
                                            </label>
                                            <div class="col-sm-9">
                                                <input type="password" name="current_password" class="form-control"
                                                    placeholder="Masukkan kata sandi lama">
                                            </div>
                                        </div>

                                        <div class="form-group row align-items-center">
                                            <label class="col-sm-3 col-form-label font-weight-bold">
                                                Kata Sandi Baru
                                            </label>
                                            <div class="col-sm-9">
                                                <input type="password" name="password" class="form-control"
                                                    placeholder="Kata sandi baru">
                                            </div>
                                        </div>

                                        <div class="form-group row align-items-center">
                                            <label class="col-sm-3 col-form-label font-weight-bold">
                                                Konfirmasi Kata Sandi
                                            </label>
                                            <div class="col-sm-9">
                                                <input type="password" name="password_confirmation" class="form-control"
                                                    placeholder="Ulangi kata sandi">
                                            </div>
                                        </div>

                                        {{-- tombol tetap posisi awal (kiri, sejajar input) --}}
                                        <div class="form-group row">
                                            <div class="offset-sm-3 col-sm-9">
                                                <button type="submit" class="btn btn-danger btn-confirm"
                                                    data-type="update-password">
                                                    <i class="fas fa-save mr-1"></i> Perbarui Kata Sandi
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
    </section>
@stop

@section('css')

@stop
<style>
    body {
        overflow-y: scroll;
    }
</style>
@section('js')
    <script>
        $(document).ready(function() {
            @if ($errors->has('image'))
                $('#profileImageModal').modal('show');
            @endif
        });
    </script>

    <script>
        $(document).ready(function() {
            $(".alert").delay(6000).slideUp(300);
        });
    </script>

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


    <script>
        $(document).ready(function() {
            $('.myTable').DataTable({
                responsive: true
            });

        });
    </script>

    <script>
        $(function() {

            // ✅ ERROR VALIDATION
            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan!',
                    html: `{!! implode('<br>', $errors->all()) !!}`,
                    confirmButtonColor: '#e3342f'
                });
            @endif

            // ✅ SUCCESS
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#28a745',
                    timer: 3000,
                    showConfirmButton: false
                });
            @endif

            // ✅ AUTO OPEN MODAL JIKA ERROR IMAGE
            @if ($errors->has('image'))
                $('#profileImageModal').modal('show');
            @endif

        });
    </script>
    <script>
        $(document).ready(function() {

            $('.btn-confirm').on('click', function(e) {
                e.preventDefault();

                let form = $(this).closest('form');
                let type = $(this).data('type');

                let config = {
                    title: 'Yakin?',
                    text: 'Aksi ini tidak bisa dibatalkan!',
                    icon: 'warning',
                };

                // Custom tiap tombol
                if (type === 'update-status') {
                    config.title = 'Ubah Status Booking?';
                    config.text = 'Status booking akan diperbarui!';
                }

                if (type === 'update-profile') {
                    config.title = 'Simpan Perubahan?';
                    config.text = 'Data profil akan diperbarui!';
                }

                if (type === 'update-bio') {
                    config.title = 'Simpan Bio?';
                    config.text = 'Perubahan bio akan disimpan!';
                }

                if (type === 'update-password') {
                    config.title = 'Ubah Password?';
                    config.text = 'Password akan diganti!';
                }

                if (type === 'delete-image') {
                    config.title = 'Hapus Foto Profil?';
                    config.text = 'Foto profil akan dihapus permanen!';
                    config.icon = 'warning';
                }


                Swal.fire({
                    ...config,
                    showCancelButton: true,
                    confirmButtonColor: '#e3342f',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, lanjut!',
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
