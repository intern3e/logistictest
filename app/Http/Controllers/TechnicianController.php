<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use App\Models\Technician;
use App\Models\Schedule;
use App\Models\project_cust;
use App\Models\SolarAccount;

class TechnicianController extends Controller
{
    // ── Constants ──────────────────────────────────────────────────────────
    const SKILL_OPTIONS = [
        'ไฟฟ้า', 'โยธา', 'ประปา', 'แอร์', 'เครื่องจักร', 'อิเล็กทรอนิกส์',
        'เชื่อมโลหะ', 'ระบบแสงสว่าง', 'Solar', 'ระบบ Automation', 'IT/Network',
        'ซ่อมบำรุง', 'Team ซัพพอร์ท', 'Project Manager', 'Installation',
        'Inspector', 'Specialist', 'Sale Team',
    ];

    const COMPETENCY_LIST = [
        ['key' => 'Electrical',  'label' => 'Electrical'],
        ['key' => 'Automation',  'label' => 'Automation'],
        ['key' => 'Programming', 'label' => 'Programming'],
        ['key' => 'Mechanical',  'label' => 'Mechanical'],
        ['key' => 'Solar',       'label' => 'Solar'],
        ['key' => 'Fire Safety', 'label' => 'Fire Safety'],
        ['key' => 'Safety',      'label' => 'Safety'],
        ['key' => 'Leadership',  'label' => 'Leadership'],
    ];

    const COMPETENCY_LEVELS = [
        'none'   => 'ไม่มี',
        'basic'  => 'พื้นฐาน',
        'skill'  => 'ชำนาญ',
        'expert' => 'เชี่ยวชาญ',
    ];

    const SOFTWARE_OPTIONS = [
        'AutoCAD', 'AutoCAD Elec.', 'SolidWorks', 'SketchUp',
        'Revit', 'DIALux', 'ETAP', 'TIA Portal',
        'Mitsubishi PLC', 'Rockwell/AB', 'SCADA/HMI', 'PVsyst',
        'MS Office', 'Python', 'Java',
    ];

    const JOB_TYPES = [
        'solar_install'     => '🔆 ติดตั้ง Solar',
        'solar_wash'        => '🧹 ล้างแผง Solar',
        'solar_maintenance' => '🔧 ซ่อมบำรุง Solar',
        'electrical'        => '⚡ งานไฟฟ้า',
        'civil'             => '🏗 งานโยธา',
        'general'           => '📋 งานทั่วไป',
    ];

    const PROFILE_FOLDER = 'technician/profile';
    const LICENSE_FOLDER = 'technician/licenses';

