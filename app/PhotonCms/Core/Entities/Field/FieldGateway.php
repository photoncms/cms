<?php

namespace Photon\PhotonCms\Core\Entities\Field;

use Photon\PhotonCms\Core\Entities\Field\Contracts\FieldGatewayInterface;

/**
 * Decouples repository from data sources.
 */
class FieldGateway implements FieldGatewayInterface
{

    /**
     * Retrieves a Field instance by ID.
     *
     * @param int $id
     * @return Field
     */
    public function retrieve($id)
    {
        return Field::find($id);
    }

    /**
     * Retrieves a Field instance by module ID.
     *
     * @param int $id
     * @return Field
     */
    public function retrieveByModuleId($id)
    {
        return Field::whereModuleId($id)->get();
    }

    /**
     * Persists a Field instance into the DB.
     *
     * @param \Photon\PhotonCms\Core\Entities\Field\Field $field
     * @return boolean
     */
    public function persist(Field $field)
    {
        $field->save();
        return true;
    }

    /**
     * Removes a Field instance from the DB.
     *
     * @param \Photon\PhotonCms\Core\Entities\Field\Field $field
     * @return boolean
     */
    public function delete(Field $field)
    {
        return $field->delete();
    }

    /**
     * Returns the next available ID in the DB.
     *
     * @return int
     */
    protected function getNextId()
    {
        return Field::max('id') + 1;
    }

    /**
     * Retrieves fields by module ID.
     *
     * @param int $moduleId
     * @return Field
     */
    public function retrieveByRelatedModuleId($moduleId)
    {
        return Field::whereRelatedModule($moduleId)->get();
    }
}