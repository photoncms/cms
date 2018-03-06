<?php

namespace Photon\PhotonCms\Core\Controllers;

use Illuminate\Http\Response;
use Photon\Http\Controllers\Controller;
use Photon\PhotonCms\Core\Response\ResponseRepository;
use Photon\PhotonCms\Core\Transaction\TransactionFactory;
use Photon\PhotonCms\Core\Entities\Module\ModuleRepository;
use Photon\PhotonCms\Core\Entities\Module\Contracts\ModuleGatewayInterface;
use Photon\PhotonCms\Core\Entities\Model\ModelTemplateFactory;
use Photon\PhotonCms\Core\Entities\DynamicModule\DynamicModuleRepository;
use Photon\PhotonCms\Core\Entities\DynamicModule\DynamicModuleHelpers;
use Photon\PhotonCms\Core\Entities\Module\ModuleHelpers;
use Photon\PhotonCms\Core\Exceptions\PhotonException;

use Photon\PhotonCms\Core\Entities\DynamicModule\DynamicModuleFactory;
use Photon\PhotonCms\Core\Entities\DynamicModule\DynamicModuleGatewayFactory;
use Photon\PhotonCms\Core\Entities\DynamicModule\DynamicModuleLibrary;
use Photon\PhotonCms\Core\Entities\Node\NodeRepository;
use Photon\PhotonCms\Core\Entities\Node\NodeLibrary;
use Photon\PhotonCms\Core\Entities\Field\FieldRepository;
use Photon\PhotonCms\Core\Entities\Field\Contracts\FieldGatewayInterface;
use Photon\PhotonCms\Core\Entities\FieldType\FieldTypeRepository;
use Photon\PhotonCms\Core\Entities\FieldType\FieldTypeGateway;
use Photon\PhotonCms\Core\Entities\DynamicModuleField\Contracts\HasValidation;

use Photon\PhotonCms\Core\PermissionServices\PermissionChecker;
use Photon\PhotonCms\Core\Entities\DynamicModuleExporter\DynamicModuleExporterFactory;
use Photon\PhotonCms\Core\Entities\DynamicModuleSubscriber\DynamicModuleSubscriberFactory;
use Photon\PhotonCms\Core\Entities\MenuItem\MenuItem;
use Illuminate\Support\Facades\Cache;
use Photon\PhotonCms\Core\PermissionServices\PermissionHelper;

