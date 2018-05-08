<?php

namespace Photon\PhotonCms\Core\Controllers;

// General
use Schema;
use Photon\Http\Controllers\Controller;
use Photon\PhotonCms\Core\Exceptions\PhotonException;

// Dependency injection
use Photon\PhotonCms\Core\Transaction\TransactionFactory;
use Photon\PhotonCms\Core\Response\ResponseRepository;
use Photon\PhotonCms\Core\Entities\Module\ModuleRepository;
use Photon\PhotonCms\Core\Entities\Module\ModuleHelpers;
use Photon\PhotonCms\Core\Entities\Field\FieldRepository;
use Photon\PhotonCms\Core\Entities\Field\FieldHelpers;
use Photon\PhotonCms\Core\Entities\Field\FieldLibrary;
use Photon\PhotonCms\Core\Entities\DynamicModuleModel\DynamicModuleModelHelper;
use Photon\PhotonCms\Core\Entities\Model\ModelRepository;
use Photon\PhotonCms\Core\Entities\Model\ModelCompiler;
use Photon\PhotonCms\Core\Entities\Model\ModelTemplateFactory;
use Photon\PhotonCms\Core\Entities\Model\Contracts\ModelGatewayInterface;
use Photon\PhotonCms\Core\Entities\NativeClass\NativeClassRepository;
use Photon\PhotonCms\Core\Entities\NativeClass\NativeClassCompiler;
use Photon\PhotonCms\Core\Entities\NativeClass\Contracts\NativeClassGatewayInterface;
use Photon\PhotonCms\Core\Entities\Seed\SeedRepository;
use Photon\PhotonCms\Core\Transform\TransformationController;
use Photon\PhotonCms\Core\Requests\Module\CreateModuleRequest;
use Photon\PhotonCms\Core\Requests\Module\UpdateModuleRequest;
use Photon\PhotonCms\Core\Entities\Module\Contracts\ModuleGatewayInterface;
use Photon\PhotonCms\Core\Entities\Field\Contracts\FieldGatewayInterface;
use Photon\PhotonCms\Core\Entities\Migration\Contracts\MigrationGatewayInterface;
use Photon\PhotonCms\Core\Entities\DynamicModuleMigration\DynamicModuleMigrationRepository;
use Photon\PhotonCms\Core\Entities\DynamicModuleMigration\DynamicModuleMigrationFactory;
use Photon\PhotonCms\Core\Entities\Migration\MigrationCompiler;
use Photon\PhotonCms\Core\Helpers\MigrationsHelper;
use Photon\PhotonCms\Core\Entities\Seed\Contracts\SeedGatewayInterface;
use Photon\PhotonCms\Core\Entities\FieldType\FieldTypeRepository;
use Photon\PhotonCms\Core\Entities\FieldType\FieldTypeGateway;
use Photon\PhotonCms\Core\Entities\ModelAttribute\ModelAttributeFactory;
use Photon\PhotonCms\Core\Entities\Field\Migrations\FieldUpdateTemplateFactory;
use Photon\PhotonCms\Core\Helpers\CodeHelper;

use Photon\PhotonCms\Core\Entities\Seed\SeedTemplate;
use Photon\PhotonCms\Core\Entities\ModelRelation\ModelRelationFactory;
use Photon\PhotonCms\Core\Entities\DynamicModule\DynamicModuleLibrary;
use Photon\PhotonCms\Core\Entities\Module\ModuleLibrary;
use Illuminate\Support\Facades\Cache;

class ModuleController extends Controller
{
    /**
     * @var ModuleRepository
     */
    private $moduleRepository;

    /**
     * @var ModuleGatewayInterface
     */
    private $moduleGateway;

    /**
     * @var ModuleLibrary
     */
    private $moduleLibrary;

    /**
     * @var FieldRepository
     */
    private $fieldRepository;

    /**
     * @var FieldHelpers
     */
    private $fieldHelpers;

    /**
     * @var FieldLibrary
     */
    private $fieldLibrary;

    /**
     * @var FieldGatewayInterface
     */
    private $fieldGateway;

    /**
     * @var ResponseRepository
     */
    private $responseRepository;

    /**
     * @var DynamicModuleMigrationRepository
     */
    private $migrationRepository;

    /**
     * @var MigrationGatewayInterface
     */
    private $migrationGateway;

    /**
     *
     * @var MigrationCompiler
     */
    private $migrationCompiler;

    /**
     * @var ModelRepository
     */
    private $modelRepository;
    /**
     * @var ModelCompiler
     */
    private $modelCompiler;

    /**
     * @var ModelGatewayInterface
     */
    private $modelGateway;

    /**
     * @var NativeClassRepository
     */
    private $classRepository;

    /**
     * @var NativeClassGatewayInterface
     */
    private $classGateway;

    /**
     * @var NativeClassCompiler
     */
    private $classCompiler;

