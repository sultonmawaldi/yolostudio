@extends('layouts.member')

@section('title', 'Kode Kupon Saya')

@section('member-content')

    <div class="space-y-6">

        {{-- HEADER CARD --}}
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 px-6 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
                Kode Kupon Saya
            </h2>

            <a href="{{ route('member.coupons.redeem') }}"
                class="inline-flex items-center justify-center gap-2 
                   bg-gradient-to-r from-indigo-500 to-blue-600 
                   text-white px-4 py-2 rounded-xl text-sm 
                   hover:opacity-90 transition no-underline w-full sm:w-auto">
                <i class="fa-solid fa-gift text-sm"></i>
                Tukar Point
            </a>
        </div>


        {{-- SUCCESS MESSAGE --}}
        @if (session('success'))
            <div class="bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 p-4 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif


        {{-- EMPTY STATE --}}
        @if ($coupons->isEmpty())
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 text-center py-12 text-gray-500 dark:text-gray-400">

                <p class="text-lg font-medium">
                    <i class="fa-solid fa-ticket mr-2"></i> Belum ada kupon
                </p>

                <p class="text-sm mt-2">
                    Tukar point kamu untuk mendapatkan kupon
                </p>
            </div>
        @else
            {{-- GRID --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-2 gap-6">
                @foreach ($coupons as $coupon)
                    <div
                        class="relative bg-white dark:bg-gray-800 
                           border border-gray-200 dark:border-gray-700 
                           rounded-2xl p-5 shadow-sm transition hover:shadow-md">

                        {{-- STATUS --}}
                        <span
                            class="absolute top-3 right-3 text-xs px-3 py-1 rounded-full whitespace-nowrap
                            {{ $coupon->status === 'used'
                                ? 'bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-300'
                                : ($coupon->expiry_date && $coupon->expiry_date < now()
                                    ? 'bg-red-100 text-red-600 dark:bg-red-900/40 dark:text-red-400'
                                    : 'bg-green-100 text-green-600 dark:bg-green-900/40 dark:text-green-400') }}">

                            {{ $coupon->status === 'used'
                                ? 'Sudah Digunakan'
                                : ($coupon->expiry_date && $coupon->expiry_date < now()
                                    ? 'Expired'
                                    : 'Aktif') }}
                        </span>

                        {{-- HEADER --}}
                        <div class="flex items-start gap-4 mb-4 flex-col sm:flex-row">

                            <div
                                class="w-14 h-14 rounded-xl 
                                   bg-gradient-to-r from-indigo-500 to-blue-600 
                                   flex items-center justify-center 
                                   text-white text-xl shadow shrink-0">
                                <i class="fa-solid fa-ticket"></i>
                            </div>

                            <div class="min-w-0">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Kode Kupon
                                </p>

                                <p
                                    class="font-bold text-lg tracking-wider 
                                      text-gray-800 dark:text-white 
                                      break-all">
                                    {{ $coupon->code }}
                                </p>
                            </div>
                        </div>

                        {{-- VALUE --}}
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Nilai Kupon
                            </p>

                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                Rp {{ number_format($coupon->value, 0, ',', '.') }}
                            </p>
                        </div>

                        {{-- INFO --}}
                        <div class="text-xs text-gray-500 dark:text-gray-400 space-y-1">
                            <p>Dibuat: {{ $coupon->created_at->format('d M Y') }}</p>

                            @if ($coupon->expiry_date)
                                <p>Expired: {{ \Carbon\Carbon::parse($coupon->expiry_date)->format('d M Y') }}</p>
                            @else
                                <p>Tidak ada masa expired</p>
                            @endif
                        </div>

                        {{-- ACTION --}}
                        @if ($coupon->status !== 'used' && (!$coupon->expiry_date || $coupon->expiry_date >= now()))
                            <button onclick="copyCoupon('{{ $coupon->code }}')"
                                class="mt-5 w-full bg-blue-600 hover:bg-blue-700 
                                   text-white py-2.5 rounded-xl text-sm 
                                   transition font-medium">
                                <i class="fa-solid fa-copy mr-1"></i>
                                Salin Kode
                            </button>
                        @endif

                    </div>
                @endforeach
            </div>

        @endif
    </div>


    {{-- COPY SCRIPT --}}
    <script>
        function copyCoupon(code) {
            navigator.clipboard.writeText(code).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Kode kupon berhasil disalin',
                    showConfirmButton: false,
                    timer: 1800
                });
            }).catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Tidak bisa menyalin kode',
                });
            });
        }
    </script>

@endsection
