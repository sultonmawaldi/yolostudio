@extends('adminlte::page')

@section('title', 'Upload & Kelola Foto Hasil')

@section('content_header')
    <div class="page-title-wrapper text-center mb-4">
        <h1 class="page-title">
            <i class="fa fa-image me-2"></i> Upload & Kelola Foto Hasil
        </h1>
        <p class="text-muted">Kelola hasil foto dari setiap transaksi dengan mudah dan cepat.</p>
        <div class="title-divider"></div>
    </div>
@stop

@section('content')


    <!-- ================= FILTER CREW & LAYANAN ================= -->
    <div class="row mb-3 g-3 align-items-end date-filter-wrapper">

        <!-- Crew -->
        <div class="col-md-4">
            <label class="filter-label" for="filterCrew"><i class="fas fa-users me-2"></i> Pilih Crew </label>

            @php
                $user = auth()->user();
            @endphp

            <select id="filterCrew" class="form-select filter-select">

                @if ($user && ($user->hasRole('admin') || $user->hasRole('moderator')))
                    <option value="">Semua Crew</option>

                    @foreach ($employees as $employee)
                        @if ($employee->user && $employee->user->hasRole('employee'))
                            <option value="{{ $employee->id }}">
                                {{ $employee->user->name ?? '-' }}
                            </option>
                        @endif
                    @endforeach
                @elseif($user && $user->hasRole('employee'))
                    <option value="{{ $user->employee->id }}" selected>
                        {{ $user->name }}
                    </option>
                @endif

            </select>
        </div>

        <!-- Layanan -->
        <div class="col-md-4">
            <label class="filter-label" for="filterService"><i class="fas fa-briefcase me-2"></i> Pilih Layanan
            </label>

            <select id="filterService" class="form-select filter-select">
                <option value="" {{ request('service') ? '' : 'selected' }}>
                    Semua Layanan
                </option>
                @foreach ($services as $service)
                    <option value="{{ $service->id }}" {{ request('service') == $service->id ? 'selected' : '' }}>
                        {{ $service->title ?? '-' }}
                    </option>
                @endforeach
            </select>
        </div>

    </div>
    <!-- ================= END FILTER ================= -->


    {{-- Search Bar Premium --}}
    <div class="card shadow-lg border-0 mb-4 search-bar-card">
        <div class="card-body">
            <form method="GET" action="{{ route('photo-results.index') }}"
                class="d-flex align-items-center gap-2 search-bar-form">

                {{-- Input Group --}}
                <div class="input-group search-input-group w-100">
                    <span class="input-group-text search-input-icon">
                        <i class="fa fa-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control search-input"
                        placeholder="Cari kode transaksi atau nama klien..." value="{{ request('search') }}">
                </div>

                {{-- Button --}}
                <button type="submit" class="btn search-btn">
                    <i class="fa fa-filter me-1"></i> Cari
                </button>

            </form>
        </div>
    </div>


    {{-- Daftar Transaksi --}}
    @forelse($transactions as $transaction)
        <div class="card mb-4 border-0 shadow-sm photo-card"
            data-employee-id="{{ $transaction->appointment->employee_id ?? '' }}"
            data-service-id="{{ $transaction->appointment->service_id ?? '' }}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="fw-bold mb-0 text-primary">
                        <i class="fa fa-barcode me-1"></i> {{ $transaction->transaction_code }}
                    </h5>

                    <div class="d-flex flex-column text-end">

                        <!-- ⭐ BADGE NAMA GOLD FLAT -->
                        <span class="badge d-flex align-items-center gap-1 px-3 py-1 mb-1">
                            <i class="fa fa-user"></i>
                            {{ $transaction->user->name ?? ($transaction->appointment->name ?? 'Tanpa Nama') }}
                        </span>


                        <!-- ⭐ BADGE LAYANAN GOLD FLAT -->
                        <span class="badge d-flex align-items-center gap-1 px-3 py-1">
                            <i class="fa fa-concierge-bell"></i>
                            {{ $transaction->appointment->service->title ?? '-' }}
                        </span>


                    </div>
                </div>

                <hr class="my-2">

                {{-- Link Publik --}}
                @if ($transaction->public_token)
                    <p class="mb-1">
                        <i class="fa fa-link text-primary"></i>
                        <a href="{{ route('photo-result.public', $transaction->public_token) }}" target="_blank">
                            {{ route('photo-result.public', $transaction->public_token) }}
                        </a>
                    </p>
                    <small class="text-muted d-block mb-3">
                        Berlaku hingga:
                        {{ $transaction->public_token_expires_at->translatedFormat('d F Y, H:i') }} WIB
                    </small>

                    <form action="{{ route('photo-results.regenerate-link', $transaction->id) }}" method="POST"
                        class="mb-3">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-sm">
                            <i class="fa fa-refresh me-1"></i> Perbarui Link Publik
                        </button>
                    </form>
                @endif

                {{-- Upload Foto --}}
                <form action="{{ route('photo-results.store') }}" method="POST" enctype="multipart/form-data"
                    class="mb-4">
                    @csrf
                    <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">
                    <div class="input-group">
                        <input type="file" name="photos[]" multiple required class="form-control">
                        <button class="btn btn-success" type="submit">
                            <i class="fa fa-upload me-1"></i> Upload Foto
                        </button>
                    </div>
                </form>


                {{-- ⭐ PREMIUM GALLERY --}}
                @if ($transaction->photoResults->count())
                    <div class="premium-gallery">
                        @foreach ($transaction->photoResults as $photo)
                            <div class="gallery-item-wrapper">

                                <div class="gallery-item" data-full="{{ Storage::url($photo->file_path) }}">
                                    <img src="{{ Storage::url($photo->file_path) }}" alt="Result Photo">
                                    <div class="gallery-hover">
                                        <button class="btn btn-sm btn-primary view-btn">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- Tombol Hapus --}}
                                <form action="{{ route('photo-results.destroy', $photo->id) }}" method="POST"
                                    class="delete-photo-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm w-100 mt-2">
                                        <i class="fa fa-trash me-1"></i> Hapus Foto
                                    </button>
                                </form>

                            </div>
                        @endforeach
                    </div>

                    {{-- ⭐ MODAL PREMIUM --}}
                    <div id="premiumModal" class="premium-modal d-none">

                        {{-- Close Button --}}
                        <span class="close-modal">&times;</span>

                        {{-- ⭐ Download Button --}}
                        <a id="downloadBtn" class="download-icon" download>
                            <i class="fa fa-download"></i>
                        </a>

                        {{-- ⭐ Navigation --}}
                        <span class="nav-arrow left-arrow">&#10094;</span>
                        <span class="nav-arrow right-arrow">&#10095;</span>

                        {{-- Isi Foto --}}
                        <img class="premium-modal-content" id="modalImage">
                    </div>

                    {{-- Tombol Aksi Global Super Premium --}}
                    @if ($transaction->photoResults->count())
                        <div class="d-flex justify-content-center gap-3 my-4 flex-wrap">

                            {{-- Tombol Kirim ke WhatsApp --}}
                            <form action="{{ route('photo-results.send-wa', $transaction->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-whatsapp-super">
                                    <i class="fab fa-whatsapp whatsapp-icon"></i> Kirim ke WhatsApp
                                </button>
                            </form>

                            {{-- Tombol Hapus Semua Foto --}}
                            <form action="{{ route('photo-results.destroy-all', $transaction->id) }}" method="POST"
                                class="delete-all-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger-super">
                                    <i class="fa fa-trash whatsapp-icon"></i> Hapus Semua Foto
                                </button>
                            </form>

                        </div>
                    @endif
                @else
                    <p class="text-muted">Belum ada foto hasil yang diupload.</p>
                @endif
            </div>
        </div>
    @empty
        <div class="text-center text-muted my-5">
            <i class="fa fa-folder-open fa-2x mb-2"></i>
            <p>Tidak ada transaksi ditemukan.</p>
        </div>
    @endforelse

    <div class="d-flex justify-content-center mt-4">
        {{ $transactions->links() }}
    </div>


