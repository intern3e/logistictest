<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * เก็บ "เลข PO เดิมจากระบบเก่า" ไว้ใน internal_po
 *  - ปกติ internal_id = เลข PO จริง (เช่น 6907-A0115)
 *  - แต่ถ้าเลข PO ภายในของระบบเก่าชนกับงานที่มีอยู่แล้วในระบบใหม่ (คนละงาน/คนละ SO)
 *    ตอนดึงเข้าระบบใหม่จะออก internal_id ใหม่ให้อัตโนมัติ (กัน PK ซ้ำ) แล้วเก็บเลขเดิมไว้ที่ legacy_po
 *  - ใช้ legacy_po + SO_id เป็นตัวระบุว่า "งานจากระบบเก่าใบนี้ถูกดึงเข้ามาแล้ว"
 *    เพื่อไม่ให้งานเดิมโผล่ให้กดซ้ำ และให้งานที่เลขชนแต่คนละ SO ยังกดได้
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('internal_po', function (Blueprint $table) {
            $table->string('legacy_po', 50)->nullable()->after('internal_id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('internal_po', function (Blueprint $table) {
            $table->dropColumn('legacy_po');
        });
    }
};
