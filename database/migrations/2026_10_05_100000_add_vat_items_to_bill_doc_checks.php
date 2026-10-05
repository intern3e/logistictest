<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bill_doc_checks', function (Blueprint $t) {
            if (!Schema::hasColumn('bill_doc_checks', 'amount_before_vat')) {
                $t->decimal('amount_before_vat', 15, 2)->nullable()->after('amount'); // ยอดก่อน VAT
            }
            if (!Schema::hasColumn('bill_doc_checks', 'vat_amount')) {
                $t->decimal('vat_amount', 15, 2)->nullable()->after('amount_before_vat'); // ภาษี VAT
            }
            if (!Schema::hasColumn('bill_doc_checks', 'items')) {
                $t->longText('items')->nullable()->after('cancel_reason'); // รายการสินค้า (JSON: name/qty/amount)
            }
        });
    }

    public function down(): void
    {
        Schema::table('bill_doc_checks', function (Blueprint $t) {
            foreach (['amount_before_vat', 'vat_amount', 'items'] as $c) {
                if (Schema::hasColumn('bill_doc_checks', $c)) $t->dropColumn($c);
            }
        });
    }
};
