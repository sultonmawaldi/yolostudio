@extends('adminlte::page')

@section('title', 'All Appointments')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>Daftar Janji Temu</h1>
    </div>
</div>
@stop

@section('content')
<!-- Modal -->
<form id="appointmentStatusForm" method="POST" action="{{ route('appointments.update.status') }}">
@csrf
<input type="hidden" name="appointment_id" id="modalAppointmentId">

<!-- ========== MODAL DETAIL JANJI TEMU (PREMIUM STYLE) ========== -->
<div class="modal fade" id="appointmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-check me-2"></i> Detail Janji Temu
                </h5>
                <button type="button" class="btn-close btn-close-white" data-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <div class="text-center mb-3">
                    <span class="badge bg-light text-dark shadow-sm px-3 py-2" id="modalBookingId">ID Pemesanan: N/A</span>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <p><strong>Klien :</strong><br><span id="modalAppointmentName">N/A</span></p>
                        <p><strong>Layanan :</strong><br><span id="modalService">N/A</span></p>
                        <p><strong>Email :</strong><br><span id="modalEmail">N/A</span></p>
                        <p><strong>Telepon :</strong><br><span id="modalPhone">N/A</span></p>
                        <p><strong>Jumlah Orang :</strong><br><span id="modalPeopleCount">N/A</span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Staf :</strong><br><span id="modalStaff">N/A</span></p>
                        <p><strong>Tanggal :</strong><br><span id="modalDate">N/A</span></p>
                        <p><strong>Waktu Mulai :</strong><br><span id="modalStartTime">N/A</span></p>
                        <p><strong>Waktu Selesai :</strong><br><span id="modalEndTime">N/A</span></p>
                        <p><strong>Total Biaya :</strong><br><span id="modalAmount">N/A</span></p>
                    </div>
                </div>

                <div class="mt-3">
                    <p><strong>Catatan :</strong></p>
                    <div id="modalNotes" class="p-2 bg-white border">N/A</div>
                </div>

                <hr class="my-3">

                <div class="text-center mb-3">
                    <label class="fw-semibold d-block mb-1">Status Saat Ini :</label>
                    <span id="modalStatusBadge" class="badge bg-secondary px-3 py-2">N/A</span>
                </div>

                <div class="form-group">
                    <label for="modalStatusSelect" class="fw-semibold mb-1">Ubah Status :</label>
                    <select name="status" class="form-select" id="modalStatusSelect">
                        <option value="Pending">Menunggu</option>
                        <option value="Processing">Diproses</option>
                        <option value="Confirmed">Dikonfirmasi</option>
                        <option value="Cancelled">Dibatalkan</option>
                        <option value="Completed">Selesai</option>
                        <option value="On Hold">Ditunda</option>
                        <option value="Rescheduled">Dijadwalkan Ulang</option>
                        <option value="No Show">Tidak Hadir</option>
                    </select>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Tutup
                </button>
                <button type="submit" class="btn btn-gradient-success">
                    <i class="fas fa-sync-alt me-1"></i> Perbarui Status
                </button>
            </div>

        </div>
    </div>
</div>
</form>

@if (session('success'))
<div class="alert alert-success alert-dismissable">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <strong>{{ session('success') }}</strong>
</div>
@endif

