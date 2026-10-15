<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * หมายเหตุการยกเลิกบิล (sync จาก ERP SOINVHD.InvCancRemark)
 *   คู่กับ tblbill.statuspdf = '6' (บิลถูกยกเลิก)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tblbill', function (Blueprint $t) {
            if (!Schema::hasColumn('tblbill', 'cancel_remark')) {
                $t->string('cancel_remark', 500)->nullable()->after('statuspdf');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tblbill', function (Blueprint $t) {
            if (Schema::hasColumn('tblbill', 'cancel_remark')) {
                $t->dropColumn('cancel_remark');
            }
        });
    }
};
