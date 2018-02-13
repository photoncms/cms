<?php

namespace Photon\PhotonCms\Core\Entities\Field;

use Photon\PhotonCms\Core\Entities\Field\FieldRepository;
use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Photon\PhotonCms\Core\Entities\Field\Contracts\FieldGatewayInterface;

/**
 * Decouples buisiness logic from object storage, manipulation and internal logic over Field entity.
 */
class FieldRepository
{
    public function find($id, FieldGatewayInterface $fieldGateway)
    {
        return $fieldGateway->retrieve($id);
    }

    /**
     * Retrieves a Field instance by module id.
     *
     * @param int $moduleId
     * @param FieldGatewayInterface $fieldGateway
     * @return Field
     */
    public function findByModuleId($moduleId, FieldGatewayInterface $fieldGateway)
    {
        return $fieldGateway->retrieveByModuleId($moduleId);
    }

    public function resetDefaultSearchChoices($moduleId, FieldGatewayInterface $fieldGateway)
    {
        $fields = $fieldGateway->retrieveByModuleId($moduleId);

        foreach ($fields as $key => $field) {
            $field->is_default_search_choice = 0;
            $field->save();
        }

        return true;
    }

    /**
     * Saves a Field using passed data.
     *
     * If there is no ID in the passed data array, a new instance will be created.
     *
     * @param array $data
     * @return Field
     * @param FieldGatewayInterface $fieldGateway
     * @throws PhotonException
     */
    public function saveFromData($data, FieldGatewayInterface $fieldGateway)
    {
        // Edit
        if (isset($data['id']) && is_numeric($data['id']) && $data['id'] > 0) {
            $field = $fieldGateway->retrieve($data['id']);
            
            if (is_null($field)) {
                throw new PhotonException('FIELD_NOT_FOUND', ['id' => $data['id']]);
            }

            if (array_key_exists('module_parent', $data) && (is_numeric($data['module_parent']) && $data['module_parent'] > 0 || $data['module_parent'] === null)) {
                $field->module_parent = $data['module_parent'];
            }

            if (array_key_exists('name', $data) && $data['name'] !== '') {
                $field->name = $data['name'];
            }

            if (array_key_exists('virtual_name', $data) && $data['virtual_name'] !== '') {
                $field->virtual_name = $data['virtual_name'];
            }

            if (array_key_exists('tooltip_text', $data)) {
                $field->tooltip_text = $data['tooltip_text'];
            }

            if (array_key_exists('validation_rules', $data)) {
                $field->validation_rules = $data['validation_rules'];
            }

            if (array_key_exists('editable', $data)) {
                $field->editable = $data['editable'];
            }

            if (array_key_exists('disabled', $data)) {
                $field->disabled = $data['disabled'];
            }

            if (array_key_exists('hidden', $data)) {
                $field->hidden = $data['hidden'];
            }

            if (array_key_exists('is_system', $data)) {
                $field->is_system = $data['is_system'];
            }

            if (array_key_exists('virtual', $data)) {
                $field->virtual = $data['virtual'];
            }

            if (array_key_exists('lazy_loading', $data)) {
                $field->lazy_loading = $data['lazy_loading'];
            }

            if (array_key_exists('order', $data) && is_numeric($data['order']) && $data['order'] >= 0) {
                $field->order = $data['order'];
            }

            if (array_key_exists('default', $data)) {
                $field->default = $data['default'];
            }

            if (array_key_exists('is_default_search_choice', $data)) {
                $field->is_default_search_choice = $data['is_default_search_choice'];
            }

            if (array_key_exists('can_create_search_choice', $data)) {
                $field->can_create_search_choice = $data['can_create_search_choice'];
            }

            if (array_key_exists('active_entry_filter', $data)) {
                $field->active_entry_filter = $data['active_entry_filter'];
            }

            if (array_key_exists('flatten_to_optgroups', $data)) {
                $field->flatten_to_optgroups = $data['flatten_to_optgroups'];
            }

            if (array_key_exists('local_key', $data)) {
                $field->local_key = $data['local_key'];
            }

            if (array_key_exists('foreign_key', $data)) {
                $field->foreign_key = $data['foreign_key'];
            }

            if (array_key_exists('nullable', $data)) {
                $field->nullable = $data['nullable'];
            }

            if (array_key_exists('indexed', $data)) {
                $field->indexed = $data['indexed'];
            }
        }
        // Create new
        else {
            $field = FieldFactory::make($data);
        }

        if ($fieldGateway->persist($field)) {
            return $field;
        }
        else {
            throw new PhotonException('FIELD_SAVE_FROM_DATA_FAILED', ['data' => $data]);
        }
    }

    /**
     * Saves a Field from data including restricted fields.
     *
     * Useful for reverting from backup.
     *
     * @param array $data
     * @param FieldGatewayInterface $fieldGateway
     * @return type
     * @throws PhotonException
     */
    public function fullSaveFromData(array $data, FieldGatewayInterface $fieldGateway)
    {
        $field = $fieldGateway->retrieve($data['id']);

        if (is_null($field)) {
            $field = FieldFactory::make($data);
        }

        FieldFactory::replaceData($field, $data);

        if ($fieldGateway->persist($field)) {
            return $field;
        }
        else {
            throw new PhotonException('FIELD_SAVE_FROM_DATA_FAILED', ['data' => $data]);
        }
    }

    /**
     * Removes a field insert from the DB.
     *
     * @param Field $field
     * @param FieldGatewayInterface $fieldGateway
     */
    public function delete(Field $field, FieldGatewayInterface $fieldGateway)
    {
        return $fieldGateway->delete($field);
    }

    /**
     * Retrieve a fields by module ID
     *
     * @param int $moduleId
     * @param FieldGatewayInterface $gateway
     * @return mixed
     */
    public function findByRelatedModuleId($moduleId, FieldGatewayInterface $gateway)
    {
        return $gateway->retrieveByRelatedModuleId($moduleId);
    }
}
