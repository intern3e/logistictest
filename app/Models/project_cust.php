<?php
// ═══════════════════════════════════════════════════════════════════
//  app/Models/project_cust.php
//  ─ ล็อค wash cycle = 12 เดือน (1 ปี) คงที่
//  ─ Auto-bump wash_next เมื่อ schedule ล้างแผงผ่านวันแล้ว
// ═══════════════════════════════════════════════════════════════════
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class project_cust extends Model
{
    protected $table = 'project_cust';

    /**
     * รอบล้างแผงล็อคที่ 12 เดือน (1 ปี) — ไม่ให้แก้
     */
    const WASH_CYCLE_MONTHS = 12;

    protected $fillable = [
        'date',
        'name',
        'desc',
        'contact_name',
        'phone',
        'size',
        'loc',
        'price',
        'type_project',
        'status',
        'supervisor',     // เก็บ finish_date (string)
        'notes',
        'wash_current',
        'wash_next',
        'wash_cycle',     // คงไว้เพื่อ backward compat แต่ไม่ใช้แล้ว (ค่า=12 เสมอ)
        'wash_logs',
        'is_extra',
    ];

    protected $casts = [
        'date'         => 'date',
        'wash_current' => 'date',
        'wash_next'    => 'date',
        'price'        => 'decimal:2',
        'wash_cycle'   => 'integer',
        'is_extra'     => 'boolean',
        'wash_logs'    => 'array',
    ];

    // ══════════════════════════════════════════════════════════════
    //  Category Helpers
    // ══════════════════════════════════════════════════════════════

    public static function categoryOf(?string $type): string
    {
        if (!$type) return 'general';
        if (str_starts_with($type, 'solar')) return 'solar';
        if ($type === 'electrical')          return 'electrical';
        if ($type === 'civil')               return 'civil';
        return 'general';
    }

    public function getCategory(): string
    {
        return self::categoryOf($this->type_project ?? '');
    }

    public function isSolarInstalled(): bool
    {
        return $this->getCategory() === 'solar' && $this->status === 'ติดตั้งสำเร็จ';
    }

    public function isWashDueSoon(): bool
    {
        if (!$this->isSolarInstalled() || !$this->wash_next) return false;
        $days = $this->daysUntilWash();
        return $days !== null && $days >= 0 && $days <= 30;
    }

    public function isWashOverdue(): bool
    {
        if (!$this->isSolarInstalled() || !$this->wash_next) return false;
        return Carbon::parse($this->wash_next)->startOfDay()->isPast();
    }

    public function daysUntilWash(): ?int
    {
        if (!$this->wash_next) return null;
        return (int) now()->startOfDay()->diffInDays(
            Carbon::parse($this->wash_next)->startOfDay(),
            false
        );
    }

    public function kwNumber(): float
    {
        if (!$this->size) return 0;
        if (preg_match('/(\d+\.?\d*)/', $this->size, $m)) return (float)$m[1];
        return 0;
    }

    // ══════════════════════════════════════════════════════════════
    //  Wash Log Helpers
    // ══════════════════════════════════════════════════════════════

    public function washLogsArr(): array
    {
        return is_array($this->wash_logs) ? $this->wash_logs : [];
    }

    /**
     * เพิ่ม wash log ใหม่ + recalc wash_next = log วันล่าสุด + 1 ปี
     */
    public function addWashLog(string $date, string $tech, string $note = ''): void
    {
        $logs = collect($this->washLogsArr())
            ->filter(fn($w) => ($w['type'] ?? 'wash') === 'wash')
            ->values()
            ->all();

        // Idempotent: ถ้ามี log วันเดียวกัน + ทีมเดียวกันอยู่แล้ว ไม่เพิ่มซ้ำ
        $exists = collect($logs)->contains(fn($w) =>
            ($w['date'] ?? '') === $date && ($w['tech'] ?? '') === $tech
        );
        if ($exists) return;

        $logs[] = [
            'type' => 'wash',
            'num'  => 0,
            'date' => $date,
            'tech' => $tech,
            'note' => $note,
        ];

        // เก็บ milestone logs ไว้ด้วย
        $milestones = collect($this->washLogsArr())
            ->filter(fn($w) => ($w['type'] ?? 'wash') === 'milestone')
            ->values()
            ->all();

        $this->normalizeWashLogs($logs, $milestones);
    }

    public function removeWashLog(int $num): void
    {
        $logs = collect($this->washLogsArr())
            ->filter(fn($w) => ($w['type'] ?? 'wash') === 'wash')
            ->reject(fn($w) => (int)($w['num'] ?? 0) === $num)
            ->values()
            ->all();

        $milestones = collect($this->washLogsArr())
            ->filter(fn($w) => ($w['type'] ?? 'wash') === 'milestone')
            ->values()
            ->all();

        $this->normalizeWashLogs($logs, $milestones);
    }

    /**
     * จัด numbering, sort, recalc wash_current + wash_next
     */
    protected function normalizeWashLogs(array $washLogs, array $milestones = []): void
    {
        // sort wash logs ตามวันที่
        usort($washLogs, fn($a, $b) => strcmp($a['date'] ?? '', $b['date'] ?? ''));
        foreach ($washLogs as $i => &$w) {
            $w['type'] = 'wash';
            $w['num']  = $i + 1;
            $w['tech'] = $w['tech'] ?? '';
            $w['note'] = $w['note'] ?? '';
        }
        unset($w);

        // รวม wash + milestone กลับ
        $all = array_merge($washLogs, $milestones);
        $this->wash_logs = $all;

        if (count($washLogs) > 0) {
            $latest             = end($washLogs);
            $this->wash_current = $latest['date'];
            $this->wash_next    = Carbon::parse($latest['date'])
                ->addMonths(self::WASH_CYCLE_MONTHS)
                ->toDateString();
        } else {
            // ยังไม่เคยล้าง → ใช้ supervisor (finish_date) เป็นฐาน
            $this->wash_current = null;
            if ($this->supervisor) {
                $this->wash_next = Carbon::parse($this->supervisor)
                    ->addMonths(self::WASH_CYCLE_MONTHS)
                    ->toDateString();
            } else {
                $this->wash_next = null;
            }
        }

        $this->wash_cycle = self::WASH_CYCLE_MONTHS;
        $this->save();
    }

    // ══════════════════════════════════════════════════════════════
    //  Milestone Log Helpers (non-solar)
    // ══════════════════════════════════════════════════════════════

    public function milestoneLogsArr(): array
    {
        return collect($this->washLogsArr())
            ->filter(fn($w) => ($w['type'] ?? '') === 'milestone')
            ->values()
            ->all();
    }

    public function addMilestone(string $date, string $note, string $by = ''): void
    {
        $all = $this->washLogsArr();
        $all[] = [
            'type' => 'milestone',
            'date' => $date,
            'note' => $note,
            'by'   => $by,
        ];
        // sort milestones ตามวันที่
        $milestones = collect($all)
            ->filter(fn($w) => ($w['type'] ?? '') === 'milestone')
            ->sortBy('date')
            ->values()
            ->all();
        $washLogs = collect($all)
            ->filter(fn($w) => ($w['type'] ?? 'wash') === 'wash')
            ->values()
            ->all();
        $this->wash_logs = array_merge($washLogs, $milestones);
        $this->save();
    }

    public function removeMilestone(int $index): void
    {
        $milestones = $this->milestoneLogsArr();
        if (!isset($milestones[$index])) return;
        array_splice($milestones, $index, 1);

        $washLogs = collect($this->washLogsArr())
            ->filter(fn($w) => ($w['type'] ?? 'wash') === 'wash')
            ->values()
            ->all();

        $this->wash_logs = array_merge($washLogs, $milestones);
        $this->save();
    }

    // ══════════════════════════════════════════════════════════════
    //  AUTO-SYNC จาก Schedule
    // ══════════════════════════════════════════════════════════════

    /**
     * หา wash schedule ที่จะมาถึง (ยังไม่ผ่าน) ของลูกค้านี้
     * Return: Schedule object หรือ null
     */
    public function upcomingWashSchedule(): ?\App\Models\Schedule
    {
        if ($this->getCategory() !== 'solar') return null;

        return \App\Models\Schedule::where('customer_name', $this->name)
            ->where(function ($q) {
                $q->where('job_title', 'like', '%ล้าง%')
                  ->orWhere('note',    'like', '%solar_wash%')
                  ->orWhere('note',    'like', '%ล้าง%');
            })
            ->whereDate('start_date', '>=', now()->toDateString())
            ->orderBy('start_date')
            ->first();
    }

    /**
     * Auto-bump: ถ้ามี wash schedule ที่ผ่านวันแล้ว และยังไม่ถูก log
     * → เพิ่มเข้า wash_logs + recalc wash_next
     */
    public function autoBumpFromSchedules(): bool
    {
        if (!$this->isSolarInstalled()) return false;

        $bumped = false;

        // หา wash schedules ทั้งหมดที่ผ่านวันแล้ว
        $passedScheds = \App\Models\Schedule::where('customer_name', $this->name)
            ->where(function ($q) {
                $q->where('job_title', 'like', '%ล้าง%')
                  ->orWhere('note',    'like', '%solar_wash%')
                  ->orWhere('note',    'like', '%ล้าง%');
            })
            ->whereDate('start_date', '<=', now()->toDateString())
            ->get();

        $existingDates = collect($this->washLogsArr())
            ->filter(fn($w) => ($w['type'] ?? 'wash') === 'wash')
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->toDateString())
            ->all();

        foreach ($passedScheds as $sched) {
            $schedDate = Carbon::parse($sched->start_date)->toDateString();
            if (in_array($schedDate, $existingDates, true)) continue;

            // Push log (ใช้ method addWashLog ซึ่งเรียก normalize+save อัตโนมัติ)
            $this->addWashLog(
                $schedDate,
                $sched->team_name ?: 'ทีม',
                $sched->note ?: 'จากตารางงาน SO ' . ($sched->so_number ?? '')
            );
            $existingDates[] = $schedDate; // กัน duplicate ถ้ามีหลายงานวันเดียว
            $bumped = true;
        }

        return $bumped;
    }

    // ══════════════════════════════════════════════════════════════
    //  STATIC: Auto-sync ทั้งหมด — เรียกใน Controller index()
    // ══════════════════════════════════════════════════════════════
    public static function syncAllWashSchedules(): int
    {
        $count = 0;
        self::where('type_project', 'like', 'solar%')
            ->where('status', 'ติดตั้งสำเร็จ')
            ->get()
            ->each(function ($c) use (&$count) {
                if ($c->autoBumpFromSchedules()) $count++;
            });
        return $count;
    }
}