<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * ซ่อมข้อมูล: po_receives_line ที่ po_receive_id ยังว่าง (orphan) — เกิดจากรอบรับเข้าเก่าก่อนมีฟีเจอร์
 * backfill เดิมใช้ JOIN po_id+so_id ซึ่งถ้า po_id+so_id มีหลาย header (รับหลายรอบ) จะจับคู่กำกวม
 * ทำให้ line ของรอบเก่าไม่ถูกผูก -> shelfsale เข้าใจผิดว่ายังไม่เช็คเอาท์
 *
 * กติกาซ่อม: ต่อ (po_id + so_id) หา header ที่ "ยังไม่ถูกจับจอง" (ไม่มี line อื่นชี้ถึงด้วย po_receive_id)
 *   - มี header ว่าง 1 ตัว   -> ผูก orphan ทุก line ของกลุ่มนี้เข้ากับมัน (กรณีปกติ: รอบเก่า 1 รอบ)
 *   - ไม่มี header ว่าง       -> ผูกกับ header เก่าสุด (fallback)
 *   - มี header ว่างหลายตัว   -> ผูกกับ header ว่าง "เก่าสุด" (best guess, พบยาก)
 */
return new class extends Migration
{
    public function up(): void
    {
        $orphans = DB::table('po_receives_line')
            ->whereNull('po_receive_id')
            ->get(['id', 'po_id', 'so_id']);

        if ($orphans->isEmpty()) return;

        // header id ที่ถูกจับจองแล้ว (มี line ชี้ด้วย po_receive_id)
        $claimed = DB::table('po_receives_line')
            ->whereNotNull('po_receive_id')
            ->pluck('po_receive_id')
            ->flip();   // id => index (ใช้ isset ตรวจ)

        // header ทั้งหมด (รวมยกเลิก? -> ตัดที่ยกเลิกออก) group ตาม po_id
        $headersByPo = DB::table('po_receives')
            ->whereNull('cancelled_at')
            ->orderBy('id')
            ->get(['id', 'po_id', 'so_id'])
            ->groupBy('po_id');

        // group orphan ตาม po_id|so_id
        $groups = $orphans->groupBy(fn ($l) => $l->po_id . '|' . ($l->so_id ?? ''));

        foreach ($groups as $key => $lines) {
            [$poId, $soId] = array_pad(explode('|', $key, 2), 2, '');
            $cands = ($headersByPo->get($poId) ?? collect())
                ->filter(function ($h) use ($soId) {
                    // ตรง so_id หรือ ฝั่งใดฝั่งหนึ่งว่าง (ข้อมูลเก่าที่ so_id ว่าง)
                    return (string) ($h->so_id ?? '') === (string) $soId
                        || ($h->so_id ?? '') === '' || $soId === '';
                })
                ->sortBy('id')
                ->values();

            if ($cands->isEmpty()) continue;   // ไม่มี header ให้ผูก -> ข้าม (คงว่างไว้)

            $unclaimed = $cands->reject(fn ($h) => isset($claimed[$h->id]))->values();
            $target = $unclaimed->first() ?: $cands->first();   // ว่างเก่าสุด, ไม่งั้นเก่าสุด

            DB::table('po_receives_line')
                ->whereIn('id', $lines->pluck('id')->all())
                ->update(['po_receive_id' => $target->id]);

            // ตัวนี้ถูกจับจองแล้ว (เผื่อกลุ่มอื่นซ้ำ po เดียวกัน)
            $claimed[$target->id] = true;
        }
    }

    public function down(): void
    {
        // ไม่ย้อน (เป็นการซ่อมข้อมูลให้ถูกต้อง)
    }
};
