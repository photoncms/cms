<?php

namespace Photon\PhotonCms\Core\Entities\Module;

use Photon\PhotonCms\Core\Exceptions\PhotonException;

use Photon\PhotonCms\Core\Entities\Module\Contracts\ModuleGatewayInterface;

/**
 * Decouples repository from data sources.
 */
class ModuleGateway implements ModuleGatewayInterface
{
    /**
     * List of available module types
     *
     * @var array
     */
    private static $availableTypes = [
        'sortable_module',
        'multilevel_sortable',
        'non_sortable'
    ];

    /**
     * Retrieves all Module instances from the modules table.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function retrieveAll()
    {
        return Module::all();
    }

    /**
     * Retrieves all Module instances of the specified type.
     *
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws PhotonException
     */
    public function retrieveAllByType($type)
    {
        if (!in_array($type, self::$availableTypes)) {
            throw new PhotonException('WRONG_MODULE_TYPE', ['type' => $type]);
        }
        
        return Module::whereType($type)->get();
    }

    /**
     * Retrieves a Module instance by id.
     *
     * @param int $id
     * @return Module
     */
    public function retrieve($id)
    {
        return Module::find($id);
    }

    /**
     * Retrieves a Module instance by id.
     *
     * @param int $id
     * @return Module
     */
    public static function retrieveStatic($id)
    {
        return Module::find($id);
    }

    /**
     * Retrieves a Module instance by table name.
     *
     * @param string $tableName
     * @return Module
     */
    public static function retrieveByTableNameStatic($tableName)
    {
        return Module::whereTableName($tableName)->first();
    }

    /**
     * Retrieves a Module instance by table name.
     *
     * @param string $name
     * @return Module
     */
    public function retrieveByTableName($name)
    {
        return Module::whereTableName($name)->first();
    }

    /**
     * Retrieves a module which represents the parent scope for the given module.
     *
     * @param \Photon\PhotonCms\Core\Entities\Module\Module $module
     * @return Module
     */
    public function retrieveParentScope(Module $module)
    {
        return Module::find($module->category);
    }

    /**
     * Retrieves all modules by their parent scope module.
     *
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function retrieveScopedModulesByParentId($id)
    {
        return Module::whereCategory($id)->get();
    }

    /**
     * Persists a Module instance into the DB.
     *
     * @param \Photon\PhotonCms\Core\Entities\Module\Module $module
     * @return boolean
     */
    public function persist(Module $module)
    {
        $module->save();
        return true;
    }

    /**
     * Deletes a Module instance by ID.
     *
     * @param int $id
     * @return boolean
     */
    public function deleteById($id)
    {
        return Module::destroy($id);
    }

    /**
     * Returns the next available ID in the DB.
     *
     * @return int
     */
    public function getNextId()
    {
        return Module::max('id') + 1;
    }
}