<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * note "รอของเข้า" รายตัว (ต่อ line) — ติ๊กรายแถวจากหน้า so/show (server_update)
 *   ไปแสดงเป็น note รายบรรทัดบนหน้า internal_po/dashboard
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('internal_poline', function (Blueprint $t) {
            $t->boolean('wait_goods')->default(false)->after('item_total');
        });
    }

    public function down(): void
    {
        Schema::table('internal_poline', function (Blueprint $t) {
            $t->dropColumn('wait_goods');
        });
    }
};
