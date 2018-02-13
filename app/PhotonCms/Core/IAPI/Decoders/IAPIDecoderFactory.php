<?php

namespace Photon\PhotonCms\Core\IAPI\Decoders;

use Photon\PhotonCms\Core\Exceptions\PhotonException;

class IAPIDecoderFactory
{
    /**
     * Location of decoder classes.
     *
     * @var string
     */
    private static $decoderLocation = '\Photon\PhotonCms\Core\IAPI\Decoders';

    /**
     * Creates an instance of the appropriate decoder based on decoder name and request method.
     *
     * @param string $decoderName
     * @param string $method
     * @return \Photon\PhotonCms\Core\IAPI\Contracts\IAPIDecoderInterface
     * @throws PhotonException
     */
    public static function make($decoderName, $method)
    {
        $methodName = ucfirst(strtolower($method));
        $decoderNamespace = self::$decoderLocation."\\{$methodName}\\".$decoderName;

        if (!class_exists($decoderNamespace)) {
            throw new PhotonException('IAPI_DECODER_DOESNT_EXIST', ['decoder' => $decoderName, 'method' => $method]);
        }

        return new $decoderNamespace();
    }
}