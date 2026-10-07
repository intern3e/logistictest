<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoDocCheck extends Model
{
    protected $table = 'po_doc_checks';

    protected $fillable = [
        'po_id', 'so_id', 'vendor_name', 'customer_name',
        'has_document', 'doc_types',
        'note', 'checked_by', 'checked_at',
    ];

    protected $casts = [
        'has_document' => 'boolean',
        'checked_at'   => 'datetime',
    ];

    // ชนิดเอกสารที่รองรับ
    const DOC_TYPES = ['tax', 'receipt', 'delivery'];
}
