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
        Schema::table('purchase_orders', function (Blueprint $table) {
            // Add indexes to improve query performance for the customer export page
            $table->index('purchase_id');
            $table->index('created_at');
            $table->index(['purchase_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            // Drop the indexes
            $table->dropIndex(['purchase_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['purchase_id', 'created_at']);
        });
    }
};