<section class="content">
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card py-2 px-3">

                <!-- ================= FILTER BAR ================= -->
                <div class="row mb-3">
                <!-- Staff Filter -->
                <div class="col-md-4 mb-2">
                    <label><strong>Staf : </strong></label>
                    <select id="filterStaff" class="form-control filter-select">
                        <option value="">Semua Staf</option>
                        @foreach ($appointments->pluck('employee.user.name')->unique() as $staff)
                            <option value="{{ $staff }}">{{ $staff }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Service Filter -->
                <div class="col-md-4 mb-2">
                    <label><strong>Layanan : </strong></label>
                    <select id="filterService" class="form-control filter-select">
                        <option value="">Semua Layanan</option>
                        @foreach ($appointments->pluck('service.title')->unique() as $service)
                            <option value="{{ $service }}">{{ $service }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Filter -->
                <div class="col-md-4 mb-2">
                    <label><strong>Tanggal : </strong></label>
                    <div class="d-flex flex-wrap gap-2 mb-2 date-filter-wrapper">
                        <button type="button" class="btn date-filter-btn active" data-value="">
                             Semua
                        </button>
                        <button type="button" class="btn date-filter-btn" data-value="upcoming">
                             Akan Datang
                        </button>
                        <button type="button" class="btn date-filter-btn" data-value="past">
                             Sudah Lewat
                        </button>
                        <button type="button" class="btn date-filter-btn" data-value="range">
                             Rentang Tanggal
                        </button>
                    </div>
                    <input type="text" id="dateRangePicker" class="form-control d-none" placeholder="Pilih rentang tanggal...">
                </div>
            </div>
                <!-- ================= END FILTER BAR ================= -->

                <div class="card-body p-0">
                    <table id="myTable" class="table table-striped projects">
                        <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>Klien</th>
                            <th>Layanan</th>
                            <th>Staf</th>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th class="text-center">Status</th>
                            <th>Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
    @php
        $statusColors = [
            'Pending' => '#f39c12',
            'Processing' => '#3498db',
            'Confirmed' => '#2ecc71',
            'Cancelled' => '#ff0000',
            'Completed' => '#008000',
            'On Hold' => '#95a5a6',
            'Rescheduled' => '#f1c40f',
            'No Show' => '#e67e22',
        ];

        $appointments = $appointments->sortBy([
            ['booking_date', 'asc'],
            ['booking_start_time', 'asc']
        ]);
    @endphp

    @foreach ($appointments as $appointment)
    <tr>
        <td data-label="#"> {{ $loop->iteration }} </td>
        <td data-label="Klien">
            <div class="font-weight-semibold text-dark">{{ $appointment->name }}</div>
        </td>
        <td data-label="Layanan">{{ $appointment->service->title ?? 'N/A' }}</td>
        <td data-label="Staf">{{ $appointment->employee->user->name }}</td>
        <td data-label="Tanggal" data-date="{{ $appointment->booking_date }}" data-order="{{ $appointment->booking_date }}">
            {{ \Carbon\Carbon::parse($appointment->booking_date)->translatedFormat('l, d M Y') }}
        </td>
        <td data-label="Waktu">
            {{ \Carbon\Carbon::createFromFormat('H:i:s', $appointment->booking_start_time)->format('H:i') }}
            -
            {{ \Carbon\Carbon::createFromFormat('H:i:s', $appointment->booking_end_time)->format('H:i') }} WIB
        </td>
        <td data-label="Status" class="text-center">
            @php
                $status = $appointment->status;
                $color = $statusColors[$status] ?? '#7f8c8d';
            @endphp
            <span class="badge px-3 py-2"
                style="background-color: {{ $color }}; color: #fff; border-radius: 30px;">
                {{ $status }}
            </span>
        </td>
        <td data-label="Aksi">
            <button class="btn btn-sm btn-outline-primary rounded-pill view-appointment-btn"
                data-toggle="modal" data-target="#appointmentModal"
                data-id="{{ $appointment->id }}"
                data-booking="{{ $appointment->booking_id }}"
                data-name="{{ $appointment->name }}"
                data-service="{{ $appointment->service->title ?? 'N/A' }}"
                data-email="{{ $appointment->email }}"
                data-phone="{{ $appointment->phone }}"
                data-people="{{ $appointment->people_count ?? '-' }}"
                data-employee="{{ $appointment->employee->user->name }}"
                data-date="{{ $appointment->booking_date }}"
                data-start_time="{{ $appointment->booking_start_time }}"
                data-end_time="{{ $appointment->booking_end_time }}"
                data-amount="{{ $appointment->amount }}"
                data-notes="{{ $appointment->notes }}"
                data-status="{{ $appointment->status }}">
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
                        </section>
                        @stop

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
/* ======== Kartu Utama ======== */
.card {
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

/* ======== Filter Seragam ======== */
#filterDate, #filterStaff, #filterService, #dateRangePicker, .filter-select {
    border-radius: 50px;
    padding: 0.45rem 1rem;
    font-size: 0.9rem;
    background: #f8f9fa;
    border: 1px solid #e2e6ea;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    color: #495057;
    transition: all 0.3s ease;
}
#filterDate:focus, #filterStaff:focus, #filterService:focus, #dateRangePicker:focus {
    box-shadow: 0 0 0 0.25rem rgba(108,117,125,0.3);
    background-color: #fff;
    border-color: #6abfe3;
}
#filterDate:hover, #filterStaff:hover, #filterService:hover, #dateRangePicker:hover {
    background-color: #fff;
    border-color: #6abfe3;
}

