<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Models\Coupon;

// Controllers
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SummerNoteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\PhotoResultController;
use App\Http\Controllers\ServiceBackgroundController;

Auth::routes();

// ==========================
// FRONTEND (GUEST)
// ==========================

Route::get('/', function () {
    return view('frontend.home');
})->name('home');

Route::get('/booking', [FrontendController::class, 'booking'])->name('booking');

Route::get('/services/{service}/addons', [FrontendController::class, 'getServiceAddons']);
Route::get('/services/{service}/backgrounds', [ServiceBackgroundController::class, 'index']);


Route::get('/employees', [EmployeeController::class, 'index']); // ✅ TAMBAH INI


// Layanan & karyawan
Route::get('/services/{service}/employees', [FrontendController::class, 'getEmployees'])->name('get.employees');
Route::get(
    '/employees/{employee}/availability/{date}',
    [FrontendController::class, 'getEmployeeAvailability']
)->name('employee.availability');

Route::get(
    '/employees/{employee}/categories',
    [FrontendController::class, 'getCategoriesByEmployee']
);

Route::get(
    '/employees/{employee}/categories/{category}/services',
    [FrontendController::class, 'getServicesByEmployeeAndCategory']
);




// Booking & appointment
Route::post('/bookings', [AppointmentController::class, 'store'])->name('bookings.store');

Route::post(
    '/appointments/{appointment}/cancel',
    [AppointmentController::class, 'cancel']
)->name('appointments.cancel');

Route::post(
    '/appointments/{appointment}/reschedule',
    [AppointmentController::class, 'reschedule']
)->name('appointments.reschedule');

Route::get('/appointments/{appointment}/reschedule/availability', [FrontendController::class, 'getAvailabilityForReschedule'])
    ->name('appointments.reschedule.availability');



// ==========================
// AUTHENTICATED ROUTES
// ==========================
Route::middleware(['auth'])->group(function () {

    // ==========================
    // MEMBER ROUTES
    // ==========================
    Route::middleware(['role:member'])->group(function () {

        Route::get('/member/dashboard', [TransactionController::class, 'memberIndex'])->name('member.dashboard');
        Route::get('/member/profile', fn() => view('frontend.member.profile'))->name('member.profile');

        // ======= Transaksi Member =======
        Route::prefix('member/transactions')->name('member.transactions.')->group(function () {

            // Daftar transaksi
            Route::get('/', [TransactionController::class, 'memberIndex'])->name('index');

            // Detail transaksi
            Route::get('/{transaction}', [TransactionController::class, 'memberShow'])
                ->whereNumber('transaction')
                ->name('show');

            // Pelunasan via Midtrans
            Route::get('/{transaction}/pay-remaining', [TransactionController::class, 'memberPayRemainingMidtrans'])
                ->whereNumber('transaction')
                ->name('pay_remaining');
        });
    });


    // ==========================
    // ADMIN & MODERATOR ROUTES
    // ==========================
    Route::middleware(['role:admin|moderator|employee'])->group(function () {
        // Dashboard Admin
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // ======= Coupon Management =======
        Route::prefix('coupons')->name('coupons.')->group(function () {
            Route::get('/', [CouponController::class, 'index'])->name('index')->middleware('permission:coupons.view');
            Route::get('/create', [CouponController::class, 'create'])->name('create')->middleware('permission:coupons.create');
            Route::post('/', [CouponController::class, 'store'])->name('store')->middleware('permission:coupons.create');
            Route::get('/{coupon}/edit', [CouponController::class, 'edit'])->name('edit')->middleware('permission:coupons.edit');
            Route::put('/{coupon}', [CouponController::class, 'update'])->name('update')->middleware('permission:coupons.edit');
            Route::delete('/{coupon}', [CouponController::class, 'destroy'])->name('destroy')->middleware('permission:coupons.delete');
        });

        // ======= User Management =======
        Route::resource('user', UserController::class)->middleware('permission:users.view|users.create|users.edit|users.delete');
        Route::patch('user/pasword-update/{user}', [UserController::class, 'password_update'])->name('user.password.update');
        Route::put('user/profile-pic/{user}', [UserController::class, 'updateProfileImage'])->name('user.profile.image.update');
        Route::patch('delete-profile-image/{user}', [UserController::class, 'deleteProfileImage'])->name('delete.profile.image');
        Route::get('user-trash', [UserController::class, 'trashView'])->name('user.trash');
        Route::get('user-restore/{id}', [UserController::class, 'restore'])->name('user.restore');
        Route::delete('user-delete/{id}', [UserController::class, 'force_delete'])->name('user.force.delete');

        // ======= Profile =======
        Route::get('profile', [ProfileController::class, 'index'])->name('profile');
        Route::patch('profile-update/{user}', [ProfileController::class, 'profileUpdate'])->name('user.profile.update');
        Route::patch('employe-profile-update/{employee}', [ProfileController::class, 'employeeProfileUpdate'])->name('employee.profile.update');

        // ======= Settings =======
        Route::get('settings', [SettingController::class, 'index'])->name('setting')->middleware('permission:setting update');
        Route::post('settings/{setting}', [SettingController::class, 'update'])->name('setting.update');

        // ======= Category & Services =======
        Route::resource('category', CategoryController::class)->middleware('permission:categories.view|categories.create|categories.edit|categories.delete');
        Route::resource('service', ServiceController::class)->middleware('permission:services.view|services.create|services.edit|services.delete');
        Route::get('service-trash', [ServiceController::class, 'trashView'])->name('service.trash');
        Route::get('service-restore/{id}', [ServiceController::class, 'restore'])->name('service.restore');
        Route::delete('service-delete/{id}', [ServiceController::class, 'force_delete'])->name('service.force.delete');

        // ======= Summernote =======
        Route::post('summernote', [SummerNoteController::class, 'summerUpload'])->name('summer.upload.image');
        Route::post('summernote/delete', [SummerNoteController::class, 'summerDelete'])->name('summer.delete.image');

        // ======= Employee =======
        Route::get('employee-booking', [UserController::class, 'EmployeeBookings'])->name('employee.bookings');
        Route::get('my-booking/{id}', [UserController::class, 'show'])->name('employee.booking.detail');
        Route::put('employee-bio/{employee}', [EmployeeController::class, 'updateBio'])->name('employee.bio.update');

        // ======= Appointment =======
        Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments')->middleware('permission:appointments.view|appointments.create|services.appointments|appointments.delete');
        Route::post('/appointments/update-status', [AppointmentController::class, 'updateStatus'])->name('appointments.update.status');

        // ======= Dashboard Status =======
        Route::post('/update-status', [DashboardController::class, 'updateStatus'])->name('dashboard.update.status');

        // ======= Transaction (Admin/Staff) =======
        Route::resource('transactions', TransactionController::class);
        Route::get('/transactions/{transaction}/pay-remaining', [TransactionController::class, 'payRemainingMidtrans'])->name('transactions.pay_remaining');
        Route::post('/transactions/{transaction}/update-status', [TransactionController::class, 'updateStatus'])->name('transactions.updateStatus');
        Route::post('/transactions/{transaction}/cash-payment', [TransactionController::class, 'payRemainingCash'])->name('transactions.cash_payment');
    });

    // ======= Testing =======
    Route::get('test', fn(Request $request) => view('test', ['request' => $request]));
    Route::post('test', fn(Request $request) => dd($request->all())->toArray())->name('test');

    // ======= Photo Result =======
    Route::get('/backend/photo-results', [PhotoResultController::class, 'index'])->name('photo-results.index');
    Route::post('/backend/photo-results/upload', [PhotoResultController::class, 'store'])->name('photo-results.store');
    Route::delete('/backend/photo-results/{id}', [PhotoResultController::class, 'destroy'])->name('photo-results.destroy');
    Route::get('/backend/photo-results/send/{transactionId}', [PhotoResultController::class, 'sendToWhatsApp'])->name('photo-results.send');

    Route::post('/backend/photo-results/regenerate-link/{transaction}', [PhotoResultController::class, 'regenerateLink'])
        ->name('photo-results.regenerate-link');
    Route::delete('/photo-results/{transaction}/destroy-all', [PhotoResultController::class, 'destroyAll'])
        ->name('photo-results.destroy-all');
});


