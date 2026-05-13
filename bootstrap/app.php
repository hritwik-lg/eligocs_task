<?php

use App\Http\Middleware\EnsureTenantIsActive;
use App\Http\Middleware\IdentifyTenant;
use App\Http\Middleware\SuperAdminAuthenticate;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register named middleware aliases
        $middleware->alias([
            'auth.super_admin'  => SuperAdminAuthenticate::class,
            'identify.tenant'   => IdentifyTenant::class,
            'tenant.active'     => EnsureTenantIsActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withProviders([
        App\Providers\TenancyServiceProvider::class,
    ])
    ->create();
