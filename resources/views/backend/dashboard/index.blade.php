@extends('adminlte::page')

@section('title', 'Jadwal Pemesanan')

@section('content_header')

    <div class="page-title-wrapper text-center mb-4">
        <h1 class="page-title">
            <i class="fa fa-calendar-alt me-2"></i>
            Jadwal Pemesanan
        </h1>
        <div class="title-divider"></div>
    </div>

@stop


@section('content')
    <div class="calendar-wrapper container-fluid px-2 px-md-3 py-3">
        <div id="calendar" class="bg-white rounded-4 shadow-sm p-3 p-md-4"></div>
    </div>

    <!-- Modal Detail Pemesanan -->
    <form id="appointmentStatusForm" method="POST" action="{{ route('dashboard.update.status') }}">
        @csrf
        <input type="hidden" name="appointment_id" id="modalAppointmentId">

        <div class="modal fade" id="appointmentModal" tabindex="-1" aria-labelledby="appointmentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header text-white" style="background: linear-gradient(135deg, #007bff, #00c4ff);">
                        <h5 class="modal-title fw-semibold" id="appointmentModalLabel">
                            <i class="fa fa-calendar-check me-2"></i> Detail Pemesanan
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Tutup"></button>
                    </div>

                    <div class="modal-body p-4 bg-light">

                        <div class="row g-4">

                            <!-- ================= LEFT : CLIENT INFO ================= -->
                            <div class="col-md-6 pe-md-4 border-md-end">
                                <h6 class="text-uppercase text-bold fw-bold mb-3">
                                    Informasi Pengguna
                                </h6>

                                <div class="mb-3">
                                    <small class="text-muted d-block">Pengguna</small>
                                    <div id="modalAppointmentName">-</div>
                                </div>

                                <div class="mb-3">
                                    <small class="text-muted d-block">Email</small>
                                    <div id="modalEmail">-</div>
                                </div>

                                <div class="mb-0">
                                    <small class="text-muted d-block">Telepon</small>
                                    <div id="modalPhone">-</div>
                                </div>
                            </div>

                            <!-- ================= RIGHT : BOOKING DETAIL ================= -->
                            <div class="col-md-6 ps-2 ps-md-4 h-100 d-flex flex-column">
                                <h6 class="text-uppercase text-bold fw-bold mb-3">
                                    Detail Pemesanan
                                </h6>

                                <div class="mb-3">
                                    <small class="text-muted d-block">Karyawan</small>
                                    <div id="modalEmployee">-</div>
                                </div>

                                <div class="mb-3">
                                    <small class="text-muted d-block">Layanan</small>
                                    <div id="modalService">-</div>
                                </div>

                                <div class="mb-3">
                                    <small class="text-muted d-block">Jumlah Orang</small>
                                    <div id="modalPeople">-</div>
                                </div>

                                <div class="mb-3" id="backgroundWrapper" style="display:none;">
                                    <small class="text-muted d-block">Latar Belakang Dipilih</small>
                                    <div id="modalBackground">-</div>
                                </div>

                                <div class="mb-3">
                                    <small class="text-muted d-block">Tanggal</small>
                                    <div id="modalDate">-</div>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Mulai</small>
                                        <div id="modalStartTime">-</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Selesai</small>
                                        <div id="modalEndTime">-</div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- ================= NOTES & STATUS ================= -->
                        <div class="row g-4 notes-status-section">

                            <div class="col-md-8">
                                <h6 class="section-label">Catatan</h6>
                                <div class="notes-box" id="modalNotes">-</div>
                            </div>

                            <div class="col-md-4 status-section">
                                <h6 class="section-label">Status</h6>

                                <div id="modalStatusBadge" class="mb-3"></div>

                                <label for="modalStatusSelect" class="status-label">
                                    Ubah Status
                                </label>

                                <select name="status" id="modalStatusSelect" class="w-100">
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

                    </div>

                    <div class="modal-footer bg-light px-4 d-flex justify-content-end gap-2">

                        <button type="button" class="btn btn-outline-secondary btn-modal" data-bs-dismiss="modal">
                            <i class="fa fa-times me-1"></i> Tutup
                        </button>

                        <button type="submit" class="btn btn-gradient-success btn-modal shadow-sm" id="saveStatusBtn">
                            <i class="fa fa-save me-1"></i> Simpan Perubahan
                        </button>

                    </div>
                </div>
            </div>
        </div>
    </form>
