@extends('layouts.member')

@section('title', 'Pelunasan Transaksi')

@section('member-content')
    <div class="max-w-3xl mx-auto mt-10 p-6 bg-white rounded-2xl shadow-lg">

        {{-- Header --}}
        <h2 class="text-2xl font-bold mb-4 text-blue-600">
            Pelunasan Booking #{{ $transaction->appointment->booking_id ?? '-' }}
        </h2>

        {{-- Info Transaksi --}}
        <div class="mb-6 space-y-2 text-gray-700">
            <p>Kode Transaksi: <strong>{{ $transaction->transaction_code }}</strong></p>
            <p>
                Status Pembayaran:
                <span
                    class="px-3 py-1 rounded-full text-sm font-semibold 
                @if ($transaction->payment_status === 'Paid') bg-green-100 text-green-700 
                @elseif($transaction->payment_status === 'DP') bg-yellow-100 text-yellow-700 
                @else bg-gray-100 text-gray-600 @endif">
                    {{ $transaction->payment_status }}
                </span>
            </p>
        </div>

        {{-- Divider --}}
        <div class="border-t border-gray-200 my-4"></div>

        {{-- Tagihan --}}
        <div class="space-y-2 text-gray-700">
            <p>Total Tagihan: <strong>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</strong></p>
            <p>Sudah Dibayar: <strong>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</strong></p>
            <p>Sisa Pembayaran: <strong>Rp
                    {{ number_format(max($transaction->total_amount - $transaction->amount, 0), 0, ',', '.') }}</strong></p>
        </div>

        {{-- Action Button --}}
        <div class="mt-6">
            @if ($transaction->payment_status !== 'Paid')
                <button id="pay-button"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-3 rounded-xl transition">
                    Bayar Sekarang
                </button>
            @else
                <div class="w-full bg-green-100 text-green-700 px-4 py-3 rounded-xl font-semibold text-center">
                    ✅ Transaksi sudah lunas
                </div>
            @endif
        </div>

        {{-- Kembali --}}
        <a href="{{ route('member.transactions.index') }}"
            class="block mt-6 text-center text-gray-500 hover:text-blue-600 text-sm">
            ← Kembali ke Riwayat Transaksi
        </a>

    </div>

    {{-- ✅ SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- ✅ Midtrans Snap --}}
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
                            })
                            .finally(() => {
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
                    },
                    onClose: function() {
                        console.log('Popup pembayaran ditutup tanpa menyelesaikan transaksi.');
                    }
                });
            });
        });
    </script>
@endsection
