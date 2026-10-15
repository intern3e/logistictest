<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * ตรวจ/ซิงก์สถานะ "บิลถูกยกเลิก" จาก ERP (SOINVHD.DocuStatus = 'C')
 *
 * แนวทาง: sync เข้าฐาน logistic แล้วทุกหน้าเช็ค local — และ "ยิง ERP ให้น้อยที่สุด"
 *   - source of truth: ERP SOINVHD (INVNO = เลขบิล = tblbill.billid), DocuStatus='C' = ยกเลิก
 *   - local marker:    tblbill.statuspdf = '6' (คอนเวนชันเดิม) + tblbill.cancel_remark (หมายเหตุการยกเลิก)
 *
 * กัน ERP โดนยิงรัว (ผู้ใช้ 100-150 คนพร้อมกัน):
 *   1) บิลที่ "รู้แล้วว่ายกเลิก" (statuspdf='6' local) -> ไม่ถาม ERP เลย
 *   2) บิลที่ "เพิ่งเช็คว่าปกติ" (cache TTL) -> ไม่ถาม ERP ซ้ำจน cache หมดอายุ
 *   3) เหลือเฉพาะบิลที่ "ยังไม่รู้" เท่านั้นที่ถาม ERP (ครั้งเดียวต่อ TTL)
 *   => หลัง warm-up แทบไม่แตะ ERP
 */
class BillCancellation
{
    public const ERP_CONNECTION       = 'mssql_account03';
    public const CANCELLED_STATUSPDF  = '6';
    private const CACHE_PREFIX_OK      = 'billcancel_ok:';   // บิลที่เช็คแล้วว่า "ไม่ยกเลิก"
    private const CACHE_TTL            = 600;                 // 10 นาที
    private const CACHE_PREFIX_RMK     = 'billcancel_rmk:';  // บิลยกเลิกที่ลองหาหมายเหตุแล้ว (กันยิง ERP ซ้ำ)
    private const RMK_TTL              = 86400;               // 1 วัน

