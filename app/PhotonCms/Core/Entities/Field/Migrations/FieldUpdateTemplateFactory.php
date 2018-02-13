<?php

namespace Photon\PhotonCms\Core\Entities\Field\Migrations;

use Photon\PhotonCms\Core\Entities\Field\Field;
use Photon\PhotonCms\Core\Entities\Field\FieldTransformer;
use Photon\PhotonCms\Core\Entities\Field\Migrations\FieldUpdateTemplate;

class FieldUpdateTemplateFactory
{

    private static $fieldsForCheck = ['nullable', 'default'];

    public static function makeFieldUpdateTemplateFromDataDifference(Field $fieldBefore, array $fieldAfter)
    {
        $transformer = new FieldTransformer();
        $beforeFieldArray = $transformer->transform($fieldBefore);

        $compareResults = self::compareNewAndOldData($fieldAfter, $beforeFieldArray);
        if (is_array($compareResults) && !empty($compareResults)) {
            $fieldName = $fieldBefore->getUniqueName();

            $fieldUpdateTemplate = new FieldUpdateTemplate();
            $fieldUpdateTemplate->setFieldName($fieldName);
            $fieldUpdateTemplate->setLaravelType($fieldBefore->fieldType->laravel_type);

            foreach ($compareResults as $fieldName => $compareResult) {
                switch ($fieldName) {
                    case 'nullable':
                        if ($compareResults[$fieldName]) {
                            $fieldUpdateTemplate->changeToNullable();
                        }
                        else {
                            $fieldUpdateTemplate->changeToNotNullable();
                        }
                        break;

                    case 'default':
                        $fieldUpdateTemplate->setDefault($compareResult);
                }
            }

            // Differences found
            return $fieldUpdateTemplate;
        }

        // No differences found
        return null;
    }

    private static function compareNewAndOldData($newData, $oldData)
    {
        $changes = [];
        foreach (self::$fieldsForCheck as $fieldForCheck) {
            if (
                key_exists($fieldForCheck, $newData) && key_exists($fieldForCheck, $oldData) &&
                $newData[$fieldForCheck] != $oldData[$fieldForCheck]
            ) {
                $changes[$fieldForCheck] = $newData[$fieldForCheck];
            }
        }
        return $changes;
    }
}