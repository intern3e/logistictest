<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * เช็คเอกสารของ PO (จากหน้า mobile_app) :
 *   - has_document = ได้รับเอกสารไหม
 *   - เลือกชนิดเอกสารที่ได้: ใบกำกับภาษี / ใบเสร็จ / ใบส่งของ (เลือกหรือไม่เลือกก็ได้)
 * เก็บในฐาน logistic
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('po_doc_checks', function (Blueprint $t) {
            $t->id();
            $t->string('po_id', 50)->unique();      // เลข PO
            $t->string('so_id', 50)->nullable();
            $t->string('vendor_name', 255)->nullable();
            $t->string('customer_name', 255)->nullable();

            $t->boolean('has_document')->default(false);     // ได้รับเอกสารไหม
            // ชนิดเอกสารที่ได้รับ เก็บคอลัมเดียวแบบ csv เช่น "tax,receipt,delivery" (tax=ใบกำกับภาษี, receipt=ใบเสร็จ, delivery=ใบส่งของ)
            $t->string('doc_types', 100)->nullable();

            $t->text('note')->nullable();
            $t->string('checked_by', 100)->nullable();
            $t->timestamp('checked_at')->nullable();
            $t->timestamps();

            $t->index('has_document');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('po_doc_checks');
    }
};
