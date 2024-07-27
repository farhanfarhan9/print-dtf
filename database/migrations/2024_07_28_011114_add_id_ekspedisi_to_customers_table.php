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
        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedBigInteger('id_ekspedisi')->nullable()->after('address');
            // If you want to add a foreign key constraint, uncomment the next line
            // $table->foreign('id_ekspedisi')->references('id')->on('ekspedisis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('id_ekspedisi');
            // If you added a foreign key constraint, uncomment the next line
            // $table->dropForeign(['id_ekspedisi']);
        });
    }
};
