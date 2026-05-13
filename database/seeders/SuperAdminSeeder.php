<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SuperAdmin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET search_path TO public');
 
        SuperAdmin::updateOrCreate(
            ['email' => env('SUPER_ADMIN_EMAIL', 'superadmin@eligocs.com')],
            [
                'name'     => 'Super Admin',
                'email'    => env('SUPER_ADMIN_EMAIL', 'superadmin@eligocs.com'),
                'password' => Hash::make(env('SUPER_ADMIN_PASSWORD', 'SuperAdmin@123')),
            ]
        );
 
        $this->command->info('Super Admin seeded: ' . env('SUPER_ADMIN_EMAIL', 'superadmin@saasapp.com'));
    }
}
