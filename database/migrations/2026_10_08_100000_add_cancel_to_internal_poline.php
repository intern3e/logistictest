<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ยกเลิกไส้ใน (รายบรรทัด) แบบ "ไม่ลบทิ้ง" — เก็บว่าใครยกเลิก/เมื่อไหร่ไว้เป็นหลักฐาน
 *   cancelled_at ว่าง = ยังใช้งาน / มีค่า = ถูกยกเลิก
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('internal_poline', function (Blueprint $t) {
            $t->dateTime('cancelled_at')->nullable()->after('picked_by');
            $t->string('cancelled_by', 100)->nullable()->after('cancelled_at');
        });
    }

    public function down(): void
    {
        Schema::table('internal_poline', function (Blueprint $t) {
            $t->dropColumn(['cancelled_at', 'cancelled_by']);
        });
    }
};
