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
        Schema::create('tblsos', function (Blueprint $table) {
            $table->string('so_id');
            $table->string('customer_id');
            $table->string('so_item_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tblsos');
    }
};