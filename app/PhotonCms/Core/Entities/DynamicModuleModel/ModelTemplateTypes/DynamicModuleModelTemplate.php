<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleModel\ModelTemplateTypes;

use Config;
use Photon\PhotonCms\Core\Entities\Model\ModelTemplate;
use Photon\PhotonCms\Core\Helpers\ClassNameHelper;
use Photon\PhotonCms\Core\Entities\FieldType\FieldTypeFactory;

class DynamicModuleModelTemplate extends ModelTemplate
{

    /**
     * Flag which determines if the model class represented by this template should have built in getters and setters.
     *
     * @var boolean
     */
    protected $useGettersAndSetters = true;

    /**
     * Sets up namespace, class usage and interfaces for dynamic module model class.
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->setNamespace(Config::get('photon.dynamic_models_namespace'));
        $this->assignUse('Photon\PhotonCms\Core\Entities\DynamicModuleField\FieldTransformationController');
        $this->addImplementation('Photon\PhotonCms\Core\Entities\DynamicModule\Contracts\DynamicModuleInterface');
        $this->assignTrait('Photon\PhotonCms\Core\Traits\DynamicModel\ExtensionSparks');
    }

    /**
     * Generates the body of a field setter code.
     *
     * @param string $attributeName
     * @return string
     */
    public function getAttributeSetterCode($attributeName)
    {
        if (isset($this->attributes[$attributeName])) {
            return 'FieldTransformationController::input($this, \''.$attributeName.'\', $'.$attributeName.', '.$this->attributes[$attributeName]->getFieldType()->id.');';
        }
        elseif (key_exists($attributeName, $this->relations)) {
            return 'FieldTransformationController::input($this, \''.$attributeName.'\', $'.$attributeName.', '.$this->relations[$attributeName]->getFieldType()->id.');';
        }
    }

    /**
     * Generates the body of a field getter code.
     *
     * @param string $attributeName
     * @return string
     */
    public function getAttributeGetterCode($attributeName)
    {
        if (isset($this->attributes[$attributeName])) {
            return 'return FieldTransformationController::output($this, \''.$attributeName.'\', '.$this->attributes[$attributeName]->getFieldType()->id.');';
        }
        elseif (key_exists($attributeName, $this->relations)) {
            return 'return FieldTransformationController::output($this, \''.$attributeName.'\', '.$this->relations[$attributeName]->getFieldType()->id.');';
        }
    }
}