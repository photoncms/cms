<?php

namespace Photon\PhotonCms\Dependencies\Notifications\Helpers;

use Photon\PhotonCms\Dependencies\DynamicModels\User;
use Photon\PhotonCms\Core\Entities\NotificationHelpers\Contracts\NotificationHelperInterface;

use Photon\PhotonCms\Dependencies\Notifications\NewUserRegistered;

class NewUserRegisteredHelper implements NotificationHelperInterface
{
    /**
     * Determines who is supposed to be notified with the specific notification
     * and notifies using native Laravel notification.
     *
     * @param array $data
     */
    public function notify($newUser)
    {
        $currentUser = \Auth::user();
        $administrators = User::
            whereHas('roles', function ($q) {
                $q->where('name', 'super_administrator');
            })
            ->get();

        foreach ($administrators as $administrator) {
            $administrator->notify(new NewUserRegistered($newUser));
        }
    }
}