    // ════════════════════════════════════════════════════════════════════════
    //   INDEX — หน้าหลักรวมทุกอย่าง
    // ════════════════════════════════════════════════════════════════════════
    public function index(Request $request)
    {
        $teamFilter = $request->query('team');
        $search     = $request->query('search', '');

        // Technicians
        $techQuery = Technician::query()->where('status', '!=', 'leave');
        if ($teamFilter) $techQuery->where('emp_team', $teamFilter);
        if ($search) {
            $techQuery->where(function ($q) use ($search) {
                $q->where('emp_name',      'like', "%{$search}%")
                  ->orWhere('emp_name_eng', 'like', "%{$search}%")
                  ->orWhere('emp_nickname', 'like', "%{$search}%")
                  ->orWhere('emp_position', 'like', "%{$search}%")
                  ->orWhere('emp_skill',    'like', "%{$search}%");
            });
        }
        $technicians = $techQuery
            ->orderBy('emp_team')
            ->orderByRaw("FIELD(emp_position,'หัวหน้าทีม') DESC")
            ->orderBy('emp_name')
            ->get();

        // Schedules
        $schedQuery = Schedule::query();
        if ($teamFilter) $schedQuery->where('team_name', $teamFilter);
        $schedules = $schedQuery->orderBy('start_date', 'desc')->get();

        // Teams
        $teams = Technician::where('emp_position', 'หัวหน้าทีม')
            ->where('status', '!=', 'leave')
            ->whereNotNull('emp_team')
            ->orderBy('emp_team')
            ->get()
            ->map(fn($h) => [
                'team_name'     => $h->emp_team,
                'head_id'       => $h->emp_id,
                'head_name'     => $h->emp_name,
                'head_nickname' => $h->emp_nickname,
                'head_photo'    => $h->img,
                'member_count'  => Technician::where('emp_team', $h->emp_team)
                    ->where('status', '!=', 'leave')->count(),
            ]);

        $availableTeams = Technician::where('emp_position', 'หัวหน้าทีม')
            ->where('status', '!=', 'leave')
            ->whereNotNull('emp_team')
            ->orderBy('emp_team')
            ->pluck('emp_team')->unique()->values();

        // Solar customers & accounts
        $customers = project_cust::orderBy('id')->get();
        $accounts  = SolarAccount::orderBy('id')->get();

        // Solar stats (ไม่รวมสำเร็จ)
        $visible       = $customers->where('status', '!=', 'ติดตั้งสำเร็จ');
        $kwSum         = $visible->sum(fn($c) => $c->kwNumber());
        $cntQuote      = $visible->where('status', 'เสนอ')->count();
        $cntClosed     = $visible->where('status', 'ปิดการขาย')->count();
        $cntInstalling = $visible->where('status', 'กำลังติดตั้ง')->count();
        $installedTotal= $customers->where('status', 'ติดตั้งสำเร็จ')->count();

        $stats = [
            'total_tech'      => Technician::where('status', '!=', 'leave')->count(),
            'total_heads'     => Technician::where('emp_position', 'หัวหน้าทีม')
                                    ->where('status', '!=', 'leave')->count(),
            'total_teams'     => $teams->count(),
            'total_cust'      => $customers->count(),
            'kw_sum'          => $kwSum,
            'cnt_quote'       => $cntQuote,
            'cnt_closed'      => $cntClosed,
            'cnt_installing'  => $cntInstalling,
            'installed_total' => $installedTotal,
        ];

        return view('project.dashboardtechnician', compact(
            'technicians', 'schedules', 'teams', 'availableTeams',
            'stats', 'teamFilter', 'search',
            'customers', 'accounts'
        ) + [
            'skillOptions'     => self::SKILL_OPTIONS,
            'competencyList'   => self::COMPETENCY_LIST,
            'competencyLevels' => self::COMPETENCY_LEVELS,
            'softwareOptions'  => self::SOFTWARE_OPTIONS,
            'jobTypes'         => self::JOB_TYPES,
        ]);
    }

    // ════════════════════════════════════════════════════════════════════════
    //   TECHNICIAN CRUD
    // ════════════════════════════════════════════════════════════════════════
    public function storeTechnician(Request $request)
    {
        $data = $this->validateTechnician($request, true);
        $data['status'] = 'active';

        if ($request->hasFile('img')) {
            $data['img'] = $this->storeProfileImage($request->file('img'), $data['emp_id']);
        } else {
            unset($data['img']);
        }

        $data['emp_skill']         = implode(',', $request->input('emp_skill', []));
        $data['date_of_birth']     = $this->parseDateInput($request->input('date_of_birth'));
        $data['licenses']          = $this->processLicenses($request, null, $data['emp_id']);
        $data['core_competencies'] = $this->processCompetencies($request);
        $data['software_tools']    = $this->processSoftwareTools($request);

        $check = $this->validateTeamRules($data, null);
        if ($check !== true) return $check;

        Technician::create($data);
        return redirect()->route('technician.dashboard')->with('success', 'เพิ่มช่างเรียบร้อย');
    }

