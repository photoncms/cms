<?php

namespace Photon\PhotonCms\Core\Traits\Jwt;

use Photon\PhotonCms\Dependencies\DynamicModels\User;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Photon\PhotonCms\Core\Exceptions\PhotonException;

trait ImpersonatesUsers
{

    /**
     * Start impersonating a user with the provided ID.
     *
     * If the user exists, the ID is saved within 'impersonation' custom claim within the new token. The old token is invalidated.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function startImpersonating($id)
    {
        $impersonatedUser = User::find($id);
        if (!$impersonatedUser) {
            throw new PhotonException('IMPERSONATION_FAILED_USER_NOT_FOUND', ['id' => $id]);
        }

        $oldToken = JWTAuth::getToken();

        // Check if already on
        $parsedPayload = JWTAuth::getPayload($oldToken);
        $impersonatedUserId = $parsedPayload->get('impersonating');

        if ($impersonatedUserId) {
            throw new PhotonException('IMPERSONATION_ALREADY_ON');
        }

        $user = \Auth::user();
        $newToken = JWTAuth::fromUser($user, ['impersonating' => $id]);

        JWTAuth::invalidate($oldToken);

        return $this->responseRepository->make('IMPERSONATING_ON', ['token' => ['token' => $newToken, 'ttl' => config('jwt.ttl')]]);
    }

    /**
     * Stop impersonating a user.
     *
     * Invalidates an old token which had the 'impersonation' custom claim, and creates a new one with this claim set to false.
     *
     * @return \Illuminate\Http\Response
     */
    public function stopImpersonating()
    {
        $oldToken = JWTAuth::getToken();

        $parsedPayload = JWTAuth::getPayload($oldToken);
        $impersonatedUserId = $parsedPayload->get('impersonating');

        if (!$impersonatedUserId) {
            throw new PhotonException('IMPERSONATION_ALREADY_OFF');
        }

        $user = JWTAuth::parseToken()->authenticate();
        $newToken = JWTAuth::fromUser($user, ['impersonating' => false]);

        JWTAuth::invalidate($oldToken);

        return $this->responseRepository->make('IMPERSONATING_OFF', ['token' => ['token' => $newToken, 'ttl' => \Config::get('jwt.ttl')]]);
    }
}