    /**
     * @var SeedRepository
     */
    private $seedRepository;

    /**
     * @var SeedGatewayInterface
     */
    private $seedGateway;

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
     * @var ReportingService
     */
    private $reportingService;

    /**
     * Controller construcor.
     *
     * @param ModuleRepository $moduleRepository
     * @param ModuleGatewayInterface $moduleGateway
     * @param ModuleLibrary $moduleLibrary
     * @param FieldRepository $fieldRepository
     * @param FieldHelpers $fieldHelpers
     * @param FieldLibrary $fieldLibrary
     * @param FieldGatewayInterface $fieldGateway
     * @param ResponseRepository $responseRepository
     * @param DynamicModuleMigrationRepository $migrationRepository
     * @param MigrationGatewayInterface $migrationGateway
     * @param ModelRepository $modelRepository
     * @param ModelCompiler $modelCompiler
     * @param ModelGatewayInterface $modelGateway
     * @param NativeClassRepository $classRepository
     * @param NativeClassGatewayInterface $classGateway
     * @param NativeClassCompiler $classCompiler
     * @param SeedRepository $seedRepository
     * @param SeedGatewayInterface $seedGateway
     * @param FieldTypeRepository $fieldTypeRepository
     * @param FieldTypeGateway $fieldTypeGateway
     * @param DynamicModuleLibrary $dynamicModuleLibrary
     */
    public function __construct(
        ModuleRepository $moduleRepository,
        ModuleGatewayInterface $moduleGateway,
        ModuleLibrary $moduleLibrary,
        FieldRepository $fieldRepository,
        FieldHelpers $fieldHelpers,
        FieldLibrary $fieldLibrary,
        FieldGatewayInterface $fieldGateway,
        ResponseRepository $responseRepository,
        DynamicModuleMigrationRepository $migrationRepository,
        MigrationGatewayInterface $migrationGateway,
        MigrationCompiler $migrationCompiler,
        ModelRepository $modelRepository,
        ModelCompiler $modelCompiler,
        ModelGatewayInterface $modelGateway,
        NativeClassRepository $classRepository,
        NativeClassGatewayInterface $classGateway,
        NativeClassCompiler $classCompiler,
        SeedRepository $seedRepository,
        SeedGatewayInterface $seedGateway,
        FieldTypeRepository $fieldTypeRepository,
        FieldTypeGateway $fieldTypeGateway,
        DynamicModuleLibrary $dynamicModuleLibrary
    )
    {
        $this->moduleRepository         = $moduleRepository;
        $this->moduleGateway            = $moduleGateway;
        $this->moduleLibrary            = $moduleLibrary;
        $this->fieldRepository          = $fieldRepository;
        $this->fieldHelpers             = $fieldHelpers;
        $this->fieldLibrary             = $fieldLibrary;
        $this->fieldGateway             = $fieldGateway;
        $this->responseRepository       = $responseRepository;
        $this->migrationRepository      = $migrationRepository;
        $this->migrationGateway         = $migrationGateway;
        $this->migrationCompiler        = $migrationCompiler;
        $this->modelRepository          = $modelRepository;
        $this->modelCompiler            = $modelCompiler;
        $this->modelGateway             = $modelGateway;
        $this->classGateway             = $classGateway;
        $this->classRepository          = $classRepository;
        $this->classCompiler            = $classCompiler;
        $this->seedRepository           = $seedRepository;
        $this->seedGateway              = $seedGateway;
        $this->fieldTypeRepository      = $fieldTypeRepository;
        $this->fieldTypeGateway         = $fieldTypeGateway;
        $this->dynamicModuleLibrary     = $dynamicModuleLibrary;
        $this->reportingService         = \App::make('ReportingService');
    }

    /**
     * Retrieves all modules.
     * Optionally can retrieve categories as well.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllModules()
    {
        $modules = $this->moduleLibrary->getAllModules();

        $responseData = ['modules' => $modules];

        if (\Request::get('with') === 'categories') {
            $responseData['categories'] = $this->moduleRepository->getAllMultilevelSortable($this->moduleGateway);
        }

        return $this->responseRepository->make('GET_ALL_MODULES_SUCCESS', $responseData);
    }

    /**
     * Retrieves a module.
     *
     * @param string $tableName
     * @return \Illuminate\Http\Response
     * @throws PhotonException
     */
    public function getModule($tableName)
    {
        $module = $this->moduleRepository->findModuleByTableName($tableName, $this->moduleGateway);

        if (is_null($module)) {
            throw new PhotonException('MODULE_NOT_FOUND', ['table_name' => $tableName]);
        }
        
        $module->load(['fields' => function($query) {
            $query->orderBy("order", "asc");
        }]);

        return $this->responseRepository->make('GET_MODULE_INFORMATION_SUCCESS', ['module' => $module]);
    }

