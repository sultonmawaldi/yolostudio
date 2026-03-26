{{-- ✅ REGISTER PAGE NO HP --}}
@extends('adminlte::auth.auth-page', ['auth_type' => 'register'])

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop

@php($login_url = View::getSection('login_url') ?? config('adminlte.login_url', 'login'))
@php($register_url = View::getSection('register_url') ?? config('adminlte.register_url', 'register'))

@if (config('adminlte.use_route_url', false))
    @php($login_url = $login_url ? route($login_url) : '')
    @php($register_url = $register_url ? route($register_url) : '')
@else
    @php($login_url = $login_url ? url($login_url) : '')
    @php($register_url = $register_url ? url($register_url) : '')
@endif

@section('auth_header')
    <div class="text-center mb-4">
        <h2 class="fw-bold mb-2 text-gradient kalcer-heading">
            ✦ Daftar Akun Baru ✦
        </h2>
        <p class="mb-4 fw-light" style="font-size:1rem; color:#6c757d; letter-spacing:0.5px; line-height:1.5;">
            Daftar dengan nomor HP aktif untuk mulai menggunakan layanan kami <i class="fas fa-camera me-2 text-primary"></i>
        </p>
    </div>
@stop

@section('auth_body')
    <form action="{{ $register_url }}" method="POST" novalidate>
        @csrf

        {{-- NAME FIELD --}}
        <div class="mb-3">
            <div class="input-group shadow-sm">
                <input type="text" name="name" class="form-control border-0 @error('name') is-invalid @enderror"
                    placeholder="Nama Lengkap" value="{{ old('name') }}" required autofocus style="border-radius: .5rem;">
            </div>

            @error('name')
                <div class="custom-error mt-2">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- EMAIL FIELD --}}
        <div class="mb-3">
            <div class="input-group shadow-sm">
                <input type="email" name="email" class="form-control border-0 @error('email') is-invalid @enderror"
                    placeholder="Alamat Email" value="{{ old('email') }}" required style="border-radius: .5rem;">
            </div>

            @error('email')
                <div class="custom-error mt-2">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- PHONE FIELD --}}
        <div class="mb-3">
            <div class="input-group shadow-sm">
                <span class="input-group-text border-0 d-flex align-items-center"
                    style="background-color: rgba(255, 255, 255, 0.35); border-radius: .5rem 0 0 .5rem;">
                    <img src="https://flagcdn.com/w20/id.png" alt="ID"
                        style="width:20px; height:auto; margin-right:8px;">
                    +62
                </span>

                <input type="tel" name="phone" id="phone"
                    class="form-control border-0 @error('phone') is-invalid @enderror" placeholder="Nomor HP / Whatsapp"
                    value="{{ old('phone') }}" inputmode="numeric" pattern="[0-9]{9,13}" maxlength="13" required
                    style="border-radius: 0 .5rem .5rem 0;">
            </div>

            @error('phone')
                <div class="custom-error mt-2">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- PASSWORD FIELD --}}
        <div class="mb-3">
            <div class="input-group shadow-sm">
                <input type="password" name="password" id="password"
                    class="form-control border-0 @error('password') is-invalid @enderror" placeholder="Kata Sandi" required
                    style="border-radius: .5rem 0 0 .5rem;">

                <button type="button" id="toggle-password" class="btn btn-light border-0 text-secondary px-3"
                    style="border-radius: 0 .5rem .5rem 0;">
                    <i class="fas fa-eye"></i>
                </button>
            </div>

            @error('password')
                <div class="custom-error mt-2">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- PASSWORD CONFIRM FIELD --}}
        <div class="mb-3">
            <div class="input-group shadow-sm">
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control border-0"
                    placeholder="Konfirmasi Kata Sandi" required style="border-radius: .5rem;">
            </div>
        </div>

        {{-- SUBMIT BUTTON --}}
        <button type="submit" class="btn btn-gradient w-100 rounded-pill shadow-sm py-2 mt-3 mb-2">
            <i class="fas fa-user-plus me-1"></i> Daftar Sekarang
        </button>
    </form>

@stop

@section('auth_footer')
    <div class="text-center mt-3">
        <p class="mb-0">Sudah punya akun?
            <a href="{{ $login_url }}" class="text-decoration-none text-primary fw-semibold">Masuk di sini</a>
        </p>
    </div>
@stop

