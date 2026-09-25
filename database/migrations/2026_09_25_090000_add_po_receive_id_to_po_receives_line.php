<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * เพิ่ม po_receive_id ให้ po_receives_line เพื่อระบุว่า line นี้อยู่ "รอบรับเข้า" (header) ไหน
 * รองรับเคส: PO รับมาบางส่วน -> เช็คเอาท์ไปแล้ว -> ของมาใหม่ = สร้าง header รอบใหม่ (เช็คเอาท์ได้อีกรอบ)
 * (ของเดิม backfill ให้ชี้ header ตาม po_id + so_id)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('po_receives_line', function (Blueprint $table) {
            if (!Schema::hasColumn('po_receives_line', 'po_receive_id')) {
                $table->unsignedBigInteger('po_receive_id')->nullable()->index()->after('so_id');
            }
        });

        // backfill: จับคู่ line -> header ด้วย po_id + so_id (NULL-safe) ก่อน แล้วค่อย po_id อย่างเดียว
        DB::statement("
            UPDATE po_receives_line l
            JOIN po_receives r ON r.po_id = l.po_id AND r.so_id <=> l.so_id
            SET l.po_receive_id = r.id
            WHERE l.po_receive_id IS NULL
        ");
        DB::statement("
            UPDATE po_receives_line l
            JOIN po_receives r ON r.po_id = l.po_id
            SET l.po_receive_id = r.id
            WHERE l.po_receive_id IS NULL
        ");
    }

    public function down(): void
    {
        Schema::table('po_receives_line', function (Blueprint $table) {
            $table->dropColumn('po_receive_id');
        });
    }
};
