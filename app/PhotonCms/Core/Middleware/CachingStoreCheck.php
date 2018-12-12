<?php

namespace Photon\PhotonCms\Core\Middleware;

use Tymon\JWTAuth\Middleware\BaseMiddleware;
use Photon\PhotonCms\Core\Exceptions\PhotonException;

class CachingStoreCheck extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        $driver = config("cache.default");
        $photonCaching = env("USE_PHOTON_CACHING");

        if($photonCaching && $driver != "redis") {
            throw new PhotonException('PHOTON_INVALID_CACHE_DRIVER');   
        }

        return $next($request);
    }
}
