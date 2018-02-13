<?php

namespace Photon\PhotonCms\Core\Entities\Field;

use Photon\PhotonCms\Core\Exceptions\PhotonException;

/**
 * Contains Field entity helper functions.
 *
 * Functionalities which are directly related to the Field entity, but aren't performed over Field instances.
 */
class FieldHelpers
{

    private $fieldNameRule = '/[^a-zA-Z0-9_]/';

    /**
     * Validates the Field name.
     *
     * @param string $fieldName
     * @return string
     * @throws PhotonException
     */
    public function validateFieldName($fieldName)
    {
        preg_match_all($this->fieldNameRule, $fieldName, $matches);

        if (!empty($matches[0])) {
            throw new PhotonException('BAD_FIELD_NAME', ['invalid_characters' => array_values(array_unique($matches[0]))]);
        }

        return $fieldName;
    }

    /**
     * Validates multiple Field names.
     *
     * @param array $fieldNames
     * @throws PhotonException
     */
    public function validateFieldNames(array $fieldNames)
    {
        $errorNames = [];
        foreach ($fieldNames as $fieldName) {
            preg_match_all($this->fieldNameRule, $fieldName, $matches);

            if (!empty($matches[0])) {
                $errorNames[$fieldName] = array_values(array_unique($matches[0]));
            }
        }

        if (!empty($errorNames)) {
            throw new PhotonException('BAD_FIELD_NAME', ['invalid_characters' => $errorNames]);
        }
    }
}