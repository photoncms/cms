<?php

namespace Photon\PhotonCms\Core\Transform\Contracts;

interface TransformerFullTransformConvertedInterface
{
    /**
     * Transforms the whole object into an array.
     * Converts each attribute if necessary.
     *
     * @param Module $object
     */
    public function fullTransformConverted($object);
}