<?php

namespace Photon\PhotonCms\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Photon\PhotonCms\Core\Services\Reporting\ReportingService;

class ReportingServiceProvider extends ServiceProvider
{

    /**
     * Registers services
     */
    public function register()
    {
        $this->app->singleton('ReportingService', function($app) {
            $reportingService = new ReportingService();
            if (\Request::exists('reporting') && \Request::get('reporting')) {
                $reportingService->activate();
            }
            return $reportingService;
        });
    }
}