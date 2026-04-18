@extends('adminlte::page')

@section('title', 'Daftar Janji Temu')

@section('content_header')

    <div class="page-title-wrapper text-center mb-4">
        <h1 class="page-title">
            <i class="fa fa-list-ul me-2"></i>
            Daftar Janji Temu
        </h1>
        <div class="title-divider"></div>
    </div>

@stop


@section('content')
    <!-- Modal Detail Janji Temu -->
    <form id="appointmentStatusForm" method="POST" action="{{ route('appointments.update.status') }}">
        @csrf
        <input type="hidden" name="appointment_id" id="modalAppointmentId">

        <div class="modal fade" id="appointmentModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <!-- Header -->
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-calendar-check me-2"></i> Detail Janji Temu
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body">

                        <!-- Booking ID -->
                        <div class="text-center mb-3">
                            <span class="booking-id" id="modalBookingId">ID Pemesanan : -</span>
                        </div>

                        <!-- Informasi Utama -->
                        <div class="detail-section">
                            <div class="detail-row"><span>Pengguna</span><strong id="modalAppointmentName">-</strong></div>
                            <div class="detail-row"><span>Email</span><strong id="modalEmail">-</strong></div>
                            <div class="detail-row"><span>Telepon</span><strong id="modalPhone">-</strong></div>
                            <div class="detail-row"><span>Karyawan</span><strong id="modalEmployee">-</strong></div>
                            <div class="detail-row"><span>Layanan</span><strong id="modalService">-</strong></div>
                            <div class="detail-row" id="backgroundRow" style="display:none;">
                                <span>Background</span><strong id="modalBackground">-</strong>
                            </div>
                            <div class="detail-row"><span>Jumlah Orang</span><strong id="modalPeopleCount">-</strong></div>
                            <div class="detail-row"><span>Tanggal</span><strong id="modalDate">-</strong></div>
                            <div class="detail-row">
                                <span>Waktu</span>
                                <strong><span id="modalStartTime">-</span> - <span id="modalEndTime">-</span></strong>
                            </div>
                            <div class="detail-row"><span>Harga Layanan</span><strong id="modalServicePrice">-</strong>
                            </div>
                        </div>

                        <!-- Addons -->
                        <div class="mt-4">
                            <label class="section-title">Layanan Tambahan</label>
                            <div id="modalAddons" class="addons-box">
                                <em class="text-muted">Tidak ada add on</em>
                            </div>
                        </div>

                        <div class="detail-row total-row">
                            <span>Total Biaya</span>
                            <strong id="modalAmount">-</strong>
                        </div>

                        <!-- Pembayaran -->
                        <div id="paymentSection" style="display:none;">

                            <div class="detail-row total-row">
                                <span id="paymentLabel">DP Dibayar</span>
                                <strong id="modalDpAmount" class="text-success">-</strong>
                            </div>

                            <div class="detail-row total-row" id="remainingRow">
                                <span>Sisa Pembayaran</span>
                                <strong id="modalRemaining" class="text-danger">-</strong>
                            </div>

                        </div>

                        <!-- Catatan -->
                        <div class="mt-4">
                            <label class="section-title">Catatan</label>
                            <div id="modalNotes" class="notes-box">-</div>
                        </div>

                        <!-- Status -->
                        <!-- JUDUL DI LUAR BOX -->
                        <label class="section-title mt-4">Status Saat Ini</label>

                        <div class="status-section">

                            <!-- BOX STATUS -->
                            <div id="modalStatusBadge" class="current-status-box">
                                <!-- Badge muncul di sini -->
                            </div>

                            <div class="divider-status"></div>

                            <!-- UBAH STATUS -->
                            <label class="change-status-label">Ubah Status</label>
                            <select name="status" id="modalStatusSelect">
                                <option value="Pending">Menunggu</option>
                                <option value="Processing">Diproses</option>
                                <option value="Confirmed">Dikonfirmasi</option>
                                <option value="Completed">Selesai</option>
                                <option value="Cancelled">Dibatalkan</option>
                                <option value="Rescheduled">Jadwal Ulang</option>
                                <option value="On Hold">Ditahan</option>
                                <option value="No Show">Tidak Hadir</option>
                            </select>

                        </div>

                    </div>

                    <!-- Footer -->
                    <div class="modal-footer custom-footer">

                        <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">
                            <i class="fa fa-times me-1"></i>Tutup
                        </button>

                        <button type="button" id="btnReschedule" class="btn btn-warning rounded-pill">
                            <i class="fas fa-calendar-alt me-1"></i>
                            Jadwal Ulang
                        </button>

                        <button type="submit" class="btn btn-gradient-success rounded-pill">
                            <i class="fas fa-save me-1"></i>
                            Simpan Perubahan
                        </button>

                    </div>

                </div>
            </div>
        </div>
    </form>



    {{-- ================= MODAL RESCHEDULE ================= --}}
    <div class="modal fade" id="rescheduleModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-2xl">

                <div class="modal-header">
                    <h5 class="modal-title d-flex align-items-center">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        <span class="fw-semibold">Jadwal Ulang Janji Temu</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body space-y-4">
                    @csrf
                    <input type="hidden" id="reschedule-appointment-id">

                    <!-- Kalender -->
                    <div class="card mb-3 shadow-sm border-0 rounded-4 modern-card">
                        <div class="card-header bg-light calendar-header-grid rounded-top-4">

                            <button class="btn btn-sm btn-light border-0 shadow-sm" id="res-prev-month">
                                <i class="bi bi-arrow-left-circle-fill modern-arrow"></i>
                            </button>

                            <h5 class="mb-0 fw-semibold text-dark text-center" id="res-current-month"></h5>

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

                    <!-- Slot Waktu -->
                    <div class="card shadow-sm border-0 rounded-3 modern-card">
                        <div class="card-header text-center py-3 rounded-top-3">
                            <h5 class="mb-1 fw-semibold bi bi-check2-square">Slot Waktu Tersedia</h5>
                            <div id="res-selected-date-display" class="text-muted small">
                                Pilih tanggal untuk melihat slot
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="reschedule-time-slots" class="d-flex flex-wrap justify-content-center gap-2">
                                <span class="text-muted w-100 text-center py-3">Pilih tanggal terlebih dahulu</span>
                            </div>
                        </div>
                    </div>
                    <!-- Legend -->
                    <div class="mt-3 text-center small">
                        <span class="badge bg-primary">Tersedia</span>
                        <span class="badge bg-warning text-dark">Slot Saat Ini</span>
                        <span class="badge bg-secondary">Sudah Dibooking</span>
                    </div>
                </div>



                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" id="reschedule-submit-btn">Simpan Perubahan</button>
                </div>

            </div>
        </div>
    </div>


    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">

                        <!-- ================= FILTER BAR ================= -->
                        <div class="filter-wrapper mb-1">

                            <!-- Row 1 : Karyawan & Layanan -->
                            <div class="row align-items-end g-3">

                                <!-- Karyawan -->
                                <div class="col-md-4 mb-3">
                                    <label class="filter-label">
                                        <i class="fas fa-users me-2"></i>
                                        Pilih Karyawan
                                    </label>

                                    <select id="filterEmployee" class="form-control filter-select mt-2">
                                        @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('moderator'))
                                            <option value="">Semua Karyawan</option>
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->id }}">
                                                    {{ $employee->user->name ?? '-' }}
                                                </option>
                                            @endforeach
                                        @elseif(auth()->user()->hasRole('employee'))
                                            <option value="{{ auth()->user()->employee->id }}" selected>
                                                {{ auth()->user()->name }}
                                            </option>
                                        @endif
                                    </select>
                                </div>
                                <!-- Layanan -->
                                <div class="col-md-4 mb-3">
                                    <label class="filter-label">
                                        <i class="fas fa-briefcase me-2"></i>
                                        Pilih Layanan
                                    </label>

                                    <select id="filterService" class="form-control filter-select mt-2">
                                        <option value="">Semua Layanan</option>
                                        @foreach ($services as $service)
                                            <option value="{{ $service->id }}">
                                                {{ $service->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>


                            <!-- Tanggal Section -->
                            <div class="mt-2 mb-1">

                                <div class="date-title mb-3">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    Filter Jadwal
                                </div>

                                <div class="d-flex flex-wrap gap-1">

                                    <button type="button"
                                        class="btn btn-outline-primary btn-sm uniform-btn date-filter-btn active"
                                        data-value="">
                                        Semua
                                    </button>

                                    <button type="button"
                                        class="btn btn-outline-primary btn-sm uniform-btn date-filter-btn"
                                        data-value="upcoming">
                                        Akan Datang
                                    </button>

                                    <button type="button"
                                        class="btn btn-outline-primary btn-sm uniform-btn date-filter-btn"
                                        data-value="past">
                                        Sudah Lewat
                                    </button>

                                    <button type="button"
                                        class="btn btn-outline-primary btn-sm uniform-btn date-filter-btn"
                                        data-value="range">
                                        Rentang Tanggal
                                    </button>

                                </div>

                                <div class="mt-3">
                                    <input type="text" id="dateRangePicker"
                                        class="form-control form-control-sm d-none date-range-input"
                                        placeholder="Pilih rentang tanggal...">
                                </div>

                            </div>

                        </div>
                        <!-- ================= END FILTER BAR ================= -->


                        <div class="card-body p-0">
                            <div class="table-responsive table-scroll">
                                <table id="myTable" class="table">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Pengguna</th>
                                            <th>Layanan</th>
                                            <th>Karyawan</th>
                                            <th>Tanggal</th>
                                            <th>Waktu</th>
                                            <th class="text-center">Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $normalizeStatus = function ($status) {
                                                return ucwords(str_replace('_', ' ', strtolower($status)));
                                            };

                                            // ================= WARNA STATUS =================
                                            $statusColors = [
                                                'Pending' => '#f39c12',
                                                'Processing' => '#3498db',
                                                'Confirmed' => '#2ecc71',
                                                'Cancelled' => '#e74c3c',
                                                'Completed' => '#16a085',
                                                'Rescheduled' => '#9b59b6',
                                                'On Hold' => '#7f8c8d',
                                                'No Show' => '#e67e22',
                                            ];

                                            // ================= LABEL INDONESIA =================
                                            $statusLabel = [
                                                'Pending' => 'Menunggu',
                                                'Processing' => 'Diproses',
                                                'Confirmed' => 'Dikonfirmasi',
                                                'Cancelled' => 'Dibatalkan',
                                                'Completed' => 'Selesai',
                                                'Rescheduled' => 'Jadwal Ulang',
                                                'On Hold' => 'Ditahan',
                                                'No Show' => 'Tidak Hadir',
                                            ];

                                            $appointments = $appointments->sortBy([
                                                ['booking_date', 'asc'],
                                                ['booking_start_time', 'asc'],
                                            ]);
                                        @endphp

                                        @foreach ($appointments as $appointment)
                                            <tr data-service-id="{{ $appointment->service->id }}"
                                                data-employee-id="{{ $appointment->employee_id }}">
                                                <td data-label="#"> {{ $loop->iteration }} </td>
                                                <td data-label="Pengguna">
                                                    <div class="font-weight-semibold text-dark">{{ $appointment->name }}
                                                    </div>
                                                </td>
                                                <td data-label="Layanan">{{ $appointment->service->title ?? 'N/A' }}</td>
                                                <td data-label="Karyawan">{{ $appointment->employee->user->name }}</td>
                                                <td data-label="Tanggal" data-date="{{ $appointment->booking_date }}"
                                                    data-order="{{ $appointment->booking_date }}">
                                                    {{ \Carbon\Carbon::parse($appointment->booking_date)->translatedFormat('l, d M Y') }}
                                                </td>
                                                <td data-label="Waktu">
                                                    {{ \Carbon\Carbon::createFromFormat('H:i:s', $appointment->booking_start_time)->format('H:i') }}
                                                    WIB
                                                </td>
                                                <td data-label="Status" class="text-center">
                                                    @php
                                                        $status = $normalizeStatus($appointment->status);
                                                        $color = $statusColors[$status] ?? '#7f8c8d';
                                                    @endphp

                                                    <span class="status-badge"
                                                        style="background-color: {{ $color }};">
                                                        {{ $statusLabel[$status] ?? $status }}
                                                    </span>
                                                </td>
                                                <td data-label="Aksi">
                                                    <button
                                                        class="btn btn-sm btn-outline-primary rounded-pill view-appointment-btn"
                                                        data-bs-toggle="modal" data-bs-target="#appointmentModal"
                                                        data-id="{{ $appointment->id }}"
                                                        data-booking="{{ $appointment->booking_id }}"
                                                        data-name="{{ $appointment->name }}"
                                                        data-service="{{ $appointment->service->title ?? 'N/A' }}"
                                                        data-service_price="{{ $appointment->service->price ?? 0 }}"
                                                        data-background="{{ optional($appointment->background)->name }}"
                                                        data-email="{{ $appointment->email }}"
                                                        data-phone="{{ $appointment->phone }}"
                                                        data-people="{{ $appointment->people_count ?? '-' }}"
                                                        data-employee="{{ $appointment->employee->user->name }}"
                                                        data-date="{{ \Carbon\Carbon::parse($appointment->booking_date)->locale('id')->translatedFormat('l, d M Y') }}"
                                                        data-start_time="{{ \Carbon\Carbon::createFromFormat('H:i:s', $appointment->booking_start_time)->format('H:i') }}"
                                                        data-end_time="{{ \Carbon\Carbon::createFromFormat('H:i:s', $appointment->booking_end_time)->format('H:i') }} WIB"
                                                        data-amount="{{ $appointment->transaction->total_amount ?? 0 }}"
                                                        data-dp="{{ $appointment->transaction->amount ?? 0 }}"
                                                        data-payment-type="{{ $appointment->transaction
                                                            ? ($appointment->transaction->amount == 0
                                                                ? 'unpaid'
                                                                : ($appointment->transaction->amount < $appointment->transaction->total_amount
                                                                    ? 'dp'
                                                                    : 'full'))
                                                            : 'unpaid' }}"
                                                        data-notes="{{ $appointment->notes }}"
                                                        data-status="{{ $appointment->status }}"
                                                        data-addons='@json($appointment->addonData)'>

                                                        <i class="fas fa-eye"></i> Lihat
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>

                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@stop

