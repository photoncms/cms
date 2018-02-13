<?php

namespace Photon\PhotonCms\Core\Entities\FieldType;

use Photon\PhotonCms\Core\Transform\BaseTransformer;
use Photon\PhotonCms\Core\Transform\Contracts\TransformerFullTransformInterface;
use Photon\PhotonCms\Core\Transform\Contracts\TransformerFullTransformConvertedInterface;
use Photon\PhotonCms\Core\Exceptions\PhotonException;

/**
 * Transforms FieldType instances into various output packages.
 */
class FieldTypeTransformer extends BaseTransformer implements TransformerFullTransformInterface, TransformerFullTransformConvertedInterface
{

    /**
     * Transforms an object into a generic array
     *
     * @var FieldType $object
     * @return array
     */
    public function transform(FieldType $object)
    {
        $objectArray = [
            'id' => ($object->id !== null) ? (int) $object->id : null,
            'type' => $object->type,
            'title' => $object->title,
            'laravel_type' => $object->laravel_type,
            'is_system' => (bool) $object->is_system
        ];

        // Add any relations preloaded with eager loading
        $result = array_merge($objectArray, $object->getRelations());

        $this->transformGenericObjects($result);

        return $result;
    }

    /**
     * Transforms the whole object into an array.
     * Passes each attribute without further conversion.
     *
     * @param FieldType $object
     */
    public function fullTransform($object)
    {
        if (!($object instanceof FieldType)) {
            throw new PhotonException('NOT_INSTANCE_OF_FIELD_TYPE', ['given' => get_class($object), 'expected' => '\Photon\PhotonCms\Core\Entities\FieldType\FieldType']);
        }

        return [
            'id' => ($object->id !== null) ? (int) $object->id : null,
            'type' => $object->type,
            'title' => $object->title,
            'laravel_type' => $object->laravel_type,
            'is_system' => (bool) $object->is_system
        ];
    }

    /**
     * Transforms the whole object into an array.
     * Converts each attribute if necessary.
     *
     * @param FieldType $object
     */
    public function fullTransformConverted($object)
    {
        if (!($object instanceof FieldType)) {
            throw new PhotonException('NOT_INSTANCE_OF_FIELD_TYPE', ['given' => get_class($object), 'expected' => '\Photon\PhotonCms\Core\Entities\FieldType\FieldType']);
        }

        $objectArray = [
            'id' => ($object->id !== null) ? (int) $object->id : null,
            'type' => $object->type,
            'title' => $object->title,
            'laravel_type' => $object->laravel_type,
            'is_system' => (bool) $object->is_system
        ];

        $this->transformGenericObjects($objectArray);

        return $objectArray;
    }
}