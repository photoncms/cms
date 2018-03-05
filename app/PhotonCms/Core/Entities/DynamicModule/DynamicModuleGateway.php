<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModule;

use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Photon\PhotonCms\Core\Entities\DynamicModule\Contracts\DynamicModuleInterface;
use Photon\PhotonCms\Core\Entities\DynamicModule\Contracts\DynamicModuleGatewayInterface;
use Photon\PhotonCms\Core\Entities\Node\Contracts\RootNodesInterface;
use Photon\PhotonCms\Core\Entities\Node\Contracts\NodeChildrenInterface;
use Photon\PhotonCms\Core\Entities\Node\Node;
use Photon\PhotonCms\Core\Entities\DynamicModuleModel\Contracts\CanFakeNodeInterface;
use Photon\PhotonCms\Core\PermissionServices\PermissionORMHelper;
use Photon\PhotonCms\Core\Helpers\DatabaseHelper;
use Photon\PhotonCms\Core\Entities\Field\FieldRepository;
use Photon\PhotonCms\Core\Entities\Module\ModuleRepository;
use Photon\PhotonCms\Core\Entities\Field\FieldGateway;

/**
 * Decouples repository from data sources.
 */
class DynamicModuleGateway implements DynamicModuleGatewayInterface, RootNodesInterface, NodeChildrenInterface
{
    /**
     * Class name with namespace of the dynamic module.
     *
     * @var string
     */
    protected $modelClassName = '';

    protected $moduleTableName = null;

    /**
     * Gateway constructor.
     *
     * $className providing the full name with the namespace of the class must be provided on gateway constructor.
     *
     * @param string $className
     * @throws PhotonException
     */
    public function __construct($className, $tableName = null)
    {
        if (class_exists($className)) {
            $this->modelClassName = $className;
        }
        else {
            throw new PhotonException('CLASS_DOESNT_EXIST', ['class' => $className]);
        }
        $this->moduleTableName = $tableName;
    }

    /**
     * Checks if the affected module is of type single-entry
     *
     * ToDo: this is a terrible thing to do! module dependency should be decoupled by gateway specialization. We need a gateway for each module type to cure this. (Sasa|06/2016)
     *
     * @return boolean
     */
    private function isSingleEntryModule()
    {
        $className = $this->modelClassName;
        return is_subclass_of($className, '\Photon\PhotonCms\Core\Entities\DynamicModule\Contracts\SingleEntryInterface');
    }

    /**
     * Retrieves a dynamic module entry instance from the DB.
     *
     * @param int $id
     * @return mixed
     */
    public function retrieve($id)
    {
        $className = $this->modelClassName;

        // This is for single entry module types only
        // if ($this->isSingleEntryModule()) {
        //     $instance = $this->retrieveFirst();
        //     // If the first instance haven't already been created, create it.
        //     if (!$instance) {
        //         $firstInstance = new $className;
        //         $firstInstance->save();
        //         return $firstInstance;
        //     }
        //     else {
        //         return $instance;
        //     }
        // }
        $queryBuilder = $className::query();

        PermissionORMHelper::applyRestrictions(
            $queryBuilder,
            $this->moduleTableName
        );

        $this->loadRelations($queryBuilder);

        return $queryBuilder->find($id);
    }

    /**
     * Fetches the first entry in the module table.
     *
     * @return mixed
     */
    private function retrieveFirst()
    {
        $className = $this->modelClassName;
        $queryBuilder = $className::query();

        PermissionORMHelper::applyRestrictions(
            $queryBuilder,
            $this->moduleTableName
        );

        $this->loadRelations($queryBuilder);

        return $queryBuilder->find(1);
    }

