<?php

namespace Photon\PhotonCms\Core\IAPI;

use Photon\PhotonCms\Core\IAPI\Contracts\IAPICallChainStepInterface;

class IAPIAttributeStep implements IAPICallChainStepInterface
{
    /**
     * Hardcoded identificator for this step type.
     *
     * @var string
     */
    private $type = 'attribute';

    /**
     * Name value of a call stack attribute request.
     * For example IAPI->[this will be the name for the step]->...
     *
     * @var string
     */
    private $name;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Retrieves the hardcoded type for this step type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Retrieves the requested attribute name.
     * For example IAPI->[this will be the name for the step]->...
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}