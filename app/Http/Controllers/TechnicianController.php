<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Technician;
use App\Models\Schedule;

class TechnicianController extends Controller
{
    // ── Constants ────────────────────────────────────────────────────────────
    const COMPANY_LIST = [
        ['code' => '3E',   'label' => 'Triple E Trading'],
        ['code' => '3IN',  'label' => 'Triple E Innovation'],
        ['code' => '3EM',  'label' => 'Triple E Empire Group'],
        ['code' => '3EL',  'label' => 'Triple E Lighting'],
        ['code' => 'HD',   'label' => 'Hikari Denki'],
        ['code' => 'EP',   'label' => 'Eita & Paul'],
        ['code' => '3P',   'label' => 'Triple P Factory & Eng'],
        ['code' => 'AE&T', 'label' => 'AE&T International'],
    ];

    const SKILL_OPTIONS = [
        'ไฟฟ้า', 'โยธา', 'ประปา', 'แอร์', 'เครื่องจักร', 'อิเล็กทรอนิกส์',
        'เชื่อมโลหะ', 'ระบบแสงสว่าง', 'Solar', 'ระบบ Automation', 'IT/Network',
        'ซ่อมบำรุง',
    ];

    const COMPETENCY_LIST = [
        ['key' => 'ELE', 'label' => 'Electrical'],
        ['key' => 'AUT', 'label' => 'Automation'],
        ['key' => 'PRG', 'label' => 'Programming'],
        ['key' => 'MEC', 'label' => 'Mechanical'],
        ['key' => 'SOL', 'label' => 'Solar'],
        ['key' => 'FIR', 'label' => 'Fire Safety'],
        ['key' => 'SAF', 'label' => 'Safety'],
        ['key' => 'LDR', 'label' => 'Leadership'],
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

    // Storage paths: storage/app/public/technician/{folder}
    const PROFILE_FOLDER = 'technician/profile';
    const LICENSE_FOLDER = 'technician/licenses';

    // ── Index ────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $companyFilter = $request->query('company');
        $teamFilter    = $request->query('team');
        $search        = $request->query('search', '');

        $techQuery = Technician::query()->where('status', '!=', 'leave');
        if ($companyFilter) $techQuery->where('emp_company', $companyFilter);
        if ($teamFilter)    $techQuery->where('emp_team', $teamFilter);
        if ($search) {
            $techQuery->where(function ($q) use ($search) {
                $q->where('emp_name',     'like', "%{$search}%")
                  ->orWhere('emp_name_eng','like', "%{$search}%")
                  ->orWhere('emp_position','like', "%{$search}%")
                  ->orWhere('emp_skill',   'like', "%{$search}%");
            });
        }

        $technicians = $techQuery
            ->orderBy('emp_company')
            ->orderBy('emp_team')
            ->orderByRaw("FIELD(emp_position,'หัวหน้าทีม') DESC")
            ->orderBy('emp_name')
            ->get();

        $schedQuery = Schedule::query();
        if ($teamFilter) $schedQuery->where('team_name', $teamFilter);
        $schedules = $schedQuery->orderBy('start_date', 'desc')->get();

        $companies = Technician::where('status', '!=', 'leave')
            ->select('emp_company')->distinct()
            ->orderBy('emp_company')->pluck('emp_company');

        $teams = Technician::where('emp_position', 'หัวหน้าทีม')
            ->where('status', '!=', 'leave')
            ->whereNotNull('emp_team')
            ->when($companyFilter, fn ($q) => $q->where('emp_company', $companyFilter))
            ->orderBy('emp_company')->orderBy('emp_team')
            ->get()
            ->map(fn ($h) => [
                'team_name'    => $h->emp_team,
                'company'      => $h->emp_company,
                'head_id'      => $h->emp_id,
                'head_name'    => $h->emp_name,
                'head_photo'   => $h->img,
                'member_count' => Technician::where('emp_team', $h->emp_team)
                    ->where('status', '!=', 'leave')->count(),
            ]);

        $availableTeams = Technician::where('emp_position', 'หัวหน้าทีม')
            ->where('status', '!=', 'leave')
            ->whereNotNull('emp_team')
            ->orderBy('emp_team')
            ->pluck('emp_team')->unique()->values();

        $stats = [
            'total_tech'   => Technician::where('status', '!=', 'leave')->count(),
            'total_heads'  => Technician::where('emp_position', 'หัวหน้าทีม')
                                ->where('status', '!=', 'leave')->count(),
            'total_teams'  => $teams->count(),
            'active_jobs'  => Schedule::whereIn('status', ['in_progress'])->count(),
            'pending_jobs' => Schedule::where('status', 'pending')->count(),
            'done_jobs'    => Schedule::where('status', 'done')->count(),
        ];

        return view('project.dashboardtechnician', compact(
            'technicians', 'schedules', 'companies', 'teams',
            'availableTeams', 'stats', 'companyFilter', 'teamFilter', 'search'
        ) + [
            'companyList'      => self::COMPANY_LIST,
            'skillOptions'     => self::SKILL_OPTIONS,
            'competencyList'   => self::COMPETENCY_LIST,
            'competencyLevels' => self::COMPETENCY_LEVELS,
            'softwareOptions'  => self::SOFTWARE_OPTIONS,
        ]);
    }

