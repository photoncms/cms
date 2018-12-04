<?php

namespace Photon\PhotonCms\Core\Helpers;

class TrimResponseHelper
{

    /**
     * Prepares included_fields received as get paramethers.
     *
     * @return array
     */
    public static function prepareIncludedFields()
    {
        $preparedFields = [];

        $includedFields = \Request::get('include');

        if(!is_array($includedFields)) {
            return $preparedFields;
        }

        foreach ($includedFields as $key => $field) {
            $fields = array_reverse(explode(".", $field));

            $partialArray = [];
            foreach ($fields as $fieldValue) {
                $partialArray = [$fieldValue => $partialArray];
            }

            $preparedFields = array_merge_recursive($preparedFields, $partialArray);
        }

        return $preparedFields;
    }
}