{{-- ✅ LUPA KATA SANDI PAGE - SESUAI LOGIN BLADE --}}
@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('title', 'Lupa Kata Sandi')

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop

@php($password_email_url = View::getSection('password_email_url') ?? config('adminlte.password_email_url', 'password/email'))

@if (config('adminlte.use_route_url', false))
    @php($password_email_url = $password_email_url ? route($password_email_url) : '')
@else
    @php($password_email_url = $password_email_url ? url($password_email_url) : '')
@endif

@section('auth_header')
    <div class="text-center mb-4">
        <h2 class="fw-bold mb-2 text-gradient kalcer-heading">
            <i class="fas fa-key me-2"></i> Lupa Kata Sandi
        </h2>

        <p class="mb-4 fw-light text-muted" style="font-size:1rem; letter-spacing:0.5px; line-height:1.6;">
            Masukkan email terdaftar kamu untuk menerima link reset kata sandi
            <i class="fas fa-key me-2 text-warning"></i>
        </p>

    </div>
@stop

@section('auth_body')

    {{-- ALERT STATUS --}}
    @if (session('status'))
        <div class="custom-success mb-3">
            <i class="fas fa-check-circle"></i>
            <div>
                {{ session('status') }}
            </div>
        </div>
    @endif


    <form action="{{ $password_email_url }}" method="POST" novalidate>
        @csrf

        {{-- EMAIL FIELD --}}
        <div class="mb-3">
            <div class="input-group shadow-sm">
                <input type="email" name="email" id="email"
                    class="form-control border-0 @error('email') is-invalid @enderror" placeholder="Alamat Email"
                    value="{{ old('email') }}" required autofocus style="border-radius: .5rem;">
            </div>

            @error('email')
                <div class="custom-error mt-2">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- SUBMIT BUTTON --}}
        <button type="submit" class="btn btn-gradient w-100 rounded-pill shadow-sm py-2 mt-3 mb-2">
            <i class="fas fa-paper-plane me-1"></i> Kirim Link Reset
        </button>
    </form>
@stop

@section('auth_footer')
    <div class="text-center mt-3">
        <p class="mb-1">
            <a href="{{ route('login') }}" class="text-decoration-none text-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Halaman Masuk
            </a>
        </p>
    </div>
@stop

@push('css')
    <style>
        body.login-page {
            background: linear-gradient(135deg, rgba(102, 166, 255, 0.7), rgba(137, 247, 254, 0.7)),
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
            /* scroll aktif di layar kecil */
        }

        /* Login Box - ukuran tetap */
        .login-box {
            width: 100%;
            max-width: 400px;
            /* tetap, tidak responsive */
            display: flex;
            flex-direction: column;
            align-items: center;
            box-sizing: border-box;
            margin-top: 0;
        }

        /* Logo */
        .login-logo img {
            width: auto;
            max-width: 120px;
            /* tetap */
            height: auto;
            margin-bottom: 15px;
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

        /* Footer selalu ikut scroll */
        .login-box {
            display: flex;
            flex-direction: column;
        }

        /* Responsive kecil untuk scroll saja */
        @media (max-height: 667px) {

            /* iPhone SE */
            body.login-page {
                justify-content: flex-start;
                /* biar scroll muncul */
            }
        }

        @media (max-height: 600px) {

            /* Nest Hub */
            body.login-page {
                justify-content: flex-start;
            }
        }

        /* Tombol Masuk lebih kecil & Kalcer aesthetic */
        .btn-gradient {
            background: linear-gradient(90deg, #6abfe3, #7873f5);
            /* gradient Kalcer-style */
            color: #fff;
            font-weight: 600;
            font-size: 0.9rem;
            /* lebih kecil dari default */
            padding: 0.45rem 1.2rem;
            /* vertical lebih compact, horizontal tetap nyaman */
            transition: all 0.3s;
            backdrop-filter: blur(10px);
            border-radius: 50px;
            /* tetap rounded-pill */
            border: none;
        }

        /* Hover / focus effect */
        .btn-gradient:hover,
        .btn-gradient:focus {
            transform: scale(1.03);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
            color: #fff;
        }

        /* Font & style Kalcer vibes */
        .kalcer-heading {
            font-family: 'Poppins', 'Montserrat', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 1px;
            line-height: 1.2;
            background: #0b1292;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
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

        /* ================= SUCCESS MODERN ================= */

        .custom-success {
            width: 100%;
            margin-bottom: 15px;
            font-size: 0.85rem;
            font-weight: 500;
            color: #0f5132;
            background: rgba(25, 135, 84, 0.12);
            border: 1px solid rgba(25, 135, 84, 0.25);
            padding: 10px 14px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
            backdrop-filter: blur(6px);
            animation: fadeInSuccess 0.4s ease;
        }

        .custom-success i {
            font-size: 1rem;
            color: #198754;
        }

        @keyframes fadeInSuccess {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
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
