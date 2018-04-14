<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Inspire::class,
        Commands\RHInstallCommand::class,
        Commands\RHClearCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
         $schedule->command('inspire')
                  ->hourly();

        //每天12点备份并清理一次
        $schedule->command('backup:clean')->dailyAt('12:00');
        $schedule->command('backup:run --only-db')->dailyAt('12:00');
    }
}
