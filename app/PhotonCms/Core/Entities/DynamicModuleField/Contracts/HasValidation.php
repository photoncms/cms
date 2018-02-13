<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleField\Contracts;

/**
 * Segregated interface used to point out that the class which implements it will be able to provide a validation string for that specific field type.
 */
interface HasValidation
{
    /**
     * Returns a Laravel-based validation string
     *
     * @return string
     */
    public function getValidationString();
}