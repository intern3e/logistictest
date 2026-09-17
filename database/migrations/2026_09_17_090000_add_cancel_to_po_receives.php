<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * รองรับ "ยกเลิกการรับเข้า (รับเข้าผิด)" แบบ soft-cancel ที่ระดับ header:
     * - เพิ่ม cancelled_at / cancelled_by ให้ po_receives (เก็บประวัติ ไม่ลบจริง)
     * - ยกเลิก unique(po_id, so_id) เดิม เพราะตอนนี้ 1 (po_id, so_id) มีได้หลายแถว
     *   (แถวเก่าที่ถูกยกเลิก + แถวใหม่ที่รับเข้าใหม่) -> แทนด้วย index ธรรมดาไว้ค้นหา
     */
    public function up(): void
    {
        Schema::table('po_receives', function (Blueprint $table) {
            if (!Schema::hasColumn('po_receives', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('checkout_time');
            }
            if (!Schema::hasColumn('po_receives', 'cancelled_by')) {
                $table->string('cancelled_by', 100)->nullable()->after('cancelled_at');
            }
        });

        // ยกเลิก unique(po_id, so_id) -> อนุญาตให้มีแถวซ้ำ (เก่าที่ยกเลิก + ใหม่)
        try {
            DB::statement('ALTER TABLE po_receives DROP INDEX uq_po_so');
        } catch (\Throwable $e) {
            // ไม่มี index ชื่อนี้ก็ข้าม
        }

        // index ธรรมดาไว้ค้นหาเร็ว (ถ้ายังไม่มี)
        try {
            DB::statement('ALTER TABLE po_receives ADD INDEX idx_po_so_receives (po_id, so_id)');
        } catch (\Throwable $e) {
            // มีอยู่แล้วก็ข้าม
        }
    }

    public function down(): void
    {
        try { DB::statement('ALTER TABLE po_receives DROP INDEX idx_po_so_receives'); } catch (\Throwable $e) {}

        Schema::table('po_receives', function (Blueprint $table) {
            if (Schema::hasColumn('po_receives', 'cancelled_by')) {
                $table->dropColumn('cancelled_by');
            }
            if (Schema::hasColumn('po_receives', 'cancelled_at')) {
                $table->dropColumn('cancelled_at');
            }
        });
    }
};
