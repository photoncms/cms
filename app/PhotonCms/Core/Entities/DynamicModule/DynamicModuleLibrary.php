<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModule;

use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Photon\PhotonCms\Core\Response\ResponseRepository;
use Photon\PhotonCms\Core\Entities\Module\ModuleRepository;
use Photon\PhotonCms\Core\Entities\Module\Contracts\ModuleGatewayInterface;
use Photon\PhotonCms\Core\Entities\Model\ModelTemplateFactory;
use Photon\PhotonCms\Core\Entities\DynamicModule\DynamicModuleRepository;
use Photon\PhotonCms\Core\Entities\DynamicModule\DynamicModuleGatewayFactory;
use Photon\PhotonCms\Core\Entities\DynamicModule\DynamicModuleFactory;
use Photon\PhotonCms\Core\Entities\DynamicModule\DynamicModuleHelpers;
use Photon\PhotonCms\Core\Entities\FieldType\FieldTypeRepository;
use Photon\PhotonCms\Core\Entities\FieldType\FieldTypeGateway;

/**
 * Library for Dynamic Module entity.
 * This represents abstracts of redundant business logic. No model, storage or any other non-business logic implementation is allowed here!
 */
class DynamicModuleLibrary
{

    /**
     * @var ResponseRepository
     */
    private $responseRepository;

    /**
     * @var ModuleRepository
     */
    private $moduleRepository;

    /**
     * @var ModuleGatewayInterface
     */
    private $moduleGateway;

    /**
     * @var DynamicModuleRepository
     */
    private $dynamicModuleRepository;

    /**
     * @var DynamicModuleHelpers
     */
    private $dynamicModuleHelpers;

    /**
     * @var FieldTypeRepository
     */
    private $fieldTypeRepository;

    /**
     * @var FieldTypeGateway
     */
    private $fieldTypeGateway;
    
    /**
     * @param ResponseRepository $responseRepository
     * @param ModuleRepository $moduleRepository
     * @param ModuleGatewayInterface $moduleGateway
     * @param DynamicModuleRepository $dynamicModuleRepository
     * @param DynamicModuleHelpers $dynamicModuleHelpers
     * @param FieldTypeRepository $fieldTypeRepository
     * @param FieldTypeGateway $fieldTypeGateway
     */
    public function __construct(
        ResponseRepository $responseRepository,
        ModuleRepository $moduleRepository,
        ModuleGatewayInterface $moduleGateway,
        DynamicModuleRepository $dynamicModuleRepository,
        DynamicModuleHelpers $dynamicModuleHelpers,
        FieldTypeRepository $fieldTypeRepository,
        FieldTypeGateway $fieldTypeGateway
    )
    {
        $this->responseRepository      = $responseRepository;
        $this->moduleRepository        = $moduleRepository;
        $this->moduleGateway           = $moduleGateway;
        $this->dynamicModuleRepository = $dynamicModuleRepository;
        $this->dynamicModuleHelpers    = $dynamicModuleHelpers;
        $this->fieldTypeRepository      = $fieldTypeRepository;
        $this->fieldTypeGateway         = $fieldTypeGateway;
    }

    /**
     * Fetches a dynamic module item from the DB.
     *
     * @param string $tableName
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findEntryByTableNameAndId($tableName, $id)
    {
        $dynamicModuleGateway = $this->getGatewayInstanceByTableName($tableName);

        return $this->dynamicModuleRepository->findById($id, $dynamicModuleGateway);
    }

    /**
     * Fetches all dynamic module items from the DB.
     * Result is an array of \Illuminate\Database\Eloquent\Model extended by node and/or with regular dynamic module model.
     *
     * @param string $tableName
     * @param array $filter
     * @param array $pagination
     * @return array
     */
    public function getAllEntries($tableName, $filter = null, $pagination = null, $sorting = null)
    {
        $dynamicModuleGateway = $this->getGatewayInstanceByTableName($tableName);

        return $this->dynamicModuleRepository->getAll($dynamicModuleGateway, $filter, $pagination, $sorting);
    }

