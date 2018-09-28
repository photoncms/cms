<?php

namespace Photon\PhotonCms\Core\Trim;

class TrimmingController
{

    /**
     * Trim all mappable objects provided in input data.
     *
     * @param array $data
     * @return array
     */
    public function trim($data)
    { 
        // if not retreiving dynamic module entry return
        if(!isset($data['entries']) && !isset($data['entry'])) {
            return $data;
        }

        // prepare included fields array
    	$includedFields = $this->prepareIncludedFields();

        // trim single entry
        if(isset($data['entry'])) {
            $data['entry'] = $this->trimData($data['entry'], $includedFields);
            return $data;
        }
        
        $data['entries'] = $this->trimDataArray($data['entries'], $includedFields);

        return $data;
    }

    /**
     * Prepares included_fields received as get paramethers.
     *
     * @return array
     */
    private function prepareIncludedFields()
    {
        $preparedFields = [];

        // if there are no filtered fields return
        $includedFields = \Request::get('include');
        if(!$includedFields) {
            return $preparedFields;
        }

        // parser included fields from string to associative array
        $includedFields = explode(",", $includedFields);

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
            if(isset($array[$key])) {
                $trimmedData[$key] = $array[$key];
                if(is_array($trimmedData[$key]) && count($value) > 0) {
                	$trimmedData[$key] = $this->trimData($trimmedData[$key], $value);
                }
            }
        }

        return $trimmedData;
    }
}