    public function updateTechnician(Request $request, $empId)
    {
        $tech = Technician::where('emp_id', $empId)->firstOrFail();
        $data = $this->validateTechnician($request, false);

        if ($request->hasFile('img')) {
            if ($tech->img) Storage::disk('public')->delete($tech->img);
            $data['img'] = $this->storeProfileImage($request->file('img'), $tech->emp_id);
        } else {
            unset($data['img']);
        }

        $data['emp_skill']         = implode(',', $request->input('emp_skill', []));
        $data['date_of_birth']     = $this->parseDateInput($request->input('date_of_birth'));
        $data['licenses']          = $this->processLicenses($request, $tech, $tech->emp_id);
        $data['core_competencies'] = $this->processCompetencies($request);
        $data['software_tools']    = $this->processSoftwareTools($request);

        if (!empty($data['emp_position']) && $data['emp_position'] === 'หัวหน้าทีม' && $tech->emp_position !== 'หัวหน้าทีม') {
            $check = $this->validateTeamRules($data, $tech);
            if ($check !== true) return $check;
        }

        $tech->update($data);
        return redirect()->route('technician.dashboard')->with('success', 'แก้ไขช่างเรียบร้อย');
    }

    public function destroyTechnician($empId)
    {
        $tech = Technician::where('emp_id', $empId)->firstOrFail();

        if ($tech->emp_position === 'หัวหน้าทีม' && $tech->emp_team) {
            $count = Technician::where('emp_team', $tech->emp_team)
                ->where('status', '!=', 'leave')
                ->where('emp_id', '!=', $tech->emp_id)->count();
            if ($count > 0) {
                return back()->withErrors(['delete' => "ไม่สามารถลบหัวหน้าทีมได้ - ยังมีสมาชิก {$count} คนในทีม"]);
            }
        }

        if ($tech->img) Storage::disk('public')->delete($tech->img);
        foreach (($tech->licenses ?? []) as $lic) {
            if (!empty($lic['file'])) Storage::disk('public')->delete($lic['file']);
        }

        $tech->delete();
        return redirect()->route('technician.dashboard')->with('success', 'ลบช่างเรียบร้อย');
    }

    // ════════════════════════════════════════════════════════════════════════
    //   SCHEDULE CRUD (รวม sync project_cust)
    // ════════════════════════════════════════════════════════════════════════
    public function storeSchedule(Request $request)
    {
        $data = $request->validate([
            'so_number'          => 'required|string|max:100',
            'customer_id'        => 'nullable|integer',
            'customer_name'      => 'required|string',
            'job_type'           => 'nullable|string|max:50',
            'job_title'          => 'required|string',
            'job_location'       => 'nullable|string',
            'job_la_long'        => 'nullable|string',
            'team_name'          => 'required|string',
            'start_date'         => 'required|date',
            'end_date'           => 'required|date|after_or_equal:start_date',
            'note'               => 'nullable|string',
            'cust_desc'          => 'nullable|string',
            'cust_contact_name'  => 'nullable|string',
            'cust_phone'         => 'nullable|string',
            'cust_size'          => 'nullable|string',
            'cust_price'         => 'nullable|numeric',
        ]);

        if (!Technician::where('emp_team', $data['team_name'])
            ->where('emp_position', 'หัวหน้าทีม')
            ->where('status', '!=', 'leave')
            ->exists()) {
            return back()->withErrors(['team_name' => "ทีม \"{$data['team_name']}\" ยังไม่มีหัวหน้า"])->withInput();
        }

        // Sync project_cust
        $customer = $this->syncCustomer($data);

        Schedule::create([
            'so_number'     => $data['so_number'],
            'customer_name' => $data['customer_name'],
            'job_type'      => $data['job_type'] ?? 'general',
            'job_title'     => $data['job_title'],
            'job_location'  => $data['job_location'] ?? null,
            'job_la_long'   => $data['job_la_long']  ?? null,
            'team_name'     => $data['team_name'],
            'start_date'    => $data['start_date'],
            'end_date'      => $data['end_date'],
            'note'          => $data['note'] ?? null,
        ]);

        // Auto wash log ถ้างานล้างแผงและวันเริ่มผ่านแล้ว
        if (($data['job_type'] ?? '') === 'solar_wash' && $customer) {
            if ($data['start_date'] <= Carbon::today()->toDateString()) {
                $customer->addWashLog($data['start_date'], $data['team_name'], $data['note'] ?? '');
            }
        }

        return redirect()->route('technician.dashboard')->with('success', 'เพิ่มงานเรียบร้อย');
    }

