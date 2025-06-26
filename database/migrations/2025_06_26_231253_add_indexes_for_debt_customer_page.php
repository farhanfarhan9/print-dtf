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
        // Add indexes to purchase_orders table for status field
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->index('status');
            $table->index(['status', 'created_at']);
        });

        // Add indexes to payments table
        Schema::table('payments', function (Blueprint $table) {
            $table->index('purchase_id');
            $table->index('created_at');
        });

        // Add indexes to products table
        Schema::table('products', function (Blueprint $table) {
            $table->index('nama_produk');
        });

        // Add indexes to customers table
        Schema::table('customers', function (Blueprint $table) {
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes from purchase_orders table
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['status', 'created_at']);
        });

        // Drop indexes from payments table
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['purchase_id']);
            $table->dropIndex(['created_at']);
        });

        // Drop indexes from products table
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['nama_produk']);
        });

        // Drop indexes from customers table
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });
    }
};