/* ======== Tombol Filter (Hari Ini, Besok, dst) ======== */
.date-filter-wrapper {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    justify-content: center;
}
.date-filter-btn {
    border-radius: 50px;
    padding: 0.45rem 1rem;
    font-size: 0.85rem;
    border: 1px solid #6abfe3;
    color: #6abfe3;
    background-color: #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    transition: all 0.3s;
}
.date-filter-btn:hover {
    background-color: #6abfe3;
    color: #fff;
    border-color: #6abfe3;
}
.date-filter-btn.active {
    background: linear-gradient(90deg, #6abfe3, #7873f5);
    color: #fff;
    border-color: #6abfe3;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
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
    font-weight: 700;
    text-transform: uppercase;
    padding: 14px 12px;
    letter-spacing: 0.5px;
    white-space: nowrap;
    text-align: center;
    border-bottom: 2px solid rgba(0,123,255,0.25);

    /* hilangkan bg default tiap kolom */
    background: transparent !important;
}

/* Isi Tabel */
#myTable td {
    vertical-align: middle;
    text-align: center;
    border-bottom: 1px solid rgba(0,0,0,0.05);
    padding: 10px 12px;
}

#myTable tbody tr:hover {
    background-color: #f7faff;
    transition: 0.25s ease;
}


/* ======== Responsive Card View (Mobile & Tablet) ======== */
@media (max-width: 1024px) {
    #myTable {
        border: none;
    }
    #myTable thead {
        display: none;
    }
    #myTable tbody {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 15px;
    }
    #myTable tr {
        display: flex;
        flex-direction: column;
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        padding: 1rem;
        width: 90%;
        max-width: 420px;
        transition: transform 0.2s ease;
    }
    #myTable tr:hover {
        transform: translateY(-4px);
    }
    #myTable td {
    display: grid;
    grid-template-columns: 140px auto; /* kolom kiri tetap, kanan fleksibel */
    align-items: center; /* rata tengah vertikal */
    gap: 4px;
    padding: 6px 0;
    font-size: 0.95rem;
}

#myTable td::before {
    content: attr(data-label) " :";
    font-weight: 600;
    color: #444;
    text-align: left;
    white-space: nowrap; /* mencegah label turun ke bawah */
}

}

/* ======== Animasi Ringan ======== */
@keyframes fadeIn {
    from {opacity: 0; transform: translateY(10px);}
    to {opacity: 1; transform: translateY(0);}
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

/* ===== Info Grid ===== */
.info-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 8px 16px;
}

.info-grid div {
  display: grid;
  grid-template-columns: 120px auto;
  align-items: center;
  background: #fff;
  border-radius: 10px;
  padding: 6px 10px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.info-grid span:first-child {
  font-weight: 600;
  color: #555;
  position: relative;
}

.info-grid span:first-child::after {
  content: " :";
  position: absolute;
  right: 4px;
}

.info-grid span:last-child {
  color: #222;
  padding-left: 5px;
  text-align: left;
  overflow-wrap: break-word;
}

/* Waktu mulai & selesai sejajar horizontal */
.time-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
  grid-column: span 2;
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
  box-shadow: 0 3px 10px rgba(0,176,155,0.3);
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

#myTable_filter {
    width: 100%; /* pastikan mengambil full row */
    display: flex;
    justify-content: center;
}
#myTable_filter input {
    margin: 0; /* hilangkan margin default */
}




</style>
@stop


