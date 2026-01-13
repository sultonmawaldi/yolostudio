@extends('layouts.app')

@section('title', 'Pelunasan Transaksi')

@section('content')
<div class="max-w-3xl mx-auto mt-10 p-6 bg-white dark:bg-gray-900 rounded-xl shadow-lg text-center">
    <h2 class="text-xl font-bold mb-4 text-blue-600 dark:text-blue-400">
        Pelunasan Booking #{{ $transaction->appointment->booking_id ?? '-' }}
    </h2>

    <div class="mb-3 text-gray-700 dark:text-gray-300">
        <p>Kode Transaksi: <strong>{{ $transaction->transaction_code }}</strong></p>
        <p>Status Pembayaran:
            <span class="px-3 py-1 rounded-full text-sm font-semibold 
                @if($transaction->payment_status === 'Paid') bg-green-100 text-green-700 
                @elseif($transaction->payment_status === 'DP') bg-yellow-100 text-yellow-700 
                @else bg-gray-100 text-gray-600 @endif">
                {{ $transaction->payment_status }}
            </span>
        </p>
    </div>

    <div class="border-t border-gray-200 dark:border-gray-700 my-4"></div>

    <p class="mb-3 text-gray-700 dark:text-gray-300">
        Total Tagihan: <strong>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</strong>
    </p>
    <p class="mb-3 text-gray-700 dark:text-gray-300">
        Sudah Dibayar: <strong>Rp {{ number_format($transaction->amount, 0, ',', '.') }}</strong>
    </p>
    <p class="mb-5 text-gray-700 dark:text-gray-300">
        Sisa Pembayaran:
        <strong>Rp {{ number_format(max($transaction->total_amount - $transaction->amount, 0), 0, ',', '.') }}</strong>
    </p>

    @if($transaction->payment_status !== 'Paid')
        <button id="pay-button" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg transition">
            Bayar Sekarang
        </button>
    @else
        <div class="bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 px-4 py-2 rounded-lg">
            ✅ Transaksi sudah lunas
        </div>
    @endif

    <a href="{{ route('member.transactions.index') }}" 
       class="block mt-5 text-gray-500 hover:text-blue-500 text-sm">
       ← Kembali ke Riwayat Transaksi
    </a>
</div>

{{-- ✅ SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- ✅ Midtrans Snap --}}
<script src="https://app.sandbox.midtrans.com/snap/snap.js"
    data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const payButton = document.getElementById('pay-button');
    if (!payButton) return;

    payButton.addEventListener('click', (e) => {
        e.preventDefault();

        window.snap.pay('{{ $snapToken }}', {
            onSuccess: function (result) {
                // 🔄 Tampilkan loading alert
                Swal.fire({
                    title: 'Memproses Pembayaran...',
                    text: 'Mohon tunggu sebentar, sistem sedang memverifikasi transaksi Anda.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => Swal.showLoading(),
                });

                // Kirim callback ke backend
                fetch("{{ route('midtrans.callback', ['transaction' => $transaction->id]) }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ midtrans_response: result })
                })
                .finally(() => {
                    // 🕒 Tambahkan jeda agar popup tidak langsung menutup
                    setTimeout(() => {
                        Swal.close();
                        // Redirect ke halaman finish (SweetAlert muncul di dashboard)
                        window.location.href = "{{ route('member.payment.finish', ['transaction' => $transaction->id]) }}";
                    }, 2000);
                });
            },
            onPending: function () {
                Swal.fire({
                    icon: 'info',
                    title: 'Menunggu Pembayaran',
                    text: 'Selesaikan pembayaran Anda di Midtrans.',
                    confirmButtonColor: '#2563eb'
                });
            },
            onError: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: 'Terjadi kesalahan saat memproses pembayaran.',
                    confirmButtonColor: '#dc2626'
                });
            },
            onClose: function () {
                console.log('Popup pembayaran ditutup tanpa menyelesaikan transaksi.');
            }
        });
    });
});
</script>
@endsection
