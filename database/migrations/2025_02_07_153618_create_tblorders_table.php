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
        Schema::create('tblorders', function (Blueprint $table) {
            $table->string('so_detail_id');
            $table->string('so_id');
            $table->string('item_id');
            $table->text('item_name');
            $table->integer('item_quantity');
            $table->float('item_unit_price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tblorders');
    }
};
