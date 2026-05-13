<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'pgsql';
    // No schema prefix here — the TenancyServiceProvider sets search_path=public
    // for all admin/central operations. The Tenant model always lives in the public schema.
    protected $table = 'tenants';

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'email',
        'phone',
        'status',
        'schema_name',
        'settings',
        'activated_at',
    ];

    protected $casts = [
        'settings' => 'array',
        'activated_at' => 'datetime',
    ];

    /**
     * Status constants
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_SUSPENDED = 'suspended';

    /**
     * Boot method to auto-generate slug and schema_name
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Tenant $tenant) {
            if (empty($tenant->slug)) {
                $tenant->slug = Str::slug($tenant->name);
            }
            if (empty($tenant->schema_name)) {
                $tenant->schema_name = config('tenancy.schema_prefix') . $tenant->slug;
            }
        });
    }

    /**
     * Check if tenant is active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Activate tenant
     */
    public function activate(): void
    {
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'activated_at' => now(),
        ]);
    }

    /**
     * Deactivate tenant
     */
    public function deactivate(): void
    {
        $this->update([
            'status' => self::STATUS_INACTIVE,
        ]);
    }

    /**
     * Get full domain for tenant
     */
    public function getFullDomainAttribute(): string
    {
        if ($this->domain) {
            return $this->domain;
        }
        return $this->slug . '.' . config('tenancy.central_domain');
    }

    /**
     * Scope: active tenants
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope: inactive tenants
     */
    public function scopeInactive($query)
    {
        return $query->where('status', self::STATUS_INACTIVE);
    }
}
