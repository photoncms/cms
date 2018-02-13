<?php

namespace Photon\PhotonCms\Core\Transform\Contracts;

interface TransformerFullTransformInterface
{
    /**
     * Transforms the whole object into an array.
     * Passes each attribute without further conversion.
     *
     * @param mixed $object
     */
    public function fullTransform($object);
}