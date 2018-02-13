<?php

namespace Photon\PhotonCms\Core\Entities\Image;

use Photon\PhotonCms\Core\Entities\Image\Contracts\ImageFactoryInterface;
use Photon\PhotonCms\Core\Exceptions\PhotonException;

class ImagickImageFactory implements ImageFactoryInterface
{

    /**
     * Loads an image for processing using GD library.
     *
     * @param string $filePathAndName
     * @return resource|false
     * @throws PhotonException
     */
    public static function makeFromFile($filePathAndName)
    {
        $handle = fopen($filePathAndName, 'rb');
        
        $imagick = new \Imagick();
        $imagick->readImageFile($handle); 

        if(!$imagick) {
            return false;
        }

        return $imagick;
    }
}