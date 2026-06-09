<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class quotation extends Model
{
    protected $table = 'quotations';

    protected $fillable = [
        'quotation_no',
        'doc_date',
        'customer_code',
        'customer_company',
        'customer_address',
        'customer_tel',
        'customer_tax',
        'customer_branch',
        'contact_name',
        'valid_days',
        'expire_date',
        'credit_days',
        'note',
        'pdf_path',
        'gross_amount',
        'vat_amount',
        'grand_total',
        'status',
    ];

    protected $casts = [
        'doc_date'     => 'date',
        'expire_date'  => 'date',
        'valid_days'   => 'integer',
        'credit_days'  => 'integer',
        'gross_amount' => 'decimal:2',
        'vat_amount'   => 'decimal:2',
        'grand_total'  => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(QuotationItem::class, 'quotation_id')->orderBy('line_no');
    }

    /**
     * คำนวณยอดรวมจากรายการสินค้า
     */
    public function recalculate(): self
    {
        $gross = $this->items->sum(fn($item) => $item->qty * $item->unit_price);
        $vat   = $gross * 0.07;

        $this->gross_amount = round($gross, 2);
        $this->vat_amount   = round($vat, 2);
        $this->grand_total  = round($gross + $vat, 2);

        return $this;
    }

    /**
     * คำนวณ expire_date จาก doc_date + valid_days
     */
    public function calcExpireDate(): self
    {
        if ($this->doc_date && $this->valid_days > 0) {
            $this->expire_date = $this->doc_date->copy()->addDays($this->valid_days);
        }

        return $this;
    }

    /**
     * บันทึกไฟล์ PDF ลง storage/app/quotations
     *
     * @param  string  $base64Pdf  ข้อมูล PDF เป็น base64 (ส่งมาจากหน้าเว็บ)
     * @return self
     */
    /**
     * บันทึก PDF ลง storage/app/public/quotations/{quotation_no}.pdf
     */
    public function storePdf(string $base64Pdf): self
    {
        $binary   = base64_decode($base64Pdf);
        $filename = $this->quotation_no . '.pdf';
        $path     = 'quotations/' . $filename;

        \Illuminate\Support\Facades\Storage::disk('public')->put($path, $binary);

        $this->pdf_path = $path;
        $this->save();

        return $this;
    }

    /**
     * URL สำหรับดาวน์โหลด PDF  →  /storage/quotations/QT-202506-0001.pdf
     */
    public function getPdfUrlAttribute(): ?string
    {
        return $this->pdf_path
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($this->pdf_path)
            : null;
    }

    /**
     * Full path  →  C:\laragon\www\logistic\storage\app\public\quotations\QT-202506-0001.pdf
     */
    public function getPdfFullPathAttribute(): ?string
    {
        return $this->pdf_path
            ? \Illuminate\Support\Facades\Storage::disk('public')->path($this->pdf_path)
            : null;
    }

    public function hasPdf(): bool
    {
        return $this->pdf_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->pdf_path);
    }

    public function deletePdf(): self
    {
        if ($this->hasPdf()) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($this->pdf_path);
        }
        $this->pdf_path = null;
        return $this;
    }
}