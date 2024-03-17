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
        Schema::create('internal_processes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('purchase_order_id');
            $table->integer('machine_no')->nullable();
            $table->integer('shift_no')->nullable();
            $table->integer('print_no')->nullable();
            $table->boolean('is_done')->default(0);
            $table->boolean('is_comfirm')->default(0);
            $table->date('execution_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internal_processes');
    }
};
