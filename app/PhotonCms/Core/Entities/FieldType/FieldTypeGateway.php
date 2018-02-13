<?php

namespace Photon\PhotonCms\Core\Entities\FieldType;

class FieldTypeGateway
{
    /**
     * Retrieves a FieldType by id as a static method.
     *
     * @param int $id
     * @return FieldType
     */
    public static function retrieveStatic($id)
    {
        return FieldType::find($id);
    }
    
    /**
     * Retrieves a FieldType by id as a static method.
     *
     * @param int $id
     * @return FieldType
     */
    public static function retrieveArrayStatic($id)
    {
        return config("field-types." . $id);
    }

    /**
     * Retrieves a FieldType by ID.
     *
     * @param int $id
     * @return FieldType
     */
    public function retrieve($id)
    {
        return FieldType::find($id);
    }

    /**
     * Retrieves all FieldTypes
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function retrieveAll()
    {
        return FieldType::all();
    }
}