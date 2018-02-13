<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleField\Contracts;

/**
 * Segregated interface used to point out that the class which implements it will be able to transform the output of that class' field type.
 */
interface TransformsOutput
{
    /**
     * Transforms a value for the object attribute output.
     * Serves as a generic getter.
     *
     * @param mixed $object
     * @param string $attributeName
     */
    public function output($object, $attributeName);
}