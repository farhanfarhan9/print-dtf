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
            // Make existing columns nullable and change type to bigint
            $table->bigInteger('provinsi')->nullable()->change();
            $table->bigInteger('city')->nullable()->change();
            $table->bigInteger('district')->nullable()->change();
            $table->bigInteger('postal')->nullable()->change();

            // Add new varchar columns
            $table->string('provinsi_name')->nullable();
            $table->string('city_name')->nullable();
            $table->string('district_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Reverse the changes
            $table->integer('provinsi')->nullable(false)->change(); // assuming original type was integer
            $table->integer('city')->nullable(false)->change();
            $table->integer('district')->nullable(false)->change();
            $table->integer('postal')->nullable(false)->change();

            // Remove the added columns
            $table->dropColumn(['provinsi_name', 'city_name', 'district_name']);
        });
    }
};
