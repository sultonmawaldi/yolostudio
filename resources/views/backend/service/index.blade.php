@extends('adminlte::page')

@section('title', 'Daftar Layanan')

@section('content_header')
    <div class="page-title-wrapper text-center mb-4">
        <h1 class="page-title">
            <i class="fa fa-concierge-bell me-2"></i>
            Daftar Layanan
        </h1>
        <div class="title-divider"></div>
    </div>
@stop


@section('content')

    <div class="mb-3 text-end">

        <a href="{{ route('service.create') }}" class="btn btn-gradient-primary shadow-sm me-2">
            <i class="fas fa-plus me-1"></i> Tambah Layanan
        </a>

        <a href="{{ route('service.trash') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-trash-alt me-1"></i> Lihat Sampah
        </a>

    </div>

    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-body">
            <div class="table-responsive">
                <table id="serviceTable" class="table align-middle table-hover table-borderless">
                    <thead class="table-header-gradient">
                        <tr>
                            <th>#</th>
                            <th>Judul</th>
                            <th>Gambar</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($services as $service)
                            <tr class="bg-white shadow-sm-hover">

                                <td class="fw-semibold text-muted">
                                    {{ $loop->iteration }}
                                </td>

                                <td>
                                    {{ $service->title }}
                                </td>

                                <td>
                                    <img src="{{ $service->image ? asset('uploads/images/service/' . $service->image) : asset('uploads/images/no-image.jpg') }}"
                                        style="width:70px;height:70px;object-fit:cover" class="rounded shadow-sm">
                                </td>

                                <td>
                                    {{ $service->category->title ?? '-' }}
                                </td>

                                <td>
                                    @php
                                        $badgeClass = $service->status
                                            ? 'bg-gradient-success'
                                            : 'bg-gradient-secondary';
                                        $statusText = $service->status ? 'Aktif' : 'Nonaktif';
                                    @endphp

                                    <span class="badge text-white px-3 py-2 rounded-pill shadow-sm {{ $badgeClass }}">
                                        {{ $statusText }}
                                    </span>
                                </td>

                                <td class="text-center">

                                    <div class="d-flex justify-content-center flex-wrap gap-2">

                                        <a href="{{ route('service.edit', $service->id) }}"
                                            class="btn btn-sm btn-outline-info action-btn" title="Edit Layanan">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form action="{{ route('service.destroy', $service->id) }}" method="POST"
                                            class="delete-form">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-sm btn-outline-danger action-btn"
                                                title="Hapus Layanan">
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
            font-size: 0.95rem;
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

        .bg-gradient-success {
            background: linear-gradient(45deg, #28a745, #60d394);
        }

        .bg-gradient-danger {
            background: linear-gradient(45deg, #e74c3c, #ff7675);
        }

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
            font-size: 0.82rem;
        }

        .table td {
            padding: 8px 10px;
            font-size: 0.82rem;
        }

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

        #serviceTable tbody tr {
            animation: fadeInUp .3s ease forwards;
        }
    </style>
@stop


@section('js')

    <script>
        $(document).ready(function() {

            var table = $('#serviceTable').DataTable({
                responsive: true,
                paging: true,
                info: true,
                pageLength: 10,

                dom: "<'row mb-3'<'col-12 d-flex justify-content-end pe-3'f>>rtip",

                language: {
                    search: "",
                    searchPlaceholder: "Cari layanan...",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Berikutnya",
                        previous: "Sebelumnya"
                    },
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ layanan",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 layanan",
                    infoFiltered: "(difilter dari _MAX_ total layanan)",
                    zeroRecords: "Tidak ada layanan ditemukan",
                    lengthMenu: "Tampilkan _MENU_ baris"
                }

            });


            $('#serviceTable_filter input')
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


            $(document).on('submit', '.delete-form', function(e) {

                e.preventDefault();
                let form = this;

                Swal.fire({
                    title: 'Hapus Layanan?',
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


            table.on('draw', function() {

                $('#serviceTable tbody tr').each(function(i) {

                    $(this)
                        .css('opacity', '0')
                        .delay(i * 50)
                        .animate({
                            opacity: 1,
                            top: 0
                        }, 200);

                });

            });


            $(".alert").delay(6000).slideUp(300);

        });
    </script>


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
            })
        @endif

        @if (session('error'))
            Toast.fire({
                icon: 'error',
                title: '{{ session('error') }}'
            })
        @endif

        @if (session('info'))
            Toast.fire({
                icon: 'info',
                title: '{{ session('info') }}'
            })
        @endif
    </script>

@stop
