<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * เพิ่มคอลัมน์ mark การยกเลิกรับเข้า แทนการลบ row ทิ้ง (กันข้อมูลหาย)
     * ยกเลิกรับเข้า = set cancelled_at / cancelled_by ไม่ใช่ DELETE
     */
    public function up(): void
    {
        Schema::table('po_receives_line', function (Blueprint $table) {
            if (!Schema::hasColumn('po_receives_line', 'cancelled_at')) {
                $table->dateTime('cancelled_at')->nullable()->after('sus_time');
            }
            if (!Schema::hasColumn('po_receives_line', 'cancelled_by')) {
                $table->string('cancelled_by', 100)->nullable()->after('cancelled_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('po_receives_line', function (Blueprint $table) {
            if (Schema::hasColumn('po_receives_line', 'cancelled_by')) {
                $table->dropColumn('cancelled_by');
            }
            if (Schema::hasColumn('po_receives_line', 'cancelled_at')) {
                $table->dropColumn('cancelled_at');
            }
        });
    }
};
