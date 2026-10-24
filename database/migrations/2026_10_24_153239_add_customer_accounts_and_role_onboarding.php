<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check');
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('creator', 'brand', 'customer', 'admin'))");
        }
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('users', fn (Blueprint $table) => $table->enum('role', ['creator', 'brand', 'customer', 'admin'])->default('creator')->change());
        }
        Schema::create('customer_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
        Schema::table('brand_profiles', function (Blueprint $table): void {
            $table->string('business_category', 150)->nullable();
        });
        Schema::table('collaboration_requests', function (Blueprint $table): void {
            $table->foreignId('brand_profile_id')->nullable()->change();
            $table->foreignId('customer_profile_id')->nullable()->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        if (DB::table('users')->where('role', 'customer')->exists() || DB::table('customer_profiles')->exists()) {
            throw new RuntimeException('Customer accounts exist. Migrate them before rolling back.');
        }
        Schema::table('collaboration_requests', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('customer_profile_id');
            $table->foreignId('brand_profile_id')->nullable(false)->change();
        });
        Schema::table('brand_profiles', fn (Blueprint $table) => $table->dropColumn('business_category'));
        Schema::dropIfExists('customer_profiles');
        if (DB::getDriverName() === 'sqlite') {
            Schema::table('users', fn (Blueprint $table) => $table->enum('role', ['creator', 'brand', 'admin'])->default('creator')->change());
        }
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE users DROP CONSTRAINT users_role_check');
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('creator', 'brand', 'admin'))");
        }
    }
};
