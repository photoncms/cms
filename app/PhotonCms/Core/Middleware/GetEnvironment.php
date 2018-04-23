<?php

namespace Photon\PhotonCms\Core\Middleware;

use Tymon\JWTAuth\Middleware\BaseMiddleware;
use Photon\PhotonCms\Core\Exceptions\PhotonException;

class GetEnvironment extends BaseMiddleware
{
    /**
     * Retrievs an environment setting. If set to 'local' will include that info in a header response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        $response = $next($request);

        if (\App::environment('local')) {
            $response->header('Photon-Environment', 'local');
        }
        
        return $response;
    }
}
