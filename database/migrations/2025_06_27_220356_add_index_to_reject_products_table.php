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
        Schema::table('reject_products', function (Blueprint $table) {
            // Add index to product_id for faster joins
            $table->index('product_id');

            // Add index to created_at for faster date filtering
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reject_products', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex(['product_id']);
            $table->dropIndex(['created_at']);
        });
    }
};