    // ── Store Technician ─────────────────────────────────────────────────────
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

        $teamCheck = $this->validateTeamRules($data, null);
        if ($teamCheck !== true) return $teamCheck;

        Technician::create($data);
        return redirect()->route('technician.dashboard')->with('success', 'เพิ่มช่างเรียบร้อย');
    }

    // ── Update Technician ────────────────────────────────────────────────────
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
            $teamCheck = $this->validateTeamRules($data, $tech);
            if ($teamCheck !== true) return $teamCheck;
        }

        $tech->update($data);
        return redirect()->route('technician.dashboard')->with('success', 'แก้ไขช่างเรียบร้อย');
    }

    // ── Destroy Technician ───────────────────────────────────────────────────
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

    // ── Schedule CRUD ───────────────────────────────────────────────────────
    public function storeSchedule(Request $request)
    {
        $data = $request->validate([
            'so_number'     => 'required|string|max:100',
            'customer_name' => 'required|string',
            'job_title'     => 'required|string',
            'job_location'  => 'nullable|string',
            'team_name'     => 'required|string',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'status'        => 'nullable|in:pending,in_progress,done,cancelled',
            'note'          => 'nullable|string',
        ]);

        if (!Technician::where('emp_team', $data['team_name'])
            ->where('emp_position', 'หัวหน้าทีม')
            ->where('status', '!=', 'leave')
            ->exists()) {
            return back()->withErrors(['team_name' => "ทีม \"{$data['team_name']}\" ยังไม่มีหัวหน้า"])->withInput();
        }

        Schedule::create($data);
        return redirect()->route('technician.dashboard')->with('success', 'เพิ่มงานเรียบร้อย');
    }

    public function updateSchedule(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);
        $data = $request->validate([
            'so_number'     => 'required|string|max:100',
            'customer_name' => 'required|string',
            'job_title'     => 'required|string',
            'job_location'  => 'nullable|string',
            'team_name'     => 'required|string',
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'status'        => 'nullable|in:pending,in_progress,done,cancelled',
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
    //   PRIVATE HELPERS
    // ════════════════════════════════════════════════════════════════════════

    /**
     * Validation — ทุก field nullable ยกเว้น emp_id ตอน create
     */
    private function validateTechnician(Request $request, bool $isCreate): array
    {
        $rules = [
            'emp_name'     => 'nullable|string|max:150',
            'emp_name_eng' => 'nullable|string|max:150',
            'emp_phone'    => 'nullable|string|max:100',
            'date_of_birth'=> 'nullable|string',
            'img'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'status'       => 'nullable|in:active,leave',
            'emp_company'  => 'nullable|string|max:50',
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

        $messages = [
            'emp_id.required' => 'กรุณากรอกรหัสพนักงาน',
            'emp_id.regex'    => 'รหัสพนักงานใช้ได้เฉพาะ ตัวอักษร, ตัวเลข, - และ _',
            'emp_id.unique'   => 'รหัสพนักงานนี้มีอยู่แล้ว',
        ];

        return $request->validate($rules, $messages);
    }

    /**
     * รูปโปรไฟล์: storage/app/public/technician/profile/{emp_id}_profile.{ext}
     */
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
        foreach (['jpg','jpeg','png','webp'] as $ext) {
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

    /**
     * รองรับทั้ง date picker (CE: YYYY-MM-DD) และ พ.ศ. string
     */
    private function parseDateInput(?string $input): ?string
    {
        if (empty($input)) return null;
        $input = trim($input);

        // YYYY-MM-DD — date picker ส่งมาเป็น CE อยู่แล้ว
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $input, $m)) {
            $year = (int)$m[1];
            if ($year > 2400) $year -= 543; // เผื่อเป็น พ.ศ.
            return sprintf('%04d-%02d-%02d', $year, $m[2], $m[3]);
        }
        // DD/MM/YYYY (assume BE if > 2400)
        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $input, $m)) {
            $year = (int)$m[3];
            if ($year > 2400) $year -= 543;
            return sprintf('%04d-%02d-%02d', $year, (int)$m[2], (int)$m[1]);
        }
        return null;
    }

    /**
     * Licenses: storage/app/public/technician/licenses/{emp_id}_lic_{N}.{ext}
     */
    private function processLicenses(Request $request, ?Technician $existing, string $empId): array
    {
        $input = $request->input('licenses', []);
        if (!is_array($input)) return [];

        $safeId = $this->sanitizeId($empId);

        $existingLicenses = $existing ? ($existing->licenses ?? []) : [];
        $existingFilesMap = [];
        foreach ($existingLicenses as $lic) {
            if (!empty($lic['file'])) $existingFilesMap[$lic['file']] = true;
        }

        $result    = [];
        $usedFiles = [];
        $nextNum   = 1;

        foreach ($input as $idx => $lic) {
            $title        = trim($lic['title']         ?? '');
            $docNo        = trim($lic['doc_no']        ?? '');
            $dateIssued   = trim($lic['date_issued']   ?? '');
            $existingFile = trim($lic['existing_file'] ?? '');

            if ($title === '' && $docNo === '' && $dateIssued === '' && $existingFile === ''
                && !$request->hasFile("licenses.{$idx}.file_upload")) {
                continue;
            }

            $filePath = $existingFile ?: null;
            $candidate = null;

            if ($request->hasFile("licenses.{$idx}.file_upload")) {
                $uploadedFile = $request->file("licenses.{$idx}.file_upload");
                $ext = strtolower($uploadedFile->getClientOriginalExtension() ?: 'pdf');

                while (true) {
                    $filename  = "{$safeId}_lic_{$nextNum}.{$ext}";
                    $candidate = self::LICENSE_FOLDER . '/' . $filename;
                    $isExistingOfThisTech = isset($existingFilesMap[$candidate]);
                    $existsOnDisk = Storage::disk('public')->exists($candidate);
                    if (!$existsOnDisk || $isExistingOfThisTech) break;
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

            $result[] = [
                'title'       => $title,
                'doc_no'      => $docNo,
                'date_issued' => $dateIssued,
                'file'        => $filePath,
            ];
        }

        foreach ($existingFilesMap as $path => $_) {
            if (!isset($usedFiles[$path])) {
                Storage::disk('public')->delete($path);
            }
        }

        return $result;
    }

    private function processCompetencies(Request $request): array
    {
        $input = $request->input('core_competencies', []);
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
                return back()->withErrors(['emp_team' => "ทีม \"{$data['emp_team']}\" ยังไม่มีหัวหน้า - ต้องตั้งหัวหน้าก่อน"])->withInput();
            }
        }
        return true;
    }
}