    /**
     * Creates a module with fields from request data.
     *
     * @param CreateModuleRequest $request
     * @return \Illuminate\Http\Response
     */
    public function createModule(CreateModuleRequest $request)
    {
        // Initialize a transaction
        $transactionController = TransactionFactory::make('CREATE_MODULE');

        // Prepare input data
        $moduleData = \Request::get('module');
        $moduleData['model_name'] = DynamicModuleModelHelper::generateModelNameFromString($moduleData['name']);
        ModuleHelpers::validateTableName($moduleData['table_name']);
        $fieldsData = \Request::get('fields');

        // validate that there is only one default_search_choice
        $hasSearchChoice = false;
        foreach ($fieldsData as $key => $fieldData) {
            if(isset($fieldData['is_default_search_choice']) && filter_var($fieldData['is_default_search_choice'], FILTER_VALIDATE_BOOLEAN) ) {
                if($hasSearchChoice) {
                    throw new PhotonException('MODULE_CREATION_ERROR_MULTIPLE_DEFAULT_SEARCH_CHOICES');
                }
                $hasSearchChoice = true;
            }

            // if field is relation with entry filter set check if field exists
            if(isset($fieldData['active_entry_filter']) && isset($fieldData['related_module'])) {
                $relatedModuleFields = $this->fieldRepository->findByModuleId($fieldData['related_module'], $this->fieldGateway);
                $isInRelatedModule = false;
                foreach ($relatedModuleFields as $key => $relatedField) 
                    if($relatedField->column_name == $fieldData['active_entry_filter'])
                        $isInRelatedModule = true;

                if(!$isInRelatedModule)
                    throw new PhotonException('MODULE_CREATION_ERROR_ACTIVE_ENTRY_FILTER_NOT_FOUND');
            }

            if(isset($fieldData['can_create_search_choice']) && $fieldData['can_create_search_choice'] && isset($fieldData['related_module'])) {
                $relatedModuleFields = $this->fieldRepository->findByModuleId($fieldData['related_module'], $this->fieldGateway);
                $hasDefaultSearchChoice = false;
                foreach ($relatedModuleFields as $key => $relatedField) 
                    if($relatedField->is_default_search_choice)
                        $hasDefaultSearchChoice = true;

                if(!$hasDefaultSearchChoice)
                    throw new PhotonException('MODULE_CREATION_ERROR_RELATED_MODULE_DEFAULT_SEARCH_CHOICE_MISSING');
            }

            if($fieldData['type'] == 17 && (!isset($fieldData['disabled']) || $fieldData['disabled'] != 1))
                throw new PhotonException('VALIDATION_ERROR', [
                    'error_fields' => [ 
                        "fields[{$key}][disabled]" => [
                            "failed_rule" => "Confirmed",
                            "message" => "The fields.{$key}.disabled option must be set to true."
                        ]
                    ]
                ]);
        }

        $this->deleteMigrations();

        // Preload all field types into the ORM for better efficiency
        $this->fieldTypeRepository->preloadAll($this->fieldTypeGateway);

        $module = null;
        // Queue a module insert into transaction
        $transactionController->queue(
            function () use (&$module, &$moduleData) {
                $module = $this->moduleRepository->saveFromData($moduleData, $this->moduleGateway);
            },
            function () use (&$module) {
                $this->moduleRepository->deleteById($module->id, $this->moduleGateway);
            },
            'MODULE_CREATION_DB_INSERT',
            'MODULE_CREATION_DB_INSERT_ROLLBACK'
        );

        // Queue field inserts into transaction
        $transactionController->queue(
            function () use (&$module, &$fieldsData) {
                foreach ($fieldsData as $key => $fieldData) {
                    $fieldsData[$key]['module_id'] = $module->id;
                    $fieldsData[$key]['order'] = $key;
                    $this->fieldRepository->saveFromData($fieldsData[$key], $this->fieldGateway);
                }
            },
            null,
            'MODULE_CREATION_FIELD_INSERTS',
            'MODULE_CREATION_FIELD_INSERTS_ROLLBACK'
        );

        // Field names preparation
        $fieldNames = [];
        foreach ($fieldsData as $fieldData) {
            // No attributes or relations for a virtual field
            if (key_exists('virtual', $fieldData) && ($fieldData['virtual'] == 1 || $fieldData['virtual'] = true)) {
                continue;
            }

            $fieldType = FieldTypeRepository::findByIdStatic($fieldData['type']);

            // Field names
            if ($fieldType->isAttribute()) {
                if ($fieldType->isRelation()) {
                    $fieldNames[] = $fieldData['relation_name'];
                }
                else {
                    $fieldNames[] = $fieldData['column_name'];
                }
            }
        }

        // Model relations preparation
        $modelRelations = ModelRelationFactory::makeMultipleFromFieldDataArray($fieldsData, $moduleData['table_name']);
        // Model attributes preparation
        $modelAttributes = ModelAttributeFactory::makeMultipleFromFieldDataArray($fieldsData);

        // Validate field names
        $this->fieldHelpers->validateFieldNames($fieldNames);

        // Validate anchor text against field names
        if (key_exists('anchor_text', $moduleData)) {

            $availableFieldNames = [];
            foreach ($modelAttributes as $modelAttribute) {
                $availableFieldNames[$modelAttribute->getName()] = null;
            }
            foreach ($modelRelations as $modelRelation) {
                if (!$modelRelation->requiresPivot()) {
                    $availableFieldNames[$modelRelation->getRelationName()] = $this->fieldLibrary->findFieldNamesRecursivelyByModuleId(
                        $this->moduleLibrary->findByTableName($modelRelation->getTargetTable())->id
                    );
                }
            }

            ModuleHelpers::validateAnchorTextAgainstFieldNames($moduleData['anchor_text'], $availableFieldNames);
        }

        // Validate anchor html against field names
        if (key_exists('anchor_html', $moduleData)) {

            $availableFieldNames = [];
            foreach ($modelAttributes as $modelAttribute) {
                $availableFieldNames[$modelAttribute->getName()] = null;
            }
            foreach ($modelRelations as $modelRelation) {
                if (!$modelRelation->requiresPivot()) {
                    $availableFieldNames[$modelRelation->getRelationName()] = $this->fieldLibrary->findFieldNamesRecursivelyByModuleId(
                        $this->moduleLibrary->findByTableName($modelRelation->getTargetTable())->id
                    );
                }
            }

            ModuleHelpers::validateAnchorTextAgainstFieldNames($moduleData['anchor_html'], $availableFieldNames);
        }

        // Validate slugs against field names
        if (key_exists('slug', $moduleData)) {

            $availableFieldNames = [];
            foreach ($modelAttributes as $modelAttribute) {
                $availableFieldNames[$modelAttribute->getName()] = null;
            }
            foreach ($modelRelations as $modelRelation) {
                if (!$modelRelation->requiresPivot()) {
                    $availableFieldNames[$modelRelation->getRelationName()] = $this->fieldLibrary->findFieldNamesRecursivelyByModuleId(
                        $this->moduleLibrary->findByTableName($modelRelation->getTargetTable())->id
                    );
                }
            }

            ModuleHelpers::validateAnchorTextAgainstFieldNames($moduleData['slug'], $availableFieldNames);
        }

        // Prepare migration template
        $migrationTemplate = DynamicModuleMigrationFactory::makeByType($moduleData['type']);
        $migrationTemplate->setClassName(MigrationsHelper::generateAutoMigrationClassName());
        $migrationTemplate->addNewModuleTable($moduleData['table_name'], $modelAttributes, $modelRelations);

        // Queue creation of migration into transaction
        $transactionController->queue(
            function () use (&$migrationTemplate) {
                $this->migrationRepository->create($migrationTemplate, $this->migrationCompiler, $this->migrationGateway);
            },
            function () use (&$migrationTemplate) {
                $this->migrationRepository->delete($migrationTemplate, $this->migrationGateway);
            },
            'MODULE_CREATION_ADDING_MIGRATION',
            'MODULE_CREATION_ADDING_MIGRATION_ROLLBACK'
        );

        // Prepare model template
        $modelTemplate = ModelTemplateFactory::makeByType($moduleData['type']);
        $modelTemplate->setTableName($moduleData['table_name']);
        $modelTemplate->setModelName($moduleData['model_name']);
        $modelTemplate->addAttributes($modelAttributes);
        $modelTemplate->addRelations($modelRelations);

        // Queue model file creation into transaction
        $transactionController->queue(
            function () use (&$modelTemplate) {
                $this->modelRepository->saveFromTemplate($modelTemplate, $this->modelCompiler, $this->modelGateway);
            },
            function () use (&$modelTemplate) {
                $this->modelRepository->deleteFromTemplate($modelTemplate, $this->modelGateway);
            },
            'MODULE_CREATION_CREATING_MODEL',
            'MODULE_CREATION_CREATING_MODEL_ROLLBACK'
        );

        // Prepare extender template
        $extenderName = DynamicModuleModelHelper::generateModelExtenderNameFromString($moduleData['name']);
        $extenderTemplate = ModelTemplateFactory::makeDynamicModuleExtensionTemplate();
        $extenderTemplate->setClassName($extenderName);
        // Queue module extender file creation into transaction
        $transactionController->queue(
            function () use (&$extenderTemplate) {
                $this->classRepository->saveFromTemplate($extenderTemplate, $this->classCompiler, $this->classGateway);
            },
            function () use (&$extenderTemplate) {
                $this->classRepository->deleteFromTemplate($extenderTemplate, $this->classGateway);
            },
            'MODULE_CREATION_CREATING_MODULE_EXTENDER',
            'MODULE_CREATION_CREATING_MODULE_EXTENDER_ROLLBACK'
        );

        // Queue artisan migration run into transaction
        $transactionController->queue(
            function () use (&$migrationTemplate) {
                $this->migrationRepository->run($migrationTemplate, $this->migrationGateway);
            },
            null,
            'MODULE_CREATION_MIGRATION_RUN'
        );

        // Commit the transaction
        $transactionController->commit();

        // Rebuild field and module seeders
        $this->rebuildSeeders();

        // Reload module fields from the DB
        $module->load('fields');
        $responseData = [
            'module' => $module
        ];

        return $this->responseRepository->make(
            (
                ($this->reportingService->isActive())
                    ? 'MODULE_CREATION_REPORT_SUCCESS'
                    : 'MODULE_CREATION_SUCCESS'
            ),
            $responseData
        );
    }

