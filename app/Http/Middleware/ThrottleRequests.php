<?php

namespace Photon\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Photon\PhotonCms\Core\Response\ResponseRepository;
use Illuminate\Routing\Middleware\ThrottleRequests as ThrottleRequestsBase;

class ThrottleRequests extends ThrottleRequestsBase
{

    /**
     * Create a new request throttler.
     *
     * @param RateLimiter $limiter
     * @param ResponseRepository $responseRepository
     * @return void
     */
    public function __construct(
        RateLimiter $limiter,
        ResponseRepository $responseRepository
    )
    {
        parent::__construct($limiter);
        $this->responseRepository = $responseRepository;
    }

    /**
     * Create a 'too many attempts' response.
     *
     * @param  string  $key
     * @param  int  $maxAttempts
     * @return \Illuminate\Http\Response
     */
    protected function buildResponse($key, $maxAttempts)
    {
        $response = $this->responseRepository->make('TOO_MANY_ATTEMPTS');

        return $this->addHeaders(
            $response, $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts),
            $this->limiter->availableIn($key)
        );
    }

    /**
     * Handle an incoming request.
     * IMPORTANT - method is overridden ony to be able to set maxAttempts and decayMinutes through
     * config and env. There is no other reason for overrdding the method.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int  $maxAttempts
     * @param  float|int  $decayMinutes
     * @return mixed
     */
    public function handle($request, Closure $next, $maxAttempts = null, $decayMinutes = null)
    {
        $maxAttempts = is_null($maxAttempts) ? config('photon.throttle_max_times') : $maxAttempts;
        $decayMinutes = is_null($decayMinutes) ? config('photon.throttle_cooldown_minutes') : $decayMinutes;
        $key = $this->resolveRequestSignature($request);

        if ($this->limiter->tooManyAttempts($key, $maxAttempts, $decayMinutes)) {
            return $this->buildResponse($key, $maxAttempts);
        }

        $this->limiter->hit($key, $decayMinutes);

        $response = $next($request);

        return $this->addHeaders(
            $response, $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts)
        );
    }
}
