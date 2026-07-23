<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('po_receive_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('po_receive_id')
                  ->constrained('po_receives')
                  ->cascadeOnDelete();
            $table->string('list_no', 20)->nullable();
            $table->string('good_id', 50)->nullable();
            $table->string('good_name', 500)->nullable();
            $table->decimal('recv_qty', 12, 2);
            $table->timestamps();

            $table->index(['po_receive_id', 'list_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('po_receive_items');
    }
};