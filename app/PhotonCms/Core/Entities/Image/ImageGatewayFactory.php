<?php

namespace Photon\PhotonCms\Core\Entities\Image;

use Photon\PhotonCms\Core\Exceptions\PhotonException;

class ImageGatewayFactory
{
    private static $mimeTypes = [
        'image/png' => 'Png',
        'image/jpg' => 'Jpeg',
        'image/jpeg' => 'Jpeg',
        'image/gif' => 'Gif'
    ];

    /**
     * Creates a gateway instance for a specific image library.
     *
     * @param string $libraryName
     * @param string $filePathAndName
     * @return string
     * @throws PhotonException
     */
    public static function make($libraryName, $filePathAndName)
    {
        try {
            $mimeType = \File::mimeType($filePathAndName);
        }
        catch (\Exception $e) {
            throw new PhotonException('FAILED_TO_READ_FILE_MIME_TYPE', ['file' => $filePathAndName]);
        }

        if (key_exists($mimeType, self::$mimeTypes)) {
            $className = 'Photon\PhotonCms\Core\Entities\Image\\'.$libraryName.self::$mimeTypes[$mimeType].'Gateway';

            if (class_exists($className)) {
                return new $className();
            }
            else {
                throw new PhotonException('IMAGE_GATEWAY_DOESNT_EXIST', ['class' => $className]);
            }
        }
        else {
            throw new PhotonException('UNSUPPORTED_MIME_TYPE', ['mimetype' => $mimeType]);
        }
    }
}