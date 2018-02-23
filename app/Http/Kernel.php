<?php

namespace Photon\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
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
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Photon\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
        'public' => [ // Used for public websites
            \Photon\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            // \Photon\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
        'broadcasting' => [
            'jwt.auth',
        ],
        'adminpanel' => [
            'jwt.auth',
            'convertStringBooleans',
//            'jwt.refresh' // This will refresh the token every once in a while, making it necesary to renew the token (good against token hijack)
        ],
        'throttle_protected' => [
            'throttle'
        ]
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \Photon\Http\Middleware\Authenticate::class,
//        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
//        'guest' => \Photon\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Photon\Http\Middleware\ThrottleRequests::class,
        'convertStringBooleans' => \Photon\PhotonCms\Core\Middleware\ConvertStringBooleans::class,
        'jwt.auth' => \Photon\PhotonCms\Core\Middleware\GetUserFromToken::class,
        'jwt.refresh' => \Tymon\JWTAuth\Middleware\RefreshToken::class,
        'checkLicense' => \Photon\PhotonCms\Core\Middleware\CheckLicense::class,
        'isSuperAdmin' => \Photon\PhotonCms\Core\Middleware\IsSuperAdmin::class
    ];
}