@stop

@push('css')
    <style>
        html,
        body {
            overflow-x: hidden !important;
        }

        html.swal2-shown,
        body.swal2-shown {
            overflow: unset !important;
            /* jangan ubah overflow body */
            padding-right: 0 !important;
            /* hilangkan padding kompensasi */
            margin-right: 0 !important;
            /* cegah loncat ke kiri */
        }

        .card {
            border-radius: 12px;
        }

        hr {
            border-top: 1px solid #dee2e6;
        }

        /* ⭐ GALLERY */
        .premium-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
        }

        .gallery-item {
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            cursor: pointer;
        }

        .gallery-item img {
            width: 100%;
            height: 170px;
            object-fit: cover;
            transition: .3s;
            border-radius: 12px;
        }

        .gallery-item:hover img {
            transform: scale(1.07);
        }

        .gallery-hover {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.35);
            opacity: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            transition: .3s;
        }

        .gallery-item:hover .gallery-hover {
            opacity: 1;
        }

        /* ⭐ MODAL */
        .premium-modal {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(0, 0, 0, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .premium-modal-content {
            max-width: 90%;
            max-height: 80vh;
            border-radius: 12px;
            transition: .25s ease;
        }

        /* ⭐ Close Button */
        .close-modal {
            position: fixed;
            top: 20px;
            right: 25px;
            font-size: 38px;
            color: white;
            cursor: pointer;
            background: none !important;
            z-index: 10001;
            transition: .25s;
        }

        .close-modal:hover {
            transform: scale(1.2);
            text-shadow: 0 0 12px #fff;
        }

        /* ⭐ Download Button PREMIUM */
        .download-icon {
            position: fixed;
            top: 32px;
            right: 80px;
            font-size: 20px;
            color: white;
            cursor: pointer;
            background: none !important;
            z-index: 10001;
            transition: .25s;
        }

        .download-icon:hover {
            transform: scale(1.2);
            text-shadow: 0 0 12px #fff;
        }

        /* ⭐ ARROWS */
        .nav-arrow {
            position: fixed;
            top: 50%;
            transform: translateY(-50%);
            font-size: 32px;
            /* sedikit lebih kecil agar pas di dalam lingkaran */
            color: white;
            cursor: pointer;
            z-index: 10001;
            transition: .3s;
            user-select: none;

            /* 🔥 Tambahan Premium */
            width: 55px;
            height: 55px;
            background: rgba(0, 0, 0, 0.35);
            /* transparan */
            backdrop-filter: blur(4px);
            /* efek kaca premium */
            border-radius: 50%;
            /* bulat sempurna */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .nav-arrow:hover {
            transform: translateY(-50%) scale(1.15);
            background: rgba(0, 0, 0, 0.55);
            text-shadow: 0 0 12px white;
        }

        .left-arrow {
            left: 30px;
        }

        .right-arrow {
            right: 30px;
        }

        .gallery-item-wrapper {
            display: flex;
            flex-direction: column;
        }

        .delete-photo-form button {
            border-radius: 10px;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
            transition: .2s;
        }

        .delete-photo-form button:hover {
            transform: scale(1.03);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
        }

        .badge i {
            margin-right: 6px !important;
            /* jarak seragam */
        }

        /* ===============================
                                                                                                                               ✨ Search Bar Premium Modern ✨
                                                                                                                            ================================= */

        .search-bar-card {
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
        }

        .search-bar-form {
            flex-wrap: nowrap;
        }

        .search-input-group {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }

        .search-input-icon {
            background: rgba(0, 123, 255, 0.7);
            color: #fff;
            border: 0;
            transition: all 0.3s;
        }

        .search-input {
            background: rgba(255, 255, 255, 0.2);
            border: 0;
            backdrop-filter: blur(8px);
            transition: all 0.3s;
            height: 48px;
        }

        .search-input:focus {
            box-shadow: 0 4px 20px rgba(0, 123, 255, 0.4);
            outline: none;
        }

        .search-btn {
            border-radius: 12px;
            background: linear-gradient(135deg, #0066ff, #00ccff);
            color: #fff;
            box-shadow: 0 6px 15px rgba(0, 102, 255, 0.4);
            transition: all 0.3s;
            height: 48px;
            display: flex;
            align-items: center;
        }

        .search-btn:hover {
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 8px 20px rgba(0, 102, 255, 0.5);
        }

        .search-btn i {
            margin-right: 0.5rem;
            /* jarak icon ke teks */
        }


        /* Responsive: mobile friendly */
        @media (max-width: 576px) {
            .search-bar-form {
                flex-direction: column;
                gap: 10px;
            }

            .search-btn {
                width: 100%;
                justify-content: center;
            }
        }

        /* ===============================
                                                                                                                               ✨ Tombol Super Premium (WhatsApp & Hapus) ✨
                                                                                                                            ================================= */

        .btn-whatsapp-super,
        .btn-danger-super {
            font-weight: 700;
            font-size: 1.05rem;
            padding: 14px 28px;
            /* padding sama */
            border-radius: 50px;
            /* pill shape */
            display: inline-flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            color: #fff;
            transition: all 0.3s ease, box-shadow 0.3s ease;
            min-width: 220px;
            /* lebar minimum agar seragam */
        }

        /* ===============================
                                                                                                                               Tombol WhatsApp
                                                                                                                            ================================= */
        .btn-whatsapp-super {
            background: linear-gradient(135deg, #25D366, #1ebe5d);
            box-shadow: 0 6px 20px rgba(37, 211, 102, 0.35);
        }

        .btn-whatsapp-super .whatsapp-icon {
            font-size: 1.3rem;
            margin-right: 14px;
            /* jarak icon ke teks */
            transition: all 0.3s ease;
        }

        /* Hover Effects */
        .btn-whatsapp-super:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 8px 25px rgba(37, 211, 102, 0.6), 0 0 12px rgba(37, 211, 102, 0.5) inset;
        }

        .btn-whatsapp-super:hover .whatsapp-icon {
            text-shadow: 0 0 8px #25D366, 0 0 12px #25D366, 0 0 16px #1ebe5d;
        }

        /* ===============================
                                                                                                                               Tombol Hapus Semua Foto
                                                                                                                            ================================= */
        .btn-danger-super {
            background: linear-gradient(135deg, #ff5e57, #ff2a2a);
            box-shadow: 0 6px 20px rgba(255, 94, 87, 0.35);
        }

        .btn-danger-super .fa-trash {
            margin-right: 14px;
            /* samakan dengan WhatsApp */
            font-size: 1.3rem;
            /* samakan ukuran icon */
            transition: all 0.3s ease;
        }

        /* Hover Effects */
        .btn-danger-super:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 8px 25px rgba(255, 94, 87, 0.6), 0 0 12px rgba(255, 42, 42, 0.5) inset;
        }

        .btn-danger-super:hover .fa-trash {
            text-shadow: 0 0 8px #ff5e57, 0 0 12px #ff2a2a, 0 0 16px #ff2a2a;
        }

        /* ===============================
                                                                                                                               Flex container (tengah & responsif)
                                                                                                                            ================================= */
        .d-flex.gap-3 {
            justify-content: center;
            /* selalu di tengah */
            flex-wrap: wrap;
            /* responsif */
            gap: 1rem;
            /* jarak antar tombol */
        }

        /* Mobile Responsive */
        @media (max-width: 576px) {

            .d-flex.gap-3 .btn-whatsapp-super,
            .d-flex.gap-3 .btn-danger-super {
                flex: 1 1 100%;
                max-width: none;
                min-width: 0;
            }
        }

        /* ======== Filter Seragam ======== */
        #filterCrew,
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
        #filterCrew:focus,
        #filterService:focus,
        .filter-select:focus {
            box-shadow: 0 0 0 0.25rem rgba(108, 117, 125, 0.3);
            background-color: #fff;
            border-color: #6abfe3;
        }

        #filterCrew:hover,
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

            #filterCrew,
            #filterService,
            .filter-select {
                width: 100%;
                /* full lebar di mobile */
                min-width: auto;
            }
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
    </style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        document.addEventListener('DOMContentLoaded', function() {

            const items = document.querySelectorAll('.gallery-item');
            const modal = document.getElementById('premiumModal');
            const modalImg = document.getElementById('modalImage');
            const downloadBtn = document.getElementById('downloadBtn');
            const closeModal = document.querySelector('.close-modal');

            let imageList = [];
            let currentIndex = 0;

            // Ambil semua gambar
            items.forEach((item, index) => {
                const fullImg = item.getAttribute('data-full');
                imageList.push(fullImg);

                item.addEventListener('click', function() {
                    currentIndex = index;
                    modalImg.src = fullImg;
                    downloadBtn.href = fullImg;
                    modal.classList.remove('d-none');
                });
            });

            // Close Modal
            closeModal.onclick = () => modal.classList.add('d-none');
            modal.onclick = (e) => {
                if (e.target === modal) modal.classList.add('d-none');
            };

            // Next & Prev Navigation
            document.querySelector('.left-arrow').onclick = function() {
                currentIndex = (currentIndex - 1 + imageList.length) % imageList.length;
                modalImg.src = imageList[currentIndex];
                downloadBtn.href = imageList[currentIndex];
            };

            document.querySelector('.right-arrow').onclick = function() {
                currentIndex = (currentIndex + 1) % imageList.length;
                modalImg.src = imageList[currentIndex];
                downloadBtn.href = imageList[currentIndex];
            };



            /* ============================================================
               ⭐ SWIPE SUPPORT (Mobile Friendly)
            ============================================================ */
            let startX = 0;
            let endX = 0;
            const SWIPE_THRESHOLD = 60; // sensitivitas geser

            if (modalImg) {

                modalImg.addEventListener('touchstart', function(e) {
                    startX = e.touches[0].clientX;
                });

                modalImg.addEventListener('touchmove', function(e) {
                    endX = e.touches[0].clientX;
                });

                modalImg.addEventListener('touchend', function() {
                    let distance = endX - startX;

                    // Geser ke kanan = previous
                    if (distance > SWIPE_THRESHOLD) {
                        document.querySelector('.left-arrow').click();
                    }

                    // Geser ke kiri = next
                    if (distance < -SWIPE_THRESHOLD) {
                        document.querySelector('.right-arrow').click();
                    }

                    startX = 0;
                    endX = 0;
                });
            }

        });

        document.addEventListener('DOMContentLoaded', function() {
            // Tangkap semua form delete-all
            const deleteAllForms = document.querySelectorAll('.delete-all-form');

            deleteAllForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault(); // hentikan submit default

                    Swal.fire({
                        title: 'Yakin ingin menghapus semua foto?',
                        text: "Tindakan ini tidak bisa dibatalkan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ff2a2a',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, hapus semua!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit(); // submit form jika dikonfirmasi
                        }
                    });
                });
            });
        });
        document.getElementById('filterCrew').addEventListener('change', applyFilter);
        document.getElementById('filterService').addEventListener('change', applyFilter);

        function applyFilter() {
            const crew = document.getElementById('filterCrew').value;
            const service = document.getElementById('filterService').value;

            const params = new URLSearchParams(window.location.search);

            if (crew) {
                params.set('crew', crew);
            } else {
                params.delete('crew');
            }

            if (service) {
                params.set('service', service);
            } else {
                params.delete('service');
            }

            params.delete('page'); // reset ke page 1 saat filter

            window.location.search = params.toString();
        }
    </script>
@endpush
