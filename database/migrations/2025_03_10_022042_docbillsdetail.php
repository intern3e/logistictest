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
        Schema::create('doc_detail', function (Blueprint $table) {
            $table->string('doc_detail_id');
            $table->string('so_id');
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
        Schema::dropIfExists('bill_detail');
    }
};

