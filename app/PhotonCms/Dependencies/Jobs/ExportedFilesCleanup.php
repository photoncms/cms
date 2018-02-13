<?php

namespace Photon\PhotonCms\Dependencies\Jobs;

use Illuminate\Console\Scheduling\Schedule;

class ExportedFilesCleanup
{

    /**
     * This method will be automatically called.
     *
     * Use the following documentation for defining sheduled jobs. Pay attention to use the current Laravel version docs, this is for 5.3
     * https://laravel.com/docs/5.3/scheduling#defining-schedules
     *
     * @param Schedule $schedule
     */
    public static function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $files       = [];
            $pathToFiles = config('excel.export.store.path');

            foreach (glob("$pathToFiles/*.*") as $file) {
                if (filectime($file) + config('photon.exported_files_ttl') < time()) {
                    unlink($file);
                }
            }
        })->everyMinute();
    }
}