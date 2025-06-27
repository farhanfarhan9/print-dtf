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
        // Add indexes to customers table
        Schema::table('customers', function (Blueprint $table) {
            $table->index('name');
        });

        // Add indexes to purchases table
        Schema::table('purchases', function (Blueprint $table) {
            $table->index('customer_id');
            $table->index('payment_status');
            $table->index(['customer_id', 'payment_status']);
            $table->index('created_at');
            $table->index('total_payment');
        });

        // Add indexes to payments table
        Schema::table('payments', function (Blueprint $table) {
            $table->index('purchase_id');
            $table->index('amount');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes from customers table
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['name']);
        });

        // Remove indexes from purchases table
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropIndex(['customer_id']);
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['customer_id', 'payment_status']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['total_payment']);
        });

        // Remove indexes from payments table
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['purchase_id']);
            $table->dropIndex(['amount']);
            $table->dropIndex(['created_at']);
        });
    }
};
