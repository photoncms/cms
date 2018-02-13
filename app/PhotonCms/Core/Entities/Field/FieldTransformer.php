<?php

namespace Photon\PhotonCms\Core\Entities\Field;

use Photon\PhotonCms\Core\Entities\Field\Field;
use Photon\PhotonCms\Core\Transform\BaseTransformer;
use Photon\PhotonCms\Core\Transform\Contracts\TransformerFullTransformInterface;
use Photon\PhotonCms\Core\Transform\Contracts\TransformerFullTransformConvertedInterface;
use Photon\PhotonCms\Core\Exceptions\PhotonException;

/**
 * Transforms Field instances into various output packages.
 */
class FieldTransformer extends BaseTransformer implements TransformerFullTransformInterface, TransformerFullTransformConvertedInterface
{

    /**
     * Transforms an object into a generic array
     *
     * @var  Field $object
     * @return array
     */
    public function transform(Field $object)
    {
        if ($object->isEmpty) {
            $objectArray = $this->getEmptyObjectRepresentation();
        }
        else {
            $objectArray = [
                'id' => ($object->id !== null) ? (int) $object->id : $object->id,
                'type' => (int) $object->type,
                'name' => $object->name,
                'related_module' => ($object->related_module !== null) ? (int) $object->related_module : $object->related_module,
                'relation_name' => $object->relation_name,
                'pivot_table' => $object->pivot_table,
                'column_name' => $object->column_name,
                'virtual_name' => $object->virtual_name,
                'tooltip_text' => $object->tooltip_text,
                'validation_rules' => $object->validation_rules,
                'module_id' => (int) $object->module_id,
                'order' => (int) $object->order,
                'editable' => (bool) $object->editable,
                'disabled' => (bool) $object->disabled,
                'hidden' => (bool) $object->hidden,
                'is_system' => (bool) $object->is_system,
                'virtual' => (bool) $object->virtual,
                'lazy_loading' => (bool) $object->lazy_loading,
                'default' => $object->default,
                'local_key' => $object->local_key,
                'foreign_key' => $object->foreign_key,
                'is_default_search_choice' => (bool) $object->is_default_search_choice,
                'can_create_search_choice' => (bool) $object->can_create_search_choice,
                'active_entry_filter' => $object->active_entry_filter,
                'flatten_to_optgroups' => (bool) $object->flatten_to_optgroups,
                'nullable' => (bool) $object->nullable,
                'indexed' => (bool) $object->indexed,
                'unique_name' => $object->getUniqueName(),
                'created_at' => $object->created_at,
                'updated_at' => $object->updated_at
            ];
        }

        $this->transformGenericObjects($objectArray);

        return $objectArray;
    }

    /**
     * Transforms the whole object into an array.
     * Passes each attribute without further conversion.
     *
     * @param Field $object
     */
    public function fullTransform($object)
    {
        if (!($object instanceof Field)) {
            throw new PhotonException('NOT_INSTANCE_OF_FIELD', ['given' => get_class($object), 'expected' => '\Photon\PhotonCms\Core\Entities\Field\Field']);
        }

        if ($object->isEmpty) {
            return $this->getEmptyObjectRepresentation();
        }
        else {
            return [
                'id' => ($object->id !== null) ? (int) $object->id : $object->id,
                'type' => (int) $object->type,
                'name' => $object->name,
                'related_module' => ($object->related_module !== null) ? (int) $object->related_module : $object->related_module,
                'relation_name' => $object->relation_name,
                'pivot_table' => $object->pivot_table,
                'column_name' => $object->column_name,
                'virtual_name' => $object->virtual_name,
                'tooltip_text' => $object->tooltip_text,
                'validation_rules' => $object->validation_rules,
                'module_id' => (int) $object->module_id,
                'order' => (int) $object->order,
                'editable' => (bool) $object->editable,
                'disabled' => (bool) $object->disabled,
                'hidden' => (bool) $object->hidden,
                'is_system' => (bool) $object->is_system,
                'virtual' => (bool) $object->virtual,
                'lazy_loading' => (bool) $object->lazy_loading,
                'default' => $object->default,
                'nullable' => (bool) $object->nullable,
                'local_key' => $object->local_key,
                'foreign_key' => $object->foreign_key,
                'is_default_search_choice' => (bool) $object->is_default_search_choice,
                'can_create_search_choice' => (bool) $object->can_create_search_choice,
                'active_entry_filter' => $object->active_entry_filter,
                'flatten_to_optgroups' => (bool) $object->flatten_to_optgroups,
                'nullable' => (bool) $object->nullable,
                'unique_name' => $object->getUniqueName(),
                'indexed' => (bool) $object->indexed,
                'created_at' => $object->created_at,
                'updated_at' => $object->updated_at,
            ];
        }
    }

