<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class log_quotation extends Model
{
    protected $table = 'log_quotations';
    public $timestamps = false;

    protected $fillable = [
        'quotation_no', 'action',
        'field_changed', 'old_value', 'new_value', 'note', 'created_by',
    ];

    public static function record(
        string $quotationNo,
        string $action,
        string $note      = '',
        string $field     = '',
        string $oldVal    = '',
        string $newVal    = '',
        string $createdBy = ''
    ): void {
        static::create([
            'quotation_no'  => $quotationNo,
            'action'        => $action,
            'field_changed' => $field    ?: null,
            'old_value'     => $oldVal   ?: null,
            'new_value'     => $newVal   ?: null,
            'note'          => $note     ?: null,
            'created_by'    => $createdBy ?: (auth()->user()->name ?? 'system'),
        ]);
    }
}