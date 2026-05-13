<?php

namespace App\Providers;

use App\Services\TenantManager;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;

class TenancyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(TenantManager::class, function ($app) {
            return new TenantManager();
        });

        // Alias for easy resolution
        $this->app->alias(TenantManager::class, 'tenancy');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Set public schema as default whenever a new DB connection is resolved
        \Illuminate\Support\Facades\DB::statement('SET search_path TO public');

        // Also listen for reconnections
        // \Illuminate\Support\Facades\DB::reconnecting(function () {
        //     \Illuminate\Support\Facades\DB::statement('SET search_path TO public');
        // });
    }
}
