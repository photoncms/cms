<?php

namespace Photon\PhotonCms\Core\Entities\User;

use Photon\PhotonCms\Core\Transform\BaseTransformer;

/**
 * Transforms User instances into various output packages.
 */
class UserTransformer extends BaseTransformer
{

    /**
     * Transforms an object into a generic array
     *
     * @var User $object
     * @return array
     */
    public function transform(User $object)
    {
        $objectArray = [
            'id' => (int) $object->id,
            'first_name' => $object->first_name,
            'last_name' => $object->last_name,
            'email' => $object->email,
            'roles' => $object->roles
        ];

        $this->transformGenericObjects($objectArray);
        
        return $objectArray;
    }
}