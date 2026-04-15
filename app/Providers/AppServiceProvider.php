<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\Paginator;
use Midtrans\Config;
use App\Models\Setting;
use App\Models\Transaction;
use App\Observers\TransactionObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        /**
         * 🔐 Super Admin (ID = 1)
         */
        Gate::before(function ($user) {
            return $user->id === 1 ? true : null;
        });

        /**
         * 💳 Konfigurasi Midtrans
         */
        if (config('midtrans.server_key') && config('midtrans.client_key')) {
            Config::$serverKey    = config('midtrans.server_key');
            Config::$clientKey    = config('midtrans.client_key');
            Config::$isProduction = (bool) config('midtrans.is_production', false);
            Config::$isSanitized  = (bool) config('midtrans.is_sanitized', true);
            Config::$is3ds        = (bool) config('midtrans.is_3ds', true);
        }

        view()->share('setting', Setting::first());

        /**
         * 🎯 Register Transaction Observer
         */
        Transaction::observe(TransactionObserver::class);

        /**
         * 🌐 Locale
         */
        app()->setLocale(session('locale', config('app.locale')));

        /**
         * 📄 Pagination
         */
        Paginator::useBootstrap();
    }
}
