<?php

namespace Photon\PhotonCms\Core\Entities\Module;

use Photon\PhotonCms\Core\Transform\BaseTransformer;
use Photon\PhotonCms\Core\Transform\Contracts\TransformerFullTransformInterface;
use Photon\PhotonCms\Core\Transform\Contracts\TransformerFullTransformConvertedInterface;
use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Photon\PhotonCms\Core\PermissionServices\PermissionChecker;

/**
 * Transforms Module instances into various output packages.
 */
class ModuleTransformer extends BaseTransformer implements TransformerFullTransformInterface, TransformerFullTransformConvertedInterface
{

    /**
     * Transforms an object into a generic array
     *
     * @var  Module $object
     * @return array
     */
    public function transform(Module $object)
    {
        $objectArray = [
            'id' => ($object->id !== null) ? (int) $object->id : $object->id,
            'category' => ($object->category !== null) ? (int) $object->category : $object->category,
            'type' => $object->type,
            'name' => $object->name,
//            'model_name' => $object->model_name, // shouldn't be available on the API
            'table_name' => $object->table_name,
            'icon' => $object->icon,
            'anchor_text' => $object->anchor_text,
            'anchor_html' => $object->anchor_html,
            'slug' => $object->slug,
            'created_at' => $object->created_at,
            'updated_at' => $object->updated_at,
            'lazy_loading' => (bool) $object->lazy_loading,
            'reporting' => (bool) $object->reporting,
            'max_depth' => ($object->max_depth !== null) ? (int) $object->max_depth : $object->max_depth,
        ];

        $restrictedFields = [];
        foreach ($objectArray as $fieldName => $attribute) {
            if (!PermissionChecker::canEditModuleField($object->getTable(), $fieldName)) {
                $restrictedFields[] = $fieldName;
            }
        }
        $objectArray['permission_control']['edit_restrictions'] = $restrictedFields;

        $objectArray['permission_control']['crud'] = [
            'create' => (PermissionChecker::canModifyModule($object->table_name)) ? PermissionChecker::canCRUDCreate($object->table_name) : false,
            'update' => (PermissionChecker::canModifyModule($object->table_name)) ? PermissionChecker::canCRUDUpdate($object->table_name) : false,
            'delete' => (PermissionChecker::canModifyModule($object->table_name)) ? PermissionChecker::canCRUDDelete($object->table_name) : false,
        ];

        // Add any relations preloaded with eager loading
        $result = array_merge($objectArray, $object->getRelations());

        $this->transformGenericObjects($result);

        return $result;
    }

    /**
     * Transforms the whole object into an array.
     * Passes each attribute without further conversion.
     *
     * @param Module $object
     */
    public function fullTransform($object)
    {
        if (!($object instanceof Module)) {
            throw new PhotonException('NOT_INSTANCE_OF_MODULE', ['given' => get_class($object), 'expected' => '\Photon\PhotonCms\Core\Entities\Module\Module']);
        }

        return [
            'id' => ($object->id !== null) ? (int) $object->id : $object->id,
            'category' => ($object->category !== null) ? (int) $object->category : $object->category,
            'type' => $object->type,
            'name' => $object->name,
            'model_name' => $object->model_name,
            'table_name' => $object->table_name,
            'anchor_text' => $object->anchor_text,
            'anchor_html' => $object->anchor_html,
            'slug' => $object->slug,
            'icon' => $object->icon,
            'created_at' => $object->created_at,
            'updated_at' => $object->updated_at,
            'lazy_loading' => (bool) $object->lazy_loading,
            'reporting' => (int) $object->reporting,
            'max_depth' => ($object->max_depth !== null) ? (int) $object->max_depth : $object->max_depth,
        ];
    }

    /**
     * Transforms the whole object into an array.
     * Converts each attribute if necessary.
     *
     * @param Module $object
     */
    public function fullTransformConverted($object)
    {
        if (!($object instanceof Module)) {
            throw new PhotonException('NOT_INSTANCE_OF_MODULE', ['given' => get_class($object), 'expected' => '\Photon\PhotonCms\Core\Entities\Module\Module']);
        }
        
        $objectArray = [
            'id' => ($object->id !== null) ? (int) $object->id : $object->id,
            'category' => ($object->category !== null) ? (int) $object->category : $object->category,
            'type' => $object->type,
            'name' => $object->name,
            'model_name' => $object->model_name,
            'table_name' => $object->table_name,
            'anchor_text' => $object->anchor_text,
            'anchor_html' => $object->anchor_html,
            'slug' => $object->slug,
            'icon' => $object->icon,
            'created_at' => $object->created_at,
            'updated_at' => $object->updated_at,
            'lazy_loading' => (bool) $object->lazy_loading,
            'reporting' => (bool) $object->reporting,
            'max_depth' => ($object->max_depth !== null) ? (int) $object->max_depth : $object->max_depth,
        ];

        $this->transformGenericObjects($objectArray);

        return $objectArray;
    }
}