@push('css')
    <style>
        body.register-page {
            background: linear-gradient(135deg, rgba(137, 247, 254, 0.7), rgba(102, 166, 255, 0.7)),
                url('{{ asset('uploads/images/bg-login.jpg') }}') center/cover no-repeat;
            font-family: 'Poppins', sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            padding: 10px;
            box-sizing: border-box;
            overflow-y: auto;
        }

        .register-box {
            width: 100%;
            max-width: 400px;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 0;
        }

        /* Card transparan & blur */
        .card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 1.8rem;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.25);
            border: none;
            width: 100%;
            padding: 20px;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
        }

        /* Card Header */
        .card .card-header {
            text-align: center;
            background: transparent;
            border-bottom: none;
            color: #fff;
            padding: 15px 0;
        }

        /* Card Body transparan */
        .card .card-body {
            background: transparent;
            display: flex;
            flex-direction: column;
            gap: 15px;
            padding: 15px 0;
        }

        /* Card Footer tetap di dalam card */
        .card .card-footer {
            background: transparent;
            border-top: none;
            text-align: center;
            padding: 15px 0;
            color: #fff;
        }

        /* Hapus ikon mata bawaan browser */
        input::-ms-reveal,
        input::-ms-clear {
            display: none !important;
        }

        .btn-gradient {
            background: linear-gradient(90deg, #6abfe3, #7873f5);
            color: #fff;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.45rem 1.2rem;
            transition: all 0.3s;
            border-radius: 50px;
            border: none;
        }

        .btn-gradient:hover,
        .btn-gradient:focus {
            transform: scale(1.03);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
            color: #fff;
        }

        .kalcer-heading {
            font-family: 'Poppins', 'Montserrat', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 1px;
            line-height: 1.2;
            background: #0b1292;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* === Input Transparan Putih Cerah === */
        .form-control {
            background: rgba(255, 255, 255, 0.4) !important;
            /* lebih terang dan jelas */
            color: #000 !important;
            /* teks hitam agar kontras di atas putih */
            border: 1px solid rgba(255, 255, 255, 0.6) !important;
            border-radius: 50px;
            padding: 0.65rem 1.2rem;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            backdrop-filter: blur(8px);
            /* efek kaca lembut */
            -webkit-backdrop-filter: blur(8px);
        }

        /* Placeholder lebih terlihat tapi tetap lembut */
        .form-control::placeholder {
            color: rgba(0, 0, 0, 0.5);
            font-weight: 400;
        }

        /* Fokus input lebih cerah */
        .form-control:focus {
            background: rgba(255, 255, 255, 0.6) !important;
            border-color: rgba(255, 255, 255, 0.9) !important;
            box-shadow: 0 0 12px rgba(255, 255, 255, 0.5);
            color: #000;
        }

        /* Input group transparan */
        .input-group-text {
            background: rgba(255, 255, 255, 0.35) !important;
            border: 1px solid rgba(255, 255, 255, 0.5) !important;
            color: #000 !important;
            backdrop-filter: blur(8px);
        }

        /* Tombol toggle mata */
        #toggle-password {
            background: rgba(255, 255, 255, 0.35) !important;
            color: rgba(0, 0, 0, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.5) !important;
            border-left: none;
            transition: all 0.3s;
            backdrop-filter: blur(8px);
        }

        #toggle-password:hover {
            background: rgba(255, 255, 255, 0.55) !important;
            color: #000;
        }

        /* Validasi error transparan */
        .is-invalid {
            border-color: rgba(255, 77, 77, 0.9) !important;
            background: rgba(255, 77, 77, 0.25) !important;
        }

        .invalid-feedback {
            color: #cc0000;
            font-weight: 400;
        }

        /* ================= ERROR MODERN ================= */

        .custom-error {
            width: 100%;
            margin-top: 8px;
            /* 🔥 kasih jarak dari input */
            font-size: 0.8rem;
            font-weight: 500;
            color: #842029;
            background: rgba(255, 99, 132, 0.12);
            border: 1px solid rgba(255, 99, 132, 0.25);
            padding: 8px 12px;
            border-radius: 8px;
            display: flex;
            /* 🔥 jangan inline-flex */
            align-items: center;
            gap: 6px;
            backdrop-filter: blur(4px);
            animation: fadeInError 0.3s ease;
        }

        .custom-error i {
            font-size: 0.85rem;
        }

        .is-invalid {
            border-color: rgba(255, 99, 132, 0.7) !important;
            box-shadow: 0 0 0 2px rgba(255, 99, 132, 0.2);
        }

        @keyframes fadeInError {
            from {
                opacity: 0;
                transform: translateY(-3px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endpush

@section('adminlte_js')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const phoneInput = document.getElementById("phone");
            const passwordInput = document.getElementById("password");
            const confirmInput = document.getElementById("password_confirmation");
            const togglePassword = document.getElementById("toggle-password");

            // 🔹 Hanya angka & hilangkan 0 di depan
            phoneInput.addEventListener("input", function() {
                this.value = this.value.replace(/\D/g, '');
                if (this.value.startsWith('0')) this.value = this.value.substring(1);
            });

            // 🔹 Toggle password visibility untuk keduanya
            togglePassword.addEventListener("click", function() {
                const type = passwordInput.type === "password" ? "text" : "password";
                passwordInput.type = type;
                confirmInput.type = type; // toggle juga field konfirmasi
                this.innerHTML = type === "password" ?
                    '<i class="fas fa-eye"></i>' :
                    '<i class="fas fa-eye-slash"></i>';
            });
        });
    </script>
@stop
