@extends('layouts.member')

@section('title', 'Kode Kupon Saya')

@section('member-content')

    <div class="bg-white rounded-2xl shadow p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold">Kode Kupon Saya</h2>

            <a href="{{ route('member.coupons.redeem') }}"
                class="bg-gradient-to-r from-indigo-500 to-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:opacity-90">
                Tukar Point
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded-lg mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if ($coupons->isEmpty())
            <div class="text-center py-10 text-gray-500">
                <p class="text-lg">🎟️ Belum ada kupon</p>
                <p class="text-sm mt-1">Tukar point kamu untuk mendapatkan kupon</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach ($coupons as $coupon)
                    <div class="relative bg-gradient-to-br from-blue-50 to-white border rounded-2xl p-5 shadow-sm">

                        {{-- STATUS --}}
                        <span
                            class="absolute top-3 right-3 text-xs px-3 py-1 rounded-full
                        {{ $coupon->is_used
                            ? 'bg-gray-200 text-gray-600'
                            : ($coupon->expired_at && $coupon->expired_at < now()
                                ? 'bg-red-100 text-red-600'
                                : 'bg-green-100 text-green-600') }}">
                            {{ $coupon->is_used
                                ? 'Sudah Digunakan'
                                : ($coupon->expired_at && $coupon->expired_at < now()
                                    ? 'Expired'
                                    : 'Aktif') }}
                        </span>

                        {{-- HEADER --}}
                        <div class="flex items-center gap-4 mb-4">
                            <div
                                class="w-14 h-14 rounded-xl bg-gradient-to-r from-indigo-500 to-blue-600 flex items-center justify-center text-white text-xl">
                                🎫
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Kode Kupon</p>
                                <p class="font-bold text-lg tracking-wider">{{ $coupon->code }}</p>
                            </div>
                        </div>

                        {{-- VALUE --}}
                        <div class="mb-4">
                            <p class="text-sm text-gray-500">Nilai Kupon</p>
                            <p class="text-2xl font-bold text-blue-600">
                                Rp {{ number_format($coupon->value, 0, ',', '.') }}
                            </p>
                        </div>

                        {{-- INFO --}}
                        <div class="text-xs text-gray-500 space-y-1">
                            <p>Dibuat: {{ $coupon->created_at->format('d M Y') }}</p>

                            @if ($coupon->expired_at)
                                <p>Expired: {{ $coupon->expired_at->format('d M Y') }}</p>
                            @else
                                <p>Tidak ada masa expired</p>
                            @endif
                        </div>

                        {{-- ACTION --}}
                        @if (!$coupon->is_used && (!$coupon->expired_at || $coupon->expired_at >= now()))
                            <button onclick="copyCoupon('{{ $coupon->code }}')"
                                class="mt-4 w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 text-sm">
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
            navigator.clipboard.writeText(code);
            alert('Kode kupon berhasil disalin: ' + code);
        }
    </script>

@endsection
