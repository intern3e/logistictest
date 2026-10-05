<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * บันทึกการตรวจเอกสารบิล vs ข้อมูลในระบบ (งานบัญชี)
 * เก็บในฐาน logistic (default connection)
 */
class BillDocCheck extends Model
{
    protected $table = 'bill_doc_checks';

    protected $fillable = [
        'period', 'bill_no', 'so_no',
        'bill_type', 'bill_date', 'customer_id', 'customer_name',
        'amount', 'amount_before_vat', 'vat_amount',
        'cancelled', 'sys_status', 'cancel_reason', 'items', 'in_system',
        'has_document', 'not_found', 'not_signed', 'match_status', 'check_source', 'confidence',
        'doc_amount', 'doc_date', 'doc_customer', 'doc_file',
        'note', 'checked_by', 'checked_at',
    ];

    protected $casts = [
        'bill_date'    => 'date',
        'doc_date'     => 'date',
        'checked_at'   => 'datetime',
        'amount'            => 'decimal:2',
        'amount_before_vat' => 'decimal:2',
        'vat_amount'        => 'decimal:2',
        'items'             => 'array',
        'doc_amount'   => 'decimal:2',
        'confidence'   => 'decimal:2',
        'cancelled'    => 'boolean',
        'in_system'    => 'boolean',
        'has_document' => 'boolean',
        'not_found'    => 'boolean',
        'not_signed'   => 'boolean',
    ];

    // ประเภทบิล
    const TYPE_GOODS   = 'สินค้า';
    const TYPE_SERVICE = 'บริการ';

    // สถานะการจับคู่
    const M_PENDING      = 'pending';       // ยังไม่ตรวจ
    const M_MATCHED      = 'matched';       // มีเอกสาร + ข้อมูลตรง
    const M_MISMATCH     = 'mismatch';      // มีเอกสารแต่ข้อมูลไม่ตรง
    const M_MISSING_DOC  = 'missing_doc';   // ในระบบมี แต่ไม่มีเอกสาร
    const M_DOC_NO_SYS   = 'doc_no_system'; // มีเอกสารแต่ไม่มีในระบบ
}
