<?php

namespace Photon\PhotonCms\Core\IAPI\Contracts;

interface IAPIDecoderInterface
{
    /**
     * Decodes call chain request based on steps and method parameters.
     * Each decoder is manually programmed.
     *
     * @param array $steps
     * @param type $parameters
     */
    public function decode(array $steps, $parameters = []);
}