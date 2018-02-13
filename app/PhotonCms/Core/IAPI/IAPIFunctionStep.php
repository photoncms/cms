<?php

namespace Photon\PhotonCms\Core\IAPI;

use Photon\PhotonCms\Core\IAPI\Contracts\IAPICallChainStepInterface;

class IAPIFunctionStep implements IAPICallChainStepInterface
{
    /**
     * Hardcoded identificator for this step type.
     *
     * @var string
     */
    private $type = 'function';

    /**
     * Name value of a call stack function call.
     * For example IAPI->[this will be the name for the step]()->...
     *
     * @var string
     */
    private $name;

    /**
     * Arguments of a call stack function call.
     * For example IAPI->something([these will be the arguments])->...
     *
     * @var array
     */
    private $arguments = [];

    /**
     * @param string $name
     * @param array $arguments
     */
    public function __construct($name, $arguments = [])
    {
        $this->name = $name;
        $this->arguments = $arguments;
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
     * Retrieves the called function name.
     * For example IAPI->[this will be the name for the step]()->...
     *
     * @var string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the count of submitted arguments to the call.
     *
     * @return int
     */
    public function countArguments()
    {
        return count($this->arguments);
    }

    /**
     * Retrieves arguments of this function call.
     * For example IAPI->something([these will be the arguments])->...
     *
     * @return type
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Retrieves the function argument under a specific index.
     * If the argument doesn't exist, null will be returned.
     *
     * @param int $index
     * @return mixed|null
     */
    public function getArgument($index)
    {
        return (key_exists($index, $this->arguments))
            ? $this->arguments[$index]
            : null;
    }
}