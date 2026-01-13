@extends('adminlte::page')

@section('title', 'Daftar Pengguna')

@section('content_header')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="fw-bold text-primary mb-0">
        <i class="fas fa-users me-2 text-primary"></i> Daftar Pengguna
    </h1>
    <div>
        <a href="{{ route('user.create') }}" class="btn btn-gradient-primary shadow-sm me-2">
            <i class="fas fa-plus me-1"></i> Tambah Pengguna
        </a>
        <a href="{{ route('user.trash') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-trash-alt me-1"></i> Lihat Sampah
        </a>
    </div>
</div>
@stop

@section('content')
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-pill px-4" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card border-0 shadow-lg rounded-4">
    <div class="card-body table-responsive p-4">
        <table id="userTable" class="table align-middle table-hover table-borderless">
            <thead class="bg-gradient text-white" style="background: linear-gradient(90deg, #007bff, #00b4d8);">
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Foto</th>
                    <th>Peran</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    @php
                        $status = $user->status ? 'Aktif' : 'Tidak Aktif';
                        $badgeClass = $user->status ? 'bg-gradient-success' : 'bg-gradient-danger';
                    @endphp
                    <tr class="bg-white shadow-sm-hover">
                        <td class="fw-semibold text-muted text-center">{{ $loop->iteration }}</td>
                        <td class="fw-bold text-dark">
                            {{ $user->name }}<br>
                            <small class="text-muted">{{ $user->created_at->translatedFormat('d M Y') }}</small>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td class="text-center">
                            <img src="{{ $user->profileImage() }}" 
                                 class="rounded-circle shadow-sm" 
                                 style="width: 50px; height: 50px; object-fit: cover;">
                        </td>
                        <td>
                            @forelse ($user->getRoleNames() as $role)
                                <span class="badge bg-gradient-info text-white px-3 py-1">{{ ucfirst($role) }}</span>
                            @empty
                                <span class="badge bg-gradient-secondary">Tanpa Peran</span>
                            @endforelse
                        </td>
                        <td>
                            <span class="badge text-white px-3 py-2 rounded-pill shadow-sm {{ $badgeClass }}">
                                {{ $status }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="{{ route('user.edit', $user->id) }}" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('user.destroy', $user->id) }}" method="POST"
                                      onsubmit="return confirm('Yakin ingin memindahkan pengguna ini ke sampah?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
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
/* === CARD & TABLE STYLE PREMIUM === */
.card {
    background: #ffffff;
    border: none;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 6px 22px rgba(0, 0, 0, 0.05);
}

/* === TABLE === */
.table {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
    background-color: #fff;
    font-size: 0.95rem;
}

/* === HEADER === */
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

/* === BODY === */
.table td {
    vertical-align: middle !important;
    text-align: center;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    border-right: 1px solid rgba(0, 0, 0, 0.03);
    padding: 10px 12px;
    color: #333;
}

.table td:last-child, .table th:last-child {
    border-right: none;
}

.table tbody tr:hover {
    background-color: #f7faff;
    transition: 0.25s ease;
}

/* === BADGE GRADIENT === */
.bg-gradient-success { background: linear-gradient(45deg, #28a745, #60d394); }
.bg-gradient-info { background: linear-gradient(45deg, #17a2b8, #5bc0de); }
.bg-gradient-danger { background: linear-gradient(45deg, #e74c3c, #ff7675); }
.bg-gradient-secondary { background: linear-gradient(45deg, #95a5a6, #bdc3c7); }

/* === BUTTONS === */
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

/* === SEARCH INPUT === */
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
    $('#userTable').DataTable({
        responsive: true,
        language: {
            search: "",
            searchPlaceholder: "Cari nama atau email pengguna...",
            paginate: {
                next: "›",
                previous: "‹"
            },
            info: "Menampilkan _START_–_END_ dari _TOTAL_ pengguna"
        },
        dom: "<'row mb-3'<'col-12 d-flex justify-content-end'f>>" + "rtip"
    });
    $(".alert").delay(4000).slideUp(300);
});
</script>
@stop
