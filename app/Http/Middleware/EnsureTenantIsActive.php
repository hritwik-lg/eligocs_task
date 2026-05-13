<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantIsActive
{
    /**
     * Ensure the identified tenant is active.
     * Skipped automatically for admin routes (IdentifyTenant returns before this runs).
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip for admin panel routes
        if ($request->is('admin') || $request->is('admin/*')) {
            return $next($request);
        }

        // currentTenant is only bound when IdentifyTenant found one
        if (! app()->bound('currentTenant')) {
            return $next($request);
        }

        $tenant = app('currentTenant');

        if (! $tenant || ! $tenant->isActive()) {
            return response()->view('errors.tenant-inactive', [
                'tenant' => $tenant,
            ], 403);
        }

        return $next($request);
    }
}
