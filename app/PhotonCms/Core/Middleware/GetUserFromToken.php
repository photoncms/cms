<?php

/*
 * This file is part of jwt-auth.
 *
 * (c) Sean Tymon <tymon148@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Photon\PhotonCms\Core\Middleware;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Photon\PhotonCms\Core\Response\ResponseRepository;
use Tymon\JWTAuth\Middleware\BaseMiddleware;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Routing\ResponseFactory;
use Photon\PhotonCms\Core\Exceptions\PhotonException;

class GetUserFromToken extends BaseMiddleware
{

    /**
     * Create a new BaseMiddleware instance.
     *
     * @param \Illuminate\Contracts\Routing\ResponseFactory  $response
     * @param \Illuminate\Contracts\Events\Dispatcher  $events
     * @param \Tymon\JWTAuth\JWTAuth  $auth
     */
    public function __construct(
        ResponseFactory $response,
        Dispatcher $events,
        JWTAuth $auth,
        ResponseRepository $responseRepository
    )
    {
        $this->response           = $response;
        $this->events             = $events;
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
    public function handle($request, \Closure $next)
    {
        if (! $token = $this->auth->setRequest($request)->getToken()) {
            return $this->responseRepository->make('TOKEN_ABSENT');
        }

        try {
            $user = $this->auth->authenticate($token);
        } catch (TokenExpiredException $e) {
            return $this->responseRepository->make('TOKEN_EXPIRED');
        } catch (JWTException $e) {
            return $this->responseRepository->make('TOKEN_INVALID');
        }

        if (! $user) {
            return $this->responseRepository->make('USER_NOT_FOUND');
        }
        
        // If we should impersonate a user in this round, then let's do it
        $parsedPayload = \JWTAuth::getPayload($token);
        $impersonatedUserId = $parsedPayload->get('impersonating');
        if ($impersonatedUserId) {
            if (!\Auth::onceUsingId($impersonatedUserId)) {
                throw new PhotonException('IMPERSONATION_FAILED_USER_NOT_FOUND', ['id' => $impersonatedUserId]);
            }
        }

        $this->events->fire('tymon.jwt.valid', $user);

        return $next($request);
    }
}
