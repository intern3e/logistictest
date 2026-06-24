<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class quotation extends Model
{
    protected $table = 'quotations';

    protected $fillable = [
        'quotation_no', 'doc_date', 'customer_code', 'customer_company',
        'customer_address', 'customer_tel', 'customer_tax', 'customer_branch',
        'contact_name', 'valid_days', 'expire_date', 'credit_days', 'note',
        'pdf_path', 'gross_amount', 'vat_amount', 'grand_total',
        'status', 'emp_name',
    ];

    protected $casts = [
        'doc_date'     => 'date:Y-m-d',   
        'expire_date'  => 'date:Y-m-d',
        'valid_days'   => 'integer',
        'credit_days'  => 'integer',
        'gross_amount' => 'decimal:2',
        'vat_amount'   => 'decimal:2',
        'grand_total'  => 'decimal:2',
    ];
    
// ✅ แก้
public function logs()
{
    return $this->hasMany(log_quotation::class, 'quotation_no', 'quotation_no')->orderByDesc('created_at');
}

// ✅ items() ด้วย
public function items()
{
    return $this->hasMany(quotationItem::class, 'quotation_no', 'quotation_no')->orderBy('line_no');
}
    public function recalculate(): self
    {
        $gross = $this->items->sum(fn($item) => $item->qty * $item->unit_price);
        $vat   = $gross * 0.07;
        $this->gross_amount = round($gross, 2);
        $this->vat_amount   = round($vat, 2);
        $this->grand_total  = round($gross + $vat, 2);
        return $this;
    }

    public function storePdf(string $base64Pdf): self
    {
        $binary  = base64_decode($base64Pdf);
        $path    = 'quotations/' . $this->quotation_no . '.pdf';
        \Illuminate\Support\Facades\Storage::disk('public')->put($path, $binary);
        $this->pdf_path = $path;
        $this->save();
        return $this;
    }

    public function getPdfUrlAttribute(): ?string
    {
        return $this->pdf_path
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($this->pdf_path)
            : null;
    }

    public function getPdfFullPathAttribute(): ?string
    {
        return $this->pdf_path
            ? \Illuminate\Support\Facades\Storage::disk('public')->path($this->pdf_path)
            : null;
    }

    public function hasPdf(): bool
    {
        return $this->pdf_path
            && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->pdf_path);
    }
}