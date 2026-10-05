<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ตารางตรวจเอกสารบิลกับข้อมูลในระบบ (งานบัญชี)
 *   - 1 แถว = 1 เลขบิลของเดือนที่ตรวจ (period)
 *   - ข้อมูลฝั่งระบบ (so_no / amount / customer / type) sync มาจาก ERP/so
 *   - ฝั่งเอกสาร (has_document / doc_* ) มาจากคนติ๊ก หรือ bot/OCR ภายนอกยิงเข้ามา
 *   เก็บในฐาน logistic (default connection)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bill_doc_checks', function (Blueprint $t) {
            $t->id();

            // ---- คีย์หลักเชิงธุรกิจ ----
            $t->string('period', 7)->index();            // เดือนที่ตรวจ: YYYY-MM
            $t->string('bill_no', 50);                   // เลขบิล / เลขที่ใบกำกับ
            $t->string('so_no', 50)->nullable();

            // ---- ข้อมูลฝั่งระบบ (sync จาก ERP) ----
            $t->string('bill_type', 20)->nullable();     // 'สินค้า' | 'บริการ' | null = ยังไม่ระบุ
            $t->date('bill_date')->nullable();           // วันที่บิลในระบบ
            $t->string('customer_id', 50)->nullable();
            $t->string('customer_name', 255)->nullable();
            $t->decimal('amount', 15, 2)->nullable();    // ยอดเงินในระบบ (รวม VAT)
            $t->boolean('cancelled')->default(false);    // บิลถูกยกเลิกใน ERP (DocuStatus = C)
            $t->boolean('in_system')->default(true);     // มีอยู่ในระบบไหม (false = เจอเฉพาะเอกสาร)

            // ---- ฝั่งเอกสาร / ผลการตรวจ ----
            $t->boolean('has_document')->default(false); // พบเอกสารแล้ว (ติ๊ก)
            // pending = ยังไม่ตรวจ, matched = มีเอกสาร+ข้อมูลตรง, mismatch = มีเอกสารแต่ไม่ตรง,
            // missing_doc = ในระบบมีแต่ไม่มีเอกสาร, doc_no_system = มีเอกสารแต่ไม่มีในระบบ
            $t->string('match_status', 30)->default('pending')->index();
            $t->string('check_source', 20)->nullable();  // manual | bot | ocr
            $t->decimal('confidence', 5, 2)->nullable(); // ความมั่นใจ OCR (0-100) ถ้ามาจาก bot

            // ข้อมูลที่อ่านได้จากเอกสาร (ใช้เทียบกับฝั่งระบบ)
            $t->decimal('doc_amount', 15, 2)->nullable();
            $t->date('doc_date')->nullable();
            $t->string('doc_customer', 255)->nullable();
            $t->string('doc_file', 255)->nullable();     // path/ref ไฟล์ PDF ถ้ามี

            $t->text('note')->nullable();
            $t->string('checked_by', 100)->nullable();
            $t->timestamp('checked_at')->nullable();

            $t->timestamps();

            $t->unique(['period', 'bill_no']);           // 1 เลขบิล ต่อ 1 เดือน
            $t->index(['period', 'bill_type']);
            $t->index(['period', 'has_document']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bill_doc_checks');
    }
};
