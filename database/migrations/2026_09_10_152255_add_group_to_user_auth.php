<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_auth', function (Blueprint $table) {
            if (!Schema::hasColumn('user_auth', 'group')) {
                // group = ชื่อ Sale ที่เป็น "กลุ่ม" ของพนักงานคนนี้ (ใช้กรองสิทธิ์เห็นงานในหน้า ชั้น SALE)
                $table->string('group')->nullable()->after('page');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_auth', function (Blueprint $table) {
            if (Schema::hasColumn('user_auth', 'group')) {
                $table->dropColumn('group');
            }
        });
    }
};
