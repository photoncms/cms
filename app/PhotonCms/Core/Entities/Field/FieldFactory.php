<?php

namespace Photon\PhotonCms\Core\Entities\Field;

/**
 * Handles object manipulation.
 */
class FieldFactory
{
    /**
     * Creates a Field instance and constructs it with passed data.
     *
     * @param type $data
     * @return \Photon\PhotonCms\Core\Entities\Field\Field
     */
    public static function make($data = [])
    {
        return new Field($data);
    }

    /**
     *
     * Makes an empty instance of an ORM Field.
     *
     * This should never be persisted!
     *
     * @return \Photon\PhotonCms\Core\Entities\Field\Field
     */
    public static function makeEmpty()
    {
        $emptyField = new Field();
        $emptyField->isEmpty = true;

        return $emptyField;
    }

    /**
     * Replaces all data including restricted fields from data array.
     *
     * Useful for reverting from backup.
     *
     * @param \Photon\PhotonCms\Core\Entities\Field\Field $field
     * @param array $data
     */
    public static function replaceData(Field $field, array $data)
    {
        $attributes = [
            'id',
            'type',
            'name',
            'related_module',
            'relation_name',
            'pivot_table',
            'column_name',
            'virtual_name',
            'tooltip_text',
            'validation_rules',
            'module_id',
            'order',
            'editable',
            'disabled',
            'hidden',
            'is_system',
            'virtual',
            'lazy_loading',
            'nullable',
            'indexed',
            'default',
            'local_key',
            'foreign_key',
            'is_default_search_choice',
            'can_create_search_choice',
            'active_entry_filter',
            'flatten_to_optgroups',
            'unique_name',
            'created_at',
            'updated_at'
        ];
        
        foreach ($data as $propertyName => $value) {
            if (in_array($propertyName, $attributes)) {
                $field->$propertyName = $value;
            }
        }
    }
}