    public function updateSchedule(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);
        $data = $request->validate([
            'so_number'     => 'required|string|max:100',
            'customer_name' => 'required|string',
            'job_type'      => 'nullable|string|max:50',
            'job_title'     => 'required|string',
            'job_location'  => 'nullable|string',
            'job_la_long'   => 'nullable|string',
            'team_name'     => 'required|string',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'note'          => 'nullable|string',
        ]);
        $schedule->update($data);
        return redirect()->route('technician.dashboard')->with('success', 'แก้ไขงานเรียบร้อย');
    }

    public function destroySchedule($id)
    {
        Schedule::findOrFail($id)->delete();
        return redirect()->route('technician.dashboard')->with('success', 'ลบงานเรียบร้อย');
    }

    // ════════════════════════════════════════════════════════════════════════
    //   PROJECT_CUST (CUSTOMER) CRUD
    // ════════════════════════════════════════════════════════════════════════
    public function customerStore(Request $r)
    {
        $data = $this->validateCustomer($r);
        $data['status']     = $data['status']     ?? 'เสนอ';
        $data['wash_cycle'] = $data['wash_cycle'] ?? 6;
        $data['date']       = Carbon::today()->toDateString();
        $data['wash_logs']  = [];

        project_cust::create($data);
        return redirect()->route('technician.dashboard')->with('success', 'เพิ่มลูกค้าเรียบร้อย');
    }

    public function customerUpdate(Request $r, $id)
    {
        $c = project_cust::findOrFail($id);
        if ($c->is_extra) {
            return back()->withErrors(['delete' => 'ไม่สามารถแก้ไขระบบเก่าได้']);
        }
        $data = $this->validateCustomer($r, true);

        // recalc wash_next ถ้าเปลี่ยน wash_cycle
        if ($r->has('wash_cycle') && $c->wash_current) {
            $data['wash_next'] = Carbon::parse($c->wash_current)
                ->addMonths($data['wash_cycle'] ?: 6)
                ->toDateString();
        }

        $c->update($data);
        return redirect()->route('technician.dashboard')->with('success', 'แก้ไขลูกค้าเรียบร้อย');
    }

    public function customerDestroy($id)
    {
        $c = project_cust::findOrFail($id);
        if ($c->is_extra) {
            return back()->withErrors(['delete' => 'ไม่สามารถลบระบบเก่าได้']);
        }
        $c->delete();
        return redirect()->route('technician.dashboard')->with('success', 'ลบลูกค้าเรียบร้อย');
    }

    // ── WASH LOGS ────────────────────────────────────────────────────────
    public function washStore(Request $r, $id)
    {
        $data = $r->validate([
            'wash_date' => 'required|date',
            'tech'      => 'required|string|max:100',
            'note'      => 'nullable|string',
        ]);
        $c = project_cust::findOrFail($id);
        $c->addWashLog($data['wash_date'], $data['tech'], $data['note'] ?? '');
        return redirect()->route('technician.dashboard')->with('success', 'บันทึกการล้างแผงเรียบร้อย');
    }

    public function washDestroy($id, $num)
    {
        $c = project_cust::findOrFail($id);
        $exists = collect($c->washLogsArr())->contains(fn($w) => (int)($w['num'] ?? 0) === (int)$num);
        if (!$exists) {
            return back()->withErrors(['delete' => 'ไม่พบรายการ']);
        }
        $c->removeWashLog((int)$num);
        return redirect()->route('technician.dashboard')->with('success', 'ลบประวัติการล้างเรียบร้อย');
    }

    // ════════════════════════════════════════════════════════════════════════
    //   SOLAR ACCOUNTS CRUD
    // ════════════════════════════════════════════════════════════════════════
    public function accountStore(Request $r)
    {
        $data = $this->validateAccount($r);
        SolarAccount::create($data);
        return redirect()->route('technician.dashboard')->with('success', 'เพิ่มบัญชีเรียบร้อย');
    }

    public function accountUpdate(Request $r, $id)
    {
        $a = SolarAccount::findOrFail($id);
        $a->fill($this->validateAccount($r))->save();
        return redirect()->route('technician.dashboard')->with('success', 'บันทึกเรียบร้อย');
    }

    public function accountDestroy($id)
    {
        SolarAccount::findOrFail($id)->delete();
        return redirect()->route('technician.dashboard')->with('success', 'ลบเรียบร้อย');
    }

    // ════════════════════════════════════════════════════════════════════════
    //   PRIVATE HELPERS
    // ════════════════════════════════════════════════════════════════════════

    /**
     * Sync ลูกค้าเข้า project_cust ตอนสร้าง schedule
     * - มี customer_id → ใช้เดิม (update เฉพาะฟิลด์ที่กรอก)
     * - ไม่มี → ค้นชื่อ → ถ้าไม่เจอ สร้างใหม่
     */
    private function syncCustomer(array $data): ?project_cust
    {
        $customer = null;

        if (!empty($data['customer_id'])) {
            $customer = project_cust::find((int)$data['customer_id']);
        }

        if (!$customer) {
            $customer = project_cust::where('name', trim($data['customer_name']))
                ->orderByDesc('id')->first();
        }

        if (!$customer) {
            $customer = project_cust::create([
                'date'         => Carbon::today()->toDateString(),
                'name'         => trim($data['customer_name']),
                'desc'         => $data['cust_desc']         ?? null,
                'contact_name' => $data['cust_contact_name'] ?? null,
                'phone'        => $data['cust_phone']        ?? null,
                'size'         => $data['cust_size']         ?? null,
                'price'        => $data['cust_price']        ?? null,
                'loc'          => $data['job_la_long']       ?? null,
                'type_project' => $data['job_type']          ?? null,
                'status'       => 'เสนอ',
                'wash_cycle'   => 6,
                'wash_logs'    => [],
                'is_extra'     => false,
                'notes'        => $data['note']              ?? null,
            ]);
        } else {
            $up = [];
            if (!empty($data['cust_desc']))         $up['desc']         = $data['cust_desc'];
            if (!empty($data['cust_contact_name'])) $up['contact_name'] = $data['cust_contact_name'];
            if (!empty($data['cust_phone']))         $up['phone']        = $data['cust_phone'];
            if (!empty($data['cust_size']))          $up['size']         = $data['cust_size'];
            if (!empty($data['cust_price']))         $up['price']        = $data['cust_price'];
            if (!empty($data['job_la_long']) && empty($customer->loc)) {
                $up['loc'] = $data['job_la_long'];
            }
            if ($up) $customer->update($up);
        }

        return $customer;
    }

    private function validateCustomer(Request $r, bool $isUpdate = false): array
    {
        return $r->validate([
            'name'         => ($isUpdate ? 'sometimes|' : '') . 'required|string|max:255',
            'desc'         => 'nullable|string',
            'contact_name' => 'nullable|string|max:255',
            'phone'        => 'nullable|string|max:255',
            'size'         => 'nullable|string|max:50',
            'price'        => 'nullable|numeric',
            'loc'          => 'nullable|string|max:255',
            'type_project' => 'nullable|string|max:50',
            'status'       => 'nullable|string|max:50',
            'supervisor'   => 'nullable|string|max:100',
            'notes'        => 'nullable|string',
            'wash_cycle'   => 'nullable|integer|in:6,12',
            'date'         => 'nullable|date',
        ]);
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

    private function validateTechnician(Request $request, bool $isCreate): array
    {
        $rules = [
            'emp_name'     => 'nullable|string|max:150',
            'emp_name_eng' => 'nullable|string|max:150',
            'emp_nickname' => 'nullable|string|max:100',
            'emp_phone'    => 'nullable|string|max:100',
            'date_of_birth'=> 'nullable|string',
            'img'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'status'       => 'nullable|in:active,leave',
            'emp_position' => 'nullable|in:ลูกทีม,หัวหน้าทีม',
            'emp_team'     => 'nullable|string|max:255',
            'emp_skill'    => 'nullable|array',
            'emp_skill.*'  => 'string|max:100',
            'licenses'                 => 'nullable|array',
            'licenses.*.title'         => 'nullable|string|max:255',
            'licenses.*.doc_no'        => 'nullable|string|max:150',
            'licenses.*.date_issued'   => 'nullable|string|max:20',
            'licenses.*.file_upload'   => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
            'licenses.*.existing_file' => 'nullable|string',
            'core_competencies'   => 'nullable|array',
            'core_competencies.*' => 'nullable|in:none,basic,skill,expert',
            'software_tools'   => 'nullable|array',
            'software_tools.*' => 'string|max:150',
        ];

        if ($isCreate) {
            $rules['emp_id'] = 'required|string|max:20|unique:technicians,emp_id|regex:/^[A-Za-z0-9_\-]+$/';
        }

        return $request->validate($rules, [
            'emp_id.required' => 'กรุณากรอกรหัสพนักงาน',
            'emp_id.regex'    => 'รหัสพนักงานใช้ได้เฉพาะ ตัวอักษร, ตัวเลข, - และ _',
            'emp_id.unique'   => 'รหัสพนักงานนี้มีอยู่แล้ว',
        ]);
    }

    private function storeProfileImage($file, string $empId): string
    {
        $safeId   = $this->sanitizeId($empId);
        $ext      = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $filename = "{$safeId}_profile.{$ext}";
        $path     = self::PROFILE_FOLDER . '/' . $filename;
        $this->cleanupProfileVariants($safeId);
        $file->storeAs(self::PROFILE_FOLDER, $filename, 'public');
        return $path;
    }

    private function cleanupProfileVariants(string $safeId): void
    {
        foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
            $p = self::PROFILE_FOLDER . "/{$safeId}_profile.{$ext}";
            if (Storage::disk('public')->exists($p)) {
                Storage::disk('public')->delete($p);
            }
        }
    }

    private function sanitizeId(string $empId): string
    {
        return preg_replace('/[^A-Za-z0-9_\-]/', '_', $empId);
    }

    private function parseDateInput(?string $input): ?string
    {
        if (empty($input)) return null;
        $input = trim($input);
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $input, $m)) {
            $year = (int)$m[1];
            if ($year > 2400) $year -= 543;
            return sprintf('%04d-%02d-%02d', $year, $m[2], $m[3]);
        }
        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $input, $m)) {
            $year = (int)$m[3];
            if ($year > 2400) $year -= 543;
            return sprintf('%04d-%02d-%02d', $year, (int)$m[2], (int)$m[1]);
        }
        return null;
    }

    private function processLicenses(Request $request, ?Technician $existing, string $empId): array
    {
        $input  = $request->input('licenses', []);
        if (!is_array($input)) return [];

        $safeId           = $this->sanitizeId($empId);
        $existingLicenses = $existing ? ($existing->licenses ?? []) : [];
        $existingFilesMap = [];
        foreach ($existingLicenses as $lic) {
            if (!empty($lic['file'])) $existingFilesMap[$lic['file']] = true;
        }

        $result = []; $usedFiles = []; $nextNum = 1;

        foreach ($input as $idx => $lic) {
            $title        = trim($lic['title']         ?? '');
            $docNo        = trim($lic['doc_no']        ?? '');
            $dateIssued   = trim($lic['date_issued']   ?? '');
            $existingFile = trim($lic['existing_file'] ?? '');

            if ($title === '' && $docNo === '' && $dateIssued === '' && $existingFile === ''
                && !$request->hasFile("licenses.{$idx}.file_upload")) {
                continue;
            }

            $filePath  = $existingFile ?: null;
            $candidate = null;

            if ($request->hasFile("licenses.{$idx}.file_upload")) {
                $uploadedFile = $request->file("licenses.{$idx}.file_upload");
                $ext = strtolower($uploadedFile->getClientOriginalExtension() ?: 'pdf');
                while (true) {
                    $filename  = "{$safeId}_lic_{$nextNum}.{$ext}";
                    $candidate = self::LICENSE_FOLDER . '/' . $filename;
                    if (!Storage::disk('public')->exists($candidate) || isset($existingFilesMap[$candidate])) break;
                    $nextNum++;
                }
                if ($filePath && isset($existingFilesMap[$filePath]) && $filePath !== $candidate) {
                    Storage::disk('public')->delete($filePath);
                }
                $uploadedFile->storeAs(self::LICENSE_FOLDER, $filename, 'public');
                $filePath = $candidate;
                $nextNum++;
            }

            if ($filePath) $usedFiles[$filePath] = true;
            $result[] = ['title' => $title, 'doc_no' => $docNo, 'date_issued' => $dateIssued, 'file' => $filePath];
        }

        foreach ($existingFilesMap as $path => $_) {
            if (!isset($usedFiles[$path])) Storage::disk('public')->delete($path);
        }

        return $result;
    }

    private function processCompetencies(Request $request): array
    {
        $input  = $request->input('core_competencies', []);
        $result = [];
        foreach (self::COMPETENCY_LIST as $c) {
            $val = $input[$c['key']] ?? 'none';
            if (!array_key_exists($val, self::COMPETENCY_LEVELS)) $val = 'none';
            $result[$c['key']] = $val;
        }
        return $result;
    }

    private function processSoftwareTools(Request $request): array
    {
        $input = $request->input('software_tools', []);
        if (!is_array($input)) return [];
        $clean = [];
        foreach ($input as $sw) {
            $sw = trim((string)$sw);
            if ($sw !== '' && !in_array($sw, $clean, true)) $clean[] = $sw;
        }
        return $clean;
    }

    private function validateTeamRules(array $data, ?Technician $tech)
    {
        $position = $data['emp_position'] ?? null;

        if ($position === 'หัวหน้าทีม') {
            $teamName = $data['emp_team'] ?? ($tech->emp_team ?? '');
            if (empty($teamName)) {
                return back()->withErrors(['emp_team' => 'หัวหน้าทีมต้องระบุชื่อทีม'])->withInput();
            }
            $exists = Technician::where('emp_team', $teamName)
                ->where('emp_position', 'หัวหน้าทีม')
                ->where('status', '!=', 'leave')
                ->when($tech, fn($q) => $q->where('emp_id', '!=', $tech->emp_id))
                ->exists();
            if ($exists) {
                return back()->withErrors(['emp_team' => "ทีม \"{$teamName}\" มีหัวหน้าอยู่แล้ว"])->withInput();
            }
        } elseif (!empty($data['emp_team'])) {
            if (!Technician::where('emp_team', $data['emp_team'])
                ->where('emp_position', 'หัวหน้าทีม')
                ->where('status', '!=', 'leave')
                ->exists()) {
                return back()->withErrors(['emp_team' => "ทีม \"{$data['emp_team']}\" ยังไม่มีหัวหน้า"])->withInput();
            }
        }

        return true;
    }
}