    /**
     * Prepares an instance of a dynamic module gateway based on the module table name.
     *
     * @param string $tableName
     * @return Contracts\DynamicModuleGatewayInterface
     * @throws PhotonException
     */
    public function getGatewayInstanceByTableName($tableName)
    {
        $module = $this->moduleRepository->findModuleByTableName($tableName, $this->moduleGateway);

        if (!$module) {
            throw new PhotonException('MODULE_NOT_FOUND', ['table_name' => $tableName]);
        }

        $modelTemplate = ModelTemplateFactory::makeDynamicModuleModelTemplate();
        $modelTemplate->setModelName($module->model_name);

        return DynamicModuleGatewayFactory::make($modelTemplate->getFullClassName(), $tableName);
    }

    /**
     * Returns the parent scope item if one is found.
     *
     * IMPORTANT: table name parameter represents the table where the child item comes from, and the ID represents the scope parent id.
     *
     * @param string $childTableName
     * @param int $scopeItemId
     */
    public function getParentScopeItemByTableNameAndId($childTableName, $scopeItemId)
    {
        // Get the child module
        $childModule = $this->moduleRepository->findModuleByTableName($childTableName, $this->moduleGateway);

        if (!$childModule) {
            throw new PhotonException('MODULE_NOT_FOUND', ['table_name' => $childTableName]);
        }
        if (!$childModule->category) {
            throw new PhotonException('SCOPE_MODULE_NOT_FOUND', ['module_id' => $childModule->category]);
        }

        // Get the scope module
        $scopeModule = $this->moduleRepository->findById($childModule->category, $this->moduleGateway);

        if (!$scopeModule) {
            throw new PhotonException('SCOPE_MODULE_NOT_FOUND', ['module_id' => $childModule->category]);
        }

        // Get the dynamic module gateway instance
        $modelTemplate = ModelTemplateFactory::makeDynamicModuleModelTemplate();
        $modelTemplate->setModelName($scopeModule->model_name);

        $dynamicModuleGateway = DynamicModuleGatewayFactory::make($modelTemplate->getFullClassName(), $scopeModule->table_name);

        // Return the parent scope item
        return $this->dynamicModuleRepository->findById($scopeItemId, $dynamicModuleGateway);
    }

    /**
     * Updates anchor texts for all dynamic module entries of a specific dynamic module.
     * This is usefull when updating a relation and related anchor texts need an update.
     *
     * @param mixed $module
     */
    public function updateAllModuleAnchorTextsForEntries($module, $name)
    {
        if(!config('photon.mass_auto_update_anchor'))
            return false;

        $modelTemplate = ModelTemplateFactory::makeDynamicModuleModelTemplate();
        $modelTemplate->setModelName($module->model_name);

        $dynamicModuleGateway = DynamicModuleGatewayFactory::make($modelTemplate->getFullClassName(), $module->table_name);
        $dynamicModuleFactory = new DynamicModuleFactory($modelTemplate->getFullClassName());

        $allEntries = $this->dynamicModuleRepository->getAll($dynamicModuleGateway);
        if ($allEntries instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $allEntries = $allEntries->getCollection();
        }
        
        foreach ($allEntries as $entry) {
            $entry->{$name} = $this->dynamicModuleHelpers->generateAnchorTextFromItem($entry, $module->{$name});
            $this->dynamicModuleRepository->save($entry, $dynamicModuleGateway);
        }
    }

    /**
     * Updates anchor texts recursively through all related modules.
     *
     * @param int $moduleId
     */
    public function updateAnchorTextRecursivelyThroughRelations($moduleId, $name)
    {
        $module = $this->moduleRepository->findById($moduleId, $this->moduleGateway);

        $somehowRelatedModules = $this->findTargetingModulesByTargetIdRecursively($module->id);

        foreach ($somehowRelatedModules as $relatedModule) {
            if ($relatedModule->{$name}) {
                $this->updateAllModuleAnchorTextsForEntries($relatedModule, $name);
            }
        }
    }

