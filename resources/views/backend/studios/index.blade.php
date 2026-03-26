@extends('adminlte::page')

@section('title', 'Daftar Studio')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="fw-bold text-primary mb-0">
            <i class="fas fa-camera me-2"></i> Daftar Studio
        </h1>
        <a href="{{ route('studio.create') }}" class="btn btn-gradient-primary shadow-sm">
            <i class="fas fa-plus me-1"></i> Tambah Studio
        </a>
    </div>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-pill px-4">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-body table-responsive p-4">
            <table id="studioTable" class="table align-middle table-hover table-borderless">
                <thead class="text-white" style="background: linear-gradient(90deg, #007bff, #00b4d8);">
                    <tr>
                        <th>#</th>
                        <th>Gambar</th>
                        <th>Nama</th>
                        <th>Telepon</th>
                        <th>Kota</th>
                        <th>Alamat</th>
                        <th>Maps</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($studios as $studio)
                        <tr class="bg-white shadow-sm-hover">
                            <td class="fw-semibold text-muted">{{ $loop->iteration }}</td>

                            {{-- IMAGE --}}
                            <td>
                                @if ($studio->image)
                                    <img src="{{ asset('uploads/studios/' . $studio->image) }}" class="rounded shadow-sm"
                                        style="height:50px">
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td class="fw-bold">{{ $studio->name }}</td>
                            <td>{{ $studio->phone ?? '-' }}</td>
                            <td>{{ $studio->city ?? '-' }}</td>
                            <td>{{ Str::limit($studio->address, 45) }}</td>

                            <td>
                                @if ($studio->google_maps)
                                    <a href="{{ $studio->google_maps }}" target="_blank"
                                        class="btn btn-sm btn-outline-success rounded-pill">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td>
                                <span class="badge rounded-pill px-3 {{ $studio->status ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $studio->status ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>

                            <td>{{ $studio->created_at?->format('d M Y') }}</td>

                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('studio.edit', $studio) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('studio.destroy', $studio) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus studio ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
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
        .btn-gradient-primary {
            background: linear-gradient(90deg, #007bff, #00b4d8);
            color: #fff;
            border-radius: 30px;
            padding: .5rem 1.25rem;
            border: none;
        }

        .btn-gradient-primary:hover {
            opacity: .9;
            transform: translateY(-1px);
        }
    </style>
@stop

@section('js')
    <script>
        $(function() {
            $('#studioTable').DataTable({
                responsive: true,
                pageLength: 10,
                lengthChange: false,
                language: {
                    search: "",
                    searchPlaceholder: "Cari studio...",
                    paginate: {
                        next: "›",
                        previous: "‹"
                    },
                    info: "Menampilkan _START_–_END_ dari _TOTAL_ studio"
                },
                dom: "<'row mb-3'<'col-12 d-flex justify-content-end'f>>rtip"
            });
        });
    </script>
@stop
