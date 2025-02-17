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
        Schema::create('tblbill', function (Blueprint $table) {
            $table->string('so_id');
            $table->string('status');
            $table->string('customer_id');
            $table->string('customer_tel');
            $table->string('customer_address');
            $table->string('customer_la_long');
            $table->string('date_of_dali');
            $table->string('so_detail_id');
            $table->text('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tblbill');
    }
};
