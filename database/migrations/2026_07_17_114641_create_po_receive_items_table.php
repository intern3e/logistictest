<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('po_receives', function (Blueprint $table) {
            $table->id();
            $table->string('po_num', 50)->index();
            $table->string('po_id', 50)->nullable();
            $table->string('vendor_name')->nullable();
            $table->unsignedInteger('item_count')->default(0);
            $table->decimal('total_qty', 12, 2)->default(0);
            $table->string('received_by')->nullable();
            $table->timestamp('received_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('po_receives');
    }
};