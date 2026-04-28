<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class project_cust extends Model
{
    protected $table = 'project_cust';

    protected $fillable = [
        'date',
        'name',
        'desc',
        'contact_name',
        'phone',
        'size',
        'loc',
        'price',
        // 'type_project' เดิม ยังคงไว้ (backward compat)
        'type_project',
        'status',
        'supervisor',
        'notes',
        // ── Solar-specific ──
        'wash_current',
        'wash_next',
        'wash_cycle',
        'wash_logs',
        // ── General ──
        'is_extra',
        // ── Project tracking ──
        'start_date',
        'finish_date',
        'warranty_end',      // วันหมดประกัน (สำหรับ solar/electrical)
        'contract_no',       // เลขสัญญา
        'progress_pct',      // % ความคืบหน้า (0-100)
        'milestone_logs',    // JSON: [{date, note, by}] — timeline งานทั่วไป
    ];

    protected $casts = [
        'date'          => 'date',
        'wash_current'  => 'date',
        'wash_next'     => 'date',
        'start_date'    => 'date',
        'finish_date'   => 'date',
        'warranty_end'  => 'date',
        'price'         => 'decimal:2',
        'wash_cycle'    => 'integer',
        'progress_pct'  => 'integer',
        'is_extra'      => 'boolean',
        'wash_logs'     => 'array',
        'milestone_logs'=> 'array',
    ];


    public static function statusOptions(string $type = ''): array
    {
        return match(self::categoryOf($type)) {
            'solar'      => [
                'เสนอ'           => 'เสนอ',
                'ปิดการขาย'      => 'ปิดการขาย',
                'กำลังติดตั้ง'   => 'กำลังติดตั้ง',
                'ติดตั้งสำเร็จ'  => 'ติดตั้งสำเร็จ',
            ],
            'electrical' => [
                'เสนอ'               => 'เสนอ',
                'ปิดการขาย'          => 'ปิดการขาย',
                'ดำเนินการ'          => 'ดำเนินการ',
                'ทดสอบ/ตรวจรับ'     => 'ทดสอบ/ตรวจรับ',
                'เสร็จสิ้น'          => 'เสร็จสิ้น',
                'ยกเลิก'             => 'ยกเลิก',
            ],
            'civil'      => [
                'เสนอ'               => 'เสนอ',
                'ปิดการขาย'          => 'ปิดการขาย',
                'ดำเนินการ'          => 'ดำเนินการ',
                'ตรวจรับงาน'         => 'ตรวจรับงาน',
                'เสร็จสิ้น'          => 'เสร็จสิ้น',
                'ยกเลิก'             => 'ยกเลิก',
            ],
            default      => [  
                'เสนอ'      => 'เสนอ',
                'ดำเนินการ' => 'ดำเนินการ',
                'เสร็จสิ้น' => 'เสร็จสิ้น',
                'ยกเลิก'    => 'ยกเลิก',
            ],
        };
    }

    public static function categoryOf(string $type): string
    {
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
        return Carbon::parse($this->wash_next)->diffInDays(now(), false) >= -30;
    }
    public function isWashOverdue(): bool
    {
        if (!$this->isSolarInstalled() || !$this->wash_next) return false;
        return Carbon::parse($this->wash_next)->isPast();
    }
    public function isWarrantyExpired(): bool
    {
        if (!$this->warranty_end) return false;
        return Carbon::parse($this->warranty_end)->isPast();
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
    public function washLogsArr(): array
    {
        return is_array($this->wash_logs) ? $this->wash_logs : [];
    }

    public function addWashLog(string $date, string $tech, string $note = ''): void
    {
        $logs   = $this->washLogsArr();
        $logs[] = ['num' => 0, 'date' => $date, 'tech' => $tech, 'note' => $note];
        $this->normalizeWashLogs($logs);
    }

    public function removeWashLog(int $num): void
    {
        $logs = array_values(array_filter(
            $this->washLogsArr(),
            fn($w) => (int)($w['num'] ?? 0) !== $num
        ));
        $this->normalizeWashLogs($logs);
    }

    protected function normalizeWashLogs(array $logs): void
    {
        usort($logs, fn($a, $b) => strcmp($a['date'] ?? '', $b['date'] ?? ''));
        foreach ($logs as $i => &$w) {
            $w['num']  = $i + 1;
            $w['tech'] = $w['tech'] ?? '';
            $w['note'] = $w['note'] ?? '';
        }
        unset($w);

        $this->wash_logs = $logs;

        if (count($logs) > 0) {
            $latest             = end($logs);
            $this->wash_current = $latest['date'];
            $this->wash_next    = Carbon::parse($latest['date'])
                ->addMonths($this->wash_cycle ?: 6)
                ->toDateString();
        } else {
            // ไม่เคยล้าง → ถ้า finish_date มี ให้คำนวณจาก finish_date
            if ($this->finish_date) {
                $this->wash_current = null;
                $this->wash_next    = Carbon::parse($this->finish_date)
                    ->addMonths($this->wash_cycle ?: 6)
                    ->toDateString();
            } else {
                $this->wash_current = null;
                $this->wash_next    = null;
            }
        }

        $this->save();
    }
    public function milestoneLogsArr(): array
    {
        return is_array($this->milestone_logs) ? $this->milestone_logs : [];
    }

    public function addMilestone(string $date, string $note, string $by = ''): void
    {
        $logs   = $this->milestoneLogsArr();
        $logs[] = ['date' => $date, 'note' => $note, 'by' => $by];
        usort($logs, fn($a, $b) => strcmp($a['date'], $b['date']));
        $this->milestone_logs = $logs;
        $this->save();
    }

    public function removeMilestone(int $index): void
    {
        $logs = $this->milestoneLogsArr();
        array_splice($logs, $index, 1);
        $this->milestone_logs = array_values($logs);
        $this->save();
    }
}