    /**
     * Retrieves all entry instances of a single dynamic module from the DB.
     *
     * @param array $filter
     * @param array $pagination
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function retrieveALL($filter = null, $pagination = null, $sorting = null)
    {
        $className = $this->modelClassName;
        $queryBuilder = $className::query();

        PermissionORMHelper::applyRestrictions(
            $queryBuilder,
            $this->moduleTableName
        );

        $this->loadRelations($queryBuilder);

        if (is_array($filter)) {
            $this->applyFilter($queryBuilder, $filter);
        }

        if (is_array($sorting)) {
            $this->applySorting($queryBuilder, $sorting);
        }

        if (is_array($pagination)) {
            return $this->paginateResults($queryBuilder, $pagination);
        }

        return $queryBuilder->get();
    }

    private function loadRelations(&$queryBuilder)
    {
        $module = ModuleRepository::findByTableNameStatic($this->moduleTableName);

        $fieldRepository = new FieldRepository();
        $fieldGateway = new FieldGateway();

        $fields = $fieldRepository->findByModuleId($module->id, $fieldGateway);

        $relationArray = [
            "created_by_relation",
            "updated_by_relation"
        ];

        $className = $this->modelClassName;
        $class = new $className();
        
        foreach ($fields as $key => $field) {
            if($field->relation_name && method_exists($class, $field->relation_name . "_relation"))
                if($field->related_module == 4)
                    $relationArray[] = $field->relation_name . "_relation.resized_images_relation.image_size_relation";
                else 
                    $relationArray[] = $field->relation_name . "_relation";
        }

        $queryBuilder->with($relationArray);
    }

    private function applySorting($queryBuilder, $sortData)
    {
        foreach ($sortData as $sortKey => $sortDirection) {
            $queryBuilder->orderBy($sortKey, $sortDirection);
        }
    }

    /**
     * Applies filter query parameters to the query builder.
     *
     * @param QueryBuilder $queryBuilder
     * @param array $filterData
     * @param string $filterData
     */
    private function applyFilter($queryBuilder, $filterData, $relationName = null)
    {
        foreach ($filterData as $fieldName => $compareCriteria) {
            foreach ($compareCriteria as $compareCommand => $compareValue) {
                if ($compareCommand === 'equal') {
                    $queryBuilder->where($fieldName, '=', $compareValue);
                }
                elseif ($compareCommand === 'not') {
                    $queryBuilder->where($fieldName, '!=', $compareValue);
                }
                elseif ($compareCommand === 'more_than') {
                    $queryBuilder->where($fieldName, '>', $compareValue);
                }
                elseif ($compareCommand === 'less_than') {
                    $queryBuilder->where($fieldName, '<', $compareValue);
                }
                elseif ($compareCommand === 'more_than_equal') {
                    $queryBuilder->where($fieldName, '>=', $compareValue);
                }
                elseif ($compareCommand === 'less_than_equal') {
                    $queryBuilder->where($fieldName, '<=', $compareValue);
                }
                elseif ($compareCommand === 'in' || $compareCommand === 'in_all') {
                    // Don't handle in_all for relations, as it was already handled by applyInAllFilter()
                    if($compareCommand === 'in_all' && $relationName) {
                        continue;
                    }

                    if ($compareValue === '' || !$compareValue) {
                        $compareValue = [];
                    }

                    if (!is_array($compareValue)) {
                        $compareValue = explode(',', $compareValue);
                    }

                    $queryBuilder->whereIn($fieldName, $compareValue);
                }
                elseif ($compareCommand === 'like') {
                    // $likeWords = explode(' ', $compareValue);

                    // foreach ($likeWords as $keyword) {
                    //     $queryBuilder->where($fieldName, "LIKE","%$keyword%");
                    // }
                    $queryBuilder->where($fieldName, "LIKE","%$compareValue%");
                }
                elseif ($compareCommand === 'begins_with') {
                    // $likeWords = explode(' ', $compareValue);

                    // foreach ($likeWords as $keyword) {
                    //     $queryBuilder->where($fieldName, "LIKE","$keyword%");
                    // }
                    $queryBuilder->where($fieldName, "LIKE","$compareValue%");
                }
                elseif ($compareCommand === 'ends_with') {
                    // $likeWords = explode(' ', $compareValue);

                    // foreach ($likeWords as $keyword) {
                    //     $queryBuilder->where($fieldName, "LIKE","%$keyword");
                    // }
                    $queryBuilder->where($fieldName, "LIKE","%$compareValue");
                }
                else {
                    $relationName = "{$fieldName}_relation";

                    $this->applyInAllFilter($queryBuilder, $compareCriteria, $relationName);

                    $queryBuilder->whereHas($relationName, function ($query) use ($compareCriteria, $relationName) {
                        $this->applyFilter($query, $compareCriteria, $relationName);
                    });
                }
            }
        }
    }

    /**
     * Applies the in_all filter query parameters to the query builder, if one exists.
     *
     * @param QueryBuilder $queryBuilder
     * @param array $filterData
     * @param string $relationName
     */
    private function applyInAllFilter($queryBuilder, $filterData, $relationName) {
        foreach ($filterData as $fieldName => $compareCriteria) {
            foreach ($compareCriteria as $compareCommand => $compareValue) {
                if ($compareCommand === 'in_all') {

                    if ($compareValue === '' || !$compareValue) {
                        $compareValue = [];
                    }

                    if (!is_array($compareValue)) {
                        $compareValue = explode(',', $compareValue);
                    }

                    foreach($compareValue as $compareValueId){
                        $queryBuilder->whereHas($relationName, function($query) use ($compareValueId, $fieldName){
                            $query->where($fieldName, '=', $compareValueId);
                        });
                    }
                }
            }
        }
    }

