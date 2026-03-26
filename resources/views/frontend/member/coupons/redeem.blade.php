@extends('layouts.member')

@section('title', 'Tukar Point')

@section('member-content')

    <div class="space-y-6">

        {{-- HEADER CARD --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 px-6 py-5">

            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
                Tukar Point
            </h2>

            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Gunakan point untuk mendapatkan kupon diskon
            </p>
        </div>


        {{-- ALERT --}}
        @if (session('success'))
            <div class="bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 p-4 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300 p-4 rounded-xl text-sm">
                {{ session('error') }}
            </div>
        @endif


        {{-- MAIN CARD --}}
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">

            {{-- IMAGE --}}
            <div class="relative">
                <div class="flex justify-center">
                    <img src="{{ asset('images/coupon.png') }}" alt="Kupon Potongan 100rb"
                        class="w-full sm:w-4/5 lg:w-2/3 xl:w-1/2 
                h-auto object-contain rounded-xl">
                </div>

                <span
                    class="absolute top-4 right-4 bg-yellow-400 text-yellow-900 
                       dark:bg-yellow-600 dark:text-yellow-100 
                       text-xs px-3 py-1 rounded-full font-semibold">
                    Reward
                </span>
            </div>

            {{-- CONTENT --}}
            <div class="p-6 space-y-6">

                {{-- INFO GRID --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-center">

                    <div class="bg-gray-100 dark:bg-gray-700 rounded-xl p-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Point Anda
                        </p>
                        <p class="text-xl font-bold text-blue-600 dark:text-blue-400 mt-1">
                            {{ $points }}
                        </p>
                    </div>

                    <div class="bg-gray-100 dark:bg-gray-700 rounded-xl p-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Potongan Kupon
                        </p>
                        <p class="text-xl font-bold text-green-600 dark:text-green-400 mt-1">
                            Rp {{ number_format($couponValue, 0, ',', '.') }}
                        </p>
                    </div>

                </div>

                {{-- REQUIREMENT --}}
                <div
                    class="bg-blue-50 dark:bg-blue-900/40 
                       border border-blue-200 dark:border-blue-700 
                       rounded-xl p-4 text-center">

                    <p class="text-blue-700 dark:text-blue-300 text-sm">
                        Dibutuhkan
                        <strong>{{ $requiredPoints }}</strong>
                        point untuk menukar kupon ini
                    </p>
                </div>


                {{-- ACTION --}}
                <form action="{{ route('member.coupons.redeem.store') }}" method="POST" class="space-y-3">
                    @csrf

                    <button type="submit"
                        class="w-full bg-blue-600 dark:bg-blue-500 text-white 
                           py-3 rounded-xl font-semibold 
                           hover:bg-blue-700 dark:hover:bg-blue-600 
                           transition disabled:opacity-50 disabled:cursor-not-allowed"
                        {{ $points < $requiredPoints ? 'disabled' : '' }}>

                        <i class="fa-solid fa-gift mr-2"></i>
                        Tukar Sekarang
                    </button>

                    @if ($points < $requiredPoints)
                        <p class="text-center text-sm text-red-500 dark:text-red-400">
                            Point Anda belum mencukupi
                        </p>
                    @endif

                </form>

            </div>
        </div>

    </div>

@endsection
