@extends('layouts.app')

@section('title', 'Booking')

@section('content')
    <div class="container">
        <div class="booking-container">
            <div class="booking-header">
                <h2><i class="bi bi-calendar-check"></i> Pemesanan Studio </h2>
                <p class="mb-0">Pesan studio foto anda dalam beberapa langkah mudah</p>
            </div>

            <div class="booking-steps position-relative">
                <div class="step active" data-step="1">
                    <div class="step-number">1</div>
                    <div class="step-title">Studio</div>
                </div>
                <div class="step" data-step="2">
                    <div class="step-number">2</div>
                    <div class="step-title">Kategori</div>
                </div>
                <div class="step" data-step="3">
                    <div class="step-number">3</div>
                    <div class="step-title">Layanan</div>
                </div>
                <div class="step" data-step="4">
                    <div class="step-number">4</div>
                    <div class="step-title">Jadwal</div>
                </div>
                <div class="step" data-step="5">
                    <div class="step-number">5</div>
                    <div class="step-title">Konfirmasi</div>
                </div>
            </div>


            <div class="booking-content">
                <!-- Step 1: Studio Selection -->
                <div class="booking-step active" id="step1">
                    <h3 class="mb-4 text-center">
                        <i class="fa fa-map-marker-alt me-2" style="color:#2d92e0;"></i>
                        Pilih Studio Foto
                    </h3>

                    <div class="row row-cols-1 row-cols-md-3 g-4" id="employees-container">
                        <!-- Studio akan dimuat via JS -->
                    </div>
                </div>


                <!-- Step 2: Category Selection -->
                <div class="booking-step" id="step2">
                    <h3 class="mb-4 text-center">
                        <i class="fa fa-table-cells-large me-2" style="color:#2d92e0;"></i>
                        Pilih Kategori Layanan
                    </h3>
                    <div class="selected-employee-name mb-3 fw-bold"></div>

                    <div class="row row-cols-1 row-cols-md-3 g-4" id="categories-container">
                    </div>
                </div>


                <!-- Step 3: Service Selection -->
                <div class="booking-step" id="step3">
                    <h3 class="mb-4 text-center">
                        <i class="fa fa-concierge-bell me-2" style="color:#2d92e0;"></i>
                        Pilih Layanan Foto
                    </h3>

                    <div class="selected-category-name mb-3 fw-bold"></div>

                    <div class="row row-cols-1 row-cols-md-3 g-4" id="services-container">
                    </div>
                </div>


                <!-- Step 4: Date and Time Selection -->
                <div class="booking-step" id="step4">
                    <h3 class="mb-4 text-center">
                        <i class="fa fa-calendar-alt me-2" style="color: #2d92e0; transition: 0.3s;"></i> Pilih Tanggal &
                        Waktu
                    </h3>
                    <div class="selected-service-name mb-3 fw-bold"></div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4 shadow-sm border-0 rounded-4 modern-card">
                                <div
                                    class="card-header bg-light d-flex justify-content-between align-items-center rounded-top-4">
                                    <button class="btn btn-sm btn-light border-0 shadow-sm" id="prev-month">
                                        <i class="bi bi-arrow-left-circle-fill modern-arrow"></i>
                                    </button>
                                    <h5 class="mb-0 fw-semibold text-dark" id="current-month"></h5>
                                    <button class="btn btn-sm btn-light border-0 shadow-sm" id="next-month">
                                        <i class="bi bi-arrow-right-circle-fill modern-arrow"></i>
                                    </button>
                                </div>
                                <div class="card-body">
                                    <table class="table table-calendar text-center align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Sen</th>
                                                <th>Sel</th>
                                                <th>Rab</th>
                                                <th>Kam</th>
                                                <th>Jum</th>
                                                <th>Sab</th>
                                                <th>Min</th>
                                            </tr>
                                        </thead>
                                        <tbody id="calendar-body">
                                            <!-- Calendar will be generated dynamically -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card shadow-sm border-0 rounded-3 modern-card">
                                <div class="card-header text-center py-3 rounded-top-3">
                                    <h5 class="mb-1 fw-semibold bi bi-check2-square"> Slot Waktu Tersedia</h5>
                                    <div id="selected-date-display" class="text-muted small"></div>
                                </div>

                                <div class="card-body">
                                    <div id="time-slots-container" class="d-flex flex-wrap">
                                        <!-- Time slots will be loaded dynamically -->
                                        <div class="text-center text-muted w-100 py-4">
                                            Silahkan pilih tanggal untuk melihat waktu yang tersedia
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 5: Confirmation -->
                <div class="booking-step" id="step5">
                    <h3 class="mb-4 fw-bold text-center">
                        <i class="fa fa-check-circle me-2" style="color: #2d92e0; transition: 0.3s;"></i> Konfirmasi
                        Pemesanan
                    </h3>

                    <!-- Informasi Pelanggan -->
                    @auth
                        @if (Auth::user()->hasAnyRole(['admin', 'moderator', 'employee']))
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header bg-white border-bottom">
                                    <h5 class="mb-0 fw-semibold">
                                        <i class="fa-solid fa-user-plus me-2"></i> Informasi Anda
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form id="customer-info-form">
                                        @csrf
                                        <input type="hidden" id="total_amount" name="total_amount" value="0">
                                        <input type="hidden" id="payment_status" name="payment_status" value="">
                                        <input type="hidden" id="midtrans_order_id" name="midtrans_order_id"
                                            value="">
                                        <input type="hidden" name="coupon_id" id="coupon_id">

                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="customer-name" class="form-label">Nama Lengkap</label>
                                                <input type="text" class="form-control" id="customer-name" name="name"
                                                    placeholder="Nama pelanggan" value="{{ old('name') }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="customer-email" class="form-label">Email</label>
                                                <input type="email" class="form-control" id="customer-email"
                                                    name="email" placeholder="email@domain.com" value="{{ old('email') }}"
                                                    required>
                                            </div>
                                            <div class="col-md-12">
                                                <label for="customer-phone" class="form-label">Nomor HP / WhatsApp</label>
                                                <div class="input-group">
                                                    <span class="input-group-text prefix-phone">
                                                        <img src="https://flagcdn.com/w20/id.png" alt="ID Flag"
                                                            width="20" height="14" class="me-1">
                                                        +62
                                                    </span>
                                                    <input type="tel" class="form-control border-start-0"
                                                        id="customer-phone" name="phone" placeholder="8123456789"
                                                        pattern="[0-9]{8,13}" maxlength="13" inputmode="numeric" required>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <label for="customer-notes" class="form-label">Catatan (Opsional)</label>
                                                <textarea class="form-control" id="customer-notes" name="notes" rows="3"
                                                    placeholder="Tulis catatan jika ada...">{{ old('notes') }}</textarea>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @else
                            <form id="customer-info-form" class="d-none">
                                @csrf
                                <input type="hidden" id="total_amount" name="total_amount" value="0">
                                <input type="hidden" id="payment_status" name="payment_status" value="">
                                <input type="hidden" id="midtrans_order_id" name="midtrans_order_id" value="">
                                <input type="hidden" name="coupon_id" id="coupon_id">

                                <input type="hidden" id="customer-name" value="{{ auth()->user()->name }}">
                                <input type="hidden" id="customer-email" value="{{ auth()->user()->email }}">
                                <input type="hidden" id="customer-phone" value="{{ auth()->user()->phone ?? '' }}">
                                <input type="hidden" id="customer-notes" value="">
                            </form>
                        @endif
                    @else
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white border-bottom">
                                <h5 class="mb-0 fw-semibold">
                                    <i class="fa-solid fa-user-plus me-2"></i> Informasi Anda
                                </h5>
                            </div>
                            <div class="card-body">
                                <form id="customer-info-form">
                                    @csrf
                                    <input type="hidden" id="total_amount" name="total_amount" value="0">
                                    <input type="hidden" id="payment_status" name="payment_status" value="">
                                    <input type="hidden" id="midtrans_order_id" name="midtrans_order_id" value="">
                                    <input type="hidden" name="coupon_id" id="coupon_id">

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="customer-name" class="form-label">Nama Lengkap</label>
                                            <input type="text" class="form-control" id="customer-name" name="name"
                                                placeholder="Nama Anda" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="customer-email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="customer-email" name="email"
                                                placeholder="email@domain.com" required>
                                        </div>
                                        <div class="col-md-12">
                                            <label for="customer-phone" class="form-label">Nomor HP / WhatsApp</label>
                                            <div class="input-group">
                                                <span class="input-group-text prefix-phone">
                                                    <img src="https://flagcdn.com/w20/id.png" alt="ID Flag" width="20"
                                                        height="14" class="me-1">
                                                    +62
                                                </span>
                                                <input type="tel" class="form-control border-start-0" id="customer-phone"
                                                    name="phone" placeholder="8123456789" pattern="[0-9]{8,13}"
                                                    maxlength="13" inputmode="numeric" required>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <label for="customer-notes" class="form-label">Catatan (Opsional)</label>
                                            <textarea class="form-control" id="customer-notes" name="notes" rows="3"
                                                placeholder="Tulis catatan jika ada..."></textarea>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endauth


                    <!-- Ringkasan Pemesanan -->
                    <div class="card shadow-sm border-0 mb-4 summary-card">
                        <div class="card-header bg-grey border-bottom">
                            <h5 class="mb-0 fw-semibold text-center">
                                <i class="fa-solid fa-clipboard-check me-2"></i> Ringkasan Pemesanan
                            </h5>
                        </div>

                        <div class="card-body">

                            <div class="summary-row">
                                <div class="summary-label">
                                    <i class="fa fa-map-marker-alt summary-icon"></i>
                                    Studio
                                </div>
                                <div class="summary-value" id="summary-employee">-</div>
                            </div>

                            <div class="summary-row">
                                <div class="summary-label">
                                    <i class="fa fa-table-cells-large summary-icon"></i>
                                    Kategori
                                </div>
                                <div class="summary-value" id="summary-category">-</div>
                            </div>

                            <div class="summary-row">
                                <div class="summary-label">
                                    <i class="fa fa-concierge-bell summary-icon"></i>
                                    Layanan
                                </div>
                                <div class="summary-value" id="summary-service">-</div>
                            </div>

                            <div class="summary-row">
                                <div class="summary-label">
                                    <i class="fa-solid fa-user-group summary-icon"></i>
                                    Jumlah Orang
                                </div>
                                <div class="summary-value" id="summary-people">-</div>
                            </div>

                            <div class="summary-row">
                                <div class="summary-label">
                                    <i class="fa fa-calendar-alt summary-icon"></i>
                                    Tanggal
                                </div>
                                <div class="summary-value" id="summary-date">-</div>
                            </div>

                            <div class="summary-row">
                                <div class="summary-label">
                                    <i class="fa fa-clock summary-icon"></i>
                                    Waktu
                                </div>
                                <div class="summary-value" id="summary-time">-</div>
                            </div>

                            <div class="summary-row">
                                <div class="summary-label">
                                    <i class="fa fa-hourglass-half summary-icon"></i>
                                    Durasi
                                </div>
                                <div class="summary-value" id="summary-duration">-</div>
                            </div>

                        </div>
                    </div>

                    <!-- PILIH BACKGROUND -->
                    <div class="card shadow-sm border-0 mb-4 d-none" id="background-card">
                        <div class="card-header bg-grey border-bottom">
                            <h5 class="mb-0 fw-semibold text-center">
                                <i class="fa-solid fa-palette me-2"></i> Pilih Latar Belakang
                            </h5>
                        </div>
                        <div class="card-body">
                            <div id="background-container" class="d-flex flex-wrap gap-3 justify-content-center">
                                <!-- JS render di sini -->
                            </div>
                        </div>
                    </div>


                    <div id="addon-container"></div>


                    <!-- Checkout Pembayaran -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-grey border-bottom">
                            <h5 class="mb-0 fw-semibold text-center">
                                <i class="fa-solid fa-cart-shopping me-2"></i> Ringkasan Pembayaran
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Metode Pembayaran -->
                            <div class="mb-4">
                                <label for="payment-method" class="form-label fw-semibold">Metode Pembayaran :</label>
                                <select id="payment-method" class="form-select">
                                    <option value="cash">Bayar Lunas (Cash)</option>
                                    <option value="dp">Bayar Uang Muka (DP)</option>
                                </select>
                            </div>

                            <!-- Kupon -->
                            <div class="mb-4">
                                <label for="coupon-code" class="form-label fw-semibold">Kode Kupon (Opsional) :</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="coupon-code"
                                        placeholder="Masukkan kode kupon">
                                    <button class="btn btn-primary" type="button" id="apply-coupon">Gunakan</button>
                                </div>

                                <div class="form-text text-success d-none" id="coupon-success-msg">
                                    Kupon berhasil diterapkan!
                                </div>
                                <div class="form-text text-danger d-none" id="coupon-error-msg">
                                    Kupon tidak valid.
                                </div>
                            </div>

                            <!-- Rincian Pembayaran -->
                            <div id="payment-summary" class="border-top pt-3">
                                <label for="coupon-code" class="form-label fw-semibold">Rincian Pembayaran :</label>

                                <!-- LAYANAN UTAMA -->
                                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap"
                                    id="service-row">
                                    <span class="text-muted" id="service-name">-</span>
                                    <span class="text-end" id="service-price">Rp0</span>
                                </div>

                                <!-- ADDON -->
                                <div id="addon-rows"></div>

                                <!-- SUBTOTAL -->
                                <div
                                    class="d-flex justify-content-between align-items-center mb-2 flex-wrap border-top pt-2">
                                    <span class="text-muted">Subtotal</span>
                                    <span class="fw-medium text-end" id="original-price">Rp0</span>
                                </div>

                                <!-- DISKON -->
                                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap d-none"
                                    id="discount-row">
                                    <span class="text-muted">Potongan Kupon</span>
                                    <span class="text-end" id="discount-amount">- Rp0</span>
                                </div>

                                <!-- TOTAL -->
                                <div
                                    class="d-flex justify-content-between align-items-center mb-2 border-top pt-2 flex-wrap">
                                    <span class="fw-semibold">Total</span>
                                    <span class="fw-bold text-end" id="final-price">Rp0</span>
                                </div>

                                <!-- DP -->
                                <div class="d-flex justify-content-between align-items-center mb-2 border-top pt-2 flex-wrap d-none"
                                    id="dp-row">
                                    <span class="fw-semibold">Bayar Sekarang (DP)</span>
                                    <span class="fw-bold text-end" id="dp-amount">Rp0</span>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap d-none"
                                    id="sisa-row">
                                    <span class="text-muted">Sisa Pembayaran</span>
                                    <span class="text-end text-danger" id="sisa-payment">Rp0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- SYARAT & KETENTUAN -->
                    <div class="mt-4 border-top pt-3 terms-check">
                        <div class="form-check d-flex align-items-start gap-2">
                            <input class="form-check-input mt-1" type="checkbox" id="agree-terms">

                            <label class="form-check-label small" for="agree-terms">
                                Saya telah membaca dan menyetujui
                                <span class="terms-wrapper">
                                    <a href="#" class="fw-semibold terms-link" data-bs-toggle="modal"
                                        data-bs-target="#termsModal">
                                        syarat & ketentuan
                                    </a>
                                </span>
                                yang berlaku.
                            </label>
                        </div>
                    </div>

                </div> <!-- end Step 5 -->
                <!-- MODAL SYARAT & KETENTUAN -->
                <div class="modal fade" id="termsModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                        <div class="modal-content border-0 shadow-lg rounded-4 terms-modal">

                            <!-- HEADER -->
                            <div class="modal-header border-0 justify-content-center position-relative py-3">
                                <div class="text-center">

                                    <!-- Icon -->
                                    <div class="d-flex justify-content-center mb-2">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                            <!-- ganti warna ikon -->
                                            <i class="fa-solid fa-file-contract text-white fs-5"></i>
                                            <!-- atau bisa pakai text-white -->
                                            <!-- <i class="fa-solid fa-file-contract text-white fs-5"></i> -->
                                        </div>
                                    </div>


                                    <!-- Title -->
                                    <h5 class="modal-title fw-semibold mb-1">
                                        Syarat & Ketentuan
                                    </h5>

                                    <!-- Subtitle dengan Icon Alert -->
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <i class="fa-solid fa-triangle-exclamation text-warning fs-6"></i>
                                        <span class="fw-medium text-warning">
                                            Harap dibaca sebelum melanjutkan proses booking
                                        </span>
                                    </div>

                                </div>

                                <!-- Close button -->
                                <button type="button" class="btn-close position-absolute end-0 top-0 mt-3 me-3"
                                    data-bs-dismiss="modal" aria-label="Close">
                                </button>
                            </div>


                            <!-- BODY -->
                            <div class="modal-body pt-0">
                                <div class="terms-box rounded-3 p-3">

                                    <ul class="list-unstyled mb-0 small terms-list">

                                        <li class="d-flex align-items-start mb-3">
                                            <i class="fa-solid fa-calendar-check text-primary me-3 mt-1"></i>
                                            <span>
                                                Booking hanya berlaku sesuai
                                                <strong>tanggal dan waktu</strong>
                                                yang telah dipilih dan dikonfirmasi.
                                            </span>
                                        </li>

                                        <li class="d-flex align-items-start mb-3">
                                            <i class="fa-solid fa-clock text-primary me-3 mt-1"></i>
                                            <span>
                                                Pelanggan diharapkan hadir
                                                <strong>10 menit sebelum sesi dimulai</strong>
                                                untuk melakukan persiapan.
                                            </span>
                                        </li>

                                        <li class="d-flex align-items-start mb-3">
                                            <i class="fa-solid fa-hourglass-half text-warning me-3 mt-1"></i>
                                            <span>
                                                Toleransi keterlambatan maksimal
                                                <strong>10 menit</strong>.
                                            </span>
                                        </li>

                                        <li class="d-flex align-items-start mb-3">
                                            <i class="fa-solid fa-couch text-success me-3 mt-1"></i>
                                            <span>
                                                Semua aksesoris dan properti studio dapat digunakan
                                                <strong>gratis</strong> selama sesi foto,
                                                selama digunakan secara wajar.
                                            </span>
                                        </li>

                                        <li class="d-flex align-items-start mb-3">
                                            <i class="fa-solid fa-money-bill-wave text-danger me-3 mt-1"></i>
                                            <span>
                                                Pembayaran <strong>DP tidak dapat dikembalikan</strong>
                                                jika terjadi pembatalan sepihak.
                                            </span>
                                        </li>

                                        <li class="d-flex align-items-start mb-3">
                                            <i class="fa-solid fa-arrows-rotate text-info me-3 mt-1"></i>
                                            <span>
                                                Jadwal ulang dapat dilakukan maksimal
                                                <strong>H-1 sebelum jadwal booking</strong>
                                                dan hanya dapat dilakukan
                                                <strong>1 (satu) kali</strong>.
                                            </span>
                                        </li>

                                        <li class="d-flex align-items-start mb-3">
                                            <i class="fa-solid fa-circle-plus text-primary me-3 mt-1"></i>
                                            <span>
                                                Layanan tambahan dikenakan biaya sesuai harga yang berlaku.
                                            </span>
                                        </li>

                                    </ul>

                                </div>
                            </div>

                            <!-- FOOTER -->
                            <div class="modal-footer border-0 justify-content-center pt-3 pb-3">
                                <button class="btn btn-primary px-5 rounded-pill" data-bs-dismiss="modal">
                                    Saya Mengerti
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div> <!-- end booking-content -->

            <!-- Booking Footer -->
            <div class="booking-footer mt-4 d-flex justify-content-between">
                <button class="btn btn-outline-secondary" id="prev-step" disabled>
                    <i class="bi bi-arrow-left"></i> Kembali
                </button>
                <button class="btn btn-primary" id="next-step">
                    Selanjutnya <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </div> <!-- end booking-container -->
    </div> <!-- end container -->

    <!-- Success Modal -->
    <div class="modal fade" id="bookingSuccessModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 rounded-3 shadow-sm">

                <!-- Header -->
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Booking Dikonfirmasi!</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <!-- Body -->
                <div class="modal-body p-3 p-sm-4">

                    <!-- Icon -->
                    <div class="text-center mb-3">
                        <i class="bi bi-check-circle text-success" style="font-size: 3.5rem;"></i>
                    </div>

                    <!-- Terima Kasih + Nama Customer sejajar -->
                    <div class="text-center mb-2">
                        <h4 class="d-inline-block mb-0 me-2">Terima Kasih!</h4>
                        <span class="fw-bold fs-5" id="modal-customer-name"></span>
                    </div>

                    <p class="text-center mb-2 small">Pemesanan Anda telah berhasil di booking.</p>

                    <div class="alert alert-info small mb-3 text-center" style="line-height:1.4;">
                        Email konfirmasi & WhatsApp telah dikirim ke alamat Anda.
                    </div>

                    <!-- Ringkasan Booking -->
                    <div class="booking-details">
                        <h6 class="fw-bold mb-2">Detail Booking:</h6>
                        <table class="table table-borderless table-sm custom-table mb-0">
                            <tbody id="modal-booking-details">
                                <!-- Data akan diinject oleh JS saveBooking -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer justify-content-center border-0">
                    <button type="button" class="btn btn-success px-4 py-2" id="bookingModalCloseBtn">Tutup</button>
                </div>
            </div>
        </div>
    </div>



    <script>
        @if (auth()->check())
            window.currentAuthUser = {
                id: {{ auth()->user()->id }},
                role: "{{ optional(auth()->user()->roles->first())->name ?? 'member' }}",
                name: "{{ auth()->user()->name }}"
            };
        @else
            window.currentAuthUser = null;
        @endif
    </script>

    <script>
        $(document).ready(function() {

            let currentMonth = new Date().getMonth();
            let currentYear = new Date().getFullYear();

            const categories = @json($categories);

            // 🔥 FIX: GLOBAL EMPLOYEES
            let employees = [];

            // 🔥 GLOBAL BOOKING STATE
            window.bookingState = {
                currentStep: 1,
                selectedCategory: null,
                selectedService: null,
                selectedEmployee: null,
                selectedDate: null,
                selectedTime: null,

                // 🖼️ BACKGROUND PILIHAN
                selectedBackground: null
            };



            /* =====================================================
             INIT
            ===================================================== */
            updateProgressBar();
            generateCalendar();

            loadStudios();
            goToStep(1);

            /* =====================================================
             STEP NAVIGATION
            ===================================================== */
            $("#next-step").click(function() {
                if (!validateStep(bookingState.currentStep)) return;
                if (bookingState.currentStep < 5) {
                    goToStep(bookingState.currentStep + 1);
                } else {
                    submitBooking();
                }
            });

            $("#prev-step").click(function() {
                if (bookingState.currentStep > 1) {
                    goToStep(bookingState.currentStep - 1);
                }
            });

            /* =====================================================
             LOAD CATEGORY BY EMPLOYEE (🔥 FIXED SCOPE)
            ===================================================== */
            function loadCategoriesByEmployee(employeeId) {
                $("#categories-container").html(`
    <div class="d-flex justify-content-center align-items-center w-100"
         style="min-height:300px">
        <div class="spinner-border text-primary"></div>
    </div>
`);


                $.ajax({
                    url: `/employees/${employeeId}/categories`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(res) {
                        $("#categories-container").empty();

                        if (!res.categories || res.categories.length === 0) {
                            $("#categories-container").html(`
                                <div class="d-flex justify-content-center align-items-center w-100 text-muted"
                                    style="min-height:300px">
                                    <p class="mb-0">Tidak ada kategori untuk studio ini</p>
                                </div>
                            `);
                            return;
                        }

                        res.categories.forEach((category, index) => {
                            $("#categories-container").append(`
                        <div class="col animate-slide-in" style="animation-delay:${index * 80}ms">
                            <div class="card border h-100 category-card text-center p-2"
                                 data-category="${category.id}">
                                <div class="card-body">
                                    ${category.image ? `<img src="uploads/images/category/${category.image}" class="img-fluid mb-2 rounded">` : ''}
                                    <h5>${category.title}</h5>
                                    <p class="text-muted small">${category.body || ''}</p>
                                </div>
                            </div>
                        </div>
                    `);
                        });
                    },
                    error: function() {
                        $("#categories-container").html(`
                    <div class="d-flex justify-content-center align-items-center w-100 text-danger"
                        style="min-height:300px">
                        <p class="mb-0">Gagal memuat kategori</p>
                    </div>
                `);
                    }
                });
            }

            /* =====================================================
               STEP 1 — STUDIO
               ===================================================== */

            function loadStudios() {
                // Tampilkan spinner loading
                $("#employees-container").html(`
        <div class="d-flex justify-content-center align-items-center w-100"
            style="min-height:300px">
            <div class="spinner-border text-primary"></div>
        </div>
    `);

                $.ajax({
                    url: '/employees',
                    type: 'GET',
                    success: function(res) {
                        $("#employees-container").empty();

                        // Ambil data employees dari response
                        employees = res.employees || [];

                        // Filter hanya employee (dipakai sebagai studio di frontend)
                        const studioList = employees.filter(emp => emp.role === 'employee');

                        // Jika tidak ada studio
                        if (studioList.length === 0) {
                            $("#employees-container").html(`
                    <p class="text-center text-muted w-100 mt-3">
                        Tidak ada studio tersedia.
                    </p>
                `);
                            return;
                        }

                        studioList.forEach(employee => {

                            const user = employee.user || {};
                            const studioName = user.name || 'Studio';
                            const imageUrl = user.image_url || '/assets/img/studio.png';

                            $("#employees-container").append(`
                    <div class="col animate-slide-in">
                        <div class="card border h-100 employee-card text-center p-3"
                            data-employee="${employee.id}">
                            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                
                                <img src="${imageUrl}"
                                    class="rounded-circle mb-3 shadow-sm"
                                    style="width:120px;height:120px;object-fit:cover">

                                <h5 class="mb-1">${studioName}</h5>
                                <p class="text-muted small mb-0">Studio</p>

                            </div>
                        </div>
                    </div>
                `);
                        });

                    },
                    error: function(err) {
                        console.error('Gagal load studio:', err);
                        $("#employees-container").html(`
                <p class="text-center text-danger w-100 mt-3">
                    Gagal memuat studio.
                </p>
            `);
                    }
                });
            }


            // Event klik untuk pilih studio
            $(document).on("click", ".employee-card", function() {
                $(".employee-card").removeClass("selected");
                $(this).addClass("selected");

                const employeeId = $(this).data("employee");
                bookingState.selectedEmployee = employees.find(e => e.id == employeeId);

                if (!bookingState.selectedEmployee) return;

                // Reset step berikut
                bookingState.selectedCategory = null;
                bookingState.selectedService = null;
                bookingState.selectedDate = null;
                bookingState.selectedTime = null;

                const studioName = bookingState.selectedEmployee.user?.name ?? 'Studio';

                $(".selected-employee-name").text(`Studio Dipilih : ${studioName}`);

                loadCategoriesByEmployee(employeeId);

                updateCalendar();
                goToStep(2);
            });


            /* =====================================================
             STEP 2 — CATEGORY
            ===================================================== */
            $(document).on("click", ".category-card", function() {
                $(".category-card").removeClass("selected");
                $(this).addClass("selected");

                bookingState.selectedCategory = $(this).data("category");
                bookingState.selectedService = null;
                bookingState.selectedDate = null;
                bookingState.selectedTime = null;

                // ✅ TAMBAHAN — tampilkan kategori di STEP 3
                const categoryTitle = $(this).find("h5").text();
                $("#step3 .selected-category-name")
                    .text(`Kategori Dipilih : ${categoryTitle}`);

                updateServicesStep(bookingState.selectedCategory);
                goToStep(3);
            });

            /* =====================================================
             STEP 3 — SERVICE
            ===================================================== */
            $(document).on("click", ".service-card", function() {
                $(".service-card").removeClass("selected");
                $(this).addClass("selected");

                // 🔥 AMBIL SEMUA DATA HARGA SEKALI
                const price = parseInt($(this).data("price")) || 0;
                const maxPeople = parseInt($(this).data("max-people")) || 1;
                const minPeople = parseInt($(this).data("min-people")) || 1;
                const extraPrice = parseInt($(this).data("extra-price")) || 0;
                const dpAmount = parseInt($(this).data("dp-amount")) || 0;

                // ✅ SATU SUMBER DATA UTAMA
                bookingState.selectedService = {
                    id: $(this).data("service"),
                    title: $(this).find(".card-title").text(),
                    price: price,
                    max_people: maxPeople,
                    min_people: minPeople,
                    extra_price_per_person: extraPrice,
                    dp_amount: dpAmount
                };

                // 🔥 GANTI SUMBER HARGA (pengganti handler lama)
                window.selectedServiceTitle = bookingState.selectedService.title;
                window.selectedServicePrice = price;
                window.maxPeople = maxPeople;
                window.minPeople = minPeople;
                window.extraPricePerPerson = extraPrice;
                window.dpAmount = dpAmount;
                window.peopleCount = minPeople;

                // UI step 4
                $("#step4 .selected-service-name")
                    .text(`Layanan Dipilih : ${bookingState.selectedService.title}`);

                basePrice = window.selectedServicePrice;
                updatePeopleSummary();
                updatePaymentSummary();



                goToStep(4);
            });


            /* =====================================================
             STEP 4 — DATE
            ===================================================== */
            $(document).on("click", ".calendar-day:not(.disabled)", function() {
                $(".calendar-day").removeClass("selected");
                $(this).addClass("selected");

                bookingState.selectedDate = $(this).data("date");
                updateTimeSlots(bookingState.selectedDate);
            });

            function loadServiceAddons() {
                const serviceId = bookingState.selectedService?.id;
                if (!serviceId) return;

                const container = $("#addon-container");
                const serviceMaxPeople = bookingState.selectedService?.peopleCount ?? null;

                container.html(`
        <div class="d-flex justify-content-center align-items-center" style="height:100px">
            <div class="spinner-border text-primary"></div>
        </div>
    `);

                $.get(`/services/${serviceId}/addons`)
                    .done(function(res) {

                        container.empty();
                        if (!res.addons || res.addons.length === 0) return;

                        container.append(`
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-grey border-bottom text-center">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-plus me-2"></i>
                            Layanan Tambahan
                        </h5>
                    </div>
                </div>
            `);

                        res.addons.forEach(a => {

                            let maxQty = (a.max_qty !== null && a.max_qty !== undefined) ?
                                Number(a.max_qty) :
                                null;

                            // =========================
                            // PHOTOBOX DYNAMIC MAX PEOPLE
                            // =========================
                            let isDynamicPeople = false;

                            if (
                                a.unit === 'person' &&
                                a.name.toLowerCase().includes('photobox')
                            ) {
                                isDynamicPeople = true;
                                maxQty = window.peopleCount || serviceMaxPeopleFallback() || 1;
                            }

                            let unitText = '';
                            let extraInfo = '';

                            if (a.unit === 'minute') {
                                extraInfo = `<small class="ms-2">(+5 menit)</small>`;

                            } else if (a.unit === 'person') {

                                unitText = 'orang';

                                if (maxQty && !isDynamicPeople) {
                                    extraInfo = `<small class="ms-2">(maks. ${maxQty} orang)</small>`;
                                }

                                // kalau dynamic → jangan tampilkan angka mentah
                                if (isDynamicPeople) {
                                    extraInfo =
                                        `<small class="ms-2">(maks. mengikuti jumlah orang)</small>`;
                                }

                            } else {
                                unitText = a.unit ?? '';
                            }

                            container.append(`
        <div class="card shadow-sm border-0 mb-3 addon-item"
            data-addon-id="${a.id}"
            data-addon-name="${a.name}"
            data-addon-price="${a.price}"
            data-addon-unit="${a.unit}"
            data-addon-max="${isDynamicPeople ? 'dynamic_people' : (maxQty ?? '')}">

            <div class="card-header bg-grey border-bottom">
                <h6 class="mb-0 fw-semibold">
                    <i class="fa-solid fa-plus me-2"></i>
                    ${a.name}
                </h6>
            </div>

            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">

                    <span class="fs-6">
                        ${formatRupiah(a.price)}
                        ${unitText ? `/ ${unitText}` : ''}
                        ${extraInfo}
                    </span>

                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn-modern addon-decrease">
                            <i class="bi bi-dash-lg"></i>
                        </button>

                        <span class="addon-qty fw-semibold fs-5 text-center"
                              style="min-width:30px">0</span>

                        <button type="button" class="btn-modern addon-increase">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>

                </div>
            </div>

            <input type="hidden"
                   name="addons[${a.id}]"
                   class="addon-input"
                   value="0">
        </div>
    `);
                        });

                    })
                    .fail(() => container.empty());
            }








            /* =====================================================
               CORE FUNCTIONS
            ===================================================== */
            function goToStep(step) {
                // Tampilkan step yang aktif
                $(".booking-step").removeClass("active");
                $("#step" + step).addClass("active");

                // Update progress bar steps
                $(".step").removeClass("active completed");
                for (let i = 1; i <= 5; i++) {
                    if (i < step) $(`.step[data-step="${i}"]`).addClass("completed");
                    if (i === step) $(`.step[data-step="${i}"]`).addClass("active");
                }

                // Simpan state current step
                bookingState.currentStep = step;

                // Update UI
                updateProgressBar();
                updateStepButtons(step);

                // 🔥 Load addons hanya di STEP 5
                if (step === 5) {
                    loadServiceAddons();

                    if (bookingState?.selectedService?.id) {
                        loadServiceBackgrounds(bookingState.selectedService.id);
                    }
                } else {
                    // ✨ RESET BACKGROUND jika step < 5
                    bookingState.selectedBackground = null;
                    document.querySelectorAll('.background-item').forEach(el => {
                        el.classList.remove('selected', 'active');
                    });
                }

                // ✨ RESET TANGGAL & WAKTU jika step < 4
                if (step < 4) {
                    // Reset state
                    bookingState.selectedDate = null;
                    bookingState.selectedTime = null;
                    window.selectedStartTime = null;
                    window.selectedEndTime = null;
                    window.selectedTimeDisplay = null;

                    // Reset UI kalender
                    $(".calendar-day").removeClass("selected");

                    // Reset UI slot waktu
                    $(".time-slot").removeClass("selected active");
                    $("#time-slots-container").html(`
        <div class="text-center w-100 py-4">
            <div class="alert alert-info">
                <i class="bi bi-calendar-event me-2"></i>
                Silakan pilih tanggal untuk melihat slot waktu yang tersedia
            </div>
        </div>
    `);

                    // Jika ada input hidden / form
                    const dateInput = document.getElementById('booking-date');
                    const timeInput = document.getElementById('booking-time');
                    if (dateInput) dateInput.value = "";
                    if (timeInput) timeInput.value = "";
                }
            }



            function updateProgressBar() {
                const progress = ((bookingState.currentStep - 1) / 4) * 100;
                $(".progress").css("width", progress + "%");
            }

            function updateStepButtons(step) {
                const $prev = $("#prev-step");
                const $next = $("#next-step");

                // Reset button
                $prev.prop("disabled", false).removeClass("d-none");
                $next.prop("disabled", false).removeClass("d-none");

                switch (step) {
                    case 1:
                        $prev.addClass("d-none");
                        $next.addClass("d-none");
                        break;

                    case 2:
                    case 3:
                        $prev.show();
                        $next.addClass("d-none");
                        break;

                    case 4:
                        $prev.show();
                        $next.show().html(`Selanjutnya <i class="bi bi-arrow-right"></i>`);
                        break;

                    case 5:
                        $prev.show();
                        $next.show().html(`Konfirmasi & Bayar <i class="bi bi-check-circle"></i>`);
                        break;
                }
            }



            function validateStep(step) {
                const alertConfig = {
                    icon: 'warning',
                    title: 'Oops...',
                    confirmButtonColor: '#3085d6',
                    scrollbarPadding: false,
                };

                switch (step) {
                    case 1:
                        if (!bookingState.selectedEmployee) {
                            Swal.fire({
                                ...alertConfig,
                                text: 'Silakan pilih studio terlebih dahulu!'
                            });
                            return false;
                        }
                        return true;

                    case 2:
                        if (!bookingState.selectedCategory) {
                            Swal.fire({
                                ...alertConfig,
                                text: 'Silakan pilih kategori terlebih dahulu!'
                            });
                            return false;
                        }
                        return true;

                    case 3:
                        if (!bookingState.selectedService) {
                            Swal.fire({
                                ...alertConfig,
                                text: 'Silakan pilih layanan terlebih dahulu!'
                            });
                            return false;
                        }
                        return true;

                    case 4:
                        if (!bookingState.selectedDate || !bookingState.selectedTime) {
                            Swal.fire({
                                ...alertConfig,
                                text: 'Silakan pilih tanggal dan waktu!'
                            });
                            return false;
                        }
                        return true;

                    default:
                        return true;
                }
            }


            function updateServicesStep(categoryId) {

                // safety check
                if (!bookingState.selectedEmployee || !bookingState.selectedEmployee.id) {
                    console.error('Karyawan belum dipilih');
                    return;
                }

                const employeeId = bookingState.selectedEmployee.id;

                // loading
                $("#services-container").html(`
        <div class="d-flex justify-content-center align-items-center w-100"
             style="min-height:300px">
            <div class="spinner-border text-primary"></div>
        </div>
    `);

                $.ajax({
                    url: `/employees/${employeeId}/categories/${categoryId}/services`,
                    type: 'GET',
                    dataType: 'json',

                    success: function(res) {

                        $("#services-container").empty();

                        // validasi response
                        if (!res || !res.services || res.services.length === 0) {
                            $("#services-container").html(`
                    <div class="d-flex justify-content-center align-items-center w-100"
                         style="min-height:300px">
                        <div class="text-center text-muted">
                            <i class="bi bi-inbox fs-1 mb-2 d-block"></i>
                            <p class="mb-0">Tidak ada layanan untuk kategori ini</p>
                        </div>
                    </div>
                `);
                            return;
                        }

                        // render services
                        res.services.forEach(service => {

                            // image dari DB + fallback
                            const imageUrl = service.image ?
                                `/uploads/images/service/${service.image}` :
                                `/uploads/images/no-image.jpg`;

                            const price = service.price ? Number(service.price).toLocaleString(
                                'id-ID') : '0';
                            const minPeople = service.min_people ?? 1;
                            const maxPeople = service.max_people ?? '-';

                            $("#services-container").append(`
                    <div class="col animate-slide-in">
                        <div class="card border h-100 service-card text-center p-2"
                             data-service="${service.id}"
                             data-price="${service.price ?? 0}"
                             data-max-people="${service.max_people ?? 0}"
                             data-dp-amount="${service.dp_amount ?? 0}"
                             data-min-people="${minPeople}"
                             data-extra-price="${service.extra_price_per_person ?? 0}">

                            <div class="card-body">

                                <div class="mb-2 rounded overflow-hidden"
                                    style="height:300px;">
                                    <img src="${imageUrl}"
                                        alt="${service.title}"
                                        style="width:100%; height:100%; object-fit:cover; display:block;">
                                </div>

                                <h5 class="card-title mb-3">${service.title}</h5>

                                <p class="fw-bold mb-2" style="font-size:1.1rem; line-height:1.4;">
                                    Rp ${price} <span class="text-muted fw-normal" style="font-size:0.9rem;">/ ${minPeople} orang</span>
                                </p>

                                <p class="text-muted small mt-5 mb-0"
                                   style="line-height:1.6;">
                                    <i class="fas fa-users me-1"></i>
                                    Maks. ${maxPeople} orang
                                </p>

                            </div>
                        </div>
                    </div>
                `);
                        });
                    },

                    error: function(xhr) {
                        console.error(xhr);

                        $("#services-container").html(`
                <div class="d-flex justify-content-center align-items-center w-100"
                     style="min-height:300px">
                    <div class="text-center text-danger">
                        <i class="bi bi-x-circle fs-1 mb-2 d-block"></i>
                        <p class="mb-0">Gagal memuat layanan</p>
                    </div>
                </div>
            `);
                    }
                });
            }







            function generateCalendar() {
                renderCalendar(currentMonth, currentYear);
            }
            $("#prev-month").click(function() {
                navigateMonth(-1);
            });

            $("#next-month").click(function() {
                navigateMonth(1);
            });


            function renderCalendar(month, year) {
                currentMonth = month;
                currentYear = year;

                const firstDay = new Date(year, month, 1);
                const lastDay = new Date(year, month + 1, 0);
                const daysInMonth = lastDay.getDate();
                const startingDay = (firstDay.getDay() + 6) % 7; // Senin = 0

                const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus",
                    "September", "Oktober", "November", "Desember"
                ];
                $("#current-month").text(`${monthNames[month]} ${year}`);
                $("#calendar-body").empty();

                let date = 1;
                for (let i = 0; i < 6; i++) {
                    const row = $("<tr></tr>");
                    for (let j = 0; j < 7; j++) {
                        if (i === 0 && j < startingDay) row.append("<td></td>");
                        else if (date > daysInMonth) break;
                        else {
                            const today = new Date();
                            const cellDate = new Date(year, month, date);
                            const formattedDate =
                                `${year}-${(month + 1).toString().padStart(2,'0')}-${date.toString().padStart(2,'0')}`;
                            const isPast = cellDate < new Date(today.setHours(0, 0, 0, 0));
                            const cell = $(
                                `<td class="text-center calendar-day${isPast ? ' disabled' : ''}" data-date="${formattedDate}">${date}</td>`
                            );
                            row.append(cell);
                            date++;
                        }
                    }
                    if (row.children().length > 0) $("#calendar-body").append(row);
                }
            }


            function navigateMonth(direction) {
                let month = currentMonth + direction;
                let year = currentYear;

                if (month < 0) {
                    month = 11;
                    year--;
                } else if (month > 11) {
                    month = 0;
                    year++;
                }

                renderCalendar(month, year);
            }



            function updateCalendar() {

                // Clear previous selections
                bookingState.selectedDate = null;
                bookingState.selectedTime = null;
                $(".calendar-day").removeClass("selected");
                $(".time-slot").removeClass("selected");

                // Show initial state instead of loading spinner
                $("#time-slots-container").html(`
                    <div class="text-center w-100 py-4">
                        <div class="alert alert-info">
                            <i class="bi bi-calendar-event me-2"></i>
                            Silakan pilih tanggal untuk melihat slot waktu yang tersedia
                        </div>
                    </div>
                `);
            }


            // ✅ Update time slots dan highlight slot yang sudah dipilih
            function updateTimeSlots(selectedDate) {
                if (!selectedDate) {
                    $("#time-slots-container").html(`
        <div class="text-center w-100 py-4">
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Tidak ada tanggal yang dipilih
            </div>
        </div>
    `);
                    return;
                }

                if (!bookingState.selectedEmployee) {
                    $("#time-slots-container").html(`
        <div class="text-center w-100 py-4">
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Silakan pilih studio terlebih dahulu
            </div>
        </div>
    `);
                    return;
                }

                if (!bookingState.selectedService) {
                    $("#time-slots-container").html(`
        <div class="text-center w-100 py-4">
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Silakan pilih layanan terlebih dahulu
            </div>
        </div>
    `);
                    return;
                }

                const employeeId = bookingState.selectedEmployee.id;
                const serviceId = bookingState.selectedService.id;
                const apiDate = new Date(selectedDate).toISOString().split('T')[0];

                $("#time-slots-container").html(`
    <div class="text-center w-100 py-4">
        <div class="spinner-border text-primary" role="status"></div>
        <div class="mt-2">Memeriksa ketersediaan...</div>
    </div>
`);

                $.ajax({
                    url: `/employees/${employeeId}/availability/${apiDate}`,
                    data: {
                        service_id: serviceId
                    },
                    success: function(response) {
                        $("#time-slots-container").empty();

                        const slots = response.available_slots || [];
                        if (!slots.length) {
                            $("#time-slots-container").html(`
                <div class="text-center w-100 py-4">
                    <div class="alert alert-warning">
                        <i class="bi bi-clock-history me-2"></i>
                        Tidak ada slot yang tersedia untuk tanggal ini
                    </div>
                </div>
            `);
                            return;
                        }

                        const sessionDuration = parseInt(slots[0]?.session_duration) || 0;
                        const breakDuration = parseInt(slots[0]?.break_duration) || 0;

                        bookingState.selectedEmployee.sessionDuration = sessionDuration;
                        bookingState.selectedEmployee.breakDuration = breakDuration;
                        bookingState.selectedEmployee.slot_group_id = response.slot_group_id;

                        $("#time-slots-container").append(`
            <div class="slot-info mb-3">
                <small class="text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    Sesi Foto: ${sessionDuration} menit
                    ${breakDuration ? ` | Jeda: ${breakDuration} menit` : ''}
                </small>
            </div>
        `);

                        const $slotsContainer = $(
                            "<div class='slots-grid d-flex flex-wrap justify-content-center gap-2'></div>"
                        );

                        slots.forEach(slot => {
                            const displayText = `${slot.start} - ${slot.end}`;

                            // 🔥 SAFE DEFAULT (ANTI ERROR)
                            const isHoliday = slot.is_holiday ?? false;
                            const isBooked = slot.is_booked ?? false;

                            const slotElement = $(`
                <div class="time-slot btn btn-outline-primary mb-2 ${isBooked || isHoliday ? 'disabled' : ''}"
                    data-start="${slot.start}"
                    data-end="${slot.end}"
                    data-time="${displayText}"
                    title="${displayText}">
                    <i class="bi bi-clock me-1"></i> ${slot.start}
                </div>
            `);

                            // 🔥 TAMBAH CLASS VISUAL
                            if (isHoliday) {
                                slotElement.addClass('slot-holiday');
                            } else if (isBooked) {
                                slotElement.addClass('slot-booked');
                            }

                            // ✅ hanya bisa klik jika available
                            if (!isBooked && !isHoliday) {
                                slotElement.on("click", function() {
                                    $(".time-slot").removeClass("selected active");
                                    $(this).addClass("selected active");

                                    bookingState.selectedTime = {
                                        start: $(this).data("start"),
                                        end: $(this).data("end"),
                                        display: $(this).data("time")
                                    };

                                    window.selectedStartTime = bookingState.selectedTime
                                        .start;
                                    window.selectedEndTime = bookingState.selectedTime
                                        .end;
                                    window.selectedTimeDisplay = bookingState
                                        .selectedTime.display;

                                    updateSummary();
                                });

                                if (bookingState.selectedTime?.start === slot.start) {
                                    slotElement.addClass("selected active");
                                }
                            }

                            $slotsContainer.append(slotElement);
                        });

                        $("#time-slots-container").append($slotsContainer);
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON?.error || 'Kesalahan saat memuat ketersediaan';
                        $("#time-slots-container").html(`
            <div class="text-center w-100 py-4">
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-octagon me-2"></i> ${msg}
                </div>
            </div>
        `);
                    }
                });
            }



            // ✅ Update summary agar pakai durasi, break pivot & format time slot start-end
            function updateSummary() {
                const selectedCategory = categories.find(cat => cat.id == bookingState.selectedCategory);

                $("#summary-category").text(selectedCategory ? selectedCategory.title : "Not selected");

                if (bookingState.selectedService) {
                    const sessionDuration = bookingState.selectedTime?.slot_duration ??
                        bookingState.selectedEmployee.sessionDuration ??
                        bookingState.selectedEmployee.slot_duration;

                    $("#summary-service").text(`${bookingState.selectedService.title}`);
                    $("#summary-duration").text(`${sessionDuration} menit`);
                }

                if (bookingState.selectedEmployee) {
                    $("#summary-employee").text(bookingState.selectedEmployee.user.name);
                }

                if (bookingState.selectedDate && bookingState.selectedTime?.start && bookingState.selectedTime
                    ?.end) {
                    const formattedDate = new Date(bookingState.selectedDate).toLocaleDateString("id-ID", {
                        weekday: "long",
                        year: "numeric",
                        month: "long",
                        day: "numeric",
                    });

                    const startTime = bookingState.selectedTime.start.slice(0, 5); // HH:MM
                    const endTime = bookingState.selectedTime.end.slice(0, 5); // HH:MM

                    $("#summary-date").text(`${formattedDate}`);
                    $("#summary-time").text(`${startTime} – ${endTime} WIB`);
                }
            }

            function loadServiceBackgrounds(serviceId) {
                const container = $('#background-container');
                const card = $('#background-card');

                // Tampilkan spinner
                container.html(`
        <div class="d-flex justify-content-center align-items-center" style="height: 100px;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Memuat...</span>
            </div>
        </div>
    `);

                $.get(`/services/${serviceId}/backgrounds`, function(res) {
                    container.empty();

                    // Jika tidak ada background, sembunyikan card
                    if (!res.backgrounds || !res.backgrounds.length) {
                        card.addClass('d-none');

                        // Tandai service ini TIDAK punya background
                        if (bookingState.selectedService) {
                            bookingState.selectedService.hasBackgrounds = false;
                        }

                        return;
                    }

                    card.removeClass('d-none');

                    // Tandai service ini punya background
                    if (bookingState.selectedService) {
                        bookingState.selectedService.hasBackgrounds = true;
                    }

                    res.backgrounds.forEach(bg => {
                        container.append(`
                <div class="d-flex flex-column align-items-center">
                    <div class="background-item"
                        data-id="${bg.id}"
                        data-name="${bg.name}"
                        data-value="${bg.value}"
                        title="${bg.name}"
                        style="background:${bg.value};">
                    </div>
                    <div class="bg-name">${bg.name}</div>
                </div>
            `);
                    });
                }).fail(err => {
                    container.html(`
            <div class="text-center text-danger small">
                Gagal memuat background
            </div>
        `);
                    console.error('Background error', err.responseText);
                });
            }




            /// ✅ Simpan booking ke server
            function saveBooking(data) {
                $.ajax({
                    url: '/bookings',
                    method: 'POST',
                    data: data,
                    success: function(res) {
                        const bookingId = res.appointment?.booking_id || '-';
                        const bookingDate = new Date(data.booking_date);
                        const formattedDate = bookingDate.toLocaleDateString('id-ID', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric'
                        });
                        const startTime = data.booking_start_time?.slice(0, 5);
                        const endTime = data.booking_end_time?.slice(0, 5);

                        const bookingItems = [{
                                icon: 'bi-hash',
                                label: 'ID Booking',
                                value: bookingId
                            },
                            {
                                icon: 'bi-card-checklist',
                                label: 'Layanan',
                                value: data.service_title
                            },
                            {
                                icon: 'bi-calendar-check',
                                label: 'Tanggal',
                                value: formattedDate
                            },
                            {
                                icon: 'bi-clock',
                                label: 'Waktu',
                                value: `${startTime} – ${endTime} WIB`
                            },
                            {
                                icon: 'bi-people',
                                label: 'Jumlah Orang',
                                value: data.people_count || 1
                            },
                            {
                                icon: 'bi-credit-card',
                                label: 'Status Pembayaran',
                                value: data.payment_status
                            },
                            {
                                icon: 'bi-cash-stack',
                                label: 'Total',
                                value: `Rp ${parseInt(data.total_amount).toLocaleString('id-ID')}`
                            }
                        ];

                        // Inject data ke modal sebagai tabel
                        const tbody = $('#modal-booking-details');
                        tbody.empty();
                        bookingItems.forEach(item => {
                            tbody.append(`
                    <tr>
                        <td class="fw-semibold d-flex align-items-center gap-2">
                            <i class="bi ${item.icon}"></i> ${item.label}
                        </td>
                        <td>${item.value}</td>
                    </tr>
                `);
                        });

                        // Inject nama customer
                        $('#modal-customer-name').text(data.name);

                        // Tampilkan modal
                        const modalEl = document.getElementById('bookingSuccessModal');
                        const modal = new bootstrap.Modal(modalEl, {
                            backdrop: 'static',
                            keyboard: false
                        });
                        modal.show();

                        // Fungsi redirect
                        const redirectToDashboard = () => {
                            let redirectUrl = '/';
                            if (typeof currentAuthUser !== 'undefined' && currentAuthUser) {
                                if (currentAuthUser.role === 'member') redirectUrl =
                                    '/member/dashboard';
                                else if (['admin', 'moderator', 'employee'].includes(currentAuthUser
                                        .role)) redirectUrl = 'admin/dashboard';
                            }
                            window.location.href = redirectUrl;
                        };

                        // Tombol Tutup
                        $('#bookingModalCloseBtn').off('click').on('click', function() {
                            modal.hide();
                        });

                        // Event ketika modal tertutup (tombol close x atau backdrop)
                        modalEl.addEventListener('hidden.bs.modal', function() {
                            redirectToDashboard();
                        });

                    },
                    error: function(xhr) {
                        alert('Terjadi kesalahan saat menyimpan booking. Silakan hubungi support.');
                        $("#next-step").prop('disabled', false).html(
                            'Konfirmasi & Bayar <i class="bi bi-check-circle"></i>');
                    }
                });
            }


            function focusToField(selector) {
                const el = document.querySelector(selector);
                if (!el) return;

                el.scrollIntoView({
                    behavior: 'auto', // langsung cepat
                    block: 'center'
                });

                el.classList.add('is-invalid');
                el.focus();
            }

            $(document).ready(function() {
                $('#customer-info-form input, #customer-info-form textarea').on('input', function() {
                    $(this).removeClass('is-invalid');
                });
            });

            function submitBooking() {
                const form = $('#customer-info-form');
                const csrfToken = form.find('input[name="_token"]').val();
                const paymentMethod = $('#payment-method').val();

                // 0️⃣ Validasi data diri
                const name = $('#customer-name').val().trim();
                const email = $('#customer-email').val().trim();
                const phone = $('#customer-phone').val().trim();

                if (!name || name.length < 3) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Nama wajib diisi minimal 3 karakter',
                        confirmButtonColor: '#f59e0b',
                        allowOutsideClick: false,
                        didClose: () => focusToField('#customer-name')
                    });
                    return;
                }

                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!email || !emailRegex.test(email)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Email tidak valid',
                        confirmButtonColor: '#f59e0b',
                        allowOutsideClick: false,
                        didClose: () => focusToField('#customer-email')
                    });
                    return;
                }

                if (!phone || phone.replace(/\D/g, '').length < 8) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Nomor HP wajib diisi',
                        confirmButtonColor: '#f59e0b',
                        allowOutsideClick: false,
                        didClose: () => focusToField('#customer-phone')
                    });
                    return;
                }

                // 0️⃣b Validasi background
                if (bookingState.selectedService?.hasBackgrounds &&
                    (!bookingState.selectedBackground || !bookingState.selectedBackground.id)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Latar belakang belum dipilih',
                        confirmButtonColor: '#f59e0b',
                        allowOutsideClick: false,
                        didClose: () => $('#background-container').get(0)?.scrollIntoView({
                            behavior: 'auto',
                            block: 'center'
                        })
                    });
                    return;
                }

                // 🔒 Validasi syarat & ketentuan
                if (!$('#agree-terms').is(':checked')) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Syarat & Ketentuan',
                        text: 'Silakan centang syarat & ketentuan sebelum melanjutkan booking.',
                        confirmButtonText: 'Mengerti',
                        confirmButtonColor: '#2563eb', // primary
                        allowOutsideClick: false,
                        didClose: () => {
                            // scroll ke checkbox agar user langsung lihat
                            document.querySelector('#agree-terms')
                                ?.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center'
                                });

                            $('#agree-terms').focus();
                        }
                    });
                    return;
                }


                // 1️⃣ Harga dasar layanan
                const basePrice = Number(window.selectedServicePrice || 0);

                // 2️⃣ Normalisasi addons
                const addons = Object.values(window.selectedAddons || {});
                const normalizedAddons = addons.map(addon => {
                    if (!addon?.id) return null;

                    const qty = addon.unit === 'minute' ?
                        1 :
                        (addon.qty || 1);

                    const totalPrice = addon.unit === 'minute' ?
                        addon.price :
                        addon.price * qty;

                    return {
                        id: addon.id,
                        name: addon.name || '',
                        unit: addon.unit || 'item',
                        qty: qty,
                        duration: addon.unit === 'minute' ? (addon.qty || 0) : undefined,
                        price: addon.price || 0,
                        total_price: totalPrice
                    };
                }).filter(a => a !== null);


                // 3️⃣ Total addons
                let addonsTotal = normalizedAddons.reduce((sum, a) => sum + Number(a.total_price || 0), 0);

                // 4️⃣ Total sebelum diskon
                const totalBeforeDiscount = basePrice + addonsTotal;

                // 5️⃣ Diskon
                const discountAmount = Number(window.discountValue || 0);
                const totalAfterDiscount = Math.max(0, totalBeforeDiscount - discountAmount);

                // 6️⃣ DP / Full
                const dpAmount = bookingState.selectedService.dp_amount || Math.round(totalAfterDiscount / 2);
                const paymentAmount = paymentMethod === 'dp' ? dpAmount : totalAfterDiscount;

                // 7️⃣ Hitung jumlah orang
                let additionalPeople = 0;
                addons.forEach(addon => {
                    if (addon.name.toLowerCase().includes('tambahan orang')) additionalPeople += addon.qty;
                });
                const minPeople = Number(window.minPeople || 1);
                const peopleCount = minPeople + additionalPeople;

                // 8️⃣ Format nomor HP
                let rawPhone = phone;
                if (!rawPhone.startsWith('+62')) {
                    rawPhone = rawPhone.replace(/\D/g, '');
                    if (rawPhone.startsWith('0')) rawPhone = rawPhone.substring(1);
                    rawPhone = '+62' + rawPhone;
                }

                // 9️⃣ Payload booking
                const bookingData = {
                    employee_id: bookingState.selectedEmployee.id,
                    service_id: bookingState.selectedService.id,
                    service_title: bookingState.selectedService.title,
                    slot_group_id: bookingState.selectedEmployee.pivot?.slot_group_id || bookingState
                        .selectedEmployee.slot_group_id,
                    background_id: bookingState.selectedBackground?.id || null,
                    name,
                    email,
                    phone: rawPhone,
                    notes: $('#customer-notes').val(),
                    total_amount: totalBeforeDiscount,
                    discount_amount: discountAmount,
                    amount: paymentAmount,
                    dp_amount: dpAmount,
                    payment_type: paymentMethod,
                    people_count: peopleCount,
                    addons: normalizedAddons,
                    coupon_id: $("#coupon_id").val() || null,
                    booking_date: bookingState.selectedDate,
                    booking_start_time: window.selectedStartTime + ':00',
                    booking_end_time: window.selectedEndTime + ':00',
                    _token: csrfToken,
                    service_price: window.selectedServicePrice
                };

                if (typeof currentAuthUser !== 'undefined' && currentAuthUser) {
                    bookingData.user_id = currentAuthUser.id;
                }

                console.log("📦 FINAL BOOKING DATA", bookingData);

                const nextBtn = $("#next-step");
                nextBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm"></span> Memproses...');

                // 1️⃣0️⃣ Gratis / Kupon
                if (totalAfterDiscount <= 0) {
                    bookingData.dp_method = null;
                    bookingData.pelunasan_method = 'coupon';
                    bookingData.payment_status = 'Paid';
                    bookingData.status = 'Confirmed';
                    saveBooking(bookingData);
                    return;
                }

                // 🔐 AUTO CASH UNTUK ADMIN / EMPLOYEE
                if (typeof currentAuthUser !== 'undefined' && currentAuthUser &&
                    (currentAuthUser.role === 'admin' || currentAuthUser.role === 'employee')) {

                    bookingData.status = 'Confirmed';

                    if (paymentMethod === 'dp') {
                        bookingData.dp_method = 'Cash';
                        bookingData.pelunasan_method = null;
                        bookingData.payment_status = 'DP';
                    } else {
                        bookingData.dp_method = null;
                        bookingData.pelunasan_method = 'Cash';
                        bookingData.payment_status = 'Paid';
                    }

                    saveBooking(bookingData);
                    return; // ⛔ STOP supaya tidak lanjut ke Midtrans
                }

                // 1️⃣1️⃣ Midtrans
                bookingData.status = 'Confirmed';

                // 🔥 DEFAULT: anggap belum bayar
                bookingData.payment_status = 'Pending';

                if (paymentMethod === 'dp') {
                    bookingData.dp_method = 'Midtrans';
                    bookingData.pelunasan_method = null;
                } else {
                    bookingData.dp_method = null;
                    bookingData.pelunasan_method = 'Midtrans';
                }

                $.post('/midtrans/token', bookingData)
                    .done(res => {
                        if (!res.token) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal membuat token Midtrans',
                                text: 'Cek konfigurasi sandbox / production.',
                                confirmButtonColor: '#dc2626',
                                allowOutsideClick: false
                            });

                            // 🔥 SAVE sebagai FAILED biar ga hilang
                            bookingData.payment_status = 'Failed';
                            saveBooking(bookingData);

                            nextBtn.prop('disabled', false).text('Konfirmasi & Bayar');
                            return;
                        }

                        snap.pay(res.token, {

                            // ✅ BERHASIL BAYAR
                            onSuccess: r => {
                                bookingData.payment_result = JSON.stringify(r);
                                bookingData.midtrans_order_id = r.order_id || r.transaction_id;

                                bookingData.payment_status = 'Paid';

                                saveBooking(bookingData);
                            },

                            // ⏳ PENDING (QRIS / VA)
                            onPending: r => {
                                bookingData.payment_result = JSON.stringify(r);
                                bookingData.midtrans_order_id = r.order_id || r.transaction_id;

                                bookingData.status = 'Pending';
                                bookingData.payment_status = 'Pending';

                                saveBooking(bookingData);

                                Swal.fire({
                                    icon: 'info',
                                    title: 'Menunggu Pembayaran',
                                    text: 'Silakan selesaikan pembayaran Anda.',
                                    confirmButtonColor: '#2563eb'
                                });
                            },

                            // ❌ ERROR MIDTRANS
                            onError: err => {
                                console.error('Snap error:', err);

                                bookingData.payment_status = 'Failed';
                                saveBooking(bookingData);

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Pembayaran gagal',
                                    confirmButtonColor: '#dc2626'
                                });

                                nextBtn.prop('disabled', false).text('Konfirmasi & Bayar');
                            },

                            // ❌ USER TUTUP POPUP
                            onClose: () => {

                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Pembayaran dibatalkan',
                                    text: 'Booking belum disimpan. Silakan ulangi jika ingin melanjutkan.',
                                    confirmButtonColor: '#f59e0b'
                                });

                                nextBtn.prop('disabled', false).text('Konfirmasi & Bayar');
                            }
                        });
                    })
                    .fail(xhr => {
                        console.error('Token request failed:', xhr.responseJSON || xhr);

                        bookingData.payment_status = 'Failed';
                        saveBooking(bookingData);

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal koneksi ke Midtrans',
                            confirmButtonColor: '#dc2626',
                            allowOutsideClick: false
                        });

                        nextBtn.prop('disabled', false).text('Konfirmasi & Bayar');
                    });
            }

            // Pilih background
            $(document).on('click', '.background-item', function() {
                $('.background-item').removeClass('selected');
                $(this).addClass('selected');

                $('#background-card').removeClass('background-required');

                bookingState.selectedBackground = {
                    id: $(this).data('id'),
                    value: $(this).data('value')
                };

                console.log('🎨 Latar belakang dipilih:', bookingState.selectedBackground);
            });




        });
    </script>

    <script>
        const serviceNameEl = document.getElementById("service-name");
        const servicePriceEl = document.getElementById("service-price");
        const originalPriceEl = document.getElementById("original-price");
        const finalPriceEl = document.getElementById("final-price");
        const discountRow = document.getElementById("discount-row");
        const discountAmountEl = document.getElementById("discount-amount");

        const dpRow = document.getElementById("dp-row");
        const sisaRow = document.getElementById("sisa-row");
        const dpAmountEl = document.getElementById("dp-amount");
        const sisaPaymentEl = document.getElementById("sisa-payment");

        const paymentMethodEl = document.getElementById("payment-method");
        const couponInput = document.getElementById("coupon-code");
        const applyCouponBtn = document.getElementById("apply-coupon");
        const couponSuccessMsg = document.getElementById("coupon-success-msg");
        const couponErrorMsg = document.getElementById("coupon-error-msg");
        const couponIdHidden = document.getElementById("coupon_id");

        const additionalRow = document.getElementById("additional-row");
        const additionalAmountEl = document.getElementById("additional-amount");
        const additionalLabelEl = document.getElementById("additional-label");

        let basePrice = 0;
        window.dpAmount = window.dpAmount || 0;
        window.selectedAddons = window.selectedAddons || {};



        // Addon management (semua addon diperlakukan sama, termasuk unit 'minute')
        function updateAddon(addonId, name, price, qtyChange, unit, maxQty = null) {

            if (!window.selectedAddons) {
                window.selectedAddons = {};
            }

            // Jika addon belum ada, buat entri baru
            if (!window.selectedAddons[addonId]) {

                if (qtyChange < 0) return; // tidak bisa kurangi jika belum ada

                window.selectedAddons[addonId] = {
                    id: addonId,
                    name: name,
                    price: Number(price),
                    unit: unit,
                    qty: 0
                };
            }

            const addon = window.selectedAddons[addonId];

            // Update qty
            addon.qty += qtyChange;

            // Batasi jumlah jika maxQty diberikan
            if (maxQty !== null && maxQty !== "" && maxQty !== "null") {
                const parsedMax = Number(maxQty);
                if (!isNaN(parsedMax) && addon.qty > parsedMax) {
                    addon.qty = parsedMax;
                }
            }

            // Hapus addon jika qty <= 0
            if (addon.qty <= 0) {
                delete window.selectedAddons[addonId];
            }
        }



        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(number);
        }

        const addonRowsEl = document.getElementById("addon-rows");

        function getAddonTotal() {
            return Object.values(window.selectedAddons).reduce((sum, addon) => {
                return sum + (addon.price * addon.qty);
            }, 0);
        }




        function renderAddonRows() {
            addonRowsEl.innerHTML = "";

            if (!window.selectedAddons) {
                window.selectedAddons = {};
            }

            Object.values(window.selectedAddons).forEach(addon => {

                if (!addon || addon.qty <= 0) return;

                let total = addon.price * addon.qty;
                let label = `${addon.name} × ${addon.qty}`; // normal, semua addon sama

                addonRowsEl.insertAdjacentHTML('beforeend', `
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">${label}</span>
                <span>${formatRupiah(total)}</span>
            </div>
        `);

            });
        }



        // Event listener untuk semua tombol plus/minus addon
        document.addEventListener("click", function(e) {

            const btn = e.target.closest(".addon-increase, .addon-decrease");
            if (!btn) return;

            const card = btn.closest(".addon-item");
            if (!card) return;

            const addonId = card.dataset.addonId;
            const name = card.dataset.addonName;
            const price = Number(card.dataset.addonPrice);
            const unit = card.dataset.addonUnit;
            let max = card.dataset.addonMax;

            if (max === "dynamic_people") {
                max = Number(window.peopleCount || serviceMaxPeopleFallback());
            } else {
                max = max ? Number(max) : null;
            }

            const qtyEl = card.querySelector(".addon-qty");
            const inputEl = card.querySelector(".addon-input");

            if (!window.selectedAddons) {
                window.selectedAddons = {};
            }

            const change = btn.classList.contains("addon-increase") ? 1 : -1;

            // Jika addon belum ada, buat entri baru
            if (!window.selectedAddons[addonId]) {
                if (change < 0) return;

                window.selectedAddons[addonId] = {
                    id: addonId,
                    name: name,
                    price: price,
                    unit: unit,
                    qty: 0
                };
            }

            const addon = window.selectedAddons[addonId];
            addon.qty += change;

            // Batasi maxQty jika ada
            if (max && addon.qty > max) {
                addon.qty = max;
            }

            // Hapus addon jika qty <= 0
            if (addon.qty <= 0) {
                delete window.selectedAddons[addonId];
            }

            const updatedAddon = window.selectedAddons[addonId];

            // Update UI qty
            if (!updatedAddon) {
                qtyEl.textContent = "0";
                inputEl.value = 0;
            } else {
                qtyEl.textContent = updatedAddon.qty;
                inputEl.value = updatedAddon.qty;
            }

            renderAddonRows();
            updatePeopleSummary();
            updatePaymentSummary();
        });

        function serviceMaxPeopleFallback() {
            return bookingState?.selectedService?.max_people ?? 0;
        }

        function updatePaymentSummary(dynamicTotal = null) {
            if (!window.selectedServicePrice) return;

            const servicePrice = Number(window.selectedServicePrice);
            const appliedDiscount = Number(window.discountValue || 0);

            // Total addon termasuk Tambahan Orang
            const addonTotal = getAddonTotal();

            const subtotal = (dynamicTotal !== null ? Number(dynamicTotal) : servicePrice) + addonTotal;

            const totalAfterDiscount = Math.max(0, subtotal - appliedDiscount);

            // Layanan
            if (serviceNameEl && servicePriceEl) {
                serviceNameEl.textContent = window.selectedServiceTitle || "-";
                servicePriceEl.textContent = formatRupiah(servicePrice);
            }

            // Render semua addon
            renderAddonRows();

            // Subtotal (hanya jika ada diskon)
            if (appliedDiscount > 0) {
                originalPriceEl.textContent = formatRupiah(subtotal);
                originalPriceEl.parentElement.classList.remove("d-none");
            } else {
                originalPriceEl.parentElement.classList.add("d-none");
            }

            // Diskon
            if (appliedDiscount > 0) {
                discountRow.classList.remove("d-none");
                discountAmountEl.textContent = `- ${formatRupiah(appliedDiscount)}`;
            } else {
                discountRow.classList.add("d-none");
            }

            // Total
            finalPriceEl.textContent = formatRupiah(totalAfterDiscount);
            finalPriceEl.parentElement.classList.remove("d-none");

            // DP logic tetap sama
            // =========================
            // DP SAFE LOGIC (FIXED)
            // =========================

            const dpOption = paymentMethodEl?.querySelector('option[value="dp"]');

            // pastikan dpValue SELALU valid number
            const dpValue = Number.isFinite(Number(window.dpAmount)) ?
                Number(window.dpAmount) :
                Math.round(totalAfterDiscount / 2);

            // validasi DP tersedia atau tidak (lebih aman)
            const hasDp = dpValue > 0;

            // kalau tidak ada DP → disable option + paksa text
            if (dpOption) {
                dpOption.disabled = !hasDp;
                dpOption.textContent = hasDp ?
                    "Bayar Uang Muka (DP)" :
                    "Bayar Uang Muka (Tidak tersedia)";
            }

            // kalau user sudah pilih dp tapi tidak valid → paksa cash
            if (!hasDp && paymentMethodEl?.value === "dp") {
                paymentMethodEl.value = "cash";
            }

            // render UI DP hanya kalau valid DAN dipilih
            const isDpSelected = paymentMethodEl?.value === "dp";

            if (hasDp && isDpSelected) {
                const sisa = Math.max(0, totalAfterDiscount - dpValue);

                dpRow.classList.remove("d-none");
                sisaRow.classList.remove("d-none");

                dpAmountEl.textContent = formatRupiah(dpValue);
                sisaPaymentEl.textContent = formatRupiah(sisa);
            } else {
                dpRow.classList.add("d-none");
                sisaRow.classList.add("d-none");
            }
        }



        // ✨ SweetAlert2 Premium Config (dengan animasi + dark mode + tampilan elegan)
        const SwalPremium = Swal.mixin({
            customClass: {
                popup: 'rounded-4 shadow-lg p-4 animate__animated animate__fadeInDown',
                title: 'fw-semibold text-capitalize', // tidak uppercase semua
                confirmButton: 'btn btn-primary px-4 rounded-pill shadow-sm',
                cancelButton: 'btn btn-outline-secondary px-4 rounded-pill ms-2'
            },
            buttonsStyling: false,
            background: window.matchMedia('(prefers-color-scheme: dark)').matches ? '#121212' : '#ffffff',
            color: window.matchMedia('(prefers-color-scheme: dark)').matches ? '#e0e0e0' : '#333',
            showClass: {
                popup: 'animate__animated animate__fadeInDown'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp'
            }
        });

        // ✅ Fungsi untuk menerapkan kupon
        async function applyCoupon() {

            const codeInput = couponInput?.value?.trim();

            /**
             * =====================================================
             * 🚫 Kupon kosong
             * =====================================================
             */
            if (!codeInput) {
                return SwalPremium.fire({
                    icon: 'warning',
                    title: 'Kode kupon kosong',
                    text: 'Silakan masukkan kode kupon terlebih dahulu.',
                    confirmButtonText: 'Mengerti'
                });
            }

            /**
             * =====================================================
             * 🔒 Cek login (ALLOW ADMIN & EMPLOYEE)
             * =====================================================
             */
            const isUser = typeof currentAuthUser !== "undefined" && currentAuthUser;
            const isAdmin = typeof isAdminUser !== "undefined" && isAdminUser;
            const isEmployee = typeof isEmployeeUser !== "undefined" && isEmployeeUser;

            if (!isUser && !isAdmin && !isEmployee) {
                return SwalPremium.fire({
                    icon: 'info',
                    title: 'Khusus member',
                    html: `
                <p class="mb-3">Gunakan kupon hanya untuk <b>member terdaftar</b>.</p>
                <div class="d-flex justify-content-center gap-2 mt-3">
                    <a href="/login" class="btn btn-primary px-4 rounded-pill shadow-sm">Masuk</a>
                    <a href="/register" class="btn btn-outline-secondary px-4 rounded-pill shadow-sm">Daftar</a>
                </div>
            `,
                    showConfirmButton: false,
                    allowOutsideClick: true
                });
            }

            /**
             * =====================================================
             * 🔎 AMBIL SERVICE ID (TANPA VALIDASI SWAL)
             * =====================================================
             */
            const serviceId =
                bookingState?.selectedService?.id ||
                window.selectedServiceId ||
                window.bookingData?.service_id ||
                null;

            /**
             * =====================================================
             * 🧮 HITUNG SUBTOTAL
             * =====================================================
             */
            const servicePrice = Number(window.selectedServicePrice || 0);

            const addonTotal =
                typeof getAddonTotal === "function" ?
                Number(getAddonTotal()) :
                0;

            const subtotalBeforeDiscount = servicePrice + addonTotal;

            // reset message (AMAN DARI NULL)
            if (couponSuccessMsg) couponSuccessMsg.classList.add("d-none");
            if (couponErrorMsg) couponErrorMsg.classList.add("d-none");

            console.log("COUPON DEBUG", {
                code: codeInput,
                service_id: serviceId,
                subtotal: subtotalBeforeDiscount
            });

            try {

                const response = await fetch('/validate-coupon', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute('content')
                    },
                    body: JSON.stringify({
                        code: codeInput,
                        service_id: Number(serviceId),
                        subtotal: subtotalBeforeDiscount
                    })
                });

                const data = await response.json();

                if (!response.ok || !data.id) {
                    throw new Error(data.message || "Kupon tidak valid.");
                }

                /**
                 * =====================================================
                 * 🎟️ HITUNG DISKON
                 * =====================================================
                 */
                let discountValue = 0;
                const couponValue = Number(data.value);

                if (data.type === "percentage") {
                    discountValue = Math.round(
                        subtotalBeforeDiscount * (couponValue / 100)
                    );
                } else if (data.type === "fixed") {
                    discountValue = Math.min(
                        couponValue,
                        subtotalBeforeDiscount
                    );
                }

                // simpan global
                window.discountValue = discountValue;
                if (couponIdHidden) couponIdHidden.value = data.id;

                /**
                 * =====================================================
                 * ✅ SUCCESS MESSAGE (FIX UTAMA)
                 * =====================================================
                 */
                if (couponSuccessMsg) {
                    couponSuccessMsg.innerHTML = `
                <i class="fas fa-check-circle me-1 text-success"></i>
                Kupon <strong>${data.code}</strong> berhasil diterapkan
                (${data.type === 'percentage'
                    ? data.value + '%'
                    : formatRupiah(data.value)})
            `;
                    couponSuccessMsg.classList.remove("d-none");
                }

                if (couponErrorMsg) {
                    couponErrorMsg.classList.add("d-none");
                }

                /**
                 * =====================================================
                 * 🔄 UPDATE PAYMENT SUMMARY
                 * =====================================================
                 */
                if (typeof updatePaymentSummary === "function") {
                    updatePaymentSummary();
                }

                /**
                 * =====================================================
                 * 💳 VALIDASI DP
                 * =====================================================
                 */
                const totalAfterDiscount =
                    Math.max(0, subtotalBeforeDiscount - discountValue);

                const dpValue =
                    window.dpAmount ||
                    Math.round(totalAfterDiscount / 2);

                if (
                    paymentMethodEl?.value === "dp" &&
                    totalAfterDiscount <= dpValue
                ) {
                    paymentMethodEl.value = "cash";
                }

                /**
                 * =====================================================
                 * 🎉 ALERT BERHASIL
                 * =====================================================
                 */
                SwalPremium.fire({
                    icon: 'success',
                    title: 'Kupon berhasil diterapkan',
                    confirmButtonText: 'Lanjutkan',
                });

            } catch (error) {

                /**
                 * =====================================================
                 * ❌ RESET KUPON
                 * =====================================================
                 */
                window.discountValue = 0;
                if (couponIdHidden) couponIdHidden.value = "";

                if (couponErrorMsg) {
                    couponErrorMsg.textContent =
                        error.message || "Kupon tidak valid.";
                    couponErrorMsg.classList.remove("d-none");
                }

                if (typeof updatePaymentSummary === "function") {
                    updatePaymentSummary();
                }

                SwalPremium.fire({
                    icon: 'error',
                    title: 'Kupon gagal diterapkan',
                    text: error.message ||
                        'Kode kupon tidak valid atau sudah kedaluwarsa.',
                    confirmButtonText: 'Mengerti',
                    iconColor: '#ef4444'
                });
            }
        }

        /**
         * =====================================================
         * 🚀 EVENT
         * =====================================================
         */
        if (applyCouponBtn) {
            applyCouponBtn.addEventListener("click", applyCoupon);
        }



        function updatePeopleSummary() {
            const minPeople = Number(window.minPeople || 1);
            let additionalPeople = 0;

            // Hitung dari addon "Tambahan Orang" saja
            Object.values(window.selectedAddons).forEach(addon => {
                if (addon.name.toLowerCase().includes('tambahan orang')) {
                    additionalPeople += addon.qty;
                }
            });

            // Total orang = paket + tambahan orang
            const totalPeople = minPeople + additionalPeople;

            // Update ringkasan pemesanan
            const summaryPeopleEl = document.getElementById('summary-people');
            if (summaryPeopleEl) summaryPeopleEl.textContent = totalPeople;

            // Update global peopleCount
            window.peopleCount = totalPeople;
        }



        document.addEventListener("DOMContentLoaded", () => {
            basePrice = window.selectedServicePrice || 0;
            updatePeopleSummary();
            updatePaymentSummary();

        });

        if (paymentMethodEl) {
            paymentMethodEl.addEventListener('change', () => {
                discountValue = 0;
                window.discountValue = 0;
                couponInput.value = "";
                couponIdHidden.value = "";
                couponSuccessMsg.classList.add('d-none');
                couponErrorMsg.classList.add('d-none');
                updatePeopleSummary();
                updatePaymentSummary();
            });
        }

        const prevStepBtn = document.getElementById('prev-step');
        if (prevStepBtn) {
            prevStepBtn.addEventListener('click', () => {

                window.selectedAddons = {};
                discountValue = 0;
                window.discountValue = 0;
                couponInput.value = "";
                couponIdHidden.value = "";
                couponSuccessMsg.classList.add('d-none');
                couponErrorMsg.classList.add('d-none');
                updatePeopleSummary();
                updatePaymentSummary();
                refreshDynamicAddons();
            });
        }

        function refreshDynamicAddons() {
            document.querySelectorAll('[data-addon-max="dynamic_people"]').forEach(el => {
                el.dataset.addonMax = window.peopleCount || serviceMaxPeopleFallback();
            });
        }
    </script>











@endsection
