@extends('layouts.member')

@section('title', 'Riwayat Transaksi')

@section('member-content')
    <div class="max-w-7xl mx-auto px-4 py-10 space-y-10">

        @php use Illuminate\Support\Carbon; @endphp

        {{-- Riwayat Transaksi --}}
        <div class="bg-white rounded-2xl shadow p-6" x-data="{ open: true }">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold flex items-center gap-2">
                    <i class="bi bi-credit-card text-blue-600"></i> Riwayat Transaksi
                </h2>
                <button @click="open = !open" class="text-sm text-blue-600 font-medium hover:underline">
                    <span x-show="open">Sembunyikan</span>
                    <span x-show="!open">Tampilkan</span>
                </button>
            </div>

            <div x-show="open" x-transition>
                @if ($transactions->isEmpty())
                    <div class="p-6 bg-gray-50 text-gray-600 rounded-xl text-center border border-gray-200">
                        Belum ada transaksi yang tercatat.
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach ($transactions as $trx)
                            @php
                                $remaining =
                                    $trx->payment_status === 'DP'
                                        ? max(($trx->total_amount ?? 0) - ($trx->amount ?? 0), 0)
                                        : 0;
                                $bookingDate = optional($trx->appointment)->booking_date;
                                $bookingTime = optional($trx->appointment)->booking_time;
                                $status = $trx->appointment->status ?? '-';
                                $canReschedule =
                                    $trx->appointment &&
                                    $bookingDate &&
                                    (Carbon::parse($bookingDate)->isToday() || Carbon::parse($bookingDate)->isFuture());
                                $blockedStatus = ['Cancelled', 'Completed'];
                            @endphp

                            <div id="trx-{{ $trx->transaction_code }}"
                                class="relative bg-gradient-to-br from-indigo-50 to-blue-50 border border-blue-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition">

                                {{-- HEADER --}}
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h3 class="text-lg font-semibold flex items-center gap-2">
                                            <i class="bi bi-receipt text-blue-600"></i>
                                            #{{ $trx->transaction_code }}
                                        </h3>
                                        <p class="text-xs text-gray-500 flex items-center gap-1">
                                            <i class="bi bi-calendar3"></i>
                                            @if ($bookingDate)
                                                {{ Carbon::parse($bookingDate)->format('d M Y') }} • {{ $bookingTime }}
                                            @else
                                                <span class="italic text-gray-400">Tanggal belum ditentukan</span>
                                            @endif
                                        </p>
                                    </div>

                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full
            @if ($trx->payment_status === 'Paid') bg-green-100 text-green-700
            @elseif($trx->payment_status === 'DP') bg-yellow-100 text-yellow-700
            @else bg-gray-100 text-gray-600 @endif">
                                        {{ $trx->payment_status }}
                                    </span>
                                </div>

                                {{-- INFO PEMBAYARAN --}}
                                <div class="space-y-2 text-sm text-gray-700">
                                    <div class="flex justify-between">
                                        <span><i class="bi bi-wallet2"></i> Metode</span>
                                        <span class="font-medium">{{ $trx->payment_method ?? '-' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span><i class="bi bi-cash-coin"></i> Dibayar</span>
                                        <span class="font-bold text-blue-600">Rp
                                            {{ number_format($trx->amount ?? 0, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span><i class="bi bi-currency-exchange"></i> Total</span>
                                        <span class="font-bold text-blue-600">Rp
                                            {{ number_format($trx->total_amount ?? 0, 0, ',', '.') }}</span>
                                    </div>

                                    @if ($trx->payment_status === 'DP' && $remaining > 0)
                                        <div
                                            class="mt-3 bg-yellow-50 border border-yellow-200 rounded-xl px-3 py-2 flex justify-between items-center text-yellow-700">
                                            <span class="flex items-center gap-1"><i class="bi bi-clock-history"></i> Sisa
                                                Pembayaran</span>
                                            <span class="font-semibold text-yellow-800">Rp
                                                {{ number_format($remaining, 0, ',', '.') }}</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- STATUS & ACTION --}}
                                <div
                                    class="mt-4 pt-3 border-t border-blue-200 flex justify-between items-center flex-wrap gap-2 text-xs text-gray-700">
                                    <div class="flex items-center gap-1">
                                        <i class="bi bi-clipboard-check"></i>
                                        <span>Status Booking:</span>
                                        <span
                                            class="px-2 py-1 rounded-full
                @if ($status === 'Confirmed') bg-green-100 text-green-700
                @elseif($status === 'Pending') bg-yellow-100 text-yellow-700
                @elseif($status === 'Cancelled') bg-red-100 text-red-700
                @else bg-gray-100 text-gray-600 @endif">
                                            {{ $status }}
                                        </span>
                                    </div>

                                    @if ($trx->payment_status === 'DP')
                                        <a href="{{ route('member.transactions.pay_remaining', $trx->id) }}"
                                            class="px-2 py-1 rounded bg-blue-600 text-white hover:bg-blue-700 transition">
                                            Bayar Sisa
                                        </a>
                                    @endif

                                    @if ($canReschedule && !in_array($status, $blockedStatus))
                                        <button type="button"
                                            class="btn-reschedule px-2 py-1 rounded bg-blue-100 text-blue-600 hover:bg-blue-200 transition"
                                            data-appointment-id="{{ $trx->appointment->id }}"
                                            data-employee-id="{{ $trx->appointment->employee_id }}"
                                            data-service-id="{{ $trx->appointment->service_id }}"
                                            data-slot-group-id="{{ $trx->appointment->slot_group_id }}">
                                            Reschedule
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- Modal Reschedule --}}
    <div class="modal fade" id="rescheduleModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-2xl">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Reschedule Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body space-y-4">
                    @csrf
                    <input type="hidden" id="reschedule-appointment-id">

                    <div>
                        <label class="form-label fw-semibold">Tanggal Baru</label>
                        <input type="date" id="reschedule-date" class="form-control">
                    </div>

                    <div>
                        <label class="form-label fw-semibold">Pilih Jam</label>
                        <div id="reschedule-time-slots" class="d-flex flex-wrap gap-2">
                            <span class="text-muted">Pilih tanggal terlebih dahulu</span>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" id="reschedule-submit-btn">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Script Reschedule --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Open Modal & Reset
            document.querySelectorAll('.btn-reschedule').forEach(btn => {
                btn.addEventListener('click', function() {
                    const appointmentId = this.dataset.appointmentId;
                    document.getElementById('reschedule-appointment-id').value = appointmentId;
                    document.getElementById('reschedule-date').value = '';
                    const container = document.getElementById('reschedule-time-slots');
                    container.innerHTML =
                        '<span class="text-muted">Pilih tanggal terlebih dahulu</span>';

                    const modalEl = document.getElementById('rescheduleModal');
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();
                });
            });

            // Load Slots Saat Tanggal Diubah
            const dateInput = document.getElementById('reschedule-date');
            dateInput?.addEventListener('change', function() {
                const appointmentId = document.getElementById('reschedule-appointment-id')?.value;
                const date = this.value;
                if (!appointmentId || !date) return;
                loadRescheduleSlots(appointmentId, date);
            });

            function loadRescheduleSlots(appointmentId, date) {
                const container = document.getElementById('reschedule-time-slots');
                container.innerHTML = '<div class="text-muted">Memuat slot...</div>';

                fetch(`/appointments/${appointmentId}/reschedule/availability?date=${date}`)
                    .then(res => res.json())
                    .then(res => {
                        if (!res.success) {
                            container.innerHTML = `<div class="text-danger">${res.message}</div>`;
                            return;
                        }

                        container.innerHTML = '';
                        if (res.available_slots.length === 0) {
                            container.innerHTML =
                                '<div class="text-muted">Tidak ada slot tersedia di tanggal ini</div>';
                            return;
                        }

                        res.available_slots.forEach(slot => {
                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.className = 'btn btn-outline-primary btn-sm m-1 btn-reschedule-slot';
                            btn.textContent = slot.display;
                            btn.dataset.start = slot.start;
                            btn.dataset.end = slot.end;
                            btn.disabled = !!slot.is_booked;
                            if (slot.is_booked) btn.classList.add('disabled');

                            btn.addEventListener('click', function() {
                                document.querySelectorAll('.btn-reschedule-slot').forEach(b => b
                                    .classList.remove('active'));
                                btn.classList.add('active');
                            });

                            container.appendChild(btn);
                        });
                    })
                    .catch(() => container.innerHTML = '<div class="text-danger">Gagal memuat slot</div>');
            }

            // Submit Reschedule
            document.getElementById('reschedule-submit-btn')?.addEventListener('click', function() {
                const appointmentId = document.getElementById('reschedule-appointment-id')?.value;
                const date = document.getElementById('reschedule-date')?.value;
                const slot = document.querySelector('.btn-reschedule-slot.active');

                if (!appointmentId) return alert('Appointment tidak ditemukan');
                if (!date) return alert('Silakan pilih tanggal terlebih dahulu');
                if (!slot) return alert('Silakan pilih jam terlebih dahulu');

                fetch(`/appointments/${appointmentId}/reschedule`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            new_date: date,
                            new_start_time: slot.dataset.start,
                            new_end_time: slot.dataset.end
                        })
                    })
                    .then(async res => {
                        const data = await res.json();
                        if (!res.ok || !data.success) throw new Error(data.message ||
                            'Gagal reschedule');
                        return data;
                    })
                    .then(() => location.reload())
                    .catch(err => {
                        console.error(err);
                        alert(err.message);
                    });
            });

        });
    </script>

@endsection
