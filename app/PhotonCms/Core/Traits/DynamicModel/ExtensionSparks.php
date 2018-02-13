<?php

namespace Photon\PhotonCms\Core\Traits\DynamicModel;

use Photon\PhotonCms\Core\Exceptions\PhotonException;

trait ExtensionSparks
{

    /**
     * Cloned object before the event.
     *
     * @var object
     */
    public $cloneBefore = null;

    /**
     * Cloned object after the event.
     *
     * @var object
     */
    public $cloneAfter = null;

    /**
     * Cloned object before the event.
     *
     * @var object
     */
    public $cloneBeforeReposition = null;

    /**
     * Cloned object after the event.
     *
     * @var object
     */
    public $cloneAfterReposition = null;

    // Preparation of clones for pre and post save events
    public function prepareCloneBeforePersist()
    {
        $this->cloneBefore = clone $this;
    }

    /**
     * Prepares a clone after persisting.
     */
    public function prepareCloneAfterPersist()
    {
        $relations = $this->getRelations();

        $relationArray = [];
        foreach ($relations as $key => $relation) {
            $relationArray[] = $key;
        }
        
        $this->cloneAfter = $this->load($relationArray);
    }

    // Preparation of clones for pre and post save events
    public function prepareCloneBeforeReposition()
    {
        $this->cloneBeforeReposition = clone $this;
    }

    /**
     * Prepares a clone after persisting.
     */
    public function prepareCloneAfterReposition()
    {
        $relations = $this->getRelations();

        $relationArray = [];
        foreach ($relations as $key => $relation) {
            $relationArray[] = $key;
        }

        $this->cloneAfterReposition = $this->load($relationArray);
    }

    /**
     * Fire pre save events.
     */
    public function firePreSaveEvents(&$data)
    {
        $parsedClassName = explode('\\',get_class($this));
        $className = end($parsedClassName);

        $extensionsClass = \Config::get('photon.dynamic_module_extensions_namespace').'\\'.$className."ModuleExtensions";

        if ($this->cloneBefore) {
            if (
                class_exists($extensionsClass) &&
                in_array('Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHandlesPreUpdate', class_implements($extensionsClass))
            ) {
                $extensionsInstance = \App::make($extensionsClass);
                $extensionsInstance->setRequestData($data);
                $extensionsInstance->preUpdate($this, $this->cloneBefore, $this->cloneAfter);
            }
        }
        else {
            if (
                class_exists($extensionsClass) &&
                in_array('Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHandlesPreCreate', class_implements($extensionsClass))
            ) {
                $extensionsInstance = \App::make($extensionsClass);
                $extensionsInstance->setRequestData($data);
                $extensionsInstance->preCreate($this, $this->cloneAfter);
            }
        }
    }

    /**
     * Fire post save events.
     */
    public function firePostSaveEvents(&$data)
    {
        $parsedClassName = explode('\\',get_class($this));
        $className = end($parsedClassName);

        $extensionsClass = \Config::get('photon.dynamic_module_extensions_namespace').'\\'.$className."ModuleExtensions";

        if ($this->cloneBefore) {
            if (
                class_exists($extensionsClass) &&
                in_array('Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHandlesPostUpdate', class_implements($extensionsClass))
            ) {
                $extensionsInstance = \App::make($extensionsClass);
                $extensionsInstance->setRequestData($data);
                $extensionsInstance->postUpdate($this, $this->cloneBefore, $this->cloneAfter);
            }
        }
        else {
            if (
                class_exists($extensionsClass) &&
                in_array('Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHandlesPostCreate', class_implements($extensionsClass))
            ) {
                $extensionsInstance = \App::make($extensionsClass);
                $extensionsInstance->setRequestData($data);
                $extensionsInstance->postCreate($this, $this->cloneAfter);
            }
        }
    }

    /**
     * Fires pre delete events.
     */
    public function firePreDeleteEvents()
    {
        $parsedClassName = explode('\\',get_class($this));
        $className = end($parsedClassName);

        $extensionsClass = \Config::get('photon.dynamic_module_extensions_namespace').'\\'.$className."ModuleExtensions";

        if (
            class_exists($extensionsClass) &&
            in_array('Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHandlesPreDelete', class_implements($extensionsClass))
        ) {
            $extensionsInstance = \App::make($extensionsClass);
            $extensionsInstance->preDelete($this, $this->cloneBefore);
        }
    }

    /**
     * Fires port delete events.
     */
    public function firePostDeleteEvents()
    {
        $parsedClassName = explode('\\',get_class($this));
        $className = end($parsedClassName);

        $extensionsClass = \Config::get('photon.dynamic_module_extensions_namespace').'\\'.$className."ModuleExtensions";

        if (
            class_exists($extensionsClass) &&
            in_array('Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHandlesPostDelete', class_implements($extensionsClass))
        ) {
            $extensionsInstance = \App::make($extensionsClass);
            $extensionsInstance->postDelete($this);
        }
    }

