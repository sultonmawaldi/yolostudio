@extends('layouts.member')

@section('title', 'Tukar Point')

@section('member-content')
    <div class="max-w-xl mx-auto">

        {{-- ALERT --}}
        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded-lg mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4">
                {{ session('error') }}
            </div>
        @endif

        {{-- CARD COUPON --}}
        <div class="bg-white rounded-3xl shadow-lg overflow-hidden">

            {{-- IMAGE --}}
            <div class="relative">
                <img src="{{ asset('images/coupon.png') }}" alt="Coupon" class="w-full h-48 object-cover">
                <span
                    class="absolute top-4 right-4 bg-yellow-400 text-yellow-900 text-sm px-3 py-1 rounded-full font-semibold">
                    Reward
                </span>
            </div>

            {{-- CONTENT --}}
            <div class="p-6 space-y-4">

                <div class="text-center">
                    <h2 class="text-2xl font-bold text-gray-800">
                        Tukar Point Anda
                    </h2>
                    <p class="text-gray-500 text-sm">
                        Gunakan point untuk mendapatkan kupon diskon
                    </p>
                </div>

                {{-- INFO --}}
                <div class="grid grid-cols-2 gap-4 text-center">
                    <div class="bg-gray-100 rounded-xl p-3">
                        <p class="text-sm text-gray-500">Point Anda</p>
                        <p class="text-xl font-bold text-blue-600">
                            {{ $points }}
                        </p>
                    </div>

                    <div class="bg-gray-100 rounded-xl p-3">
                        <p class="text-sm text-gray-500">Potongan Kupon</p>
                        <p class="text-xl font-bold text-green-600">
                            Rp {{ number_format($couponValue, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                {{-- REQUIREMENT --}}
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-center">
                    <p class="text-blue-700">
                        Dibutuhkan <strong>{{ $requiredPoints }}</strong> point
                    </p>
                </div>

                {{-- ACTION --}}
                <form action="{{ route('member.coupons.redeem.store') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full bg-blue-600 text-white py-3 rounded-xl font-semibold hover:bg-blue-700 transition disabled:opacity-50"
                        {{ $points < $requiredPoints ? 'disabled' : '' }}>
                        Tukar Sekarang
                    </button>
                </form>

                @if ($points < $requiredPoints)
                    <p class="text-center text-sm text-red-500">
                        Point Anda belum mencukupi
                    </p>
                @endif

            </div>
        </div>

    </div>
@endsection