    /**
     * Executes a query builder and paginates results.
     *
     * @param QueryBuilder $queryBuilder
     * @param array $paginationData
     * @return array
     */
    private function paginateResults($queryBuilder, $paginationData)
    {
        $itemsPerPage = (isset($paginationData['items_per_page'])) ? $paginationData['items_per_page'] : 10;
        $currentPage = (isset($paginationData['current_page'])) ? $paginationData['current_page'] : 1;

        \Illuminate\Pagination\Paginator::currentPageResolver(function() use ($currentPage) {
            return $currentPage;
        });

        $paginator = $queryBuilder->paginate($itemsPerPage);

        if ($paginator->currentPage() > $paginator->lastPage()) {
            $lastPage = $paginator->lastPage();
            \Illuminate\Pagination\Paginator::currentPageResolver(function() use ($lastPage) {
                return $lastPage;
            });
            $paginator = $queryBuilder->paginate($itemsPerPage);
        }

        return $paginator;
    }

    /**
     * Persists a single dynamic module entry into the DB.
     *
     * @param DynamicModuleInterface $entry
     * @return boolean
     */
    public function persist(DynamicModuleInterface &$entry)
    {
        $className = $this->modelClassName;

        // This is only for single entry module types
        if ($this->isSingleEntryModule()) {
            $instance = $this->retrieveFirst();
            // If the first entry haven't already been created, create it.
            if (!$instance) {
                $firstInstance = new $className;
                $firstInstance->save();
                $instance = $firstInstance;
            }
            // It is illegal for single entry module types to have more than one entry, thus index must allways be 1
            if ($entry->id > 1) {
                throw new PhotonException('SINGLE_ENTRY_MODULE_CAN_HAVE_ONLY_ONE_INSERT');
            }
            // If someone is trying to save without an ID, act as if has; goal is to update the entry.
            elseif (!$entry->id) {
                $entry->id = 1;
                $entry->exists = true;
            }
            // Perform a persist just to get updated values into the DB
            $entry->save();
            // Return a freshly loaded instance from the DB, because the data was never properly merged
            $entry = $instance->fresh();
            return true;
        }

        // Just a regular persist
        $entry->save();
        return true;
    }

    /**
     * Deletes a single dynamic module entry instance by id from the DB.
     *
     * @param int $id
     * @return boolean
     */
    public function deleteById($id)
    {
        $className = $this->modelClassName;

        // This is only for single entry module types
        if ($this->isSingleEntryModule()) {
            // It is illegal for single entry module types to delete their only entry.
            throw new PhotonException('SINGLE_ENTRY_MODULE_ENTRY_CANNOT_BE_DELETED');
        }

        $entry = $this->retrieve($id);
        if (!$entry) {
            throw new PhotonException('DYNAMIC_MODULE_ENTRY_NOT_FOUND', ['id' => $id]);
        }

        return $className::destroy($id);
    }

    /**
     * Deletes a module entry
     *
     * @param DynamicModuleInterface $entry
     * @return boolean
     */
    public function delete(DynamicModuleInterface $entry)
    {
        // This is only for single entry module types
        if ($this->isSingleEntryModule()) {
            // It is illegal for single entry module types to delete their only entry.
            throw new PhotonException('SINGLE_ENTRY_MODULE_ENTRY_CANNOT_BE_DELETED');
        }

        return $entry->delete();
    }

    /**
     * Returns the next available ID in the DB.
     *
     * @return int
     */
    public function getNextId()
    {
        $className = $this->modelClassName;

        return $className::max('id') + 1;
    }

    /**
     * Retrieves dynamic module entries by field name and relation value
     *
     * @param string $fieldName
     * @param int $relationId
     * @return type
     */
    public function retrieveByRelationValue($fieldName, $relationId)
    {
        $className = $this->modelClassName;

        return $className::where($fieldName, $relationId)->get();
    }

    // Baum Node related

