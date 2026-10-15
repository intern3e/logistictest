<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * เอกสาร PO จากซัพ (สแกน PDF) + ผล OCR + การจับคู่เข้า PO
 */
class PoSupplierDoc extends Model
{
    protected $table = 'po_supplier_docs';

    protected $fillable = [
        'original_name', 'rel_folder', 'stored_path', 'txt_path', 'file_hash',
        'ocr_po_number', 'invoice_no', 'vendor_name', 'vendor_address',
        'subtotal', 'vat', 'total', 'items_json', 'ocr_text',
        'ocr_status', 'ocr_error',
        'po_id', 'so_id', 'match_status', 'match_confidence',
        'uploaded_by', 'note',
        'page_count', 'pages_done', 'pages_json',   // OCR รายหน้า: 1 ไฟล์มีหลายหน้า (หลายบิล)
    ];

    protected $casts = [
        'subtotal' => 'float',
        'vat'      => 'float',
        'total'    => 'float',
    ];

    const OCR_PENDING    = 'pending';
    const OCR_PROCESSING = 'processing';
    const OCR_DONE       = 'done';
    const OCR_ERROR      = 'error';

    const MATCH_NONE   = 'unmatched';
    const MATCH_AUTO   = 'auto';
    const MATCH_MANUAL = 'manual';
}
