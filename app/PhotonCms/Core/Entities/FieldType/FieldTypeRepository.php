<?php

namespace Photon\PhotonCms\Core\Entities\FieldType;

use Photon\PhotonCms\Core\Exceptions\PhotonException;

class FieldTypeRepository
{

    /**
     * Retrieves all field types.
     *
     * @param \Photon\PhotonCms\Core\Entities\FieldType\FieldTypeGateway $fieldTypeGateway
     * @return Collection
     */
    public function getAll(FieldTypeGateway $fieldTypeGateway)
    {
        return $fieldTypeGateway->retrieveAll();
    }

    /**
     * Static method for searching of the field type by ID.
     *
     * @param int $id
     * @return FieldType
     * @throws PhotonException
     */
    public static function findByIdStatic($id)
    {
        // $fieldType = FieldTypeGateway::retrieveStatic($id);
        $fieldType = FieldTypeGateway::retrieveArrayStatic($id);

        if ($fieldType) {
            // return FieldTypeFactory::makeFromBaseObject($fieldType);
            $fieldType['id'] = $id;
            return FieldTypeFactory::makeFromBaseArray($fieldType);
        }
        else {
            throw new PhotonException('FIELD_TYPE_DOESNT_EXIST', ['id' => $id]);
        }
    }

    /**
     * Finds a field type by ID.
     *
     * @param int $id
     * @param \Photon\PhotonCms\Core\Entities\FieldType\FieldTypeGateway $fieldTypeGateway
     * @return FieldType
     * @throws PhotonException
     */
    public function findById($id, FieldTypeGateway $fieldTypeGateway)
    {
        $fieldType = $fieldTypeGateway->retrieve($id);

        if ($fieldType) {
            return FieldTypeFactory::makeFromBaseObject($fieldType);
        }
        else {
            throw new PhotonException('FIELD_TYPE_DOESNT_EXIST', ['id' => $id]);
        }
    }

    /**
     * Checks if a FieldType is a relation by using its ID.
     *
     * @param int $id
     * @return boolean
     */
    public function isRelation($id)
    {
        $fieldType = $this->findById($id);

        return $fieldType->isRelation();
    }

    /**
     * Preloads all FieldType entries into the ORM.
     * This prevents multiple querying during runtime.
     *
     * @param \Photon\PhotonCms\Core\Entities\FieldType\FieldTypeGateway $fieldTypeGateway
     */
    public function preloadAll(FieldTypeGateway $fieldTypeGateway)
    {
        $fieldTypeGateway->retrieveAll();
    }
}