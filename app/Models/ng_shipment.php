<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ng_shipment extends Model
{
    use HasFactory;

    protected $table = 'ng_shipment';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'so_id',
        'bill_no',
        'driver_name',
        'seller_name',
        'customer_name',
        'bill_in_by',
        'ng_date',
        'note',
        'status',
        'resolved_date',
        'new_bill_no',
    ];

    protected $casts = [
        'ng_date'       => 'date:Y-m-d',
        'resolved_date' => 'date:Y-m-d',
    ];

    public function scopePending($query)
    {
        return $query->where('status', 'ng');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public static function syncDay(string $date, array $ngJobs, array $okBillNos): array
    {
        $inserted  = 0;
        $resolved  = 0;

        $okBillNos = array_map('strval', $okBillNos);

        if (!empty($okBillNos)) {
            $resolved = static::whereIn('status', ['ng', 'pending'])
                ->where(function ($q) use ($okBillNos) {
                    $q->whereIn('bill_no', $okBillNos)
                      ->orWhere(function ($q2) use ($okBillNos) {
                          $q2->whereNotNull('new_bill_no')
                             ->whereIn('new_bill_no', $okBillNos);
                      });
                })
                ->update([
                    'status'        => 'completed',
                    'resolved_date' => $date,
                ]);
        }

        foreach ($ngJobs as $job) {
            $billNo = trim((string) ($job['bill_no'] ?? ''));
            if (!$billNo) continue;

            $exists = static::where('bill_no', $billNo)
                            ->where('ng_date', $date)
                            ->exists();
            if ($exists) continue;

            static::create([
                'so_id'         => trim((string) ($job['so_id'] ?? '')) ?: null,
                'bill_no'       => $billNo,
                'driver_name'   => trim($job['driver_name']   ?? ''),
                'seller_name'   => trim($job['seller_name']   ?? '') ?: null,
                'bill_in_by'    => $job['bill_in_by']    ?? null,
                'customer_name' => trim($job['customer_name'] ?? '') ?: null,
                'ng_date'       => $date,
                'note'          => trim($job['note'] ?? '') ?: null,
                'status'        => 'ng',
            ]);

            $inserted++;
        }

        return compact('inserted', 'resolved');
    }
}