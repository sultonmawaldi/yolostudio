@extends('layouts.app')

@section('title', 'Member Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10 space-y-10">

    {{-- ✅ Notifikasi Kupon Baru --}}
    @if(session('new_coupon'))
        <div class="fixed top-4 right-4 z-50">
            <div class="bg-gradient-to-r from-blue-400 to-blue-500 text-white shadow-2xl rounded-2xl px-6 py-4 flex items-center space-x-3 animate-fade-in-up">
                <i class="bi bi-check-circle-fill text-2xl"></i>
                <span class="font-semibold text-lg">{{ session('new_coupon') }}</span>
            </div>
        </div>

        <script>
            setTimeout(() => document.querySelector('.fixed')?.remove(), 5000);
        </script>

        <style>
            @keyframes fade-in-up {
                0% { opacity: 0; transform: translateY(10px); }
                100% { opacity: 1; transform: translateY(0); }
            }
            .animate-fade-in-up { animation: fade-in-up 0.5s ease-out; }
        </style>
    @endif

  @if(request()->get('paid') === 'true' && request()->get('transaction_code'))
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    Swal.fire({
        icon: 'success',
        title: 'Pembayaran Berhasil!',
        text: 'Sisa pembayaran Anda telah dikonfirmasi.',
        confirmButtonText: 'Lihat Transaksi',
        confirmButtonColor: '#2563eb'
    }).then(() => {
        const code = "{{ request()->get('transaction_code') }}";
        const target = document.getElementById('trx-' + code);

        if (target) {
            setTimeout(() => {
                // ==== Scroll halus natural ====
                target.scrollIntoView({ behavior: 'smooth', block: 'center' });

                // ==== Efek highlight ====
                target.classList.add('ring-4', 'ring-blue-400', 'ring-offset-2');
                setTimeout(() => target.classList.remove('ring-4', 'ring-blue-400', 'ring-offset-2'), 4000);

                // ==== Ubah badge jadi "Paid" ====
                const badge = target.querySelector('span.px-3');
                if (badge) {
                    badge.textContent = 'Paid';
                    badge.className =
                        'px-3 py-1 text-xs font-semibold rounded-full shadow-sm bg-green-100 text-green-700 dark:bg-green-800/60 dark:text-green-200';
                }

                // ==== Animasi angka “Dibayar” ====
                const allSpans = target.querySelectorAll('div.flex.justify-between span.font-bold');
                if (allSpans.length >= 2) {
                    const paidEl = allSpans[0];
                    const totalEl = allSpans[1];
                    const totalText = totalEl.textContent.replace(/[^\d]/g, '');
                    const total = parseInt(totalText) || 0;
                    const startText = paidEl.textContent.replace(/[^\d]/g, '');
                    const startValue = parseInt(startText) || 0;

                    let current = startValue;
                    const animDuration = 800; // lebih cepat & halus
                    const startTimeAnim = performance.now();

                    function animateNumber(time) {
                        const elapsed = time - startTimeAnim;
                        const progress = Math.min(elapsed / animDuration, 1);
                        current = Math.floor(startValue + (total - startValue) * progress);
                        paidEl.textContent = 'Rp ' + current.toLocaleString('id-ID');
                        if (progress < 1) requestAnimationFrame(animateNumber);
                        else {
                            paidEl.classList.add('pulse-green');
                            setTimeout(() => paidEl.classList.remove('pulse-green'), 1800);
                        }
                    }
                    requestAnimationFrame(animateNumber);
                }

                // ==== Hapus sisa pembayaran & tombol bayar ====
                const sisaBox = target.querySelector('.bg-yellow-50');
                if (sisaBox) sisaBox.remove();
                const payBtn = target.querySelector('a[href*="pay_remaining"]');
                if (payBtn) payBtn.remove();

            }, 500);
        } else {
            window.location.href = "{{ route('member.transactions.index') }}";
        }
    });
});
</script>

