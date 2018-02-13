<?php

namespace Photon\PhotonCms\Core\Entities\Image;

use Photon\PhotonCms\Core\Entities\Image\GDBaseGateway;

class GDJpegGateway extends GDBaseGateway
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
        $quality = ($quality === null)
            ? 100 // default value
            : $quality;

        if (!is_resource($image)) {
            throw new PhotonException('UNEXPECTED_TYPE', ['expected' => 'resource']);
        }

        return imagejpeg(
            $image,
            config('filesystems.disks.assets.root').'/'.$pathAndName,
            $quality
        );
    }
}