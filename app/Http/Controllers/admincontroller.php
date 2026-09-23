<?php


namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\tblsos;
use App\Models\tblcustomer;
use App\Models\bill_detail;
use App\Models\so_item_id;
use App\Models\Bill;
use function Laravel\Prompts\table;
use Illuminate\Support\Facades\Validator;

use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $this->requireLogin($request);
        $date = $request->get('date');
        $search = $request->get('search'); // คำค้นหา ใช้ค้นทุกแถวในระบบ ไม่ใช่แค่หน้าที่แสดงอยู่
        $message = null;  // กำหนดค่าเริ่มต้นให้กับตัวแปร $message

        // แสดงเฉพาะข้อมูลตั้งแต่วันที่นี้เป็นต้นไป (อิงวันที่ส่งของ date_of_dali)
        $startDate = '2026-09-19';

        $query = Bill::query()->whereDate('date_of_dali', '>=', $startDate);

        // ถ้าผู้ใช้กรอกวันที่ ให้กรองข้อมูลที่มีวันที่ตรงกับที่เลือก
        if ($date) {
            $query->whereDate('date_of_dali', $date); // ใช้ชื่อคอลัมน์วันที่ของคุณ
        }

        // ถ้าผู้ใช้พิมพ์คำค้นหา ให้ค้นจากรหัสลูกค้า, รหัส SO และเลขบิล (billid) ทั่วทั้งฐานข้อมูล
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('customer_id', 'like', "%{$search}%")
                  ->orWhere('so_id', 'like', "%{$search}%")
                  ->orWhere('billid', 'like', "%{$search}%");
            });
        }

        // ===== สรุปความคืบหน้าแต่ละขั้น (เปิดบิล / จัดเส้นทาง / ส่งสินค้า) =====
        // นับตามเงื่อนไขที่กรองอยู่ (วันที่/คำค้นหา) ถ้าไม่กรอง = ทั้งระบบ, ไม่นับรายการที่ยกเลิก (statuspdf = 6)
        $billTable = (new Bill)->getTable();
        $statsBase = clone $query;
        $active = function () use ($statsBase) {
            return (clone $statsBase)->where(function ($q) {
                $q->whereNull('statuspdf')->orWhere('statuspdf', '!=', 6);
            });
        };

        $activeCount    = $active()->count();
        $cancelledCount = (clone $statsBase)->where('statuspdf', 6)->count();

        $billDone  = $active()->where('statuspdf', 1)->count();
        // เงื่อนไข "จัดเส้นทางแล้ว" = มีแถวใน transaction_transport ที่มี time_pick
        // 2 ตารางใช้ collation ต่างกัน (general_ci / unicode_ci) ต้องบังคับให้ตรงกันก่อนเทียบ
        $routeExists = function ($q) use ($billTable) {
            $q->select(DB::raw(1))
              ->from('transaction_transport')
              ->whereRaw('transaction_transport.bill_id COLLATE utf8mb4_unicode_ci = ' . $billTable . '.so_detail_id COLLATE utf8mb4_unicode_ci')
              ->whereNotNull('transaction_transport.time_pick');
        };

        $routeDone = $active()->whereExists($routeExists)->count();
        $deliDone  = $active()->where('statusdeli', 'จัดส่งสำเร็จ')->count();

        $stageStats = [
            ['label' => 'เปิดบิลส่งของ', 'icon' => 'fa-file-invoice', 'done' => $billDone,  'pending' => $activeCount - $billDone],
            ['label' => 'จัดเส้นทาง',    'icon' => 'fa-route',        'done' => $routeDone, 'pending' => $activeCount - $routeDone],
            ['label' => 'ส่งสินค้า',      'icon' => 'fa-truck',        'done' => $deliDone,  'pending' => $activeCount - $deliDone],
        ];
        foreach ($stageStats as &$s) {
            $s['percent'] = $activeCount > 0 ? round($s['done'] * 100 / $activeCount) : 0;
        }
        unset($s);

        // ===== ฟิลเตอร์ตามสถานะแต่ละขั้น (ใช้กับตารางเท่านั้น การ์ดสรุปด้านบนยังนับตามวันที่/คำค้นหา) =====
        $notCancelled = function ($q) {
            $q->whereNull('statuspdf')->orWhere('statuspdf', '!=', 6);
        };

        $billStatus  = $request->get('bill_status');
        $routeStatus = $request->get('route_status');
        $deliStatus  = $request->get('deli_status');

        // เปิดบิลส่งของ
        if ($billStatus === 'done') {
            $query->where('statuspdf', 1);
        } elseif ($billStatus === 'pending') {
            $query->where(function ($q) {
                $q->whereNull('statuspdf')->orWhereNotIn('statuspdf', [1, 6]);
            });
        } elseif ($billStatus === 'cancel') {
            $query->where('statuspdf', 6);
        }

        // จัดเส้นทาง
        if ($routeStatus === 'done') {
            $query->where($notCancelled)->whereExists($routeExists);
        } elseif ($routeStatus === 'pending') {
            $query->where($notCancelled)->whereNotExists($routeExists);
        }

        // ส่งสินค้า
        $deliMap = ['success' => 'จัดส่งสำเร็จ', 'hold' => 'ค้างบิล', 'wrong' => 'สินค้าผิด'];
        if (isset($deliMap[$deliStatus])) {
            $query->where($notCancelled)->where('statusdeli', $deliMap[$deliStatus]);
        } elseif ($deliStatus === 'pending') {
            // "รอดำเนินการ" = ยังไม่มีผลส่งจริง (null, '', '0' หรือค่าอื่นที่ไม่ใช่ 3 สถานะผลส่ง)
            $query->where($notCancelled)->where(function ($q) use ($deliMap) {
                $q->whereNull('statusdeli')
                  ->orWhereNotIn('statusdeli', array_values($deliMap));
            });
        }

        $bill = $query->orderBy('so_id', 'desc') // เปลี่ยนมาเรียงตาม so_id ตามโครงสร้างจริง
                      ->paginate(200); // เปลี่ยนจาก get() เป็น paginate(200) เพื่อแบ่งหน้า

        // ตรวจสอบว่ามีข้อมูลหรือไม่
        if ($bill->isEmpty()) {
            $message = 'ไม่พบข้อมูลที่ตรงกับเงื่อนไขที่เลือก';
        }

        // คงค่า Query String (เช่น วันที่เลือก, คำค้นหา) ไว้ในลิงก์เปลี่ยนหน้า
        $bill->appends($request->all());

        // "จัดสินค้า": ดึงเวลาจริงจากตาราง transaction_transport (bill_id ในตารางนี้ เก็บ so_detail_id ของบิล)
        // เพื่อเอาเวลา (time_pick) และชื่อผู้จัด (name_pick) จริงจาก DB มาแสดง แทนการเดาจาก emp_picker เฉยๆ
        $soDetailIds = $bill->getCollection()->pluck('so_detail_id')->filter()->unique()->values()->toArray();

        $pickLogs = DB::table('transaction_transport')
            ->whereIn('bill_id', $soDetailIds)
            ->get()
            ->keyBy('bill_id');

        $bill->getCollection()->transform(function ($item) use ($pickLogs) {
            $log = $pickLogs->get($item->so_detail_id);
            $item->pack_name = $log->name_pick ?? null;
            $item->pack_time = $log->time_pick ?? null;
            // "ส่งสินค้า": เวลาและชื่อผู้กดยืนยันผลส่ง (บันทึกจากหน้า /billreceive)
            $item->deli_name = $log->check_name ?? null;
            $item->deli_time = $log->check_time ?? null;
            return $item;
        });

        // คำนวณจำนวนทั้งหมดในระบบ
        $totalCount = Bill::whereDate('date_of_dali', '>=', $startDate)->count();

        // คำนวณจำนวนเฉพาะในวันนี้ (อ้างอิงจากคอลัมน์ date_of_dali และวันที่ปัจจุบัน)
        $todayCount = Bill::whereDate('date_of_dali', Carbon::today())->count();

        return view('admin.dashboardadmin', compact('bill', 'message', 'totalCount', 'todayCount', 'stageStats', 'activeCount', 'cancelledCount', 'startDate'));
    }
    public function dashboardpdf(Request $request)
    {
        // หน้านี้ไม่ต้อง login
        $date = $request->get('date');
        $message = null;  // กำหนดค่าเริ่มต้นให้กับตัวแปร $message

        // ถ้าผู้ใช้กรอกวันที่ ให้กรองข้อมูลที่มีวันที่ตรงกับที่เลือก
        if ($date) {
            $bill = Bill::whereDate('date_of_dali', $date)  // ใช้ชื่อคอลัมน์ที่ถูกต้อง
                        ->orderBy('so_detail_id', 'desc')
                        ->get();

            // ตรวจสอบว่ามีข้อมูลหรือไม่
            if ($bill->isEmpty()) {
                $message = 'ไม่พบข้อมูลที่ตรงกับวันที่เลือก';
            }
        } else {
            // ถ้าไม่ได้กรอกวันที่ จะดึงข้อมูลทั้งหมด
            $bill = Bill::orderBy('so_detail_id', 'desc')
                        ->get();
        }

        return view('admin.dashboardadminpdf', compact('bill', 'message'));
    }

    public function adminroute(Request $request)
    {
        $this->requireLogin($request);
        $date = $request->get('date');
        $message = null;  // กำหนดค่าเริ่มต้นให้กับตัวแปร $message

        // ถ้าผู้ใช้กรอกวันที่ ให้กรองข้อมูลที่มีวันที่ตรงกับที่เลือก
        if ($date) {
            $bill = Bill::whereDate('date_of_dali', $date)  // ใช้ชื่อคอลัมน์ที่ถูกต้อง
                        ->orderBy('so_detail_id', 'desc')
                        ->get();

            // ตรวจสอบว่ามีข้อมูลหรือไม่
            if ($bill->isEmpty()) {
                $message = 'ไม่พบข้อมูลที่ตรงกับวันที่เลือก';
            }
        } else {
            // ถ้าไม่ได้กรอกวันที่ จะดึงข้อมูลทั้งหมด
            $bill = Bill::orderBy('so_detail_id', 'desc')
                        ->get();
        }

        return view('admin.adminroute', compact('bill', 'message'));
    }

    public function history(Request $request)
    {
        // หน้านี้ไม่ต้อง login
        $date = $request->get('date');
        $message = null;  // กำหนดค่าเริ่มต้นให้กับตัวแปร $message

        // ถ้าผู้ใช้กรอกวันที่ ให้กรองข้อมูลที่มีวันที่ตรงกับที่เลือก
        if ($date) {
            $bill = Bill::whereDate('time', $date)  // ใช้ชื่อคอลัมน์ที่ถูกต้อง
                        ->orderBy('so_detail_id', 'desc')
                        ->get();

            // ตรวจสอบว่ามีข้อมูลหรือไม่
            if ($bill->isEmpty()) {
                $message = 'ไม่พบข้อมูลที่ตรงกับวันที่เลือก';
            }
        } else {
            // ถ้าไม่ได้กรอกวันที่ จะดึงข้อมูลทั้งหมด
            $bill = Bill::orderBy('so_detail_id', 'desc')
                        ->get();
        }

        return view('admin.history', compact('bill', 'message'));
    }