@section('css')
    <style>
        /* ======== Filter Seragam ======== */
        #filterDate,
        #filterEmployee,
        #filterService,
        #dateRangePicker,
        .filter-select {
            border-radius: 50px;
            padding: 0.45rem 1rem;
            font-size: 0.9rem;
            background: #f8f9fa;
            border: 1px solid #e2e6ea;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            color: #495057;
            transition: all 0.3s ease;
        }

        #filterDate:focus,
        #filterEmployee:focus,
        #filterService:focus,
        #dateRangePicker:focus {
            box-shadow: 0 0 0 0.25rem rgba(108, 117, 125, 0.3);
            background-color: #fff;
            border-color: #6abfe3;
        }

        #filterDate:hover,
        #filterEmployee:hover,
        #filterService:hover,
        #dateRangePicker:hover {
            background-color: #fff;
            border-color: #6abfe3;
        }

        /* ======== Tabel Versi Desktop ======== */
        #myTable {
            border-radius: 10px;
            overflow: hidden;
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        /* --- GRADIENT FULL HEADER, BUKAN PER KOLOM --- */
        #myTable thead {
            background: linear-gradient(90deg, #007bff, #00b4d8);
        }

        #myTable thead th {
            color: #fff;
            font-size: 13px;
            text-transform: uppercase;
            padding: 14px 12px;
            letter-spacing: 0.5px;
            white-space: nowrap;
            text-align: center;
            border-bottom: 2px solid rgba(0, 123, 255, 0.25);

            /* hilangkan bg default tiap kolom */
            background: transparent !important;
        }

        /* Isi Tabel */
        #myTable td {
            font-size: 14px;
            padding: 12px;
        }


        #myTable tbody tr:hover {
            background-color: #f7faff;
            transition: 0.25s ease;
        }


        /* ======== Animasi Ringan ======== */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #myTable tr {
            animation: fadeIn 0.3s ease forwards;
        }

        /* ===== Modal Premium Ukuran Sedang & Rapi ===== */
        .modal-dialog {
            max-width: 600px !important;
            margin: 1.5rem auto;
        }

        .modal-content {
            border-radius: 16px !important;
            background: #ffffff;
            overflow: hidden;
            box-shadow: 0 6px 22px rgba(0, 0, 0, 0.08);
            transition: all 0.25s ease-in-out;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
        }

        .modal-body {
            overflow-y: auto;
            padding: 1.2rem 1.5rem;
            background-color: #f9fafc;
            flex: 1;
        }

        .modal-header {
            background: linear-gradient(135deg, #007bff, #00b4d8);
            color: #fff;
            border-bottom: none;
            text-align: center;
        }

        .modal-title {
            font-size: 1.1rem;
            font-weight: 600;
        }

        .modal-footer {
            background: #fff;
            border-top: none;
            padding: 0.8rem 1.2rem;
        }



        /* Catatan */
        #modalNotes {
            min-height: 60px;
            font-size: 0.95rem;
            border-radius: 8px;
            color: #444;
        }

        /* Tombol gradasi */
        .btn-gradient-success {
            background: linear-gradient(135deg, #00b09b, #96c93d);
            color: #fff !important;
            border: none;
            font-weight: 600;
            border-radius: 10px;
            padding: 8px 18px;
            transition: 0.3s ease;
            box-shadow: 0 3px 10px rgba(0, 176, 155, 0.3);
        }

        .btn-gradient-success:hover {
            background: linear-gradient(135deg, #00a087, #7bb92c);
            transform: translateY(-2px);
        }

        /* Responsif */
        @media (max-width: 768px) {
            .modal-dialog {
                max-width: 95% !important;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .time-row {
                grid-template-columns: 1fr;
            }
        }

        .filter-label {
            font-weight: 600;
            font-size: 14px;
            color: #444;
            letter-spacing: 0.3px;
        }

        /* Judul lebih halus */
        .date-title {
            font-weight: 600;
            font-size: 15px;
            color: #333;
            letter-spacing: 0.3px;
        }


        /* Tombol filter jadwal lebih kecil */
        /* Desktop & default */
        .date-filter-btn,
        .uniform-btn {
            flex: 1 1 120px;
            /* semua tombol punya lebar minimal sama */
            max-width: 150px;
            /* batas maksimal */
            font-size: 0.85rem;
            padding: 0.35rem 1rem;
            text-align: center;
            border-radius: 50px;
            border: 1px solid #6abfe3;
            background-color: #fff;
            color: #6abfe3;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        /* Tombol aktif */
        .date-filter-btn.active {
            background: linear-gradient(90deg, #6abfe3, #7873f5);
            color: #fff;
            border-color: #6abfe3;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        /* Hover */
        .date-filter-btn:hover,
        .uniform-btn:hover {
            background-color: #6abfe3;
            color: #fff;
            border-color: #6abfe3;
        }

        /* Mobile */
        @media (max-width: 768px) {

            .date-filter-btn,
            .uniform-btn {
                flex: 1 1 45%;
                /* dua tombol per baris, sama panjang */
                max-width: none;
                /* hilangkan batas max-width */
                font-size: 0.8rem;
                padding: 0.3rem 0.5rem;
            }
        }

        .page-title-wrapper {
            margin-top: 10px;
        }

        .page-title {
            font-weight: 700;
            font-size: 1.8rem;
            color: #2c3e50;
            letter-spacing: 0.4px;
        }

        .page-title i {
            color: #007bff;
        }

        .title-divider {
            width: 70px;
            height: 4px;
            margin: 12px auto 0;
            border-radius: 10px;
            background: linear-gradient(90deg, #007bff, #00c4ff);
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 1.4rem;
            }

            .title-divider {
                width: 50px;
                height: 3px;
            }
        }

        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
            padding: 20px 20px 10px 20px;
        }

        .date-range-input {
            width: 320px;
            max-width: 100%;
            border-radius: 50px;
        }

        /* ===== Booking ID ===== */
        .booking-id {
            background: linear-gradient(90deg, #007bff, #00b4d8);
            color: #fff;
            padding: 6px 16px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 600;
        }


        /* ===============================
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           DETAIL MODAL FINAL CLEAN
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ================================= */

        .detail-section {
            background: #f8fafc;
            border-radius: 16px;
            padding: 18px 22px;
            border: 1px solid #e5e7eb;
        }

        .detail-row {
            display: flex;
            align-items: flex-start;
            padding: 12px 0;
            border-bottom: 1px dashed #e5e7eb;
            gap: 12px;
            /* Jarak antar kolom */
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        /* LABEL */
        .detail-row>span:first-child {
            width: 180px;
            font-weight: 500;
            /* Tidak terlalu tebal */
            color: #6b7280;
            position: relative;
            padding-right: 14px;
            /* Ruang sebelum titik dua */
        }

        /* TITIK DUA */
        .detail-row>span:first-child::after {
            content: ":";
            position: absolute;
            right: 4px;
            /* Tidak terlalu mepet */
            color: #9ca3af;
            font-weight: 400;
        }

        /* VALUE */
        .detail-row strong {
            flex: 1;
            font-weight: 400;
            color: #111827;
            line-height: 1.6;
            white-space: normal !important;
            word-break: break-word !important;
        }

        /* TOTAL ROW STYLE */
        .total-row {
            display: flex;
            justify-content: space-between;
            /* Pastikan kiri & kanan */
            align-items: center;
            background: #f9fafb;
            margin-top: 18px;
            padding: 16px 18px;
            border-top: 2px solid #e5e7eb;
            border-radius: 10px;
        }

        /* Label Total */
        .total-row span {
            font-size: 15px;
            font-weight: 600;
            color: #374151;
        }

        /* Nominal Total */
        .total-row strong {
            font-size: 15px;
            font-weight: 500;
            color: #000000;
            /* Hijau elegan */
            text-align: right;
        }

        /* Notes & Addons */
        .notes-box,
        .addons-box {
            background: #f8fafc;
            border-radius: 14px;
            padding: 14px 16px;
            border: 1px solid #e5e7eb;
            font-size: 14px;
        }

        .section-title {
            font-size: 15px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 12px;
            position: relative;
            padding-left: 12px;
            letter-spacing: 0.3px;
        }

        .section-title::before {
            content: "";
            position: absolute;
            left: 0;
            top: 3px;
            width: 4px;
            height: 18px;
            border-radius: 4px;
            background: linear-gradient(135deg, #007bff, #00b4d8);
        }

        /* ================= NO LAYOUT SHIFT ================= */
        html {
            overflow-y: scroll;
        }

        body {
            padding-right: 0 !important;
        }

        body.modal-open {
            overflow: hidden !important;
            padding-right: 0 !important;
        }

        .modal-dialog {
            max-height: 90vh;
            margin: 1.5rem auto;
        }

        .modal-content {
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            /* biar footer tidak ikut scroll */
        }

        /* ================= MODAL BODY ================= */
        .modal-body {
            overflow-y: auto;
            padding: 1rem 1.5rem;
            flex: 1 1 auto;
            /* agar modal-body fleksibel scrollable */
        }

        /* ================= MODAL FOOTER ================= */
        .modal-footer {
            position: sticky;
            bottom: 0;
            background-color: #fff;
            z-index: 10;
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-top: 1px solid #dee2e6;
        }

        /* ================= SLOT WAKTU STYLE ================= */
        #reschedule-time-slots {
            max-height: 250px;
            overflow-y: auto;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        /* SLOT BASE */
        #reschedule-time-slots .time-slot {
            min-width: 95px;
            text-align: center;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            background-color: #ffffff;
            font-size: 0.9rem;
            font-weight: 500;
            position: relative;

            /* IMPORTANT: jangan pakai transition all */
            transition: background-color 0.2s ease,
                color 0.2s ease,
                box-shadow 0.2s ease;
        }

        /* HOVER */
        #reschedule-time-slots .time-slot:hover:not(.disabled):not(.selected) {
            background-color: #eff6ff;
            border-color: #3b82f6;
            color: #2563eb;
        }

        /* SELECTED (TIDAK UBAH SIZE SAMA SEKALI) */
        #reschedule-time-slots .time-slot.selected {
            background-color: #0d6efd !important;
            color: #fff !important;
            border-color: #0d6efd !important;

            /* pakai inner shadow supaya tidak geser */
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.3),
                0 4px 10px rgba(13, 110, 253, 0.25);
        }

        /* OLD SLOT (KUNING) */
        #reschedule-time-slots .time-slot.btn-warning {
            background-color: #fef3c7 !important;
            color: #92400e !important;
            border-color: #facc15 !important;
            cursor: not-allowed;
        }

        /* ICON JAM */
        #reschedule-time-slots .time-slot.btn-warning::after {
            content: "\f017";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            position: absolute;
            top: 4px;
            right: 6px;
            font-size: 10px;
            opacity: 0.8;
        }

        /* DISABLED */
        #reschedule-time-slots .time-slot.disabled {
            background-color: #f3f4f6;
            color: #9ca3af;
            cursor: not-allowed;
            border-color: #e5e7eb;
        }

        /* SCROLLBAR CLEAN */
        #reschedule-time-slots::-webkit-scrollbar {
            width: 6px;
        }

        #reschedule-time-slots::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 6px;
        }

        /* ================= MODERN CARD STYLE ================= */
        .modern-card {
            background-color: #fff;
            border-radius: 1rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .modern-arrow {
            font-size: 1.2rem;
            color: #3b82f6;
            transition: transform 0.2s;
        }

        .modern-arrow:hover {
            transform: scale(1.1);
        }

        /* Kalender table */
        .table-calendar th,
        .table-calendar td {
            padding: 0.75rem;
            vertical-align: middle;
        }

        .table-calendar td {
            cursor: pointer;
            border-radius: 0.5rem;
            transition: all 0.2s;
        }

        .table-calendar td.available:hover {
            background-color: #3b82f6;
            color: #fff;
        }

        .table-calendar td.selected {
            background-color: #e0f2ff;
            font-weight: 600;
        }

        .table-calendar td.disabled {
            color: #9ca3af;
            cursor: not-allowed;
        }

        .calendar-header-grid {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
        }

        .calendar-header-grid h5 {
            justify-self: center;
        }

        .calendar-header-grid button:first-child {
            justify-self: start;
        }

        .calendar-header-grid button:last-child {
            justify-self: end;
        }

        .table-calendar thead th {
            font-weight: 400 !important;
            font-size: 13px;
            letter-spacing: 0.3px;
            color: #000;
        }

        /* ================= FORCE FULL WIDTH FIX ================= */

        .table-calendar {
            width: 100% !important;
            table-layout: fixed !important;
            border-collapse: collapse !important;
            border-spacing: 0 !important;
        }

        /* Reset semua padding horizontal */
        .table-calendar th,
        .table-calendar td {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        /* Bagi rata 7 kolom */
        .table-calendar td,
        .table-calendar th {
            width: calc(100% / 7);
        }

        /* Tinggi cell tetap elegan */
        .table-calendar td {
            height: 48px;
            text-align: center;
            vertical-align: middle;
        }

        /* Pastikan parent tidak kasih ruang */
        .table-calendar {
            margin: 0 !important;
        }

        /* Label kecil */
        .small.fw-semibold {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 6px;
            display: block;
        }

        /* ================= SECTION TITLE (SAMA SEPERTI CATATAN) ================= */
        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #374151;
            margin-bottom: 8px;
            display: block;
        }

        /* ================= WRAPPER BOX ================= */
        .status-section {
            background: #f9fafb;
            padding: 18px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }

        /* ================= CURRENT STATUS BOX ================= */
        .current-status-box {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 44px;
            padding: 10px 16px;
            background: #ffffff;
            border: 1px dashed #d1d5db;
            border-radius: 10px;
            font-weight: 600;
            color: #111827;
        }

        /* ================= DIVIDER ================= */
        .divider-status {
            height: 1px;
            background: #e5e7eb;
            margin: 16px 0;
        }

        /* ================= CHANGE LABEL ================= */
        .change-status-label {
            font-size: 13px;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 6px;
            display: block;
        }

        /* ================= SELECT MODERN ================= */
        #modalStatusSelect {
            width: 100%;
            appearance: none;
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 10px 42px 10px 14px;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            transition: all 0.2s ease;
            cursor: pointer;

            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%236b7280' viewBox='0 0 16 16'%3E%3Cpath d='M1.5 5.5l6 6 6-6' stroke='%236b7280' stroke-width='2' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
        }

        /* Hover */
        #modalStatusSelect:hover {
            border-color: #cbd5e1;
        }

        /* Focus */
        #modalStatusSelect:focus {
            outline: none;
            border-color: #16a34a;
            box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.15);
        }

        /* === INPUT PENCARIAN === */
        .dataTables_filter {
            text-align: right;
        }

        .dataTables_filter input {
            margin-top: 0.2rem;
            /* turunkan input lebih presisi */
            margin-right: 0.2rem;
            border-radius: 50px !important;
            padding: 0.5rem 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border: 1px solid #dee2e6;
            transition: 0.3s;
        }

        .dataTables_filter input:focus {
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
            border-color: #80bdff;
        }

        .custom-footer {
            display: flex;
            gap: 10px;
        }

        .custom-footer .btn {
            border-radius: 50px !important;
            min-height: 44px;
            padding: 8px 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            /* 🔥 ini yang bikin icon ada jarak */
            white-space: nowrap;
        }

        /* MODE MOBILE */
        @media (max-width: 576px) {
            .custom-footer {
                flex-direction: column;
            }

            .custom-footer .btn {
                width: 100%;
            }
        }

        /* ===== HORIZONTAL SCROLL TABLE (LIKE TRANSAKSI) ===== */
        .table-scroll {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* supaya tabel tidak berubah jadi card */
        #myTable {
            min-width: 900px;
            /* bisa kamu naikkan kalau kolom banyak */
            white-space: nowrap;
        }

        /* scrollbar lebih halus */
        .table-scroll::-webkit-scrollbar {
            height: 6px;
        }

        .table-scroll::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            /* lebih kecil */
            font-size: 11px;
            /* lebih kecil */
            font-weight: 600;
            border-radius: 999px;
            color: #fff;
            text-align: center;
            min-width: 95px;
            /* ikut diperkecil biar proporsional */
        }

        .modal-header .btn-close {
            filter: invert(1) grayscale(100%) !important;
            opacity: 1 !important;
        }
    </style>
