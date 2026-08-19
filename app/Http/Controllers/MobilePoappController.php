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
        // ★ verify ticket แบบเดียวกับ /solist ถ้ามี ticket ส่งมา
        $ticket = $request->input('ticket');

        if ($ticket && !Auth::guard('web')->check()) {
            $ticketRecord = SsoTicket::where('ticket', $ticket)
                ->where('client_key', '3e')
                ->first();

            if ($ticketRecord && $ticketRecord->markAsUsed()) {
                $user = UserAuth::find($ticketRecord->id_emp);
                if ($user && $user->is_active) {
                    Auth::guard('web')->login($user);
                    Log::info("mobile-app: SSO login success user={$user->id_emp}");
                } else {
                    Log::warning("mobile-app: ticket valid but user not found/inactive id_emp={$ticketRecord->id_emp}");
                }
            } else {
                Log::warning("mobile-app: invalid or expired ticket={$ticket}");
            }
        }

        // ★ ถ้ายังไม่ login เลย (ไม่มี ticket และไม่มี session) → บล็อกการเข้าถึง
        if (!Auth::guard('web')->check()) {
            abort(403, 'กรุณาเข้าใช้งานผ่านเมนูหลัก');
        }

        return view('po.mobile_app');
    }

    /**
     * GET /api/getPODetail?PONum=xxx
     * ดึง PO + SO พร้อมกัน ส่งกลับ { poData, soInfo }
     */
/**
     * GET /api/getPODetail?PONum=xxx
     * ดึง PO + SO พร้อมกัน ส่งกลับ { poData, soInfo }
     */
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

            // ★ เช็คระบบเก่า: store.statusArea = '0' แปลว่าเช็คของออก (DATECHECKOUT) ไปแล้ว
            // PONum ที่ค้นหา / DocuNo ที่ตอบกลับมา อาจมี prefix "PO" ปนมา ตัดออกก่อนเทียบกับ store.PO (เก็บแบบดิบ ไม่มี prefix)
            $poNumClean  = preg_replace('/^PO/i', '', (string) $poNum);
            $docuNoClean = $docuNo ? preg_replace('/^PO/i', '', (string) $docuNo) : null;

            $checkedOutLegacy = DB::connection(self::LEGACY_CONNECTION)->table('store')
                ->where('statusArea', '0')
                ->where(function ($q) use ($poNumClean, $docuNoClean) {
                    $q->where('PO', $poNumClean);
                    if ($docuNoClean && $docuNoClean !== $poNumClean) {
                        $q->orWhere('PO', $docuNoClean);
                    }
                })
                ->orderByDesc('DATECHECKOUT')
                ->first();

            if ($checkedOutLegacy) {
                return response()->json([
                    'checked_out' => true,
                    'message'     => 'PO นี้ถูกเช็คของออก (ระบบเก่า) ไปแล้ว ไม่สามารถรับเข้าเพิ่มได้',
                    'po_id'       => $checkedOutLegacy->PO,
                    'so_id'       => $checkedOutLegacy->SO,
                    'checkout_by' => null, // ระบบเก่าไม่มีชื่อคนเช็คของออกเก็บไว้จริง (boxS คือชื่อกล่อง)
                    'checkout_at' => $checkedOutLegacy->DATECHECKOUT,
                ], 409);
            }

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