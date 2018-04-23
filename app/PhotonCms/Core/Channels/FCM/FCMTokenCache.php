<?php

namespace Photon\PhotonCms\Core\Channels\FCM;

use Photon\PhotonCms\Core\Entities\FcmTokens\FcmTokens;

class FCMTokenCache
{
    /**
     * Adds a FCM token to the specified user in the Cache and removes the token from anyone who was using it before.
     *
     * @param int $userId
     * @param string $userToken
     */
    public static function addUserToken($userId, $userToken)
    {
        $fcmTokenCheck = FcmTokens::where('user', $userId)
            ->where('token', $userToken)
            ->count();

        if($fcmTokenCheck > 0) {
            return;
        }

        $fcmToken = new FcmTokens;

        $fcmToken->token = $userToken;

        $fcmToken->user = $userId;

        $fcmToken->save();
    }

    /**
     * Retrieves an array of all available FCM tokens for a specified user.
     *
     * @param int $userId
     * @return array
     */
    public static function getUserTokens($userId)
    {
        $fcmTokens = FcmTokens::where('user', $userId)->get();

        if(!$fcmTokens) {
            return [];
        }

        $userTokens = array();

        foreach($fcmTokens as $fcmToken) {
            $userTokens[] = $fcmToken->token;
        }

        return $userTokens;
    }

    /**
     * Removes a token from the speified user token cache.
     *
     * @param int $userId
     * @param string $userToken
     */
    public static function removeUserToken($userId, $userToken)
    {
        $fcmToken = FcmTokens::where('user', $userId)
            ->where('token', $userToken)
            ->delete();
    }

    /**
     * Removes all available tokens for a specified user from the cache.
     *
     * @param int $userId
     */
    public static function clearUserTokens($userId)
    {
        $fcmToken = FcmTokens::where('user', $userId)->delete();
    }
}