// ==========================
// PHOTO RESULTS PUBLIC
// ==========================
// Halaman publik hasil foto
Route::get('/photo-result/{token}', [PhotoResultController::class, 'showPublic'])
    ->name('photo-result.public');

// Download file hasil foto
Route::get('/photo-results/download/{photoResult}/{token}', [PhotoResultController::class, 'download'])
    ->name('photo-results.download');

// Download semua hasil foto dalam 1 zip
Route::get('/photo-results/download-all/{token}', [PhotoResultController::class, 'downloadAll'])
    ->name('photo-results.downloadAll');

// Kirim WA otomatis
Route::post('/photo-results/{transaction}/send-wa', [PhotoResultController::class, 'sendWhatsappFonnte'])
    ->name('photo-results.send-wa');




// ==========================
// PAYMENT & MIDTRANS
// ==========================
Route::post('/midtrans/token', [PaymentController::class, 'getSnapToken']);
Route::post('/midtrans/notification', [MidtransController::class, 'notificationHandler']);
Route::post('/midtrans/callback/{transaction}', [TransactionController::class, 'updateStatus'])->name('midtrans.callback');
Route::get('/member/payment/finish/{transaction}', [TransactionController::class, 'paymentFinish'])
    ->name('member.payment.finish');

Route::post('/validate-coupon', function (Request $request) {
    if (!auth()->check()) {
        return response()->json([
            'message' => 'Kupon hanya untuk member.'
        ], 403); // 403 Forbidden
    }

    $request->validate([
        'code' => 'required|string',
    ]);

    $userId = auth()->id();

    $coupon = Coupon::where('code', strtoupper($request->code))
        ->where('active', true)
        ->where('status', 'unused')
        ->where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
                ->orWhereNull('user_id');
        })
        ->where(function ($q) {
            $q->whereNull('expiry_date')
                ->orWhere('expiry_date', '>=', now());
        })
        ->first();

    if (!$coupon) {
        return response()->json([
            'message' => 'Kupon tidak valid atau tidak tersedia.'
        ], 404);
    }

    return response()->json([
        'id'    => $coupon->id,
        'type'  => $coupon->type,
        'value' => $coupon->value,
    ]);
});



Route::get('/locale/{lang}', function ($lang) {
    if (in_array($lang, ['en', 'id'])) {
        Session::put('locale', $lang);
        App::setLocale($lang);
    }
    return redirect()->back();
});
