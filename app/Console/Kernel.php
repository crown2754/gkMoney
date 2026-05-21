<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @var array
     */
    protected $commands = [
        Commands\FetchTodayExchangeRates::class,
        Commands\CreateAssetSnapshots::class,
    ];

    /**
     * Define the command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('exchange:fetch-today-rates')
            ->dailyAt('00:30')
            ->withoutOverlapping();

        $schedule->command('asset:snapshot')
            ->dailyAt('01:00')
            ->withoutOverlapping();
    }
}