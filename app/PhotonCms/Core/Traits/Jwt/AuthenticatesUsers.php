<?php

namespace Photon\PhotonCms\Core\Traits\Jwt;

use JWTAuth;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;

use Carbon\Carbon;

trait AuthenticatesUsers
{

    /**
     * Authenticates a user.
     *
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Request $request)
    {
        $this->validate($request,
            [
                'email' => 'required|email',
                'password' => 'required'
            ]
        );

        // grab credentials from the request
        $credentials = $request->only('email', 'password');

        if (\Config::get('photon.use_registration_service_email')) {
            $credentials['confirmed'] = 1;
        }

        try {
            // attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                if (\Config::get('photon.use_registration_service_email')) {
                    unset($credentials['confirmed']);
                    if (JWTAuth::attempt($credentials)) {
                        return $this->responseRepository->make('USER_NOT_CONFIRMED');
                    }
                }

                return $this->responseRepository->make('USER_LOGIN_FAILURE_INVALID_CREDENTIALS');
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return $this->responseRepository->make('USER_LOGIN_FAILURE');
        }

        $user = \Auth::user();
        $user->showRelations();

        // Check if expiration checking is on and the password expired
        if (config('jwt.use_password_expiration')) {
            $expiredTime = $user->password_created_at;

            // This will handle an invalid creation time
            if (!$expiredTime) {
                return $this->responseRepository->make('PASSWORD_EXPIRED');
            }

            $expiredTime = $expiredTime->addMinutes(config('jwt.password_expiration_time'));
            $currentTime = Carbon::now();

            if ($currentTime > $expiredTime) {
                return $this->responseRepository->make('PASSWORD_EXPIRED');
            }
        }

        // all good so return the token
        return $this->responseRepository->make('USER_LOGIN_SUCCESS', ['user' => $user, 'token' => ['token' => $token, 'ttl' => \Config::get('jwt.ttl')]]);
    }

    /**
     * Retrieves an authenticated user via header token.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAuthenticatedUser()
    {
        $user = \Auth::user();
        $user->showRelations();

        $token = JWTAuth::getToken();
        $parsedPayload = \JWTAuth::getPayload($token);
        $impersonatedUserId = $parsedPayload->get('impersonating');
        $impersonating = (bool) $impersonatedUserId;

        // the token is valid and we have found the user via the sub claim
        return $this->responseRepository->make('GET_LOGGED_IN_USER_SUCCESS', ['user' => $user, 'impersonating' => $impersonating]);
    }

    /**
     * Refreshes a logged in user token.
     *
     * @return \Illuminate\Http\Response
     */
    public function refreshToken()
    {
        $oldToken = JWTAuth::getToken();

        $parsedPayload = JWTAuth::getPayload($oldToken);
        $impersonatedUserId = $parsedPayload->get('impersonating');

        $user = JWTAuth::parseToken()->authenticate();
        $newToken = JWTAuth::fromUser($user, ['impersonating' => $impersonatedUserId]);

//        JWTAuth::invalidate($oldToken); // The old token will be kept valid until it expires by itself

        return $this->responseRepository->make('TOKEN_REFRESH_SUCCESS', ['token' => ['token' => $newToken, 'ttl' => \Config::get('jwt.ttl')]]);
    }

    /**
     * Logs a user out by invalidating the JWT token.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        $token = JWTAuth::getToken();
        JWTAuth::invalidate($token);

        // all good so return the token
        return $this->responseRepository->make('USER_LOGOUT_SUCCESS');
    }
}