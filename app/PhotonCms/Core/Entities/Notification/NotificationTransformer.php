<?php

namespace Photon\PhotonCms\Core\Entities\Notification;

use Photon\PhotonCms\Core\Transform\BaseTransformer;
use Illuminate\Notifications\DatabaseNotification;

/**
 * Transforms User instances into various output packages.
 */
class NotificationTransformer extends BaseTransformer
{

    /**
     * Transforms an object into a generic array
     *
     * @var User $object
     * @return array
     */
    public function transform(DatabaseNotification $object)
    {
        $parsedClassNamespace = explode('\\', $object->type);
        $notificationClassName = end($parsedClassNamespace);

        $objectArray = [
            'id' => $object->id,
            'type' => $notificationClassName,
            'read_at' => $object->read_at,
            'created_at' => $object->created_at
        ];
        $objectArray = $objectArray + $object->data;

        $this->transformGenericObjects($objectArray);
        
        return $objectArray;
    }
}