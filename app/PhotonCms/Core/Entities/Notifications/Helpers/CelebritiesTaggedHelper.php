<?php

namespace Photon\PhotonCms\Core\Entities\Notifications\Helpers;

use Photon\PhotonCms\Dependencies\DynamicModels\User;
use Photon\PhotonCms\Core\Entities\NotificationHelpers\Contracts\NotificationHelperInterface;
use Photon\PhotonCms\Dependencies\DynamicModels\Assets;
use Photon\PhotonCms\Core\Entities\Notifications\CelebritiesTagged;

class CelebritiesTaggedHelper implements NotificationHelperInterface
{
    /**
     * Determines who is supposed to be notified with the specific notification
     * and notifies using native Laravel notification.
     *
     * @param array $data
     */
    public function notify($asset)
    {
        $asset = Assets::where('id', $asset->id)->with('tags_relation')->first();

        $subscribedUser = User::find($asset->created_by);
        $subscribedUser->notify(new CelebritiesTagged($asset));
    }
}
