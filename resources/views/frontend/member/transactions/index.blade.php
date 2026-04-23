@extends('layouts.member')

@section('title', 'Riwayat Transaksi')

@section('member-content')
    <div class="mx-auto">

        @php use Illuminate\Support\Carbon; @endphp

        {{-- Riwayat Transaksi --}}

        <div x-data="{ open: true }">

            {{-- CARD JUDUL --}}
            <div class="bg-white rounded-2xl shadow p-6 mb-6">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
                        Riwayat Transaksi
                    </h2>
                    <button @click="open = !open" class="text-sm text-blue-600 font-medium hover:underline">
                        <span x-show="open">Sembunyikan</span>
                        <span x-show="!open">Tampilkan</span>
                    </button>
                </div>
            </div>

            {{-- CARD ISI --}}
            <div x-show="open" x-transition
                class="bg-white dark:bg-gray-800 
           rounded-2xl shadow p-6 
           border border-gray-200 dark:border-gray-700">

                @if ($transactions->isEmpty())
                    <div
                        class="p-6 bg-gray-50 dark:bg-gray-900 
                   text-gray-600 dark:text-gray-400 
                   rounded-xl text-center 
                   border border-gray-200 dark:border-gray-700">
                        Belum ada transaksi yang tercatat.
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-2 gap-4 sm:gap-6">
                        @foreach ($transactions as $trx)
                            @php
                                $appointment = optional($trx->appointment);

                                $remaining =
                                    $trx->payment_status === 'DP'
                                        ? max(($trx->total_amount ?? 0) - ($trx->amount ?? 0), 0)
                                        : 0;

                                $bookingDate = $appointment->booking_date;
                                $startTime = $appointment->booking_start_time;
                                $endTime = $appointment->booking_end_time;
                                $status = $appointment->status ?? '-';
                                $peopleCount = $appointment->people_count ?? 1;

                                $canReschedule =
                                    $appointment &&
                                    $bookingDate &&
                                    Carbon::parse($bookingDate)->gt(now()->startOfDay());

                                $blockedStatus = ['Cancelled', 'Completed'];
                                $hasRescheduled = $appointment && $appointment->reschedule_count >= 1;
                            @endphp

                            <div
                                class="bg-white dark:bg-gray-900
                           border border-gray-200 dark:border-gray-700
                           rounded-2xl p-4 sm:p-5
                           shadow-sm hover:shadow-md
                           transition space-y-4">

                                {{-- HEADER --}}
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">

                                    <div class="min-w-0">
                                        <h3
                                            class="text-base sm:text-lg font-semibold 
                                       text-gray-800 dark:text-gray-100 truncate">
                                            #{{ $appointment->booking_id ?? $trx->transaction_code }}
                                        </h3>

                                        @if ($bookingDate)
                                            @php
                                                $formattedDate = \Carbon\Carbon::parse($bookingDate)->format('d M Y');
                                                $formattedStart = $startTime
                                                    ? \Carbon\Carbon::parse($startTime)->format('H:i')
                                                    : null;
                                                $formattedEnd = $endTime
                                                    ? \Carbon\Carbon::parse($endTime)->format('H:i')
                                                    : null;
                                            @endphp

                                            <p
                                                class="text-xs sm:text-sm 
                                           text-gray-500 dark:text-gray-400 
                                           mt-1 flex flex-wrap items-center gap-1">
                                                <i class="fa-regular fa-calendar text-[10px] sm:text-xs"></i>
                                                {{ $formattedDate }}

                                                @if ($formattedStart)
                                                    <span class="mx-1">•</span>
                                                    <i class="fa-regular fa-clock text-[10px] sm:text-xs"></i>
                                                    {{ $formattedStart }}
                                                    @if ($formattedEnd)
                                                        - {{ $formattedEnd }} WIB
                                                    @endif
                                                @endif
                                            </p>
                                        @endif
                                    </div>

                                    <div class="self-start sm:self-auto">
                                        <span
                                            class="inline-block px-3 py-1 text-[10px] sm:text-xs font-semibold rounded-full whitespace-nowrap
                                @if ($trx->payment_status === 'Paid') bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400
                                @elseif($trx->payment_status === 'DP')
                                    bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400
                                @else
                                    bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 @endif">
                                            {{ $trx->payment_status }}
                                        </span>
                                    </div>
                                </div>

                                {{-- INFO --}}
                                <div class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 space-y-2">


                                    {{-- Layanan --}}
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-500 dark:text-gray-400">Layanan</span>
                                        <span class="font-medium text-right">
                                            {{ $appointment->service->title ?? '-' }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-500 dark:text-gray-400">Jumlah Orang</span>
                                        <span class="font-medium text-right">
                                            {{ $peopleCount }} Orang
                                        </span>
                                    </div>

                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-500 dark:text-gray-400">Total</span>
                                        <span class="font-semibold text-blue-600 dark:text-blue-400 text-right">
                                            Rp {{ number_format($trx->total_amount ?? 0, 0, ',', '.') }}
                                        </span>
                                    </div>

                                    @if ($remaining > 0)
                                        <div
                                            class="flex flex-col sm:flex-row sm:justify-between sm:items-center
                                       bg-yellow-50 border border-yellow-200 text-yellow-700
                                       dark:bg-yellow-900/30 dark:border-yellow-700 dark:text-yellow-400
                                       rounded-lg px-3 py-2 gap-1">
                                            <span class="text-xs sm:text-sm">Sisa Pembayaran</span>
                                            <span class="font-semibold text-sm">
                                                Rp {{ number_format($remaining, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                {{-- FOOTER --}}
                                <div
                                    class="pt-3 border-t border-gray-200 dark:border-gray-700
                               flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 text-xs">

                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-gray-500 dark:text-gray-400">Status:</span>
                                        <span
                                            class="px-2 py-1 rounded-full text-[10px] sm:text-xs
                                @if ($status === 'Confirmed') bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400
                                @elseif($status === 'Pending')
                                    bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400
                                @elseif($status === 'Cancelled')
                                    bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400
                                @else
                                    bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 @endif">
                                            {{ $status }}
                                        </span>
                                    </div>

                                    <div class="flex flex-wrap gap-2 w-full sm:w-auto">

                                        @if ($trx->payment_status === 'DP')
                                            <a href="{{ route('member.transactions.pay_remaining', $trx->id) }}"
                                                class="flex-1 sm:flex-none text-center px-3 py-2 rounded-lg 
                                           bg-blue-600 text-white text-xs
                                           hover:bg-blue-700 transition
                                           no-underline hover:no-underline">
                                                Bayar Sisa
                                            </a>
                                        @endif

                                        @if ($canReschedule && !$hasRescheduled && !in_array($status, $blockedStatus))
                                            <button type="button"
                                                class="btn-reschedule flex-1 sm:flex-none text-center px-3 py-2 rounded-lg
                                           bg-blue-100 text-blue-600 hover:bg-blue-200
                                           dark:bg-blue-900/40 dark:text-blue-400 dark:hover:bg-blue-900/60
                                           transition text-xs"
                                                data-appointment-id="{{ $appointment->id }}"
                                                data-booking-date="{{ $bookingDate }}"
                                                data-booking-time-start="{{ $startTime }}"
                                                data-booking-time-end="{{ $endTime }}">
                                                Jadwal ulang
                                            </button>
                                        @elseif($hasRescheduled)
                                            <span
                                                class="flex-1 sm:flex-none text-center px-3 py-2 rounded
                                           bg-gray-100 text-gray-500
                                           dark:bg-gray-700 dark:text-gray-300 text-xs">
                                                Sudah di jadwal ulang
                                            </span>
                                        @endif

                                    </div>
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
                    <h5 class="modal-title fw-bold">Jadwal ulang pemesanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body space-y-4">
                    @csrf
                    <input type="hidden" id="reschedule-appointment-id">

                    {{-- Kalender --}}
                    <div class="card mb-3 shadow-sm border-0 rounded-4 modern-card">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center rounded-top-4">
                            <button class="btn btn-sm btn-light border-0 shadow-sm" id="res-prev-month">
                                <i class="bi bi-arrow-left-circle-fill modern-arrow"></i>
                            </button>
                            <h5 class="mb-0 fw-semibold text-dark" id="res-current-month"></h5>
                            <button class="btn btn-sm btn-light border-0 shadow-sm" id="res-next-month">
                                <i class="bi bi-arrow-right-circle-fill modern-arrow"></i>
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-calendar text-center align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Sen</th>
                                        <th>Sel</th>
                                        <th>Rab</th>
                                        <th>Kam</th>
                                        <th>Jum</th>
                                        <th>Sab</th>
                                        <th>Min</th>
                                    </tr>
                                </thead>
                                <tbody id="reschedule-calendar-body">
                                    <!-- Kalender akan digenerate JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Slot Waktu --}}
                    <div class="card shadow-sm border-0 rounded-3 modern-card">
                        <div class="card-header text-center py-3 rounded-top-3">
                            <h5 class="mb-1 fw-semibold bi bi-check2-square"> Slot Waktu Tersedia </h5>
                            <div id="res-selected-date-display" class="text-muted small">Pilih tanggal untuk melihat
                                slot
                            </div>
                        </div>

                        <div class="card-body">
                            <div id="reschedule-time-slots" class="d-flex flex-wrap justify-content-center gap-2">
                                <span class="text-muted w-100 text-center py-3">Pilih tanggal terlebih dahulu</span>
                            </div>
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
        window.bookingMode = 'booking';
        window.rescheduleAppointmentId = null;
        window.originalBookingDate = null;
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            let resCurrentMonth = new Date().getMonth();
            let resCurrentYear = new Date().getFullYear();
            let selectedResDate = null;

            // =============================
            // OPEN MODAL RESCHEDULE
            // =============================
            document.querySelectorAll('.btn-reschedule').forEach(btn => {
                btn.addEventListener('click', function() {

                    const appointmentId = this.dataset.appointmentId;
                    const bookingDate = this.dataset.bookingDate;
                    const bookingTimeStart = this.dataset.bookingTimeStart;
                    const bookingTimeEnd = this.dataset.bookingTimeEnd;

                    document.getElementById('reschedule-appointment-id').value = appointmentId;
                    window.originalBookingDate = bookingDate;
                    window.originalBookingTime = {
                        start: bookingTimeStart,
                        end: bookingTimeEnd
                    };

                    const d = bookingDate ? new Date(bookingDate) : new Date();
                    resCurrentMonth = d.getMonth();
                    resCurrentYear = d.getFullYear();
                    selectedResDate = bookingDate;

                    renderRescheduleCalendar(resCurrentMonth, resCurrentYear, bookingDate);

                    // Format tanggal rapi
                    document.getElementById('res-selected-date-display').textContent = bookingDate ?
                        `Dipilih: ${new Date(bookingDate).toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                  })}` :
                        'Pilih tanggal';

                    const modal = new bootstrap.Modal(document.getElementById('rescheduleModal'));
                    modal.show();
                });
            });

            // =============================
            // NAVIGASI BULAN
            // =============================
            document.getElementById('res-prev-month').addEventListener('click', function() {
                navigateResMonth(-1);
            });
            document.getElementById('res-next-month').addEventListener('click', function() {
                navigateResMonth(1);
            });

            function navigateResMonth(dir) {
                resCurrentMonth += dir;
                if (resCurrentMonth < 0) {
                    resCurrentMonth = 11;
                    resCurrentYear--;
                }
                if (resCurrentMonth > 11) {
                    resCurrentMonth = 0;
                    resCurrentYear++;
                }
                renderRescheduleCalendar(resCurrentMonth, resCurrentYear, selectedResDate);
            }

            // =============================
            // RENDER KALENDER RESCHEDULE
            // =============================
            function renderRescheduleCalendar(month, year, preselectedDate) {
                const tbody = document.getElementById('reschedule-calendar-body');
                tbody.innerHTML = '';
                const firstDay = new Date(year, month, 1);
                const lastDay = new Date(year, month + 1, 0);
                const daysInMonth = lastDay.getDate();
                const startingDay = (firstDay.getDay() + 6) % 7;

                const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus",
                    "September", "Oktober", "November", "Desember"
                ];
                document.getElementById('res-current-month').textContent = `${monthNames[month]} ${year}`;

                let date = 1;
                for (let i = 0; i < 6; i++) {
                    const row = document.createElement('tr');
                    for (let j = 0; j < 7; j++) {
                        if (i === 0 && j < startingDay) {
                            row.appendChild(document.createElement('td'));
                        } else if (date > daysInMonth) break;
                        else {
                            const cell = document.createElement('td');
                            cell.textContent = date;
                            const cellDate =
                                `${year}-${(month+1).toString().padStart(2,'0')}-${date.toString().padStart(2,'0')}`;
                            cell.dataset.date = cellDate;
                            cell.classList.add('calendar-day');

                            if (preselectedDate === cellDate) {
                                cell.classList.add('selected');
                            }

                            const today = new Date();
                            const cDate = new Date(year, month, date);
                            if (cDate < today.setHours(0, 0, 0, 0)) {
                                cell.classList.add('disabled');
                            }

                            cell.addEventListener('click', function() {
                                document.querySelectorAll('#reschedule-calendar-body .calendar-day')
                                    .forEach(c => c.classList.remove('selected'));
                                if (!cell.classList.contains('disabled')) {
                                    cell.classList.add('selected');
                                    selectedResDate = cell.dataset.date;

                                    document.getElementById('res-selected-date-display').textContent =
                                        `Dipilih: ${new Date(selectedResDate).toLocaleDateString('id-ID', {
                                    day: '2-digit',
                                    month: 'short',
                                    year: 'numeric'
                                })}`;

                                    loadRescheduleTimeSlots(selectedResDate);
                                }
                            });

                            row.appendChild(cell);
                            date++;
                        }
                    }
                    tbody.appendChild(row);
                }
            }

            // =============================
            // LOAD SLOT WAKTU
            // =============================
            function loadRescheduleTimeSlots(date) {
                const appointmentId = document.getElementById('reschedule-appointment-id').value;
                const container = document.getElementById('reschedule-time-slots');
                container.innerHTML =
                    `<div class="text-center w-100 py-4"><div class="spinner-border text-primary"></div> Memuat slot...</div>`;

                fetch(`/appointments/${appointmentId}/reschedule/availability?date=${date}`)
                    .then(res => res.json())
                    .then(res => {
                        container.innerHTML = '';
                        if (!res.success || res.available_slots.length === 0) {
                            container.innerHTML =
                                `<div class="text-muted w-100 text-center py-3">Tidak ada slot tersedia</div>`;
                            return;
                        }

                        res.available_slots.forEach(slot => {
                            const div = document.createElement('div');
                            const isCurrentBookingSlot = window.originalBookingTime?.start === slot
                                .start &&
                                window.originalBookingTime?.end === slot.end;

                            div.className = 'time-slot btn m-1';
                            div.textContent = slot.display;
                            div.dataset.start = slot.start;
                            div.dataset.end = slot.end;

                            if (slot.is_booked && !isCurrentBookingSlot) {
                                div.classList.add('btn-outline-secondary', 'disabled');
                                div.disabled = true;
                            } else {
                                div.classList.add('btn-outline-primary');
                            }

                            if (slot.is_old) {
                                div.classList.add('active', 'btn-warning', 'text-dark');
                            }

                            div.addEventListener('click', function() {
                                if (div.disabled) return;
                                document.querySelectorAll('#reschedule-time-slots .time-slot')
                                    .forEach(b => b.classList.remove('selected', 'active'));
                                div.classList.add('selected', 'active');
                            });

                            container.appendChild(div);
                        });
                    })
                    .catch(() => container.innerHTML =
                        `<div class="text-danger w-100 text-center py-3">Gagal memuat slot</div>`);
            }

            // =============================
            // SUBMIT RESCHEDULE (GANTI ALERT -> SWEETALERT2)
            // =============================
            document.getElementById('reschedule-submit-btn').addEventListener('click', function() {
                const appointmentId = document.getElementById('reschedule-appointment-id').value;
                const slot = document.querySelector('#reschedule-time-slots .time-slot.selected');
                const date = selectedResDate;

                if (!appointmentId) return Swal.fire('Error', 'Appointment tidak ditemukan', 'error');
                if (!date) return Swal.fire('Error', 'Silakan pilih tanggal', 'warning');
                if (!slot) return Swal.fire('Error', 'Silakan pilih slot', 'warning');

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
                    .then(res => res.json())
                    .then(res => {
                        if (!res.success) return Swal.fire('Gagal', res.message || 'Gagal jadwal ulang',
                            'error');

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message || 'Jadwal ulang berhasil!',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload();
                        });
                    })
                    .catch(() => Swal.fire('Error', 'Terjadi kesalahan server', 'error'));
            });

        });
    </script>


    <style>
        /* Selalu tampilkan scrollbar di halaman agar layout stabil */
        html {
            overflow-y: scroll;
            scrollbar-gutter: stable;
        }

        /* Saat modal terbuka, sembunyikan scroll halaman utama */
        body.modal-open {
            overflow: hidden !important;
            padding-right: 0 !important;
            /* mencegah layout shift */
        }

        /* Pastikan modal bisa scroll jika konten tinggi */
        .modal {
            overflow-y: auto;
            /* scrollbar muncul hanya di modal */
            -webkit-overflow-scrolling: touch;
            /* smooth scroll untuk mobile */
        }

        /* Opsional: batasi tinggi modal supaya scrollbar muncul jika terlalu tinggi */
        .modal-dialog {
            max-height: 90vh;
            /* modal tidak lebih tinggi dari 90% viewport */
        }

        .modal-content {
            max-height: 90vh;
            overflow-y: auto;
            /* scroll di dalam modal jika konten panjang */
        }

        /* Slot waktu */
        #reschedule-time-slots .time-slot {
            min-width: 90px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        /* Slot dipilih / aktif */
        #reschedule-time-slots .time-slot.selected,
        #reschedule-time-slots .time-slot.active {
            border-width: 2px !important;
            font-weight: 600;
        }

        /* Slot lama (booking sebelumnya) */
        #reschedule-time-slots .time-slot.btn-warning {
            position: relative;
            background-color: #fcd34d !important;
            /* kuning */
            color: #1f2937 !important;
            /* teks gelap */
            border-color: #fbbf24 !important;
        }

        /* Tambahkan icon jam kecil di pojok atas kanan slot lama */
        #reschedule-time-slots .time-slot.btn-warning::after {
            content: "\f017";
            /* unicode FontAwesome untuk fa-clock */
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            /* solid style */
            position: absolute;
            top: 2px;
            right: 4px;
            font-size: 10px;
            color: #1f2937;
        }

        /* Slot bentrok / sudah penuh */
        #reschedule-time-slots .time-slot.disabled {
            cursor: not-allowed;
            opacity: 0.6;
        }

        /* Hover untuk slot yang bisa dipilih */
        #reschedule-time-slots .time-slot.btn-outline-primary:hover {
            background-color: #3b82f6 !important;
            color: #fff !important;
        }
    </style>
@endsection
