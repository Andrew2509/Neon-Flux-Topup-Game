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
        Schema::create('provider_categories', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->default('TokoVoucher');
            $table->string('ext_id')->index(); // ID from Tokovoucher (1, 2, etc)
            $table->string('name');
            $table->string('status')->default('Aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_categories');
    }
};