<style>
/* 🌿 Efek pulse hijau di angka “Dibayar” */
@keyframes pulse-green {
  0% { color: #16a34a; text-shadow: 0 0 0 rgba(34,197,94,0.5); transform: scale(1); }
  50% { color: #22c55e; text-shadow: 0 0 12px rgba(34,197,94,0.7); transform: scale(1.08); }
  100% { color: inherit; text-shadow: none; transform: scale(1); }
}
.pulse-green {
  animation: pulse-green 1.6s ease-in-out;
}
</style>
@endif


@if(request()->get('pending') === 'true')
<script>
Swal.fire({
    icon: 'info',
    title: 'Menunggu Konfirmasi',
    text: 'Pembayaran Anda sedang diproses.',
    confirmButtonText: 'OK'
});
</script>
@endif

@if(request()->get('failed') === 'true')
<script>
Swal.fire({
    icon: 'error',
    title: 'Pembayaran Gagal!',
    text: 'Silakan coba lagi.',
    confirmButtonText: 'OK'
});
</script>
@endif




    {{-- 📊 RINGKASAN STATISTIK --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @php
            $cards = [
                ['icon' => 'fas fa-wallet', 'title' => 'Total Transaksi', 'value' => $transactions->count()],
                ['icon' => 'fas fa-ticket-alt', 'title' => 'Kupon Digunakan', 'value' => $usedCoupons ?? 0],
                ['icon' => 'fas fa-gift', 'title' => 'Kupon Aktif', 'value' => $coupons->count() ?? 0],
            ];
        @endphp

        @foreach($cards as $card)
            <div class="relative bg-gradient-to-br from-white via-blue-50 to-blue-100 dark:from-gray-800 dark:via-gray-900 dark:to-blue-950 p-6 rounded-2xl shadow-lg text-gray-900 dark:text-white transition-all hover:-translate-y-1 hover:shadow-2xl border border-blue-100/60 dark:border-gray-700 backdrop-blur-lg overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-br from-white/70 via-blue-50/60 to-blue-100/40 dark:from-gray-800/70 dark:via-gray-900/50 dark:to-blue-950/30 opacity-90 rounded-2xl"></div>
                <div class="relative z-10 flex items-center space-x-4">
                    <div class="w-14 h-14 flex items-center justify-center rounded-xl bg-blue-500/10 dark:bg-blue-400/20 text-3xl text-blue-600 dark:text-blue-300 group-hover:scale-110 transition-transform duration-300">
                        <i class="{{ $card['icon'] }}"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $card['title'] }}</p>
                        <p class="text-4xl font-extrabold text-gray-900 dark:text-white">{{ $card['value'] }}</p>
                    </div>
                </div>
                <div class="absolute -bottom-10 right-0 w-32 h-32 bg-gradient-to-tr from-blue-200/20 to-transparent rounded-full blur-2xl"></div>
            </div>
        @endforeach
    </div>

    {{-- 🎟️ KUPON AKTIF --}}
    <div class="bg-gradient-to-br from-white via-blue-50 to-blue-100 dark:from-gray-800 dark:via-gray-900 dark:to-blue-950 backdrop-blur-xl rounded-2xl shadow-lg border border-blue-100/50 dark:border-gray-700 p-6 transition">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">🎟️ Kupon Aktif Anda</h2>

        @if(isset($coupons) && $coupons->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($coupons as $coupon)
                    <div class="relative bg-gradient-to-r from-white via-blue-50 to-blue-100 dark:from-gray-800 dark:via-gray-900 dark:to-blue-950 text-gray-900 dark:text-white rounded-2xl shadow-md p-6 overflow-hidden group transition-all duration-300 hover:-translate-y-1 hover:shadow-xl coupon border border-blue-100/70 dark:border-gray-700">
                        <div class="absolute top-0 bottom-0 left-0 w-6 bg-gradient-to-b from-transparent via-white/40 to-transparent dark:via-gray-600/40 rounded-r-full"></div>
                        <div class="absolute top-0 bottom-0 right-0 w-6 bg-gradient-to-b from-transparent via-white/40 to-transparent dark:via-gray-600/40 rounded-l-full"></div>
                        <div class="absolute inset-y-0 left-1/2 transform -translate-x-1/2 w-[2px] border-dashed border-blue-300/50 dark:border-blue-500/30 border-l"></div>

                        <div class="relative z-10 grid grid-cols-2 items-center">
                            <div class="space-y-2">
                                <p class="text-xs uppercase tracking-widest font-semibold text-gray-500 dark:text-gray-400">Kode Kupon</p>
                                <p class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ $coupon->code }}</p>
                                <p class="text-sm text-gray-700 dark:text-gray-200">
                                    @if($coupon->type === 'fixed')
                                        💰 Rp {{ number_format($coupon->value, 0, ',', '.') }}
                                    @else
                                        💸 {{ $coupon->value }}%
                                    @endif
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Kadaluarsa: {{ $coupon->expiry_date ? \Carbon\Carbon::parse($coupon->expiry_date)->format('d M Y') : 'Tidak ada' }}
                                </p>
                            </div>

                            <div class="flex flex-col items-center justify-center space-y-2">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold border border-blue-300/40 dark:border-gray-600/60
                                    {{ $coupon->status === 'unused' 
                                        ? 'bg-blue-100 text-gray-900 dark:bg-blue-800 dark:text-blue-200' 
                                        : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">
                                    {{ $coupon->status === 'unused' ? 'Belum Digunakan' : 'Sudah Digunakan' }}
                                </span>
                                <button class="mt-2 bg-gradient-to-r from-blue-500/10 to-blue-300/20 hover:from-blue-500/20 hover:to-blue-400/30 text-gray-900 dark:text-white text-xs font-medium rounded-lg px-3 py-1 transition-all duration-200">
                                    Gunakan Sekarang
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-6 text-center bg-blue-50 dark:bg-blue-900/30 rounded-lg text-gray-700 dark:text-gray-300">
                Anda belum memiliki kupon aktif. 
            </div>
        @endif
    </div>

    {{-- 🧾 Riwayat Transaksi --}}
    @php use Illuminate\Support\Carbon; @endphp
    <div class="bg-gradient-to-br from-white via-blue-50 to-blue-100 dark:from-gray-900 dark:via-gray-950 dark:to-blue-950 rounded-3xl shadow-2xl border border-blue-100/50 dark:border-gray-800/70 p-6 md:p-8 backdrop-blur-lg overflow-hidden" x-data="{ open: true }">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight flex items-center gap-3">
                <i class="bi bi-credit-card text-blue-600 dark:text-blue-400 fs-3"></i>
                Riwayat Transaksi
            </h2>
            <button @click="open = !open" class="text-sm text-blue-600 dark:text-blue-400 font-medium hover:underline transition">
                <span x-show="open">Sembunyikan</span>
                <span x-show="!open">Tampilkan</span>
            </button>
        </div>

        <div x-show="open" x-transition>
            @if($transactions->isEmpty())
                <div class="p-8 bg-white/60 dark:bg-gray-800/50 text-gray-700 dark:text-gray-300 rounded-2xl text-center border border-blue-100/50 dark:border-gray-700 shadow-inner">
                    Belum ada transaksi yang tercatat.
                </div>
            @else
                <div class="grid gap-6 sm:gap-8 md:grid-cols-2 xl:grid-cols-3">
                    @foreach($transactions as $trx)
                        @php
                            $remaining = ($trx->payment_status === 'DP') ? max(($trx->total_amount ?? 0) - ($trx->amount ?? 0), 0) : 0;
                            $isFuture = Carbon::parse(optional($trx->appointment)->booking_date)->isFuture();
                        @endphp

                        <div id="trx-{{ $trx->transaction_code }}"class="group relative bg-white/80 dark:bg-gray-800/70 backdrop-blur-md rounded-2xl border border-blue-100/70 dark:border-gray-700 p-6 shadow-md hover:shadow-2xl transition-all duration-500 hover:-translate-y-1 overflow-hidden">
                            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 via-indigo-400 to-purple-500 rounded-t-2xl"></div>

                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white tracking-tight flex items-center gap-2">
                                        <i class="bi bi-receipt text-primary"></i>
                                        #{{ $trx->transaction_code }}
                                    </h3>
                                    @php
                                        $bookingDate = optional($trx->appointment)->booking_date;
                                        $bookingTime = optional($trx->appointment)->booking_time;
                                    @endphp
                                    <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                        <i class="bi bi-calendar3"></i>
                                        @if($bookingDate)
                                            {{ Carbon::parse($bookingDate)->format('d M Y') }} • {{ $bookingTime }}
                                        @else
                                            <span class="italic text-gray-400">Tanggal belum ditentukan</span>
                                        @endif
                                    </p>
                                </div>

                                <span class="px-3 py-1 text-xs font-semibold rounded-full shadow-sm
                                    @if($trx->payment_status === 'Paid') bg-green-100 text-green-700 dark:bg-green-800/60 dark:text-green-200
                                    @elseif($trx->payment_status === 'DP') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/50 dark:text-yellow-300
                                    @else bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 @endif">
                                    {{ $trx->payment_status }}
                                </span>
                            </div>

                            <div class="space-y-3 text-sm text-gray-600 dark:text-gray-300">
                                <div class="flex justify-between">
                                    <span><i class="bi bi-wallet2 text-primary"></i> Metode</span>
                                    <span class="font-medium">{{ $trx->payment_method ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span><i class="bi bi-cash-coin text-success"></i> Dibayar</span>
                                    <span class="font-bold">Rp {{ number_format($trx->amount ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span><i class="bi bi-currency-exchange text-primary"></i> Total</span>
                                    <span class="font-bold">Rp {{ number_format($trx->total_amount ?? 0, 0, ',', '.') }}</span>
                                </div>

                                @if($trx->payment_status === 'DP' && $remaining > 0)
                                    <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-xl px-4 py-3 flex justify-between items-center">
                                        <span class="flex items-center gap-2 text-sm font-medium text-yellow-700">
                                            <i class="bi bi-clock-history"></i> Sisa Pembayaran
                                        </span>
                                        <span class="font-semibold text-yellow-800">
                                            Rp {{ number_format($remaining, 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <div class="mt-6 pt-4 border-t border-blue-100/50 dark:border-gray-700 flex justify-between items-center flex-wrap gap-3">
                                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                                    <i class="bi bi-clipboard-check"></i>
                                    <span>Status Booking:</span>
                                    @php
                                        $status = $trx->appointment->status ?? '-';
                                    @endphp
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full shadow-sm
                                        @if($status === 'Confirmed') bg-green-100 text-green-700 dark:bg-green-800/60 dark:text-green-200
                                        @elseif($status === 'Pending') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/50 dark:text-yellow-300
                                        @elseif($status === 'Cancelled') bg-red-100 text-red-700 dark:bg-red-900/60 dark:text-red-200
                                        @else bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 @endif">
                                        {{ $status }}
                                    </span>
                                </div>

                                @if ($trx->payment_status === 'DP')
                                    <a href="{{ route('member.transactions.pay_remaining', $trx->id) }}"
                                       class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium bg-blue-500/10 text-blue-600 hover:bg-blue-500/20 transition-all">
                                       <i class="bi bi-wallet2"></i> Bayar Sisa
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>

<style>
.coupon::before, .coupon::after {
    content: '';
    position: absolute;
    top: 50%;
    width: 22px;
    height: 22px;
    background: white;
    border-radius: 50%;
    transform: translateY(-50%);
    z-index: 10;
}
.dark .coupon::before, .dark .coupon::after { background: #1f2937; }
.coupon::before { left: -11px; }
.coupon::after { right: -11px; }

.ring-blue-400 {
  transition: box-shadow 0.4s ease-in-out;
}

</style>
@endsection