public function updateStatus(Request $request)
{
    // ตรวจสอบว่ามีค่า soDetailIds ส่งมาหรือไม่
    $soDetailIds = $request->input('soDetailIds');
    if (empty($soDetailIds)) {
        return response()->json(['success' => false, 'message' => 'No SO Detail IDs provided'], 400);
    }

    try {
        // อัปเดตสถานะจาก 0 เป็น 1
        DB::table('tblbill')
            ->whereIn('so_detail_id', $soDetailIds)
            ->update(['status' => 1]);

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        // จัดการข้อผิดพลาดที่เกิดขึ้น
        return response()->json(['success' => false, 'message' => 'Failed to update status', 'error' => $e->getMessage()], 500);
    }
}
public function updateStatuspdf(Request $request)
{
    $soDetailIds = $request->input('soDetailIds');
    if (empty($soDetailIds)) {
        return response()->json(['success' => false, 'message' => 'No SO Detail IDs provided'], 400);
    }

    try {
        DB::table('tblbill')
            ->whereIn('so_detail_id', $soDetailIds)
            ->update([
                'statuspdf'  => 1,
                'print_time' => \Carbon\Carbon::now('Asia/Bangkok'),
            ]);

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Failed to update status', 'error' => $e->getMessage()], 500);
    }
}
public function updateStatuspdfback(Request $request)
{
    // ตรวจสอบว่ามีค่า soDetailIds ส่งมาหรือไม่
    $soDetailIds = $request->input('soDetailIds');
    if (empty($soDetailIds)) {
        return response()->json(['success' => false, 'message' => 'No SO Detail IDs provided'], 400);
    }

    try {
        // อัปเดตสถานะจาก 0 เป็น 1
        DB::table('tblbill')
            ->whereIn('so_detail_id', $soDetailIds)
            ->update([
                'statuspdf' => 1,
                'status' => 0
            ]);


        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        // จัดการข้อผิดพลาดที่เกิดขึ้น
        return response()->json(['success' => false, 'message' => 'Failed to update status', 'error' => $e->getMessage()], 500);
    }
}
// app/Http/Controllers/BillController.php
public function updateBillIssue(Request $request)
{
    $request->validate([
        'so_detail_id' => 'required',
        'bill_issue_no' => 'required|string|max:255',
    ]);

    $bill = Bill::where('so_detail_id', $request->so_detail_id)->first();

    if (!$bill) {
        return response()->json(['message' => 'ไม่พบข้อมูล so_detail_id'], 404);
    }

    $bill->bill_issue_no = $request->bill_issue_no;
    $bill->save();

    return response()->json(['message' => 'อัปเดตสำเร็จ']);
}

