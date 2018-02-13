<?php

namespace Photon\PhotonCms\Core\Entities\Image\Contracts;

interface ImageFactoryInterface
{

    /**
     * Loads an image for processing using a specific library.
     *
     * @param string $filePathAndName
     * @return resource|false
     * @throws PhotonException
     */
    public static function makeFromFile($filePathAndName);
}