<?php

namespace App\Http\Controllers;
use App\Models\Bill;
use App\Models\docBills;
use App\Models\pobills;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\log;

class AlertController extends Controller
{
    public function dashboard(Request $request)
        {
            $date = $request->input('date');
            $missingBills = [];
            $error = null;

            Log::info('========== AlertBill START ==========');
            Log::info('Request date: ' . ($date ?? 'null'));

            if ($date) {
                try {
                    $apiUrl = "http://server_update:8000/api/getTotalBillByDate?date={$date}";
                    Log::info('Calling API: ' . $apiUrl);

                    $response = Http::timeout(15)->get($apiUrl);

                    Log::info('API HTTP status: ' . $response->status());
                    Log::info('API raw body: ' . $response->body());

                    if ($response->successful()) {
                        $json = $response->json();
                        $apiData = $json['data'] ?? [];

                        Log::info('API data count: ' . count($apiData));
                        Log::info('API first item: ' . json_encode($apiData[0] ?? null));

                        // สร้าง prefix 4yymm
                        $carbon = \Carbon\Carbon::parse($date);
                        $yy = $carbon->format('y');
                        $mm = $carbon->format('m');
                        $prefix = '4' . $yy . $mm;

                        Log::info("Prefix calculated (ค.ศ.): {$prefix}");

                        // ลอง prefix แบบ พ.ศ. ด้วย เผื่อใช้
                        $yyTh = (int)$carbon->format('Y') + 543;
                        $prefixTh = '4' . substr($yyTh, -2) . $mm;
                        Log::info("Prefix calculated (พ.ศ.): {$prefixTh}");

                        // กรองเฉพาะบิลที่ขึ้นต้นด้วย prefix (ลองทั้ง 2 แบบ)
                        $filtered = collect($apiData)->filter(function ($item) use ($prefix, $prefixTh) {
                            $docuNo = $item['DocuNo'] ?? '';
                            return str_starts_with($docuNo, $prefix) || str_starts_with($docuNo, $prefixTh);
                        })->values();

                        Log::info('Filtered count (matched prefix): ' . $filtered->count());
                        Log::info('Filtered sample: ' . json_encode($filtered->take(3)->all()));

                        $apiBillIds = $filtered->pluck('DocuNo')->filter()->unique()->toArray();
                        Log::info('API billIds: ' . json_encode($apiBillIds));

                        if (!empty($apiBillIds)) {
                            $existingBillIds = DB::table('tblbill')
                                ->whereIn('billid', $apiBillIds)
                                ->pluck('billid')
                                ->toArray();

                            Log::info('Existing in DB: ' . json_encode($existingBillIds));

                            $missingBillIds = array_diff($apiBillIds, $existingBillIds);
                            Log::info('Missing billIds: ' . json_encode($missingBillIds));
                                $missingBills = $filtered
                                    ->whereIn('DocuNo', $missingBillIds)
                                    ->sortBy('DocuNo')
                                    ->values()
                                    ->all();

                            Log::info('Missing bills count: ' . count($missingBills));
                        } else {
                            Log::warning('No bills matched prefix - apiBillIds is empty');
                        }
                    } else {
                        $error = 'API ตอบกลับไม่สำเร็จ (HTTP ' . $response->status() . ')';
                        Log::error($error);
                    }
                } catch (\Exception $e) {
                    $error = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
                    Log::error('Exception: ' . $e->getMessage());
                    Log::error($e->getTraceAsString());
                }
            }

                // สร้าง pagination 100 ต่อหน้า
                $perPage = 100;
                $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
                $collection = collect($missingBills);
                $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
                    $collection->forPage($currentPage, $perPage)->values(),
                    $collection->count(),
                    $perPage,
                    $currentPage,
                    ['path' => $request->url(), 'query' => $request->query()]
                );

                Log::info('Final missingBills count: ' . count($missingBills));
                Log::info('========== AlertBill END ==========');

                return view('alert.alertbill', [
                    'missingBills' => $paginated,
                    'date' => $date,
                    'error' => $error,
                ]);
        }
 public function updatesolve(Request $request)
{
    try {
        $id = $request->input('id');
        $table = $request->input('table');
        $solve = $request->input('solve');

        if (!$id || !$table || !$solve) {
            return response()->json(['status' => 'error', 'message' => 'Missing parameters'], 400);
        }

        switch ($table) {
            case 'tblbill':
                $item = Bill::where('so_detail_id', $id)->first();
                break;
            case 'pobills':
                $item = PoBills::where('po_detail_id', $id)->first();
                break;
            case 'docbills':
                $item = DocBills::where('doc_id', $id)->first();
                break;
            default:
                return response()->json(['status' => 'error', 'message' => 'ไม่รู้จักชื่อตาราง'], 400);
        }

        if (!$item) {
            return response()->json(['status' => 'error', 'message' => 'ไม่พบข้อมูล'], 404);
        }

        $item->solve = $solve;
        $item->save();

        return response()->json(['status' => 'success', 'message' => 'อัปเดตข้อมูลสำเร็จ']);
    } catch (\Throwable $e) {
        \Log::error('Update Solve Error: ' . $e->getMessage());
        return response()->json([
            'status' => 'error',
            'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
        ], 500);
    }
}


 public function dashboardaccount(Request $request)
    {

        $bill = Bill::orderBy('so_detail_id', 'desc')
                        ->get();
        $items = $bill; // เพิ่มบรรทัดนี้
        return view('alert.alertaccount', compact('bill'));
    }
public function finish(Request $request)
{
    // ตรวจสอบว่าได้ส่ง so_detail_id มาหรือไม่
    $soDetailId = $request->input('so_detail_id');

    // ค้นหาข้อมูลในฐานข้อมูลโดยใช้ so_detail_id
    $item = bill::where('so_detail_id', $soDetailId)->first();

    if ($item) {
        // หากพบข้อมูล ให้ทำการอัปเดตค่า NG เป็น null
        $item->statuspdf = 4;
        $item->save();
        return response()->json(['status' => 'success', 'message' => 'เสร็จสิ้น']);
    
    }

    // หากไม่พบข้อมูล
    return response()->json(['status' => 'error', 'message' => 'so_detail_id not found']);
}
public function getBillDetail(Request $request, $id)
{
    try {
        $results = collect();

        // ค้นหาใน bill_detail (tblbill)
        $tblbill = DB::table('bill_detail')->where('so_detail_id', $id)->get();

        // ค้นหาใน pobills_detail (pobills)
        $pobills = DB::table('pobills_detail')->where('po_detail_id', $id)->get();

        // ค้นหาใน doc_detail (docbills)
        $docbills = DB::table('doc_detail')->where('doc_id', $id)->get();

        // รวมข้อมูลทั้งหมด
        $results = $results->merge($tblbill)->merge($pobills)->merge($docbills);

        return response()->json($results);

    } catch (\Exception $e) {
        \Log::error('getBillDetail error: ' . $e->getMessage());
        return response()->json(['error' => 'Server error'], 500);
    }
}

}
