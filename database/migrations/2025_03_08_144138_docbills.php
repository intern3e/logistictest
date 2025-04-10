<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('docbills', function (Blueprint $table) {
            $table->string('doc_id'); 
            $table->integer('doctype');
            $table->string('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->text('notes')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('docbills');
    }
};