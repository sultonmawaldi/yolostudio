@extends('adminlte::page')

@section('title', 'Jadwal Janji Temu')

@section('content_header')
    <h1 class="fw-bold text-primary text-center mb-3">
        <i class="fa fa-calendar-alt me-2"></i> Jadwal Janji Temu
    </h1>

    {{-- Alert Success --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    {{-- Alert Error --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif
@stop

@section('content')
<div class="calendar-wrapper container-fluid px-2 px-md-3 py-3">
    <div id="calendar" class="bg-white rounded-4 shadow-sm p-3 p-md-4"></div>
</div>

<!-- Modal Detail Janji Temu -->
<form id="appointmentStatusForm" method="POST" action="{{ route('dashboard.update.status') }}">
    @csrf
    <input type="hidden" name="appointment_id" id="modalAppointmentId">

    <div class="modal fade" id="appointmentModal" tabindex="-1" aria-labelledby="appointmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #007bff, #00c4ff);">
                    <h5 class="modal-title fw-semibold" id="appointmentModalLabel">
                        <i class="fa fa-calendar-check me-2"></i> Detail Janji Temu
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6 border-end">
                            <div class="d-flex flex-column gap-2">
                                <p><strong>Klien:</strong> <span id="modalAppointmentName">-</span></p>
                                <p><strong>Layanan:</strong> <span id="modalService">-</span></p>
                                <p><strong>Email:</strong> <span id="modalEmail">-</span></p>
                                <p><strong>Telepon:</strong> <span id="modalPhone">-</span></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="d-flex flex-column gap-2">
                                <p><strong>Staf:</strong> <span id="modalStaff">-</span></p>
                                <p><strong>Mulai:</strong> <span id="modalStartTime">-</span></p>
                                <p><strong>Selesai:</strong> <span id="modalEndTime">-</span></p>
                                <p><strong>Biaya:</strong> <span id="modalAmount">-</span></p>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="row g-3">
                        <div class="col-md-8">
                            <p><strong>Catatan:</strong></p>
                            <div class="p-3 bg-light rounded shadow-sm" id="modalNotes">-</div>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Status Saat Ini:</strong></p>
                            <div id="modalStatusBadge" class="fs-5 mb-2">-</div>

                            <label for="modalStatusSelect" class="fw-semibold mt-2">Ubah Status:</label>
                            <select name="status" class="form-select shadow-sm" id="modalStatusSelect">
                                <option value="Pending payment">Menunggu Pembayaran</option>
                                <option value="Processing">Sedang Diproses</option>
                                <option value="Confirmed">Dikonfirmasi</option>
                                <option value="Cancelled">Dibatalkan</option>
                                <option value="Completed">Selesai</option>
                                <option value="On Hold">Ditunda</option>
                                <option value="No Show">Tidak Hadir</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light px-4">
                    <button type="submit" class="btn btn-gradient-success shadow-sm" id="saveStatusBtn">
                        <i class="fa fa-save me-1"></i> Simpan Perubahan
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet" />
<style>
    /* ========== LAYOUT DASAR ========== */
    html {
    overflow-y: auto !important;
   }

    html, body {
    overflow-x: hidden !important;
    }
    
    .calendar-wrapper {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
    }

    #calendar {
        border-radius: 20px;
        transition: 0.3s;
    }

    #calendar:hover {
        box-shadow: 0 10px 28px rgba(0,0,0,0.1);
    }

    /* Toolbar */
    .fc-toolbar-title {
        font-weight: 700 !important;
        font-size: 1.5rem !important;
        color: #2c3e50;
    }

    .fc-button {
        border-radius: 8px !important;
        border: none !important;
        font-weight: 600 !important;
    }

    .fc-button-primary {
        background: linear-gradient(135deg, #007bff, #00b4d8) !important;
        color: #fff !important;
    }

    .fc-button-primary:hover {
        background: linear-gradient(135deg, #0069d9, #0096c7) !important;
    }

    /* Events */
    .fc-daygrid-day:hover {
        background: rgba(0, 123, 255, 0.05);
        cursor: pointer;
    }

    /* Tombol simpan */
    .btn-gradient-success {
        background: linear-gradient(45deg, #00c853, #009624);
        color: white;
        border: none;
    }

    .btn-gradient-success:hover {
        background: linear-gradient(45deg, #009624, #00c853);
    }

    /* Tooltip */
    .custom-tooltip {
        position: absolute;
        z-index: 9999;
        background: rgba(34,34,34,0.9);
        color: #fff;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 13px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
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
        background-color: rgba(0,0,0,0.45) !important;
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
    max-width: 650px !important; /* sedang dan proporsional di semua layar */
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
    border-top: none;
    background: #fff;
    padding: 1rem 1.5rem;
}

/* Catatan */
#modalNotes {
    min-height: 80px;
    font-size: 0.95rem;
    color: #444;
    border-radius: 10px;
}

/* Tombol Gradasi */
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
    box-shadow: 0 4px 14px rgba(0, 176, 155, 0.4);
}

/* Select & Badge Rapi */
#modalStatusSelect {
    border-radius: 10px;
    font-weight: 500;
    background-color: #fff;
    border: 1px solid #ddd;
    transition: 0.2s;
}
#modalStatusSelect:focus {
    border-color: #00b4d8;
    box-shadow: 0 0 5px rgba(0,180,216,0.3);
}

#modalStatusBadge .badge {
    font-size: 0.9rem;
    padding: 0.5em 1em;
    border-radius: 10px;
}

/* Animasi Muncul */
.modal.fade .modal-dialog {
    transform: translateY(-15px);
    opacity: 0;
    transition: all 0.25s ease;
}

.modal.fade.show .modal-dialog {
    transform: translateY(0);
    opacity: 1;
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

/* Waktu & judul */
.fc-list-event-time,
.fc-list-event-title {
    color: #2f3640 !important;
    font-size: 0.95rem;
    transition: color 0.3s ease;
}

/* ===== Tanggal (list-day) ===== */
.fc-list-day {
    color: #1a3c73 !important;
    background: linear-gradient(90deg, #e3f2fd, #ffffff) !important;
    font-weight: 600;
    font-size: 1rem;
    letter-spacing: 0.3px;
    border-left: 4px solid #007bff !important;
    padding: 0.75rem 1rem !important;
}

/* ===== Baris event ===== */
.fc-list-event td {
    background: #ffffff !important;
    border-bottom: 1px solid #f2f4f6 !important;
    padding: 0.85rem 1rem !important;
    position: relative;
    z-index: 1;
    overflow: hidden;
    transition: all 0.35s ease;
}

/* ===== Efek hover elegan (hanya baris aktif, tidak geser) ===== */
.fc-list-event:hover td {
    background: linear-gradient(90deg, rgba(0,123,255,0.05), rgba(0,180,216,0.08)) !important;
    box-shadow: inset 4px 0 0 #007bff, 0 3px 10px rgba(0,123,255,0.1);
    cursor: pointer;
    transition: all 0.35s ease-in-out;
}

/* ===== Garis aksen di sisi kiri (fade halus) ===== */
.fc-list-event td::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 0;
    height: 100%;
    background: linear-gradient(180deg, #007bff, #00b4d8);
    transition: width 0.4s ease;
    z-index: 0;
    border-radius: 0 4px 4px 0;
}
.fc-list-event:hover td::before {
    width: 4px;
}

/* ===== Warna teks saat hover ===== */
.fc-list-event:hover a {
    color: #004c8c !important;
    letter-spacing: 0.3px;
}
.fc-list-event:hover .fc-list-event-time {
    color: #007bff !important;
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
    box-shadow: 0 10px 25px rgba(0,0,0,0.07);
    border: 1px solid #f1f3f4;
}

/* ---- HEADER TITLE (iOS BOLD STYLE) ---- */
.fc-toolbar-title {
    font-weight: 800 !important;
    font-size: 1.65rem !important;
    color: #1a1d21 !important;
    letter-spacing: -0.3px;
}

/* ---- HEADER NAV BUTTONS (rounded soft) ---- */
.fc-button {
    border-radius: 12px !important;
    padding: 6px 14px !important;
    border: none !important;
    font-weight: 600 !important;
    background: #f1f3f4 !important;
    color: #333 !important;
    transition: 0.25s;
}

.fc-button:hover {
    background: #e5e8ea !important;
}

/* Today Button */
.fc-button-primary {
    background: #007aff !important;
    color: #fff !important;
    box-shadow: 0 2px 7px rgba(0,122,255,0.25);
}
.fc-button-primary:hover {
    background: #006be6 !important;
}

/* ---- WEEKDAY HEADER (Mon, Tue...) ---- */
.fc-col-header-cell-cushion {
    font-weight: 700 !important;
    color: #2d3436 !important;
    padding: 10px 0 !important;
    font-size: 0.9rem !important;
}

/* ---- DAY CELLS ---- */
.fc-daygrid-day {
    border: none !important;
    padding: 6px !important;
}

.fc-daygrid-day-frame {
    border-radius: 14px !important;
    transition: 0.25s ease;
}

.fc-daygrid-day-frame:hover {
    background: #f3f7ff !important;
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
    padding: 4px 6px !important;
    font-size: 0.78rem !important;
    font-weight: 600 !important;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
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

/* ---- LIST VIEW ---- */
.fc-list-day {
    background: #eef3ff !important;
    border-left: 5px solid #007aff !important;
    padding: 12px 10px !important;
    font-weight: 700 !important;
    color: #2c3e50 !important;
}

.fc-list-event {
    border-radius: 14px !important;
    margin-bottom: 8px !important;
    background: #fff !important;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    transition: 0.25s ease;
}

.fc-list-event:hover {
    transform: scale(1.02);
    background: #f4f9ff !important;
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
.fc-col-header-cell {
    border-right: 1px solid #e5e5e5 !important;
}
.fc-col-header-cell:last-child {
    border-right: none !important;
}



</style>
@stop


@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const tooltip = document.createElement('div');
    tooltip.className = 'custom-tooltip';
    document.body.appendChild(tooltip);

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'id',
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
        eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },

        eventDidMount: function(info) {
            const colors = {
                'Pending payment': '#f39c12',
                'Processing': '#3498db',
                'Confirmed': '#2ecc71',
                'Cancelled': '#e74c3c',
                'Completed': '#16a085',
                'On Hold': '#7f8c8d',
                'No Show': '#d35400'
            };
            info.el.style.backgroundColor = colors[info.event.extendedProps.status] || '#95a5a6';
            info.el.style.color = '#fff';
            info.el.style.borderRadius = '6px';
            info.el.style.border = 'none';

            // Tooltip
            info.el.addEventListener('mouseenter', e => {
                const desc = info.event.extendedProps.description || 'Tidak ada keterangan.';
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
            $('#modalStaff').text(ev.extendedProps.staff || '-');
            $('#modalAmount').text(ev.extendedProps.amount || '-');
            $('#modalNotes').text(ev.extendedProps.description || '-');

            const start = new Date(ev.start);
            const end = ev.end ? new Date(ev.end) : null;
            const options = { weekday:'long', day:'numeric', month:'long', year:'numeric', hour:'2-digit', minute:'2-digit', hour12:false };
            $('#modalStartTime').text(start.toLocaleString('id-ID', options));
            $('#modalEndTime').text(end ? end.toLocaleString('id-ID', options) : '-');

            const status = ev.extendedProps.status || 'Pending payment';
            $('#modalStatusSelect').val(status);

            const badgeColors = {
                'Pending payment': '#f39c12',
                'Processing': '#3498db',
                'Confirmed': '#2ecc71',
                'Cancelled': '#e74c3c',
                'Completed': '#16a085',
                'On Hold': '#7f8c8d',
                'No Show': '#d35400'
            };
            $('#modalStatusBadge').html(`<span class="badge" style="background-color:${badgeColors[status]};color:white">${status}</span>`);

            const modal = new bootstrap.Modal(document.getElementById('appointmentModal'));
            modal.show();
        }
    });

    calendar.render();
});

window.addEventListener('shown.bs.modal', () => {
  document.body.style.paddingRight = '0px';
});
window.addEventListener('hidden.bs.modal', () => {
  document.body.style.paddingRight = '0px';
});
</script>
@stop