    /**
     * Updates a module along with fields.
     *
     * @param string|int $tableName
     * @param UpdateModuleRequest $request
     * @param TransformationController $transformationController
     * @return \Illuminate\Http\Response
     * @throws PhotonException
     */
    public function updateModule($tableName, UpdateModuleRequest $request, TransformationController $transformationController)
    {
        // Initialize the transaction
        $transactionController = TransactionFactory::make('UPDATE_MODULE');

        // Prepare input data
        $moduleData = \Request::get('module');
        $fieldsData = \Request::get('fields');

        $module = $this->moduleRepository->findModuleByTableName($tableName, $this->moduleGateway);

        if (is_null($module)) {
            throw new PhotonException('MODULE_NOT_FOUND', ['table_name' => $tableName]);
        }

        if(config("photon.use_photon_cache")) {
            $relatedModules = $this->dynamicModuleLibrary->findRelatedModules($module);
            Cache::tags($relatedModules)->flush();
        }

        $moduleData['id'] = $module->id;
        $hasNewSearchChoice = false;
        foreach ($fieldsData as $key => $fieldData) {
            $fieldsData[$key]['module_id'] = $module->id;
            if(isset($fieldData['is_default_search_choice']) && filter_var($fieldData['is_default_search_choice'], FILTER_VALIDATE_BOOLEAN) ) {
                if($hasNewSearchChoice) {
                    throw new PhotonException('MODULE_UPDATE_ERROR_MULTIPLE_DEFAULT_SEARCH_CHOICES');
                }
                $hasNewSearchChoice = true;
            }

            if(isset($fieldData['can_create_search_choice']) && $fieldData['can_create_search_choice'] && isset($fieldData['related_module'])) {
                $relatedModuleFields = $this->fieldRepository->findByModuleId($fieldData['related_module'], $this->fieldGateway);
                $hasDefaultSearchChoice = false;
                foreach ($relatedModuleFields as $key => $relatedField) 
                    if($relatedField->is_default_search_choice)
                        $hasDefaultSearchChoice = true;

                if(!$hasDefaultSearchChoice)
                    throw new PhotonException('MODULE_CREATION_ERROR_RELATED_MODULE_DEFAULT_SEARCH_CHOICE_MISSING');
            }

            if($fieldData['type'] == 17 && (!isset($fieldData['disabled']) || $fieldData['disabled'] != 1))
                throw new PhotonException('VALIDATION_ERROR', [
                    'error_fields' => [ 
                        "fields[{$key}][disabled]" => [
                            "failed_rule" => "Confirmed",
                            "message" => "The fields.{$key}.disabled option must be set to true."
                        ]
                    ]
                ]);
        }

        $this->deleteMigrations();

        // Preload all field types into the ORM for better efficiency
        $this->fieldTypeRepository->preloadAll($this->fieldTypeGateway);

        // Backing up module data
        $moduleBackupData = $transformationController->objectFullTransform($module);

        // Backing up field data
        $existingFields    = $module->fields;
        $fieldBackupData = [];
        foreach ($existingFields as $field) {
            $fieldBackupData[] = $transformationController->objectFullTransform($field);
        }

        // Separate remaining fields and fields for removal
        $fieldsForRemoval = [];
        $remainingFields = [];
        foreach ($existingFields as $existingField) {
            if (isset($existingField->id) && in_array($existingField->id, array_column($fieldsData, 'id'))) {
                $remainingFields[] = $existingField;
            }
            else {
                $fieldsForRemoval[] = $existingField;
            }
        }

        // Separate remaining field names
        $preservedFieldNames = [];
        foreach ($remainingFields as $remainingField)
        {
            $fieldType = $this->fieldTypeRepository->findById($remainingField->type, $this->fieldTypeGateway);

            if (!$fieldType->requiresPivot() && !$remainingField->virtual) {
                if ($fieldType->isRelation()) {
                    $preservedFieldNames[$remainingField->relation_name] = $this->fieldLibrary->findFieldNamesRecursivelyByModuleId($remainingField->related_module);
                }
                else {
                    $preservedFieldNames[$remainingField->column_name] = null;
                }
            }
        }

        // Separate remaining relation names
        foreach ($fieldsData as $fieldData) {
            // Virtual fields are not visible here
            if (key_exists('virtual', $fieldData) && ($fieldData['virtual'] == 1 || $fieldData['virtual'] = true)) {
                continue;
            }
            
            if (!key_exists('id', $fieldData)) {
                $fieldType = $this->fieldTypeRepository->findById($fieldData['type'], $this->fieldTypeGateway);

                if (!$fieldType->requiresPivot() && (!key_exists('virtual', $fieldData) || !$fieldData['virtual'])) {
                    if ($fieldType->isRelation()) {
                        $preservedFieldNames[$fieldData['relation_name']] = $this->fieldLibrary->findFieldNamesRecursivelyByModuleId($fieldData['related_module']);
                    }
                    else {
                        $preservedFieldNames[$fieldData['column_name']] = null;
                    }
                }
            }
        }

        //Validate field names
        $this->fieldHelpers->validateFieldNames(array_keys($preservedFieldNames));

        // Validate anchor text against field names
        if (key_exists('anchor_text', $moduleData)) {
            ModuleHelpers::validateAnchorTextAgainstFieldNames($moduleData['anchor_text'], $preservedFieldNames);
        }

        // Validate anchor html against field names
        if (key_exists('anchor_html', $moduleData)) {
            ModuleHelpers::validateAnchorTextAgainstFieldNames($moduleData['anchor_html'], $preservedFieldNames);
        }

        // Validate anchor html against field names
        if (key_exists('slug', $moduleData)) {
            ModuleHelpers::validateAnchorTextAgainstFieldNames($moduleData['slug'], $preservedFieldNames);
        }

        // Queue module update into transaction
        $transactionController->queue(
            function () use (&$moduleData) {
                $this->moduleRepository->saveFromData($moduleData, $this->moduleGateway);
            },
            function () use (&$moduleBackupData) {
                $this->moduleRepository->fullUpdateFromData($moduleBackupData, $this->moduleGateway);
            },
            'UPDATING_MODULE',
            'UPDATING_MODULE_ROLLBACK'
        );

//        $existingFields <- instances
//        $fieldBackupData <- arrays
//        $fieldsForRemoval <- instances
//        $remainingFields <- instances

        // Field separation
        $fieldsForCreation = []; // <- arrays
        $fieldsForUpdate = []; // <- arrays

        // Updating field order
        foreach ($fieldsData as $order => $fieldData) {
            $fieldData['order'] = $order;
            if (!key_exists('id', $fieldData)) {
                $fieldsForCreation[] = $fieldData;
            }
            else {
                $fieldsForUpdate[] = $fieldData;
            }
        }

        // Queue new fields creation and fields update into transaction
        $newFields = [];
        $transactionController->queue(
            function () use (&$newFields, &$fieldsForUpdate, &$fieldsForCreation, $moduleData, $hasNewSearchChoice) {
                if($hasNewSearchChoice && !(\Request::exists('reporting') && \Request::get('reporting'))) {
                    $this->fieldRepository->resetDefaultSearchChoices($moduleData['id'], $this->fieldGateway);
                }
                foreach ($fieldsForUpdate as $fieldForUpdate) {
                    $this->fieldRepository->saveFromData($fieldForUpdate, $this->fieldGateway);
                }
                foreach ($fieldsForCreation as $fieldForCreation) {
                    $field = $this->fieldRepository->saveFromData($fieldForCreation, $this->fieldGateway);
                    $newFields[] = $field;
                }
            },
            function () use (&$fieldBackupData, &$newFields) {
                foreach ($fieldBackupData as $fieldData) {
                    $this->fieldRepository->fullSaveFromData($fieldData, $this->fieldGateway);
                }
                foreach ($newFields as $newField) {
                    $this->fieldRepository->delete($newField, $this->fieldGateway);
                }
            },
            'UPDATING_EXISTING_FIELDS',
            'UPDATING_EXISTING_FIELDS_ROLLBACK'
        );

        // Queue field removal into transaction
        $transactionController->queue(
            function () use (&$fieldsForRemoval) {
                foreach ($fieldsForRemoval as $fieldForRemoval) {
                    $this->fieldRepository->delete($fieldForRemoval, $this->fieldGateway);
                }
            },
            function () use (&$fieldBackupData) {
                foreach ($fieldBackupData as $fieldData) {
                    $this->fieldRepository->fullSaveFromData($fieldData, $this->fieldGateway);
                }
            },
            'DELETING_FIELDS',
            'DELETING_FIELDS_ROLLBACK'
        );

        // Model relations for creation
        $migrationRelationsForCreation = ModelRelationFactory::makeMultipleFromFieldDataArray($fieldsForCreation, $module->table_name);
        // Model attributes for creations
        $migrationFieldsForCreation = ModelAttributeFactory::makeMultipleFromFieldDataArray($fieldsForCreation);
        
        // Model attributes for update
        $migrationFieldsForUpdate = [];
        foreach ($fieldsForUpdate as $fieldForUpdate) {
            $fieldForUpdateTemplate = FieldUpdateTemplateFactory::makeFieldUpdateTemplateFromDataDifference(
                $this->fieldRepository->find($fieldForUpdate['id'], $this->fieldGateway),
                $fieldForUpdate
            );
            if ($fieldForUpdateTemplate) {
                $migrationFieldsForUpdate[] = $fieldForUpdateTemplate;
            }
        }

        // Migration fields for removal
        $migrationFieldsForRemoval = ModelAttributeFactory::makeMultipleFromFields($fieldsForRemoval);
        // Migration relations for removal
        $migrationRelationsForRemoval = ModelRelationFactory::makeMultipleFromFields($fieldsForRemoval);

        // Building up a migration if neccessary
        if (
            !empty($migrationFieldsForCreation) ||
            !empty($migrationFieldsForUpdate) ||
            !empty($migrationFieldsForRemoval) ||
            !empty($migrationRelationsForCreation) ||
            !empty($migrationRelationsForRemoval)
        ) {
            // Defining migration template which will be used to update data for all the fields.
            $migrationTemplate = DynamicModuleMigrationFactory::make();
            $migrationTemplate->setClassName(MigrationsHelper::generateAutoMigrationClassName());
            $migrationTemplate->addTableForUpdate($module->table_name, $migrationFieldsForCreation, $migrationFieldsForUpdate, $migrationFieldsForRemoval, $migrationRelationsForCreation, $migrationRelationsForRemoval);

            // Queue migration creation into transaction
            $transactionController->queue(
                function () use (&$migrationTemplate) {
                    $this->migrationRepository->create($migrationTemplate, $this->migrationCompiler, $this->migrationGateway);
                },
                function () use (&$migrationTemplate) {
                    $this->migrationRepository->delete($migrationTemplate, $this->migrationGateway);
                },
                'FIELD_UPDATE_ADDING_MIGRATION',
                'FIELD_UPDATE_ADDING_MIGRATION_ROLLBACK'
            );
        }

        // Queue model file rebuild into transaction
        $transactionController->queue(
            function () use (&$module) {
                $this->modelRepository->rebuildModel($module, $this->modelCompiler, $this->modelGateway);
            },
            function () use (&$module) {
            // ToDo: This is a paradox, if a transaction rolls back at this point, it is expected that the previous changes are already ireversible (Sasa|03/2017)
//                $this->modelRepository->deleteClassFile($modelTemplate, $this->modelGateway);
            },
            'MODULE_CREATION_CREATING_MODEL',
            'MODULE_CREATION_CREATING_MODEL_ROLLBACK'
        );

        // Queue artisan migration run into transaction
        if (isset($migrationTemplate)) {
            $transactionController->queue(
                function () use (&$migrationTemplate) {
                    $this->migrationRepository->run($migrationTemplate, $this->migrationGateway);
                },
                null,
                'FIELD_UPDATE_MIGRATION_RUN'
            );
        }

        // Commit the transaction
        $transactionController->commit();

        // Rebuild module and field seeders
        $this->rebuildSeeders();

        // Reload module fields from the DB
        $module = $module->fresh('fields');
        $responseData = [
            'module' => $module
        ];

        // Update entry anchor texts
        if (!(\Request::exists('reporting') && \Request::get('reporting'))) {
            $this->dynamicModuleLibrary->updateAllModuleAnchorTextsForEntries($module, "anchor_text");
            $this->dynamicModuleLibrary->updateAllModuleAnchorTextsForEntries($module, "anchor_html");
        }

        return $this->responseRepository->make(
            (
                ($this->reportingService->isActive())
                    ? 'MODULE_UPDATE_REPORT_SUCCESS'
                    : 'MODULE_UPDATE_SUCCESS'
            ),
            $responseData
        );
    }

