<?php

namespace Photon\PhotonCms\Core\Entities\ModelAttribute;

use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Photon\PhotonCms\Core\Entities\Field\Field;
use Photon\PhotonCms\Core\Entities\Field\FieldTransformer;
use Photon\PhotonCms\Core\Entities\FieldType\FieldTypeRepository;

class ModelAttributeFactory
{
    /**
     * Makes a properly set instance of a ModelAttribute.
     *
     * @param array $data
     * @return \Photon\PhotonCms\Core\Entities\ModelAttribute\ModelAttribute
     * @throws BaseException
     */
    public static function make(array $data)
    {
        if (!isset($data['name'])) {
            throw new PhotonException('ATTRIBUTE_NAME_MISSING');
        }
        $name        = $data['name'];
        $fieldType   = (key_exists('field_type', $data))
            ? $data['field_type']
            : $data['type'];
        $laravelType = (key_exists('laravel_type', $data)) ? $data['laravel_type'] : null;
        $visibility  = (key_exists('visibility', $data)) ? $data['visibility'] : 'public';
        $default     = (key_exists('default', $data)) ? $data['default'] : null;
        $nullable    = (key_exists('nullable', $data)) ? $data['nullable'] : false;
        $indexed    = (key_exists('indexed', $data)) ? $data['indexed'] : false;
        $fillable    = (key_exists('fillable', $data)) ? $data['fillable'] : true;
        $unique      = (key_exists('unique', $data)) ? $data['unique'] : false;
        $parameters  = (key_exists('parameters', $data)) ? $data['parameters'] : [];

        $modelAttribute = new ModelAttribute($name, $fieldType, $laravelType, $visibility, $default, $nullable, $indexed, $fillable, $unique, $parameters);

        return $modelAttribute;
    }

    public static function makeMultiple(array $data)
    {
        $attributes = [];

        foreach ($data as $attributeData) {
            $attributes[] = self::make($attributeData);
        }

        return $attributes;
    }

    public static function makeFromField(Field $field)
    {
        $transformer = new FieldTransformer();
        $attributeData = $transformer->transform($field);
        $attributeData['name'] = $field->column_name;

        return self::make($attributeData);
    }

    public static function makeMultipleFromFields($fields)
    {
        $attributes = [];
        foreach ($fields as $field) {
            $fieldType = FieldTypeRepository::findByIdStatic($field->type);

            // Relations
            if (!$fieldType->isRelation() && !$field->virtual) {
                $attributes[] = self::makeFromField($field);
            }
        }
        return $attributes;
    }

    public static function makeMultipleFromFieldDataArray($fieldsDataArray)
    {
        $attributes = [];
        foreach ($fieldsDataArray as $fieldData) {
            // No attributes or relations for a virtual field
            if (key_exists('virtual', $fieldData) && ($fieldData['virtual'] == 1 || $fieldData['virtual'] = true)) {
                continue;
            }

            $fieldType = FieldTypeRepository::findByIdStatic($fieldData['type']);

            if (!$fieldType->isRelation()) {
                $attributeData = [
                    'name' => $fieldData['column_name'],
                    'field_type' => $fieldType
                ];

                if (key_exists('visibility', $fieldData)) { // Not implemented yet
                    $attributeData['visibility'] = $fieldData['visibility'];
                }
                if (key_exists('default', $fieldData)) {
                    $attributeData['default'] = $fieldData['default'];
                }
                if (key_exists('nullable', $fieldData)) {
                    $attributeData['nullable'] = $fieldData['nullable'];
                }
                if (key_exists('indexed', $fieldData)) {
                    $attributeData['indexed'] = $fieldData['indexed'];
                }
                if (key_exists('fillable', $fieldData)) { // Not implemented yet
                    $attributeData['fillable'] = $fieldData['fillable'];
                }
                if (key_exists('unique', $fieldData)) { // Not implemented yet
                    $attributeData['unique'] = $fieldData['unique'];
                }

                $attributes[] = self::make($attributeData);
            }
        }
        return $attributes;
    }
}