public function updateStatuspdf2(Request $request)
{
    // ตรวจสอบว่ามีค่า soDetailIds ส่งมาหรือไม่
    $soDetailIds = $request->input('soDetailIds');
    if (empty($soDetailIds)) {
        return response()->json(['success' => false, 'message' => 'No SO Detail IDs provided'], 400);
    }

    try {
        // อัปเดตสถานะจาก 0 เป็น 1
        DB::table('tblbill')
            ->whereIn('so_detail_id', $soDetailIds)
                ->update([
                        'statuspdf' => 2,
                        'status' => 1
                    ]);

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        // จัดการข้อผิดพลาดที่เกิดขึ้น
        return response()->json(['success' => false, 'message' => 'Failed to update status', 'error' => $e->getMessage()], 500);
    }
}
public function updateDeliveryDate(Request $request)
{
    try {
        // ตรวจสอบข้อมูลที่ส่งมา
        $validator = Validator::make($request->all(), [
            'so_detail_id' => 'required',
            'new_date' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'ข้อมูลไม่ถูกต้อง: ' . $validator->errors()->first()
            ], 422);
        }

        // ตรวจสอบก่อนว่ามีข้อมูลในฐานข้อมูลหรือไม่
        $existing = DB::table('tblbill')
            ->where('so_detail_id', $request->so_detail_id)
            ->first();

        if (!$existing) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบข้อมูลที่ต้องการอัปเดต'
            ], 404);
        }

        // อัปเดตข้อมูล
        $updated = DB::table('tblbill')
            ->where('so_detail_id', $request->so_detail_id)
            ->update([
                'date_of_dali' => $request->new_date,
            ]);

        if ($updated) {
            return response()->json([
                'success' => true,
                'message' => 'อัปเดตวันที่ส่งของเรียบร้อยแล้ว'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถอัปเดตข้อมูลได้'
            ], 500);
        }
    } catch (\Exception $e) {
        // บันทึกข้อผิดพลาดลง log
        \Log::error('Error updating delivery date: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
        ], 500);
    }
}
public function upload(Request $request)
{
    $request->validate([
        'pdffile' => 'required|mimes:pdf|max:10240'
    ]);

    $file = $request->file('pdffile');
    $originalName = $file->getClientOriginalName();

    // โฟลเดอร์ปลายทางทั้ง 2
    $path1 = storage_path('app/public/doc_document');
    $path2 = storage_path('app/public/bill_document');

    // สร้างโฟลเดอร์ถ้ายังไม่มี
    if (!file_exists($path1)) mkdir($path1, 0777, true);
    if (!file_exists($path2)) mkdir($path2, 0777, true);

    // move ครั้งแรก
    $file->move($path1, $originalName);

    // copy ไปอีกโฟลเดอร์
    copy($path1 . '/' . $originalName, $path2 . '/' . $originalName);

    return back()->with('success', 'อัปโหลดไฟล์ ' . $originalName . ' เรียบร้อยแล้ว!');
}

