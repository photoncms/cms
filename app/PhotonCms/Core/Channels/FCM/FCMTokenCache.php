<?php

namespace Photon\PhotonCms\Core\Channels\FCM;

class FCMTokenCache
{

    /**
     * Array name for storing FCM tokens in Cache.
     *
     * @var string
     */
    private static $cacheKey = 'notification_fcm_tokens';

    /**
     * Adds a FCM token to the specified user in the Cache and removes the token from anyone who was using it before.
     *
     * @param int $userId
     * @param string $userToken
     */
    public static function addUserToken($userId, $userToken)
    {
        self::ensureCacheExistance();
        $tokenCollection = \Cache::get(self::$cacheKey);
        $tokenCollection = self::clearTokenUsage($userToken, $tokenCollection);
        $tokenCollection[$userId][] = $userToken;
        \Cache::forever(self::$cacheKey, $tokenCollection);
    }

    /**
     * Retrieves an array of all available FCM tokens for a specified user.
     *
     * @param int $userId
     * @return array
     */
    public static function getUserTokens($userId)
    {
        self::ensureCacheExistance();
        $tokenCollection = \Cache::get(self::$cacheKey);
        return (array_key_exists($userId, $tokenCollection))
            ? $tokenCollection[$userId]
            : [];
    }

    /**
     * Removes a token from the speified user token cache.
     *
     * @param int $userId
     * @param string $userToken
     */
    public static function removeUserToken($userId, $userToken)
    {
        self::ensureCacheExistance();
        $tokenCollection = \Cache::get(self::$cacheKey);
        if (
            array_key_exists($userId, $tokenCollection) &&
            is_array($tokenCollection[$userId]) &&
            !empty($tokenCollection[$userId])
        ) {
            foreach ($tokenCollection[$userId] as $key => $token) {
                if ($token === $userToken) {
                    unset($tokenCollection[$userId][$key]);
                }
            }
        }
        \Cache::forever(self::$cacheKey, $tokenCollection);
    }

    /**
     * Removes all available tokens for a specified user from the cache.
     *
     * @param int $userId
     */
    public static function clearUserTokens($userId)
    {
        self::ensureCacheExistance();
        $tokenCollection = \Cache::get(self::$cacheKey);
        if (array_key_exists($userId, $tokenCollection)) {
            unset($tokenCollection[$userId]);
        }
        \Cache::forever(self::$cacheKey, $tokenCollection);
    }

    /**
     * Ensures the cache array for storing FCM tokens exists.
     */
    private static function ensureCacheExistance()
    {
        if (!\Cache::has(self::$cacheKey)) {
            \Cache::forever(self::$cacheKey, []);
        }
    }

    /**
     * Removes a token from any users available token list.
     * Returns the modified array.
     *
     * @param string $userToken
     * @param array $tokens
     * @return array
     */
    private static function clearTokenUsage($userToken, array $tokens)
    {
        foreach ($tokens as $key => $token) {
            if ($token === $userToken) {
                unset ($tokens[$key]);
            }
            elseif (is_array($token) && ! empty($token)) {
                $tokens[$key] = self::clearTokenUsage($userToken, $token);
            }
        }

        return $tokens;
    }
}