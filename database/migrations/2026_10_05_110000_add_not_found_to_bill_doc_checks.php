<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bill_doc_checks', function (Blueprint $t) {
            if (!Schema::hasColumn('bill_doc_checks', 'not_found')) {
                $t->boolean('not_found')->default(false)->after('has_document'); // ยืนยันหาแล้วไม่พบบิล
            }
        });
    }

    public function down(): void
    {
        Schema::table('bill_doc_checks', function (Blueprint $t) {
            if (Schema::hasColumn('bill_doc_checks', 'not_found')) $t->dropColumn('not_found');
        });
    }
};
