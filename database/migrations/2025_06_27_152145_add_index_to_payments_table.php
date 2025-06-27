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
        $indexes = $this->getTableIndexes('payments');

        Schema::table('payments', function (Blueprint $table) use ($indexes) {
            if (!in_array('payments_created_at_index', $indexes)) {
                $table->index('created_at');
            }

            if (!in_array('payments_purchase_id_index', $indexes)) {
                $table->index('purchase_id');
            }

            if (!in_array('payments_bank_detail_index', $indexes)) {
                $table->index('bank_detail');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Remove indexes if they exist
            $table->dropIndexIfExists(['created_at']);
            $table->dropIndexIfExists(['purchase_id']);
            $table->dropIndexIfExists(['bank_detail']);
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
