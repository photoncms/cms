<?php

namespace Photon\PhotonCms\Core\Entities\ModuleType;

use Photon\PhotonCms\Core\Transform\BaseTransformer;
//use Photon\PhotonCms\Core\Transform\Contracts\TransformerFullTransformInterface;
//use Photon\PhotonCms\Core\Transform\Contracts\TransformerFullTransformConvertedInterface;
use Photon\PhotonCms\Core\Exceptions\PhotonException;

/**
 * Transforms ModuleType instances into various output packages.
 */
class ModuleTypeTransformer extends BaseTransformer
{

    /**
     * Transforms an object into a generic array
     *
     * @var ModuleType $object
     * @return array
     */
    public function transform(ModuleType $object)
    {
        $objectArray = [
            'id' => (int) $object->id,
            'type' => $object->type,
            'title' => $object->title
        ];

        // Add any relations preloaded with eager loading
        $result = array_merge($objectArray, $object->getRelations());

        $this->transformGenericObjects($result);

        return $result;
    }
}