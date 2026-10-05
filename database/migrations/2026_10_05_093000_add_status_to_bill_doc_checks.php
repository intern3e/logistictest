<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bill_doc_checks', function (Blueprint $t) {
            if (!Schema::hasColumn('bill_doc_checks', 'sys_status')) {
                $t->string('sys_status', 10)->nullable()->after('cancelled');   // DocuStatus ดิบจาก ERP (Y/P/N/C)
            }
            if (!Schema::hasColumn('bill_doc_checks', 'cancel_reason')) {
                $t->string('cancel_reason', 255)->nullable()->after('sys_status'); // เหตุผลยกเลิก (InvCancRemark)
            }
        });
    }

    public function down(): void
    {
        Schema::table('bill_doc_checks', function (Blueprint $t) {
            if (Schema::hasColumn('bill_doc_checks', 'sys_status')) $t->dropColumn('sys_status');
            if (Schema::hasColumn('bill_doc_checks', 'cancel_reason')) $t->dropColumn('cancel_reason');
        });
    }
};
