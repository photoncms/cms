<?php

namespace Photon\PhotonCms\Core\Trim;

use Photon\PhotonCms\Core\Helpers\TrimResponseHelper;

class TrimmingController
{

    /**
     * Trim all mappable objects provided in input data.
     *
     * @param array $data
     * @return array
     */
    public function trim($data, $responseName)
    {
        // Trim auth/me user data
        if($responseName === 'GET_LOGGED_IN_USER_SUCCESS') {
            return $this->filterData($data, 'user');
        }

        // Trim menus/{id} data
        if($responseName === 'LOAD_MENU_ITEMS_SUCCESS') {
            return $this->filterData($data, 'menu_items', true);
        }

        // Trim single entry
        if(isset($data['entry'])) {
            return $this->filterData($data, 'entry');
        }

        // Trim multiple entries
        if(isset($data['entries'])) {
            return $this->filterData($data, 'entries', true);
        }

        return $data;
    }

    /**
     * Performs the data filtering
     *
     * @param   array  $data
     * @param   string  $keyName
     * @param   boolean  $isDataArray
     * @return  array
     */
    private function filterData($data, $keyName, $isDataArray = false)
    {
        $includedFields = TrimResponseHelper::prepareIncludedFields();

        if (empty($includedFields)) {
            return $data;
        }

        $data[$keyName] = $isDataArray
            ? $this->trimDataArray($data[$keyName], $includedFields)
            : $this->trimData($data[$keyName], $includedFields);

        return $data;
    }

    /**
     * Trim array of data
     *
     * @param array $array
     * @param array $includedFields
     * @return array
     */
    private function trimDataArray($array, $includedFields)
    {
        $trimmedData = [];
        foreach ($array as $entry) {
        	$trimmedData[] = $this->trimData($entry, $includedFields);
        }

        return $trimmedData;
    }

    /**
     * Trim data
     *
     * @param array $array
     * @param array $includedFields
     * @return array
     */
    private function trimData($array, $includedFields)
    {
        $trimmedData = [];
        foreach ($includedFields as $key => $value) {
            if(array_key_exists($key, $array)) {
                $trimmedData[$key] = $array[$key];
                if(is_array($trimmedData[$key]) && count($value) > 0) {
                	$trimmedData[$key] = $this->trimData($trimmedData[$key], $value);
                }
            }
        }

        return $trimmedData;
    }
}
