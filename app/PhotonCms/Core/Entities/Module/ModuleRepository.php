<?php

namespace Photon\PhotonCms\Core\Entities\Module;

use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Photon\PhotonCms\Core\Entities\Module\Contracts\ModuleGatewayInterface;
use Photon\PhotonCms\Core\Entities\Module\ModuleGateway;

/**
 * Decouples buisiness logic from object storage, manipulation and internal logic over Module entity.
 */
class ModuleRepository
{

    /**
     * Retrieves all modules from modules table.
     *
     * @param ModuleGateway $moduleGateway
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(ModuleGatewayInterface $moduleGateway)
    {
        return $moduleGateway->retrieveAll();
    }

    /**
     * Retrieves all multilevel-sortable-module instances of Module from the modules table.
     *
     * @param ModuleGatewayInterface $moduleGateway
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllMultilevelSortable(ModuleGatewayInterface $moduleGateway)
    {
        return $moduleGateway->retrieveAllByType('multilevel_sortable');
    }

    /**
     * Retrieves a module from the modules table by id.
     *
     * @param int $id
     * @param ModuleGatewayInterface $moduleGateway
     * @return Module
     */
    public function findById($id, ModuleGatewayInterface $moduleGateway)
    {
        return $moduleGateway->retrieve($id);
    }

    /**
     * Retrieves a module from the modules table by id.
     *
     * @param int $id
     * @param ModuleGatewayInterface $moduleGateway
     * @return Module
     */
    public static function findByIdStatic($id)
    {
        return ModuleGateway::retrieveStatic($id);
    }

    /**
     * Retrieves a module from the modules table by table name.
     *
     * @param string $tableName
     * @return Module
     */
    public static function findByTableNameStatic($tableName)
    {
        return ModuleGateway::retrieveByTableNameStatic($tableName);
    }

    /**
     * Retrieves a module from the modules table.
     *
     * @param string $tableName
     * @param ModuleGatewayInterface $moduleGateway
     * @return Module
     */
    public function findModuleByTableName($tableName, ModuleGatewayInterface $moduleGateway)
    {
        return $moduleGateway->retrieveByTableName($tableName);
    }

    /**
     * Retrieves a parent scope module for the given module.
     *
     * @param \Photon\PhotonCms\Core\Entities\Module\Module $module
     * @param ModuleGatewayInterface $moduleGateway
     * @return Module
     */
    public function findParentScopeModule(Module $module, ModuleGatewayInterface $moduleGateway)
    {
        return $moduleGateway->retrieveParentScope($module);
    }

    /**
     * Retrieves a Collection of Module instances by scope ID.
     *
     * @param int $id
     * @param ModuleGatewayInterface $moduleGateway
     * @return Collection
     */
    public function findScopedModulesByParentId($id, ModuleGatewayInterface $moduleGateway)
    {
        return $moduleGateway->retrieveScopedModulesByParentId($id);
    }

    /**
     * Saves a module into the modules table.
     *
     * If there is no id set in the input array, a new module will be created.
     *
     * @param array $data
     * @param ModuleGatewayInterface $moduleGateway
     * @return Module
     * @throws PhotonException
     */
    public function saveFromData($data, ModuleGatewayInterface $moduleGateway)
    {
        if (isset($data['id']) && is_numeric($data['id']) && $data['id'] > 0) {
            $module = $moduleGateway->retrieve($data['id']);

            // ToDo: Insert handling for restricted fields - throw exceptions. Requires a transaction controller in ModuleController::update() (Sasa|02/2016)
            
            if (is_null($module)) {
                throw new PhotonException('MODULE_NOT_FOUND', ['id' => $data['id']]);
            }

            if (isset($data['category']) && (is_numeric($data['category']) && $data['category'] > 0 || $data['category'] === null || $data['category'] === 'null')) {
                $module->category = $data['category'];
            }

            if (isset($data['name']) && $data['name'] !== '') {
                $module->name = $data['name'];
            }

            if (array_key_exists('anchor_text', $data)) {
                $module->anchor_text = $data['anchor_text'];
            }

            if (array_key_exists('anchor_html', $data)) {
                $module->anchor_html = $data['anchor_html'];
            }

            if (array_key_exists('slug', $data)) {
                $module->slug = $data['slug'];
            }

            if (isset($data['max_depth']) && (is_numeric($data['max_depth']) && $data['max_depth'] > 0 || $data['max_depth'] === null)) {
                $module->max_depth = $data['max_depth'];
            }

            if (isset($data['icon'])) {
                $module->icon = $data['icon'];
            }

            if (isset($data['reporting'])) {
                $module->reporting = $data['reporting'];
            }
        }
        else {
            $module = ModuleFactory::make($data);
        }

        if ($moduleGateway->persist($module)) {
            return $module;
        }
        else {
            throw new PhotonException('MODULE_SAVE_FROM_DATA_FAILED', ['data' => $data]);
        }
    }

    /**
     * Updates the whole module with restricted fields as well.
     *
     * Usefull for reverting from backups.
     *
     * @param array $data
     * @param ModuleGatewayInterface $moduleGateway
     * @return Module
     * @throws PhotonException
     */
    public function fullUpdateFromData(array $data, ModuleGatewayInterface $moduleGateway)
    {
        $module = $moduleGateway->retrieve($data['id']);

        if (is_null($module)) {
            throw new PhotonException('MODULE_NOT_FOUND', ['id' => $data['id']]);
        }

        ModuleFactory::replaceData($module, $data);

        if ($moduleGateway->persist($module)) {
            return $module;
        }
        else {
            throw new PhotonException('MODULE_SAVE_FROM_DATA_FAILED', ['data' => $data]);
        }
    }

    /**
     * Removes a Module from modules table by id.
     *
     * @param int $id
     * @param ModuleGatewayInterface $moduleGateway
     * @throws PhotonException
     */
    public function deleteById($id, ModuleGatewayInterface $moduleGateway)
    {
        if (!is_numeric($id) || $id < 1) {
            throw new PhotonException('MODULE_INVALID_ID', ['id' => $id]);
        }

        return $moduleGateway->deleteById($id);
    }
}