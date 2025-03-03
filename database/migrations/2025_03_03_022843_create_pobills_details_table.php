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
        Schema::create('pobills_detail', function (Blueprint $table) {
            $table->string('po_detail_id');
            $table->string('po_id');
            $table->string('item_id');
            $table->string('item_name');
            $table->float('quantity');
            $table->float('unit_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pobills_details');
    }
};
