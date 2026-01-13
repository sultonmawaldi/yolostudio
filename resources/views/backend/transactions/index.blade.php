@extends('adminlte::page')

@section('title', 'Semua Transaksi')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="fw-bold text-primary mb-0">
        <i class="fas fa-credit-card me-2 text-primary"></i> Semua Transaksi
    </h1>
    <a href="{{ route('transactions.create') }}" class="btn btn-gradient-primary shadow-sm">
        <i class="fas fa-plus me-1"></i> Tambah Transaksi
    </a>
</div>
@stop

@section('content')
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-pill px-4" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
@endif

<div class="card border-0 shadow-lg rounded-4">
    <div class="card-body table-responsive p-4">
        <table id="transactionsTable" class="table align-middle table-hover table-borderless">
            <thead class="bg-gradient text-white" style="background: linear-gradient(90deg, #007bff, #00b4d8);">
                <tr>
                    <th>#</th>
                    <th>Kode</th>
                    <th>ID Pemesanan</th>
                    <th>Klien</th>
                    <th>Layanan</th>
                    <th>Staf</th>
                    <th>Metode</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $transaction)
                    @php
                        $status = $transaction->payment_status ?? 'Menunggu';
                        $badgeClass = match($status) {
                            'Paid' => 'bg-gradient-success',
                            'DP' => 'bg-gradient-warning',
                            'Failed' => 'bg-gradient-danger',
                            default => 'bg-gradient-secondary'
                        };
                    @endphp
                    <tr class="bg-white shadow-sm-hover">
                        <td class="fw-semibold text-muted">{{ $loop->iteration }}</td>
                        <td class="fw-bold text-dark">{{ $transaction->transaction_code }}</td>
                        <td>{{ $transaction->appointment->booking_id ?? '-' }}</td>
                        <td>{{ $transaction->appointment->name ?? '-' }}</td>
                        <td>{{ $transaction->appointment->service->title ?? '-' }}</td>
                        <td>{{ $transaction->appointment->employee->user->name ?? '-' }}</td>
                        <td><span class="text-capitalize">{{ $transaction->payment_method ?? '-' }}</span></td>
                        <td class="fw-bold text-primary">Rp {{ number_format($transaction->total_amount ?? 0, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge text-white px-3 py-2 rounded-pill shadow-sm {{ $badgeClass }}">
                                {{ $status }}
                            </span>
                        </td>
                        <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('transactions.edit', $transaction->id) }}" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST" onsubmit="return confirm('Hapus transaksi ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>

                            @if ($transaction->payment_status === 'DP')
                                <div class="mt-2">
                                    <a href="{{ route('transactions.pay_remaining', $transaction->id) }}" 
                                       class="btn btn-sm btn-outline-primary me-1">
                                        Lunasi via Midtrans
                                    </a>
                                    <form action="{{ route('transactions.cash_payment', $transaction->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success" onclick="return confirm('Yakin sudah menerima pelunasan tunai?')">
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
    font-size: 0.95rem;
}

/* === HEADER TABEL === */
.table thead th {
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid rgba(0, 123, 255, 0.25);
    text-align: center;
    vertical-align: middle;
    color: #fff;
    padding: 14px 12px;
    white-space: nowrap;
}

/* === ISI TABEL === */
.table td {
    vertical-align: middle !important;
    text-align: center;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    border-right: 1px solid rgba(0, 0, 0, 0.03);
    padding: 10px 12px;
    color: #333;
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
.bg-gradient-success { background: linear-gradient(45deg, #28a745, #60d394); }
.bg-gradient-info { background: linear-gradient(45deg, #17a2b8, #5bc0de); }
.bg-gradient-danger { background: linear-gradient(45deg, #e74c3c, #ff7675); }
.bg-gradient-secondary { background: linear-gradient(45deg, #95a5a6, #bdc3c7); }

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
.btn-outline-info, .btn-outline-danger {
    border-radius: 30px;
    padding: 6px 10px;
}

/* === INPUT PENCARIAN === */
.dataTables_filter {
    text-align: right;
}
.dataTables_filter input {
    border-radius: 50px !important;
    padding: 0.5rem 1rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border: 1px solid #dee2e6;
    transition: 0.3s;
}
.dataTables_filter input:focus {
    box-shadow: 0 0 0 3px rgba(0,123,255,0.25);
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
</style>
@stop

@section('js')
<script>
$(function () {
    $('#transactionsTable').DataTable({
        responsive: true,
        language: {
            search: "",
            searchPlaceholder: "Cari transaksi...",
            paginate: {
                next: "›",
                previous: "‹"
            },
            info: "Menampilkan _START_–_END_ dari _TOTAL_ transaksi",
            zeroRecords: "Tidak ada transaksi ditemukan",
        },
        dom: "<'row mb-3'<'col-12 d-flex justify-content-end'f>>" + "rtip"
    });
    $(".alert").delay(4000).slideUp(300);
});
</script>
@stop
