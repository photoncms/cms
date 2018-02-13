<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModule;

use Photon\PhotonCms\Core\Response\ResponseRepository;

class DynamicModuleInterrupter
{
    /**
     * @var ResponseRepository
     */
    private $responseRepository;

    /**
     * @param ResponseRepository $responseRepository
     */
    public function __construct(
        ResponseRepository $responseRepository
    )
    {
        $this->responseRepository = $responseRepository;
    }

    /**
     * Uses a Module Model class name to find the Module extension class if it exist.
     * If the found extension class supports the specific interface, it will be instanced
     * and interrupter method called.
     *
     * @param string $className
     * @return mixed
     */
    public function interruptCreate($className, &$data)
    {
        $originalClass = \Config::get('photon.dynamic_models_namespace').'\\'.$className;
        $extensionsClass = \Config::get('photon.dynamic_module_extensions_namespace').'\\'.$className."ModuleExtensions";

        if (
            class_exists($extensionsClass) &&
            in_array('Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionCanInterruptCreate', class_implements($extensionsClass))
        ) {
            $originalInstance = new $originalClass();
            $extensionsInstance = \App::make($extensionsClass);
            $extensionsInstance->setTableName($originalInstance->getTable());
            $extensionsInstance->setRequestData($data);
            return $extensionsInstance->interruptCreate();
        }
    }

    /**
     * Uses a Module Model class name to find the Module extension class if it exist.
     * If the found extension class supports the specific interface, it will be instanced
     * and interrupter method called.
     *
     * @param string $className
     * @param array $entries
     * @return mixed
     */
    public function interruptRetrieve($className, &$entries)
    {
        $originalClass   = \Config::get('photon.dynamic_models_namespace').'\\'.$className;
        $extensionsClass = \Config::get('photon.dynamic_module_extensions_namespace').'\\'.$className."ModuleExtensions";

        if (
            class_exists($extensionsClass) &&
            in_array('Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionCanInterruptRetrieve', class_implements($extensionsClass))
        ) {
            $originalInstance   = new $originalClass();
            $extensionsInstance = \App::make($extensionsClass);
            $extensionsInstance->setTableName($originalInstance->getTable());
            return $extensionsInstance->interruptRetrieve($entries);
        }
    }

    /**
     * Uses a Module Model class name to find the Module extension class if it exist.
     * If the found extension class supports the specific interface, it will be instanced
     * and interrupter method called.
     *
     * @param string $className
     * @param object $entry
     * @return mixed
     */
    public function interruptUpdate($className, $entry, &$data)
    {
        $originalClass   = \Config::get('photon.dynamic_models_namespace').'\\'.$className;
        $extensionsClass = \Config::get('photon.dynamic_module_extensions_namespace').'\\'.$className."ModuleExtensions";

        if (
            class_exists($extensionsClass) &&
            in_array('Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionCanInterruptUpdate', class_implements($extensionsClass))
        ) {
            $originalInstance   = new $originalClass();
            $extensionsInstance = \App::make($extensionsClass);
            $extensionsInstance->setTableName($originalInstance->getTable());
            $extensionsInstance->setRequestData($data);
            return $extensionsInstance->interruptUpdate($entry);
        }
    }

    /**
     * Uses a Module Model class name to find the Module extension class if it exist.
     * If the found extension class supports the specific interface, it will be instanced
     * and interrupter method called.
     *
     * @param string $className
     * @param object $entry
     * @return mixed
     */
    public function interruptDelete($className, $entry)
    {
        $originalClass   = \Config::get('photon.dynamic_models_namespace').'\\'.$className;
        $extensionsClass = \Config::get('photon.dynamic_module_extensions_namespace').'\\'.$className."ModuleExtensions";

        if (
            class_exists($extensionsClass) &&
            in_array('Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionCanInterruptDelete', class_implements($extensionsClass))
        ) {
            $originalInstance = new $originalClass();
            $extensionsInstance = \App::make($extensionsClass);
            $extensionsInstance->setTableName($originalInstance->getTable());
            return $extensionsInstance->interruptDelete($entry);
        }
    }
}

