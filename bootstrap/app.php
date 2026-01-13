<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',       // ← WAJIB agar validate-barcode terbaca
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api',                         // ← tanpa ini prefix API hilang!
    )
    ->withMiddleware(function (Middleware $middleware) {

        // Spatie middleware
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        // Middleware web
        $middleware->web(\App\Http\Middleware\SetLocale::class);

        // CORS untuk Electron
        $middleware->append(\App\Http\Middleware\CorsMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
