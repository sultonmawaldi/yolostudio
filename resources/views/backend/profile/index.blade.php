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
                        <a href="{{ route('dashboard') }}">Beranda</a>
                    </li>
                    <li class="breadcrumb-item active">Profil Pengguna</li>
                </ol>
            </div>
        </div>
    </div>
@stop

@section('content')

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

                                {{-- FORM UPLOAD LANGSUNG --}}
                                <form id="formUploadFoto" action="{{ route('user.profile.image.update', $user->id) }}"
                                    method="post" enctype="multipart/form-data">

                                    @csrf
                                    @method('PUT')

                                    {{-- INPUT FILE HIDDEN --}}
                                    <input type="file" name="image" id="inputFoto" class="d-none" accept="image/*">

                                    {{-- BUTTON TRIGGER --}}
                                    <button type="button" class="btn btn-sm btn-primary mb-1"
                                        onclick="document.getElementById('inputFoto').click();">
                                        <i class="fas fa-camera mr-1"></i>
                                        Ganti Foto
                                    </button>

                                </form>

                                {{-- HAPUS FOTO --}}
                                @if ($user->image)
                                    <form action="{{ route('delete.profile.image', $user->id) }}" method="post"
                                        class="d-inline">

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

                            <ul class="list-group list-group-unbordered mt-3 small">

                                <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                    <span class="font-weight-semibold text-dark">Login Terakhir</span>
                                    <span class="text-muted text-right">
                                        {{ $user->lastSuccessfulLoginAt() ? $user->lastSuccessfulLoginAt()->diffForHumans() : '-' }}
                                    </span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                    <span class="font-weight-semibold text-dark">Dibuat</span>
                                    <span class="text-muted text-right">
                                        {{ $user->created_at->diffForHumans() }}
                                    </span>
                                </li>

                                <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                    <span class="font-weight-semibold text-dark">Peran</span>
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
                                        <a class="nav-link" href="#timeline" data-bs-toggle="tab">
                                            <i class="fas fa-comments mr-1"></i> Komentar
                                        </a>
                                    </li>
                                @endif

                                <li class="nav-item">
                                    <a class="nav-link active" href="#settings" data-bs-toggle="tab">
                                        <i class="fas fa-user mr-1"></i> Profil
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" href="#logs" data-bs-toggle="tab">
                                        <i class="fas fa-history mr-1"></i> Riwayat Login
                                    </a>
                                </li>

                                @if ($user->employee)
                                    <li class="nav-item">
                                        <a class="nav-link" href="#bio" data-bs-toggle="tab">
                                            <i class="fas fa-id-card mr-1"></i> Bio
                                        </a>
                                    </li>
                                @endif

                                <li class="nav-item">
                                    <a class="nav-link" href="#password" data-bs-toggle="tab">
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

                                        <div class="card-footer text-right">
                                            <button type="submit" class="btn btn-primary px-4 btn-confirm"
                                                data-type="update-profile">
                                                <i class="fas fa-save mr-1"></i> Simpan Perubahan
                                            </button>
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
                                                <label class="col-sm-2 col-form-label">X</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" name="social[x]"
                                                        value="{{ $user->employee->social['x'] ?? '' }}"
                                                        placeholder="Link X">
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

                                            <div class="card-footer text-right">
                                                <button type="submit" class="btn btn-primary px-4 btn-confirm"
                                                    data-type="update-bio">
                                                    <i class="fas fa-save mr-1"></i> Simpan Perubahan
                                                </button>
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
                                        <div class="card-footer text-right">
                                            <button type="submit" class="btn btn-primary px-4 btn-confirm"
                                                data-type="update-password">
                                                <i class="fas fa-save mr-1"></i> Perbarui Kata Sandi
                                            </button>
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

    <style>
        body.swal2-shown {
            overflow-y: scroll !important;
            padding-right: 0 !important;
        }
    </style>
@stop

@section('js')

    {{-- ================= TOAST NOTIFICATION ================= --}}
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });

        // SUCCESS TOAST
        @if (session('success'))
            Toast.fire({
                icon: 'success',
                title: "{{ session('success') }}"
            });
        @endif

        // ERROR TOAST (validation umum)
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                Toast.fire({
                    icon: 'error',
                    title: "{{ $error }}"
                });
            @endforeach
        @endif

        // ERROR IMAGE → tetap buka modal (tidak diubah logic)
        @if ($errors->has('image'))
            $(document).ready(function() {
                $('#profileImageModal').modal('show');
            });
        @endif
    </script>


    {{-- ================= AUTO HIDE ALERT (tetap) ================= --}}
    <script>
        $(document).ready(function() {
            $(".alert").delay(6000).slideUp(300);
        });
        // ==========================
        // AUTO UPLOAD FOTO PROFIL
        // ==========================
        document.addEventListener('DOMContentLoaded', function() {

            const inputFoto = document.getElementById('inputFoto');
            const formFoto = document.getElementById('formUploadFoto');

            if (inputFoto) {
                inputFoto.addEventListener('change', function() {
                    if (this.files && this.files.length > 0) {
                        formFoto.submit();
                    }
                });
            }

        });
    </script>


    {{-- ================= SWAL VALIDATION (tetap boleh dipakai) ================= --}}
    <script>
        $(function() {

            // ⚠️ OPTIONAL: kalau masih ada error besar selain toast
            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan!',
                    html: `{!! implode('<br>', $errors->all()) !!}`,
                    confirmButtonColor: '#e3342f'
                });
            @endif

            // ⚠️ SUCCESS sudah diganti TOAST (ini sengaja di-nonaktifkan)
            // supaya tidak dobel popup

        });
    </script>


    {{-- ================= CONFIRM ACTION BUTTON ================= --}}
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

                if (type === 'update-profile') {
                    config.title = 'Simpan Perubahan?';
                    config.text = 'Data profil akan diperbarui!';
                }

                if (type === 'update-bio') {
                    config.title = 'Simpan Bio?';
                    config.text = 'Perubahan bio akan disimpan!';
                }

                if (type === 'update-password') {
                    config.title = 'Ubah Kata Sandi?';
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
