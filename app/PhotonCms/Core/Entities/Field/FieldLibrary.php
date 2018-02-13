<?php

namespace Photon\PhotonCms\Core\Entities\Field;

use Photon\PhotonCms\Core\Entities\Field\FieldRepository;
use Photon\PhotonCms\Core\Entities\Field\Contracts\FieldGatewayInterface;
use Photon\PhotonCms\Core\Entities\FieldType\FieldTypeRepository;
use Photon\PhotonCms\Core\Entities\FieldType\FieldTypeGateway;

class FieldLibrary
{

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
     *
     * @param FieldRepository $fieldRepository
     * @param FieldGatewayInterface $fieldGateway
     * @param FieldTypeRepository $fieldTypeRepository
     * @param FieldTypeGateway $fieldTypeGateway
     */
    public function __construct(
        FieldRepository $fieldRepository,
        FieldGatewayInterface $fieldGateway,
        FieldTypeRepository $fieldTypeRepository,
        FieldTypeGateway $fieldTypeGateway
    )
    {
        $this->fieldRepository     = $fieldRepository;
        $this->fieldGateway        = $fieldGateway;
        $this->fieldTypeRepository = $fieldTypeRepository;
        $this->fieldTypeGateway    = $fieldTypeGateway;
    }

    /**
     * Retrieves field names recursively by module ID through relations.
     *
     * @param int $moduleId
     * @param array $acquiredRelations
     * @return mixed
     */
    public function findFieldNamesRecursivelyByModuleId($moduleId, array &$acquiredRelations = [])
    {
        if (!array_key_exists($moduleId, $acquiredRelations)) {
            $acquiredRelations[$moduleId] = [];
        }
        $fields = $this->fieldRepository->findByModuleId($moduleId, $this->fieldGateway);
        $fieldNames = [];
        foreach ($fields as $field) {
            $fieldType = $this->fieldTypeRepository->findById($field->type, $this->fieldTypeGateway);
            if ($fieldType->isAttribute() && $fieldType->isRelation()) {
                if (isset($acquiredRelations[$moduleId][$field->id][$field->related_module])) {
                    $fieldNames[$field->relation_name] = null;
                }
                else {
                    $acquiredRelations[$moduleId][$field->id][$field->related_module] = true;
                    $fieldNames[$field->relation_name] = $this->findFieldNamesRecursivelyByModuleId($field->related_module, $acquiredRelations);
                }
            }
            else {
                $fieldNames[$field->column_name] = null;
            }
        }
        return $fieldNames;
    }
}