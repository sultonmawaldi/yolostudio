@extends('adminlte::page')

@section('title', 'Upload & Kelola Foto Hasil')

@section('content_header')
    <div class="page-title-wrapper text-center mb-4">
        <h1 class="page-title">
            <i class="fa fa-image me-2"></i> Upload & Kelola Foto Hasil
        </h1>
        <div class="title-divider"></div>
    </div>
@stop

@section('content')


    <!-- ================= FILTER KARYAWAN & LAYANAN ================= -->
    <div class="row mb-3 g-3 align-items-end date-filter-wrapper">

        <!-- Karyawan -->
        <div class="col-md-4">
            <label class="filter-label" for="filterEmployee">
                <i class="fas fa-users me-2"></i> Pilih Karyawan
            </label>

            @php
                $user = auth()->user();
            @endphp

            <select id="filterEmployee" class="form-select filter-select">

                @if ($user && ($user->hasRole('admin') || $user->hasRole('moderator')))

                    <option value="" {{ request('employee') ? '' : 'selected' }}>
                        Semua Karyawan
                    </option>

                    @foreach ($employees as $employee)
                        @if ($employee->user && $employee->user->hasRole('employee'))
                            <option value="{{ $employee->id }}"
                                {{ request('employee') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->user->name ?? '-' }}
                            </option>
                        @endif
                    @endforeach
                @elseif($user && $user->hasRole('employee'))
                    <option value="{{ $user->employee->id }}"
                        {{ request('employee') == $user->employee->id ? 'selected' : '' }}>
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
    <div class="search-card mb-4">

        <form method="GET" action="{{ route('photo-results.index') }}" class="search-form">

            <div class="search-input-wrapper">

                <i class="fa fa-search search-icon"></i>

                <input type="text" name="search" class="search-input"
                    placeholder="Cari kode transaksi atau nama pengguna..." value="{{ request('search') }}">

                <button type="submit" class="search-btn">
                    Cari
                </button>

            </div>

        </form>

    </div>


    {{-- Daftar Transaksi --}}
    @forelse($transactions as $transaction)
        <div class="card mb-4 border-0 shadow-sm photo-card"
            data-employee-id="{{ $transaction->appointment->employee_id ?? '' }}"
            data-service-id="{{ $transaction->appointment->service_id ?? '' }}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0 text-primary">
                        <i class="fa fa-barcode me-1"></i> {{ $transaction->transaction_code }}
                    </h5>

                    <div class="d-flex flex-column text-end">

                        <!-- BADGE NAMA -->
                        <span class="badge bg-warning text-dark d-flex align-items-center gap-1 px-3 py-1 mb-1">
                            <i class="fa fa-user"></i>
                            {{ $transaction->user->name ?? ($transaction->appointment->name ?? 'Tanpa Nama') }}
                        </span>

                        <!-- BADGE LAYANAN -->
                        <span class="badge bg-primary d-flex align-items-center gap-1 px-3 py-1">
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
                        class="refresh-form mb-3">
                        @csrf

                        <button type="submit" class="btn-refresh">
                            <i class="fa fa-sync-alt me-2"></i>
                            Perbarui Link Publik
                        </button>
                    </form>
                @endif

                <form action="{{ route('photo-results.store') }}" method="POST" enctype="multipart/form-data"
                    class="upload-section">
                    @csrf
                    <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">

                    <div id="dropZone" class="drop-zone text-center p-4 rounded">
                        <i class="fa fa-cloud-upload fa-2x mb-2"></i>
                        <p class="mb-1">Seret & lepas foto di sini</p>
                        <small class="text-muted">atau klik untuk memilih file (PNG, JPG, JPEG)</small>

                        <input type="file" name="photos[]" id="fileInput" multiple hidden>
                    </div>

                    <div class="mt-2 text-end upload-action">
                        <button class="btn btn-upload">
                            <i class="fa fa-cloud-upload-alt me-1"></i>
                            Upload Foto
                        </button>
                    </div>
                </form>


                {{-- ⭐ PREMIUM GALLERY --}}
                @if ($transaction->photoResults->count())
                    <div class="premium-gallery mt-3">
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

        .search-card {
            background: #fff;
            border-radius: 16px;
            padding: 14px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
        }

        /* wrapper utama */
        .search-input-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f8f9fa;
            padding: 8px 12px;
            border-radius: 12px;
            border: 1px solid #e9ecef;
            transition: 0.2s ease;
        }

        /* focus effect */
        .search-input-wrapper:focus-within {
            border-color: #0d6efd;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
        }

        /* icon */
        .search-icon {
            color: #6c757d;
            font-size: 14px;
            margin-left: 4px;
        }

        /* input */
        .search-input {
            flex: 1;
            border: none;
            background: transparent;
            outline: none;
            font-size: 14px;
        }

        /* button */
        .search-btn {
            background: linear-gradient(135deg, #0d6efd, #3b82f6);
            border: none;
            color: #fff;
            padding: 8px 16px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 13px;
            transition: 0.2s ease;
        }

        .search-btn:hover {
            transform: translateY(-1px);
            filter: brightness(1.05);
        }

        .search-btn:active {
            transform: scale(0.98);
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

        .drop-zone {
            border: 2px dashed #0d6efd;
            background: #f8f9ff;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .drop-zone:hover {
            background: #eef3ff;
            border-color: #0a58ca;
        }

        .drop-zone.dragover {
            background: #dbe7ff;
            border-color: #084298;
        }

        .btn-upload {
            background: linear-gradient(135deg, #10b981, #34d399);
            color: #fff;
            border: none;
            padding: 10px 18px;
            border-radius: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 6px 15px rgba(16, 185, 129, 0.25);
            transition: all 0.25s ease;
        }

        .btn-upload:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.35);
            filter: brightness(1.05);
        }

        .btn-upload:active {
            transform: scale(0.98);
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2);
        }

        .upload-section {
            margin-bottom: 25px;
        }

        .premium-gallery {
            margin-top: 25px;
            padding-top: 10px;
            border-top: 1px solid #e9ecef;
        }

        .btn-refresh {
            background: linear-gradient(135deg, #f59e0b, #fbbf24);
            color: #1f2937;
            border: none;
            padding: 9px 16px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 6px 15px rgba(245, 158, 11, 0.25);
            transition: all 0.25s ease;
        }

        .btn-refresh:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(245, 158, 11, 0.35);
            filter: brightness(1.05);
        }

        .btn-refresh:active {
            transform: scale(0.97);
        }

        .refresh-form {
            display: inline-block;
        }
    </style>
@endpush

@push('js')
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
        document.getElementById('filterEmployee').addEventListener('change', applyFilter);
        document.getElementById('filterService').addEventListener('change', applyFilter);

        function applyFilter() {
            const employee = document.getElementById('filterEmployee').value;
            const service = document.getElementById('filterService').value;

            const params = new URLSearchParams(window.location.search);

            if (employee) {
                params.set('employee', employee);
            } else {
                params.delete('employee');
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const dropZone = document.getElementById('dropZone');
            const fileInput = document.getElementById('fileInput');

            // 🔴 safety check WAJIB
            if (!dropZone || !fileInput) return;

            // klik untuk pilih file
            dropZone.addEventListener('click', () => fileInput.click());

            // drag over
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.classList.add('dragover');
            });

            // drag leave
            dropZone.addEventListener('dragleave', () => {
                dropZone.classList.remove('dragover');
            });

            // drop
            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.classList.remove('dragover');

                const files = e.dataTransfer.files;

                // penting: assign file
                fileInput.files = files;

                // update teks kalau ada <p>
                const text = dropZone.querySelector('p');
                if (text) {
                    text.innerText = `${files.length} file siap diupload`;
                }
            });

        });
    </script>
@endpush
