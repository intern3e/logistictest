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
        Schema::create('so_item_id', function (Blueprint $table) {
            $table->string('so_id');
            $table->string('item_id');
            $table->string('item_name');
            $table->text('item_quantity');
            $table->text('item_unit_price');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('so_item_id');
    }
};
