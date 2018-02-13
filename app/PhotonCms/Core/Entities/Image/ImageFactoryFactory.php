<?php

namespace Photon\PhotonCms\Core\Entities\Image;

use Photon\PhotonCms\Core\Entities\Image\Contracts\ImageFactoryInterface;
use Photon\PhotonCms\Core\Exceptions\PhotonException;

class ImageFactoryFactory
{
    /**
     * Creates a factory inastance for a specific image library.
     *
     * @param string $libraryName
     * @return ImageFactoryInterface
     * @throws PhotonException
     */
    public static function make($libraryName)
    {
        $className = 'Photon\PhotonCms\Core\Entities\Image\\'.$libraryName.'ImageFactory';

        if (class_exists($className)) {
            return $className;
        }
        else {
            throw new PhotonException('IMAGE_FACTORY_DOESNT_EXIST', ['class' => $className]);
        }
    }
}