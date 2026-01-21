@extends('layouts.app')

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
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-grey border-bottom">
                            <h5 class="mb-0 fw-semibold text-center">
                                <i class="fa-solid fa-clipboard-check me-2"></i> Ringkasan Pemesanan
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <!-- ITEM REUSABLE COMPONENT -->

                                <div class="row mb-3 align-items-center summary-row">
                                    <div class="col-4 text-muted d-flex align-items-center">
                                        <i class="fa fa-map-marker-alt me-2 summary-icon"></i>
                                        <span class="summary-label">Studio :</span>
                                    </div>
                                    <div class="col-8 fw-medium" id="summary-employee">-</div>
                                </div>

                                <div class="row mb-3 align-items-center summary-row">
                                    <div class="col-4 text-muted d-flex align-items-center">
                                        <i class="fa fa-table-cells-large me-2 summary-icon"></i>
                                        <span class="summary-label">Kategori :</span>
                                    </div>
                                    <div class="col-8 fw-medium" id="summary-category">-</div>
                                </div>

                                <div class="row mb-3 align-items-center summary-row">
                                    <div class="col-4 text-muted d-flex align-items-center">
                                        <i class="fa fa-concierge-bell me-2 summary-icon"></i>
                                        <span class="summary-label">Layanan :</span>
                                    </div>
                                    <div class="col-8 fw-medium" id="summary-service">-</div>
                                </div>

                                <div class="row mb-3 align-items-center">
                                    <div class="col-4 text-muted d-flex align-items-center">
                                        <i class="fa-solid fa-user-group me-2 summary-icon"></i>
                                        <span class="summary-label">Jumlah Orang :</span>
                                    </div>
                                    <div class="col-8 fw-medium" id="summary-people">-</div>
                                </div>

                                <div class="row mb-3 align-items-center summary-row">
                                    <div class="col-4 text-muted d-flex align-items-center">
                                        <i class="fa fa-calendar-alt me-2 summary-icon"></i>
                                        <span class="summary-label">Tanggal :</span>
                                    </div>
                                    <div class="col-8 fw-medium" id="summary-date">-</div>
                                </div>

                                <div class="row mb-3 align-items-center summary-row">
                                    <div class="col-4 text-muted d-flex align-items-center">
                                        <i class="fa fa-clock me-2 summary-icon"></i>
                                        <span class="summary-label">Waktu :</span>
                                    </div>
                                    <div class="col-8 fw-medium" id="summary-time">-</div>
                                </div>

                                <div class="row mb-3 align-items-center summary-row">
                                    <div class="col-4 text-muted d-flex align-items-center">
                                        <i class="fa fa-hourglass-half me-2 summary-icon"></i>
                                        <span class="summary-label">Durasi :</span>
                                    </div>
                                    <div class="col-8 fw-medium" id="summary-duration">-</div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- PILIH BACKGROUND -->
                    <div class="card shadow-sm border-0 mb-4 d-none" id="background-card">
                        <div class="card-header bg-grey border-bottom">
                            <h5 class="mb-0 fw-semibold text-center">
                                <i class="fa-solid fa-palette me-2"></i> Pilih Background
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
                                    <option value="cash">Bayar Cash</option>
                                    <option value="dp">Bayar DP</option>
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

                                <!-- TAMBAHAN ORANG -->
                                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap d-none"
                                    id="additional-row">
                                    <span class="text-muted" id="additional-label">Tambahan Orang</span>
                                    <span class="text-end" id="additional-amount">Rp0</span>
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
                </div> <!-- end Step 5 -->
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
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Booking Dikonfirmasi!</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                    <h4 class="mt-3">Terima Kasih!</h4>
                    <p>Pemesanan anda telah berhasil di booking.</p>
                    <div class="alert alert-info mt-3">
                        <p class="mb-0">Email konfirmasi dan pesan WhatsApp telah dikirim ke alamat email Anda.</p>
                    </div>
                    <div class="booking-details mt-4 text-start">
                        <h5>Booking Detail:</h5>
                        <div id="modal-booking-details"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>



    <script>
        $(document).ready(function() {
            let currentMonth = new Date().getMonth();
            let currentYear = new Date().getFullYear();


            const categories = @json($categories);

            // 🔥 FIX: GLOBAL EMPLOYEES
            let employees = [];

            let bookingState = {
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

                        employees = res.employees || [];

                        employees.forEach(employee => {
                            const studioName = employee.user?.name ?? 'Studio';

                            $("#employees-container").append(`
    <div class="col animate-slide-in">
        <div class="card border h-100 employee-card text-center p-2"
             data-employee="${employee.id}">
            <div class="card-body">
                <img src="/assets/img/studio.png"
                     class="img-fluid mb-2 rounded"
                     style="max-height:120px">
                <h5>${employee.user?.name ?? 'Studio'}</h5>
                <p class="text-muted small">Studio</p>
            </div>
        </div>
    </div>
`);

                        });
                    }
                });
            }

            $(document).on("click", ".employee-card", function() {
                $(".employee-card").removeClass("selected");
                $(this).addClass("selected");

                const employeeId = $(this).data("employee");
                bookingState.selectedEmployee = employees.find(e => e.id == employeeId);

                if (!bookingState.selectedEmployee) return;

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

                // Loading
                container.html(`
        <div class="d-flex justify-content-center align-items-center" style="height: 100px;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Memuat...</span>
            </div>
        </div>
    `);

                $.get(`/services/${serviceId}/addons`)
                    .done(function(res) {
                        container.empty();

                        // 👉 JIKA TIDAK ADA ADDON → KOSONGKAN & STOP
                        if (!res.addons || res.addons.length === 0) {
                            return;
                        }

                        /* ===== JUDUL LAYANAN TAMBAHAN ===== */
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

                        /* ===== LIST ADDON ===== */
                        res.addons.forEach(a => {
                            container.append(`
                    <div class="card shadow-sm border-0 mb-3 addon-item"
                         data-addon-id="${a.id}"
                         data-addon-name="${a.name}"
                         data-addon-price="${a.price}">

                        <div class="card-header bg-grey border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-plus me-2"></i> ${a.name}
                            </h6>
                        </div>

                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="fw-medium fs-6 text-muted">
                                    Rp ${a.price}
                                </span>

                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn-modern addon-decrease">
                                        <i class="bi bi-dash-lg"></i>
                                    </button>

                                    <span class="addon-qty fw-medium fs-5 user-select-none text-center"
                                          style="min-width: 25px;">0</span>

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
                    .fail(function() {
                        container.empty(); // juga bersih
                    });
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

                    // ✅ LOAD BACKGROUND BERDASARKAN LAYANAN
                    if (bookingState?.selectedService?.id) {
                        loadServiceBackgrounds(bookingState.selectedService.id);
                    }
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

                const employeeId = bookingState.selectedEmployee.id;

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

                        if (!res.services || res.services.length === 0) {
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

                        res.services.forEach(service => {
                            $("#services-container").append(`
    <div class="col animate-slide-in">
        <div class="card border h-100 service-card text-center p-2"
             data-service="${service.id}"
             data-price="${service.price}"
             data-max-people="${service.max_people}"
             data-dp-amount="${service.dp_amount}"
             data-min-people="${service.min_people}"
            data-extra-price="${service.extra_price_per_person}">
            <div class="card-body">
                <img src="/assets/img/service.png"
                     class="img-fluid mb-2 rounded"
                     style="max-height:120px">
                <h5 class="card-title">${service.title}</h5>
                <p class="text-muted small">
                    Max ${service.max_people} orang
                </p>
                <p class="fw-bold">
                    Rp ${Number(service.price).toLocaleString('id-ID')}
                </p>
            </div>
        </div>
    </div>
`);


                        });
                    },
                    error: function() {
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

                // Spinner loading
                $("#time-slots-container").html(`
        <div class="text-center w-100 py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Memuat...</span>
            </div>
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
                        <button class="btn btn-sm btn-outline-primary mt-2" onclick="updateCalendar()">
                            <i class="bi bi-arrow-left me-1"></i> Kembali ke kalender
                        </button>
                    </div>
                `);
                            return;
                        }

                        const sessionDuration = slots[0]?.slot_duration ?? response.slot_duration ?? 60;
                        const breakDuration = slots[0]?.break_duration ?? response.break_duration ?? 0;
                        bookingState.selectedEmployee.sessionDuration = sessionDuration;
                        bookingState.selectedEmployee.breakDuration = breakDuration;
                        bookingState.selectedEmployee.slot_group_id = response
                            .slot_group_id; // ✅ simpan slot group


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

                            const slotElement = $(`
                    <div class="time-slot btn btn-outline-primary mb-2 ${slot.is_booked ? 'disabled' : ''}"
                        data-start="${slot.start}"
                        data-end="${slot.end}"
                        data-time="${displayText}"
                        title="${displayText}">
                        <i class="bi bi-clock me-1"></i> ${slot.start}
                    </div>
                `);

                            if (!slot.is_booked) {
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

                                // Highlight slot yang sudah dipilih sebelumnya
                                if (bookingState.selectedTime?.start === slot.start) {
                                    slotElement.addClass("selected active");
                                    window.selectedStartTime = slot.start;
                                    window.selectedEndTime = slot.end;
                                    window.selectedTimeDisplay = displayText;
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
                    <button class="btn btn-sm btn-outline-primary mt-2" onclick="updateTimeSlots('${selectedDate}')">
                        <i class="bi bi-arrow-repeat me-1"></i> Coba lagi
                    </button>
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

                    if (!res.backgrounds || !res.backgrounds.length) {
                        card.addClass('d-none');
                        return;
                    }

                    card.removeClass('d-none');

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
                        const bookingId = res.appointment && res.appointment.booking_id ? res
                            .appointment.booking_id : '-';

                        // 🌗 Deteksi dark mode otomatis
                        const darkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
                        const bgColor = darkMode ? '#0f172a' : '#ffffff';
                        const textColor = darkMode ? '#e2e8f0' : '#1f2937';
                        const cardBg = darkMode ? '#1e293b' : '#f9fafb';
                        const shadow = darkMode ?
                            '0 8px 24px rgba(0, 0, 0, 0.6)' :
                            '0 6px 20px rgba(0, 0, 0, 0.15)';

                        Swal.fire({
                            icon: 'success',
                            title: '<div style="font-size:1.4rem;font-weight:700;">Booking Berhasil</div>',
                            html: `
                    <div style="text-align:left;line-height:1.6;font-size:0.92rem;margin-top:8px;">
                        <p>Terima kasih <b>${data.name}</b>! Pemesanan Anda telah <b>dikonfirmasi</b>.</p>
                        <div style="
                            background:${cardBg};
                            border-radius:14px;
                            padding:14px 16px;
                            margin-top:10px;
                            box-shadow: inset 0 0 0 1px rgba(0,0,0,0.05);
                        ">
                            <p style="margin:0;"><b>ID Booking        :</b> ${bookingId}</p>
                            <p style="margin:0;"><b>Layanan            :</b> ${data.service_title}</p>
                            <p style="margin:0;"><b>Tanggal             :</b> ${data.booking_date}</p>
                            <p style="margin:0;"><b>Waktu               :</b> ${data.booking_start_time?.slice(0,5)} – ${data.booking_end_time?.slice(0,5)} WIB</p>
                            <p style="margin:0;"><b>Jumlah Orang   :</b> ${data.people_count || 1}</p>
                            <p style="margin:0;"><b>Status Pembayaran :</b> ${data.payment_status}</p>
                            <p style="margin:0;"><b>Total                :</b> Rp ${parseInt(data.total_amount).toLocaleString('id-ID')}</p>
                        </div>
                        <p style="margin-top:12px;font-size:0.85rem;opacity:0.85;">
                            Detail booking telah dikirim ke email dan WhatsApp Anda 
                        </p>
                    </div>
                `,
                            confirmButtonText: 'Tutup',
                            confirmButtonColor: '#2563eb',
                            background: bgColor,
                            color: textColor,
                            width: '420px',
                            padding: '1.3rem 1rem 1.4rem',
                            allowOutsideClick: false,
                            backdrop: `
                    rgba(0,0,0,0.5)
                    url("https://cdn.jsdelivr.net/gh/saadeghi/files@main/balloons.gif")
                    center top
                    no-repeat
                `,
                            customClass: {
                                popup: 'swal-premium-popup',
                                title: 'swal-premium-title',
                                confirmButton: 'swal-premium-button'
                            },
                            didOpen: (popup) => {
                                popup.style.boxShadow = shadow;
                                popup.style.borderRadius = '20px';
                            }
                        }).then(() => {
                            // 🚀 Redirect setelah tombol "Tutup" diklik
                            let redirectUrl = '/'; // default guest ke homepage

                            if (typeof currentAuthUser !== 'undefined' && currentAuthUser) {
                                if (currentAuthUser.role === 'member') {
                                    redirectUrl = '/member/dashboard';
                                } else if (
                                    currentAuthUser.role === 'admin' ||
                                    currentAuthUser.role === 'moderator' ||
                                    currentAuthUser.role === 'employee'
                                ) {
                                    redirectUrl = '/dashboard';
                                }
                            }

                            window.location.href = redirectUrl;
                        });
                    },
                    error: function(xhr) {
                        console.error(xhr.responseJSON);

                        const darkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
                        const bgColor = darkMode ? '#0f172a' : '#ffffff';
                        const textColor = darkMode ? '#e2e8f0' : '#1f2937';

                        Swal.fire({
                            icon: 'error',
                            title: '<div style="font-size:1.3rem;font-weight:700;">Booking Gagal</div>',
                            text: 'Terjadi kesalahan saat menyimpan booking. Silakan hubungi support.',
                            confirmButtonText: 'Tutup',
                            confirmButtonColor: '#dc2626',
                            background: bgColor,
                            color: textColor,
                            width: '400px',
                            padding: '1.2rem 1rem 1.4rem',
                            allowOutsideClick: false
                        });

                        $("#next-step").prop('disabled', false).html(
                            'Konfirmasi & Bayar <i class="bi bi-check-circle"></i>');
                    }
                });
            }

            function focusToField(selector) {
                const el = document.querySelector(selector);
                if (!el) return;

                el.scrollIntoView({
                    behavior: 'auto', // ⬅️ LANGSUNG CEPAT
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

                // ===============================
                // 0️⃣ VALIDASI DATA DIRI (WAJIB)
                // ===============================
                const name = $('#customer-name').val().trim();
                const email = $('#customer-email').val().trim();
                const phone = $('#customer-phone').val().trim();

                if (!name || name.length < 3) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data Belum Lengkap',
                        text: 'Nama wajib diisi minimal 3 karakter.',
                        confirmButtonColor: '#f59e0b',
                        allowOutsideClick: false,
                        didClose: () => {
                            focusToField('#customer-name');
                        }
                    });
                    return; // ⛔ STOP TOTAL
                }

                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!email || !emailRegex.test(email)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Email Tidak Valid',
                        text: 'Masukkan alamat email yang valid.',
                        confirmButtonColor: '#f59e0b',
                        allowOutsideClick: false,
                        didClose: () => {
                            focusToField('#customer-email');
                        }
                    });
                    return; // ⛔ STOP TOTAL
                }

                if (!phone || phone.replace(/\D/g, '').length < 8) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Nomor HP / WhatsApp Tidak Valid',
                        text: 'Nomor HP / WhatsApp wajib diisi.',
                        confirmButtonColor: '#f59e0b',
                        allowOutsideClick: false,
                        didClose: () => {
                            focusToField('#customer-phone');
                        }
                    });
                    return; // ⛔ STOP TOTAL
                }


                // ===============================
                // 1️⃣ HARGA DASAR LAYANAN
                // ===============================
                const basePrice = Number(window.selectedServicePrice || 0);

                // ===============================
                // 2️⃣ NORMALISASI ADDONS (OBJECT → ARRAY)
                // ===============================
                const addons = Object.values(window.selectedAddons || {});

                // ===============================
                // 3️⃣ TOTAL ADDONS
                // ===============================
                let addonsTotal = 0;
                addons.forEach(addon => {
                    addonsTotal += Number(addon.price || 0) * Number(addon.qty || 0);
                });

                // ===============================
                // 4️⃣ TOTAL SEBELUM DISKON
                // ===============================
                const totalBeforeDiscount = basePrice + addonsTotal;

                // ===============================
                // 5️⃣ DISKON
                // ===============================
                const discountAmount = Number(window.discountValue || 0);
                const totalAfterDiscount = Math.max(0, totalBeforeDiscount - discountAmount);

                // ===============================
                // 6️⃣ DP / FULL
                // ===============================
                const dpAmount =
                    bookingState.selectedService.dp_amount ||
                    Math.round(totalAfterDiscount / 2);

                const paymentAmount =
                    paymentMethod === 'dp' ? dpAmount : totalAfterDiscount;

                // ===============================
                // 7️⃣ HITUNG JUMLAH ORANG (DARI ADDON)
                // ===============================
                let additionalPeople = 0;
                addons.forEach(addon => {
                    if (addon.name.toLowerCase().includes('tambahan orang')) {
                        additionalPeople += addon.qty;
                    }
                });

                const minPeople = Number(window.minPeople || 1);
                const peopleCount = minPeople + additionalPeople;

                // ===============================
                // 8️⃣ FORMAT NOMOR HP
                // ===============================
                let rawPhone = $('#customer-phone').val().trim();
                if (!rawPhone.startsWith('+62')) {
                    rawPhone = rawPhone.replace(/\D/g, '');
                    if (rawPhone.startsWith('0')) rawPhone = rawPhone.substring(1);
                    rawPhone = '+62' + rawPhone;
                }

                // ===============================
                // 9️⃣ PAYLOAD BOOKING
                // ===============================
                const bookingData = {
                    employee_id: bookingState.selectedEmployee.id,
                    service_id: bookingState.selectedService.id,
                    service_title: bookingState.selectedService.title,
                    slot_group_id: bookingState.selectedEmployee.slot_group_id,

                    background_id: bookingState.selectedBackground?.id || null,

                    name: $('#customer-name').val(),
                    email: $('#customer-email').val(),
                    phone: rawPhone,
                    notes: $('#customer-notes').val(),

                    total_amount: totalBeforeDiscount,
                    discount_amount: discountAmount,
                    amount: paymentAmount,
                    dp_amount: dpAmount,
                    payment_type: paymentMethod,

                    people_count: peopleCount,

                    addons: addons, // 🔥 SUMBER KEBENARAN

                    coupon_id: $("#coupon_id").val() || null,
                    booking_date: bookingState.selectedDate,
                    booking_start_time: window.selectedStartTime + ':00',
                    booking_end_time: window.selectedEndTime + ':00',

                    _token: csrfToken
                };

                bookingData.service_price = window.selectedServicePrice;
                bookingData.addons = Array.isArray(addons) ? addons : [];


                if (typeof currentAuthUser !== 'undefined' && currentAuthUser) {
                    bookingData.user_id = currentAuthUser.id;
                }

                console.log("📦 FINAL BOOKING DATA", bookingData);

                const nextBtn = $("#next-step");
                nextBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm"></span> Memproses...'
                );

                // ===============================
                // 1️⃣0️⃣ GRATIS
                // ===============================
                if (totalAfterDiscount <= 0) {
                    bookingData.payment_method = 'Gratis / Kupon';
                    bookingData.payment_status = 'Paid';
                    bookingData.status = 'Confirmed';
                    saveBooking(bookingData);
                    return;
                }

                // ===============================
                // 1️⃣1️⃣ MIDTRANS
                // ===============================
                bookingData.payment_method = 'Midtrans';
                bookingData.payment_status = paymentMethod === 'dp' ? 'DP' : 'Paid';
                bookingData.status = 'Confirmed';

                $.post('/midtrans/token', bookingData)
                    .done(res => {
                        snap.pay(res.token, {
                            onSuccess: r => {
                                bookingData.payment_result = JSON.stringify(r);
                                bookingData.midtrans_order_id = r.order_id || r.transaction_id;
                                saveBooking(bookingData);
                            },
                            onPending: r => {
                                bookingData.payment_result = JSON.stringify(r);
                                bookingData.midtrans_order_id = r.order_id || r.transaction_id;
                                bookingData.status = 'Processing';
                                bookingData.payment_status = 'Pending';
                                saveBooking(bookingData);
                            },
                            onError: () => nextBtn.prop('disabled', false).text('Konfirmasi & Bayar'),
                            onClose: () => nextBtn.prop('disabled', false).text('Konfirmasi & Bayar')
                        });
                    })
                    .fail(() => {
                        nextBtn.prop('disabled', false).text('Konfirmasi & Bayar');
                    });
            }

            // 🎨 Pilih background
            $(document).on('click', '.background-item', function() {
                $('.background-item').removeClass('selected');
                $(this).addClass('selected');

                bookingState.selectedBackground = {
                    id: $(this).data('id'),
                    value: $(this).data('value')
                };

                console.log('🎨 Background dipilih:', bookingState.selectedBackground);
            });


        });
    </script>

    @if ($setting->footer)
        {!! $setting->footer !!}
    @endif

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



        // Addon management
        function updateAddon(addonId, name, price, qtyChange, serviceId = null) {
            if (serviceId && window.selectedServiceId !== serviceId) return;

            if (!window.selectedAddons[addonId]) {
                window.selectedAddons[addonId] = {
                    id: addonId,
                    name: name,
                    price: Number(price),
                    qty: 0
                };
            }

            window.selectedAddons[addonId].qty += qtyChange;
            if (window.selectedAddons[addonId].qty <= 0) delete window.selectedAddons[addonId];

            updatePeopleSummary();
            updatePaymentSummary();
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
            return Object.values(window.selectedAddons).reduce((sum, addon) => sum + addon.price * addon.qty, 0);
        }

        function renderAddonRows() {
            if (!addonRowsEl) return;

            addonRowsEl.innerHTML = "";

            Object.values(window.selectedAddons).forEach(addon => {
                const row = document.createElement("div");
                row.className = "d-flex justify-content-between align-items-center mb-2 flex-wrap";

                row.innerHTML = `
            <span class="text-muted">${addon.name} × ${addon.qty}</span>
            <span class="text-end">${formatRupiah(addon.price * addon.qty)}</span>
        `;

                addonRowsEl.appendChild(row);
            });
        }

        document.addEventListener("click", function(e) {
            const btn = e.target.closest(".addon-increase, .addon-decrease");
            if (!btn) return;

            const card = btn.closest(".addon-item");
            if (!card) return;

            const addonId = card.dataset.addonId;
            const name = card.dataset.addonName;
            const price = Number(card.dataset.addonPrice);

            const qtyEl = card.querySelector(".addon-qty");
            const inputEl = card.querySelector(".addon-input");

            let qty = Number(qtyEl.textContent || 0);

            if (btn.classList.contains("addon-increase")) {
                qty++;
                updateAddon(addonId, name, price, 1, card.dataset.serviceId);
            } else {
                if (qty <= 0) return;
                qty--;
                updateAddon(addonId, name, price, -1, card.dataset.serviceId);
            }

            qtyEl.textContent = qty;
            inputEl.value = qty;

            // 🔥 Ganti updatePeopleCountDisplay dengan updatePeopleSummary
            updatePeopleSummary();
            updatePaymentSummary();
        });


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
            const dpOption = paymentMethodEl?.querySelector('option[value="dp"]');
            const dpValue = window.dpAmount || Math.round(totalAfterDiscount / 2);

            if (dpOption) dpOption.disabled = totalAfterDiscount <= dpValue;

            if (paymentMethodEl?.value === "dp") {
                const sisa = totalAfterDiscount - dpValue;
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

        // ✅ Fungsi untuk menerapkan kupon (FINAL & SAFE)
        async function applyCoupon() {
            const codeInput = couponInput.value.trim();

            // 🚫 Kupon kosong
            if (!codeInput) {
                return SwalPremium.fire({
                    icon: 'warning',
                    title: 'Kode kupon kosong',
                    text: 'Silakan masukkan kode kupon terlebih dahulu.',
                    confirmButtonText: 'Mengerti'
                });
            }

            // 🔒 Cek login
            if (typeof currentAuthUser === "undefined" || !currentAuthUser) {
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

            // Reset pesan kupon
            couponSuccessMsg.classList.add("d-none");
            couponErrorMsg.classList.add("d-none");

            try {
                const response = await fetch('/validate-coupon', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute('content')
                    },
                    body: JSON.stringify({
                        code: codeInput
                    })
                });

                const data = await response.json();
                if (!response.ok || !data.id) {
                    throw new Error(data.message || "Kupon tidak valid.");
                }

                /**
                 * =====================================================
                 * 🧮 SUBTOTAL REAL-TIME (SERVICE + SEMUA ADDON)
                 * =====================================================
                 */
                const servicePrice = Number(window.selectedServicePrice || 0);
                const addonTotal = typeof getAddonTotal === "function" ? getAddonTotal() : 0;
                const subtotalBeforeDiscount = servicePrice + addonTotal;

                /**
                 * 🎟️ Hitung diskon
                 */
                let discountValue = 0;
                if (data.type === "percent") {
                    discountValue = Math.round(subtotalBeforeDiscount * (data.value / 100));
                } else if (data.type === "fixed") {
                    discountValue = Math.min(data.value, subtotalBeforeDiscount);
                }

                // Simpan global
                window.discountValue = discountValue;
                couponIdHidden.value = data.id;

                // 🔄 Update UI (SINGLE SOURCE OF TRUTH)
                if (typeof updatePaymentSummary === "function") {
                    updatePaymentSummary();
                }

                /**
                 * 💳 Validasi DP
                 */
                const totalAfterDiscount = Math.max(0, subtotalBeforeDiscount - discountValue);
                const dpValue = window.dpAmount || Math.round(totalAfterDiscount / 2);

                if (
                    paymentMethodEl?.value === "dp" &&
                    totalAfterDiscount <= dpValue
                ) {
                    paymentMethodEl.value = "cash";
                }

                // 🎉 SweetAlert sukses
                SwalPremium.fire({
                    icon: 'success',
                    title: 'Kupon berhasil diterapkan',
                    html: `
                    <div class="text-start mx-auto" style="max-width: 340px; line-height:1.6;">
                        <p><strong>Kode Kupon</strong> : ${data.code || codeInput}</p>
                        <p><strong>Jenis Diskon</strong> : ${data.type === 'percent' ? 'Persentase' : 'Nominal Tetap'}</p>
                        <p><strong>Nilai Diskon</strong> :
                            ${data.type === 'percent'
                                ? data.value + '%'
                                : formatRupiah(data.value)}
                        </p>
                    </div>
                `,
                    confirmButtonText: 'Lanjutkan',
                    iconColor: '#4CAF50'
                });

            } catch (error) {
                // Reset kupon
                window.discountValue = 0;
                couponIdHidden.value = "";

                couponErrorMsg.textContent = error.message || "Kupon tidak valid.";
                couponErrorMsg.classList.remove("d-none");

                if (typeof updatePaymentSummary === "function") {
                    updatePaymentSummary();
                }

                SwalPremium.fire({
                    icon: 'error',
                    title: 'Kupon gagal diterapkan',
                    text: error.message || 'Kode kupon tidak valid atau sudah kedaluwarsa.',
                    confirmButtonText: 'Mengerti',
                    iconColor: '#ef4444'
                });
            }
        }

        // 🚀 Pasang event listener
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
    </script>










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



    @if (session('login_success'))
        <!-- Toast Container -->
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 1080; max-width: 95%;">
            <div id="loginToast" class="toast align-items-center text-white bg-success border-0 shadow-lg" role="alert"
                aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body fw-semibold">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('login_success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>

        <!-- Toast JS with fade in/out -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toastEl = document.getElementById('loginToast');

                // Tambahkan animasi fade
                toastEl.classList.add('fade', 'show-toast');

                const toast = new bootstrap.Toast(toastEl, {
                    delay: 5000
                });
                toast.show();

                // Bersihkan DOM setelah toast hilang
                toastEl.addEventListener('hidden.bs.toast', function() {
                    toastEl.remove();
                });
            });
        </script>

        <!-- Custom CSS untuk fade in/out -->
        <style>
            @keyframes fadeInOut {
                0% {
                    opacity: 0;
                    transform: translateY(-20%);
                }

                10% {
                    opacity: 1;
                    transform: translateY(0);
                }

                90% {
                    opacity: 1;
                    transform: translateY(0);
                }

                100% {
                    opacity: 0;
                    transform: translateY(-20%);
                }
            }

            .toast.show-toast {
                animation: fadeInOut 5s ease-in-out forwards;
            }

            /* Responsive untuk mobile */
            @media (max-width: 576px) {
                .toast {
                    width: 100%;
                    min-width: 0;
                    border-radius: 0.5rem;
                }

                .position-fixed {
                    top: 1rem;
                    right: 0.5rem;
                    left: 0.5rem;
                }
            }
        </style>
    @endif
@endsection
