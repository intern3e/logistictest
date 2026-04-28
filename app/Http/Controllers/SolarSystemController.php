<?php

namespace App\Http\Controllers;

use App\Models\SolarAccount;
use App\Models\SolarCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SolarSystemController extends Controller
{
    /* =========================================================
     |  STATIC DATA — เอกสารและขั้นตอน Gantt (ไม่ต้องมี table)
     |========================================================= */
    private const PERMIT_DOCS = [
        ['name' => 'เอกสารคำร้องขอ ข.1 หรือ ข.2 และอื่นๆ', 'status' => 'มี'],
        ['name' => 'สำเนาหนังสือรับรองการจดทะเบียนนิติบุคคล ไม่เกิน 6 เดือน 1 ชุด', 'status' => 'มี'],
        ['name' => 'สำเนาหรือภาพถ่ายโฉนดที่ดินเท่าต้นฉบับ พร้อมลงนาม', 'status' => 'มี'],
        ['name' => 'หนังสือยินยอมของเจ้าของที่ดิน (กรณีไม่ใช่เจ้าของที่ดิน) 1 ชุด', 'status' => 'ไม่ใช้'],
        ['name' => 'สำเนาบัตรประชาชนและทะเบียนบ้านของผู้ขออนุญาต ผู้รับมอบอำนาจ และผู้มีอำนาจลงนามแทนนิติบุคคล พร้อมลงนาม 1 ชุด', 'status' => 'ยังไม่ครบ'],
        ['name' => 'หนังสือแสดงความยินยอมและรับรองของสถาปนิก วิศวกรผู้ออกแบบ พร้อมสำเนาใบอนุญาตผู้ประกอบวิชาชีพ 1 ชุด', 'status' => 'มี'],
        ['name' => 'แผนผังบริเวณ แบบแปลน และรายการประกอบแบบแปลน 5 ชุด', 'status' => 'มี'],
    ];

    private const NOTIFY_DOCS = [
        ['name' => 'หนังสือแจ้งการติดตั้งแผงเซลล์แสงอาทิตย์', 'qty' => '1 ฉบับ'],
        ['name' => 'หนังสือแบบไฟฟ้า พร้อมลายเซ็นต์จากวิศวกรไฟฟ้า', 'qty' => '1 ชุด'],
        ['name' => 'หนังสือแสดงความยินยอมและรับรองของสถาปนิก/วิศวกร พร้อมสำเนาใบอนุญาต', 'qty' => '1 ชุด'],
        ['name' => 'สำเนาหนังสือรับรองการจดทะเบียนนิติบุคคล ไม่เกิน 6 เดือน', 'qty' => '1 ชุด'],
        ['name' => 'หนังสือมอบอำนาจการขอเชื่อมต่อกับระบบโครงข่ายไฟฟ้า กฟน.', 'qty' => '1 ฉบับ'],
        ['name' => 'สำเนาบัตรประชาชนและทะเบียนบ้านของผู้ขออนุญาต และผู้มีอำนาจลงนาม', 'qty' => '1 ชุด'],
    ];

    private const WORK_STEPS = [
        ['step_no' => 1,  'name' => 'วางแผนและออกแบบ (แบบไฟฟ้า, หม้อแปลง, โยธา, เอกสาร) ส่งลูกค้าอนุมัติ', 'start_day' => 1,  'duration_days' => 14, 'category' => 'design'],
        ['step_no' => 2,  'name' => 'ยื่นแบบโยธา อ.1', 'start_day' => 10, 'duration_days' => 20, 'category' => 'permit'],
        ['step_no' => 3,  'name' => 'จัดซื้อวัสดุอุปกรณ์ทั้งหมด', 'start_day' => 10, 'duration_days' => 15, 'category' => 'procure'],
        ['step_no' => 4,  'name' => 'Install Mounting, Walkway', 'start_day' => 25, 'duration_days' => 5,  'category' => 'install'],
        ['step_no' => 5,  'name' => 'Install Wireway, Cable tray', 'start_day' => 25, 'duration_days' => 5,  'category' => 'install'],
        ['step_no' => 6,  'name' => 'สร้างห้อง Inverter', 'start_day' => 25, 'duration_days' => 7,  'category' => 'design'],
        ['step_no' => 7,  'name' => 'Install Inverter, Optimizer', 'start_day' => 30, 'duration_days' => 5,  'category' => 'install'],
        ['step_no' => 8,  'name' => 'Install PV Module', 'start_day' => 30, 'duration_days' => 5,  'category' => 'install'],
        ['step_no' => 9,  'name' => 'Wiring DC', 'start_day' => 35, 'duration_days' => 5,  'category' => 'wire'],
        ['step_no' => 10, 'name' => 'Wiring AC', 'start_day' => 38, 'duration_days' => 5,  'category' => 'wire'],
        ['step_no' => 11, 'name' => 'ดับไฟ ขนานไฟฟ้าเข้ากับระบบของโรงงาน (ยังไม่ออนไลน์)', 'start_day' => 43, 'duration_days' => 3,  'category' => 'power'],
        ['step_no' => 12, 'name' => 'ขนานไฟแรงสูง DTVT', 'start_day' => 43, 'duration_days' => 5,  'category' => 'power'],
        ['step_no' => 13, 'name' => 'ตรวจสอบระบบโดยวิศวกรก่อนเปิดใช้งาน', 'start_day' => 48, 'duration_days' => 3,  'category' => 'verify'],
        ['step_no' => 14, 'name' => 'Commissioning test, Install plant', 'start_day' => 50, 'duration_days' => 5,  'category' => 'verify'],
        ['step_no' => 15, 'name' => 'ยื่นขนานไฟฟ้า กกพ.', 'start_day' => 55, 'duration_days' => 6,  'category' => 'verify'],
    ];

    /* =========================================================
     |  PAGE — แสดง dashboard
     |========================================================= */
    public function index()
    {
        $customers = SolarCustomer::orderBy('id')->get();
        $accounts  = SolarAccount::orderBy('id')->get();

        $visible        = $customers->where('status', '!=', 'ติดตั้งสำเร็จ');
        $kwSum          = $visible->sum(fn($c) => $c->kwNumber());
        $cntQuote       = $visible->where('status', 'เสนอ')->count();
        $cntClosed      = $visible->where('status', 'ปิดการขาย')->count();
        $cntInstalling  = $visible->where('status', 'กำลังติดตั้ง')->count();
        $installedTotal = $customers->where('status', 'ติดตั้งสำเร็จ')->count();

        return view('project.indexsolarplan', [
            'customers'      => $customers,
            'accounts'       => $accounts,
            'permitDocs'     => self::PERMIT_DOCS,
            'notifyDocs'     => self::NOTIFY_DOCS,
            'workSteps'      => self::WORK_STEPS,
            'kwSum'          => $kwSum,
            'cntQuote'       => $cntQuote,
            'cntClosed'      => $cntClosed,
            'cntInstalling'  => $cntInstalling,
            'installedTotal' => $installedTotal,
        ]);
    }

    /* =========================================================
     |  CUSTOMERS
     |========================================================= */

    /** GET /solar/customers/{id} */
    public function customerShow($id)
    {
        return response()->json(SolarCustomer::findOrFail($id));
    }

    /** POST /solar/customers */
    public function customerStore(Request $r)
    {
        $data = $this->validateCustomer($r);

        $data['status']     = $data['status']     ?? 'เสนอ';
        $data['wash_cycle'] = $data['wash_cycle'] ?? 6;
        $data['date']       = $data['date']       ?? now()->toDateString();
        $data['wash_logs']  = [];

        $c = SolarCustomer::create($data);

        return response()->json([
            'ok'       => true,
            'message'  => 'เพิ่มลูกค้าเรียบร้อย',
            'customer' => $c,
        ]);
    }

    /** PUT /solar/customers/{id} */
    public function customerUpdate(Request $r, $id)
    {
        $c = SolarCustomer::findOrFail($id);

        if ($c->is_extra) {
            return response()->json(['ok' => false, 'message' => 'ไม่สามารถแก้ไขระบบเก่า'], 422);
        }

        $data = $this->validateCustomer($r, true);
        $c->fill($data);

        // ถ้าเปลี่ยน wash_cycle และมีวันล่าสุดอยู่ — recalc wash_next
        if ($r->has('wash_cycle') && $c->wash_current) {
            $c->wash_next = Carbon::parse($c->wash_current)
                ->addMonths($c->wash_cycle ?: 6)
                ->toDateString();
        }

        $c->save();

        return response()->json([
            'ok'       => true,
            'message'  => 'บันทึกข้อมูลเรียบร้อย',
            'customer' => $c,
        ]);
    }

    /** PATCH /solar/customers/{id}/status */
    public function customerUpdateStatus(Request $r, $id)
    {
        $r->validate([
            'status' => 'required|string|in:เสนอ,ปิดการขาย,กำลังติดตั้ง,ติดตั้งสำเร็จ',
        ]);

        $c = SolarCustomer::findOrFail($id);

        if ($c->status === 'ติดตั้งสำเร็จ') {
            return response()->json([
                'ok'      => false,
                'message' => 'ไม่สามารถเปลี่ยนสถานะที่ติดตั้งสำเร็จแล้ว',
            ], 422);
        }

        $c->status = $r->status;
        $c->save();

        return response()->json(['ok' => true, 'customer' => $c]);
    }

    /** PATCH /solar/customers/{id}/notes */
    public function customerUpdateNotes(Request $r, $id)
    {
        $r->validate(['notes' => 'nullable|string']);

        $c = SolarCustomer::findOrFail($id);
        if ($c->is_extra) {
            return response()->json(['ok' => false, 'message' => 'ไม่สามารถแก้ไขระบบเก่า'], 422);
        }

        $c->notes = $r->notes;
        $c->save();

        return response()->json(['ok' => true, 'customer' => $c]);
    }

    /** DELETE /solar/customers/{id} */
    public function customerDestroy($id)
    {
        $c = SolarCustomer::findOrFail($id);

        if ($c->is_extra) {
            return response()->json(['ok' => false, 'message' => 'ไม่สามารถลบระบบเก่าได้'], 422);
        }

        $c->delete();

        return response()->json(['ok' => true, 'message' => 'ลบเรียบร้อย']);
    }

    /* =========================================================
     |  WASH LOGS — ทำงานบน JSON column ของ customer
     |========================================================= */

    /** POST /solar/customers/{id}/wash-logs */
    public function washStore(Request $r, $id)
    {
        $data = $r->validate([
            'wash_date' => 'required|date',
            'tech'      => 'required|string|max:100',
            'note'      => 'nullable|string',
        ]);

        $c = SolarCustomer::findOrFail($id);
        $c->addWashLog($data['wash_date'], $data['tech'], $data['note'] ?? '');

        return response()->json([
            'ok'       => true,
            'message'  => 'บันทึกการล้างเรียบร้อย',
            'customer' => $c->fresh(),
        ]);
    }

    /** DELETE /solar/customers/{id}/wash-logs/{num} */
    public function washDestroy($id, $num)
    {
        $c = SolarCustomer::findOrFail($id);

        $exists = collect($c->washLogsArr())->contains(fn($w) => (int)($w['num'] ?? 0) === (int)$num);
        if (!$exists) {
            return response()->json(['ok' => false, 'message' => 'ไม่พบรายการ'], 404);
        }

        $c->removeWashLog((int)$num);

        return response()->json([
            'ok'       => true,
            'message'  => 'ลบเรียบร้อย',
            'customer' => $c->fresh(),
        ]);
    }

    /* =========================================================
     |  ACCOUNTS
     |========================================================= */

    public function accountStore(Request $r)
    {
        $data = $this->validateAccount($r);
        $a    = SolarAccount::create($data);

        return response()->json([
            'ok'      => true,
            'message' => 'เพิ่มบัญชีเรียบร้อย',
            'account' => $a,
        ]);
    }

    public function accountUpdate(Request $r, $id)
    {
        $a    = SolarAccount::findOrFail($id);
        $data = $this->validateAccount($r);
        $a->fill($data)->save();

        return response()->json([
            'ok'      => true,
            'message' => 'บันทึกเรียบร้อย',
            'account' => $a,
        ]);
    }

    public function accountDestroy($id)
    {
        SolarAccount::findOrFail($id)->delete();
        return response()->json(['ok' => true, 'message' => 'ลบเรียบร้อย']);
    }

    /* =========================================================
     |  VALIDATION HELPERS
     |========================================================= */
    private function validateCustomer(Request $r, bool $isUpdate = false): array
    {
        $rules = [
            'name'         => ($isUpdate ? 'sometimes|' : '') . 'required|string|max:255',
            'desc'         => 'nullable|string',
            'contact_name' => 'nullable|string|max:255',
            'phone'        => 'nullable|string|max:255',
            'size'         => 'nullable|string|max:50',
            'price'        => 'nullable|numeric',
            'loc'          => 'nullable|string|max:255',
            'status'       => 'nullable|string|max:50',
            'supervisor'   => 'nullable|string|max:100',
            'notes'        => 'nullable|string',
            'wash_cycle'   => 'nullable|integer|in:6,12',
            'date'         => 'nullable|date',
        ];
        return $r->validate($rules);
    }

    private function validateAccount(Request $r): array
    {
        return $r->validate([
            'no'           => 'nullable|string|max:20',
            'plane'        => 'nullable|string|max:255',
            'username'     => 'nullable|string|max:255',
            'password'     => 'nullable|string|max:255',
            'email'        => 'nullable|string|max:255',
            'app_password' => 'nullable|string|max:255',
            'customer'     => 'nullable|string|max:255',
            'inverter'     => 'nullable|string|max:100',
        ]);
    }
}