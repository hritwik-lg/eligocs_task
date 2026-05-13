<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\Tenant\DashboardController as TenantDashboardController;
use App\Http\Controllers\Tenant\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Debug — http://laravel.localhost:8000/debug-tenant
|--------------------------------------------------------------------------
*/
Route::get('/debug-tenant', function (\Illuminate\Http\Request $request) {
    $host  = $request->getHost();
    $parts = explode('.', $host);
    $slug  = count($parts) > 1 ? $parts[0] : null;
    $tenant = $slug ? \App\Models\Tenant::where('slug', $slug)->first() : null;

    return response()->json([
        'host'           => $host,
        'extracted_slug' => $slug,
        'central_domain' => config('tenancy.central_domain'),
        'tenant_found'   => $tenant?->only(['id','name','slug','status','schema_name']),
        'will_identify'  => $tenant && $tenant->status === 'active',
    ], 200, [], JSON_PRETTY_PRINT);
});

/*
|--------------------------------------------------------------------------
| Super Admin Panel — http://localhost:8000/admin
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {

    Route::middleware('guest:super_admin')->group(function () {
        Route::get('/login',  [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');
    });

    Route::middleware('auth.super_admin')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/',        [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('tenants', TenantController::class);
        Route::patch('/tenants/{tenant}/activate',   [TenantController::class, 'activate'])->name('tenants.activate');
        Route::patch('/tenants/{tenant}/deactivate', [TenantController::class, 'deactivate'])->name('tenants.deactivate');
    });
});

/*
|--------------------------------------------------------------------------
| Tenant Workspace — http://{slug}.localhost:8000
|
| The identify.tenant middleware reads the subdomain, finds the tenant,
| and switches the PostgreSQL schema. NO login needed.
|--------------------------------------------------------------------------
*/
Route::middleware(['identify.tenant', 'tenant.active'])->group(function () {

    // Root: http://laravel.localhost:8000/ → dashboard
    Route::get('/', [TenantDashboardController::class, 'index'])->name('tenant.dashboard');

    // Tasks CRUD
    Route::resource('tasks', TaskController::class)->names([
        'index'   => 'tenant.tasks.index',
        'create'  => 'tenant.tasks.create',
        'store'   => 'tenant.tasks.store',
        'show'    => 'tenant.tasks.show',
        'edit'    => 'tenant.tasks.edit',
        'update'  => 'tenant.tasks.update',
        'destroy' => 'tenant.tasks.destroy',
    ]);

    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])
         ->name('tenant.tasks.status');
});

