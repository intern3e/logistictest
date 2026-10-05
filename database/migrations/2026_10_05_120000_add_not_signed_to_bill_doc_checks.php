<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bill_doc_checks', function (Blueprint $t) {
            if (!Schema::hasColumn('bill_doc_checks', 'not_signed')) {
                $t->boolean('not_signed')->default(false)->after('not_found'); // พบเอกสารแต่ยังไม่ได้เซ็นบิล
            }
        });
    }

    public function down(): void
    {
        Schema::table('bill_doc_checks', function (Blueprint $t) {
            if (Schema::hasColumn('bill_doc_checks', 'not_signed')) $t->dropColumn('not_signed');
        });
    }
};