    /**
     * Fires pre retrieve events.
     */
    public function firePreRetrieveEvents(&$data)
    {
        $parsedClassName = explode('\\',get_class($this));
        $className = end($parsedClassName);

        $extensionsClass = \Config::get('photon.dynamic_module_extensions_namespace').'\\'.$className."ModuleExtensions";

        if (
            class_exists($extensionsClass) &&
            in_array('Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHandlesPreRetrieve', class_implements($extensionsClass))
        ) {
            $extensionsInstance = \App::make($extensionsClass);
            $extensionsInstance->setRequestData($data);
            $extensionsInstance->preRetrieve();
        }
    }

    /**
     * Fires post retrieve events.
     */
    public function firePostRetrieveEvents(&$data)
    {
        $parsedClassName = explode('\\',get_class($this));
        $className = end($parsedClassName);

        $extensionsClass = \Config::get('photon.dynamic_module_extensions_namespace').'\\'.$className."ModuleExtensions";

        if (
            class_exists($extensionsClass) &&
            in_array('Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHandlesPostRetrieve', class_implements($extensionsClass))
        ) {
            $extensionsInstance = \App::make($extensionsClass);
            $extensionsInstance->setRequestData($data);
            $extensionsInstance->postRetrieve($this);
        }
    }

    /**
     * Fires an extension action.
     *
     * @param string $action
     * @return \Illuminate\Http\Response
     * @throws PhotonException
     */
    public function fireAction($action, $parameters)
    {
        $parameters = explode('/',$parameters);
        $parsedClassName = explode('\\',get_class($this));
        $className = end($parsedClassName);

        $extensionsClass = \Config::get('photon.dynamic_module_extensions_namespace').'\\'.$className."ModuleExtensions";
        $methodName = 'call'.ucfirst($action);

        if (class_exists($extensionsClass)) {
            if (method_exists($extensionsClass, $methodName)) {
                $extensionsInstance = \App::make($extensionsClass);
                $allParameters = array_merge([$this], $parameters);
                return call_user_func_array(array($extensionsInstance, $methodName), $allParameters);
            }
            else {
                throw new PhotonException('MODULE_EXTENSION_ACTION_NOT_FOUND', ['action' => $action]);
            }
        }
        else {
            throw new PhotonException('MODULE_EXTENSION_CLASS_NOT_FOUND', ['class' => $extensionsClass]);
        }
    }

    /**
     * Gets compiled available extension functions.
     *
     * @return array
     */
    public function getAvailableExtensions()
    {
        $parsedClassName = explode('\\',get_class($this));
        $className = end($parsedClassName);

        $extensionsClass = \Config::get('photon.dynamic_module_extensions_namespace').'\\'.$className."ModuleExtensions";

        if (
            class_exists($extensionsClass) &&
            in_array('Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHasExtensionFunctions', class_implements($extensionsClass))
        ) {
            $extensionsInstance = \App::make($extensionsClass);
            return $extensionsInstance->getExtensionFunctionCalls($this);
        }
    }

    /**
     * Executes getter extension method.
     *
     * @return array
     */
    public function callAvailableMagicGetterExtension()
    {
        $parsedClassName = explode('\\',get_class($this));
        $className = end($parsedClassName);

        $extensionsClass = \Config::get('photon.dynamic_module_extensions_namespace').'\\'.$className."ModuleExtensions";

        if (
            class_exists($extensionsClass) &&
            in_array('Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHasGetterExtension', class_implements($extensionsClass))
        ) {
            $extensionsInstance = \App::make($extensionsClass);
            return $extensionsInstance->executeGetterExtension($this);
        }

        return [];
    }

    /**
     * Executes setter extension method.
     *
     * @return mixed
     */
    public function callAvailableMagicSetterExtension(&$data)
    {
        $parsedClassName = explode('\\',get_class($this));
        $className = end($parsedClassName);

        $extensionsClass = \Config::get('photon.dynamic_module_extensions_namespace').'\\'.$className."ModuleExtensions";

        if (
            class_exists($extensionsClass) &&
            in_array('Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHasSetterExtension', class_implements($extensionsClass))
        ) {
            $extensionsInstance = \App::make($extensionsClass);
            $extensionsInstance->setRequestData($data);
            return $extensionsInstance->executeSetterExtension($this);
        }
    }

    public function firePreNodeReposition()
    {
        $parsedClassName = explode('\\',get_class($this));
        $className = end($parsedClassName);

        $extensionsClass = \Config::get('photon.dynamic_module_extensions_namespace').'\\'.$className."ModuleExtensions";

        if (
            class_exists($extensionsClass) &&
            in_array('Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHandlesPreReposition', class_implements($extensionsClass))
        ) {
            $extensionsInstance = \App::make($extensionsClass);
            $extensionsInstance->preReposition($this, $this->cloneBeforeReposition, $this->cloneAfterReposition);
        }
    }

    public function firePostNodeReposition()
    {
        $parsedClassName = explode('\\',get_class($this));
        $className = end($parsedClassName);

        $extensionsClass = \Config::get('photon.dynamic_module_extensions_namespace').'\\'.$className."ModuleExtensions";

        if (
            class_exists($extensionsClass) &&
            in_array('Photon\PhotonCms\Core\Entities\DynamicModuleExtension\Contracts\ModuleExtensionHandlesPostReposition', class_implements($extensionsClass))
        ) {
            $extensionsInstance = \App::make($extensionsClass);
            $extensionsInstance->postReposition($this, $this->cloneBeforeReposition, $this->cloneAfterReposition);
        }
    }
}