<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * สถานะ "ยกเลิกงานคนขับ" แบบ soft-cancel บน transaction_transport
     * เดิมยกเลิกแล้วลบแถวทิ้ง (ข้อมูลหาย) -> เปลี่ยนเป็นตั้ง cancelled_at/by ไว้
     * งานที่ cancelled จะไม่ถูกดึงไปแสดง (so/show, dashboarddoc, oil ฯลฯ) และคืนกลับไปหน้าจ่ายงานได้
     */
    public function up(): void
    {
        Schema::table('transaction_transport', function (Blueprint $table) {
            if (!Schema::hasColumn('transaction_transport', 'cancelled_at')) {
                $table->dateTime('cancelled_at')->nullable()->after('id_transport');
            }
            if (!Schema::hasColumn('transaction_transport', 'cancelled_by')) {
                $table->string('cancelled_by', 100)->nullable()->after('cancelled_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transaction_transport', function (Blueprint $table) {
            foreach (['cancelled_at', 'cancelled_by'] as $col) {
                if (Schema::hasColumn('transaction_transport', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
