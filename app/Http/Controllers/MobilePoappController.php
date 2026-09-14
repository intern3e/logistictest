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
            'SONum'             => 'nullable|string|max:50',
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

        try {
            $header = DB::transaction(function () use ($validated, $photoPath, $receivedAt, $receivedBy) {

                $header = PoReceive::where('po_id', $validated['PONum'])->lockForUpdate()->first();

                $custName = $validated['CustName'] ?? null;
                $custPONo = $validated['CustPONo'] ?? null;

                if ($header) {
                    $header->update([
                        'so_id'     => $validated['SONum'] ?? $header->so_id,
                        'status'    => $validated['Status'],
                        'cust_name' => $custName ?: $header->cust_name,
                        'POref'     => $custPONo ?: $header->POref,
                    ]);
                } else {
                    $header = PoReceive::create([
                        'po_id'         => $validated['PONum'],
                        'so_id'         => $validated['SONum'] ?? null,
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
                        'good_name'   => $it['GoodName'] ?? null,
                        'recv_qty'    => $it['RecvQty'],
                        'unit_price'  => $it['UnitPrice'] ?? null,
                        'shelf'       => $validated['Shelf'] ?? null,
                        'photo_path'  => $photoPath,
                        'received_by' => $receivedBy,
                        'received_at' => $receivedAt,
                    ]);
                }

                return $header;
            });
        } catch (\Exception $e) {
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }

            return response()->json([
                'message' => 'บันทึกลงฐานข้อมูลไม่สำเร็จ: ' . $e->getMessage(),
            ], 500);
        }

        // ── พิมพ์สติกเกอร์ ──
        if (!empty($validated['Printer']) && !empty($validated['SONum'])) {
            $this->insertPrintWarehouse(
                $validated['SONum'],
                $validated['CustPONo'] ?? '',
                $validated['CustName'] ?? '',
                $validated['Printer'],
                $validated['PrintSheets'] ?? 1
            );
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
            ->join('po_receives', 'po_receives.po_id', '=', 'po_receives_line.po_id')
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
                $header = PoReceive::where('po_id', $validated['PONum'])
                    ->lockForUpdate()
                    ->first();

                if (!$header) {
                    abort(404, 'ไม่พบข้อมูลการรับเข้าของ PO นี้');
                }

                PoReceiveLine::where('po_id', $validated['PONum'])
                    ->whereNotNull('photo_path')
                    ->pluck('photo_path')
                    ->unique()
                    ->each(function ($path) {
                        Storage::disk('public')->delete($path);
                    });

                PoReceiveLine::where('po_id', $validated['PONum'])->delete();

                $header->update([
                    'status'        => $validated['Status'],
                    'checkout_by'   => $cancelBy,
                    'checkout_time' => now(),
                ]);
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
}