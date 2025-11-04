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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('batch_no');
            $table->string('stage');
            $table->enum('type', ['Injection', 'Suspension', 'Tablet', 'Capsule'])->nullable();
            $table->enum('status', ['pending', 'submitted'])->default('pending');
            
            // Line Clearance Field (Combined)
            $table->boolean('line_clearance')->default(false);
            
            // Review and Confirmation Fields
            $table->boolean('review')->default(false);
            $table->boolean('confirmation')->default(false);
            
            // Additional Fields
            $table->text('remarks')->nullable();
            $table->date('submission_date')->nullable();
            $table->time('submission_time')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
