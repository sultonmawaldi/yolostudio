@extends('adminlte::page')

@section('title', 'Daftar Transaksi')

@section('content_header')
    <div class="page-title-wrapper text-center mb-4">
        <h1 class="page-title">
            <i class="fa fa-receipt me-2"></i>
            Daftar Transaksi
        </h1>
        <div class="title-divider"></div>
    </div>
@stop

@section('content')

    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-body">

            <!-- ================= FILTER KARYAWAN & LAYANAN ================= -->
            <div class="row mb-3 g-3 align-items-end date-filter-wrapper">

                <!-- Karyawan -->
                <div class="col-md-4">
                    <label class="filter-label" for="filterEmployee"><i class="fas fa-users me-2"></i> Pilih Karyawan </label>

                    <select id="filterEmployee" class="form-select filter-select">
                        @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('moderator'))
                            <option value="">Semua Karyawan</option>
                            @foreach ($employees as $employee)
                                @if ($employee->user->hasRole('employee'))
                                    {{-- Hanya tampilkan yang role employee --}}
                                    <option value="{{ $employee->id }}">
                                        {{ $employee->user->name ?? '-' }}
                                    </option>
                                @endif
                            @endforeach
                        @elseif(auth()->user()->hasRole('employee'))
                            <option value="{{ auth()->user()->employee->id }}" selected>
                                {{ auth()->user()->name }}
                            </option>
                        @endif
                    </select>
                </div>

                <!-- Layanan -->
                <div class="col-md-4">
                    <label class="filter-label" for="filterService"><i class="fas fa-briefcase me-2"></i> Pilih Layanan
                    </label>

                    <select id="filterService" class="form-select filter-select">
                        <option value="">Semua Layanan</option>
                        @foreach ($services as $service)
                            <option value="{{ $service->id }}">
                                {{ $service->title ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>
            <!-- ================= END FILTER ================= -->

            <div class="table-responsive">
                <table id="transactionsTable" class="table align-middle table-hover table-borderless">
                    <thead class="bg-gradient text-white" style="background: linear-gradient(90deg, #007bff, #00b4d8);">
                        <tr>
                            <th>#</th>
                            <th>Kode</th>
                            <th>ID Pemesanan</th>
                            <th>Pengguna</th>
                            <th>Layanan</th>
                            <th>Karyawan</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Dibuat</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $transaction)
                            <tr class="bg-white shadow-sm-hover"
                                data-employee-id="{{ $transaction->appointment->employee_id ?? '' }}"
                                data-service-id="{{ $transaction->appointment->service_id ?? '' }}">
                                <td class="fw-semibold text-muted">{{ $loop->iteration }}</td>
                                <td class="fw-bold text-dark">{{ $transaction->transaction_code }}</td>
                                <td>{{ $transaction->appointment->booking_id ?? '-' }}</td>
                                <td>{{ $transaction->appointment->name ?? '-' }}</td>
                                <td>{{ $transaction->appointment->service->title ?? '-' }}</td>
                                <td>{{ $transaction->appointment->employee->user->name ?? '-' }}</td>
                                <td class="fw-bold text-primary">Rp
                                    {{ number_format($transaction->total_amount ?? 0, 0, ',', '.') }}</td>
                                <td>
                                    @php
                                        $status = $transaction->payment_status ?? 'Pending';

                                        // Mapping Bahasa Indonesia
                                        $statusLabel = [
                                            'Pending' => 'Menunggu',
                                            'DP' => 'Uang Muka',
                                            'Paid' => 'Lunas',
                                            'Failed' => 'Gagal',
                                        ];

                                        $badgeClass = match ($status) {
                                            'Paid' => 'bg-gradient-success',
                                            'DP' => 'bg-gradient-warning',
                                            'Failed' => 'bg-gradient-danger',
                                            default => 'bg-gradient-secondary',
                                        };

                                        $label = $statusLabel[$status] ?? $status;
                                    @endphp

                                    <span class="badge text-white px-3 py-2 rounded-pill shadow-sm {{ $badgeClass }}">
                                        {{ $label }}
                                    </span>
                                </td>
                                <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                                <td class="text-center">
                                    <!-- Tombol Utama Edit & Hapus -->
                                    <div class="d-flex justify-content-center flex-wrap gap-2 mb-1">
                                        <!-- Tombol Edit -->
                                        <a href="{{ route('transactions.edit', $transaction->id) }}"
                                            class="btn btn-sm btn-outline-info action-btn" title="Edit Transaksi">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- Tombol Hapus -->
                                        <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST"
                                            class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger action-btn"
                                                title="Hapus Transaksi">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Tombol DP Lunasi -->
                                    @if ($transaction->payment_status === 'DP')
                                        <div class="d-flex justify-content-center flex-wrap gap-2 mt-1">
                                            <a href="{{ route('transactions.pay_remaining', $transaction->id) }}"
                                                class="btn btn-sm btn-outline-primary action-btn"
                                                title="Lunasi via Midtrans">
                                                Lunasi Midtrans
                                            </a>

                                            <form action="{{ route('transactions.cash_payment', $transaction->id) }}"
                                                method="POST" class="cash-form">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success action-btn"
                                                    title="Lunasi Tunai">
                                                    Lunasi Tunai
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
@stop


@section('css')
    <style>
        /* === GAYA KARTU & TABEL PREMIUM === */
        .card {
            background: #ffffff;
            border: none;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 6px 22px rgba(0, 0, 0, 0.05);
        }

        /* === TABEL === */
        .table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            background-color: #fff;
            font-size: 0.82rem;
            /* 🔥 lebih kecil & compact */
        }

        /* === HEADER TABEL === */
        .table thead th {
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            font-size: 0.75rem;
            /* 🔥 header lebih kecil */
            padding: 10px 10px;
            /* 🔥 padding lebih rapat */
            text-align: center;
        }

        /* === ISI TABEL === */
        .table td {
            vertical-align: middle !important;
            text-align: center;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            border-right: 1px solid rgba(0, 0, 0, 0.03);
            padding: 8px 10px;
            /* 🔥 lebih compact */
            font-size: 0.82rem;
        }

        /* Hilangkan garis kanan terakhir agar tidak dobel */
        .table td:last-child,
        .table th:last-child {
            border-right: none;
        }

        /* Efek hover elegan */
        .table tbody tr:hover {
            background-color: #f7faff;
            transition: 0.25s ease;
        }

        /* === BADGE DENGAN GRADIENT === */
        .bg-gradient-success {
            background: linear-gradient(45deg, #28a745, #60d394);
        }

        .bg-gradient-info {
            background: linear-gradient(45deg, #17a2b8, #5bc0de);
        }

        .bg-gradient-danger {
            background: linear-gradient(45deg, #e74c3c, #ff7675);
        }

        .bg-gradient-secondary {
            background: linear-gradient(45deg, #95a5a6, #bdc3c7);
        }

        /* === TOMBOL === */
        .btn-gradient-primary {
            background: linear-gradient(90deg, #007bff, #00b4d8);
            color: white;
            border: none;
            border-radius: 30px;
            padding: 0.5rem 1.25rem;
            transition: 0.3s;
        }

        .btn-gradient-primary:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .btn-outline-info,
        .btn-outline-danger {
            border-radius: 30px;
            padding: 6px 10px;
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

        /* === Rounded Corners for Table === */
        .table thead th:first-child {
            border-top-left-radius: 10px;
        }

        .table thead th:last-child {
            border-top-right-radius: 10px;
        }

        .table tbody tr:last-child td:first-child {
            border-bottom-left-radius: 10px;
        }

        .table tbody tr:last-child td:last-child {
            border-bottom-right-radius: 10px;
        }

        /* Hilangkan overflow agar sudut tidak terpotong */
        .table {
            overflow: hidden;
            border-radius: 10px;
        }

        /* ======== Filter Seragam ======== */
        #filterEmployee,
        #filterService,
        .filter-select {
            border-radius: 50px;
            padding: 0.5rem 1.2rem;
            /* sedikit lebih besar */
            font-size: 0.95rem;
            background: #f8f9fa;
            border: 1px solid #e2e6ea;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            color: #495057;
            transition: all 0.3s ease;
            width: 100%;
            /* agar menyesuaikan kolom */
            min-width: 220px;
            /* batas minimal agar tidak terlalu kecil */
        }

        /* Focus & Hover */
        #filterEmployee:focus,
        #filterService:focus,
        .filter-select:focus {
            box-shadow: 0 0 0 0.25rem rgba(108, 117, 125, 0.3);
            background-color: #fff;
            border-color: #6abfe3;
        }

        #filterEmployee:hover,
        #filterService:hover,
        .filter-select:hover {
            background-color: #fff;
            border-color: #6abfe3;
        }

        /* Wrapper flex agar rapi */
        .date-filter-wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            /* lebih lega antar filter */
            justify-content: flex-start;
        }

        /* ======== Judul Filter Modern ======== */
        .filter-label {
            font-weight: 600;
            font-size: 14px;
            color: #1f2937;
            margin-bottom: 10px;
            /* jarak bawah lebih lega */
            display: flex;
            align-items: center;
            gap: 6px;
            /* jarak icon & teks */
        }

        /* Untuk tampilan mobile responsive */
        @media (max-width: 768px) {

            #filterEmployee,
            #filterService,
            .filter-select {
                width: 100%;
                /* full lebar di mobile */
                min-width: auto;
            }
        }

        /* ===== Tombol Action Table ===== */
        .action-btn {
            min-width: 130px;
            /* 🔥 samakan lebar tombol */
            height: 36px;
            /* 🔥 samakan tinggi */
            padding: 0 12px;
            /* horizontal padding saja */
            font-size: 0.82rem;
            border-radius: 50px;
            /* modern pill */

            display: inline-flex;
            /* 🔥 bikin rata tengah */
            align-items: center;
            justify-content: center;
            gap: 6px;

            white-space: nowrap;
            /* cegah teks turun */
            transition: all 0.2s ease-in-out;
        }

        /* Icon spacing */
        .action-btn i {
            font-size: 0.8rem;
        }

        /* Hover efek premium */
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        /* Wrapper agar tombol tetap rapi jika wrap */
        td .d-flex.gap-2 {
            gap: 6px !important;
        }

        td .d-flex.gap-2>* {
            margin-bottom: 4px;
        }

        /* Supaya form tidak merusak flex layout */
        td form {
            margin: 0;
        }

        /* ===== Animasi Table Seperti Appointment ===== */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #transactionsTable tbody tr {
            animation: fadeInUp 0.3s ease forwards;
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
    </style>
@stop

@section('js')
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
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

        @if (session('info'))
            Toast.fire({
                icon: 'info',
                title: '{{ session('info') }}'
            });
        @endif
    </script>
    <script>
        $(document).ready(function() {

            // ================= DELETE TRANSACTION =================
            $(document).on('submit', '.delete-form', function(e) {
                e.preventDefault();

                let form = this;

                Swal.fire({
                    title: 'Hapus Transaksi?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });


            // ================= CASH PAYMENT =================
            $(document).on('submit', '.cash-form', function(e) {
                e.preventDefault();

                let form = this;

                Swal.fire({
                    title: 'Konfirmasi Pelunasan Tunai',
                    html: `
            <p>Pastikan pembayaran sudah diterima.</p>
            <strong>Transaksi hanya bisa dilunasi 1 kali.</strong>
        `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Sudah Dibayar',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // ================= DATATABLE =================
            var table = $('#transactionsTable').DataTable({
                responsive: true,
                dom: "<'row mb-3'<'col-12 d-flex justify-content-end pe-3'f>>rtip",
                language: {
                    search: "",
                    searchPlaceholder: "Cari transaksi...",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Berikutnya",
                        previous: "Sebelumnya"
                    },
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ transaksi",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 transaksi",
                    infoFiltered: "(difilter dari _MAX_ total transaksi)",
                    zeroRecords: "Tidak ada transaksi ditemukan",
                    lengthMenu: "Tampilkan _MENU_ baris",
                }
            });

            // Style search input sama seperti appointment
            $('#transactionsTable_filter input')
                .addClass('form-control rounded-pill shadow-sm')
                .css({
                    'padding': '0.45rem 2.5rem 0.45rem 1rem',
                    'border': 'none',
                    'box-shadow': '0 2px 6px rgba(0,0,0,0.08)',
                    'background-image': 'url("data:image/svg+xml,%3Csvg fill=\'%23666\' xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' width=\'16\' height=\'16\'%3E%3Cpath d=\'M10 2a8 8 0 105.293 14.293l5.707 5.707 1.414-1.414-5.707-5.707A8 8 0 0010 2zm0 2a6 6 0 110 12 6 6 0 010-12z\'/%3E%3C/svg%3E")',
                    'background-repeat': 'no-repeat',
                    'background-position': 'right 10px center',
                    'background-size': '16px 16px'
                });

            // ================= FILTER =================
            $('#filterEmployee, #filterService').on('change', function() {
                applyFilters();
            });

            function applyFilters() {

                // Reset filter seperti appointment
                $.fn.dataTable.ext.search = [];

                var employeeVal = $('#filterEmployee').val();
                var serviceVal = $('#filterService').val();

                // FILTER KARYAWAN
                if (employeeVal) {
                    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                        var row = table.row(dataIndex).node();
                        var employeeId = $(row).data('employee-id');
                        return employeeId == employeeVal;
                    });
                }

                // FILTER SERVICE
                if (serviceVal) {
                    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                        var row = table.row(dataIndex).node();
                        var serviceId = $(row).data('service-id');
                        return String(serviceId) === String(serviceVal);
                    });
                }

                // Redraw normal (biar ada efek redraw natural)
                table.draw();
            }

            // Jalankan default filter
            applyFilters();

            // ================= ALERT AUTO CLOSE =================
            $(".alert").delay(6000).slideUp(300);

        });
    </script>
@stop
