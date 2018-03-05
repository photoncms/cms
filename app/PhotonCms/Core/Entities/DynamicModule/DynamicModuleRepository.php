<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModule;

use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Photon\PhotonCms\Core\Entities\DynamicModule\DynamicModuleGateway;
use Photon\PhotonCms\Core\Entities\DynamicModule\DynamicModuleFactory;
use Photon\PhotonCms\Core\Entities\DynamicModule\Contracts\DynamicModuleInterface;

/**
 * Decouples buisiness logic from object storage, manipulation and internal logic over DynamicModule entity.
 */
class DynamicModuleRepository
{

    /**
     * Retrieves a dynamic module entry instance by id.
     *
     * @param int $id
     * @param DynamicModuleGateway $gateway
     * @return mixed
     */
    public function findById($id, DynamicModuleGateway $gateway)
    {
        return $gateway->retrieve($id);
    }

    /**
     * Retrieves all dynamic module entries.
     *
     * @param DynamicModuleGateway $gateway
     * @param array $filter
     * @param array $pagination
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(DynamicModuleGateway $gateway, $filter = null, $pagination = null, $sorting = null)
    {
        return $gateway->retrieveAll($filter, $pagination, $sorting);
    }

    /**
     * Saves a dynamic module entry from data and returns the persisted instance.
     * Since the entry is being persisted within this method, fre-save and post-save events are fired during the process.
     *
     * @param array $data
     * @param DynamicModuleGateway $gateway
     * @param DynamicModuleFactory $factory
     * @return mixed
     * @throws PhotonException
     */
    public function saveFromData(array $data, DynamicModuleGateway $gateway, DynamicModuleFactory $factory)
    {
        if (isset($data['id']) && is_numeric($data['id']) && $data['id'] > 0) {
            $entry = $gateway->retrieve($data['id']);


            if (is_null($entry)) {
                throw new PhotonException('DYNAMIC_MODULE_ENTRY_NOT_FOUND', ['id' => $data['id']]);
            }
            // Prepare a clone of the object before saving so that it can be used for pre and post save events as well as for additional actions.
            $entry->prepareCloneBeforePersist();

            $entry->setAll($data);
        }
        else {
            $entry = $factory->make($data);
        }
        
        // Prepare a clone of the object before saving after its data has been updated so that it can be used for pre and post save events as well as for additional actions.
        $entry->prepareCloneAfterPersist();

        $entry->firePreSaveEvents($data);
        
        if ($gateway->persist($entry)) {

            $entry->firePostSaveEvents($data);
            return $entry;
        }
        else {
            throw new PhotonException('SAVE_DYNAMIC_MODULE_ENTRY_FAILURE', ['data' => $data]);
        }
    }

    /**
     * Saves an entry and returns it to the output.
     *
     * @param mixed $entry
     * @param DynamicModuleGateway $gateway
     * @return mixed
     */
    public function save($entry, DynamicModuleGateway $gateway)
    {
        return $gateway->persist($entry);
    }

    /**
     * Deletes a dynamic module entry instance.
     * This method doesn't actually delete the entry, but instead it loads it and sends it to $this->delete().
     *
     * @param int $id
     * @param DynamicModuleGateway $gateway
     * @return boolean
     * @throws PhotonException
     */
    public function deleteById($id, DynamicModuleGateway $gateway)
    {
        if (!is_numeric($id) || $id < 1) {
            throw new PhotonException('DYNAMIC_MODULE_ENTRY_INVALID_ID', ['id' => $id]);
        }

        return $this->delete($gateway->retrieve($id), $gateway);
    }

    /**
     * Deletes a module entry.
     * Since the entry is being persisted within this method, fre-delete and post-delete events are fired during the process.
     *
     * @param DynamicModuleInterface $entry
     * @param DynamicModuleGateway $gateway
     * @return boolean
     */
    public function delete(DynamicModuleInterface $entry, DynamicModuleGateway $gateway)
    {
        $entry->prepareCloneBeforePersist();
        
        $entry->firePreDeleteEvents();

        $result = $gateway->delete($entry);

        $entry->firePostDeleteEvents();

        return $result;
    }

    /**
     * Retrieves entries by a relation field name and value.
     *
     * @param string $fieldName
     * @param int $relationId
     * @param DynamicModuleGateway $gateway
     * @return Collection
     */
    public function retrieveByRelationValue($fieldName, $relationId, DynamicModuleGateway $gateway)
    {
        return $gateway->retrieveByRelationValue($fieldName, $relationId);
    }

    /**
     * Backs up module data.
     *
     * @param DynamicModuleGateway $gateway
     * @return boolean
     */
    public function backupModuleData(DynamicModuleGateway $gateway)
    {
        return $gateway->backupModuleData();
    }

    /**
     * Backs up pivot tables data.
     *
     * @param string $pivotTableName
     * @param DynamicModuleGateway $gateway
     * @return boolean
     */
    public function backupPivotTableData($pivotTableName, DynamicModuleGateway $gateway)
    {
        return $gateway->backupPivotTableData($pivotTableName);
    }

    /**
     * Backs up system tables data.
     *
     * @param DynamicModuleGateway $gateway
     * @return boolean
     */
    public function backupSystemTables(DynamicModuleGateway $gateway)
    {
        return $gateway->backupSystemTables();
    }

    /**
     * Restores module data.
     *
     * @param DynamicModuleGateway $gateway
     * @return boolean
     */
    public function restoreModuleData(DynamicModuleGateway $gateway)
    {
        return $gateway->restoreBackedUpData();
    }

    /**
     * Restores pivot table data.
     *
     * @param string $pivotTableName
     * @param DynamicModuleGateway $gateway
     * @return boolean
     */
    public function restorePivotTableData($pivotTableName, DynamicModuleGateway $gateway)
    {
        return $gateway->restorePivotTableData($pivotTableName);
    }

    public function restoreSystemTables(DynamicModuleGateway $gateway)
    {        
        return $gateway->restoreSystemTables();
    }
}