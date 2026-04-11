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
use App\Http\Controllers\AddonController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\StudioController;
use App\Http\Controllers\SlotGroupController;



Auth::routes();

/*
|--------------------------------------------------------------------------
| FRONTEND (GUEST)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/booking', [FrontendController::class, 'booking'])->name('booking');

Route::get('/services/{service}/addons', [FrontendController::class, 'getServiceAddons']);
Route::get('/services/{service}/backgrounds', [ServiceBackgroundController::class, 'getByService']);

Route::get('/employees', [EmployeeController::class, 'index']);
Route::get('/services/{service}/employees', [FrontendController::class, 'getEmployees'])->name('get.employees');

Route::get('/employees/{employee}/availability/{date}', [FrontendController::class, 'getEmployeeAvailability'])
    ->name('employee.availability');

Route::get('/employees/{employee}/categories', [FrontendController::class, 'getCategoriesByEmployee']);
Route::get('/employees/{employee}/categories/{category}/services', [FrontendController::class, 'getServicesByEmployeeAndCategory']);

Route::post('/bookings', [AppointmentController::class, 'store'])->name('bookings.store');
Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
Route::post('/appointments/{appointment}/reschedule', [AppointmentController::class, 'reschedule'])->name('appointments.reschedule');
Route::get('appointments/{appointment}/reschedule/availability', [AppointmentController::class, 'getAvailabilityForReschedule']);


Route::get('/pricelist', [FrontendController::class, 'pricelist'])
    ->name('pricelist');

Route::get('/gallery', [GalleryController::class, 'index'])
    ->name('gallery.index');

Route::get('/studio', [StudioController::class, 'index'])
    ->name('studio');











/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | MEMBER ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:member')
        ->prefix('member')
        ->name('member.')
        ->group(function () {

            // Dashboard
            Route::get('/dashboard', function () {
                return redirect()->route('member.profile');
            })->name('dashboard');

            // Profile
            Route::get('/profile', [UserController::class, 'memberProfile'])
                ->name('profile');

            Route::get('/profile/edit', [UserController::class, 'memberProfileEdit'])
                ->name('profile.edit');

            Route::put('/profile', [UserController::class, 'memberProfileUpdate'])
                ->name('profile.update');

            Route::delete('/profile/delete-image', [UserController::class, 'deleteProfileImage'])
                ->name('profile.delete_image');

            // ✅ Password Update
            Route::put('/profile/password', [UserController::class, 'memberPasswordUpdate'])
                ->name('profile.password_update');







            // OPTIONAL (kalau masih dipakai)
            // Route::get('/points', [PointController::class, 'index'])->name('points');
            // Route::get('/bookings', [AppointmentController::class, 'memberIndex'])->name('bookings');

            // Transactions
            Route::prefix('transactions')->name('transactions.')->group(function () {

                // Daftar transaksi (index.blade.php)
                Route::get('/', [TransactionController::class, 'memberIndex'])->name('index');

                // Detail transaksi
                Route::get('/{transaction}', [TransactionController::class, 'memberShow'])
                    ->whereNumber('transaction')
                    ->name('show');

                // Bayar sisa (Midtrans)
                Route::get('/{transaction}/pay-remaining', [TransactionController::class, 'memberPayRemainingMidtrans'])
                    ->whereNumber('transaction')
                    ->name('pay_remaining');
            });


            // Coupons
            // Coupons (MEMBER)
            Route::prefix('coupons')->name('coupons.')->group(function () {
                Route::get('/', [CouponController::class, 'memberIndex'])
                    ->name('index');

                Route::get('/redeem', [CouponController::class, 'redeem'])
                    ->name('redeem');

                Route::post('/redeem', [CouponController::class, 'redeemStore'])
                    ->name('redeem.store');
            });
        });

    /*
    |--------------------------------------------------------------------------
    | ADMIN / MODERATOR
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin|moderator|employee')
        ->prefix('admin')
        ->group(function () {
            // Dashboard Admin
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

            // ======= Coupon Management (Admin Only) =======
            Route::middleware('role:admin')->prefix('coupons')->name('coupons.')->group(function () {

                // List semua coupon
                Route::get('/', [CouponController::class, 'index'])
                    ->name('index')
                    ->middleware('permission:coupons.view');

                // Form tambah coupon baru
                Route::get('/create', [CouponController::class, 'create'])
                    ->name('create')
                    ->middleware('permission:coupons.create');

                // Simpan coupon baru
                Route::post('/', [CouponController::class, 'store'])
                    ->name('store')
                    ->middleware('permission:coupons.create');

                // Form edit coupon
                Route::get('/{coupon}/edit', [CouponController::class, 'edit'])
                    ->name('edit')
                    ->middleware('permission:coupons.edit');

                // Update coupon
                Route::put('/{coupon}', [CouponController::class, 'update'])
                    ->name('update')
                    ->middleware('permission:coupons.edit');

                // Hapus coupon
                Route::delete('/{coupon}', [CouponController::class, 'destroy'])
                    ->name('destroy')
                    ->middleware('permission:coupons.delete');
            });

            // ======= Addon Management (Admin Only) =======
            Route::middleware('role:admin')->prefix('addons')->name('addons.')->group(function () {

                Route::get('/', [AddonController::class, 'index'])
                    ->name('index')
                    ->middleware('permission:addons.view');

                Route::get('/create', [AddonController::class, 'create'])
                    ->name('create')
                    ->middleware('permission:addons.create');

                Route::post('/', [AddonController::class, 'store'])
                    ->name('store')
                    ->middleware('permission:addons.create');

                Route::get('/{addon}/edit', [AddonController::class, 'edit'])
                    ->name('edit')
                    ->middleware('permission:addons.edit');

                Route::put('/{addon}', [AddonController::class, 'update'])
                    ->name('update')
                    ->middleware('permission:addons.edit');

                Route::delete('/{addon}', [AddonController::class, 'destroy'])
                    ->name('destroy')
                    ->middleware('permission:addons.delete');
            });

            // ======= Service Background Management (Admin Only) =======
            Route::middleware('role:admin')->prefix('service-backgrounds')->name('service-backgrounds.')->group(function () {

                Route::get('/', [ServiceBackgroundController::class, 'index'])
                    ->name('index')
                    ->middleware('permission:service-backgrounds.view');

                Route::get('/create', [ServiceBackgroundController::class, 'create'])
                    ->name('create')
                    ->middleware('permission:service-backgrounds.create');

                Route::post('/', [ServiceBackgroundController::class, 'store'])
                    ->name('store')
                    ->middleware('permission:service-backgrounds.create');

                Route::get('/{background}/edit', [ServiceBackgroundController::class, 'edit'])
                    ->name('edit')
                    ->middleware('permission:service-backgrounds.edit');

                Route::put('/{background}', [ServiceBackgroundController::class, 'update'])
                    ->name('update')
                    ->middleware('permission:service-backgrounds.edit');

                Route::delete('/{background}', [ServiceBackgroundController::class, 'destroy'])
                    ->name('destroy')
                    ->middleware('permission:service-backgrounds.delete');
            });






            // ======= User Management (Admin Only) =======
            Route::middleware('role:admin')->group(function () {
                Route::resource('user', UserController::class)
                    ->middleware('permission:users.view|users.create|users.edit|users.delete');

                Route::patch('user/pasword-update/{user}', [UserController::class, 'password_update'])
                    ->name('user.password.update');

                Route::put('user/profile-pic/{user}', [UserController::class, 'updateProfileImage'])
                    ->name('user.profile.image.update');

                Route::patch('delete-profile-image/{user}', [UserController::class, 'deleteProfileImage'])
                    ->name('delete.profile.image');

                Route::get('user-trash', [UserController::class, 'trashView'])->name('user.trash');
                Route::get('user-restore/{id}', [UserController::class, 'restore'])->name('user.restore');
                Route::delete('user-delete/{id}', [UserController::class, 'force_delete'])->name('user.force.delete');
            });

            Route::middleware('role:admin')->prefix('slot-group')->name('slot-group.')->group(function () {

                Route::get('/', [SlotGroupController::class, 'index'])
                    ->name('index')
                    ->middleware('permission:slot-groups.view');

                Route::get('/create', [SlotGroupController::class, 'create'])
                    ->name('create')
                    ->middleware('permission:slot-groups.create');

                Route::post('/', [SlotGroupController::class, 'store'])
                    ->name('store')
                    ->middleware('permission:slot-groups.create');

                Route::get('/{slotGroup}/edit', [SlotGroupController::class, 'edit'])
                    ->name('edit')
                    ->middleware('permission:slot-groups.edit');

                Route::put('/{slotGroup}', [SlotGroupController::class, 'update'])
                    ->name('update')
                    ->middleware('permission:slot-groups.edit');

                Route::delete('/{slotGroup}', [SlotGroupController::class, 'destroy'])
                    ->name('destroy')
                    ->middleware('permission:slot-groups.delete');
            });

            // ======= Profile =======
            Route::get('profile', [ProfileController::class, 'index'])->name('profile');
            Route::patch('profile-update/{user}', [ProfileController::class, 'profileUpdate'])->name('user.profile.update');
            Route::patch('employe-profile-update/{employee}', [ProfileController::class, 'employeeProfileUpdate'])->name('employee.profile.update');

            Route::middleware('role:admin')->group(function () {
                // ======= Settings =======
                Route::get('settings', [SettingController::class, 'index'])->name('setting')->middleware('permission:setting update');
                Route::post('settings/{setting}', [SettingController::class, 'update'])->name('setting.update');
            });

            Route::middleware('role:admin')->group(function () {
                // ======= Category & Services =======
                Route::resource('category', CategoryController::class)->middleware('permission:categories.view|categories.create|categories.edit|categories.delete');
            });

            Route::middleware('role:admin')->group(function () {
                Route::resource('service', ServiceController::class)->middleware('permission:services.view|services.create|services.edit|services.delete');
                Route::get('service-trash', [ServiceController::class, 'trashView'])->name('service.trash');
                Route::get('service-restore/{id}', [ServiceController::class, 'restore'])->name('service.restore');
                Route::delete('service-delete/{id}', [ServiceController::class, 'force_delete'])->name('service.force.delete');
            });

            // ======= Summernote =======
            Route::post('summernote', [SummerNoteController::class, 'summerUpload'])->name('summer.upload.image');
            Route::post('summernote/delete', [SummerNoteController::class, 'summerDelete'])->name('summer.delete.image');


            // ======= Employee =======
            Route::get('employee-booking', [UserController::class, 'EmployeeBookings'])->name('employee.bookings');
            Route::get('my-booking/{id}', [UserController::class, 'show'])->name('employee.booking.detail');
            Route::put('employee-bio/{employee}', [EmployeeController::class, 'updateBio'])->name('employee.bio.update');

            // ======= Appointment =======
            Route::prefix('appointments')->name('appointments.')->group(function () {

                // LIST APPOINTMENTS (Admin, Moderator, Employee)
                Route::get('/', [AppointmentController::class, 'index'])
                    ->name('index')
                    ->middleware('permission:appointments.view');

                // UPDATE STATUS (kalau memang perlu)
                Route::post('/update-status', [AppointmentController::class, 'updateStatus'])
                    ->name('update.status')
                    ->middleware('permission:appointments.edit');
            });


            // ======= Dashboard Status =======
            Route::post('/update-status', [DashboardController::class, 'updateStatus'])->name('dashboard.update.status');

            // ======= Transaction (Admin/Staff) =======
            Route::resource('transactions', TransactionController::class);
            Route::get('/transactions/{transaction}/pay-remaining', [TransactionController::class, 'payRemainingMidtrans'])->name('transactions.pay_remaining');
            Route::post('/transactions/{transaction}/update-status', [TransactionController::class, 'updateStatus'])->name('transactions.updateStatus');
            Route::post('/transactions/{transaction}/cash-payment', [TransactionController::class, 'payRemainingCash'])->name('transactions.cash_payment');
            Route::get('transactions/{transaction}/pay-remaining-midtrans', [TransactionController::class, 'payRemainingMidtrans'])
                ->name('transactions.payRemainingMidtrans');

            // ======= Gallery Management (Admin Only) =======
            Route::middleware('role:admin')->prefix('gallery')->name('gallery.')->group(function () {

                // List semua gallery
                Route::get('/', [GalleryController::class, 'adminIndex'])
                    ->name('index')
                    ->middleware('permission:gallery.view');

                // Form tambah gallery baru
                Route::get('/create', [GalleryController::class, 'create'])
                    ->name('create')
                    ->middleware('permission:gallery.create');

                // Simpan gallery baru
                Route::post('/', [GalleryController::class, 'store'])
                    ->name('store')
                    ->middleware('permission:gallery.create');

                // Form edit gallery
                Route::get('/{gallery}/edit', [GalleryController::class, 'edit'])
                    ->name('edit')
                    ->middleware('permission:gallery.edit');

                // Update gallery
                Route::put('/{gallery}', [GalleryController::class, 'update'])
                    ->name('update')
                    ->middleware('permission:gallery.edit');

                // Hapus gallery
                Route::delete('/{gallery}', [GalleryController::class, 'destroy'])
                    ->name('destroy')
                    ->middleware('permission:gallery.delete');
            });

            // ======= Studio Management (Admin Only) =======
            Route::middleware('role:admin')->prefix('studio')->name('studio.')->group(function () {

                // List semua studio
                Route::get('/', [StudioController::class, 'adminIndex'])
                    ->name('index')
                    ->middleware('permission:studio.view');

                // Form tambah studio baru
                Route::get('/create', [StudioController::class, 'create'])
                    ->name('create')
                    ->middleware('permission:studio.create');

                // Simpan studio baru
                Route::post('/', [StudioController::class, 'store'])
                    ->name('store')
                    ->middleware('permission:studio.create');

                // Form edit studio
                Route::get('/{studio}/edit', [StudioController::class, 'edit'])
                    ->name('edit')
                    ->middleware('permission:studio.edit');

                // Update studio
                Route::put('/{studio}', [StudioController::class, 'update'])
                    ->name('update')
                    ->middleware('permission:studio.edit');

                // Hapus studio
                Route::delete('/{studio}', [StudioController::class, 'destroy'])
                    ->name('destroy')
                    ->middleware('permission:studio.delete');
            });
            // ======= Photo Result =======
            Route::get('/photo-results', [PhotoResultController::class, 'index'])->name('photo-results.index');
            Route::post('/photo-results/upload', [PhotoResultController::class, 'store'])->name('photo-results.store');
            Route::delete('/photo-results/{id}', [PhotoResultController::class, 'destroy'])->name('photo-results.destroy');
            Route::get('/photo-results/send/{transactionId}', [PhotoResultController::class, 'sendToWhatsApp'])->name('photo-results.send');

            Route::post('/photo-results/regenerate-link/{transaction}', [PhotoResultController::class, 'regenerateLink'])
                ->name('photo-results.regenerate-link');
            Route::delete('/photo-results/{transaction}/destroy-all', [PhotoResultController::class, 'destroyAll'])
                ->name('photo-results.destroy-all');
        });

    Route::get('/transactions/payment/finish/{transaction}', [TransactionController::class, 'paymentFinish'])->name('transactions.payment.finish');

    // ======= Testing =======
    Route::get('test', fn(Request $request) => view('test', ['request' => $request]));
    Route::post('test', fn(Request $request) => dd($request->all())->toArray())->name('test');
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

    /**
     * ============================================
     * 🔒 HANYA MEMBER
     * ============================================
     */
    if (!auth()->check()) {
        return response()->json([
            'message' => 'Kupon hanya untuk member.'
        ], 403);
    }

    /**
     * ============================================
     * ✅ VALIDASI REQUEST
     * ============================================
     */
    $request->validate([
        'code'       => 'required|string',
        'service_id' => 'nullable|integer',
        'subtotal'   => 'nullable|numeric'
    ]);

    $userId = auth()->id();

    /**
     * ============================================
     * 🔎 CARI KUPON
     * ============================================
     */

    $code = trim(strtoupper($request->code));

    $coupon = Coupon::whereRaw('UPPER(code) = ?', [$code])
        ->where('active', 1)
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

    /**
     * ============================================
     * 🎯 CEK SERVICE (pivot coupon_service)
     * ============================================
     */

    $allowedServices = $coupon->services->pluck('id');

    if ($allowedServices->isNotEmpty()) {

        if (!$allowedServices->contains((int) $request->service_id)) {

            return response()->json([
                'message' => 'Kupon tidak berlaku untuk layanan ini.'
            ], 422);
        }
    }

    /**
     * ============================================
     * 💰 CEK MINIMUM TRANSAKSI
     * ============================================
     */

    if ($coupon->minimum_cart_value && $request->subtotal) {

        if ($request->subtotal < $coupon->minimum_cart_value) {

            return response()->json([
                'message' =>
                'Minimal transaksi untuk kupon ini adalah Rp ' .
                    number_format($coupon->minimum_cart_value, 0, ',', '.')
            ], 422);
        }
    }

    /**
     * ============================================
     * ✅ KUPON VALID
     * ============================================
     */

    return response()->json([
        'id'    => $coupon->id,
        'type'  => $coupon->type,
        'value' => $coupon->value,
        'code'  => $coupon->code
    ]);
});



Route::get('/locale/{lang}', function ($lang) {
    if (in_array($lang, ['en', 'id'])) {
        Session::put('locale', $lang);
        App::setLocale($lang);
    }
    return redirect()->back();
});