    /**
     * Retrieves all modules recursively which are related to the specified module ID.
     * The function has prevention from infinite loop if it happens that two modules have relations to each other.
     *
     * @param int $id
     * @param mixed $resultingModules
     * @return array
     */
    public function findTargetingModulesByTargetIdRecursively($id, &$resultingModules = [])
    {
        $allModules = $this->moduleRepository->getAll($this->moduleGateway);

        // Preload all field types into the ORM for better efficiency
        $this->fieldTypeRepository->preloadAll($this->fieldTypeGateway);

        $matchedModules = [];
        $allModules->load('fields');
        foreach ($allModules as $module) {
            // Prevents infinite loop
            if ($module->id == $id || isset($resultingModules[$id])) {
                continue;
            }
            // Loops through a level
            foreach ($module->fields as $field) {
                $fieldType = $this->fieldTypeRepository->findById($field->type, $this->fieldTypeGateway);
                if (!$fieldType->isAttribute() && $fieldType->isRelation() && $field->related_module == $id) {
                    if (!isset($resultingModules[$module->id])) {
                        $matchedModules[$module->id] = $module;
                    }
                    continue 2;
                }
            }
        }

        $resultingModules = $matchedModules + $resultingModules;
        foreach ($matchedModules as $module) {
            $this->findTargetingModulesByTargetIdRecursively($module->id, $resultingModules);
        }

        return $resultingModules;
    }

    /**
     * Checks if an entry has relations from entries from another modules or itself.
     *
     * @param mixed $module
     * @param int $entryId
     * @return boolean
     */
    public function checkIfEntryHasRelations($module, $entryId)
    {
        $allModules = $this->moduleRepository->getAll($this->moduleGateway);

        // Preload all field types into the ORM for better efficiency
        $this->fieldTypeRepository->preloadAll($this->fieldTypeGateway);

        $allModules->load('fields');
        $matchedModules = [];
        foreach ($allModules as $singleModule) {
            foreach ($singleModule->fields as $field) {
                $fieldType = $this->fieldTypeRepository->findById($field->type, $this->fieldTypeGateway);
                if ($fieldType->isAttribute() && $fieldType->isRelation() && $field->related_module == $module->id) {
                    $matchedModules[$singleModule->id] = [
                        'module' => $singleModule,
                        'fieldName' => $field->relation_name
                    ];
                    continue 2;
                }
            }
        }

        foreach ($matchedModules as $matchedModule) {
            $dynamicModuleGateway = $this->getGatewayInstanceByTableName($matchedModule['module']->table_name);

            $entries = $this->dynamicModuleRepository->retrieveByRelationValue($matchedModule['fieldName'], $entryId, $dynamicModuleGateway);

            if (!$entries->isEmpty() && !($matchedModule['module']->table_name == "resized_images" && $module->table_name == "assets")) {
                return true;
            }
        }
        return false;

    }

    /**
     * Find related modules
     *
     * @param mixed $module
     * @return array
     */
    public function findRelatedModules($module)
    {
        $allModules = $this->moduleRepository->getAll($this->moduleGateway);

        // Preload all field types into the ORM for better efficiency
        $this->fieldTypeRepository->preloadAll($this->fieldTypeGateway);

        $allModules->load('fields');
        $matchedModules = [$module->table_name];
        foreach ($allModules as $singleModule) {
            foreach ($singleModule->fields as $field) {
                $fieldType = $this->fieldTypeRepository->findById($field->type, $this->fieldTypeGateway);
                if ($fieldType->isAttribute() && $fieldType->isRelation() && $field->related_module == $module->id) {
                    $matchedModules[] = $singleModule->table_name;
                    continue 2;
                }
            }
        }

        return $matchedModules;
    }

    /**
     * Updates anchor text for an entry.
     *
     * The method uses the entry to find the anchor text stub, compile it and save it into the entry.
     *
     * @param object $entry
     */
    public function updateEntryAnchorText($entry, $name)
    {
        $moduleLibrary = \App::make('\Photon\PhotonCms\Core\Entities\Module\ModuleLibrary');
        $entry->{$name} = $this->dynamicModuleHelpers->generateAnchorTextFromItem($entry, $moduleLibrary->getAnchorTextByEntryObject($entry));
        $entry->save();
    }
}