@section('js')
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
$(document).ready(function() {
    // ================= DATATABLE =================
    var table = $('#myTable').DataTable({
        responsive: true,
        dom: "<'row mb-3'<'col-12 d-flex justify-content-center'f>>" + "rtip",
        order: [[4,'asc']],
        language: {
            search: "",
            searchPlaceholder: "Cari...",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Berikutnya",
                previous: "Sebelumnya"
            },
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ janji temu",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 janji temu",
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

    $('.date-filter-btn').on('click', function(){
        $('.date-filter-btn').removeClass('active');
        $(this).addClass('active');

        var val = $(this).data('value');
        dateFilterType = val;

        if(val === 'range'){
            $('#dateRangePicker').removeClass('d-none').val('');
        } else {
            $('#dateRangePicker').addClass('d-none').val('');
        }

        applyFilters();
    });

    flatpickr("#dateRangePicker", {
        mode: "range",
        dateFormat: "Y-m-d",
        locale: {firstDayOfWeek:1},
        onChange: function(selectedDates){
            if(selectedDates.length === 2){
                dateRange = selectedDates;
                dateFilterType = 'range';
                applyFilters();
            }
        }
    });

    $('#filterStaff, #filterService').on('change', function(){ applyFilters(); });

    function applyFilters() {
    $.fn.dataTable.ext.search = [];

    // ================= FILTER TANGGAL =================
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        var today = new Date(); today.setHours(0,0,0,0);
        var dateAttr = $(table.row(dataIndex).node()).find('td:eq(4)').data('date');
        var startTimeAttr = $(table.row(dataIndex).node()).find('td:eq(5)').text().split(' - ')[0]; // HH:mm

        // Buat Date object dari tanggal + jam
        var dateParts = dateAttr.split('-');
        var timeParts = startTimeAttr.split(':');
        var bookingDateTime = new Date(
            parseInt(dateParts[0]),
            parseInt(dateParts[1])-1,
            parseInt(dateParts[2]),
            parseInt(timeParts[0]),
            parseInt(timeParts[1])
        );

        if(dateFilterType === 'upcoming') return bookingDateTime >= today;
        if(dateFilterType === 'past') return bookingDateTime < today;
        if(dateFilterType === 'range' && dateRange.length === 2){
            var start = new Date(dateRange[0]);
            var end = new Date(dateRange[1]);
            return bookingDateTime >= start && bookingDateTime <= end;
        }

        return true;
    });

    // ================= FILTER STAFF =================
    var staffVal = $('#filterStaff').val().toLowerCase();
    if(staffVal){
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex){
            return data[3].toLowerCase().includes(staffVal);
        });
    }

    // ================= FILTER LAYANAN =================
    var serviceVal = $('#filterService').val().toLowerCase();
    if(serviceVal){
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex){
            return data[2].toLowerCase().includes(serviceVal);
        });
    }

    table.draw();

    // ================= SORT OTOMATIS UNTUK PAST =================
    if(dateFilterType === 'past'){
        table.rows().every(function(rowIdx, tableLoop, rowLoop){
            var row = $(this.node());
            var dateAttr = row.find('td:eq(4)').data('date');
            var startTimeAttr = row.find('td:eq(5)').text().split(' - ')[0];
            var dateParts = dateAttr.split('-');
            var timeParts = startTimeAttr.split(':');
            var bookingDateTime = new Date(
                parseInt(dateParts[0]),
                parseInt(dateParts[1])-1,
                parseInt(dateParts[2]),
                parseInt(timeParts[0]),
                parseInt(timeParts[1])
            );
            $(this.node()).attr('data-timestamp', bookingDateTime.getTime());
        });
        table.order([[4,'desc'], [5,'desc']]).draw(); // urut tanggal & jam descending
    } else {
        table.order([[4,'asc'], [5,'asc']]).draw(); // default urut ascending
    }
}


    applyFilters(); // filter default

    // ================= MODAL POPULATE =================
    $(document).on('click','.view-appointment-btn',function(){
        $('#modalAppointmentId').val($(this).data('id'));
        $('#modalBookingId').text('ID Pemesanan: ' + $(this).data('booking'));
        $('#modalAppointmentName').text($(this).data('name'));
        $('#modalService').text($(this).data('service'));
        $('#modalEmail').text($(this).data('email'));
        $('#modalPhone').text($(this).data('phone'));
        $('#modalPeopleCount').text($(this).data('people'));
        $('#modalStaff').text($(this).data('employee'));
        $('#modalDate').text($(this).data('date'));
        $('#modalStartTime').text($(this).data('start_time'));
        $('#modalEndTime').text($(this).data('end_time'));
        $('#modalAmount').text($(this).data('amount'));
        $('#modalNotes').text($(this).data('notes'));

        var status = $(this).data('status');
        $('#modalStatusSelect').val(status);

        var statusColors = {
            'Pending':'linear-gradient(90deg, #f39c12, #f7c65f)',
            'Processing':'linear-gradient(90deg, #3498db, #5dade2)',
            'Confirmed':'linear-gradient(90deg, #2ecc71, #58d68d)',
            'Cancelled':'linear-gradient(90deg, #e74c3c, #ff7675)',
            'Completed':'linear-gradient(90deg, #16a085, #48c9b0)',
            'On Hold':'linear-gradient(90deg, #7f8c8d, #95a5a6)',
            'Rescheduled':'linear-gradient(90deg, #f1c40f, #f7dc6f)',
            'No Show':'linear-gradient(90deg, #e67e22, #f0b27a)'
        };
        var gradient = statusColors[status] || 'linear-gradient(90deg, #bdc3c7, #95a5a6)';
        $('#modalStatusBadge').html(`<span class="badge text-white px-3 py-2 shadow-sm" style="background: ${gradient}; border-radius:30px;">${status}</span>`);
    });

    // ================= ALERT AUTO CLOSE =================
    $(".alert").delay(6000).slideUp(300);
});
</script>
@stop

