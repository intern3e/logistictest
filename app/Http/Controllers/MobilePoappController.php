<?php

namespace App\Http\Controllers;

use App\Models\PoReceive;
use App\Models\PoReceiveLine;
use App\Models\SsoTicket;
use App\Models\UserAuth;
use App\Models\PooutsideCancelled;  
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class MobilePoappController extends Controller
{
    private string $apiBase = 'http://server_update:8000';
    // private string $apiBase = 'http://192.168.1.169:8000';

    /** ชื่อ connection ของฐานข้อมูลระบบเก่า (ตาราง store) — ใช้เช็คว่า PO ถูกเช็คของออกทางระบบเก่าไปแล้วหรือยัง */
    const LEGACY_CONNECTION = 'mysql_3e';
    public function index(Request $request)
    {
        $user = $this->requireLogin($request, 'mobile-app');

        if (!in_array($user->role, ['admin', 'stock', 'store'], true)) {
            abort(403, 'คุณไม่มีสิทธิ์เข้าใช้งานหน้านี้');
        }

        return view('po.mobile_app');
    }
    /**
     * ค้นหา PO ตามชื่อซัพพลายเออร์ จาก polist (DB เก่า 3e) โดยตรง — ไม่พึ่ง API
     * ใช้ในหน้า mobile-app: พิมพ์ชื่อซัพ -> เด้งรายการ PO ที่สั่งกับซัพนั้น
     */
    public function poBySupplier(Request $request)
    {
        $request->validate(['sup' => 'required|string|max:100']);
        $sup = trim($request->query('sup'));

        $rows = DB::connection(self::LEGACY_CONNECTION)->table('polist')
            ->where('VendorName', 'LIKE', '%' . $sup . '%')
            ->whereRaw("UPPER(TRIM(COALESCE(POstatus, ''))) NOT IN ('COMPLETED', 'CANCELLED')")
            ->whereNotNull('PONum')->where('PONum', '<>', '')
            ->orderByDesc('PONum')
            ->limit(500)
            ->get(['PONum', 'VendorName', 'SONum', 'POstatus']);

        // ตัด PO ที่ "รับเข้า + เช็คเอาท์" ในระบบใหม่แล้วออก (polist ระบบเก่าไม่ sync สถานะนี้)
        // เหลือเฉพาะงานที่ยังไม่ได้รับจริง ๆ
        $candidates = $rows->pluck('PONum')
            ->flatMap(fn ($p) => [$p, 'PO' . $p])->unique()->values()->all();
        $checkedOut = PoReceive::whereIn('po_id', $candidates)
            ->whereNotNull('checkout_by')
            ->pluck('po_id')
            ->map(fn ($id) => preg_replace('/^PO/i', '', (string) $id))
            ->flip();
        $rows = $rows->reject(fn ($r) => $checkedOut->has($r->PONum))
            ->take(300)->values();

        // ดึงชื่อสินค้า + ยอดรวมของแต่ละ PO จาก MSSQL (POHD -> PODT) ตรง
        $detailByPo = $this->fetchPoItemsFromMssql($rows->pluck('PONum')->all());

        // fallback: PO ที่ MSSQL ดึงไม่ได้ (เช่น prod ต่อ account03 ไม่ได้) -> ยิง HTTP getPODetail
        $missing = $rows->pluck('PONum')
            ->filter(fn ($p) => !$detailByPo->has($p) || empty($detailByPo->get($p)['items']))
            ->values()->all();
        if (!empty($missing)) {
            $detailByPo = $detailByPo->merge($this->fetchPoItemsFromHttp($missing));
        }

        return response()->json([
            'ok'    => true,
            'items' => $rows->map(function ($r) use ($detailByPo) {
                $d = $detailByPo->get($r->PONum, []);
                return [
                    'po_num'      => $r->PONum,
                    'vendor_name' => $r->VendorName,
                    'so_num'      => $r->SONum,
                    'status'      => $r->POstatus,
                    'products'    => $d['items'] ?? [],
                    'amount'      => (float) ($d['amount'] ?? 0),
                ];
            })->values(),
        ]);
    }

    /**
     * ดึงชื่อสินค้าของ PO จาก MSSQL (POHD -> PODT) ตรง (แทนการยิง HTTP getPODetail)
     * คืน keyed by เลข PO -> collection ของ {name, qty}
     */
    private function fetchPoItemsFromMssql(array $poNums): \Illuminate\Support\Collection
    {
        $poNums = array_values(array_filter(array_unique($poNums)));
        if (!$poNums) return collect();

        try {
            $docuToPo = collect($poNums)->mapWithKeys(fn ($p) => ['PO' . $p => $p]);

            // POHD: POID + ยอดรวมของ PO (SumGoodAmnt) เหมือนที่ so/index ใช้
            $headers = DB::connection('mssql_account03')->table('POHD')
                ->whereIn('DocuNo', $docuToPo->keys()->all())
                ->get(['POID', 'DocuNo', 'SumGoodAmnt']);
            if ($headers->isEmpty()) return collect();

            $poidToPo = $headers->mapWithKeys(fn ($h) => [$h->POID => $docuToPo->get($h->DocuNo)]);

            $items = DB::connection('mssql_account03')->table('PODT')
                ->whereIn('POID', $poidToPo->keys()->all())
                ->where('CancelFlag', '<>', 'Y')
                ->get(['POID', 'GoodName', 'GoodQty2']);

            $itemsByPo = $items->groupBy('POID')->mapWithKeys(fn ($lines, $poid) => [
                $poidToPo->get($poid) => $lines->map(fn ($l) => [
                    'name' => $l->GoodName ?: '—',
                    'qty'  => (float) $l->GoodQty2,
                ])->values()->all(),
            ]);

            // คืน keyed by เลข PO -> {items, amount}
            return $headers->mapWithKeys(fn ($h) => [
                $docuToPo->get($h->DocuNo) => [
                    'items'  => $itemsByPo->get($docuToPo->get($h->DocuNo), []),
                    'amount' => (float) $h->SumGoodAmnt,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::warning('fetchPoItemsFromMssql failed: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * fallback ดึงชื่อสินค้าจาก HTTP getPODetail (server_update) — ใช้เมื่อ MSSQL ดึงไม่ได้
     * ยิงแบบ pool (ขนาน) ทีละ 20 PO
     */
    private function fetchPoItemsFromHttp(array $poNums): \Illuminate\Support\Collection
    {
        $poNums = array_values(array_filter(array_unique($poNums)));
        if (!$poNums) return collect();

        $result = collect();
        try {
            foreach (array_chunk($poNums, 20) as $chunk) {
                $responses = Http::pool(fn ($pool) => collect($chunk)->mapWithKeys(fn ($po) => [
                    (string) $po => $pool->as((string) $po)->timeout(15)
                        ->get($this->apiBase . '/api/getPODetail', ['PONum' => $po]),
                ])->all());

                foreach ($chunk as $po) {
                    $resp = $responses[(string) $po] ?? null;
                    if (!($resp instanceof \Illuminate\Http\Client\Response) || $resp->failed()) continue;

                    $data = $resp->json();
                    if (is_array($data) && isset($data['poData'])) $data = $data['poData'];
                    if (is_array($data) && isset($data[0])) $data = $data[0];
                    if (isset($data['data'])) {
                        $data = is_array($data['data']) && isset($data['data'][0]) ? $data['data'][0] : $data['data'];
                    }

                    $lines  = $data['ms_podt'] ?? [];
                    $amount = (float) ($data['SumGoodAmnt'] ?? 0);
                    if ((!is_array($lines) || empty($lines)) && $amount == 0) continue;

                    $result->put($po, [
                        'items'  => is_array($lines) ? collect($lines)->map(fn ($l) => [
                            'name' => $l['GoodName'] ?? '—',
                            'qty'  => (float) ($l['GoodQty2'] ?? 0),
                        ])->values()->all() : [],
                        'amount' => $amount,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('fetchPoItemsFromHttp failed: ' . $e->getMessage());
        }
        return $result;
    }

    public function getPODetail(Request $request)
    {
        $request->validate(['PONum' => 'required|string|max:50']);
        $poNum = $request->query('PONum');

        try {
            $poResponse = Http::timeout(15)->get($this->apiBase . '/api/getPODetail', [
                'PONum' => $poNum,
            ]);

            if ($poResponse->failed()) {
                return response()->json([
                    'message' => 'server_update ตอบกลับ error (' . $poResponse->status() . ')',
                ], $poResponse->status());
            }

            $poData = $poResponse->json();

            $norm = $poData;
            if (is_array($norm) && isset($norm[0])) $norm = $norm[0];
            if (isset($norm['data'])) {
                $norm = is_array($norm['data']) && isset($norm['data'][0])
                    ? $norm['data'][0] : $norm['data'];
            }

            $docuNo = $norm['DocuNo'] ?? null;
            $cancelledPO = PooutsideCancelled::where('po_id', $poNum)
                ->when($docuNo, fn($q) => $q->orWhere('po_id', $docuNo))
                ->first();

            if ($cancelledPO) {
                return response()->json([
                    'cancelled'    => true,
                    'message'      => 'PO นี้ถูกยกเลิกในระบบแล้ว',
                    'po_id'        => $cancelledPO->po_id,
                    'so_id'        => $cancelledPO->so_id,
                    'cancelled_by' => $cancelledPO->cancelled_by,
                    'cancelled_at' => optional($cancelledPO->cancelled_at)->format('Y-m-d H:i:s'),
                    'note'         => $cancelledPO->note,
                ], 409);
            }

            // ★ เช็คระบบใหม่: PO ถูกเช็คของออก (po_receives.checkout_by มีค่าแล้ว) → ห้ามรับเข้าเพิ่ม
            $checkedOutNew = PoReceive::where('po_id', $poNum)
                ->when($docuNo, fn($q) => $q->orWhere('po_id', $docuNo))
                ->whereNotNull('checkout_by')
                ->first();

            if ($checkedOutNew) {
                return response()->json([
                    'checked_out' => true,
                    'message'     => 'PO นี้ถูกเช็คของออกไปแล้ว ไม่สามารถรับเข้าเพิ่มได้',
                    'po_id'       => $checkedOutNew->po_id,
                    'so_id'       => $checkedOutNew->so_id,
                    'checkout_by' => $checkedOutNew->checkout_by,
                    'checkout_at' => optional($checkedOutNew->checkout_time)->format('Y-m-d H:i:s'),
                ], 409);
            }

            // หมายเหตุ: ยกเลิกการเช็คสถานะเช็คของออกจาก "ระบบเก่า" (store.statusArea='0' / DATECHECKOUT) แล้ว
            // ตามที่กำหนด ให้รับเข้าได้โดยไม่ต้องดูสถานะ checkout ของระบบเก่า (คงเช็คเฉพาะระบบใหม่ข้างบน)

            $soNums = $norm['SONumList'] ?? ($norm['SONum'] ? [$norm['SONum']] : []);
            $soNums = array_values(array_unique(array_filter($soNums)));

            $latestSoNum = $norm['SONumLatest'] ?? ($soNums[0] ?? null);

            // badge list: แสดงแค่เลข SO เฉย ๆ ครบทุกตัว
            $soList = array_map(fn($s) => ['SONum' => $s], $soNums);

            // รายละเอียด (ชื่อลูกค้า/PO อ้างอิง/Sale) เอาแค่ SO ล่าสุด
            $soInfo = ['SONum' => $latestSoNum, 'CustPONo' => '', 'CustName' => '', 'ResponseBy' => ''];

            if ($latestSoNum) {
                try {
                    $soResponse = Http::timeout(8)->get($this->apiBase . '/api/getSODetail', [
                        'SONum' => $latestSoNum,
                    ]);
                    if ($soResponse->ok()) {
                        $so = $soResponse->json();
                        if (is_array($so) && isset($so[0])) $so = $so[0];
                        if (isset($so['data'])) {
                            $so = is_array($so['data']) && isset($so['data'][0])
                                ? $so['data'][0] : $so['data'];
                        }
                        $soInfo['CustPONo']   = $so['CustPONo'] ?? $so['SoStatus']['CustPONo'] ?? '';
                        $soInfo['CustName']   = $so['CustName'] ?? $so['SoStatus']['CustName'] ?? '';
                        $soInfo['ResponseBy'] = $so['SoDetail']['ResponseBy'] ?? $so['ResponseBy'] ?? '';
                    }
                } catch (\Exception $e) {
                    Log::warning('getSODetail failed for SO ' . $latestSoNum . ': ' . $e->getMessage());
                }
            }

            return response()->json(['poData' => $poData, 'soList' => $soList, 'soInfo' => $soInfo]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'เชื่อมต่อ server_update ไม่ได้: ' . $e->getMessage(),
            ], 502);
        }
    }
    public function receivePO(Request $request)
    {
        $validated = $request->validate([
            'PONum'             => 'required|string|max:50',
            // SONum = รายการ SO ต่อด้วย comma (1 PO ผูกได้หลาย SO) แล้วแยกเก็บราย SO ภายหลัง
            // จึงยาวเกิน 50 ได้ (เช่น 8 SO) — so_id ที่เก็บจริงต่อแถวยังเป็น SO เดี่ยว (<=50)
            'SONum'             => 'nullable|string|max:1000',
            'Status'            => 'required|in:ครบ,บางส่วน',
            'Shelf'             => 'nullable|string|max:100',
            'Photo'             => 'nullable|string',
            'Printer'           => 'nullable|string|max:100',
            'PrintSheets'       => 'nullable|integer|min:1',
            'ReceivedBy'        => 'nullable|string|max:100',
            'CustPONo'          => 'nullable|string|max:200',
            'CustName'          => 'nullable|string|max:500',
            'items'             => 'required|array|min:1',
            'items.*.GoodName'  => 'nullable|string|max:500',
            'items.*.UnitPrice' => 'nullable|numeric',
            'items.*.RecvQty'   => 'required|numeric|gt:0',
        ]);

        if (PooutsideCancelled::where('po_id', $validated['PONum'])->exists()) {
            return response()->json([
                'message' => 'ไม่สามารถบันทึกรับเข้าได้ เนื่องจาก PO นี้ถูกยกเลิกในระบบแล้ว',
            ], 409);
        }

        $photoPath  = $this->savePhotoBase64($validated['Photo'] ?? null, $validated['PONum']);
        $receivedAt = now();
        $receivedBy = $validated['ReceivedBy'] ?? optional($request->user())->name;

        // แยก SO (PO เชื่อมหลาย SO) → รับเข้า/พิมพ์ แยกต่อ SO
        $soNums = array_values(array_unique(array_filter(array_map('trim', explode(',', (string) ($validated['SONum'] ?? ''))))));
        if (empty($soNums)) {
            $soNums = [null]; // เผื่อไม่มี SO (ปกติจะมี เพราะฝั่งหน้าบังคับเชื่อม SO)
        }

        // ดึงข้อมูลลูกค้า/PO ลูกค้า ต่อ SO จาก 3e so (แยกข้อมูลกันตาม SO)
        $soInfo = collect();
        $realSoNums = array_values(array_filter($soNums));
        if (!empty($realSoNums)) {
            $soInfo = DB::connection(self::LEGACY_CONNECTION)->table('so')
                ->whereIn('SONum', $realSoNums)
                ->get(['SONum', 'CustName', 'CustPONo'])
                ->keyBy('SONum');
        }

        try {
            $header = DB::transaction(function () use ($validated, $photoPath, $receivedAt, $receivedBy, $soNums, $soInfo) {
                $last = null;
                // 1 SO = 1 po_receives (แยก record) + ไส้ในของ SO นั้น
                foreach ($soNums as $soNum) {
                    $info     = $soNum ? $soInfo->get($soNum) : null;
                    $custName = optional($info)->CustName ?: ($validated['CustName'] ?? null);
                    $custPONo = optional($info)->CustPONo ?: ($validated['CustPONo'] ?? null);

                    $header = PoReceive::where('po_id', $validated['PONum'])
                        ->where('so_id', $soNum)
                        ->lockForUpdate()->first();

                    if ($header) {
                        $header->update([
                            'status'    => $validated['Status'],
                            'cust_name' => $custName ?: $header->cust_name,
                            'POref'     => $custPONo ?: $header->POref,
                        ]);
                    } else {
                        $header = PoReceive::create([
                            'po_id'         => $validated['PONum'],
                            'so_id'         => $soNum,
                            'status'        => $validated['Status'],
                            'cust_name'     => $custName,
                            'POref'         => $custPONo,
                            'checkout_by'   => null,
                            'checkout_time' => null,
                        ]);
                    }

                    foreach ($validated['items'] as $it) {
                        PoReceiveLine::create([
                            'po_id'       => $validated['PONum'],
                            'so_id'       => $soNum,
                            'good_name'   => $it['GoodName'] ?? null,
                            'recv_qty'    => $it['RecvQty'],
                            'unit_price'  => $it['UnitPrice'] ?? null,
                            'shelf'       => $validated['Shelf'] ?? null,
                            'photo_path'  => $photoPath,
                            'received_by' => $receivedBy,
                            'received_at' => $receivedAt,
                        ]);
                    }
                    $last = $header;
                }
                return $last;
            });
        } catch (\Exception $e) {
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }

            return response()->json([
                'message' => 'บันทึกลงฐานข้อมูลไม่สำเร็จ: ' . $e->getMessage(),
            ], 500);
        }

        // ── พิมพ์สติกเกอร์: แยกทุก SO พร้อมข้อมูลลูกค้า/PO ลูกค้าของ SO นั้น ──
        if (!empty($validated['Printer'])) {
            foreach ($realSoNums as $soNum) {
                $info = $soInfo->get($soNum);
                $this->insertPrintWarehouse(
                    $soNum,
                    optional($info)->CustPONo ?: ($validated['CustPONo'] ?? ''),
                    optional($info)->CustName ?: ($validated['CustName'] ?? ''),
                    $validated['Printer'],
                    $validated['PrintSheets'] ?? 1
                );
            }
        }

        $rowCount = count($validated['items']);

        return response()->json([
            'success'   => true,
            'header_id' => $header->id,
            'status'    => $header->status,
            'row_count' => $rowCount,
            'photo_url' => $photoPath ? Storage::disk('public')->url($photoPath) : null,
            'message'   => 'รับเข้าสำเร็จ ' . $rowCount . ' รายการ',
        ]);
    }
    public function history(Request $request)
    {
        $query = PoReceiveLine::query()
            // join แบบรวม so_id เพื่อกันแถวซ้ำเมื่อ 1 PO มีหลาย po_receives (แยกต่อ SO)
            // รองรับข้อมูลเก่า: line.so_id = null → จับกับ header เดียวของ PO นั้น (เดิมมี 1 แถว/PO)
            ->join('po_receives', function ($j) {
                $j->on('po_receives.po_id', '=', 'po_receives_line.po_id')
                  ->where(function ($q) {
                      $q->whereColumn('po_receives.so_id', '=', 'po_receives_line.so_id')
                        ->orWhereNull('po_receives_line.so_id');
                  })
                  // กัน join ไปเจอ header เก่าที่ถูกยกเลิก (1 po,so มีได้หลาย header) -> แถวซ้ำ
                  ->whereNull('po_receives.cancelled_at');
            })
            ->select(
                'po_receives_line.id',
                'po_receives_line.po_id',
                'po_receives_line.good_name',
                'po_receives_line.recv_qty',
                'po_receives_line.unit_price',
                'po_receives_line.shelf',
                'po_receives_line.photo_path',
                'po_receives_line.received_by',
                'po_receives_line.received_at',
                'po_receives.so_id as so_num',
                'po_receives.status as po_status'
            )
            // ไม่เอา line ที่ถูกยกเลิกไปแล้ว (cancelled) — จะได้ไม่ถูกนับเป็นของที่รับแล้ว
            ->whereNull('po_receives_line.cancelled_at')
            ->orderByDesc('po_receives_line.received_at');

        if ($request->filled('PONum')) {
            $query->where('po_receives_line.po_id', $request->query('PONum'));
        }

        return response()->json(
            $query->limit(500)->get()->map(function ($row) {
                $row->photo_url = $row->photo_path
                    ? Storage::disk('public')->url($row->photo_path)
                    : null;

                return $row;
            })
        );
    }

    /**
     * INSERT สั่งพิมพ์สติกเกอร์ลง printwarehouse (mysql_3e)
     */
    private function insertPrintWarehouse(
        string $soNum,
        string $custPONo,
        string $custName,
        string $printerName,
        int    $printQty
    ): void {
        try {
            $rows = [];
            for ($i = 0; $i < $printQty; $i++) {
                $rows[] = [
                    'SONum'        => $soNum,
                    'PORef'        => $custPONo,
                    'CustName'     => $custName,
                    'Print_Qty'    => 1,
                    'Printed_Flag' => 'N',
                    'printerName'  => $printerName,
                ];
            }
            DB::connection('mysql_3e')->table('printwarehouse')->insert($rows);
        } catch (\Exception $e) {
            Log::error('insertPrintWarehouse failed: ' . $e->getMessage());
        }
    }

    /**
     * แปลง base64 dataURL ของรูปแล้วเซฟลง storage/app/public/po-receive
     */
    private function savePhotoBase64(?string $base64, string $ponum): ?string
    {
        if (!$base64 || !preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
            return null;
        }

        $ext  = $type[1] === 'jpeg' ? 'jpg' : $type[1];
        $data = base64_decode(substr($base64, strpos($base64, ',') + 1));

        if ($data === false) {
            return null;
        }

        $safePONum = preg_replace('/[^A-Za-z0-9_-]/', '_', $ponum);

        $name = 'po-receive/' . $safePONum . '.' . $ext;
        $run  = 2;
        while (Storage::disk('public')->exists($name)) {
            $name = 'po-receive/' . $safePONum . '_' . $run . '.' . $ext;
            $run++;
        }

        Storage::disk('public')->put($name, $data);

        return $name;
    }

    public function cancelReceive(Request $request)
    {
        if (!Auth::guard('web')->check() || Auth::user()->role !== 'admin') {
            return response()->json([
                'message' => 'คุณไม่มีสิทธิ์ยกเลิกการรับเข้า (เฉพาะ admin เท่านั้น)',
            ], 403);
        }

        $validated = $request->validate([
            'PONum'    => 'required|string|max:50',
            'Status'   => 'required|in:รับเข้าผิด',
            'CancelBy' => 'nullable|string|max:100',
        ]);

        $cancelBy = $validated['CancelBy'] ?? optional($request->user())->name;

        try {
            DB::transaction(function () use ($validated, $cancelBy) {
                // ยกเลิก "ทั้ง PO" = ทุก header (ทุก SO) ที่ยัง active ของ po_id นี้
                $headers = PoReceive::where('po_id', $validated['PONum'])
                    ->lockForUpdate()
                    ->get();

                if ($headers->isEmpty()) {
                    abort(404, 'ไม่พบข้อมูลการรับเข้าของ PO นี้');
                }

                $now = now();

                // ไม่ลบ row / รูปทิ้ง — เก็บไว้เป็นประวัติ (กันข้อมูลหาย)
                // mark ยกเลิกเฉพาะ line ที่ยัง active อยู่ เพื่อไม่ให้นับเป็นของที่รับแล้ว (รับเข้าใหม่ได้)
                PoReceiveLine::where('po_id', $validated['PONum'])
                    ->whereNull('cancelled_at')
                    ->update([
                        'cancelled_at' => $now,
                        'cancelled_by' => $cancelBy,
                    ]);

                // soft-cancel header ทุก SO: ตั้ง cancelled_at/by + สถานะ "รับเข้าผิด"
                // -> global scope จะซ่อน header เหล่านี้ ทำให้รับเข้าใหม่แล้วสร้าง record ใหม่ (ไม่ทับของเก่า)
                foreach ($headers as $header) {
                    $header->update([
                        'status'       => $validated['Status'],
                        'cancelled_at' => $now,
                        'cancelled_by' => $cancelBy,
                    ]);
                }
            });
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'ยกเลิกการรับเข้าไม่สำเร็จ: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'ยกเลิกการรับเข้าเรียบร้อยแล้ว',
        ]);
    }

    /**
     * แก้ไข/ย้ายชั้นวางของสินค้าที่รับเข้าแล้ว
     * เงื่อนไข: PO นั้นต้องยังไม่ถูกเช็คของออก (po_receives.checkout_by ต้องว่าง)
     * แก้เฉพาะ line ที่ยัง active (ยังไม่ถูกยกเลิก)
     */
    public function updateShelf(Request $request)
    {
        if (!Auth::guard('web')->check()) {
            return response()->json(['message' => 'กรุณาเข้าสู่ระบบก่อน'], 401);
        }

        $validated = $request->validate([
            'PONum'           => 'required|string|max:50',
            'Lines'           => 'required|array|min:1',
            'Lines.*.id'      => 'required|integer',
            'Lines.*.shelf'   => 'nullable|string|max:100',
            'Lines.*.qty'     => 'nullable|numeric|min:0',      // แก้จำนวนที่รับ
            'Lines.*.deleted' => 'nullable|boolean',            // ลบรายการที่เพิ่มผิด (soft-cancel)
        ]);

        $updatedBy = optional($request->user())->name;

        try {
            $result = DB::transaction(function () use ($validated) {
                $header = PoReceive::where('po_id', $validated['PONum'])
                    ->lockForUpdate()
                    ->first();

                if (!$header) {
                    abort(404, 'ไม่พบข้อมูลการรับเข้าของ PO นี้');
                }

                // ★ PO ที่ถูกเช็คของออกแล้ว ห้ามย้ายชั้นวาง
                if (!empty($header->checkout_by)) {
                    abort(409, 'PO นี้ถูกเช็คของออกไปแล้ว ไม่สามารถย้ายชั้นวางได้');
                }

                $ids = array_column($validated['Lines'], 'id');

                // ดึงเฉพาะ line ที่เป็นของ PO นี้ และยังไม่ถูกยกเลิก
                $lines = PoReceiveLine::where('po_id', $validated['PONum'])
                    ->whereNull('cancelled_at')
                    ->whereIn('id', $ids)
                    ->get()
                    ->keyBy('id');

                $count = 0;
                foreach ($validated['Lines'] as $l) {
                    $line = $lines->get($l['id']);
                    if (!$line) {
                        continue; // ข้าม line ที่ไม่ใช่ของ PO นี้ / ถูกยกเลิกไปแล้ว
                    }

                    // ลบรายการที่เพิ่มผิด → soft-cancel (เก็บประวัติไว้ ไม่ลบจริง)
                    if (!empty($l['deleted'])) {
                        $line->cancelled_at = now();
                        $line->cancelled_by = optional(request()->user())->name;
                        $line->save();
                        $count++;
                        continue;
                    }

                    $line->shelf = $l['shelf'] ?? null;
                    if (array_key_exists('qty', $l) && $l['qty'] !== null && $l['qty'] !== '') {
                        $line->recv_qty = (float) $l['qty'];
                    }
                    $line->save();
                    $count++;
                }

                return $count;
            });
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getStatusCode());
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'ย้ายชั้นวางไม่สำเร็จ: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'updated' => $result,
            'message' => "ย้ายชั้นวางเรียบร้อย {$result} รายการ",
        ]);
    }

    /**
     * ดึงประวัติการรับเข้า "ระบบเก่า" (3e table store) ของ PO
     * store เก็บระดับกล่อง/ชั้นวาง (ไม่มี qty รายสินค้า) — ใช้แสดงว่า PO นี้เคยรับเข้า/วางไว้ที่ไหน
     * read-only ทั้งหมด (select เท่านั้น)
     */
    public function legacyStore(Request $request)
    {
        $poNum = (string) $request->query('PONum', '');
        if ($poNum === '') {
            return response()->json(['rows' => [], 'received' => false, 'active' => false]);
        }

        // store.PO เก็บแบบไม่มี prefix "PO" — ตัดออกก่อนเทียบ
        $poClean = preg_replace('/^PO/i', '', $poNum);

        try {
            $rows = DB::connection(self::LEGACY_CONNECTION)->table('store')
                ->where('PO', $poClean)
                ->orderBy('DATEBOX')
                ->get([
                    'ID', 'BOX', 'PO', 'SO', 'Area', 'areaS', 'boxS',
                    'statusBox', 'statusArea', 'DATEBOX', 'DATEAREA', 'DATECHECKOUT',
                ]);

            // แปลรหัสชั้นวาง (Area = area.ID) → area.areaName
            $areaIds = $rows->pluck('Area')->filter(fn ($v) => $v !== null && $v !== '')->unique()->values()->all();
            $areaNames = collect();
            if (!empty($areaIds)) {
                $areaNames = DB::connection(self::LEGACY_CONNECTION)->table('area')
                    ->whereIn('ID', $areaIds)
                    ->pluck('areaName', 'ID');
            }

            $out = $rows->map(function ($r) use ($areaNames) {
                // สถานที่เก็บ (Area = area.ID → area.areaName)
                $shelf = (!empty($r->Area) && isset($areaNames[$r->Area])) ? $areaNames[$r->Area] : '';
                $checkedOut = !empty($r->DATECHECKOUT);

                return [
                    'box'            => $r->BOX,
                    'so'             => $r->SO,
                    'shelf'          => $shelf,             // สถานที่เก็บ (Area → areaName)
                    'by'             => $r->boxS,           // ชื่อคนรับ/คนทำ
                    'date'           => $r->DATEAREA ?: $r->DATEBOX,   // เวลารับเข้า (DATEAREA)
                    'checked_out'    => $checkedOut,
                    'checkout_date'  => $r->DATECHECKOUT,   // เช็คเอาท์เมื่อไหร่
                    'checkout_place' => $r->areaS,          // เช็คเอาท์ที่ไหน (บันทึกล่าสุด)
                ];
            })->values();

            // นับเฉพาะ row ที่มีคนรับจริง (boxS ไม่ว่าง) — boxS ว่าง = ยังไม่เคยรับเข้า (เป็นแค่กล่องเปล่า)
            $out = $out->filter(fn ($x) => trim((string) ($x['by'] ?? '')) !== '')->values();

            // active = ยังมีของอยู่ในคลัง (ยังไม่เช็คของออก)
            $active = $out->contains(fn ($x) => !$x['checked_out']);

            // มี row ที่ถูกเช็คของออกแล้วไหม + เอา row เช็คเอาท์ล่าสุดไว้แสดง
            $checkedRows   = $out->filter(fn ($x) => $x['checked_out'])->values();
            $anyCheckedOut = $checkedRows->isNotEmpty();
            $checkout      = $anyCheckedOut ? $checkedRows->sortByDesc('checkout_date')->first() : null;

            return response()->json([
                'rows'        => $out,
                'received'    => $out->isNotEmpty(),
                'active'      => $active,
                'checked_out' => $anyCheckedOut,
                'checkout'    => $checkout,
            ]);
        } catch (\Exception $e) {
            Log::warning('legacyStore failed for ' . $poNum . ': ' . $e->getMessage());
            return response()->json(['rows' => [], 'received' => false, 'active' => false]);
        }
    }

    /**
     * ดึงข้อมูลรับเข้า "ระบบเก่า" (store) มาสร้างเป็นข้อมูลรับเข้า "ระบบใหม่" (po_receives_line)
     * เพื่อให้แก้ไข/ย้ายสถานที่ในระบบใหม่ต่อได้ — เขียนเฉพาะ DB logistic (ระบบเก่ายัง read-only)
     * รายการสินค้า/ราคา ส่งมาจากหน้า (ดึงจาก PO ต้นทาง MSSQL) ส่วนสถานที่/ผู้รับ/วันที่ มาจาก store
     */
    public function migrateLegacy(Request $request)
    {
        if (!Auth::guard('web')->check()) {
            return response()->json(['message' => 'กรุณาเข้าสู่ระบบก่อน'], 401);
        }

        $validated = $request->validate([
            'PONum'             => 'required|string|max:50',
            'SONum'             => 'nullable|string|max:1000',
            'CustName'          => 'nullable|string|max:500',
            'CustPONo'          => 'nullable|string|max:100',
            'ReceivedBy'        => 'nullable|string|max:100',
            'ReceivedAt'        => 'nullable|string|max:40',
            'Shelf'             => 'nullable|string|max:100',
            'items'             => 'required|array|min:1',
            'items.*.GoodName'  => 'nullable|string|max:500',
            'items.*.UnitPrice' => 'nullable|numeric',
            'items.*.RecvQty'   => 'required|numeric|gt:0',
        ]);

        // กันซ้ำ: ถ้ามีข้อมูลระบบใหม่ (active) อยู่แล้ว ไม่ต้องดึงซ้ำ
        if (PoReceiveLine::where('po_id', $validated['PONum'])->exists()) {
            return response()->json([
                'message' => 'PO นี้มีข้อมูลในระบบใหม่อยู่แล้ว',
            ], 409);
        }

        // แปลงวันที่รับเข้าจากระบบเก่า (ถ้า parse ไม่ได้ใช้เวลาปัจจุบัน)
        $receivedAt = now();
        if (!empty($validated['ReceivedAt'])) {
            $ts = strtotime($validated['ReceivedAt']);
            if ($ts) {
                $receivedAt = date('Y-m-d H:i:s', $ts);
            }
        }
        $receivedBy = $validated['ReceivedBy'] ?? optional($request->user())->name;
        $shelf      = $validated['Shelf'] ?? null;

        try {
            $count = DB::transaction(function () use ($validated, $receivedAt, $receivedBy, $shelf) {
                $header = PoReceive::where('po_id', $validated['PONum'])->lockForUpdate()->first();

                $attrs = [
                    'so_id'     => $validated['SONum'] ?? ($header->so_id ?? null),
                    'status'    => 'ครบ',
                    'cust_name' => $validated['CustName'] ?? ($header->cust_name ?? null),
                    'POref'     => $validated['CustPONo'] ?? ($header->POref ?? null),
                ];

                if ($header) {
                    $header->update($attrs);
                } else {
                    PoReceive::create(array_merge($attrs, [
                        'po_id'         => $validated['PONum'],
                        'checkout_by'   => null,
                        'checkout_time' => null,
                    ]));
                }

                $n = 0;
                foreach ($validated['items'] as $it) {
                    PoReceiveLine::create([
                        'po_id'       => $validated['PONum'],
                        'good_name'   => $it['GoodName'] ?? null,
                        'recv_qty'    => $it['RecvQty'],
                        'unit_price'  => $it['UnitPrice'] ?? null,
                        'shelf'       => $shelf,
                        'photo_path'  => null,
                        'received_by' => $receivedBy,
                        'received_at' => $receivedAt,
                    ]);
                    $n++;
                }
                return $n;
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'ดึงข้อมูลเข้าระบบใหม่ไม่สำเร็จ: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success'  => true,
            'migrated' => $count,
            'message'  => "ดึงข้อมูลเข้าระบบใหม่แล้ว {$count} รายการ",
        ]);
    }
    public function getProductVender(Request $request)
{
    $request->validate(['VendorCode' => 'required|string|max:50']);
    $vendorCode = $request->query('VendorCode');

    try {
        $res = Http::timeout(15)->get($this->apiBase . '/api/getProductVender', [
            'VendorCode' => $vendorCode,
        ]);
        if ($res->failed()) {
            return response()->json(['message' => 'server_update ตอบกลับ error'], $res->status());
        }
        return response()->json($res->json());
    } catch (\Exception $e) {
        return response()->json(['message' => 'เชื่อมต่อ server_update ไม่ได้: ' . $e->getMessage()], 502);
    }
}
}