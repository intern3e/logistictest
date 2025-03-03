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
        Schema::create('pobills', function (Blueprint $table) {
            $table->string('po_id');
            $table->string('status');
            $table->string('cartype');
            $table->string('store_name');
            $table->string('store_tel');
            $table->string('store_address');
            $table->string('store_la_long');
            $table->string('recvDate');
            $table->string('po_detail_id');
            $table->string('emp_name');
            $table->text('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pobill');
    }
};
