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
        Schema::create('flash_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->decimal('discount_price', 15, 2);
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
            $table->integer('stock')->default(-1); // -1 for unlimited
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flash_sales');
    }
};
