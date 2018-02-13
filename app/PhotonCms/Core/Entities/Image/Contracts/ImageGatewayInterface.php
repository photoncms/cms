<?php

namespace Photon\PhotonCms\Core\Entities\Image\Contracts;

interface ImageGatewayInterface
{
    /**
     * Resizes the image resource
     *
     * @param resource $image
     * @param int $width
     * @param int $height
     * @param const $mode
     * @return resource
     */
    public function resize($image, $width, $height, $mode = null);

    /**
     *
     * @param resource $image
     * @param int $x
     * @param int $y
     * @param int $width
     * @param int $height
     * @return resource
     */
    public function crop($image, $x, $y, $width, $height);

    /**
     * Reads the image width from the image resource.
     *
     * @param resource $image
     * @return int
     * @throws PhotonException
     */
    public function getWidth($image);

    /**
     * Reads the image height from the image resource.
     *
     * @param resource $image
     * @return int
     * @throws PhotonException
     */
    public function getHeight($image);

    /**
     * Saves the image data to a file.
     *
     * @param resource $image
     * @param string $pathAndName
     * @return boolean
     * @throws PhotonException
     */
    public function save($image, $pathAndName, $quality = null);

    public function isValid($image);
}