    /**
     * Retrieves all root nodes.
     * If for any reason a module type which does not extend Node class is used, the method will check if it can fake Nodes out of it.
     *
     * @param array $filterData
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws PhotonException
     */
    public function retrieveRootNodes($filterData = [])
    {
        $className = $this->modelClassName;

        $nodeDummy = new $className();

        // If the class object will be real Node instances
        if ($nodeDummy instanceof Node) {
            $nodes = $className::whereNull($nodeDummy->getParentColumnName())
            ->orderBy($nodeDummy->getQualifiedOrderColumnName());
            foreach ($filterData as $key => $value) {
                $nodes = $nodes->where($key, $value);
            }
            $nodes = $nodes->get();
        }
        // Otherwise check if these entries can be faked to Nodes
        else {
            // Fake entries to nodes
            if ($nodeDummy instanceof CanFakeNodeInterface) {
                if (empty($filterData)) {
                    $nodes = $className::all();
                }
                else {
                    $nodes = $className::query();
                    foreach ($filterData as $key => $value) {
                        $nodes = $nodes->where($key, $value);
                    }
                    $nodes = $nodes->get();
                }
            }
            // Cannot fake entries into Nodes
            else {
                throw new PhotonException('NOT_A_NODE_MODULE', ['class' => $className]);
            }
        }

        return $nodes;
    }

    /**
     * Retrieves root nodes by a scope ID.
     *
     * @param int $scopeId
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws PhotonException
     */
    public function retrieveRootNodesByScopeId($scopeId)
    {
        $className = $this->modelClassName;

        $nodeDummy = new $className();

        if (!($nodeDummy instanceof Node)) {
            throw new PhotonException('NOT_A_NODE_MODULE', ['class' => $className]);
        }

        $nodes = $className::whereNull($nodeDummy->getParentColumnName())
            ->orderBy($nodeDummy->getQualifiedOrderColumnName())
            ->whereScopeId($scopeId)
            ->get();

        return $nodes;
    }

    /**
     * Retrieves all child nodes.
     *
     * @param \Photon\PhotonCms\Core\Entities\Node\Node $node
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function retrieveChildren(Node $node)
    {
        $children = $node->orderBy($node->getQualifiedOrderColumnName())
            ->where($node->getParentColumnName(), $node->id)
            ->get();

        return $children;
    }

    /**
     * A seed-type array of model data for mass insert into the DB.
     *
     * @param array $data
     * @return boolean
     */
    public function massInsert(array $data)
    {
        $columns = \Schema::getColumnListing($this->moduleTableName);

        $dataKeys = array_keys(reset($data));

        $definiteColumns = array_intersect($columns, $dataKeys);

        $insertData = [];

        foreach ($data as $insert) {
            $newInsert = [];
            foreach ($definiteColumns as $column) {
                if (key_exists($column, $insert)) {
                    $newInsert[$column] = $insert[$column];
                }
            }
            $insertData[] = $newInsert;
        }

        \Schema::disableForeignKeyConstraints();
        \DB::table($this->moduleTableName)->insert($insertData);
        \Schema::enableForeignKeyConstraints();

        return true;
    }

    /**
     * A seed-type array of pivot table data for mass insert into the DB.
     *
     * @param array $data
     * @return boolean
     */
    public function tableMassInsert(array $data, $table)
    {
        $columns = \Schema::getColumnListing($table);

        $dataKeys = array_keys(reset($data));

        $definiteColumns = array_intersect($columns, $dataKeys);

        $insertData = [];

        foreach ($data as $insert) {
            $newInsert = [];
            foreach ($definiteColumns as $column) {
                if (key_exists($column, $insert)) {
                    $newInsert[$column] = $insert[$column];
                }
            }
            $insertData[] = $newInsert;
        }

        \Schema::disableForeignKeyConstraints();
        \DB::table($table)->insert($insertData);
        \Schema::enableForeignKeyConstraints();

        return true;
    }

    /**
     * Backs up module data into php seed files.
     *
     * @return boolean
     */
    public function backupModuleData()
    {
        $this->clearTableBackedUpData($this->moduleTableName);
        $className = $this->modelClassName;
        $increment = 0;
        $className::chunk(1000, function ($data) use (&$increment) {
            $data = $data->toArray();

            $fileName = config('photon.php_seed_backup_location')."/{$this->moduleTableName}_{$increment}.php";

            $handle = fopen($fileName, "w");
            fwrite($handle, '<?php ');
            fwrite($handle, 'return \'');
            fwrite($handle, json_encode($data, JSON_HEX_APOS));
            fwrite($handle, '\';');
            fclose($handle);

            $increment++;
        });

        return true;
    }

