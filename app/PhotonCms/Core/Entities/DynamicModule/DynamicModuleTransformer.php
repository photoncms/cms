<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModule;

use Photon\PhotonCms\Core\Entities\DynamicModule\Contracts\DynamicModuleInterface;
use Photon\PhotonCms\Core\Transform\BaseTransformer;
use Photon\PhotonCms\Core\PermissionServices\PermissionChecker;

/**
 * Transforms dynamic module entry instances into various output packages.
 */
class DynamicModuleTransformer extends BaseTransformer
{

    /**
     * Transforms an object into a generic array
     *
     * @var  object
     * @return array
     */
    public function transform(DynamicModuleInterface $object)
    {
        $attributes = $object->getAll();

        if (\Auth::user()) {
            $restrictedFields = [];
            foreach ($attributes as $fieldName => $attribute) {
                if (!PermissionChecker::canEditModuleField($object->getTable(), $fieldName)) {
                    $restrictedFields[] = $fieldName;
                }
            }
            $attributes['permission_control']['edit_restrictions'] = $restrictedFields;
        }

        $this->transformGenericObjects($attributes);

        // This is for the extension HMVC.
        if (method_exists($object, 'getAvailableExtensions')) {
            $attributes['extensions'] = $object->getAvailableExtensions();
        }

        if (\Auth::user()) {
            $attributes['permission_control']['crud'] = [
                'create' => PermissionChecker::canCRUDCreate($object->getTable()),
                'update' => PermissionChecker::canCRUDUpdateSpecific($object->getTable(), $object),
                'delete' => PermissionChecker::canCRUDDeleteSpecific($object->getTable(), $object),
            ];
        }

        // // Add any relations preloaded with eager loading
        $relations = $object->getRelations();
        foreach ($relations as $key => $relation) {
            $newKey = str_replace("_relation", "", $key);
            $relations[$newKey] = $relation;
            unset($relations[$key]);
        }

        $attributes = array_merge($attributes, $relations);
        return $attributes;
    }
}