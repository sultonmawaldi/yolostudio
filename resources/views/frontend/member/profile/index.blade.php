@extends('layouts.member')

@section('title', 'Profile Saya')

@section('member-content')
    <div class="max-w-4xl mx-auto space-y-6">

        {{-- GREETING --}}
        <div class="bg-white rounded-2xl shadow px-6 py-6 flex flex-col md:flex-row items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">
                    Hai, {{ auth()->user()->name }}!
                </h2>
                <p class="text-gray-500 mt-1">
                    Selamat datang di halaman profil Anda.
                </p>
            </div>
            <a href="{{ route('member.dashboard') }}"
                class="px-5 py-2 rounded-xl bg-blue-500 text-white font-semibold hover:bg-blue-600 transition">
                Kembali ke Dashboard
            </a>
        </div>

        {{-- PROFILE CARD --}}
        <div class="bg-white rounded-3xl shadow overflow-hidden">

            {{-- TOP SECTION --}}
            <div class="bg-gradient-to-r from-blue-500 to-blue-300 p-6 text-white relative">
                <div class="flex items-center gap-4">
                    <img src="{{ auth()->user()->profileImage() }}"
                        class="w-24 h-24 rounded-full border-4 border-white object-cover shadow-lg">
                    <div>
                        <h3 class="text-2xl font-bold">
                            {{ auth()->user()->name }}
                        </h3>
                        <p class="text-sm text-white/80 mt-1">
                            {{ auth()->user()->email }}
                        </p>
                        <p class="mt-2 text-sm">
                            <span class="font-semibold">Points:</span> {{ auth()->user()->points ?? 0 }}
                        </p>
                    </div>
                </div>

                <span
                    class="absolute top-4 right-4 bg-white/30 text-white px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide">
                    MEMBER
                </span>
            </div>

            {{-- DETAIL --}}
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>
                        <p class="text-sm text-gray-500">Unique Member ID</p>
                        <p class="font-semibold text-gray-800">
                            {{ auth()->user()->role_uid }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Phone Number</p>
                        <p class="font-semibold text-gray-800">
                            {{ auth()->user()->phone ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Joined At</p>
                        <p class="font-semibold text-gray-800">
                            {{ auth()->user()->created_at->format('d F Y') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        <span class="inline-block bg-green-100 text-green-700 text-sm px-3 py-1 rounded-full font-semibold">
                            Active
                        </span>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Total Coupons</p>
                        <p class="font-semibold text-gray-800">
                            {{ auth()->user()->coupons()->count() }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Used Coupons</p>
                        <p class="font-semibold text-gray-800">
                            {{ auth()->user()->coupons()->where('status', 'used')->count() }}
                        </p>
                    </div>

                </div>

                {{-- ACTION --}}
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('member.profile.edit') }}"
                        class="px-5 py-2 rounded-xl bg-blue-500 text-white font-semibold hover:bg-blue-600 transition">
                        Edit Profile
                    </a>

                    <a href="{{ route('member.dashboard') }}"
                        class="px-5 py-2 rounded-xl bg-gray-100 text-gray-700 font-semibold hover:bg-gray-200 transition">
                        Kembali
                    </a>
                </div>
            </div>

        </div>

    </div>
@endsection