@stop


@section('js')


    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });

        @if (session('success'))
            Toast.fire({
                icon: 'success',
                title: '{{ session('success') }}'
            });
        @endif

        @if (session('error'))
            Toast.fire({
                icon: 'error',
                title: '{{ session('error') }}'
            });
        @endif
    </script>

    <script>
        $(document).ready(function() {
            // ================= DATATABLE =================
            var table = $('#myTable').DataTable({
                responsive: true,
                dom: "<'row mb-3'<'col-12 d-flex justify-content-end pe-3'f>>" + "rtip",
                order: [
                    [4, 'asc']
                ],
                language: {
                    search: "",
                    searchPlaceholder: "Cari janji temu...",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Berikutnya",
                        previous: "Sebelumnya"
                    },
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ janji temu",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 janji temu",
                    infoFiltered: "(difilter dari _MAX_ total janji temu)",
                    zeroRecords: "Data tidak ditemukan",
                    lengthMenu: "Tampilkan _MENU_ baris",
                }
            });


            // Tambah style search bar premium dengan icon
            $('#myTable_filter input').addClass('form-control rounded-pill shadow-sm')
                .css({
                    'padding': '0.45rem 2.5rem 0.45rem 1rem',
                    'border': 'none',
                    'box-shadow': '0 2px 6px rgba(0,0,0,0.08)',
                    'background-image': 'url("data:image/svg+xml,%3Csvg fill=\'%23666\' xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' width=\'16\' height=\'16\'%3E%3Cpath d=\'M10 2a8 8 0 105.293 14.293l5.707 5.707 1.414-1.414-5.707-5.707A8 8 0 0010 2zm0 2a6 6 0 110 12 6 6 0 010-12z\'/%3E%3C/svg%3E")',
                    'background-repeat': 'no-repeat',
                    'background-position': 'right 10px center',
                    'background-size': '16px 16px'
                });

            // ================= FILTERS =================
            var dateFilterType = '';
            var dateRange = [];

            $('.date-filter-btn').on('click', function() {
                $('.date-filter-btn').removeClass('active');
                $(this).addClass('active');

                var val = $(this).data('value');
                dateFilterType = val;

                if (val === 'range') {
                    $('#dateRangePicker').removeClass('d-none').val('');
                } else {
                    $('#dateRangePicker').addClass('d-none').val('');
                }

                applyFilters();
            });

            flatpickr("#dateRangePicker", {
                mode: "range",
                dateFormat: "Y-m-d",
                locale: {
                    firstDayOfWeek: 1
                },
                onChange: function(selectedDates) {
                    if (selectedDates.length === 2) {
                        dateRange = selectedDates;
                        dateFilterType = 'range';
                        applyFilters();
                    }
                }
            });

            $('#filterEmployee, #filterService').on('change', function() {
                applyFilters();
            });

            function applyFilters() {
                $.fn.dataTable.ext.search = [];

                // ================= FILTER TANGGAL =================
                $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                    var now = new Date(); // waktu sekarang
                    var dateAttr = $(table.row(dataIndex).node()).find('td:eq(4)').data('date');
                    var startTimeAttr = $(table.row(dataIndex).node()).find('td:eq(5)').text().split(' - ')[
                        0]; // HH:mm

                    // Buat Date object dari tanggal + jam
                    var dateParts = dateAttr.split('-');
                    var timeParts = startTimeAttr.split(':');
                    var bookingDateTime = new Date(
                        parseInt(dateParts[0]),
                        parseInt(dateParts[1]) - 1,
                        parseInt(dateParts[2]),
                        parseInt(timeParts[0]),
                        parseInt(timeParts[1])
                    );

                    if (dateFilterType === 'upcoming') return bookingDateTime >= now;
                    if (dateFilterType === 'past') return bookingDateTime < now;
                    if (dateFilterType === 'range' && dateRange.length === 2) {
                        var start = new Date(dateRange[0]);
                        var end = new Date(dateRange[1]);
                        return bookingDateTime >= start && bookingDateTime <= end;
                    }

                    return true;
                });

                // ================= FILTER KARYAWAN BASED ON EMPLOYEE ID =================
                var employeeVal = $('#filterEmployee').val();

                if (employeeVal) {
                    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                        var row = table.row(dataIndex).node();
                        var employeeId = $(row).data('employee-id');
                        return employeeId == employeeVal;
                    });
                }

                // ================= FILTER LAYANAN =================
                var serviceVal = $('#filterService').val();

                if (serviceVal) {
                    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                        var rowNode = table.row(dataIndex).node();
                        var rowServiceId = $(rowNode).data('service-id');
                        return String(rowServiceId) === String(serviceVal);
                    });
                }

                // ================= SORT OTOMATIS =================
                table.rows().every(function(rowIdx, tableLoop, rowLoop) {
                    var row = $(this.node());
                    var dateAttr = row.find('td:eq(4)').data('date');
                    var startTimeAttr = row.find('td:eq(5)').text().split(' - ')[0];
                    var dateParts = dateAttr.split('-');
                    var timeParts = startTimeAttr.split(':');
                    var bookingDateTime = new Date(
                        parseInt(dateParts[0]),
                        parseInt(dateParts[1]) - 1,
                        parseInt(dateParts[2]),
                        parseInt(timeParts[0]),
                        parseInt(timeParts[1])
                    );
                    $(this.node()).attr('data-timestamp', bookingDateTime.getTime());
                });

                if (dateFilterType === 'past') {
                    table.order([
                        [4, 'desc'],
                        [5, 'desc']
                    ]).draw(); // urut tanggal & jam descending
                } else {
                    table.order([
                        [4, 'asc'],
                        [5, 'asc']
                    ]).draw(); // default urut ascending
                }
            }

            applyFilters(); // filter default
            // ================= MODAL POPULATE =================
            $(document).on('click', '.view-appointment-btn', function() {

                $('#modalAppointmentId').val($(this).data('id'));
                $('#modalBookingId').text('ID Pemesanan : ' + $(this).data('booking'));
                $('#modalAppointmentName').text($(this).data('name'));
                $('#modalService').text($(this).data('service'));

                let background = $(this).data('background');

                if (background) {
                    $('#modalBackground').text(background);
                    $('#backgroundRow').show();
                } else {
                    $('#modalBackground').text('-');
                    $('#backgroundRow').hide();
                }

                $('#modalEmail').text($(this).data('email'));
                $('#modalPhone').text($(this).data('phone'));
                $('#modalPeopleCount').text($(this).data('people'));
                $('#modalEmployee').text($(this).data('employee'));
                $('#modalDate').text($(this).data('date'));
                $('#modalStartTime').text($(this).data('start_time'));
                $('#modalEndTime').text($(this).data('end_time'));

                // ================= PRICE =================
                let servicePrice = $(this).data('service_price');
                $('#modalServicePrice').text(
                    'Rp ' + Number(servicePrice).toLocaleString('id-ID')
                );

                let totalAmount = Number($(this).data('amount')) || 0;
                let paidAmount = Number($(this).data('dp')) || 0;
                let paymentType = $(this).data('paymentType');

                let remaining = totalAmount - paidAmount;

                $('#modalAmount').text('Rp ' + totalAmount.toLocaleString('id-ID'));

                if (paidAmount <= 0) {
                    $('#paymentSection').hide();
                    // tetap lanjut modal tampil
                } else {
                    $('#paymentSection').show();
                }

                // ================= FULL PAYMENT =================
                if (paymentType === 'full' || remaining <= 0) {

                    $('#paymentLabel').text('Total Dibayar');
                    $('#modalDpAmount').text('Rp ' + paidAmount.toLocaleString('id-ID'));

                    $('#modalRemaining')
                        .removeClass('text-danger')
                        .addClass('text-success')
                        .text('LUNAS');

                    $('#remainingRow').show();
                }

                // ================= DP =================
                else {

                    $('#paymentLabel').text('DP Dibayar');
                    $('#modalDpAmount').text('Rp ' + paidAmount.toLocaleString('id-ID'));

                    $('#modalRemaining')
                        .removeClass('text-success')
                        .addClass('text-danger')
                        .text('Rp ' + remaining.toLocaleString('id-ID'));

                    $('#remainingRow').show();
                }

                $('#modalNotes').text($(this).data('notes'));

                // ================= ADD ON =================
                let addons = $(this).data('addons');
                let addonHtml = '';

                if (addons && Array.isArray(addons) && addons.length > 0) {
                    addonHtml += '<ul class="list-group list-group-flush">';

                    addons.forEach(function(a) {
                        addonHtml += `
                <li class="list-group-item border-bottom px-0 py-2 bg-transparent">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div>
                                ${a.name}
                            </div>
                            <small class="text-muted">
                                ${a.qty} x Rp ${Number(a.price).toLocaleString('id-ID')}
                            </small>
                        </div>

                        <div>
                            Rp ${Number(a.subtotal).toLocaleString('id-ID')}
                        </div>
                    </div>
                </li>
            `;
                    });

                    addonHtml += '</ul>';
                } else {
                    addonHtml = '<em class="text-muted">Tidak ada add on</em>';
                }

                $('#modalAddons').html(addonHtml);

                // ================= STATUS =================
                var status = $(this).data('status') || 'Pending';

                $('#modalStatusSelect').val(status);

                var statusLabel = {
                    'Pending': 'Menunggu',
                    'Processing': 'Diproses',
                    'Confirmed': 'Dikonfirmasi',
                    'Cancelled': 'Dibatalkan',
                    'Completed': 'Selesai',
                    'Rescheduled': 'Jadwal Ulang',
                    'On Hold': 'Ditahan',
                    'No Show': 'Tidak Hadir'
                };

                var statusColors = {
                    'Pending': '#f39c12',
                    'Processing': '#3498db',
                    'Confirmed': '#2ecc71',
                    'Cancelled': '#e74c3c',
                    'Completed': '#16a085',
                    'Rescheduled': '#9b59b6',
                    'On Hold': '#7f8c8d',
                    'No Show': '#e67e22'
                };

                var label = statusLabel[status] || status;

                $('#modalStatusBadge').html(`
        <span class="status-badge"
            style="background-color:${statusColors[status] || '#6c757d'};">
            ${label}
        </span>
    `);

                // ================= FIX BOOTSTRAP 5 MODAL OPEN =================
                const modalEl = document.getElementById('appointmentModal');
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            });

            // ================= ALERT AUTO CLOSE =================
            $(".alert").delay(6000).slideUp(300);
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // =============================
            // GLOBAL STATE
            // =============================
            let resCurrentMonth = new Date().getMonth();
            let resCurrentYear = new Date().getFullYear();
            let selectedResDate = null;
            let selectedSlot = null;
            window.originalBookingTime = null;

            const btnSubmit = document.getElementById('reschedule-submit-btn');
            const slotContainer = document.getElementById('reschedule-time-slots');

            function toggleSubmitButton() {
                if (btnSubmit) {
                    btnSubmit.disabled = !(selectedResDate && selectedSlot);
                }
            }

            // =============================
            // OPEN RESCHEDULE MODAL
            // =============================
            const btnReschedule = document.getElementById('btnReschedule');

            if (btnReschedule) {
                btnReschedule.addEventListener('click', function() {

                    const appointmentId = document.getElementById('modalAppointmentId')?.value;

                    if (!appointmentId) {
                        Swal.fire('Error', 'Janji temu tidak ditemukan', 'error');
                        return;
                    }

                    document.getElementById('reschedule-appointment-id').value = appointmentId;

                    selectedResDate = null;
                    selectedSlot = null;
                    toggleSubmitButton();

                    if (slotContainer) {
                        slotContainer.innerHTML =
                            `<div class="text-muted text-center py-3">Pilih tanggal terlebih dahulu</div>`;
                    }

                    const modalEl = document.getElementById('rescheduleModal');
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();

                    renderRescheduleCalendar(resCurrentMonth, resCurrentYear);
                });
            }

            // =============================
            // NAVIGATION MONTH
            // =============================
            document.getElementById('res-prev-month')?.addEventListener('click', () => navigateMonth(-1));
            document.getElementById('res-next-month')?.addEventListener('click', () => navigateMonth(1));

            function navigateMonth(direction) {
                resCurrentMonth += direction;

                if (resCurrentMonth < 0) {
                    resCurrentMonth = 11;
                    resCurrentYear--;
                }

                if (resCurrentMonth > 11) {
                    resCurrentMonth = 0;
                    resCurrentYear++;
                }

                renderRescheduleCalendar(resCurrentMonth, resCurrentYear);
            }

            // =============================
            // RENDER CALENDAR
            // =============================
            function renderRescheduleCalendar(month, year) {

                const tbody = document.getElementById('reschedule-calendar-body');
                if (!tbody) return;

                tbody.innerHTML = '';
                selectedResDate = null;
                selectedSlot = null;
                toggleSubmitButton();

                const firstDay = new Date(year, month, 1);
                const lastDay = new Date(year, month + 1, 0);
                const today = new Date();
                const startingDay = (firstDay.getDay() + 6) % 7;

                const monthNames = [
                    "Januari", "Februari", "Maret", "April", "Mei", "Juni",
                    "Juli", "Agustus", "September", "Oktober", "November", "Desember"
                ];

                document.getElementById('res-current-month').textContent =
                    `${monthNames[month]} ${year}`;

                let date = 1;

                for (let i = 0; i < 6; i++) {
                    const row = document.createElement('tr');

                    for (let j = 0; j < 7; j++) {

                        const cell = document.createElement('td');

                        if (i === 0 && j < startingDay) {
                            row.appendChild(cell);
                        } else if (date > lastDay.getDate()) {
                            row.appendChild(cell);
                        } else {

                            const cellDate = new Date(year, month, date);
                            const formatted =
                                `${year}-${String(month+1).padStart(2,'0')}-${String(date).padStart(2,'0')}`;

                            cell.textContent = date;
                            cell.classList.add('calendar-day');

                            if (cellDate < new Date(today.getFullYear(), today.getMonth(), today.getDate())) {
                                cell.classList.add('disabled');
                            } else {
                                cell.addEventListener('click', function() {

                                    document.querySelectorAll('.calendar-day')
                                        .forEach(c => c.classList.remove('selected'));

                                    cell.classList.add('selected');

                                    selectedResDate = formatted;
                                    selectedSlot = null;
                                    toggleSubmitButton();

                                    loadTimeSlots(formatted);

                                    document.getElementById('res-selected-date-display').textContent =
                                        "Dipilih: " +
                                        cellDate.toLocaleDateString('id-ID', {
                                            day: '2-digit',
                                            month: 'short',
                                            year: 'numeric'
                                        });
                                });
                            }

                            row.appendChild(cell);
                            date++;
                        }
                    }

                    tbody.appendChild(row);
                }
            }

            // =============================
            // LOAD TIME SLOTS
            // =============================
            function loadTimeSlots(date) {

                const appointmentId =
                    document.getElementById('reschedule-appointment-id')?.value;

                if (!appointmentId || !slotContainer) return;

                slotContainer.innerHTML =
                    `<div class="text-center py-3">
                <div class="spinner-border text-primary"></div>
             </div>`;

                fetch(`/appointments/${appointmentId}/reschedule/availability?date=${date}`)
                    .then(res => res.json())
                    .then(res => {

                        slotContainer.innerHTML = '';
                        selectedSlot = null;
                        toggleSubmitButton();

                        if (!res.success || !res.available_slots.length) {
                            slotContainer.innerHTML =
                                `<div class="text-muted text-center py-3">
                            Tidak ada slot tersedia
                         </div>`;
                            return;
                        }

                        res.available_slots.forEach(slot => {

                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.className = 'time-slot btn m-1';
                            btn.textContent = slot.display;
                            btn.dataset.start = slot.start;
                            btn.dataset.end = slot.end;

                            // ================= BOOKED SLOT =================
                            if (slot.is_booked && !slot.is_old) {
                                btn.classList.add('btn-outline-secondary', 'disabled');
                                btn.disabled = true;
                            } else {
                                btn.classList.add('btn-outline-primary');
                            }

                            // ================= SLOT SAAT INI =================
                            if (slot.is_old) {
                                btn.classList.remove('btn-outline-primary');
                                btn.classList.add('btn-warning', 'text-dark');
                                btn.classList.add('disabled');
                                btn.disabled = true; // ⬅ WAJIB
                            }

                            btn.addEventListener('click', function() {

                                if (btn.disabled) return;

                                document.querySelectorAll('#reschedule-time-slots .time-slot')
                                    .forEach(b => b.classList.remove('selected', 'active'));

                                btn.classList.add('selected', 'active');
                                selectedSlot = btn;
                                toggleSubmitButton();
                            });

                            slotContainer.appendChild(btn);
                        });
                    })
                    .catch(() => {
                        slotContainer.innerHTML =
                            `<div class="text-danger text-center py-3">
                        Gagal memuat slot
                     </div>`;
                    });
            }

            // =============================
            // SUBMIT RESCHEDULE
            // =============================
            if (btnSubmit) {

                btnSubmit.addEventListener('click', function() {

                    const appointmentId =
                        document.getElementById('reschedule-appointment-id')?.value;

                    if (!appointmentId || !selectedResDate || !selectedSlot) {
                        Swal.fire('Error', 'Lengkapi pilihan terlebih dahulu', 'warning');
                        return;
                    }

                    btnSubmit.disabled = true;
                    btnSubmit.innerHTML =
                        `<span class="spinner-border spinner-border-sm"></span> Menyimpan...`;

                    fetch(`/appointments/${appointmentId}/reschedule`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content'),
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                new_date: selectedResDate,
                                new_start_time: selectedSlot.dataset.start,
                                new_end_time: selectedSlot.dataset.end
                            })
                        })
                        .then(res => res.json())
                        .then(res => {

                            if (!res.success) {
                                throw new Error(res.message || 'Gagal jadwal ulang');
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Jadwal ulang berhasil'
                            }).then(() => location.reload());
                        })
                        .catch(err => {
                            btnSubmit.disabled = false;
                            btnSubmit.innerHTML = 'Simpan Perubahan';
                            Swal.fire('Error', err.message, 'error');
                        });
                });
            }

        });
    </script>

@stop
