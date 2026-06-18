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
        Schema::table('products', function (Blueprint $table) {
            $table->index(['user_id', 'status']);
            $table->index('type');
            $table->index('name');
            $table->index('batch_no');
            $table->index('stage');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['type']);
            $table->dropIndex(['name']);
            $table->dropIndex(['batch_no']);
            $table->dropIndex(['stage']);
            $table->dropIndex(['created_at']);
        });
    }
};