    /**
     * Transforms the whole object into an array.
     * Converts each attribute if necessary.
     *
     * @param Field $object
     */
    public function fullTransformConverted($object)
    {
        if (!($object instanceof Field)) {
            throw new PhotonException('NOT_INSTANCE_OF_FIELD', ['given' => get_class($object), 'expected' => '\Photon\PhotonCms\Core\Entities\Field\Field']);
        }

        if ($object->isEmpty) {
            $objectArray = $this->getEmptyObjectRepresentation();
        }
        else {
            $objectArray = [
                'id' => ($object->id !== null) ? (int) $object->id : $object->id,
                'type' => (int) $object->type,
                'name' => $object->name,
                'related_module' => ($object->related_module !== null) ? (int) $object->related_module : $object->related_module,
                'relation_name' => $object->relation_name,
                'pivot_table' => $object->pivot_table,
                'column_name' => $object->column_name,
                'virtual_name' => $object->virtual_name,
                'tooltip_text' => $object->tooltip_text,
                'validation_rules' => $object->validation_rules,
                'module_id' => (int) $object->module_id,
                'order' => (int) $object->order,
                'editable' => (bool) $object->editable,
                'disabled' => (bool) $object->disabled,
                'hidden' => (bool) $object->hidden,
                'is_system' => (bool) $object->is_system,
                'virtual' => (bool) $object->virtual,
                'lazy_loading' => (bool) $object->lazy_loading,
                'default' => $object->default,
                'nullable' => (bool) $object->nullable,
                'local_key' => $object->local_key,
                'foreign_key' => $object->foreign_key,
                'is_default_search_choice' => (bool) $object->is_default_search_choice,
                'can_create_search_choice' => (bool) $object->can_create_search_choice,
                'active_entry_filter' => $object->active_entry_filter,
                'flatten_to_optgroups' => (bool) $object->flatten_to_optgroups,
                'nullable' => (bool) $object->nullable,
                'unique_name' => $object->getUniqueName(),
                'indexed' => (bool) $object->indexed,
                'created_at' => $object->created_at,
                'updated_at' => $object->updated_at
            ];
        }

        $this->transformGenericObjects($objectArray);

        return $objectArray;
    }

    private function getEmptyObjectRepresentation() {
        return [
            'id' => null,
            'type' => null,
            'name' => null,
            'related_module' => null,
            'relation_name' => null,
            'pivot_table' => null,
            'column_name' => null,
            'virtual_name' => null,
            'tooltip_text' => null,
            'validation_rules' => null,
            'module_id' => null,
            'order' => null,
            'editable' => null,
            'disabled' => null,
            'hidden' => null,
            'is_system' => null,
            'virtual' => null,
            'lazy_loading' => null,
            'default' => null,
            'local_key' => null,
            'foreign_key' => null,
            'is_default_search_choice' => null,
            'can_create_search_choice' => null,
            'active_entry_filter' => null,
            'flatten_to_optgroups' => null,
            'nullable' => null,
            'indexed' => null,
            'unique_name' => null,
            'created_at' => null,
            'updated_at' => null
        ];
        /*

                'id' => ($object->id !== null) ? (int) $object->id : $object->id,
                'type' => (int) $object->type,
                'name' => $object->name,
                'related_module' => ($object->related_module !== null) ? (int) $object->related_module : $object->related_module,
                'relation_name' => $object->relation_name,
                'pivot_table' => $object->pivot_table,
                'column_name' => $object->column_name,
                'virtual_name' => $object->virtual_name,
                'tooltip_text' => $object->tooltip_text,
                'validation_rules' => $object->validation_rules,
                'module_id' => (int) $object->module_id,
                'order' => (int) $object->order,
                'editable' => (bool) $object->editable,
                'disabled' => (bool) $object->disabled,
                'hidden' => (bool) $object->hidden,
                'is_system' => (bool) $object->is_system,
                'virtual' => (bool) $object->virtual,
                'lazy_loading' => (bool) $object->lazy_loading,
                'default' => $object->default,
                'local_key' => $object->local_key,
                'foreign_key' => $object->foreign_key,
                'is_default_search_choice' => (bool) $object->is_default_search_choice,
                'can_create_search_choice' => (bool) $object->can_create_search_choice,
                'active_entry_filter' => $object->active_entry_filter,
                'flatten_to_optgroups' => (bool) $object->flatten_to_optgroups,
                'nullable' => (bool) $object->nullable,
                'unique_name' => $object->getUniqueName(),
                'indexed' => (bool) $object->indexed,
                'created_at' => $object->created_at,
                'updated_at' => $object->updated_at
                */
    }
}
