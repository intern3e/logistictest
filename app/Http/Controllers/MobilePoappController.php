<?php

namespace App\Http\Controllers;

use App\Models\PoReceive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class MobilePoappController extends Controller
{
    private string $apiBase = 'http://server_update:8000';

    public function index()
    {
        return view('po.mobile_app');
    }

    /**
     * GET /api/getPODetail?PONum=xxx
     * proxy ไปดึงรายละเอียด PO จาก server_update (กัน CORS)
     */
    public function getPODetail(Request $request)
    {
        $request->validate([
            'PONum' => 'required|string|max:50',
        ]);

        try {
            $response = Http::timeout(15)->get($this->apiBase . '/api/getPODetail', [
                'PONum' => $request->query('PONum'),
            ]);

            if ($response->failed()) {
                return response()->json([
                    'message' => 'server_update ตอบกลับ error (' . $response->status() . ') สำหรับ PO: ' . $request->query('PONum'),
                ], $response->status());
            }

            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'เชื่อมต่อ server_update ไม่ได้: ' . $e->getMessage(),
            ], 502);
        }
    }

    /**
     * POST /api/receivePO
     * บันทึกการรับสินค้าเข้า — ตารางเดียว 1 แถวต่อสินค้า 1 รายการ
     * payload: { PONum, Shelf?, Photo?, items: [{GoodID, GoodName?, UnitPrice?, RecvQty}] }
     * ชั้นวาง / รูป / ผู้บันทึก / เวลา ใช้ค่าเดียวกันทุกแถวของการกดรับครั้งนี้
     */
    public function receivePO(Request $request)
    {
        $validated = $request->validate([
            'PONum'             => 'required|string|max:50',
            'Shelf'             => 'nullable|string|max:100',
            'Photo'             => 'nullable|string',
            'items'             => 'required|array|min:1',
            'items.*.GoodID'    => 'required',
            'items.*.GoodName'  => 'nullable|string|max:500',
            'items.*.UnitPrice' => 'nullable|numeric',
            'items.*.RecvQty'   => 'required|numeric|gt:0',
        ]);

        // เซฟไฟล์รูปก่อน (ชื่อไฟล์ = เลข PO, ซ้ำแล้วไล่ _2, _3, ...)
        $photoPath  = $this->savePhotoBase64($validated['Photo'] ?? null, $validated['PONum']);
        $receivedAt = now();
        $receivedBy = optional($request->user())->name;

        // กระตุ้นให้ Model จัดโครงสร้างตาราง (สร้างตาราง/เติมคอลัมน์) ก่อน
        new PoReceive();

        // ดึงรายชื่อคอลัมน์ที่มีอยู่จริงในตาราง — insert เฉพาะคอลัมน์ที่มีจริงเท่านั้น
        // ต่อให้เติมคอลัมน์อัตโนมัติไม่สำเร็จ (สิทธิ์ DB ไม่พอ) ก็จะไม่พังเพราะ unknown column
        $columns = array_flip(\Illuminate\Support\Facades\Schema::getColumnListing('po_receives'));

        if (empty($columns)) {
            return response()->json([
                'message' => 'ไม่พบตาราง po_receives ในฐานข้อมูล และสร้างอัตโนมัติไม่สำเร็จ (เช็กสิทธิ์ DB)',
            ], 500);
        }

        try {
            $rows = DB::transaction(function () use ($validated, $photoPath, $receivedAt, $receivedBy, $columns) {
                $rows = [];

                foreach ($validated['items'] as $it) {
                    $data = [
                        'po_num'      => $validated['PONum'],
                        'good_id'     => $it['GoodID'],
                        'good_name'   => $it['GoodName'] ?? null,
                        'recv_qty'    => $it['RecvQty'],
                        'unit_price'  => $it['UnitPrice'] ?? null,
                        'shelf'       => $validated['Shelf'] ?? null,
                        'photo_path'  => $photoPath,
                        'received_by' => $receivedBy,
                        'received_at' => $receivedAt,
                    ];

                    // ตัดคีย์ที่ไม่มีคอลัมน์รองรับออก
                    $rows[] = PoReceive::create(array_intersect_key($data, $columns));
                }

                return $rows;
            });
        } catch (\Exception $e) {
            // บันทึก DB ไม่สำเร็จ → ลบไฟล์รูปที่เพิ่งเซฟทิ้ง กันไฟล์ค้าง
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }

            return response()->json([
                'message' => 'บันทึกลงฐานข้อมูลไม่สำเร็จ: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success'   => true,
            'row_count' => count($rows),
            'photo_url' => $photoPath ? Storage::disk('public')->url($photoPath) : null,
            'message'   => 'รับเข้าสำเร็จ ' . count($rows) . ' รายการ',
        ]);
    }

    /**
     * GET /api/receivePO/history?PONum=xxx
     * ดูประวัติการรับเข้า — คืนเป็นแถวตรง ๆ จากตารางเดียว
     */
    public function history(Request $request)
    {
        $query = PoReceive::query()->latest('received_at');

        if ($request->filled('PONum')) {
            $query->where('po_num', $request->query('PONum'));
        }

        return response()->json(
            $query->limit(200)->get()->map(function ($row) {
                $row->photo_url = $row->photoUrl();

                return $row;
            })
        );
    }

    /**
     * แปลง base64 dataURL ของรูปแล้วเซฟลง storage/app/public/po-receive
     * ชื่อไฟล์ = เลข PO เช่น "po-receive/PO6907-01884.jpg"
     * PO เดิมรับหลายรอบ → ไล่ _2, _3, ... กันรูปเก่าโดนทับ
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
}