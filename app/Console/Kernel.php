<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\ClearActivity;
use App\Console\Commands\CheckExpiredCoupons;
use App\Console\Commands\CheckExpiredSubscriptions;
use App\Console\Commands\SendDailyNewActivities;
use App\Console\Commands\SendDailyNewMessages;
use App\Console\Commands\SendWeeklyNewActivities;
use App\Console\Commands\SendWeeklyNewMessages;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->command(ClearActivity::class)->dailyAt('01:00');
        $schedule->command(CheckExpiredCoupons::class)->dailyAt('00:01');
        $schedule->command(CheckExpiredSubscriptions::class)->dailyAt('00:01');
        // email notications
        $schedule->command(SendDailyNewActivities::class)->dailyAt('12:00');
        $schedule->command(SendDailyNewMessages::class)->dailyAt('12:00');
        $schedule->command(SendWeeklyNewActivities::class)->weeklyOn(7, '12:00');
        $schedule->command(SendWeeklyNewMessages::class)->weeklyOn(7, '12:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
