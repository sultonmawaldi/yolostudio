@extends('layouts.member')

@section('title', 'Member Dashboard')

@section('member-content')
    <div class="max-w-4xl mx-auto px-4 py-10">

        {{-- 👋 Greeting Card --}}
        <div
            class="bg-gradient-to-r from-blue-400 to-blue-500 text-white rounded-2xl p-8 shadow-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold">Hi, {{ auth()->user()->name }}!</h1>
                <p class="text-lg opacity-90">Tingkatan Akun: {{ ucfirst(auth()->user()->account_type ?? 'Member') }}</p>
            </div>
            <div class="hidden sm:flex items-center gap-3">
                <i class="bi bi-stars text-4xl text-yellow-300"></i>
                <span class="font-medium">Selamat datang di dashboard Anda!</span>
            </div>
        </div>

    </div>
@endsection
