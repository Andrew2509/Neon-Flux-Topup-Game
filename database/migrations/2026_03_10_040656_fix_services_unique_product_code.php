<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cleanup duplicates before adding unique index
        DB::statement("DELETE s1 FROM services s1 INNER JOIN services s2 WHERE s1.id < s2.id AND s1.product_code = s2.product_code");

        Schema::table('services', function (Blueprint $table) {
            $table->string('product_code')->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropUnique(['product_code']);
        });
    }
};
