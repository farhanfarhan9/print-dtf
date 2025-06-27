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
        // Check if indexes exist before creating them
        $indexes = $this->getTableIndexes('purchase_orders');

        Schema::table('purchase_orders', function (Blueprint $table) use ($indexes) {
            if (!in_array('purchase_orders_created_at_index', $indexes)) {
                $table->index('created_at');
            }

            if (!in_array('purchase_orders_additional_price_index', $indexes)) {
                $table->index('additional_price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            // Remove indexes if they exist
            $table->dropIndexIfExists(['created_at']);
            $table->dropIndexIfExists(['additional_price']);
        });
    }

    /**
     * Get all indexes for a table
     */
    private function getTableIndexes($tableName)
    {
        return array_map('strtolower', array_column(
            DB::select("SHOW INDEXES FROM {$tableName}"),
            'Key_name'
        ));
    }
};
