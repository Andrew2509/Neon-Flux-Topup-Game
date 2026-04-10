<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('password')->constrained('roles')->onDelete('set null');
        });

        // Migration Logic: Create default roles and map existing users
        $superAdminRoleId = DB::table('roles')->insertGetId([
            'name' => 'Super Admin',
            'slug' => 'super-admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $memberRoleId = DB::table('roles')->insertGetId([
            'name' => 'Member',
            'slug' => 'member',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $vipRoleId = DB::table('roles')->insertGetId([
            'name' => 'VIP',
            'slug' => 'vip',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Map existing data
        DB::table('users')->where('role', 'admin')->update(['role_id' => $superAdminRoleId]);
        DB::table('users')->where('role', 'vip')->update(['role_id' => $vipRoleId]);
        DB::table('users')->where('role', 'member')->update(['role_id' => $memberRoleId]);
        DB::table('users')->whereNull('role_id')->update(['role_id' => $memberRoleId]);

        // Drop old column
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('member')->after('balance');
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
    }
};
