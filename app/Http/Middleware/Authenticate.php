<?php

namespace Photon\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Contracts\Auth\Guard;
use Photon\PhotonCms\Core\Response\ResponseRepository;

class Authenticate
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param Guard $auth
     * @param ResponseRepository $responseRepository
     * @return void
     */
    public function __construct(
        Guard $auth,
        ResponseRepository $responseRepository
    )
    {
        $this->auth               = $auth;
        $this->responseRepository = $responseRepository;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                return $this->responseRepository->make('AUTH_UNAUTHORIZED');
            } else {
                return redirect()->guest('auth/login');
            }
        }

        return $next($request);
    }
}
