<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->decimal('balance', 15, 2)->default(0)->after('password');
            $table->string('role')->default('member')->after('balance'); // admin, member
            $table->string('status')->default('active')->after('role'); // active, blocked, unverified
            $table->string('avatar')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'balance', 'role', 'status', 'avatar']);
        });
    }
};
