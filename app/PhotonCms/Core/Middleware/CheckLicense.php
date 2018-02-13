<?php

namespace Photon\PhotonCms\Core\Middleware;

use Tymon\JWTAuth\Middleware\BaseMiddleware;
use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Illuminate\Support\Facades\Cache;
use Photon\PhotonCms\Core\Helpers\LicenseKeyHelper;

class CheckLicense extends BaseMiddleware
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
        if(Cache::has('photon-license'))
            return $next($request);

        // check if license key exist
        $key = LicenseKeyHelper::checkLicenseKey();

        // ping home
        $validKey = LicenseKeyHelper::pingHome($key);

        // store data in cache
        Cache::put('photon-license', $validKey, 60);

        // store license key if it does not exist
        if(!$key) 
            LicenseKeyHelper::storeLiceseKey($validKey['body']['license_key']);

        return $next($request);
    }
}
