<?php

namespace Photon\PhotonCms\Core\IAPI\Contracts;

interface IAPICallChainStepInterface
{

    /**
     * Retrieves the hardcoded type for this step type.
     *
     * @return string
     */
    public function getType();

    /**
     * Retrieves the name of the step (name of the requested attribute or called function).
     *
     * @return string
     */
    public function getName();
}