class DynamicModuleController extends Controller
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
     * @var FieldRepository
     */
    private $fieldRepository;

    /**
     * @var FieldGatewayInterface
     */
    private $fieldGateway;

    /**
     * @var FieldTypeRepository
     */
    private $fieldTypeRepository;

    /**
     * @var FieldTypeGateway
     */
    private $fieldTypeGateway;

    /**
     * @var DynamicModuleLibrary
     */
    private $dynamicModuleLibrary;

    /**
     * @var NodeRepository
     */
    private $nodeRepository;

    /**
     * @var NodeLibrary
     */
    private $nodeLibrary;

    /**
     * @var \Photon\PhotonCms\Core\Entities\DynamicModule\DynamicModuleInterrupter
     */
    private $interrupter;

    /**
     * Controller construcor.
     *
     * @param ResponseRepository $responseRepository
     * @param ModuleRepository $moduleRepository
     * @param ModuleGatewayInterface $moduleGateway
     * @param DynamicModuleRepository $dynamicModuleRepository
     * @param DynamicModuleHelpers $dynamicModuleHelpers
     * @param FieldRepository $fieldRepository
     * @param FieldGatewayInterface $fieldGateway
     * @param FieldTypeRepository $fieldTypeRepository
     * @param FieldTypeGateway $fieldTypeGateway
     * @param DynamicModuleLibrary $dynamicModuleLibrary
     * @param NodeRepository $nodeRepository
     */
    public function __construct(
        ResponseRepository $responseRepository,
        ModuleRepository $moduleRepository,
        ModuleGatewayInterface $moduleGateway,
        DynamicModuleRepository $dynamicModuleRepository,
        DynamicModuleHelpers $dynamicModuleHelpers,
        FieldRepository $fieldRepository,
        FieldGatewayInterface $fieldGateway,
        FieldTypeRepository $fieldTypeRepository,
        FieldTypeGateway $fieldTypeGateway,
        DynamicModuleLibrary $dynamicModuleLibrary,
        NodeRepository $nodeRepository,
        NodeLibrary $nodeLibrary
    )
    {
        $this->responseRepository      = $responseRepository;
        $this->moduleRepository        = $moduleRepository;
        $this->moduleGateway           = $moduleGateway;
        $this->dynamicModuleRepository = $dynamicModuleRepository;
        $this->dynamicModuleHelpers    = $dynamicModuleHelpers;
        $this->fieldRepository         = $fieldRepository;
        $this->fieldGateway            = $fieldGateway;
        $this->fieldTypeRepository     = $fieldTypeRepository;
        $this->fieldTypeGateway        = $fieldTypeGateway;
        $this->dynamicModuleLibrary    = $dynamicModuleLibrary;
        $this->nodeRepository          = $nodeRepository;
        $this->nodeLibrary             = $nodeLibrary;
        $this->interrupter             = \App::make('\Photon\PhotonCms\Core\Entities\DynamicModule\DynamicModuleInterrupter');
    }

    /**
     * Retrieves all dynamic modules entries.
     * Landing method for HTTP request.
     *
     * @param string $tableName
     * @return \Illuminate\Http\Response
     */
    public function getAllEntries($tableName)
    {
        $filter = \Request::get('filter');
        $sorting = \Request::get('sorting');
        $pagination = \Request::get('pagination');

        $result = $this->getAllDynamicModuleEntries($tableName, $filter, $sorting, $pagination);

        return $this->responseRepository->make('LOAD_DYNAMIC_MODULE_ENTRIES_SUCCESS', $result);
    }

    /**
     * Retrieves all dynamic modules entries.
     *
     * @param string $tableName
     * @return \Illuminate\Http\Response
     */
    public function getAllDynamicModuleEntries($tableName, $filter = null, $sorting = null, $pagination = null)
    {
        if(config("photon.use_photon_cache")) {
            $user = \Auth::user();
            $permissions = PermissionHelper::getCurrentUserPermissions(); 

            $cacheKeyName = json_encode([
                "tableName"     => $tableName, 
                "user"          => $user->id, 
                "filter"        => $filter, 
                "sorting"       => $sorting, 
                "pagination"    => $pagination, 
                "permissions"   => $permissions
            ]);
            
            if(Cache::tags([env("APPLICATION_URL"), $tableName])->has($cacheKeyName)) {
                return Cache::tags([env("APPLICATION_URL"), $tableName])->get($cacheKeyName);
            }
        }

        $entries = $this->dynamicModuleLibrary->getAllEntries($tableName, $filter, $pagination, $sorting);

        $result = [];
        if ($entries instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $result['pagination'] = $entries;
            $entries = $entries->getCollection();
        }

        $module = $this->moduleRepository->findModuleByTableName($tableName, $this->moduleGateway);

        if (!$module) {
            throw new PhotonException('MODULE_NOT_FOUND', ['table_name' => $tableName]);
        }

        // Perform any necessary functionality over retrieved entries before returning the result.
        $this->interrupter->interruptRetrieve($module->model_name, $entries);

        $result['entries'] = $entries;

        if(config("photon.use_photon_cache")) {
            Cache::tags([env("APPLICATION_URL"), $tableName])->put($cacheKeyName, $result, config("photon.photon_caching_time"));
        }
        return $result;
    }

    /**
     * Retrieves a dynamic module entry by its ID.
     * Landing method for HTTP request.
     *
     * @param string $tableName
     * @param int $entryId
     * @return \Illuminate\Http\Response
     * @throws PhotonException
     */
    public function getEntry($tableName, $entryId)
    {
        $entry = $this->getDynamicModuleEntry($tableName, $entryId);

        if (!$entry) {
            throw new PhotonException('DYNAMIC_MODULE_ENTRY_NOT_FOUND', ['id' => $entryId]);
        }

        //ToDo: Check what's up with this (Sasa|03/2017)

//        // Check if this user is allowed to retrieve this entry.
//        if (!PermissionChecker::canCRUDRetrieveMatchingModuleEntry($tableName, $entry)) {
//            return $this->responseRepository->make('INSUFICIENT_PERMISSIONS', ['cannot_see' => $entryId]);
//        }

        $entry->showRelations();

        return $this->responseRepository->make('LOAD_DYNAMIC_MODULE_ENTRY_SUCCESS', ['entry' => $entry]);
    }

    /**
     * Retrieves a dynamic module entry by its ID.
     *
     * @param string $tableName
     * @param int $entryId
     * @return \Illuminate\Http\Response
     * @throws PhotonException
     */
    public function getDynamicModuleEntry($tableName, $entryId)
    {
        if(config("photon.use_photon_cache")) {
            if(Cache::tags([env("APPLICATION_URL"), $tableName])->has($tableName . ":" . $entryId)) {
                return Cache::tags([env("APPLICATION_URL"), $tableName])->get($tableName . ":" . $entryId);
            }
        }

        $entry = $this->dynamicModuleLibrary->findEntryByTableNameAndId($tableName, $entryId);

        //ToDo: Check what's up with this (Sasa|03/2017)

//        // Check if this user is allowed to retrieve this entry.
//        if (!PermissionChecker::canCRUDRetrieveMatchingModuleEntry($tableName, $entry)) {
//            return $this->responseRepository->make('INSUFICIENT_PERMISSIONS', ['cannot_see' => $entryId]);
//        }
        if(config("photon.use_photon_cache")) {
            Cache::tags([env("APPLICATION_URL"), $tableName])->put($tableName . ":" . $entry->id, $entry, config("photon.photon_caching_time"));
        }

        return $entry;
    }

    /**
     * Accepts a request from the route and prepares it for dynamic module entry creation.
     * Landing method for HTTP request.
     *
     * @param string $tableName
     * @return \Illuminate\Http\Response
     */
    public function insertEntry($tableName)
    {
        $data = \Request::all();

        $entry = $this->insertDynamicModuleEntry($tableName, $data);
        $entry->showRelations();

        return $this->responseRepository->make('SAVE_DYNAMIC_MODULE_ENTRY_SUCCESS', ['entry' => $entry]);
    }

    /**
     * Inserts a new dynamic module entry.
     * Call this method when creating an entry from within the application and not from a request.
     *
     * @param string $tableName
     * @return \Illuminate\Http\Response
     */
    public function insertDynamicModuleEntry($tableName, $data)
    {
        $transactionController = TransactionFactory::make('INSERT_ENTRY');

        $module = $this->moduleRepository->findModuleByTableName($tableName, $this->moduleGateway);

        if (!$module) {
            return $this->responseRepository->make('MODULE_NOT_FOUND', ['table_name' => $tableName]);
        }

        $fields = $this->fieldRepository->findByModuleId($module->id, $this->fieldGateway);

        // Regular user-defined validation
        $validationRules = [];
        foreach ($fields as $field) {
            $fieldType = $this->fieldTypeRepository->findById($field['type'], $this->fieldTypeGateway);
            if ($field->validation_rules) {
                $validationRules[$field->getUniqueName()] = $field->validation_rules;
            }
        }
        
        $validator = \Validator::make($data, $validationRules);

        if ($validator->fails()) {
            throw new PhotonException('VALIDATION_ERROR', ['error_fields' => $validator]);
        }

        // Field type built in validation
        $baseValidationRules = [];
        foreach ($fields as $field) {
            $fieldType = $this->fieldTypeRepository->findById($field['type'], $this->fieldTypeGateway);
            if ($fieldType instanceof HasValidation) {
                $baseValidationRules[
                    ($fieldType->isRelation()) ? $field->relation_name : $field->column_name
                ] = $fieldType->getValidationString();
            }
        }

        $baseValidator = \Validator::make($data, $baseValidationRules);

        if ($baseValidator->fails()) {
            throw new PhotonException('VALIDATION_ERROR', ['error_fields' => $baseValidator]);
        }

        // if field is not set in request and it's default value is not null populate request 
        foreach ($fields as $key => $field) {
            // if field is virtual no need to set up default value
            if($field->virtual)
                continue;

            // if field's default value is null no need to set it up
            if(is_null($field->default))
                continue;

            // set default value based on relation_name
            if(!is_null($field->relation_name) && !isset($data[$field->relation_name]))
                $data[$field->relation_name] = $field->default;

            // set default value based on column_name
            if(!is_null($field->column_name) && !isset($data[$field->column_name]))
                $data[$field->column_name] = $field->default;
        }

        // Perform any necesary custom functionality before inserting the entry.
        $this->interrupter->interruptCreate($module->model_name, $data);

        $modelTemplate = ModelTemplateFactory::makeDynamicModuleModelTemplate();
        $modelTemplate->setModelName($module->model_name);

        $dynamicModuleGateway = DynamicModuleGatewayFactory::make($modelTemplate->getFullClassName(), $module->table_name);
        $dynamicModuleFactory = new DynamicModuleFactory($modelTemplate->getFullClassName());

        // Create the entry
        $entry = null;
        $transactionController->queue(
            function () use (&$entry, $dynamicModuleGateway, $dynamicModuleFactory, $data) {
                $entry = $this->dynamicModuleRepository->saveFromData($data, $dynamicModuleGateway, $dynamicModuleFactory);
            },
            function () use (&$entry, $dynamicModuleGateway) {
                $this->dynamicModuleRepository->delete($entry, $dynamicModuleGateway);
            },
            'ENTRY_INSERT',
            'ENTRY_INSERT_ROLLBACK'
        );

        // set created by and update by
        $transactionController->queue(
            function () use ($module, &$entry, $dynamicModuleGateway, $data) {
                $user = \Auth::user();
                $entry->created_by = isset($data['created_by']) ? $data['created_by'] : $user->id;
                $entry->updated_by = isset($data['updated_by']) ? $data['updated_by'] : $user->id;
                $this->dynamicModuleRepository->save($entry, $dynamicModuleGateway);
            },
            null,
            'CREATED_AND_UPDATED_BY_COMPILING'
        );

        // check slug if needed
        if (config('photon.use_slugs') && $module->slug) {
            $slugValidationRules = [
                "slug" => "required|unique:" . $module->table_name . ",slug"
            ];

            $slugValidator = \Validator::make($data, $slugValidationRules);

            if ($slugValidator->fails()) {
                throw new PhotonException('VALIDATION_ERROR', ['error_fields' => $slugValidator]);
            }
            $transactionController->queue(
                function () use ($data, &$entry, $dynamicModuleGateway) {
                    $entry->slug = $data['slug'];
                    $this->dynamicModuleRepository->save($entry, $dynamicModuleGateway);
                },
                null,
                'SLUG_COMPILING'
            );
        }

        // Compile anchor text if necesary
        $transactionController->queue(
            function () use ($module, &$entry, $dynamicModuleGateway) {
                if ($module->anchor_text) {
                    $entry->anchor_text = $this->dynamicModuleHelpers->generateAnchorTextFromItem($entry, $module->anchor_text);
                    $this->dynamicModuleRepository->save($entry, $dynamicModuleGateway);
                }
                else {
                    $entry->anchor_text = $entry->id;
                    $this->dynamicModuleRepository->save($entry, $dynamicModuleGateway);
                }
            },
            null,
            'ANCHOR_TEXT_COMPILING'
        );

        // Compile anchor html if necesary
        $transactionController->queue(
            function () use ($module, &$entry, $dynamicModuleGateway) {
                if ($module->anchor_html) {
                    $entry->anchor_html = $this->dynamicModuleHelpers->generateAnchorTextFromItem($entry, $module->anchor_html);
                    $this->dynamicModuleRepository->save($entry, $dynamicModuleGateway);
                }
            },
            null,
            'ANCHOR_HTML_COMPILING'
        );   

        // Set scope if requested
        $transactionController->queue(
            function () use ($tableName, &$entry, $data) {
                if (isset($data['scope_id']) && is_numeric($data['scope_id'])) {
                    $scopeNode = $this->dynamicModuleLibrary->getParentScopeItemByTableNameAndId($tableName, $data['scope_id']);

                    if (!$scopeNode) {
                        throw new PhotonException('SCOPE_NODE_NOT_FOUND', ['id' => $data['scope_id']]);
                    }
                    $entry = $this->nodeRepository->performNodeAction($entry, 'setScope', $scopeNode);
                }
            },
            null,
            'SETTING_SCOPE'
        );

        // Set parent if requested
        $transactionController->queue(
            function () use ($tableName, &$entry, $dynamicModuleGateway, $data) {
                if (isset($data['parent_id']) && is_numeric($data['parent_id'])) {
                    $parentNode = $this->dynamicModuleLibrary->findEntryByTableNameAndId($tableName, $data['parent_id']);

                    if (!$parentNode) {
                        throw new PhotonException('PARENT_NODE_NOT_FOUND', ['id' => $data['parent_id']]);
                    }

                    $entry->scope_id = $parentNode->scope_id;
                    $this->dynamicModuleRepository->save($entry, $dynamicModuleGateway);
                    $entry = $this->nodeRepository->performNodeAction($entry, 'makeLastChildOf', $parentNode);
                }
            }
        );

        // Perform transaction
        $transactionController->commit();

        if(config("photon.use_photon_cache")) {
            $relatedModules = $this->dynamicModuleLibrary->findRelatedModules($module);
            Cache::tags($relatedModules)->flush();

            Cache::tags([env("APPLICATION_URL"), $tableName])->put($tableName . ":" . $entry->id, $entry, config("photon.photon_caching_time"));
        }

        return $entry;
    }

    /**
     * Accepts a request from the route and prepares it for dynamic module entry update.
     * Landing method for HTTP request.
     *
     * @param string $tableName
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function updateEntry($tableName, $id)
    {
        $data = \Request::all();

        $entry = $this->updateDynamicModuleEntry($tableName, $id, $data);
        $entry->showRelations();

        return $this->responseRepository->make('SAVE_DYNAMIC_MODULE_ENTRY_SUCCESS', ['entry' => $entry]);
    }

    /**
     * Updates a dynamic module entry.
     * Call this method when updating an entry from within the application and not from a request.
     *
     * @param string $tableName
     * @return \Illuminate\Http\Response
     */
    public function updateDynamicModuleEntry($tableName, $id, $data)
    {
        $module = $this->moduleRepository->findModuleByTableName($tableName, $this->moduleGateway);

        if (!$module) {
            throw new PhotonException('MODULE_NOT_FOUND', ['table_name' => $tableName]);
        }

        $entry = $this->dynamicModuleLibrary->findEntryByTableNameAndId($tableName, $id);
        if (is_null($entry)) {
            throw new PhotonException('DYNAMIC_MODULE_ENTRY_NOT_FOUND', ['id' => $id]);
        }

        foreach($data as $key => $value) {
            if (!PermissionChecker::canEditModuleField($tableName, $key)) {
                throw new PhotonException('INSUFICIENT_PERMISSIONS', ['cannot_update' => $key]);
            }
        }

        $fields = $this->fieldRepository->findByModuleId($module->id, $this->fieldGateway);

        $data['id'] = $id;
        $user = \Auth::user();
        $data['updated_by'] = $user->id;
        // remove non editable data from request
        foreach ($fields as $field) {
            if (!$field->editable && array_key_exists($field->getUniqueName(), $data)) {
                unset($data[$field->getUniqueName()]);
            }
        }

        // Regular user-defined validation
        $validator = $this->makeUpdateClientFieldValidator($data, $fields);
        if ($validator->fails()) {
            throw new PhotonException('VALIDATION_ERROR', ['error_fields' => $validator]);
        }

        // Field type built in validation
        $baseValidator = $this->makeBaseFieldValidator($data, $fields);
        if ($baseValidator->fails()) {
            throw new PhotonException('VALIDATION_ERROR', ['error_fields' => $baseValidator]);
        }

        // check slug if needed
        if (config('photon.use_slugs') && $module->slug) {
            $slugValidationRules = [
                "slug" => \Illuminate\Validation\Rule::unique($tableName)->ignore($id)
            ];

            $slugValidator = \Validator::make($data, $slugValidationRules);

            if ($slugValidator->fails()) {
                throw new PhotonException('VALIDATION_ERROR', ['error_fields' => $slugValidator]);
            }
        }


        // Perform any necessary custom functionality before update takes place.
        $this->interrupter->interruptUpdate($module->model_name, $entry, $data);

        $entry = $this->updateEntryInModule($data, $module);

        $this->dynamicModuleLibrary->updateAnchorTextRecursivelyThroughRelations($module->id, 'anchor_text');
        $this->dynamicModuleLibrary->updateAnchorTextRecursivelyThroughRelations($module->id, 'anchor_html');

        // store in cache
        if(config("photon.use_photon_cache")) {
            $relatedModules = $this->dynamicModuleLibrary->findRelatedModules($module);
            Cache::tags($relatedModules)->flush(); 

            Cache::tags([env("APPLICATION_URL"), $tableName])->put($tableName . ":" . $entry->id, $entry, config("photon.photon_caching_time"));
        }

        return $entry;
    }

    /**
     * Performs a massive update over module items.
     * Landing method for HTTP request.
     *
     * @param string $tableName
     * @return \Illuminate\Http\Response
     */
    public function massUpdate($tableName)
    {
        if (env('MASS_EDITING_DISABLED', false)) {
            throw new PhotonException('PHOTON_MASS_EDITING_DISABLED');
        }

        $data = \Request::all();
        $filter = \Request::get('filter');

        list ($updatedEntries, $failedEntries) = $this->massUpdateDynamicModuleEntries($tableName, $data, $filter);

        return $this->responseRepository->make('MASS_UPDATE_DYNAMIC_MODULE_ENTRY_SUCCESS', ['updated_entries' => $updatedEntries, 'failed_entries' => $failedEntries]);
    }

    /**
     * Performs a massive update over module items.
     *
     * @param string $tableName
     * @return \Illuminate\Http\Response
     */
    public function massUpdateDynamicModuleEntries($tableName, array $data, $filter = null)
    {
        $module = $this->moduleRepository->findModuleByTableName($tableName, $this->moduleGateway);

        if (!$module) {
            throw new PhotonException('MODULE_NOT_FOUND', ['table_name' => $tableName]);
        }

        foreach($data as $key => $value) {
            if (!PermissionChecker::canEditModuleField($tableName, $key)) {
                throw new PhotonException('INSUFICIENT_PERMISSIONS', ['cannot_update' => $key]);
            }
        }

        $fields = $this->fieldRepository->findByModuleId($module->id, $this->fieldGateway);

        // remove non editable data from request
        foreach ($fields as $field) {
            if (!$field->editable && array_key_exists($field->getUniqueName(), $data)) {
                unset($data[$field->getUniqueName()]);
            }
        }

        // Regular user-defined validation
        $validator = $this->makeUpdateClientFieldValidator($data, $fields);
        if ($validator->fails()) {
            throw new PhotonException('VALIDATION_ERROR', ['error_fields' => $validator]);
        }

        // Field type built in validation
        $baseValidator = $this->makeBaseFieldValidator($data, $fields);
        if ($baseValidator->fails()) {
            throw new PhotonException('VALIDATION_ERROR', ['error_fields' => $baseValidator]);
        }

        $entries = $this->dynamicModuleLibrary->getAllEntries($tableName, $filter);
        if ($entries instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $entries = $entries->getCollection();
        }

        foreach ($entries as $entry) {
            $data['id'] = $entry->id;
            $user = \Auth::user();
            $data['updated_by'] = $user->id;

            // Perform any necessary custom functionality before update takes place.
            $this->interrupter->interruptUpdate($module->model_name, $entry, $data);
            unset($data['id']);
        }

        $updatedEntries = [];
        $failedEntries = [];
        foreach ($entries as $entry) {
            $updateData = $data;
            $updateData['id'] = $entry->id;
            $user = \Auth::user();
            $updateData['updated_by'] = $user->id;

            try {
                $updatedEntry = $this->updateEntryInModule($updateData, $module);
                $updatedEntry->showRelations();
                $updatedEntries[] = $updatedEntry;
            } catch (\Exception $ex) {
                $entry->showRelations();
                $failedEntries[] = $entry;
            }
        }

        $this->dynamicModuleLibrary->updateAnchorTextRecursivelyThroughRelations($module->id, 'anchor_text');
        $this->dynamicModuleLibrary->updateAnchorTextRecursivelyThroughRelations($module->id, 'anchor_html');

        if(config("photon.use_photon_cache")) {
            $relatedModules = $this->dynamicModuleLibrary->findRelatedModules($module);
            Cache::tags($relatedModules)->flush(); 
        }

        return [$updatedEntries, $failedEntries];
    }

    /**
     * Updates a module entry using data array.
     *
     * @param array $data
     * @param mixed $module
     * @return mixed
     */
    private function updateEntryInModule($data, $module)
    {
        $modelTemplate = ModelTemplateFactory::makeDynamicModuleModelTemplate();
        $modelTemplate->setModelName($module->model_name);

        $dynamicModuleGateway = DynamicModuleGatewayFactory::make($modelTemplate->getFullClassName(), $module->table_name);
        $dynamicModuleFactory = new DynamicModuleFactory($modelTemplate->getFullClassName());

        $updatedEntry = $this->dynamicModuleRepository->saveFromData($data, $dynamicModuleGateway, $dynamicModuleFactory);
        if ($module->anchor_text) {
            $updatedEntry->anchor_text = $this->dynamicModuleHelpers->generateAnchorTextFromItem($updatedEntry, $module->anchor_text);
            $this->dynamicModuleRepository->save($updatedEntry, $dynamicModuleGateway);
        }

        $updatedEntry->anchor_html = $this->dynamicModuleHelpers->generateAnchorTextFromItem($updatedEntry, $module->anchor_html);
        $this->dynamicModuleRepository->save($updatedEntry, $dynamicModuleGateway);
            
        if (config('photon.use_slugs') && $module->slug) {
            if(isset($data['slug'])) {
                $updatedEntry->slug = $data['slug'];
                $this->dynamicModuleRepository->save($updatedEntry, $dynamicModuleGateway);
            }
        }
        
        return $updatedEntry->fresh();
    }

    /**
     * Makes a validator for validating provided fields with provided data.
     *
     * @param array $data
     * @param array $fields
     * @return Validator
     */
    private function makeUpdateClientFieldValidator($data, $fields)
    {
        $validationRules = [];
        foreach ($fields as $field) {
            $fieldType = $this->fieldTypeRepository->findById($field['type'], $this->fieldTypeGateway);
            // We will go through this fields validation only if it has validation and if the field was really changed
            if ($field->validation_rules && array_key_exists($field->getUniqueName(), $data)) {
                // All custom validations are justified here except for required
                // Any rule with a keyword 'required' (case-insensitive) will be removed.
                $validationStringArray = explode('|', $field->validation_rules);

                // Laravel Min and Size rule don't work properly for empty values.
                // In this case, if a Min rule exists and the attribute is passed with the request, we make sure the validation rule also has
                // a Required rule, because for this specific case, the required rule will take care of empty values.
                if (
                    stripos($field->validation_rules, 'Min') !== false || // Check for Min rule
                    stripos($field->validation_rules, 'Size') !== false // Or for Size rule
                ) {
                    if (stripos($field->validation_rules, 'Required') === false) {
                        $validationStringArray[] = 'Required';
                    }
                }
                elseif (stripos($field->validation_rules, 'Required') !== false) {
                    foreach ($validationStringArray as $key => $validationStringRule) {
                        // Remove required because update doesn't require any fields
                        if (stripos($validationStringRule, 'Required') !== false) {
                            unset($validationStringArray[$key]);
                        }
                    }
                }

                if (empty($validationStringArray)) {
                    continue;
                }

                $validationString = implode('|', $validationStringArray);

                $validationRules[$field->getUniqueName()] = $validationString;
            }
        }

        return \Validator::make($data, $validationRules);
    }

    /**
     * Makes a validator instance based on field base validation.
     *
     * @param array $data
     * @param array $fields
     * @return Validator
     */
    private function makeBaseFieldValidator($data, $fields)
    {
        $baseValidationRules = [];
        foreach ($fields as $field) {
            $fieldType = $this->fieldTypeRepository->findById($field['type'], $this->fieldTypeGateway);
            if ($fieldType instanceof HasValidation) {
                $baseValidationRules[
                    ($fieldType->isRelation()) ? $field->relation_name : $field->column_name
                ] = $fieldType->getValidationString();
            }
        }

        return \Validator::make($data, $baseValidationRules);
    }

    /**
     * Accepts a reqouest from the route and prepares it for dynamic module entry delete.
     * Landing method for HTTP request.
     *
     * @param string $tableName
     * @param int $entryId
     * @return \Illuminate\Http\Response
     */
    public function deleteEntry($tableName, $entryId)
    {
        $data = \Request::all();
        $force = isset($data['force']);
        $this->deleteDynamicModuleEntry($tableName, $entryId, $force);

        return $this->responseRepository->make('DELETE_DYNAMIC_MODULE_ENTRY_SUCCESS');
    }

    /**
     * Deletes a dynamic module entry.
     *
     * @param string $tableName
     * @param int $entryId
     * @return \Illuminate\Http\Response
     */
    public function deleteDynamicModuleEntry($tableName, $entryId, $force = false)
    {

        $module = $this->moduleRepository->findModuleByTableName($tableName, $this->moduleGateway);

        if (!$module) {
            throw new PhotonException('MODULE_NOT_FOUND', ['table_name' => $tableName]);
        }

        $dynamicModuleGateway = $this->dynamicModuleLibrary->getGatewayInstanceByTableName($tableName);

        $entry = $this->dynamicModuleRepository->findById($entryId, $dynamicModuleGateway);

        if (!$entry) {
            throw new PhotonException('DYNAMIC_MODULE_ENTRY_NOT_FOUND', ['id' => $entryId]);
        }

        if ($this->dynamicModuleLibrary->checkIfEntryHasRelations($module, $entryId) && !$force) {
            throw new PhotonException('CANNOT_DELETE_ENTRY_HAS_RELATIONS', ['id' => $entryId]);
        }

        if (ModuleHelpers::checkIfNodeModule($module) && $this->nodeLibrary->nodeHasChildren($entry) && !$force) {
            throw new PhotonException('CANNOT_DELETE_ENTRY_HAS_CHILDREN', ['id' => $entryId]);
        }

        $this->interrupter->interruptDelete($module->model_name, $entry);

        $this->dynamicModuleRepository->deleteById($entryId, $dynamicModuleGateway);

        if(config("photon.use_photon_cache")) {
            $relatedModules = $this->dynamicModuleLibrary->findRelatedModules($module);
            Cache::tags($relatedModules)->flush(); 
        }
    }

    /**
     * Exports all matching entries.
     * Uses filter and sorting.
     * Landing method for HTTP request.
     *
     * @param string $tableName
     * @return Response
     * @throws PhotonException
     */
    public function exportEntries($tableName)
    {
        $filter     = \Request::get('filter');
        $sorting    = \Request::get('sorting');
        $fileType   = \Request::get('file_type');
        $action     = \Request::get('action');
        $parameters = \Request::get('parameters');
        $fileName   = \Request::get('file_name');

        $response = $this->exportDynamicModuleEntries($tableName, $fileType, $action, $fileName, $filter, $sorting, $parameters);

        if ($response instanceof Response) {
            return $response;
        }

        return $this->responseRepository->make('MODULE_DATA_EXPORTED_SUCCESSFULLY', $response);
    }

    /**
     * Exports all matching entries.
     * Uses filter and sorting.
     *
     * @param string $tableName
     * @return Response
     * @throws PhotonException
     */
    public function exportDynamicModuleEntries($tableName, $fileType, $action, $fileName, array $filter = [], array $sorting = [], $parameters = [])
    {
        $entries = $this->dynamicModuleLibrary->getAllEntries($tableName, $filter, null, $sorting);
        if ($entries instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $entries = $entries->getCollection();
        }

        $module = $this->moduleRepository->findModuleByTableName($tableName, $this->moduleGateway);

        if (!$module) {
            throw new PhotonException('MODULE_NOT_FOUND', ['table_name' => $tableName]);
        }

        // Perform any necessary functionality over retrieved entries before returning the result.
        $this->interrupter->interruptRetrieve($module->model_name, $entries);

        $exporter = DynamicModuleExporterFactory::make($module->model_name, $fileType);
        if (!($exporter instanceof \Photon\PhotonCms\Core\Entities\DynamicModuleExporter\Contracts\DynamicModuleExporterMultipleInterface)) {
            throw new PhotonException('EXPORTING_NOT_SUPPORTED');
        }

        $response = $exporter->$action($entries, $fileName, $parameters);
    }

    /**
     *
     * @param string $tableName
     * @param int $entryId
     * @return Response
     * @throws PhotonException
     */
    public function exportEntry($tableName, $entryId)
    {
        $fileType = \Request::get('file_type');
        $action = \Request::get('action');
        $fileName = \Request::get('file_name');
        $parameters = \Request::get('parameters');

        $entry = $this->dynamicModuleLibrary->findEntryByTableNameAndId($tableName, $entryId);

        if (!$entry) {
            throw new PhotonException('DYNAMIC_MODULE_ENTRY_NOT_FOUND', ['id' => $entryId]);
        }

        // Check if this user is allowed to retrieve this entry.
        if (!PermissionChecker::canCRUDRetrieveMatchingModuleEntry($tableName, $entry)) {
            throw new PhotonException('INSUFICIENT_PERMISSIONS', ['cannot_see' => $entryId]);
        }

        $module = $this->moduleRepository->findModuleByTableName($tableName, $this->moduleGateway);

        if (!$module) {
            throw new PhotonException('MODULE_NOT_FOUND', ['table_name' => $tableName]);
        }

        $exporter = DynamicModuleExporterFactory::make($module->model_name, $fileType);
        if (!($exporter instanceof \Photon\PhotonCms\Core\Entities\DynamicModuleExporter\Contracts\DynamicModuleExporterSingleInterface)) {
            throw new PhotonException('EXPORTING_NOT_SUPPORTED');
        }
        $response = $exporter->$action($entry, $fileName, $parameters);
        if ($response instanceof Response) {
            return $response;
        }

        return $this->responseRepository->make('MODULE_DATA_EXPORTED_SUCCESSFULLY', $response);
    }

    /**
     * Downloads exported files.
     *
     * @param string $fileName
     * @return mixed
     */
    public function downloadExport($fileName)
    {
        $filePathAndName = config('excel.export.store.path').'/'.$fileName;
        if (file_exists($filePathAndName)) {
            header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
            header("Cache-Control: public"); // needed for internet explorer
            header("Content-Transfer-Encoding: ".mb_detect_encoding($filePathAndName));
            header("Content-Length:".filesize($filePathAndName));
            header("Content-Disposition: attachment; filename=".basename($filePathAndName));

            header("Content-Type: ".mime_content_type($filePathAndName));
            readfile($filePathAndName);
            exit();
        } else {
            throw new PhotonException('FILE_NOT_FOUND', ['file' => $filePathAndName]);
        }
    }

    /**
     * Calls a dynamic module extension function.
     * This requires a function to be defined in the module extension class.
     *
     * @param string $tableName
     * @param int $entryId
     * @param string $action
     * @return \Illuminate\Http\Response
     * @throws PhotonException
     */
    public function callExtension($tableName, $entryId, $action, $parameters = '')
    {
        $entry = $this->dynamicModuleLibrary->findEntryByTableNameAndId($tableName, $entryId);

        if (!$entry) {
            throw new PhotonException('DYNAMIC_MODULE_ENTRY_NOT_FOUND', ['id' => $entryId]);
        }

        return $entry->fireAction($action, $parameters);
    }

    /**
     * Counts all dynamic modules entries.
     * Landing method for HTTP request.
     *
     * @param string $tableName
     * @return \Illuminate\Http\Response
     */
    public function countAllEntries($tableName)
    {
        $filter = \Request::get('filter');

        $count = $this->countDynamicModuleEntries($tableName, $filter);

        return $this->responseRepository->make('LOAD_DYNAMIC_MODULE_ENTRIES_SUCCESS', ['count' => $count]);
    }

    /**
     * Counts all dynamic modules entries.
     *
     * @param string $tableName
     * @return \Illuminate\Http\Response
     */
    public function countDynamicModuleEntries($tableName, $filter = null)
    {
        $entries = $this->dynamicModuleLibrary->getAllEntries($tableName, $filter);
        if ($entries instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $entries = $entries->getCollection();
        }

        $module = $this->moduleRepository->findModuleByTableName($tableName, $this->moduleGateway);

        if (!$module) {
            throw new PhotonException('MODULE_NOT_FOUND', ['table_name' => $tableName]);
        }

        // Perform any necessary functionality over retrieved entries before returning the result.
        $this->interrupter->interruptRetrieve($module->model_name, $entries);

        return count($entries);
    }

    /**
     * Subscribe user to dynamic module entry
     *
     * @param string  $tableName
     * @param integer $entryId
     * @return \Illuminate\Http\Response
     */
    public function insertSubscription($tableName, $entryId)
    {
        // check if module exist
        $module = $this->moduleRepository->findModuleByTableName($tableName, $this->moduleGateway);
        if (!$module) {
            throw new PhotonException('MODULE_NOT_FOUND', ['table_name' => $tableName]);
        }

        // check if entry exist
        $entry = $this->dynamicModuleLibrary->findEntryByTableNameAndId($tableName, $entryId);
        if (!$entry) {
            throw new PhotonException('DYNAMIC_MODULE_ENTRY_NOT_FOUND', ['id' => $entryId]);
        }

        $user = \Auth::user();

        $listOfSubscribedUsers = DynamicModuleSubscriberFactory::subscribe($entry, $tableName, $user);

        return $this->responseRepository->make('SUBSCRIPTION_SUCCESS', ['listOfSubscribedUsers' => $listOfSubscribedUsers]);
    }

    /**
     * Unsubscribe user to dynamic module entry
     *
     * @param string  $tableName
     * @param integer $entryId
     * @return \Illuminate\Http\Response
     */
    public function deleteSubscription($tableName, $entryId)
    {
        // check if module exist
        $module = $this->moduleRepository->findModuleByTableName($tableName, $this->moduleGateway);
        if (!$module) {
            throw new PhotonException('MODULE_NOT_FOUND', ['table_name' => $tableName]);
        }

        // check if entry exist
        $entry = $this->dynamicModuleLibrary->findEntryByTableNameAndId($tableName, $entryId);
        if (!$entry) {
            throw new PhotonException('DYNAMIC_MODULE_ENTRY_NOT_FOUND', ['id' => $entryId]);
        }

        // unsubscribe
        $user = \Auth::user();
        $listOfSubscribedUsers = DynamicModuleSubscriberFactory::unsubscribe($entry, $tableName, $user);

        return $this->responseRepository->make('UNSUBSCRIPTION_SUCCESS', ['listOfSubscribedUsers' => $listOfSubscribedUsers]);
    }
}