    /**
     * Completely deletes a module.
     *
     * Deletes module table and inserts in module and fields tables, model file, creates migration files and rebuilds seeders.
     *
     * @param string $tableName
     * @return \Illuminate\Http\Response
     */
    public function deleteModule($tableName, TransformationController $transformationController)
    {
        // Initialize transaction
        $transactionController = TransactionFactory::make('DELETE_MODULE');

        $module = $this->moduleRepository->findModuleByTableName($tableName, $this->moduleGateway);

        // Check if module is found
        if (is_null($module)) {
            throw new PhotonException('MODULE_NOT_FOUND', ['table_name' => $tableName]);
        }

        // Check if the module is not a system module
        if ($module->is_system) {
            throw new PhotonException('MODULE_IS_SYSTEM_DELETE_FAILURE', ['table_name' => $tableName]);
        }
        
        $this->deleteMigrations();

        // Check if module has relations pointing to it
        $relatedFields = $this->fieldRepository->findByRelatedModuleId($module->id, $this->fieldGateway);
        if (!$relatedFields->isEmpty()) {
            throw new PhotonException('MODULE_HAS_RELATIONS');
        }
        
        if(config("photon.use_photon_cache")) {
            $relatedModules = $this->dynamicModuleLibrary->findRelatedModules($module);
            Cache::tags($relatedModules)->flush();
        }

        // Backing up module data
        $moduleData = $transformationController->objectFullTransform($module);

        // Backing up field data
        $fields    = $module->fields;
        $fieldData = [];
        foreach ($fields as $field) {
            $fieldData[] = $transformationController->objectFullTransform($field);
        }

        // Queue module and fields removal into transaction
        $transactionController->queue(
            function () use (&$module) {
                $this->moduleRepository->deleteById($module->id, $this->moduleGateway);
            },
            function () use (&$moduleData, &$fieldsData) {
                $module = $this->moduleRepository->saveFromData($moduleData, $this->moduleGateway);
                foreach ($fieldsData as $key => $fieldData) {
                    $this->fieldRepository->saveFromData($fieldsData[$key], $this->fieldGateway);
                }
            },
            'DELETING_MODULE_REMOVING_FIELD_AND_MODULE_INSERTS',
            'DELETING_MODULE_REMOVING_FIELD_AND_MODULE_INSERTS_ROLLBACK'
        );

        // Queue drop model into transaction
        $transactionController->queue(
            function () use (&$moduleData) {
                $this->modelRepository->deleteClassByName($moduleData['model_name'], $this->modelGateway);
            },
            null,
            'DELETING_MODULE_REMOVING_MODEL',
            '',
            false
        );

        $migrationRelationsForCreation = ModelRelationFactory::makeMultipleFromFieldDataArray($fields, $module->table_name);

        // Setting up the migration template
        $migrationTemplate = DynamicModuleMigrationFactory::make();
        $migrationTemplate->setClassName(MigrationsHelper::generateAutoMigrationClassName());
        $migrationTemplate->addTableForRemoval($module->table_name, $migrationRelationsForCreation);

        // Queue creation of migration into transaction
        $transactionController->queue(
            function () use (&$migrationTemplate) {
                $this->migrationRepository->create($migrationTemplate, $this->migrationCompiler, $this->migrationGateway);
            },
            function () use (&$migrationTemplate) {
                $this->migrationRepository->delete($migrationTemplate, $this->migrationGateway);
            },
            'MODULE_REMOVAL_ADDING_MIGRATION',
            'MODULE_REMOVAL_ADDING_MIGRATION_ROLLBACK'
        );

        // Queue artisan migration run into transaction
        $transactionController->queue(
            function () use (&$migrationTemplate) {
                $this->migrationRepository->run($migrationTemplate, $this->migrationGateway);
            },
            null,
            'MODULE_REMOVAL_MIGRATION_RUN'
        );

        // Commit the transaction
        $transactionController->commit();

        // Rebuild module and field seeders
        $this->rebuildSeeders();

        $responseData = [
            'module' => $module
        ];

        return $this->responseRepository->make(
            (
                ($this->reportingService->isActive())
                    ? 'MODULE_DELETION_REPORT_SUCCESS'
                    : 'MODULE_DELETION_SUCCESS'
            ),
            $responseData
        );
    }

    /**
     * Rebuilds module and field seeders.
     */
    private function rebuildSeeders()
    {
        // ToDo: needs a SeedTemplateFactory here (Sasa|01/2016)
        $seedTemplate = new SeedTemplate();
        $seedTemplate->addTable('modules');
        $seedTemplate->addTable('field_types');
        $seedTemplate->addTable('model_meta_types');
        $seedTemplate->useForce();
        $this->seedRepository->create($seedTemplate, $this->seedGateway);

        $seedTemplate = new SeedTemplate();
        $seedTemplate->addTable('fields');
        $seedTemplate->addTable('model_meta_data');
        $seedTemplate->addExclusion('id');
        $seedTemplate->useForce();
        $this->seedRepository->create($seedTemplate, $this->seedGateway);
    }

    /**
     * Deletes all dynamic migration files in the system.
     */
    private function deleteMigrations()
    {
        $pathToMigrations = base_path(config('photon.dynamic_model_migrations_dir'));
        $filenameExpression = '*.php';
        foreach (glob("$pathToMigrations/$filenameExpression") as $file) {
            if(is_file($file)) {
                unlink($file);
            }
        }
    }
}