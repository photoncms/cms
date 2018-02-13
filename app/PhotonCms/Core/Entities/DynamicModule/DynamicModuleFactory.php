<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModule;

use Photon\PhotonCms\Core\Exceptions\PhotonException;

/**
 * Handles object manipulation.
 */
class DynamicModuleFactory
{
    /**
     * Class name with namespace of the dynamic module.
     *
     * @var string
     */
    private $modelClassName = '';

    /**
     * Factory constructor.
     *
     * $className providing the full name with the namespace of the class must be provided on factory constructor.
     *
     * @param string $className
     * @throws PhotonException
     */
    public function __construct($className)
    {
        if (class_exists($className)) {
            $this->modelClassName = $className;
        }
        else {
            throw new PhotonException('CLASS_DOESNT_EXIST', ['class' => $className]);
        }
    }

    /**
     * Makes an instance of a dynamic module.
     *
     * @param array $data
     * @return mixed
     */
    public function make(array $data)
    {
        $newEntry = new $this->modelClassName();
        $newEntry->setAll($data);
        return $newEntry;
    }

    /**
     * Makes an empty instance of an ORM Dynamic Module.
     *
     * This should never be persisted!
     *
     * @return \Photon\PhotonCms\Core\Entities\DynamicModule\Contracts\DynamicModuleInterface
     */
    public function makeEmpty()
    {
        $emptyEntry = new $this->modelClassName();
        $emptyEntry->isEmpty = true;

        return $emptyEntry;
    }
}