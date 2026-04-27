<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * =========================
     * SCHEDULE TASKS
     * =========================
     */
    protected function schedule(Schedule $schedule): void
    {
        // QRIS AUTO EXPIRE tiap 1 menit
        $schedule->command('app:expire-qris-payment')
            ->everyMinute()
            ->withoutOverlapping() // ⛔ cegah dobel jalan
            ->runInBackground();   // 🚀 biar tidak blocking
    }

    /**
     * =========================
     * REGISTER COMMANDS
     * =========================
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
