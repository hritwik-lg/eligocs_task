<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function getConnection(): string
    {
        return 'pgsql';
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('SET search_path TO public');
 
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('domain')->unique()->nullable()->comment('Custom domain, if any');
            $table->string('schema_name')->unique()->comment('PostgreSQL schema name');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('inactive');
            $table->json('settings')->nullable()->comment('Tenant-specific settings');
            $table->timestamp('activated_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
 
            $table->index('slug');
            $table->index('status');
            $table->index('domain');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET search_path TO public');
        Schema::dropIfExists('tenants');
    }
};
