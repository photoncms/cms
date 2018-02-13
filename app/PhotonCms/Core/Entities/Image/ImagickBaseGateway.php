<?php

namespace Photon\PhotonCms\Core\Entities\Image;

use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Photon\PhotonCms\Core\Entities\Image\Contracts\ImageGatewayInterface;

abstract class ImagickBaseGateway implements ImageGatewayInterface
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
        $newImage = clone $image;

        // $newImage->setImageResolution($width, $height);
        // $newImage->setImageUnits(1);
        // $newImage->setInterlaceScheme(\Imagick::INTERLACE_JPEG);
        // $newImage->setImageCompression(\Imagick::COMPRESSION_JPEG);
        // $newImage->setImageCompressionQuality(80);

        // $newImage->setImageUnits(\Imagick::RESOLUTION_PIXELSPERINCH);
        $newImage->setImageResolution(72, 72);

        // $newImage->resampleImage(72, 72, \Imagick::FILTER_LANCZOS, 1);

        if ($newImage->getImageColorspace() == \Imagick::COLORSPACE_CMYK) {
            $newImage->transformimagecolorspace(\Imagick::COLORSPACE_SRGB);
        }

        $newImage->stripImage();

        $newImage->resizeImage($width, $height, \Imagick::FILTER_LANCZOS, 1);

        return $newImage;
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
        $newImage = clone $image;
        $newImage->cropImage($width, $height, $x, $y);

        return $newImage;
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
        return $image->getImageWidth();
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
        return $image->getImageHeight();
    }

    public function isValid($image)
    {
        return $image->valid();
    }

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
        if (!$image->valid()) {
            throw new PhotonException('UNEXPECTED_TYPE', ['expected' => 'Imagick']);
        }

        // set quality
        $quality = config("photon.imagick_quality");
        $image->setImageCompressionQuality($quality);

        // set sampling
        $sampling = config("photon.imagick_sampling");
        $image->setSamplingFactors([$sampling, "1x1", "1x1"]);

        \Storage::disk('assets')->put($pathAndName, $image);
    }
}
