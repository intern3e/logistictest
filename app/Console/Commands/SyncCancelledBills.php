<?php

namespace App\Console\Commands;

use App\Services\BillCancellation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * ซิงก์สถานะ "บิลถูกยกเลิก" จาก ERP (SOINVHD.DocuStatus='C')
 *   -> tblbill.statuspdf='6' + cancel_remark (หมายเหตุการยกเลิก)
 * ทุกหน้าที่เช็ค statuspdf='6' จะขึ้น "ยกเลิก" ให้เองหลังซิงก์
 */
class SyncCancelledBills extends Command
{
    protected $signature = 'bills:sync-cancelled {--days=120 : ย้อนหลังกี่วัน (อิง tblbill.time)} {--chunk=500}';

    protected $description = 'ซิงก์บิลที่ถูกยกเลิกจาก ERP (SOINVHD.DocuStatus=C) -> tblbill.statuspdf=6 + cancel_remark';

    public function handle(): int
    {
        $days  = max(1, (int) $this->option('days'));
        $chunk = max(50, (int) $this->option('chunk'));
        $since = now()->subDays($days)->toDateTimeString();

        // เฉพาะบิลที่ยัง "ไม่ถูกมาร์คยกเลิก" ในช่วงเวลาที่กำหนด (ลดภาระ)
        $bills = DB::table('tblbill')
            ->whereNotNull('billid')->where('billid', '<>', '')
            ->where(function ($w) {
                $w->where('statuspdf', '!=', BillCancellation::CANCELLED_STATUSPDF)
                  ->orWhereNull('statuspdf');
            })
            ->where('time', '>=', $since)
            ->orderByDesc('time')
            ->pluck('billid');

        $this->info("ตรวจบิล {$bills->count()} ใบ (ย้อนหลัง {$days} วัน)...");

        $cancelledTotal = 0;
        foreach ($bills->chunk($chunk) as $group) {
            $res = BillCancellation::sync($group->all());
            $cancelledTotal += count($res);
        }

        $this->info("เสร็จ: พบ+ซิงก์บิลยกเลิก {$cancelledTotal} ใบ");

        // เติมหมายเหตุให้บิลที่มาร์คยกเลิกไว้แล้วแต่หมายเหตุยังว่าง (ดึงจาก SOInvHDRemark / log ERP)
        $needRemark = DB::table('tblbill')
            ->where('statuspdf', BillCancellation::CANCELLED_STATUSPDF)
            ->where(function ($q) { $q->whereNull('cancel_remark')->orWhere('cancel_remark', ''); })
            ->whereNotNull('billid')->where('billid', '<>', '')
            ->where('time', '>=', $since)
            ->pluck('billid');
        $this->info("เติมหมายเหตุบิลยกเลิก {$needRemark->count()} ใบ...");
        foreach ($needRemark->chunk($chunk) as $group) {
            BillCancellation::sync($group->all());
        }
        $this->info('เติมหมายเหตุเสร็จ');
        return self::SUCCESS;
    }
}
