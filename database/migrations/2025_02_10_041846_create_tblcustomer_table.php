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
        Schema::create('tblcustomers', function (Blueprint $table) {
            $table->string('customer_id');
            $table->string('customer_name');
            $table->string('customer_tel');
            $table->text('customer_address');
            $table->text('custumer_la_long');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tblcustomers');
    }
};