@stop

@section('css')
    <style>
        /* ========== LAYOUT DASAR ========== */
        html {
            overflow-y: auto !important;
        }

        html,
        body {
            overflow-x: hidden !important;
        }

        .calendar-wrapper {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            transition: all 0.3s ease;
        }


        #calendar:hover {
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.1);
        }

        /* ===== TOOLBAR WRAPPER (background halus) ===== */
        .fc-header-toolbar {
            background: linear-gradient(135deg, #007bff, #00b4d8);
            padding: 12px 16px;
            border-radius: 16px;
            color: #fff;
        }

        /* Events */
        .fc-daygrid-day:hover {
            background: rgba(0, 123, 255, 0.05);
            cursor: pointer;
        }

        /* Tooltip */
        .custom-tooltip {
            position: absolute;
            z-index: 9999;
            background: rgba(34, 34, 34, 0.9);
            color: #fff;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 13px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.25s ease;
        }

        /* Responsif */
        @media (max-width: 768px) {
            .fc-toolbar {
                flex-direction: column !important;
                gap: 10px;
            }

            #calendar {
                border-radius: 12px;
                padding: 10px;
            }

            .fc-toolbar-title {
                font-size: 1.2rem !important;
            }
        }

        /* ========== FIX WHITE SPACE SAAT MODAL ========== */
        body.modal-open {
            overflow: hidden !important;
            padding-right: 0 !important;
        }

        body.sidebar-mini.modal-open .wrapper {
            margin-right: 0 !important;
        }

        .modal-backdrop {
            position: fixed !important;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100% !important;
            height: 100% !important;
            background-color: rgba(0, 0, 0, 0.45) !important;
            z-index: 1040;
        }

        .modal {
            overflow-y: auto !important;
            padding-right: 0 !important;
            margin: 0 auto;
        }

        .modal.fade .modal-dialog {
            transition: transform 0.25s ease, opacity 0.25s ease;
            transform: translateY(-10px);
            opacity: 0;
        }

        .modal.fade.show .modal-dialog {
            transform: translateY(0);
            opacity: 1;
        }


        /* ===== Modal Premium Style (Ukuran Sedang & Clean) ===== */
        .modal-dialog {
            max-width: 650px !important;
            /* sedang dan proporsional di semua layar */
            margin: 1.75rem auto;
        }

        .modal-content {
            border-radius: 18px !important;
            background: #ffffff;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            transition: all 0.25s ease-in-out;
        }

        .modal-header {
            border-bottom: none;
            background: linear-gradient(135deg, #007bff, #00b4d8);
            color: #fff;
            padding: 1rem 1.5rem;
            text-align: center;
        }

        .modal-title {
            font-size: 1.2rem;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .modal-body {
            background-color: #f9fafc;
            color: #333;
            padding: 1.5rem;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            align-items: center;
        }

        /* Catatan */
        #modalNotes {
            min-height: 80px;
            font-size: 0.95rem;
            color: #444;
            border-radius: 10px;
        }

        /* ===== BUTTON GRADIENT SUCCESS (NORMAL STATE) ===== */
        .btn-gradient-success {
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;

            background: linear-gradient(135deg, #00b09b, #96c93d);
            color: #fff !important;
            border: none;
            font-weight: 600;
            border-radius: 10px;
            padding: 0 16px;

            box-shadow: 0 3px 10px rgba(0, 176, 155, 0.3);
            transition: all 0.3s ease;
        }

        /* ===== HOVER ===== */
        .btn-gradient-success:hover {
            background: linear-gradient(135deg, #00a087, #7bb92c);
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(0, 176, 155, 0.4);
        }

        /* optional: active click */
        .btn-gradient-success:active {
            transform: translateY(0px);
            box-shadow: 0 2px 8px rgba(0, 176, 155, 0.25);
        }

        /* Wrapper spacing */
        .mt-4 {
            margin-top: 22px !important;
        }

        /* Label kecil */
        .small.fw-semibold {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 6px;
            display: block;
        }

        /* Select Modern */
        #modalStatusSelect {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;

            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 10px 42px 10px 14px;
            font-size: 14px;
            font-weight: 500;
            color: #374151;

            transition: all 0.2s ease;
            cursor: pointer;
        }

        /* Custom arrow */
        #modalStatusSelect {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%236b7280' viewBox='0 0 16 16'%3E%3Cpath d='M1.5 5.5l6 6 6-6' stroke='%236b7280' stroke-width='2' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
        }

        /* Hover */
        #modalStatusSelect:hover {
            border-color: #cbd5e1;
            background-color: #ffffff;
        }

        /* Focus */
        #modalStatusSelect:focus {
            outline: none;
            border-color: #16a34a;
            box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.15);
            background-color: #ffffff;
        }

        /* Optional: smooth badge spacing */
        #modalStatusBadge .status-badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 11px;
            font-weight: 600;
            border-radius: 999px;
            color: #fff;
            text-align: center;
            min-width: 95px;
            line-height: 1.2;
        }

        /* ===== ✨ Premium Smooth & Stable FullCalendar List View (Dot Bulat Elegan) ===== */

        /* Font & warna dasar */
        .fc-list-event a {
            color: #2c3e50 !important;
            font-weight: 500;
            letter-spacing: 0.2px;
            font-family: 'Poppins', 'Inter', sans-serif;
            transition: all 0.35s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 2;
        }

        /* ===== LIST HEADER (tanggal di list view) ===== */
        .fc-list-day {
            background: #f8fafc !important;
            /* sama kayak header atas */
            border-left: 4px solid #e5e7eb !important;
            /* soft, bukan biru */
            padding: 0.75rem 1rem !important;
        }

        /* teks hari & tanggal */
        .fc-list-day-cushion {
            color: #2c3e50 !important;
            /* 🔥 samakan */
            font-weight: 600 !important;
            letter-spacing: 0.3px;
        }

        /* kalau masih dianggap link */
        .fc-list-day-cushion a {
            color: #2c3e50 !important;
            text-decoration: none !important;
        }

        /* ===== ROW WRAPPER (FINAL MERGE) ===== */
        .fc-list-event {
            position: relative;
            overflow: hidden;

            border-radius: 14px !important;
            margin-bottom: 10px !important;

            background: #ffffff !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);

            transition: all 0.25s ease;
        }

        /* ===== HOVER EFFECT UTAMA ===== */
        .fc-list-event:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 123, 255, 0.12);
            cursor: pointer;
        }

        /* ===== BACKGROUND HOVER FULL (layer bawah) ===== */
        .fc-list-event::before {
            content: "";
            position: absolute;
            inset: 0;

            background: linear-gradient(90deg,
                    rgba(0, 123, 255, 0.06),
                    rgba(0, 180, 216, 0.1));

            opacity: 0;
            transition: opacity 0.25s ease;
            z-index: 0;
        }

        /* aktif saat hover */
        .fc-list-event:hover::before {
            opacity: 1;
        }

        /* ===== AKSEN KIRI ===== */
        .fc-list-event::after {
            content: "";
            position: absolute;
            left: 0;
            top: 20%;
            height: 60%;
            width: 4px;

            background: linear-gradient(180deg, #007bff, #00b4d8);
            border-radius: 4px;

            opacity: 0;
            transition: 0.3s ease;
        }

        .fc-list-event:hover::after {
            opacity: 1;
        }

        /* ===== CELL ===== */
        .fc-list-event td {
            background: transparent !important;
            /* penting */
            border: none !important;
            /* hilangkan garis pecah */
            padding: 0.9rem 1rem !important;
            position: relative;
            z-index: 1;
            transition: all 0.25s ease;
        }

        /* waktu */
        .fc-list-event-time {
            font-size: 0.85rem;
            color: #6b7280 !important;
        }

        /* judul */
        .fc-list-event-title {
            font-size: 0.95rem;
            font-weight: 600;
        }

        /* warna saat hover */
        .fc-list-event:hover .fc-list-event-time {
            color: #007bff !important;
        }

        .fc-list-event:hover .fc-list-event-title {
            color: #004c8c !important;
        }

        /* ===== FIX LEBAR KOLOM TIME ===== */
        .fc-list-event-time {
            min-width: 110px;
            /* 🔥 ini kunci */
            display: inline-block;
        }

        /* ===== KOLOM DOT BIAR ADA NAFAS ===== */
        .fc-list-event-graphic {
            width: 40px;
            text-align: center;
        }

        /* ===== DOT POSISI CENTER & GA NEMPEL ===== */
        .fc-list-event-dot {
            margin: 0 auto;
        }

        /* ===== TITLE BIAR GA KEJEPIT ===== */
        .fc-list-event-title {
            padding-left: 8px;
        }


        /* ===== Saat tidak ada event ===== */
        .fc-list-empty {
            background: #f8f9fa !important;
            color: #6c757d !important;
            font-style: italic;
            border-radius: 12px;
            text-align: center;
            padding: 1.5rem !important;
            box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.04);
        }

        /* ===== Responsif (semua layar) ===== */
        @media (max-width: 992px) {
            .fc-list-event td {
                display: block !important;
                padding: 0.75rem 1rem !important;
            }

            .fc-list-event-time {
                display: block;
                margin-top: 4px;
                color: #007bff !important;
                font-weight: 500;
            }

            .fc-list-event a {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }

            .fc-list-event-dot {
                margin-right: 6px;
            }
        }

        /* ============================================
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       📱 MODERN MOBILE / iOS CALENDAR STYLE
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       ============================================ */

        /* ---- GLOBAL CALENDAR CARD ---- */
        #calendar {
            background: #ffffff;
            border-radius: 22px !important;
            padding: 18px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.07);
            border: 1px solid #f1f3f4;
        }

        /* ---- HEADER TITLE (BACKDROP LEBIH TEBAL) ---- */
        .fc-toolbar-title {
            font-weight: 800 !important;
            font-size: 1.65rem !important;
            color: #ffffff !important;
            letter-spacing: -0.3px;

            /* 🔥 backdrop lebih tebal */
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(10px);

            /* tambahan biar makin solid */
            padding: 4px 12px;
            border-radius: 10px;

            /* efek depth */
            text-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);

            /* biar rapi */
            display: inline-block;
        }

        /* ===== BUTTON NAV (ANTI NYARU) ===== */
        .fc-button {
            border-radius: 12px !important;
            padding: 6px 14px !important;
            border: none !important;
            font-weight: 600 !important;

            /* 🔥 beda total dari toolbar */
            background: rgba(255, 255, 255, 0.2) !important;
            color: #fff !important;
            backdrop-filter: blur(6px);

            transition: all 0.25s ease;
        }

        /* hover */
        .fc-button:hover {
            background: rgba(255, 255, 255, 0.35) !important;
            transform: translateY(-1px);
        }

        /* tombol aktif */
        .fc-button-primary {
            background: #ffffff !important;
            color: #000000 !important;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        }

        /* hover active */
        .fc-button-primary:hover {
            background: #f1f5ff !important;
        }

        /* icon panah */
        .fc-icon {
            color: #000000 !important;
        }



        /* ---- DAY NUMBER ---- */
        .fc-daygrid-day-number {
            font-weight: 600;
            color: #424949;
            font-size: 0.95rem;
        }

        /* ---- EVENT BUBBLES (iOS Rounded Tags) ---- */
        .fc-event {
            border: none !important;
            border-radius: 10px !important;

            padding: 6px 8px !important;
            /* sedikit lebih lega */
            font-size: 0.8rem !important;
            font-weight: 600 !important;

            line-height: 1.2 !important;
            /* ini penting biar tidak kepotong */

            min-height: 26px;
            /* bikin event lebih “tinggi” */
            display: flex;
            align-items: center;
            /* teks selalu di tengah vertikal */

            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        /* Smooth hover */
        .fc-event:hover {
            transform: scale(1.03);
            transition: 0.2s;
            filter: brightness(1.05);
        }

        /* ---- WEEK/DAILY GRID (time grid) ---- */
        .fc-timegrid-slot {
            height: 2.6rem !important;
            border-color: #f3f4f7 !important;
        }

        .fc-timegrid-axis {
            font-size: 0.75rem;
            color: #95a5a6;
        }

        /* ---- RESPONSIVE MOBILE VIEW ---- */
        @media (max-width: 768px) {
            #calendar {
                padding: 10px;
                border-radius: 16px !important;
            }

            .fc-toolbar-title {
                font-size: 1.25rem !important;
            }

            .fc-button {
                border-radius: 10px !important;
                padding: 5px 10px !important;
            }

            .fc-daygrid-day-frame {
                border-radius: 10px !important;
            }
        }

        /* Garis antar kolom */
        .fc-daygrid-day {
            border-right: 1px solid #e5e5e5 !important;
            border-bottom: 1px solid #e5e5e5 !important;
        }

        /* Hilangkan border terakhir */
        .fc-daygrid-day:last-child {
            border-right: none !important;
        }

        /* Border di header hari (Senin, Selasa, ...) */
        .fc-col-header {
            background: #f8fafc;
            /* soft abu */
            border-radius: 10px;
        }

        .fc-col-header-cell-cushion {
            color: #374151 !important;
            font-weight: 600 !important;
            letter-spacing: 0.3px;
        }

        /* ===== PAGE TITLE STYLE ===== */

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

        /* Responsive */
        @media (max-width: 768px) {
            .page-title {
                font-size: 1.4rem;
            }

            .title-divider {
                width: 50px;
                height: 3px;
            }
        }

        /* ===== WRAPPER SECTION ===== */
        .notes-status-section {
            margin-top: 10px;
        }

        /* ===== LABEL (Catatan & Status) ===== */
        .section-label {
            font-size: 12px;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        /* ===== BOX CATATAN ===== */
        .notes-box {
            background: #ffffff;
            border-radius: 12px;
            padding: 12px 14px;
            min-height: 90px;
            border: 1px solid #e5e7eb;

            /* lebih soft */
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
            font-size: 14px;
            color: #374151;
        }

        /* ===== STATUS SECTION ===== */
        .status-section {
            padding-top: 4px;
        }

        /* label kecil */
        .status-label {
            font-size: 11px;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 6px;
            display: block;
        }

        /* dropdown lebih clean */
        #modalStatusSelect {
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 13px;
        }

        /* ===== MOBILE SPACING ===== */
        @media (max-width: 768px) {
            .status-section {
                margin-top: 18px;
            }

            .notes-box {
                min-height: 80px;
            }
        }

        .modal-header .btn-close {
            filter: invert(1) grayscale(100%) !important;
            opacity: 1 !important;
        }

        /* ===== MODAL BUTTON UNIFORM STYLE ===== */
        .modal-footer .btn {
            height: 38px;
            padding: 0 16px;
            border-radius: 10px;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            white-space: nowrap;
            transition: all 0.2s ease;
        }

        .modal-footer .btn-outline-secondary {
            border-radius: 10px;
        }

        .fc-scrollgrid,
        .fc-scrollgrid-sync-table,
        .fc-daygrid-body,
        .fc-col-header {
            width: 100% !important;
        }

        .fc .fc-scrollgrid-liquid {
            table-layout: fixed !important;
        }


        /* =========================
                                                                                                                                                                                                                                                                                                       🌙 GLOBAL DARK MODE
                                                                                                                                                                                                                                                                                                    ========================= */
        body.dark-mode {
            background: #0f172a;
            color: #e5e7eb;
        }

        /* =========================
                                                                                                                                                                                                                                                                                                       CALENDAR CARD
                                                                                                                                                                                                                                                                                                    ========================= */
        body.dark-mode #calendar {
            background: #111827;
            border: 1px solid #1f2937;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        /* =========================
                                                                                                                                                                                                                                                                                                       TOOLBAR
                                                                                                                                                                                                                                                                                                    ========================= */
        body.dark-mode .fc-header-toolbar {
            background: linear-gradient(135deg, #1e293b, #0f172a);
            color: #fff;
        }

        /* title */
        body.dark-mode .fc-toolbar-title {
            background: rgba(255, 255, 255, 0.05);
            color: #fff !important;
        }

        /* button */
        body.dark-mode .fc-button {
            background: rgba(255, 255, 255, 0.08) !important;
            color: #e5e7eb !important;
        }

        body.dark-mode .fc-button:hover {
            background: rgba(255, 255, 255, 0.18) !important;
        }

        /* active */
        body.dark-mode .fc-button-primary {
            background: #ffffff !important;
            color: #000 !important;
        }

        /* =========================
                                                                                                                                                                                                                                                                                                       GRID CALENDAR
                                                                                                                                                                                                                                                                                                    ========================= */
        body.dark-mode .fc-daygrid-day {
            background: #111827;
            border-color: #1f2937 !important;
        }

        body.dark-mode .fc-daygrid-day:hover {
            background: rgba(59, 130, 246, 0.15);
        }

        /* tanggal */
        body.dark-mode .fc-daygrid-day-number {
            color: #cbd5f5;
        }

        /* header hari */
        body.dark-mode .fc-col-header {
            background: #1f2937;
        }

        body.dark-mode .fc-col-header-cell-cushion {
            color: #e5e7eb !important;
        }

        /* =========================
                                                                                                                                                                                                                                                                                                       TIME GRID
                                                                                                                                                                                                                                                                                                    ========================= */
        body.dark-mode .fc-timegrid-slot {
            border-color: #1f2937 !important;
        }

        body.dark-mode .fc-timegrid-axis {
            color: #9ca3af;
        }

        /* =========================
                                                                                                                                                                                                                                                                                                       MODAL DARK
                                                                                                                                                                                                                                                                                                    ========================= */
        body.dark-mode .modal-content {
            background: #111827;
            color: #e5e7eb;
        }

        body.dark-mode .modal-body {
            background: #0f172a;
            color: #d1d5db;
        }

        body.dark-mode .modal-header {
            background: linear-gradient(135deg, #1e293b, #020617);
        }

        body.dark-mode .notes-box {
            background: #1f2937;
            border: 1px solid #374151;
            color: #e5e7eb;
        }

        /* =========================
                                                                                                                                                                                                                                                                                                       SELECT & INPUT
                                                                                                                                                                                                                                                                                                    ========================= */
        body.dark-mode #modalStatusSelect {
            background-color: #1f2937;
            border: 1px solid #374151;
            color: #e5e7eb;
        }

        body.dark-mode #modalStatusSelect:hover {
            background-color: #111827;
        }

        body.dark-mode #modalStatusSelect:focus {
            border-color: #22c55e;
        }

        /* =========================
                                                                                                                                                                                                                                                                                                       TOOLTIP
                                                                                                                                                                                                                                                                                                    ========================= */
        body.dark-mode .custom-tooltip {
            background: rgba(0, 0, 0, 0.9);
        }

        /* =========================
                                                                                                                                                                                                                                                                                                       PAGE TITLE
                                                                                                                                                                                                                                                                                                    ========================= */
        body.dark-mode .page-title {
            color: #e5e7eb;
        }

        /* =========================
                                                                                                                                                                                                                                                                                       DARK MODE - JAM KIRI (TIMEGRID)
                                                                                                                                                                                                                                                                                    ========================= */
        body.dark-mode .fc-timegrid-axis,
        body.dark-mode .fc-timegrid-slot-label {
            color: #000000 !important;
            font-weight: 600;
        }




        /* =========================
                                                                                                                                                                                                                           FULL DARK MODE - LIST VIEW (FINAL SAFE VERSION)
                                                                                                                                                                                                                        ========================= */

        /* =========================
                                                                                                                       🌙 FULLCALENDAR LIST DARK MODE FIX (SAFE)
                                                                                                                       ========================= */

        /* HEADER */
        body.dark-mode .fc-list-day {
            background: #1f2937 !important;
            border-left: 4px solid #374151 !important;
        }

        /* IMPORTANT: cover semua layer header tanpa ganggu layout */
        body.dark-mode .fc-list-day td,
        body.dark-mode .fc-list-day th {
            background: #1f2937 !important;
        }

        /* teks hari + tanggal */
        body.dark-mode .fc .fc-list-day-cushion {
            background: transparent !important;
            color: #f9fafb !important;
        }

        /* kalau ada link di header */
        body.dark-mode .fc .fc-list-day-cushion a {
            color: #f9fafb !important;
            text-decoration: none !important;
        }

        /* =========================
                                                                                                                       EVENT CARD
                                                                                                                       ========================= */
        body.dark-mode .fc-list-event {
            background: #111827 !important;
            border-radius: 14px !important;
            margin-bottom: 10px !important;
        }

        /* =========================
                                                                                                                       CELL (NO LAYOUT CHANGE)
                                                                                                                       ========================= */
        body.dark-mode .fc-list-event td {
            background: transparent !important;
            border: none !important;
            padding: 0.9rem 1rem !important;
            vertical-align: middle !important;
        }

        /* =========================
                                                                                                                       TIME (NO SIZE CHANGE)
                                                                                                                       ========================= */
        body.dark-mode .fc-list-event-time {
            color: #9ca3af !important;
        }

        /* =========================
                                                                                                                       TITLE (ONLY COLOR)
                                                                                                                       ========================= */
        body.dark-mode .fc-list-event-title {
            color: #f9fafb !important;
        }

        /* =========================
                                                                                                                       LINK (NO LAYOUT CHANGE)
                                                                                                                       ========================= */
        body.dark-mode .fc-list-event a {
            color: #f9fafb !important;
            text-decoration: none !important;
            font-family: 'Poppins', 'Inter', sans-serif;
        }

        /* =========================
                                                                                                                       DOT (KEEP POSITION)
                                                                                                                       ========================= */
        body.dark-mode .fc-list-event-dot {
            margin: 0 auto;
        }

        /* =========================
                                                                                                                       HOVER (COLOR ONLY, NO SHIFT)
                                                                                                                       ========================= */
        body.dark-mode .fc-list-event:hover .fc-list-event-title {
            color: #e0f2fe !important;
        }

        body.dark-mode .fc-list-event:hover .fc-list-event-time {
            color: #60a5fa !important;
        }

        /* ===============================
                                                                                                                                                                                                                   🌙 DARK MODE NOTES & STATUS FIX
                                                                                                                                                                                                                ================================= */

        body.dark-mode .section-label {
            color: #e5e7eb !important;
            /* putih soft */
        }

        body.dark-mode .notes-box {
            background: #1e293b !important;
            border: 1px solid #334155 !important;
            color: #e5e7eb !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.4);
        }

        /* status label */
        body.dark-mode .status-label {
            color: #e5e7eb !important;
        }

        /* dropdown select */
        body.dark-mode #modalStatusSelect {
            background-color: transparent !important;
            border: 1px solid #334155 !important;
            color: #e5e7eb !important;
        }

        /* option dropdown (biar gak hitam di hitam) */
        body.dark-mode #modalStatusSelect option {
            background: #0f172a;
            color: #e5e7eb;
        }

        /* mobile tetap aman */
        body.dark-mode .status-section {
            color: #e5e7eb;
        }

        /* WRAPPER HARUS TRANSPARENT */
        body.dark-mode .fc {
            background: transparent !important;
        }

        body.dark-mode .fc-header-toolbar {
            background: linear-gradient(90deg, rgba(59, 130, 246, 0.5), rgba(14, 165, 233, 0.4)) !important;
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
        let calendar;

        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');

            const tooltip = document.createElement('div');
            tooltip.className = 'custom-tooltip';
            document.body.appendChild(tooltip);

            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'id',
                noEventsContent: 'Tidak ada jadwal',
                allDaySlot: false,
                slotMinTime: '06:00:00',
                slotMaxTime: '22:00:00',
                expandRows: true,
                height: 'auto',
                contentHeight: 'auto',
                aspectRatio: 1.5,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                buttonText: {
                    today: 'Hari Ini',
                    month: 'Bulan',
                    week: 'Minggu',
                    day: 'Hari',
                    list: 'Daftar'
                },
                events: @json($appointments ?? []),
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                },

                eventDidMount: function(info) {
                    const colors = {
                        'Pending': '#f39c12',
                        'Processing': '#3498db',
                        'Confirmed': '#2ecc71',
                        'Cancelled': '#e74c3c',
                        'Completed': '#16a085',
                        'Rescheduled': '#9b59b6',
                        'On Hold': '#7f8c8d',
                        'No Show': '#e67e22'
                    };
                    info.el.style.backgroundColor = colors[info.event.extendedProps.status] ||
                        '#95a5a6';
                    info.el.style.color = '#fff';
                    info.el.style.borderRadius = '6px';
                    info.el.style.border = 'none';

                    // Tooltip
                    info.el.addEventListener('mouseenter', e => {
                        const desc = info.event.extendedProps.description ||
                            'Tidak ada keterangan.';
                        tooltip.innerHTML = `<strong>${info.event.title}</strong><br>${desc}`;
                        tooltip.style.opacity = 1;
                        tooltip.style.left = e.pageX + 10 + 'px';
                        tooltip.style.top = e.pageY + 10 + 'px';
                    });
                    info.el.addEventListener('mousemove', e => {
                        tooltip.style.left = e.pageX + 10 + 'px';
                        tooltip.style.top = e.pageY + 10 + 'px';
                    });
                    info.el.addEventListener('mouseleave', () => tooltip.style.opacity = 0);
                },

                eventClick: function(info) {
                    const ev = info.event;
                    $('#modalAppointmentId').val(ev.id);
                    $('#modalAppointmentName').text(ev.extendedProps.name || ev.title || '-');
                    $('#modalService').text(ev.extendedProps.service_title || '-');
                    $('#modalEmail').text(ev.extendedProps.email || '-');
                    $('#modalPhone').text(ev.extendedProps.phone || '-');
                    $('#modalEmployee').text(ev.extendedProps.employee || '-');
                    $('#modalAmount').text(ev.extendedProps.amount || '-');
                    $('#modalNotes').text(ev.extendedProps.description || '-');

                    const start = new Date(ev.start);
                    const end = ev.end ? new Date(ev.end) : null;

                    const dateOptions = {
                        weekday: 'long',
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    };

                    const timeOptions = {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: false
                    };

                    // Tanggal saja
                    $('#modalDate').text(
                        start.toLocaleDateString('id-ID', dateOptions)
                    );

                    // People Count
                    $('#modalPeople').text(
                        ev.extendedProps.people_count ?
                        ev.extendedProps.people_count + ' Orang' :
                        '-'
                    );

                    // Background
                    if (ev.extendedProps.background_name) {
                        $('#modalBackground').text(ev.extendedProps.background_name);
                        $('#backgroundWrapper').show();
                    } else {
                        $('#backgroundWrapper').hide();
                    }


                    // Jam mulai
                    $('#modalStartTime').text(
                        start.toLocaleTimeString('id-ID', timeOptions) + ' WIB'
                    );

                    // Jam selesai
                    $('#modalEndTime').text(
                        end ? end.toLocaleTimeString('id-ID', timeOptions) + ' WIB' : '-'
                    );


                    // Format Rupiah
                    const amount = ev.extendedProps.amount || 0;
                    const formattedAmount = new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(amount);

                    $('#modalAmount').text(formattedAmount);


                    const status = ev.extendedProps.status || 'Pending';
                    $('#modalStatusSelect').val(status);

                    const statusLabels = {
                        'Pending': 'Menunggu',
                        'Processing': 'Diproses',
                        'Confirmed': 'Dikonfirmasi',
                        'Cancelled': 'Dibatalkan',
                        'Completed': 'Selesai',
                        'Rescheduled': 'Jadwal Ulang',
                        'On Hold': 'Ditahan',
                        'No Show': 'Tidak Hadir'
                    };

                    const badgeColors = {
                        'Pending': '#f39c12',
                        'Processing': '#3498db',
                        'Confirmed': '#2ecc71',
                        'Cancelled': '#e74c3c',
                        'Completed': '#16a085',
                        'Rescheduled': '#9b59b6',
                        'On Hold': '#7f8c8d',
                        'No Show': '#e67e22'
                    };

                    $('#modalStatusBadge').html(
                        `<span class="status-badge" style="background-color:${badgeColors[status] || '#6c757d'};">
                            ${statusLabels[status] || status}
                        </span>`
                    );

                    const modalEl = document.getElementById('appointmentModal');

                    if (modalEl) {
                        const modal = new bootstrap.Modal(modalEl);
                        modal.show();
                    }
                }
            });

            calendar.render();
        });

        let resizeAnimation;

        function smoothCalendarResize() {
            const duration = 350;
            const start = performance.now();

            // kalau ada animasi lama, stop dulu biar tidak numpuk
            if (resizeAnimation) {
                cancelAnimationFrame(resizeAnimation);
            }

            function easeOutCubic(t) {
                return 1 - Math.pow(1 - t, 3);
            }

            function animate(now) {
                const elapsed = now - start;
                let progress = Math.min(elapsed / duration, 1);
                let eased = easeOutCubic(progress);

                // updateSize jangan tiap frame “keras”, cukup saat progress tertentu
                // biar tidak jitter
                if (progress === 1 || Math.floor(eased * 10) !== Math.floor((eased - 0.05) * 10)) {
                    if (calendar) {
                        calendar.updateSize();
                    }
                }

                if (progress < 1) {
                    resizeAnimation = requestAnimationFrame(animate);
                } else {
                    // final adjustment biar 100% pas
                    if (calendar) {
                        calendar.updateSize();
                    }
                }
            }

            resizeAnimation = requestAnimationFrame(animate);
        }

        $(document).on('collapsed.lte.pushmenu shown.lte.pushmenu', function() {
            smoothCalendarResize();
        });

        // debounce resize biar tidak spam saat drag window
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                smoothCalendarResize();
            }, 120);
        });
    </script>
@stop
