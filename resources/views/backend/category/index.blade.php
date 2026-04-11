@extends('adminlte::page')

@section('title', 'Daftar Kategori')

@section('content_header')
    <div class="page-title-wrapper text-center mb-4">
        <h1 class="page-title">
            <i class="fa fa-table-cells-large me-2"></i>
            Daftar Kategori
        </h1>
        <div class="title-divider"></div>
    </div>
@stop


@section('content')

    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-body">

            <div class="mb-3 text-end">
                <a href="{{ route('category.create') }}" class="btn btn-gradient-primary shadow-sm">
                    <i class="fas fa-plus me-1"></i> Tambah Kategori
                </a>
            </div>

            <div class="table-responsive">
                <table id="categoryTable" class="table align-middle table-hover table-borderless">
                    <thead class="bg-gradient text-white" style="background: linear-gradient(90deg, #007bff, #00b4d8);">
                        <tr>
                            <th>#</th>
                            <th>Nama Kategori</th>
                            <th>Slug</th>
                            <th>Jumlah Layanan</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($categories as $category)
                            <tr class="bg-white shadow-sm-hover">

                                <td class="fw-semibold text-muted">
                                    {{ $loop->iteration }}
                                </td>

                                <td class="fw-bold text-dark">
                                    {{ $category->title }}
                                </td>

                                <td>
                                    {{ $category->slug }}
                                </td>

                                <td>
                                    {{ $category->services->count() }}
                                </td>

                                <td>
                                    @php
                                        $badgeClass = $category->status
                                            ? 'bg-gradient-success'
                                            : 'bg-gradient-secondary';
                                        $statusText = $category->status ? 'Aktif' : 'Nonaktif';
                                    @endphp

                                    <span class="badge text-white px-3 py-2 rounded-pill shadow-sm {{ $badgeClass }}">
                                        {{ $statusText }}
                                    </span>
                                </td>

                                <td class="text-center">

                                    <div class="d-flex justify-content-center flex-wrap gap-2">

                                        <a href="{{ route('category.edit', $category->id) }}"
                                            class="btn btn-sm btn-outline-info action-btn" title="Edit Kategori">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form action="{{ route('category.destroy', $category->id) }}" method="POST"
                                            class="delete-form">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-outline-danger action-btn"
                                                title="Hapus Kategori">
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
    </div>

@stop

@section('css')
    <style>
        body.swal2-shown {
            overflow-y: scroll !important;
            padding-right: 0 !important;
        }

        /* === CARD & TABLE PREMIUM STYLE === */
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

        .table td:last-child,
        .table th:last-child {
            border-right: none;
        }

        .table tbody tr:hover {
            background-color: #f7faff;
            transition: 0.25s ease;
        }

        /* === BADGES === */
        .bg-gradient-success {
            background: linear-gradient(45deg, #28a745, #60d394);
        }

        .bg-gradient-danger {
            background: linear-gradient(45deg, #e74c3c, #ff7675);
        }

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

        .btn-outline-info,
        .btn-outline-danger {
            border-radius: 30px;
            padding: 6px 10px;
        }

        /* === SEARCH INPUT PREMIUM === */
        .dataTables_filter {
            text-align: right;
        }

        .dataTables_filter input {
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

        .table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            background-color: #fff;
            font-size: 0.82rem;
        }

        .table thead th {
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            font-size: 0.75rem;
            padding: 10px 10px;
            text-align: center;
        }

        .table td {
            vertical-align: middle !important;
            text-align: center;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            border-right: 1px solid rgba(0, 0, 0, 0.03);
            padding: 8px 10px;
            font-size: 0.82rem;
        }

        .table tbody tr:hover {
            background-color: #f7faff;
            transition: 0.25s ease;
        }

        /* Badge */
        .bg-gradient-success {
            background: linear-gradient(45deg, #28a745, #60d394);
        }

        .bg-gradient-danger {
            background: linear-gradient(45deg, #e74c3c, #ff7675);
        }

        /* Button */
        .btn-gradient-primary {
            background: linear-gradient(90deg, #007bff, #00b4d8);
            color: white;
            border: none;
            border-radius: 30px;
        }

        /* Action button */
        .action-btn {
            min-width: 36px;
            height: 36px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all .2s;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        /* Animation */
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

        #categoryTable tbody tr {
            animation: fadeInUp .3s ease forwards;
        }
    </style>
@stop

@section('js')

    <script>
        $(document).ready(function() {

            // ================= DATATABLE =================
            var table = $('#categoryTable').DataTable({
                responsive: true,
                paging: true,
                info: true,
                pageLength: 10,

                dom: "<'row mb-3'<'col-12 d-flex justify-content-end pe-3'f>>rtip",

                language: {
                    search: "",
                    searchPlaceholder: "Cari kategori...",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Berikutnya",
                        previous: "Sebelumnya"
                    },
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ kategori",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 kategori",
                    infoFiltered: "(difilter dari _MAX_ total kategori)",
                    zeroRecords: "Tidak ada kategori ditemukan",
                    lengthMenu: "Tampilkan _MENU_ baris"
                }
            });


            // ================= STYLE SEARCH INPUT =================
            $('#categoryTable_filter input')
                .addClass('form-control rounded-pill shadow-sm')
                .css({
                    padding: '0.45rem 2.5rem 0.45rem 1rem',
                    border: 'none',
                    boxShadow: '0 2px 6px rgba(0,0,0,0.08)',
                    'background-image': 'url("data:image/svg+xml,%3Csvg fill=\'%23666\' xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' width=\'16\' height=\'16\'%3E%3Cpath d=\'M10 2a8 8 0 105.293 14.293l5.707 5.707 1.414-1.414-5.707-5.707A8 8 0 0010 2zm0 2a6 6 0 110 12 6 6 0 010-12z\'/%3E%3C/svg%3E")',
                    backgroundRepeat: 'no-repeat',
                    backgroundPosition: 'right 10px center',
                    backgroundSize: '16px 16px'
                });


            // ================= DELETE CONFIRM =================
            $(document).on('submit', '.delete-form', function(e) {
                e.preventDefault();
                let form = this;

                Swal.fire({
                    title: 'Hapus Kategori?',
                    text: 'Data yang dihapus tidak dapat dikembalikan!',
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


            // ================= ANIMATION TABLE =================
            table.on('draw', function() {
                $('#categoryTable tbody tr').each(function(i) {
                    $(this)
                        .css('opacity', '0')
                        .delay(i * 50)
                        .animate({
                            opacity: 1,
                            top: 0
                        }, 200);
                });
            });


            // ================= ALERT AUTO CLOSE =================
            $(".alert").delay(6000).slideUp(300);

        });
    </script>


    {{-- ================= TOAST NOTIFICATION ================= --}}
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

@stop
