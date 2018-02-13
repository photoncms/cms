<?php

namespace Photon\PhotonCms\Core\Entities\Model;

use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Photon\PhotonCms\Core\Entities\Model\Contracts\ModelTemplateInterface;
use Photon\PhotonCms\Core\Entities\Model\Contracts\ModelCompilerInterface;
use Photon\PhotonCms\Core\Entities\Model\Contracts\ModelGatewayInterface;
use Photon\PhotonCms\Core\Entities\Model\ModelTemplateFactory;
use Photon\PhotonCms\Core\Entities\ModelAttribute\ModelAttributeFactory;
use Photon\PhotonCms\Core\Entities\ModelRelation\ModelRelationFactory;
use Photon\PhotonCms\Core\Entities\Module\ModuleLibrary;
use Photon\PhotonCms\Core\Entities\Module\Module;
use Photon\PhotonCms\Core\Entities\ModelMetaType\ModelMetaType;

class ModelRepository
{
    private $moduleLibrary;

    public function __construct(
        ModuleLibrary $moduleLibrary
    )
    {
        $this->moduleLibrary = $moduleLibrary;
    }

    /**
     * Saves class content to a file from template.
     *
     * @param NativeClassTemplateInterface $classTemplate
     * @param NativeClassGatewayInterface $classGateway
     */
    public function saveFromTemplate(ModelTemplateInterface $template, ModelCompilerInterface $compiler, ModelGatewayInterface $gateway)
    {
        $content = $compiler->compile($template);

        return $gateway->persistFromTemplate($content, $template);
    }

    /**
     * Deletes a class file using a NativeClassTemplate instance
     *
     * @param NativeClassTemplateInterface $classTemplate
     * @param NativeClassGatewayInterface $classGateway
     * @return boolean
     */
    public function deleteFromTemplate (ModelTemplateInterface $template, ModelGatewayInterface $gateway)
    {
        return $gateway->deleteFromTemplate($template);
    }

    /**
     * Deletes a class file by its class name.
     * This assumes that the class file was created under identical class name.
     *
     * @param string $name
     * @param string $path
     * @param NativeClassGatewayInterface $classGateway
     * @return boolean
     */
    public function deleteClassByName($name, ModelGatewayInterface $gateway)
    {
        return $gateway->deleteClassByName($name);
    }

    public function rebuildModel(Module $module, ModelCompilerInterface $compiler, ModelGatewayInterface $gateway)
    {
        $module = $module->fresh('fields');
        $modelAttributes = ModelAttributeFactory::makeMultipleFromFields($module->fields);
        $modelRelations = ModelRelationFactory::makeMultipleFromFields($module->fields);

        $modelTemplate = ModelTemplateFactory::makeByType($module->type);
        $modelTemplate->setTableName($module->table_name);
        $modelTemplate->setModelName($module->model_name);
        $modelTemplate->addAttributes($modelAttributes);
        $modelTemplate->addRelations($modelRelations);

        $modelMetaData = $module->modelMetaData;
        if (!$modelMetaData->isEmpty()) {
            foreach ($modelMetaData as $modelMeta) {
                if ($modelMeta->metaType && $modelMeta->metaType instanceof ModelMetaType) {
                    switch ($modelMeta->metaType->system_name) {
                        case 'use':
                            $modelTemplate->assignUse($modelMeta->value);
                            break;
                        case 'trait':
                            $modelTemplate->assignTrait($modelMeta->value);
                            break;
                        case 'extend':
                            $modelTemplate->setInheritance($modelMeta->value);
                            break;
                        case 'implement':
                            $modelTemplate->addImplementation($modelMeta->value);
                            break;
                        default :
                            throw new PhotonException('INVALID_MODEL_META_TYPE', ['type' => $modelMeta->metaType->system_name]);
                    }
                }
            }
        }

        $this->saveFromTemplate($modelTemplate, $compiler, $gateway);
    }

    public function rebuildAllModels(ModelCompilerInterface $compiler, ModelGatewayInterface $gateway)
    {
        $modules = $this->moduleLibrary->getAllModules();

        foreach ($modules as $module) {
            $this->rebuildModel($module, $compiler, $gateway);
        }
    }
}