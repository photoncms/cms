<?php

namespace Photon\PhotonCms\Core\Scheduler;

use Illuminate\Console\Scheduling\Schedule;

class PhotonScheduler
{

    /**
     * This method should only be called from Photon\Console\Kernel schedule method and nowhere else.
     *
     * @param Schedule $schedule
     */
    public static function schedule(Schedule $schedule)
    {
        $jobs          = [];

        // handle core jobs
        $pathToJobs    = config('photon.core_jobs_dir');
        $jobsNamespace = config('photon.core_jobs_namespace');

        foreach (glob("$pathToJobs/*.php") as $job) {
            $jobClassName     = basename($job, ".php");
            $jobFullClassName = '\\'.$jobsNamespace.$jobClassName;
            $jobFullClassName::schedule($schedule);
        }

        // handle user generated jobs
        $pathToJobs    = config('photon.jobs_dir');
        $jobsNamespace = config('photon.jobs_namespace');

        foreach (glob("$pathToJobs/*.php") as $job) {
            $jobClassName     = basename($job, ".php");
            $jobFullClassName = '\\'.$jobsNamespace.$jobClassName;
            $jobFullClassName::schedule($schedule);
        }
    }
}