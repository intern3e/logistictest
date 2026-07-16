<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class MobilePoappController extends Controller
{
    private string $apiBase = 'http://server_update:8000';
    public function index()
    {
        return view('po.mobile_app');
    }
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
                    'message' => 'server_update ตอบกลับ error (' . $response->status() . ')',
                ], $response->status());
            }
 
            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'เชื่อมต่อ server_update ไม่ได้: ' . $e->getMessage(),
            ], 502);
        }
    }
    public function receivePO(Request $request)
    {
        $validated = $request->validate([
            'PONum'            => 'required|string|max:50',
            'POID'             => 'required|string|max:20',
            'items'            => 'required|array|min:1',
            'items.*.ListNo'   => 'required',
            'items.*.GoodID'   => 'required',
            'items.*.RecvQty'  => 'required|numeric|gt:0',
        ]);
 
        // ============================================================
        // ทางเลือก A: ส่งต่อไปให้ server_update บันทึก (proxy)
        // ============================================================
        try {
            $response = Http::timeout(15)->post($this->apiBase . '/api/receivePO', $validated);
 
            if ($response->failed()) {
                return response()->json([
                    'message' => 'บันทึกไม่สำเร็จ (' . $response->status() . ')',
                ], $response->status());
            }
 
            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'เชื่อมต่อ server_update ไม่ได้: ' . $e->getMessage(),
            ], 502);
        }
 
        // ============================================================
        // ทางเลือก B: บันทึกลง DB ของ Laravel เอง — ลบ block ด้านบน
        // แล้วเปิด comment ด้านล่างนี้แทน (แก้ชื่อตารางให้ตรงกับระบบคุณ)
        // ============================================================
        /*
        foreach ($validated['items'] as $item) {
            \DB::table('po_receives')->insert([
                'poid'       => $validated['POID'],
                'ponum'      => $validated['PONum'],
                'list_no'    => $item['ListNo'],
                'good_id'    => $item['GoodID'],
                'good_name'  => $item['GoodName'] ?? null,
                'recv_qty'   => $item['RecvQty'],
                'recv_by'    => auth()->id(),
                'created_at' => now(),
            ]);
        }
 
        return response()->json([
            'message' => 'รับเข้าสำเร็จ ' . count($validated['items']) . ' รายการ',
        ]);
        */
    }
}
