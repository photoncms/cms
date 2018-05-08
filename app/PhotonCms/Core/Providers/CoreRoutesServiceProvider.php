<?php

namespace Photon\PhotonCms\Core\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class CoreRoutesServiceProvider extends ServiceProvider
{
    /**
     * The controller namespace for the application.
     *
     * @var string|null
     */
    protected $namespace = 'Photon\Http\Controllers';

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapCpRoutes();
    }

    /**
     * Define the "api" routes for the application.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->namespace('Photon\PhotonCms\Core\Controllers')
            ->group(base_path('app/PhotonCms/Core/Routes/api.php'));
    }

    /**
     * Define the "cp" routes for the application.
     *
     * @return void
     */
    protected function mapCpRoutes()
    {
        Route::middleware('public')
            ->namespace('Photon\PhotonCms\Core\Controllers')
            ->group(base_path('app/PhotonCms/Core/Routes/cp.php'));
        
    }

}