public function uploadBillIssue(Request $request)
{
    $request->validate([
        'bill_issue_no' => 'required|string',
        'pdffilebillissue' => 'required|mimes:pdf|max:10240'
    ]);

    $billIssueNo = $request->bill_issue_no;
    $file = $request->file('pdffilebillissue');

    $filename = $billIssueNo . '.pdf';

    // จัดเก็บไฟล์ใน storage/app/public/billissue_document
    $file->storeAs('public/billissue_document', $filename);

    return back()->with('message', '✅ อัปโหลดไฟล์สำเร็จ');
}
public function updateStatuspdfcan(Request $request)
{
    // ตรวจสอบว่ามีค่า soDetailIds ส่งมาหรือไม่
    $soDetailIds = $request->input('soDetailIds');
    if (empty($soDetailIds)) {
        return response()->json(['success' => false, 'message' => 'No SO Detail IDs provided'], 400);
    }

    try {
        // อัปเดตสถานะจาก 0 เป็น 1
        DB::table('tblbill')
            ->whereIn('so_detail_id', $soDetailIds)
            ->update(['statuspdf' => '6']);

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        // จัดการข้อผิดพลาดที่เกิดขึ้น
        return response()->json(['success' => false, 'message' => 'Failed to update status', 'error' => $e->getMessage()], 500);
    }
}
}