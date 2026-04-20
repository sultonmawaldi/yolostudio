@extends('adminlte::page')

@section('title', 'Galeri')

@section('content_header')
    <div class="page-title-wrapper text-center mb-4">
        <h1 class="page-title">
            <i class="fas fa-images me-2"></i>
            Galeri
        </h1>
        <div class="title-divider"></div>
    </div>
@stop

@section('content')

    <div class="mb-3 text-end">
        <a href="{{ route('gallery.create') }}" class="btn btn-gradient-primary shadow-sm">
            <i class="fas fa-plus me-1"></i> Tambah Galeri
        </a>
    </div>
    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-body table-responsive p-4">
            <table id="galleryTable" class="table align-middle table-hover table-borderless">
                <thead class="table-header-gradient">
                    <tr>
                        <th>#</th>
                        <th>Gambar</th>
                        <th>Judul</th>
                        <th>Layanan</th> {{-- ✅ GANTI --}}
                        <th>Status</th>
                        <th>Dibuat</th>
                        <th>Diperbarui</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($galleries as $gallery)
                        <tr class="bg-white shadow-sm-hover">
                            <td class="fw-semibold text-muted">{{ $loop->iteration }}</td>

                            <td>
                                @if ($gallery->image)
                                    <img src="{{ asset('uploads/gallery/' . $gallery->image) }}" class="rounded"
                                        style="height:45px; width:auto;">
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td>
                                {{ $gallery->title }}
                            </td>

                            {{-- ✅ SERVICE --}}
                            <td>
                                <span class="badge bg-gradient-info text-white px-3 py-2 rounded-pill shadow-sm">
                                    {{ $gallery->service->title ?? '-' }}
                                </span>
                            </td>

                            <td>
                                <span
                                    class="badge px-3 py-2 rounded-pill shadow-sm 
                                    {{ $gallery->status ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
                                    {{ $gallery->status ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>

                            <td>{{ optional($gallery->created_at)->format('d M Y') }}</td>
                            <td>{{ optional($gallery->updated_at)->format('d M Y') }}</td>

                            <td class="text-center">
                                <div class="d-flex justify-content-center flex-wrap gap-2">

                                    <a href="{{ route('gallery.edit', $gallery) }}"
                                        class="btn btn-sm btn-outline-info action-btn">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('gallery.destroy', $gallery) }}" method="POST"
                                        class="delete-form">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-sm btn-outline-danger action-btn">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                Belum ada data galeri.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>
@stop

@section('css')
    <style>
        body.swal2-shown {
            overflow-y: scroll !important;
            padding-right: 0 !important;
        }

        /* === CARD & TABLE STYLE PREMIUM === */
        .card {
            background: #ffffff;
            border: none;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 6px 22px rgba(0, 0, 0, 0.05);
        }

        .table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            background-color: #fff;
            font-size: 0.82rem;
            border-radius: 10px;
            overflow: hidden;
        }

        /* ================================
                                                                                                                                       TABLE HEADER GRADIENT (PROPER)
                                                                                                                                    ================================ */
        .table-header-gradient {
            background: linear-gradient(90deg, #007bff, #00b4d8) !important;
        }

        /* pastikan th tidak override background */
        .table-header-gradient th {
            background: transparent !important;
            color: #fff !important;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            font-size: 0.75rem;
            padding: 10px;
            text-align: center;
            border: none !important;
        }

        /* optional: biar lebih halus */
        .table-header-gradient th:first-child {
            border-top-left-radius: 10px;
        }

        .table-header-gradient th:last-child {
            border-top-right-radius: 10px;
        }

        .table thead th {
            white-space: nowrap;
        }

        .table td {
            vertical-align: middle;
            text-align: center;
            padding: 8px 10px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            border-right: 1px solid rgba(0, 0, 0, 0.03);
            color: #333;
        }

        .table td:last-child,
        .table th:last-child {
            border-right: none;
        }

        .table tbody tr:hover {
            background-color: #f7faff;
            transition: 0.25s ease;
        }

        /* === BADGES WITH GRADIENT === */
        .bg-gradient-success {
            background: linear-gradient(45deg, #28a745, #60d394);
        }

        .bg-gradient-secondary {
            background: linear-gradient(45deg, #95a5a6, #bdc3c7);
        }

        /* === BUTTONS === */
        .btn-gradient-primary {
            background: linear-gradient(90deg, #007bff, #00b4d8);
            color: #fff;
            border: none;
            border-radius: 30px;
            padding: .5rem 1.25rem;
            transition: .3s;
        }

        .btn-gradient-primary:hover {
            opacity: .9;
            transform: translateY(-1px);
        }

        .btn-outline-info,
        .btn-outline-danger {
            border-radius: 30px;
            padding: 6px 10px;
        }

        .action-btn {
            min-width: 36px;
            height: 36px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: .2s;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        /* === Search Input === */
        .dataTables_filter {
            text-align: right;
        }

        .dataTables_filter input {
            border-radius: 50px !important;
            padding: .5rem 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border: 1px solid #dee2e6;
            transition: .3s;
        }

        .dataTables_filter input:focus {
            box-shadow: 0 0 0 3px rgba(0, 123, 255, .25);
            border-color: #80bdff;
        }

        /* === Rounded Corners Table === */
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

        /* Animasi Table */
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

        #galleryTable tbody tr {
            animation: fadeInUp .3s ease forwards;
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
        $(document).ready(function() {

            var table = $('#galleryTable').DataTable({
                responsive: true,
                paging: true,
                pageLength: 10,
                lengthChange: false,
                dom: "<'row mb-3'<'col-12 d-flex justify-content-end pe-3'f>>rtip",
                language: {
                    search: "",
                    searchPlaceholder: "Cari galeri...",
                    paginate: {
                        next: "Berikutnya",
                        previous: "Sebelumnya"
                    },
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ galeri",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 galeri",
                    zeroRecords: "Tidak ada galeri ditemukan"
                }
            });

            $('#galleryTable_filter input').addClass('form-control rounded-pill shadow-sm').css({
                padding: '0.45rem 2.5rem 0.45rem 1rem',
                border: 'none',
                boxShadow: '0 2px 6px rgba(0,0,0,0.08)',
                backgroundImage: 'url("data:image/svg+xml,%3Csvg fill=\'%23666\' xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' width=\'16\' height=\'16\'%3E%3Cpath d=\'M10 2a8 8 0 105.293 14.293l5.707 5.707 1.414-1.414-5.707-5.707A8 8 0 0010 2zm0 2a6 6 0 110 12 6 6 0 010-12z\'/%3E%3C/svg%3E")',
                backgroundRepeat: 'no-repeat',
                backgroundPosition: 'right 10px center',
                backgroundSize: '16px 16px'
            });

            $(document).on('submit', '.delete-form', function(e) {
                e.preventDefault();
                let form = this;

                Swal.fire({
                    title: 'Hapus Galeri?',
                    text: 'Data yang dihapus tidak dapat dikembalikan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });

            // Toast Success / Error
            @if (session('success') || session('error'))
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
                        title: "{{ session('success') }}"
                    });
                @endif

                @if (session('error'))
                    Toast.fire({
                        icon: 'error',
                        title: "{{ session('error') }}"
                    });
                @endif
            @endif

            // Error validation
            @if ($errors->any())
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        html: `<ul style="text-align:left;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>`,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#d33'
                    });
                });
            @endif

            // Auto hide alert
            $(".alert").delay(6000).slideUp(300);

        });
    </script>
@stop
