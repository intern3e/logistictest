<?php

/**
 * ค่าเชื่อมต่อบริการ OCR เอกสาร (เครื่อง 192.168.1.148)
 *
 * NOTE: ไฟล์นี้อ่านจาก .env พร้อม "ค่า default" ในตัว — ถ้าไม่ตั้งใน .env ก็ใช้ค่าด้านล่างได้เลย
 * (ไม่ต้องแก้ .env) ถ้าต้องเปลี่ยน host/port/key ภายหลัง ค่อยเพิ่มใน .env ทับ
 */
return [

    // endpoint สกัดข้อมูลใบกำกับ/บิล: รับ PDF -> คืน JSON (company, address, items, subtotal, vat, total, po_number)
    'invoice_url' => env('OCR_INVOICE_URL', 'http://192.168.1.148:8020/ocr-invoice'),

    // api-key (ถ้า service ตั้งไว้) — ส่งเป็น header X-API-Key
    'api_key' => env('OCR_API_KEY', ''),

    // วินาที — OCR หลายหน้าอาจนาน
    'timeout' => (int) env('OCR_TIMEOUT', 300),

    // ตัวแยกหน้า PDF (poppler) — ถ้าเว้นว่างจะ auto-detect จาก PATH / โฟลเดอร์ WinGet
    'pdfseparate' => env('PDFSEPARATE_BIN', ''),

];
