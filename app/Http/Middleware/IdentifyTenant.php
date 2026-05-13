<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Services\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

class IdentifyTenant
{
    public function __construct(
        protected TenantManager $tenantManager
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        // Never run tenant identification on admin routes
        if ($request->is('admin') || $request->is('admin/*')) {
            return $next($request);
        }

        $host  = $request->getHost();
        $parts = explode('.', $host);

        // Bare domain (e.g. "localhost") — no subdomain, skip tenant identification
        // The route will fall through to the bare "/" → admin redirect
        if (count($parts) <= 1) {
            return $next($request);
        }

        // Has subdomain — extract slug from first part
        $slug = $parts[0];

        // Ensure we query from the public schema
        DB::statement('SET search_path TO public');

        $tenant = Tenant::where('slug', $slug)
                        ->where('status', Tenant::STATUS_ACTIVE)
                        ->first();

        if (! $tenant) {
            // Subdomain exists but no matching active tenant
            abort(404, "No active tenant found for slug: {$slug}");
        }

        // Switch PostgreSQL search_path to this tenant's schema
        $this->tenantManager->setTenant($tenant);

        // Share with views and container
        view()->share('currentTenant', $tenant);
        app()->instance('currentTenant', $tenant);

        return $next($request);
    }
}
