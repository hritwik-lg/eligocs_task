<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Tenant;
use App\Services\TenantManager;

class TenantSeeder extends Seeder
{
     public function __construct(
        protected TenantManager $tenantManager
    ) {}

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET search_path TO public');
 
        $tenantsData = [
            [
                'name'   => 'Acme Corporation',
                'slug'   => 'acme',
                'email'  => 'admin@acme.com',
                'phone'  => '+1-555-0100',
                'status' => Tenant::STATUS_ACTIVE,
                'admin'  => ['name' => 'Alice Admin', 'email' => 'alice@acme.com', 'password' => 'password'],
            ],
            [
                'name'   => 'TechStart Inc',
                'slug'   => 'techstart',
                'email'  => 'admin@techstart.com',
                'phone'  => '+1-555-0200',
                'status' => Tenant::STATUS_ACTIVE,
                'admin'  => ['name' => 'Bob Builder', 'email' => 'bob@techstart.com', 'password' => 'password'],
            ],
            [
                'name'   => 'Beta Company',
                'slug'   => 'beta',
                'email'  => 'admin@beta.com',
                'phone'  => '+1-555-0300',
                'status' => Tenant::STATUS_INACTIVE,
                'admin'  => null,
            ],
        ];
 
        foreach ($tenantsData as $data) {
            $adminData = $data['admin'];
            unset($data['admin']);
 
            // Create tenant
            $tenant = Tenant::firstOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, [
                    'schema_name' => config('tenancy.schema_prefix') . $data['slug'],
                ])
            );
 
            // Create schema if it doesn't exist
            if (!$this->tenantManager->schemaExists($tenant->schema_name)) {
                $this->tenantManager->createTenant($tenant);
                $this->command->info("Schema created for tenant: {$tenant->name}");
            }
 
            // Seed tenant admin user
            if ($adminData && $tenant->isActive()) {
                $this->tenantManager->runForTenant($tenant, function () use ($adminData) {
                    \App\Models\User::updateOrCreate(
                        ['email' => $adminData['email']],
                        [
                            'name'      => $adminData['name'],
                            'email'     => $adminData['email'],
                            'password'  => Hash::make($adminData['password']),
                            'role'      => \App\Models\User::ROLE_ADMIN,
                            'is_active' => true,
                        ]
                    );
 
                    // Seed some sample tasks
                    $users = \App\Models\User::all();
                    foreach ($users as $user) {
                        \App\Models\Task::factory()->count(5)->create(['user_id' => $user->id]);
                    }
                });
 
                $this->command->info("Seeded admin & tasks for tenant: {$tenant->name}");
            }
        }
    }
}
