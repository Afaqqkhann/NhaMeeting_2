<?php

namespace App\Console;

use App\Jobs\AddFinMedExpenseJob;
use App\Services\FinMedExpService;
use DB;
use Log;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    use DispatchesJobs;
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\Inspire::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        Log::info("Task Schedular Running");

        //$schedule->command('update:finMedExp')->dailyAt('16:08');

        $schedule->call(function () {
            (new FinMedExpService)->updateTBLFinMedExp();
        })->dailyAt('10:27');

        //})->name('HRMIS_Fin_Med')->withoutOverlapping()->daily();
        /*$schedule->command('inspire')
        
                 ->hourly();*/
        /* Log::info('Start Script for Dependents Age & Status ');

        /// Update Dependent Age & status
        $schedule->call(function () {
            DB::table('tbl_family')->update(['age' => DB::raw('TRUNC(months_between(sysdate, DATE_OF_BIRTH) / 12)')]);
        })->everyFiveMinutes(); //->dailyAt('00:05');

        //// Update dependent (son) status over 25 years
        $schedule->call(function () {
            DB::table('tbl_family')->where('relationship', '=', '7')->whereNull('affidavit')->where('age', '>', 24)->update(['family_status' => 0]);
        })->everyFiveMinutes(); //->dailyAt('00:10');
        Log::info('End Script for Dependents Age & Status '); */
    }
}
