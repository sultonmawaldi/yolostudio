<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\CouponGenerated;
use App\Listeners\SendCouponNotification;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        CouponGenerated::class => [
            SendCouponNotification::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
