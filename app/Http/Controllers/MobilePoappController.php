<?php

namespace App\Http\Controllers;

use App\Models\PoReceive;
use App\Models\PoReceiveLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MobilePoappController extends Controller
{
    private string $apiBase = 'http://server_update:8000';
    // private string $apiBase = 'http://127.0.0.1:8000';

    public function index()
    {
        return view('po.mobile_app');
    }

    /**
     * GET /api/getPODetail?PONum=xxx
     * ดึง PO + SO พร้อมกัน ส่งกลับ { poData, soInfo }
     */
    public function getPODetail(Request $request)
    {
        $request->validate([
            'PONum' => 'required|string|max:50',
        ]);

        $poNum = $request->query('PONum');

        try {
            // ── ขั้น 1: ดึง PO ก่อน ──
            $poResponse = Http::timeout(15)->get($this->apiBase . '/api/getPODetail', [
                'PONum' => $poNum,
            ]);

            if ($poResponse->failed()) {
                return response()->json([
                    'message' => 'server_update ตอบกลับ error (' . $poResponse->status() . ')',
                ], $poResponse->status());
            }

            $poData = $poResponse->json();

            // ── normalize เพื่อหา SONum ──
            $norm = $poData;
            if (is_array($norm) && isset($norm[0])) $norm = $norm[0];
            if (isset($norm['data'])) {
                $norm = is_array($norm['data']) && isset($norm['data'][0])
                    ? $norm['data'][0] : $norm['data'];
            }

            $soNum  = $norm['SONum'] ?? null;
            $soInfo = ['CustPONo' => '', 'CustName' => '', 'ResponseBy' => ''];

            // ── ขั้น 2: ดึง SO detail ──
            if ($soNum) {
                try {
                    $soResponse = Http::timeout(8)->get($this->apiBase . '/api/getSODetail', [
                        'SONum' => $soNum,
                    ]);

                    if ($soResponse->ok()) {
                        $so = $soResponse->json();
                        if (is_array($so) && isset($so[0])) $so = $so[0];
                        if (isset($so['data'])) {
                            $so = is_array($so['data']) && isset($so['data'][0])
                                ? $so['data'][0] : $so['data'];
                        }

                        $soInfo['CustPONo'] = $so['CustPONo']
                            ?? $so['SoStatus']['CustPONo']
                            ?? '';
                        $soInfo['CustName'] = $so['CustName']
                            ?? $so['SoStatus']['CustName']
                            ?? '';
                        $soInfo['ResponseBy'] = $so['SoDetail']['ResponseBy']
                            ?? $so['ResponseBy']
                            ?? '';
                    }
                } catch (\Exception $e) {
                    Log::warning('getSODetail failed: ' . $e->getMessage());
                }
            }

            return response()->json([
                'poData' => $poData,
                'soInfo' => $soInfo,
            ]);

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

    /**
     * GET /api/receivePO/history?PONum=xxx
     *
     * ส่งกลับ array ของแต่ละ line พร้อม:
     *   good_name, recv_qty, received_by, received_at, shelf, photo_url
     */
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

            // ลบไฟล์รูปที่แนบไว้ในแต่ละ line ก่อนลบ record
            PoReceiveLine::where('po_id', $validated['PONum'])
                ->whereNotNull('photo_path')
                ->pluck('photo_path')
                ->unique()
                ->each(function ($path) {
                    Storage::disk('public')->delete($path);
                });

            // ลบ line ทั้งหมดของ PO นี้ ให้กลับไปรับเข้าใหม่ได้
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