<?php

namespace Photon\PhotonCms\Dependencies\Notifications\Helpers;

use Photon\PhotonCms\Dependencies\DynamicModels\User;
use Photon\PhotonCms\Core\Entities\NotificationHelpers\Contracts\NotificationHelperInterface;

use Photon\PhotonCms\Dependencies\Notifications\UserSubscribed;

class UserSubscribedHelper implements NotificationHelperInterface
{
    /**
     * Determines who is supposed to be notified with the specific notification
     * and notifies using native Laravel notification.
     *
     * @param array $data
     */
    public function notify($data)
    {
        foreach ($data['subscribedUsers'] as $subscribedUserId => $timestamp) {
            $subscribedUser = User::find($subscribedUserId);
            $subscribedUser->notify(new UserSubscribed($data['user'], $data['tableName'], $data['entry']));
        }
    }
}