    /**
     * ตรวจบิลยกเลิก (+ sync statuspdf='6' + หมายเหตุ) โดยยิง ERP ให้น้อยที่สุด
     *
     * @param  iterable $billNos เลขบิล (= INVNO / tblbill.billid)
     * @return array<string,string> [เลขบิล(trim) => หมายเหตุการยกเลิก] เฉพาะที่ยกเลิก
     */
    public static function sync($billNos): array
    {
        $billNos = collect($billNos)
            ->map(fn ($b) => trim((string) $b))
            ->filter()
            ->unique()
            ->values()
            ->all();
        if (empty($billNos)) {
            return [];
        }

        $result     = [];   // [billNo => remark]
        $needRemark = [];   // บิลยกเลิกที่ยังไม่มีหมายเหตุ -> ไปหาจาก ERP (InvCancRemark / log SMAudit)

        // 1) รู้แล้วว่ายกเลิก (local statuspdf='6') -> ไม่ต้องถามสถานะ ERP
        $knownCancelled = [];
        try {
            foreach (
                DB::table('tblbill')
                    ->whereIn('billid', $billNos)
                    ->where('statuspdf', self::CANCELLED_STATUSPDF)
                    ->get(['billid', 'cancel_remark']) as $r
            ) {
                $no = trim((string) $r->billid);
                $rm = trim((string) ($r->cancel_remark ?? ''));
                $result[$no]          = $rm;
                $knownCancelled[$no]  = true;
                if ($rm === '' && Cache::get(self::CACHE_PREFIX_RMK . $no) === null) {
                    $needRemark[] = $no;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('BillCancellation local lookup failed: ' . $e->getMessage());
        }

        // 2) ที่เหลือ: ตัดตัวที่เพิ่งเช็คว่า "ไม่ยกเลิก" (cache) ออก
        $toCheck = [];
        foreach ($billNos as $no) {
            if (isset($knownCancelled[$no])) {
                continue;
            }
            if (Cache::get(self::CACHE_PREFIX_OK . $no) !== null) {
                continue;   // เพิ่งเช็คแล้วว่า active -> ข้าม
            }
            $toCheck[] = $no;
        }

        if (!empty($toCheck)) {
            // 3) ถาม ERP เฉพาะบิลที่ยังไม่รู้สถานะ
            $erpCancelled = [];
            $erpOk = true;
            try {
                foreach (
                    DB::connection(self::ERP_CONNECTION)->table('SOINVHD')
                        ->whereIn('INVNO', $toCheck)
                        ->where('DocuStatus', 'C')
                        ->get(['INVNO', 'InvCancRemark']) as $r
                ) {
                    $no = trim((string) $r->INVNO);
                    if ($no === '') {
                        continue;
                    }
                    $erpCancelled[$no] = trim((string) ($r->InvCancRemark ?? ''));
                }
            } catch (\Throwable $e) {
                // ERP ล่ม -> คืนเท่าที่รู้จาก local (fail-safe ไม่ทำทั้งระบบล่มตาม)
                Log::warning('BillCancellation ERP query failed: ' . $e->getMessage());
                $erpOk = false;
            }

            // 4) เขียนผล: ยกเลิก -> statuspdf='6'+หมายเหตุ ; ปกติ -> cache กันถามซ้ำ
            if ($erpOk) {
                foreach ($toCheck as $no) {
                    if (isset($erpCancelled[$no])) {
                        $remark      = $erpCancelled[$no];
                        $result[$no] = $remark;
                        try {
                            $update = ['statuspdf' => self::CANCELLED_STATUSPDF];
                            if ($remark !== '') {
                                $update['cancel_remark'] = $remark;
                            }
                            DB::table('tblbill')->where('billid', $no)
                                ->where(function ($q) {
                                    $q->where('statuspdf', '!=', self::CANCELLED_STATUSPDF)
                                      ->orWhereNull('statuspdf');
                                })
                                ->update($update);
                        } catch (\Throwable $e) {
                            Log::warning('BillCancellation write failed: ' . $e->getMessage());
                        }
                        if ($remark === '') {
                            $needRemark[] = $no;
                        }
                    } else {
                        Cache::put(self::CACHE_PREFIX_OK . $no, 1, self::CACHE_TTL);
                    }
                }
            }
        }

        // 5) บิลยกเลิกที่ยังไม่มีหมายเหตุ -> หาจาก ERP แล้วเก็บลง tblbill.cancel_remark (ลองวันละครั้งต่อบิล)
        if (!empty($needRemark)) {
            $remarks = self::fetchRemarks($needRemark);
            if ($remarks !== null) {
                foreach ($needRemark as $no) {
                    Cache::put(self::CACHE_PREFIX_RMK . $no, 1, self::RMK_TTL);
                    $rm = $remarks[$no] ?? '';
                    if ($rm === '') {
                        continue;
                    }
                    $result[$no] = $rm;
                    try {
                        DB::table('tblbill')->where('billid', $no)
                            ->where(function ($q) {
                                $q->whereNull('cancel_remark')->orWhere('cancel_remark', '');
                            })
                            ->update(['cancel_remark' => $rm]);
                    } catch (\Throwable $e) {
                        Log::warning('BillCancellation remark write failed: ' . $e->getMessage());
                    }
                }
            }
        }

        return $result;
    }

    /**
     * หาหมายเหตุการยกเลิกจาก ERP (account03) ให้บิลที่ยังไม่มีหมายเหตุ
     *   - ERP ยกเลิก (DocuStatus='C') + มี InvCancRemark -> ใช้ InvCancRemark (แทบไม่มีใครกรอก)
     *   - ไม่งั้นใช้หมายเหตุหัวใบกำกับ SOInvHDRemark (ที่บัญชีจดจริง เช่น "ย้ายวัน 12/10/2569 แต๋น")
     *   - ไม่มีทั้งคู่ -> ใช้ log SMAudit: ผู้แก้ไขล่าสุด + เวลา
     *   - ERP ยังไม่ยกเลิก / ไม่พบเลขบิล -> แปลว่าถูกยกเลิกจากระบบ logistic (หน้าแอดมิน)
     *
     * @return array<string,string>|null [เลขบิล => หมายเหตุ] ; null = ต่อ ERP ไม่ได้ (ไม่ต้อง cache)
     */
    public static function fetchRemarks(array $billNos): ?array
    {
        $billNos = array_values(array_unique(array_filter(array_map(fn ($b) => trim((string) $b), $billNos))));
        if (empty($billNos)) {
            return [];
        }

        $out = [];
        try {
            $erp = DB::connection(self::ERP_CONNECTION);

            $hd = [];
            foreach (array_chunk($billNos, 500) as $chunk) {
                foreach ($erp->table('SOINVHD')->whereIn('INVNO', $chunk)->get(['INVNO', 'SOInvID', 'DocuStatus', 'InvCancRemark']) as $r) {
                    $hd[trim((string) $r->INVNO)] = $r;
                }
            }

            // หมายเหตุหัวใบกำกับ (SOInvHDRemark) — ที่ฝ่ายบัญชีจดไว้จริง เช่น "ย้ายวัน 12/10/2569 แต๋น" (หลายบรรทัดตาม ListNo)
            $invIds = [];
            foreach ($hd as $r) {
                if (strtoupper(trim((string) $r->DocuStatus)) === 'C' && $r->SOInvID) $invIds[] = (int) $r->SOInvID;
            }
            $hdRemark = [];   // [SOInvID => "บรรทัด1 · บรรทัด2"]
            foreach (array_chunk(array_values(array_unique($invIds)), 500) as $chunk) {
                $lines = [];
                foreach (
                    $erp->table('SOInvHDRemark')->whereIn('SOInvID', $chunk)
                        ->orderBy('SOInvID')->orderBy('ListNo')
                        ->get(['SOInvID', 'Remark']) as $x
                ) {
                    $t = trim((string) $x->Remark);
                    if ($t !== '') $lines[(int) $x->SOInvID][] = $t;
                }
                foreach ($lines as $id => $ls) $hdRemark[$id] = implode(' · ', $ls);
            }

            $needAudit = [];
            foreach ($billNos as $no) {
                $r = $hd[$no] ?? null;
                if (!$r) {
                    $out[$no] = 'ยกเลิกจากระบบ logistic (ไม่พบเลขบิลในระบบบัญชี)';
                } elseif (strtoupper(trim((string) $r->DocuStatus)) !== 'C') {
                    $out[$no] = 'ยกเลิกจากระบบ logistic (ในระบบบัญชียังไม่ยกเลิก)';
                } elseif (($rm = trim((string) ($r->InvCancRemark ?? ''))) !== '') {
                    $out[$no] = $rm;
                } elseif (!empty($hdRemark[(int) $r->SOInvID])) {
                    $out[$no] = $hdRemark[(int) $r->SOInvID];
                } else {
                    $needAudit[] = $no;
                }
            }

            // log ผู้แก้ไขเอกสารล่าสุดในระบบบัญชี (การยกเลิกคือการแก้ไขสถานะเอกสาร)
            foreach (array_chunk($needAudit, 300) as $chunk) {
                $last = [];
                foreach (
                    $erp->table('SMAudit')->whereIn('audit_docuno', $chunk)->where('audit_action', 'U')
                        ->orderBy('audit_datetime')
                        ->get(['audit_docuno', 'audit_username', 'audit_datetime']) as $a
                ) {
                    $last[trim((string) $a->audit_docuno)] = $a;   // เรียงเก่า->ใหม่ ตัวท้ายสุด = ล่าสุด
                }
                foreach ($chunk as $no) {
                    $a = $last[$no] ?? null;
                    $out[$no] = $a
                        ? 'ยกเลิกในระบบบัญชี (แก้ไขล่าสุดโดย ' . trim((string) $a->audit_username)
                            . ' · ' . Carbon::parse($a->audit_datetime)->format('d/m/Y H:i') . ')'
                        : 'ยกเลิกในระบบบัญชี (ไม่ได้ระบุหมายเหตุ)';
                }
            }
        } catch (\Throwable $e) {
            Log::warning('BillCancellation fetchRemarks failed: ' . $e->getMessage());
            return null;
        }

        return $out;
    }

    /**
     * lazy-sync สำหรับหน้า list ที่โหลด model/แถวมาแล้ว:
     *   รวบเลขบิลจาก $items -> sync (ยิง ERP เท่าที่จำเป็น) -> ปรับ in-memory ที่ถูกยกเลิก
     *   (set ->statuspdf='6' + ->cancel_remark) ให้ blade ที่เช็ค statuspdf==6 ทำงานทันที
     *
     * @param  iterable $items   collection/array ของ object ที่มี property เลขบิล
     * @param  string   $billKey ชื่อ property เลขบิล (ค่าเริ่มต้น 'billid')
     * @return array<string,string> [เลขบิล => หมายเหตุ] เฉพาะที่ยกเลิก
     */
    public static function applyToModels($items, string $billKey = 'billid'): array
    {
        $billNos   = collect($items)->map(fn ($it) => $it->{$billKey} ?? null)->all();
        $cancelled = self::sync($billNos);
        if (!empty($cancelled)) {
            foreach ($items as $it) {
                $no = trim((string) ($it->{$billKey} ?? ''));
                if ($no !== '' && array_key_exists($no, $cancelled)) {
                    $it->statuspdf     = self::CANCELLED_STATUSPDF;
                    $it->cancel_remark = $cancelled[$no];
                }
            }
        }
        return $cancelled;
    }

    /**
     * เช็ค local ล้วน (ไม่แตะ ERP เลย): บิลไหนยกเลิก (statuspdf='6') + หมายเหตุ
     *   ใช้กับหน้าที่ list บิลเยอะมาก (เช่น checkbill) — อาศัย statuspdf ที่หน้าอื่น sync ไว้
     *
     * @param  iterable $billNos
     * @return array<string,string> [เลขบิล => หมายเหตุ] เฉพาะที่ยกเลิก
     */
    public static function cancelledLocal($billNos): array
    {
        $billNos = collect($billNos)
            ->map(fn ($b) => trim((string) $b))
            ->filter()
            ->unique()
            ->values()
            ->all();
        if (empty($billNos)) {
            return [];
        }

        $out = [];
        foreach (
            DB::table('tblbill')
                ->whereIn('billid', $billNos)
                ->where('statuspdf', self::CANCELLED_STATUSPDF)
                ->get(['billid', 'cancel_remark']) as $r
        ) {
            $out[trim((string) $r->billid)] = trim((string) ($r->cancel_remark ?? ''));
        }
        return $out;
    }
}
