<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTenantRequest;
use App\Models\Tenant;
use App\Services\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantController extends Controller
{
    public function __construct(
        protected TenantManager $tenantManager
    ) {}

    /**
     * List all tenants.
     */
    public function index(Request $request): View
    {
        $query = Tenant::query();

        // Search
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%")
                  ->orWhere('slug', 'ilike', "%{$search}%");
            });
        }

        // Filter by status
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $tenants = $query->latest()->paginate(15)->withQueryString();

        return view('admin.tenants.index', compact('tenants'));
    }

    /**
     * Show create tenant form.
     */
    public function create(): View
    {
        return view('admin.tenants.create');
    }

    /**
     * Store a new tenant and provision its PostgreSQL schema.
     */
    public function store(StoreTenantRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();

            // Ensure slug is set (StoreTenantRequest prepares it, but be safe)
            if (empty($data['slug'])) {
                $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
            }

            // Derive schema name from slug
            $data['schema_name'] = config('tenancy.schema_prefix', 'tenant_') . $data['slug'];

            // Default status
            $data['status'] = $data['status'] ?? 'inactive';

            // Create tenant record in public schema
            \DB::statement('SET search_path TO public');
            $tenant = Tenant::create($data);

            // Provision PostgreSQL schema + tables for this tenant
            $this->tenantManager->createTenant($tenant);

            // Reset to public schema after provisioning
            \DB::statement('SET search_path TO public');

            return redirect()->route('admin.tenants.show', $tenant)
                             ->with('success', "Tenant \"{$tenant->name}\" created and provisioned successfully.");

        } catch (\Exception $e) {
            \Log::error('Tenant creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withInput()
                         ->with('error', 'Failed to create tenant: ' . $e->getMessage());
        }
    }

    /**
     * Show a specific tenant.
     */
    public function show(Tenant $tenant): View
    {
        // Get tenant stats if schema exists
        $stats = $this->getTenantStats($tenant);

        return view('admin.tenants.show', compact('tenant', 'stats'));
    }

    /**
     * Show edit tenant form.
     */
    public function edit(Tenant $tenant): View
    {
        return view('admin.tenants.edit', compact('tenant'));
    }

    /**
     * Update tenant details.
     */
    public function update(StoreTenantRequest $request, Tenant $tenant): RedirectResponse
    {
        $data = $request->validated();

        // Prevent changing slug after creation (would require schema rename)
        unset($data['slug']);

        $tenant->update($data);

        return redirect()->route('admin.tenants.show', $tenant)
                         ->with('success', 'Tenant updated successfully.');
    }

    /**
     * Activate a tenant.
     */
    public function activate(Tenant $tenant): RedirectResponse
    {
        $tenant->activate();

        return back()->with('success', "Tenant \"{$tenant->name}\" has been activated.");
    }

    /**
     * Deactivate a tenant.
     */
    public function deactivate(Tenant $tenant): RedirectResponse
    {
        $tenant->deactivate();

        return back()->with('success', "Tenant \"{$tenant->name}\" has been deactivated.");
    }

    /**
     * Delete a tenant (soft delete).
     */
    public function destroy(Tenant $tenant): RedirectResponse
    {
        $name = $tenant->name;
        $tenant->delete();

        return redirect()->route('admin.tenants.index')
                         ->with('success', "Tenant \"{$name}\" has been deleted.");
    }

    /**
     * Get tenant stats by querying their schema.
     */
    protected function getTenantStats(Tenant $tenant): array
    {
        if (!$this->tenantManager->schemaExists($tenant->schema_name)) {
            return ['schema_exists' => false];
        }

        return $this->tenantManager->runForTenant($tenant, function () {
            return [
                'schema_exists' => true,
                'users_count'   => \DB::table('users')->count(),
                'tasks_count'   => \DB::table('tasks')->count(),
                'tasks_done'    => \DB::table('tasks')->where('status', 'done')->count(),
            ];
        });
    }
}
