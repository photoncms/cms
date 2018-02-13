<?php

namespace Photon\PhotonCms\Core\Entities\Image;

use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Photon\PhotonCms\Core\Entities\Image\GDBaseGateway;

class GDGifGateway extends GDBaseGateway
{
    /**
     * Saves the image data to a file.
     *
     * @param resource $image
     * @param string $pathAndName
     * @return boolean
     * @throws PhotonException
     */
    public function save($image, $pathAndName, $quality = null)
    {
        if (!is_resource($image)) {
            throw new PhotonException('UNEXPECTED_TYPE', ['expected' => 'resource']);
        }
        
        return imagegif(
            $image,
            config('filesystems.disks.assets.root').'/'.$pathAndName
        );
    }
}