<?php

namespace Photon\PhotonCms\Core\Entities\Image;

use Photon\PhotonCms\Core\Entities\Image\Contracts\ImageFactoryInterface;
use Photon\PhotonCms\Core\Exceptions\PhotonException;

class ImagickImageFactory implements ImageFactoryInterface
{
    private static $mimeTypes = [
        'image/png' => 'imagecreatefrompng',
        'image/jpg' => 'imagecreatefromjpeg',
        'image/jpeg' => 'imagecreatefromjpeg',
        'image/gif' => 'imagecreatefromgif'
    ];

    /**
     * Loads an image for processing using GD library.
     *
     * @param string $filePathAndName
     * @return resource|false
     * @throws PhotonException
     */
    public static function makeFromFile($filePathAndName)
    {
        try {
            $mimeType = \File::mimeType($filePathAndName);
        }
        catch (\Exception $e) {
            throw new PhotonException('FAILED_TO_READ_FILE_MIME_TYPE', ['file' => $filePathAndName]);
        }
        
        if(!key_exists($mimeType, self::$mimeTypes))
            return false;

        $handle = fopen($filePathAndName, 'rb');
        
        $imagick = new \Imagick();
        $imagick->readImageFile($handle); 

        if(!$imagick) {
            return false;
        }

        return $imagick;
    }
}