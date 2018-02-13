<?php

namespace Photon\PhotonCms\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Photon\PhotonCms\Core\Services\Logging\ErrorLogService;

class LogServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton('ErrorLogService', function($app) {
            return new ErrorLogService();
        });

        // Add other logging services here when and if necessary.
    }
}