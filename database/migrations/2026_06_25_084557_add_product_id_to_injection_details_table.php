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
        Schema::table('injection_details', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->after('id')->constrained('products')->onDelete('cascade');
        });

        // Backfill data
        \Illuminate\Support\Facades\DB::table('injection_details')->orderBy('id')->chunk(100, function ($details) {
            foreach ($details as $detail) {
                $product = \Illuminate\Support\Facades\DB::table('products')
                    ->where('name', $detail->product_name)
                    ->where('batch_no', $detail->batch_no)
                    ->where('type', 'Injection')
                    ->first();
                
                if ($product) {
                    \Illuminate\Support\Facades\DB::table('injection_details')
                        ->where('id', $detail->id)
                        ->update(['product_id' => $product->id]);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('injection_details', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
        });
    }
};
