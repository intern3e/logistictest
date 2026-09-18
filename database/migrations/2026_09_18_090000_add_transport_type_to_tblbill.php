<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ประเภทการขนส่งของบิล (เลือกตอน "จัดบิล" ในหน้า store_checkout)
     *   'company' = ขนส่งโดยรถบริษัท / 'private' = บริษัทขนส่ง(เอกชน)
     * ใช้แยก tab ในหน้า delivery / delivery-summary
     */
    public function up(): void
    {
        Schema::table('tblbill', function (Blueprint $table) {
            if (!Schema::hasColumn('tblbill', 'transport_type')) {
                $table->string('transport_type', 20)->nullable()->after('picker_time');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tblbill', function (Blueprint $table) {
            if (Schema::hasColumn('tblbill', 'transport_type')) {
                $table->dropColumn('transport_type');
            }
        });
    }
};
