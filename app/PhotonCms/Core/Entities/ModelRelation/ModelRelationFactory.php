<?php

namespace Photon\PhotonCms\Core\Entities\ModelRelation;

use Photon\PhotonCms\Core\Entities\ModelRelation\RelationTypes\ManyToOne;
use Photon\PhotonCms\Core\Entities\ModelRelation\RelationTypes\ManyToMany;
use Photon\PhotonCms\Core\Entities\FieldType\FieldTypeRepository;
use Photon\PhotonCms\Core\Entities\Module\ModuleRepository;

use Photon\PhotonCms\Core\Exceptions\PhotonException;

class ModelRelationFactory
{
    /**
     * Makes an instance of a relation type.
     *
     * @param string $type
     * @return mixed
     * @throws PhotonException
     */
    public static function make($fieldType)
    {
        $className = str_replace(' ', '', ucwords(str_replace('_', ' ', $fieldType->getRelationType())));;

        $class = "\Photon\PhotonCms\Core\Entities\ModelRelation\RelationTypes\\$className";

        if (!class_exists($class)) {
            throw new PhotonException('CLASS_DOESNT_EXIST', ['class' => $class]);
        }

        // Remove the first argument from the call and pass the rest of them to the relation constructor
        $arguments = func_get_args();
//        array_shift($arguments);

        $reflect = new \ReflectionClass($class);

        return $reflect->newInstanceArgs($arguments);
    }

    public static function makeMultipleFromFields($fields)
    {
        $relations = [];
        foreach ($fields as $field) {
            if ($field->virtual) {
                continue;
            }
            $fieldType = FieldTypeRepository::findByIdStatic($field->type);

            if ($fieldType->isRelation()) {
                $relatedModule = ModuleRepository::findByIdStatic($field->related_module);

                if (is_null($relatedModule)) {
                    throw new PhotonException('MODULE_NOT_FOUND', ['id' => $field->related_module]);
                }

                $relations[] = ModelRelationFactory::make(
                    $fieldType,
                    $field->relation_name,
                    \Config::get('photon.dynamic_models_namespace').'\\'.$relatedModule->model_name,
                    $field->module->table_name,
                    $relatedModule->table_name,
                    (($field->local_key) ? $field->local_key : $field->relation_name),                    
                    (($field->foreign_key) ? $field->foreign_key : null),
                    (($field->pivot_table) ? $field->pivot_table : ''),
                    (($field->nullable) ? $field->nullable : null)
                );
            }
        }
        return $relations;
    }

    public static function makeMultipleFromFieldDataArray($fieldsDataArray, $moduleTableName)
    {
        $relations = [];
        foreach ($fieldsDataArray as $fieldData) {
            // No attributes or relations for a virtual field
            if (key_exists('virtual', $fieldData) && ($fieldData['virtual'] == 1 || $fieldData['virtual'] == true)) {
                continue;
            }

            $fieldType = FieldTypeRepository::findByIdStatic($fieldData['type']);
            // Relations
            if ($fieldType->isRelation()) {
                $relatedModule = ModuleRepository::findByIdStatic($fieldData['related_module']);

                if (is_null($relatedModule)) {
                    throw new PhotonException('MODULE_NOT_FOUND', ['id' => $fieldData['related_module']]);
                }

                $relations[] = ModelRelationFactory::make(
                    $fieldType,
                    $fieldData['relation_name'],
                    \Config::get('photon.dynamic_models_namespace').'\\'.$relatedModule->model_name,
                    $moduleTableName,
                    $relatedModule->table_name,
                    ((key_exists('local_key', $fieldData)) ? $fieldData['local_key'] : $fieldData['relation_name']),
                    ((key_exists('foreign_key', $fieldData)) ? $fieldData['foreign_key'] : null),
                    ((key_exists('pivot_table', $fieldData)) ? $fieldData['pivot_table'] : ''),
                    ((key_exists('nullable', $fieldData)) ? $fieldData['nullable'] : null)
                );
            }
        }
        return $relations;
    }
}