<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Helper function to check if index exists
        $indexExists = function ($table, $indexName) {
            return DB::select("SHOW INDEX FROM {$table} WHERE Key_name = '{$indexName}'");
        };

        // Add indexes to customers table
        Schema::table('customers', function (Blueprint $table) use ($indexExists) {
            if (!$indexExists('customers', 'customers_name_index')) {
                $table->index('name');
            }
        });

        // Add indexes to purchases table
        Schema::table('purchases', function (Blueprint $table) use ($indexExists) {
            if (!$indexExists('purchases', 'purchases_customer_id_index')) {
                $table->index('customer_id');
            }

            if (!$indexExists('purchases', 'purchases_payment_status_index')) {
                $table->index('payment_status');
            }

            if (!$indexExists('purchases', 'purchases_customer_id_payment_status_index')) {
                $table->index(['customer_id', 'payment_status']);
            }

            if (!$indexExists('purchases', 'purchases_created_at_index')) {
                $table->index('created_at');
            }

            if (!$indexExists('purchases', 'purchases_total_payment_index')) {
                $table->index('total_payment');
            }
        });

        // Add indexes to payments table
        Schema::table('payments', function (Blueprint $table) use ($indexExists) {
            if (!$indexExists('payments', 'payments_purchase_id_index')) {
                $table->index('purchase_id');
            }

            if (!$indexExists('payments', 'payments_amount_index')) {
                $table->index('amount');
            }

            if (!$indexExists('payments', 'payments_created_at_index')) {
                $table->index('created_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Helper function to check if index exists
        $indexExists = function ($table, $indexName) {
            return DB::select("SHOW INDEX FROM {$table} WHERE Key_name = '{$indexName}'");
        };

        // Remove indexes from customers table
        Schema::table('customers', function (Blueprint $table) use ($indexExists) {
            if ($indexExists('customers', 'customers_name_index')) {
                $table->dropIndex(['name']);
            }
        });

        // Remove indexes from purchases table
        Schema::table('purchases', function (Blueprint $table) use ($indexExists) {
            if ($indexExists('purchases', 'purchases_customer_id_index')) {
                $table->dropIndex(['customer_id']);
            }

            if ($indexExists('purchases', 'purchases_payment_status_index')) {
                $table->dropIndex(['payment_status']);
            }

            if ($indexExists('purchases', 'purchases_customer_id_payment_status_index')) {
                $table->dropIndex(['customer_id', 'payment_status']);
            }

            if ($indexExists('purchases', 'purchases_created_at_index')) {
                $table->dropIndex(['created_at']);
            }

            if ($indexExists('purchases', 'purchases_total_payment_index')) {
                $table->dropIndex(['total_payment']);
            }
        });

        // Remove indexes from payments table
        Schema::table('payments', function (Blueprint $table) use ($indexExists) {
            if ($indexExists('payments', 'payments_purchase_id_index')) {
                $table->dropIndex(['purchase_id']);
            }

            if ($indexExists('payments', 'payments_amount_index')) {
                $table->dropIndex(['amount']);
            }

            if ($indexExists('payments', 'payments_created_at_index')) {
                $table->dropIndex(['created_at']);
            }
        });
    }
};
