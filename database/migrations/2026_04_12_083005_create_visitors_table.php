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
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->string('url')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('country')->nullable();
            $table->string('country_code', 10)->nullable();
            $table->timestamp('last_active_at')->useCurrent();
            $table->timestamps();

            $table->index('ip_address');
            $table->index('last_active_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
