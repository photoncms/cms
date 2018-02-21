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
        $driver = env("CACHE_DRIVER");
        $photonCaching = env("USE_PHOTON_CACHING");

        if(!$photonCaching)
            return $next($request);

        if(!in_array($driver, ["file", "database"]))
            return $next($request);

        throw new PhotonException('PHOTON_INVALID_CACHE_DRIVER');   
    }
}
