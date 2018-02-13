<?php

namespace Photon\PhotonCms\Core\Entities\Module;

use Photon\PhotonCms\Core\Entities\Module\ModuleRepository;
use Photon\PhotonCms\Core\Entities\Module\Contracts\ModuleGatewayInterface;
use Photon\PhotonCms\Core\Exceptions\PhotonException;

class ModuleLibrary
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
     * @param ModuleRepository $moduleRepository
     * @param ModuleGatewayInterface $moduleGateway
     */
    public function __construct(
        ModuleRepository $moduleRepository,
        ModuleGatewayInterface $moduleGateway
    )
    {
        $this->moduleRepository = $moduleRepository;
        $this->moduleGateway    = $moduleGateway;
    }

    /**
     * Gets all available Module instances.
     *
     * @param int $moduleId
     * @param array $acquiredModuleIds
     * @return mixed
     */
    public function getAllModules()
    {
        $modules = $this->moduleRepository->getAll($this->moduleGateway);

        return $modules;
    }

    /**
     * Finds a module by its table name.
     *
     * @param string $tableName
     * @return \Photon\PhotonCms\Core\Entities\Module\Module
     */
    public function findByTableName($tableName)
    {
        $module = $this->moduleRepository->findModuleByTableName($tableName, $this->moduleGateway);

        return $module;
    }

    /**
     * Finds a module by its table name.
     *
     * @param string $tableName
     * @return \Photon\PhotonCms\Core\Entities\Module\Module
     */
    public static function findByTableNameStatic($tableName)
    {
        return ModuleRepository::findByTableNameStatic($tableName);
    }

    /**
     * Retrieves module anchor text stub using an entry of that module.
     *
     * @param object $entry
     * @return string
     * @throws PhotonException
     */
    public function getAnchorTextByEntryObject($entry)
    {
        $tableName = $entry->getTable();

        $module = $this->findByTableName($tableName);

        if (!$module) {
            throw new PhotonException('MODULE_NOT_FOUND', ['table_name' => $tableName]);
        }

        return $module->anchor_text;
    }

    /**
     * Checks if the module exists by table name.
     *
     * @param string $tableName
     * @return boolean
     */
    public function checkIfModuleExistsByTableName($tableName)
    {
        return (bool) $this->findByTableName($tableName);
    }
}