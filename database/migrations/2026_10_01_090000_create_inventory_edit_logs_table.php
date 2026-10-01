<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_edit_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('action', 20);              // update | delete
            $table->string('target_type', 20);         // item | transaction
            $table->string('target_id', 191)->nullable();
            $table->string('target_name', 500)->nullable();
            $table->text('changes')->nullable();       // JSON: [{field,label,from,to}]
            $table->decimal('qty_from', 15, 2)->nullable();
            $table->decimal('qty_to', 15, 2)->nullable();
            $table->text('reason')->nullable();        // เหตุผล (เฉพาะตอนลบ)
            $table->string('edited_by', 191)->nullable();
            $table->string('edited_by_id', 191)->nullable();
            $table->string('role', 50)->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->index('action');
            $table->index('target_type');
            $table->index('target_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_edit_logs');
    }
};
