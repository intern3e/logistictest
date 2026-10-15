<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * เอกสาร PO จากซัพพลายเออร์ (สแกนมาเป็น PDF) :
 *   - อัปโหลดเป็นโฟลเดอร์ -> เก็บไฟล์ -> ส่ง OCR (เครื่อง .148)
 *   - OCR สกัด: ชื่อบริษัท/ที่อยู่/รายการสินค้า/vat/ยอดรวม + เลข PO ที่พบในเอกสาร
 *   - จับคู่เข้า PO (หลัก=เลข PO ในเอกสาร, สำรอง=ชื่อซัพ+ยอดรวม) ; คนยืนยัน/แก้ได้
 * เก็บในฐาน logistic
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('po_supplier_docs', function (Blueprint $t) {
            $t->id();

            // ไฟล์
            $t->string('original_name', 255);              // ชื่อไฟล์เดิมที่อัปมา
            $t->string('rel_folder', 255)->nullable();     // โฟลเดอร์ย่อยที่ผู้ใช้เลือกมา (จาก webkitRelativePath)
            $t->string('stored_path', 255);                // path PDF ใน storage/app/public
            $t->string('txt_path', 255)->nullable();       // path .txt ผล OCR
            $t->string('file_hash', 64)->nullable();       // sha1 กันอัปซ้ำ

            // ผล OCR (ข้อมูลบนเอกสารซัพ)
            $t->string('ocr_po_number', 100)->nullable();  // เลข PO ที่อ่านได้จากเอกสาร
            $t->string('vendor_name', 255)->nullable();    // ชื่อบริษัท/ซัพ
            $t->text('vendor_address')->nullable();        // ที่อยู่
            $t->decimal('subtotal', 15, 2)->nullable();    // ราคาก่อน vat
            $t->decimal('vat', 15, 2)->nullable();         // vat
            $t->decimal('total', 15, 2)->nullable();       // ราคารวม vat
            $t->longText('items_json')->nullable();        // รายการสินค้า [{name,qty,price}]
            $t->longText('ocr_text')->nullable();          // ข้อความ txt ที่จัดระเบียบแล้ว (เก็บไว้แสดงเร็ว)

            // สถานะ OCR
            $t->string('ocr_status', 20)->default('pending');  // pending|processing|done|error
            $t->text('ocr_error')->nullable();

            // การจับคู่ PO
            $t->string('po_id', 50)->nullable();           // PO ที่จับคู่แล้ว
            $t->string('so_id', 50)->nullable();
            $t->string('match_status', 20)->default('unmatched');  // unmatched|auto|manual
            $t->string('match_confidence', 20)->nullable();        // high|low (เดา) / เว้นว่างถ้า manual

            // อื่น ๆ
            $t->string('uploaded_by', 100)->nullable();
            $t->text('note')->nullable();
            $t->timestamps();

            $t->index('ocr_status');
            $t->index('match_status');
            $t->index('po_id');
            $t->index('file_hash');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('po_supplier_docs');
    }
};
