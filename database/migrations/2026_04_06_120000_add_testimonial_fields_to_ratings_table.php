<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->string('order_id', 64)->nullable()->after('user_id');
            $table->string('author_nickname', 64)->nullable()->after('comment');
            $table->boolean('is_visible')->default(true)->after('author_nickname');
        });

        Schema::table('ratings', function (Blueprint $table) {
            $table->unique('order_id');
        });
    }

    public function down(): void
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->dropUnique(['order_id']);
            $table->dropColumn(['order_id', 'author_nickname', 'is_visible']);
        });
    }
};
