<?php

namespace Photon\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Photon\PhotonCms\Core\Scheduler\PhotonScheduler;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \Photon\Console\Commands\Inspire::class,
        \Photon\PhotonCms\Core\Commands\RebuildResizedImages::class,
        \Photon\PhotonCms\Core\Commands\UpdateAnchorFields::class,
        \Photon\PhotonCms\Core\Commands\Sync::class,
        \Photon\PhotonCms\Core\Commands\HardReset::class,
        \Photon\PhotonCms\Core\Commands\SoftReset::class
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

        // Schedule Photon jobs
        PhotonScheduler::schedule($schedule);
    }
}
