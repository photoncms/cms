<?php

namespace Photon\PhotonCms\Core\Entities\Image;
use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Photon\PhotonCms\Core\Entities\Image\Contracts\ImageGatewayInterface;

class ImageRepository
{
    /**
     * Rezises the image resource
     *
     * @param resource $image
     * @param int $width
     * @param int $height
     * @param type $imageGateway
     * @return type
     * @throws PhotonException
     */
    public function resizeImage($image, $width, $height, ImageGatewayInterface $imageGateway)
    {
        if (!$imageGateway->isValid($image)) {
            throw new PhotonException('UNEXPECTED_TYPE', ['expected' => 'resource']);
        }

        return $imageGateway->resize($image, $width, $height);
    }

    /**
     * Crops the image resource
     *
     * @param resource $image
     * @param int $x
     * @param int $y
     * @param int $width
     * @param int $height
     * @param ImageGatewayInterface $imageGateway
     * @return resource
     * @throws PhotonException
     */
    public function cropImage($image, $x, $y, $width, $height, ImageGatewayInterface $imageGateway)
    {
        if (!$imageGateway->isValid($image)) {
            throw new PhotonException('UNEXPECTED_TYPE', ['expected' => 'resource']);
        }
        
        return $imageGateway->crop($image, $x, $y, $width, $height);
    }

    /**
     * Crops and resizes the image resource.
     *
     * @param resource $image
     * @param int $selectionX
     * @param int $selectionY
     * @param int $selectionWidth
     * @param int $selectionHeight
     * @param int $finalWidth
     * @param int $finalHeight
     * @param ImageGatewayInterface $imageGateway
     * @return resource
     */
    public function cropAndResize($image, $selectionX, $selectionY, $selectionWidth, $selectionHeight, $finalWidth, $finalHeight, ImageGatewayInterface $imageGateway)
    {
        $croppedImage = $this->cropImage(
            $image,
            $selectionX,
            $selectionY,
            $selectionWidth,
            $selectionHeight,
            $imageGateway
        );
        $scaledImage = $this->resizeImage(
            $croppedImage,
            $finalWidth,
            $finalHeight,
            $imageGateway
        );

        return $scaledImage;
    }

    /**
     * Saves the image resource into a file.
     *
     * @param resource $image
     * @param string $pathAndName
     * @param ImageGatewayInterface $imageGateway
     * @throws PhotonException
     */
    public function saveImage($image, $pathAndName, ImageGatewayInterface $imageGateway)
    {
        if (!$imageGateway->isValid($image)) {
            throw new PhotonException('UNEXPECTED_TYPE', ['expected' => 'resource']);
        }
        
        $imageGateway->save(
            $image,
            $pathAndName
        );
    }

    /**
     * Reads the image resource canvas width.
     *
     * @param resource $image
     * @param ImageGatewayInterface $imageGateway
     * @return int
     * @throws PhotonException
     */
    public function getImageWidth($image, ImageGatewayInterface $imageGateway)
    {
        if (!$imageGateway->isValid($image)) {
            throw new PhotonException('UNEXPECTED_TYPE', ['expected' => 'resource']);
        }

        return $imageGateway->getWidth($image);
    }

    /**
     * Reads the image resource canvas height.
     *
     * @param resource $image
     * @param ImageGatewayInterface $imageGateway
     * @return resource
     * @throws PhotonException
     */
    public function getImageHeight($image, ImageGatewayInterface $imageGateway)
    {
        if (!$imageGateway->isValid($image)) {
            throw new PhotonException('UNEXPECTED_TYPE', ['expected' => 'resource']);
        }

        return $imageGateway->getHeight($image);
    }

    /**
     * Calculates the image scaled dimensions depending on image input and expected output size values.
     * Result indicates to which size the image should be resized so that
     * cropping to the expected size can be done within the image boundaries.
     *
     * @param int $inWidth
     * @param int $inHeight
     * @param int $outWidth
     * @param int $outHeight
     * @return array
     */
    public function calculateResize($inWidth, $inHeight, $outWidth, $outHeight)
    {
        $widthAspect = $inWidth/$outWidth;
        $heightAspect = $inHeight/$outHeight;

        $supposedWidth = $outWidth;
        $supposedHeight = $outHeight;
        
        if ($inWidth !== $outWidth) {
            $supposedHeight = $inHeight/$widthAspect;
            if ($supposedHeight < $outHeight) {
                $supposedHeight = $outHeight;
                $supposedWidth = $inWidth/$heightAspect;
            }
        }

        if ($inHeight !== $outHeight) {
            $supposedWidth = $inWidth/$heightAspect;
            if ($supposedWidth < $outWidth) {
                $supposedWidth = $outWidth;
                $supposedHeight = $inHeight/$widthAspect;
            }
        }
        $width = (int) floor($supposedWidth);
        $height = (int) floor($supposedHeight);

        return [$width, $height];
    }

    /**
     * Calculates a top left position for centered cropping based on the image size and the expected cropped size.
     *
     * @param int $inWidth
     * @param int $inHeight
     * @param int $outWidth
     * @param int $outHeight
     * @return array
     */
    public function calculateCropPosition($inWidth, $inHeight, $outWidth, $outHeight)
    {
        $x = 0;
        $y = 0;

        if ($inWidth > $outWidth) {
            $x = $inWidth/2 - $outWidth/2;
        }

        if ($inHeight > $outHeight) {
            $y = $inHeight/2 - $outHeight/2;
        }

        $x = (int) floor($x);
        $y = (int) floor($y);

        return [$x, $y];
    }
}