    /**
     * Backs up pivot table data into php seed files.
     *
     * @return boolean
     */
    public function backupPivotTableData($pivotRelation)
    {
        $this->clearTableBackedUpData($pivotRelation->pivotTable);

        if(!\Schema::hasTable($pivotRelation->pivotTable)) {
            throw new PhotonException('PIVOT_TABLE_NOT_FOUND', ['pivot_table_name' => $pivotTableName]);
        }

        $increment = 0;
        \DB::table($pivotRelation->pivotTable)->orderBy($pivotRelation->sourcePivotField, "desc")->chunk(1000, function($data) use (&$increment, $pivotRelation) {
            $data = $data->toArray();

            $fileName = config('photon.php_seed_backup_location')."/{$pivotRelation->pivotTable}_{$increment}.php";

            $handle = fopen($fileName, "w");
            fwrite($handle, '<?php ');
            fwrite($handle, 'return \'');
            fwrite($handle, json_encode($data, JSON_HEX_APOS));
            fwrite($handle, '\';');
            fclose($handle);

            $increment++;
        });

        return true;
    }

    /**
     * Backs up system table data into php seed files.
     *
     * @return boolean
     */
    public function backupSystemTables()
    {        
        $systemTables = config('photon.photon_sync_backup_tables');

        foreach ($systemTables as $table) {
            $this->clearTableBackedUpData($table);

            $increment = 0;
            $columns = \DB::getSchemaBuilder()->getColumnListing($table);
            \DB::table($table)->orderBy($columns[0], "asc")->chunk(1000, function($data) use (&$increment, $table) {
                $data = $data->toArray();

                $fileName = config('photon.php_seed_backup_location')."/{$table}_{$increment}.php";

                $handle = fopen($fileName, "w");
                fwrite($handle, '<?php ');
                fwrite($handle, 'return \'');
                fwrite($handle, json_encode($data, JSON_HEX_APOS));
                fwrite($handle, '\';');
                fclose($handle);

                $increment++;
            });
        }

        return true;
    }

    private function clearTableBackedUpData($tableName)
    {
        $increment = 0;

        while (true) {
            $fileName = config('photon.php_seed_backup_location')."/{$tableName}_{$increment}.php";
            if (file_exists($fileName)) {
                unlink($fileName);
            }
            else {
                break;
            }
            $increment++;
        }
    }

    /**
     * Restores module data from php seed files.
     *
     * @return boolean
     */
    public function restoreBackedUpData()
    {
        $increment = 0;

        // Initial clear
        $fileName = config('photon.php_seed_backup_location')."/{$this->moduleTableName}_{$increment}.php";
        if (file_exists($fileName)) {
            DatabaseHelper::emptyTable($this->moduleTableName, true);
        }

        // Restoring data
        while (true) {
            $fileName = config('photon.php_seed_backup_location')."/{$this->moduleTableName}_{$increment}.php";
            if (file_exists($fileName)) {
                $data = include $fileName;
                $this->massInsert(json_decode($data, true));
            }
            else {
                break;
            }
            $increment++;
        }

        return true;
    }


    /**
     * Restores pivot table data from php seed files.
     *
     * @param string $pivotTableName
     * @return boolean
     */
    public function restorePivotTableData($pivotTableName)
    {
        $increment = 0;

        // Initial clear
        $fileName = config('photon.php_seed_backup_location')."/{$pivotTableName}_{$increment}.php";
        if (file_exists($fileName)) {
            DatabaseHelper::emptyTable($pivotTableName, true);
        }

        // Restoring data
        while (true) {
            $fileName = config('photon.php_seed_backup_location')."/{$pivotTableName}_{$increment}.php";

            if (file_exists($fileName)) {
                $data = include $fileName;
                $this->tableMassInsert(json_decode($data, true), $pivotTableName);
            }
            else {
                break;
            }
            $increment++;
        }

        return true;        
    }


    /**
     * Restores system tables data from php seed files.
     *
     * @param string $pivotTableName
     * @return boolean
     */
    public function restoreSystemTables()
    {
        $systemTables = config('photon.photon_sync_backup_tables');

        foreach ($systemTables as $table) {
            $increment = 0;

            // Initial clear
            $fileName = config('photon.php_seed_backup_location')."/{$table}_{$increment}.php";
            if (file_exists($fileName)) {
                DatabaseHelper::emptyTable($table, true);
            }

            // Restoring data
            while (true) {
                $fileName = config('photon.php_seed_backup_location')."/{$table}_{$increment}.php";
                if (file_exists($fileName)) {
                    $data = include $fileName;
                    $this->tableMassInsert(json_decode($data, true), $table);
                }
                else {
                    break;
                }
                $increment++;
            }
        }

        return true;
    }
}
