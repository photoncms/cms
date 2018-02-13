<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleSubscriber;

use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Photon\PhotonCms\Core\IAPI\IAPI;
use \Carbon\Carbon;
use Photon\PhotonCms\Core\Entities\NotificationHelpers\NotificationHelperFactory;

class DynamicModuleSubscriberFactory
{
    public static function subscribe($entry, $tableName, $user)
    {
        // find all existing users
        $subscribedUsers = \Cache::get('subscribe:' . $tableName . ':' . $entry->id, []);

        // set subscription length
        $subscriptionLength = env("SUBSCRIPTION_TIME", 10);

        // remove expired subscriptions
        $unsubscribedUsers = [];
        foreach ($subscribedUsers as $userId => $timestamp) {
            if( Carbon::now()->diffInMinutes(Carbon::createFromTimestamp( $timestamp )) > $subscriptionLength ) {
                unset($subscribedUsers[$userId]);
                $unsubscribedUsers[] = $userId;
            }
        }

        $data = [
            "subscribedUsers" => $subscribedUsers,
            "unsubscribedUsers" => $unsubscribedUsers,
            "tableName" => $tableName,
            "entry" => $entry,
        ];        
        NotificationHelperFactory::makeByHelperName("UserUnsubscribed")->notify($data);
        
        // ToDo: if this is new subscription send notification to all existing subscribers
        if(!array_key_exists($user->id, $subscribedUsers)) {     
            $data = [
                "subscribedUsers" => $subscribedUsers,
                "user" => $user,
                "tableName" => $tableName,
                "entry" => $entry,
            ];
            NotificationHelperFactory::makeByHelperName("UserSubscribed")->notify($data);
        }

        // insert / update subscription time
        $subscribedUsers[$user->id] = Carbon::now()->timestamp;

        // update session
        ksort($subscribedUsers, SORT_NUMERIC);
        \Cache::forever('subscribe:' . $tableName . ':' . $entry->id, $subscribedUsers);

        return self::prepareFullList($subscribedUsers, $user);
    }

    public static function unsubscribe($entry, $tableName, $user)
    {
        // find all existing users
        $subscribedUsers = \Cache::get('subscribe:' . $tableName . ':' . $entry->id, []);

        // delete user from cache 
        if(array_key_exists($user->id, $subscribedUsers)) {
            unset($subscribedUsers[$user->id]);
            $data = [
                "subscribedUsers" => $subscribedUsers,
                "unsubscribedUsers" => [
                    $user->id
                ],
                "tableName" => $tableName,
                "entry" => $entry,
            ];
            NotificationHelperFactory::makeByHelperName("UserUnsubscribed")->notify($data);
        }

        \Cache::forever('subscribe:' . $tableName . ':' . $entry->id, $subscribedUsers);

        return self::prepareFullList($subscribedUsers, $user);
    }

    /**
     * Prepare list of subscribed users
     *
     * @param string  $tableName
     * @param integer $entryId
     * @return \Illuminate\Http\Response
     */
    private static function prepareFullList($subscribedUsers, $user)
    {
        $fullListOfSubscribedUsers = [];

        foreach ($subscribedUsers as $userId => $timestamp) {
            if($user->id == $userId) {
                unset($subscribedUsers[$userId]);
                continue;
            }
            $iapi = new \Photon\PhotonCms\Core\IAPI\IAPI();
            $fullListOfSubscribedUsers[] = $iapi->users($userId)->get();
        }

        return $fullListOfSubscribedUsers;
    }

}