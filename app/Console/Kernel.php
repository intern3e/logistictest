<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // ไม่ตั้ง schedule — ใช้ lazy-sync ตอนผู้ใช้เข้าหน้า (รวบเลขบิลในหน้าแล้วเช็ค ERP ทีเดียว)
        // command 'bills:sync-cancelled' ยังเรียกเองได้ถ้าต้องการ sync ย้อนหลังทั้งก้อน
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
