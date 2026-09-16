<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * รองรับ "รับเข้าแยกตาม SO": 1 PO ที่เชื่อมหลาย SO ให้เก็บ po_receives ได้หลายแถว (แถวละ SO)
     * - ยกเลิก unique(po_id) เดิม -> ใช้ unique(po_id, so_id) แทน
     * - เพิ่ม so_id ให้ po_receives_line เพื่อแยกไส้ในต่อ SO (checkout/ประวัติแยกได้)
     */
    public function up(): void
    {
        // 1) ยกเลิก unique po_id เดิม
        try {
            DB::statement('ALTER TABLE po_receives DROP INDEX uq_po_id');
        } catch (\Throwable $e) {
            // ถ้าไม่มีชื่อ index นี้แล้วก็ข้าม
        }

        // 2) unique ใหม่ (po_id, so_id) — กันรับเข้า SO เดิมซ้ำ
        try {
            DB::statement('ALTER TABLE po_receives ADD UNIQUE uq_po_so (po_id, so_id)');
        } catch (\Throwable $e) {
            // ถ้ามีข้อมูลเดิมชนกันจะข้าม (ค่อยจัดการภายหลัง)
        }

        // 3) so_id ให้ po_receives_line
        Schema::table('po_receives_line', function (Blueprint $table) {
            if (!Schema::hasColumn('po_receives_line', 'so_id')) {
                $table->string('so_id', 50)->nullable()->after('po_id');
                $table->index(['po_id', 'so_id'], 'idx_po_so');
            }
        });
    }

    public function down(): void
    {
        Schema::table('po_receives_line', function (Blueprint $table) {
            if (Schema::hasColumn('po_receives_line', 'so_id')) {
                $table->dropIndex('idx_po_so');
                $table->dropColumn('so_id');
            }
        });
        try { DB::statement('ALTER TABLE po_receives DROP INDEX uq_po_so'); } catch (\Throwable $e) {}
        try { DB::statement('ALTER TABLE po_receives ADD UNIQUE uq_po_id (po_id)'); } catch (\Throwable $e) {}
    }
};
