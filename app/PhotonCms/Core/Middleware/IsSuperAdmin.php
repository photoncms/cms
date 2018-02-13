<?php

namespace Photon\PhotonCms\Core\Middleware;

use Tymon\JWTAuth\Middleware\BaseMiddleware;
use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Photon\PhotonCms\Core\PermissionServices\PermissionHelper;

class IsSuperAdmin extends BaseMiddleware
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
        if(!\Auth::user())
            throw new PhotonException("INSUFICIENT_PERMISSIONS");          
        
        if(!PermissionHelper::isAdminUser())
            throw new PhotonException("INSUFICIENT_PERMISSIONS");            

        return $next($request);
    }
}
