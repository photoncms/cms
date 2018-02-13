<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleField\Contracts;

/**
 * Segregated interface used to point out that the class which implements it will be able to transform the input of that class' field type.
 */
interface TransformsInput
{
    /**
     * Transforms a value for the object attribute input.
     * Serves as a generic setter.
     *
     * @param mixed $object
     * @param string $attributeName
     * @param mixed $value
     */
    public function input($object, $attributeName, $value);
}