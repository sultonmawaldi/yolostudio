<?php

namespace App\Listeners;

use App\Events\CouponGenerated;
use Illuminate\Support\Facades\Session;

class SendCouponNotification
{
    public function handle(CouponGenerated $event): void
    {
        // Simpan notifikasi ke session flash
        Session::flash(
            'new_coupon',
            "🎉 Selamat! Anda mendapatkan kupon baru: {$event->coupon->code}"
        );
    }
}
