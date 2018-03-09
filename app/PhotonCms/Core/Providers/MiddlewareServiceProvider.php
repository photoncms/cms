<?php

namespace Photon\PhotonCms\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Photon\PhotonCms\Core\Services\Logging\ErrorLogService;

class MiddlewareServiceProvider extends ServiceProvider
{
    /**
     * The application's global HTTP middleware stack.
     *
     * @var array
     */
    protected $middleware = [
        \Photon\PhotonCms\Core\Middleware\CachingStoreCheck::class        
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \Photon\Http\Middleware\EncryptCookies::class,
            \Photon\Http\Middleware\VerifyCsrfToken::class
        ],
        'public' => [ 
            \Photon\Http\Middleware\EncryptCookies::class
        ],
        'broadcasting' => [
            \Photon\PhotonCms\Core\Middleware\GetUserFromToken::class
        ],
        'adminpanel' => [
            \Photon\PhotonCms\Core\Middleware\GetUserFromToken::class,
            \Photon\PhotonCms\Core\Middleware\ConvertStringBooleans::class
        ]
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'convertStringBooleans' => \Photon\PhotonCms\Core\Middleware\ConvertStringBooleans::class,
        'jwt.auth' => \Photon\PhotonCms\Core\Middleware\GetUserFromToken::class,
        'jwt.refresh' => \Tymon\JWTAuth\Middleware\RefreshToken::class,
        'checkLicense' => \Photon\PhotonCms\Core\Middleware\CheckLicense::class,
        'isSuperAdmin' => \Photon\PhotonCms\Core\Middleware\IsSuperAdmin::class
    ];

    public function register()
    {        
        $this->registerAdditionalMiddleware($this->middleware);
        $this->registerAdditionalMiddlewareGroups($this->middlewareGroups);
        $this->registerAdditionalMiddlewareAlias($this->routeMiddleware);
    }

    /**
     * Register additional middleware.
     *
     * @param array $middlewares
     */
    private function registerAdditionalMiddleware(array $middlewares)
    {
        foreach ($middlewares as $middleware) {
            if (class_exists($middleware, true)) {
            	$this->app->make('Illuminate\Contracts\Http\Kernel')->pushMiddleware($middleware);
            }
        }
    }

    /**
     * Register additional middleware groups.
     *
     * @param array $middlewareGroups
     */
    private function registerAdditionalMiddlewareGroups(array $middlewareGroups)
    {
        foreach ($middlewareGroups as $group => $middlewares) {
        	foreach ($middlewares as $middleware) {
	            if (class_exists($middleware, true)) {
	            	$this->app->router->pushMiddlewareToGroup($group, $middleware);
	            }
        	}
        }
    }

    /**
     * Register additional middleware alias.
     *
     * @param array $middlewareGroups
     */
    private function registerAdditionalMiddlewareAlias(array $routeMiddleware)
    {
        foreach ($routeMiddleware as $name => $middleware) {
            if (class_exists($middleware, true)) {
            	$this->app->router->aliasMiddleware($name, $middleware);
            }
        }
    }
}