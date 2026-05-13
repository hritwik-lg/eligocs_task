<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Schema\Blueprint;

class TenantManager
{
    protected ?Tenant $currentTenant = null;

    /**
     * Initialize the tenant manager
     */
    public function __construct()
    {
        // Set default search_path to public schema
        $this->setSchema(config('tenancy.default_schema', 'public'));
    }

    /**
     * Get the current active tenant
     */
    public function getCurrentTenant(): ?Tenant
    {
        return $this->currentTenant;
    }

    /**
     * Set the current tenant and switch DB schema
     */
    public function setTenant(Tenant $tenant): void
    {
        $this->currentTenant = $tenant;
        $this->setSchema($tenant->schema_name);
    }

    /**
     * Switch to a schema by name
     */
    public function setSchema(string $schema): void
    {
        DB::statement("SET search_path TO {$schema}, public");
    }

    /**
     * Reset to the public/central schema
     */
    public function resetToPublic(): void
    {
        $this->currentTenant = null;
        $this->setSchema(config('tenancy.default_schema', 'public'));
    }

    /**
     * Identify tenant from the request (subdomain-based).
     *
     * Strategy: extract the FIRST segment of the hostname.
     * If only one segment exists (e.g. "localhost") → admin, return null.
     * If multiple segments exist (e.g. "laravel.localhost") → first segment is the tenant slug.
     *
     * This works regardless of what CENTRAL_DOMAIN is set to.
     */
    public function identifyFromRequest(\Illuminate\Http\Request $request): ?Tenant
    {
        // getHost() always strips the port number
        $host = $request->getHost();

        // Split host into parts: "laravel.localhost" → ["laravel", "localhost"]
        $parts = explode('.', $host);

        // Only one part (e.g. "localhost", "127.0.0.1") — this is the bare admin domain
        if (count($parts) <= 1) {
            return null;
        }

        // First segment is the tenant slug: "laravel" from "laravel.localhost"
        $slug = $parts[0];

        // Protect against empty or wildcard slugs
        if (empty($slug) || $slug === '*') {
            return null;
        }

        // Always query tenants from the public schema
        \DB::statement('SET search_path TO public');

        return Tenant::where('slug', $slug)
                     ->where('status', Tenant::STATUS_ACTIVE)
                     ->first();
    }

    /**
     * Create a new tenant with its schema and tables
     */
    public function createTenant(Tenant $tenant): void
    {
        $schema = $tenant->schema_name;

        // Create PostgreSQL schema
        DB::statement("CREATE SCHEMA IF NOT EXISTS \"{$schema}\"");

        // Run tenant migrations in the new schema
        $this->runTenantMigrations($schema);
    }

    /**
     * Drop the tenant schema (destructive!)
     */
    public function dropTenantSchema(Tenant $tenant): void
    {
        $schema = $tenant->schema_name;
        DB::statement("DROP SCHEMA IF EXISTS \"{$schema}\" CASCADE");
    }

    /**
     * Run tenant-specific migrations in a schema
     */
    protected function runTenantMigrations(string $schema): void
    {
        // Set search_path to tenant schema
        DB::statement("SET search_path TO {$schema}, public");

        // Create tenant tables manually
        $this->createTenantTables();

        // Reset to public
        $this->setSchema('public');
    }

    /**
     * Create all tenant-specific tables
     */
    protected function createTenantTables(): void
    {
        // Only tasks table needed — no login, no users in tenant schema
        if (! Schema::hasTable('tasks')) {
            Schema::create('tasks', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->enum('status', ['todo', 'in_progress', 'done'])->default('todo');
                $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
                $table->date('due_date')->nullable();
                $table->softDeletes();
                $table->timestamps();

                $table->index('status');
                $table->index('priority');
                $table->index('due_date');
            });
        }
    }

    /**
     * Check if tenant schema exists
     */
    public function schemaExists(string $schema): bool
    {
        $result = DB::selectOne(
            "SELECT schema_name FROM information_schema.schemata WHERE schema_name = ?",
            [$schema]
        );
        return $result !== null;
    }

    /**
     * Execute a callback within a tenant's context
     */
    public function runForTenant(Tenant $tenant, callable $callback): mixed
    {
        $previous = $this->currentTenant;
        try {
            $this->setTenant($tenant);
            return $callback($tenant);
        } finally {
            if ($previous) {
                $this->setTenant($previous);
            } else {
                $this->resetToPublic();
            }
        }
    }
}