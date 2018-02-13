<?php

namespace Photon\PhotonCms\Core\Entities\Field\Migrations;

use Photon\PhotonCms\Core\Entities\Field\Migrations\FieldUpdateMigrationTemplate;
use Photon\PhotonCms\Core\Entities\Field\FieldTransformer;
use Photon\PhotonCms\Core\Entities\Field\Field;

class FieldUpdateMigrationTemplateFactory
{

    private static $fieldsForCheck = ['nullable', 'default'];

    /**
     *
     * @param string $tableName
     * @param array $beforeFields
     * @param array $afterFields
     * @return FieldUpdateMigrationTemplate
     */
    public static function makeUpdateMigrationFromFieldsDataDifference($tableName, array $beforeFields, array $afterFields)
    {
        $migrationTemplate = new FieldUpdateMigrationTemplate();
        foreach ($beforeFields as $beforeField) {
            if (!($beforeField instanceof Field) && !$beforeField->fieldType->isAttribute()) {
                continue;
            }

            $transformer = new FieldTransformer();
            $beforeFieldArray = $transformer->transform($beforeField);
            
            $currentFieldId = $beforeFieldArray['id'];
            if (key_exists($currentFieldId, $afterFields)) {
                $compareResult = self::compareNewAndOldData($afterFields[$currentFieldId], $beforeFieldArray);
                if (is_array($compareResult) && !empty($compareResult)) {
                    $fieldName = $beforeField->getUniqueName();
                    $migrationTemplate->setField($fieldName);
                    switch ($fieldName) {
                        case 'nullable':
                            if ($compareResult) {
                                $migrationTemplate->changeToNullable($fieldName);
                            }
                            else {
                                $migrationTemplate->changeToNotNullable($fieldName);
                            }
                            break;

                        case 'default':
                            $migrationTemplate->setDefault($fieldName, $compareResult);
                    }
//                    $fieldNewData[$beforeField->getUniqueName()] = $compareResult;
                }
            }
        }

//        var_dump($fieldNewData);
        return $migrationTemplate;
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