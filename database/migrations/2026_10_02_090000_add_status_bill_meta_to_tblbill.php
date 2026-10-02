<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tblbill', function (Blueprint $table) {
            if (!Schema::hasColumn('tblbill', 'status_bill_by')) {
                $table->string('status_bill_by', 191)->nullable()->after('status_bill');
            }
            if (!Schema::hasColumn('tblbill', 'status_bill_time')) {
                $table->timestamp('status_bill_time')->nullable()->after('status_bill_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tblbill', function (Blueprint $table) {
            if (Schema::hasColumn('tblbill', 'status_bill_by')) $table->dropColumn('status_bill_by');
            if (Schema::hasColumn('tblbill', 'status_bill_time')) $table->dropColumn('status_bill_time');
        });
    }
};
