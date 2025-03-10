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
        Schema::create('docbills', function (Blueprint $table) {
            $table->string('doc_id');
            $table->string('status');
            $table->string('customer_id');
            $table->string('customer_tel');
            $table->string('customer_address');
            $table->string('customer_la_long');
            $table->string('date_of_dali');
            $table->string('doc_detail_id');
            $table->string('emp_name');
            $table->string('sale_name');
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
