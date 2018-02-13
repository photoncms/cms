<?php

namespace Photon\PhotonCms\Core\IAPI;

use Tymon\JWTAuth\Providers\Auth\IlluminateAuthAdapter;

/**
 * Photon Internal API
 */
class IAPI
{

    /**
     * Create a IAPICallChain instance based on first attribute request in the call chain.
     * First attribute request will be assigned as the first step of the IAPICallChain instance.
     *
     * @param string $name
     * @return IAPICallChain
     */
    public function __get($name)
    {
        $this->loginIapiUserIfNotLoggedIn();
        $IAPICall = new IAPICallChain();
        return $IAPICall->{$name};
    }

    /**
     * Create a IAPICallChain instance based on first function call in the call chain.
     * First function call will be assigned as the first step of the IAPICallChain instance.
     *
     * @param string $name
     * @param array $arguments
     * @return IAPICallChain
     */
    public function __call($name, $arguments)
    {
        $this->loginIapiUserIfNotLoggedIn();
        $IAPICall = new IAPICallChain();
        return call_user_func_array([$IAPICall, $name], $arguments);
    }

    /**
     * Logs in the iapi user if a user token doesn't exist within the request.
     */
    public function loginIapiUserIfNotLoggedIn()
    {
        $currentToken = \JWTAuth::getToken();
        if (!$currentToken) {
            $this->auth = app('Tymon\JWTAuth\Providers\Auth\IlluminateAuthAdapter');
            $user = $this->auth->byId(config('iapi.iapi_user_id'));
            $token = \JWTAuth::fromUser($user);
            \JWTAuth::setToken($token);
        }
    }
}