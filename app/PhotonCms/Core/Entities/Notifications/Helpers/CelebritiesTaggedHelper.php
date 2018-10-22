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
     * @return void
     */
    public function notify($data)
    {
        $asset = Assets::where('id', $data['asset']->id)->first();

        $subscribedUser = User::find($asset->created_by);

        $subscribedUser->notify(new CelebritiesTagged($asset, $data['recognizedFaces']));
    }
}
