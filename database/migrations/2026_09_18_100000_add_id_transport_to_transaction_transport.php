<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * เลขขนส่ง (tracking number) ของงานขนส่งเอกชน
     * บันทึกจากปุ่ม "เพิ่มเลขขนส่ง" ในหน้า delivery-summary
     */
    public function up(): void
    {
        Schema::table('transaction_transport', function (Blueprint $table) {
            if (!Schema::hasColumn('transaction_transport', 'id_transport')) {
                $table->string('id_transport', 100)->nullable()->after('delivery_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transaction_transport', function (Blueprint $table) {
            if (Schema::hasColumn('transaction_transport', 'id_transport')) {
                $table->dropColumn('id_transport');
            }
        });
    }
};
