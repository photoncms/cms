<?php

namespace Photon\PhotonCms\Core\Entities\Image;

use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Photon\PhotonCms\Core\Entities\Image\Contracts\ImageGatewayInterface;

abstract class GDBaseGateway implements ImageGatewayInterface
{
    /**
     * Instead of using documented constants:
     * IMG_NEAREST_NEIGHBOUR, IMG_BILINEAR_FIXED, IMG_BICUBIC, IMG_BICUBIC_FIXED
     * These give much better results:
     * IMG_BELL, IMG_BSPLINE, IMG_GAUSSIAN, IMG_HERMITE, IMG_HAMMING, IMG_POWER, IMG_TRIANGLE
     *
     * There is usually a different effect for constants when you grow or shrink the image.
     *
     * @param resource $image
     * @param int $width
     * @param int $height
     * @param const $mode
     * @return resource
     */
    public function resize($image, $width, $height, $mode = null)
    {
        $mode = ($mode === null)
            ? IMG_BICUBIC // default value
            : $mode;
        
        if (!is_resource($image)) {
            throw new PhotonException('UNEXPECTED_TYPE', ['expected' => 'resource']);
        }
            
        return imagescale($image, $width, $height);
    }

    /**
     *
     * @param resource $image
     * @param int $x
     * @param int $y
     * @param int $width
     * @param int $height
     * @return resource
     */
    public function crop($image, $x, $y, $width, $height)
    {
        if (!is_resource($image)) {
            throw new PhotonException('UNEXPECTED_TYPE', ['expected' => 'resource']);
        }

        return imagecrop($image, ['x' => $x, 'y' => $y, 'width' => $width, 'height' => $height]);
    }

    /**
     * Reads the image width from the image resource.
     *
     * @param resource $image
     * @return int
     * @throws PhotonException
     */
    public function getWidth($image)
    {
        if (!is_resource($image)) {
            throw new PhotonException('UNEXPECTED_TYPE', ['expected' => 'resource']);
        }

        return imagesx($image);
    }

    /**
     * Reads the image height from the image resource.
     *
     * @param resource $image
     * @return int
     * @throws PhotonException
     */
    public function getHeight($image)
    {
        if (!is_resource($image)) {
            throw new PhotonException('UNEXPECTED_TYPE', ['expected' => 'resource']);
        }

        return imagesy($image);
    }

    public function isValid($image)
    {
        return is_resource($image);
    }
}