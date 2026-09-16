<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * เพิ่มสถานะ "จัดแล้ว" รายไส้ใน (per line) — ให้ติ๊กจัดทีละสินค้าได้
     */
    public function up(): void
    {
        Schema::table('internal_poline', function (Blueprint $table) {
            if (!Schema::hasColumn('internal_poline', 'picked_at')) {
                $table->dateTime('picked_at')->nullable()->after('item_total');
            }
            if (!Schema::hasColumn('internal_poline', 'picked_by')) {
                $table->string('picked_by', 100)->nullable()->after('picked_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('internal_poline', function (Blueprint $table) {
            if (Schema::hasColumn('internal_poline', 'picked_by')) $table->dropColumn('picked_by');
            if (Schema::hasColumn('internal_poline', 'picked_at')) $table->dropColumn('picked_at');
        });
    }
};
