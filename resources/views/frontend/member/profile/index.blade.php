@extends('layouts.member')

@section('title', 'Profile Saya')

@section('member-content')
    <div class="mx-auto space-y-6">

        {{-- 👋 Greeting Card --}}
        <div
            class="bg-gradient-to-r from-blue-400 to-cyan-500 text-white rounded-2xl p-8 shadow-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold">Hi, {{ auth()->user()->name }}!</h1>
                <p class="text-lg opacity-90">Selamat datang di dashboard profile Anda!</p>
            </div>
            <div class="flex items-center gap-3">
                <i class="bi bi-stars text-4xl text-yellow-300"></i>
            </div>
        </div>

        {{-- PROFILE CARD --}}
        <div
            class="bg-white dark:bg-gray-900 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-800 overflow-hidden">

            {{-- HEADER --}}
            <div
                class="bg-gradient-to-r from-blue-500 via-cyan-400 to-blue-600
                        px-8 py-8 text-white relative">
                <div class="flex flex-col md:flex-row items-center md:items-end gap-6">

                    <img src="{{ auth()->user()->profileImage() }}"
                        class="w-28 h-28 md:w-32 md:h-32 rounded-full border-4 border-white object-cover shadow-lg">

                    <div class="text-center md:text-left">
                        <h3 class="text-2xl md:text-3xl font-bold tracking-tight">
                            {{ auth()->user()->name }}
                        </h3>

                        <p class="text-sm text-white/80 mt-1">
                            {{ auth()->user()->email }}
                        </p>

                        <div
                            class="mt-3 inline-flex items-center gap-2 bg-white/20 px-4 py-1.5 rounded-full text-sm font-medium">
                            <i class="bi bi-star-fill text-yellow-300"></i>
                            <span>{{ auth()->user()->points ?? 0 }} Points</span>
                        </div>
                    </div>
                </div>

                <span
                    class="absolute top-6 right-6 bg-white/20 backdrop-blur px-4 py-1 rounded-full text-xs font-semibold tracking-wide">
                    MEMBER
                </span>
            </div>

            {{-- BODY --}}
            <div class="p-8">

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">

                    <div class="bg-gray-50 dark:bg-gray-800 p-5 rounded-2xl">
                        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Member ID
                        </p>
                        <p class="mt-1 font-semibold text-gray-800 dark:text-white">
                            {{ auth()->user()->role_uid }}
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800 p-5 rounded-2xl">
                        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Nomor Handphone
                        </p>
                        <p class="mt-1 font-semibold text-gray-800 dark:text-white">
                            {{ auth()->user()->phone ?? '-' }}
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800 p-5 rounded-2xl">
                        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Bergabung Pada
                        </p>
                        <p class="mt-1 font-semibold text-gray-800 dark:text-white">
                            {{ auth()->user()->created_at->translatedFormat('d F Y') }}
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800 p-5 rounded-2xl">
                        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Status
                        </p>
                        <span
                            class="mt-2 inline-block bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400 text-sm px-4 py-1 rounded-full font-semibold">
                            Aktif
                        </span>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800 p-5 rounded-2xl">
                        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Total Kupon
                        </p>
                        <p class="mt-1 font-semibold text-gray-800 dark:text-white">
                            {{ auth()->user()->coupons()->count() }}
                        </p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800 p-5 rounded-2xl">
                        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Kupon Digunakan
                        </p>
                        <p class="mt-1 font-semibold text-gray-800 dark:text-white">
                            {{ auth()->user()->coupons()->where('status', 'used')->count() }}
                        </p>
                    </div>

                </div>

                {{-- ACTION --}}
                <div class="mt-8">
                    <a href="{{ route('member.profile.edit') }}"
                        class="inline-flex items-center justify-center px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white font-semibold transition duration-200 shadow-sm no-underline">
                        Edit Profile
                    </a>
                </div>

            </div>
        </div>

    </div>
@endsection
