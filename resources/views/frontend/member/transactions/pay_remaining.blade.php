@extends('layouts.member')

@section('title', 'Pelunasan Transaksi')

@section('member-content')
    <div class="mx-auto">

        {{-- Card Utama --}}
        <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl p-6 sm:p-8 transition">

            {{-- Header --}}
            <div class="flex items-center justify-between flex-wrap gap-3 mb-6">
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">
                        Pelunasan Booking
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        #{{ $transaction->appointment->booking_id ?? '-' }}
                    </p>
                </div>

                {{-- Status Badge --}}
                <span
                    class="px-4 py-1.5 rounded-full text-xs font-semibold
                @if ($transaction->payment_status === 'Paid') bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300
                @elseif($transaction->payment_status === 'DP') bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300
                @else bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 @endif">
                    {{ $transaction->payment_status }}
                </span>
            </div>

            {{-- Divider --}}
            <div class="border-t border-gray-200 dark:border-gray-700 mb-6"></div>

            {{-- Info Transaksi --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm sm:text-base">

                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
                    <p class="text-gray-500 dark:text-gray-400 text-xs">Kode Transaksi</p>
                    <p class="font-semibold text-gray-800 dark:text-white">
                        {{ $transaction->transaction_code }}
                    </p>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
                    <p class="text-gray-500 dark:text-gray-400 text-xs">Total Tagihan</p>
                    <p class="font-semibold text-gray-800 dark:text-white">
                        Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                    </p>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
                    <p class="text-gray-500 dark:text-gray-400 text-xs">Sudah Dibayar</p>
                    <p class="font-semibold text-gray-800 dark:text-white">
                        Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                    </p>
                </div>

                <div class="bg-blue-50 dark:bg-blue-900/30 rounded-xl p-4">
                    <p class="text-blue-600 dark:text-blue-300 text-xs">Sisa Pembayaran</p>
                    <p class="font-bold text-lg text-blue-700 dark:text-blue-300">
                        Rp {{ number_format(max($transaction->total_amount - $transaction->amount, 0), 0, ',', '.') }}
                    </p>
                </div>

            </div>

            {{-- Action Button --}}
            <div class="mt-8">
                @if ($transaction->payment_status !== 'Paid')
                    <button id="pay-button"
                        class="w-full flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-3 rounded-xl transition shadow-md">
                        <i class="fa-solid fa-credit-card"></i>
                        Bayar Sekarang
                    </button>
                @else
                    <div
                        class="w-full flex items-center justify-center gap-2 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 px-4 py-3 rounded-xl font-semibold text-center">
                        <i class="fa-solid fa-circle-check"></i>
                        Transaksi sudah lunas
                    </div>
                @endif
            </div>

            {{-- Back --}}
            <div class="mt-6 text-center">
                <a href="{{ route('member.transactions.index') }}"
                    class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition no-underline">
                    <i class="fa-solid fa-arrow-left"></i>
                    Kembali ke Riwayat Transaksi
                </a>
            </div>

        </div>
    </div>


    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Midtrans Snap --}}
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const payButton = document.getElementById('pay-button');
            if (!payButton) return;

            payButton.addEventListener('click', (e) => {
                e.preventDefault();

                window.snap.pay('{{ $snapToken }}', {
                    onSuccess: function(result) {
                        Swal.fire({
                            title: 'Memproses Pembayaran...',
                            text: 'Mohon tunggu sebentar, sistem sedang memverifikasi transaksi Anda.',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => Swal.showLoading(),
                        });

                        fetch("{{ route('midtrans.callback', ['transaction' => $transaction->id]) }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                midtrans_response: result
                            })
                        }).finally(() => {
                            setTimeout(() => {
                                Swal.close();
                                window.location.href =
                                    "{{ route('member.payment.finish', ['transaction' => $transaction->id]) }}";
                            }, 1500);
                        });
                    },
                    onPending: function() {
                        Swal.fire({
                            icon: 'info',
                            title: 'Menunggu Pembayaran',
                            text: 'Selesaikan pembayaran Anda di Midtrans.',
                            confirmButtonColor: '#2563eb'
                        });
                    },
                    onError: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan saat memproses pembayaran.',
                            confirmButtonColor: '#dc2626'
                        });
                    }
                });
            });
        });
    </script>
@endsection
