<?php

namespace Photon\PhotonCms\Core\Entities\Validation;

use Photon\PhotonCms\Core\Transform\BaseTransformer;

use Illuminate\Validation\Validator;

/**
 * Transforms Validator instances into various output packages.
 */
class ValidationTransformer extends BaseTransformer
{

    /**
     * Transforms an object into a generic array
     *
     * @var Validator $object
     * @return array
     */
    public function transform(Validator $object)
    {
        $messages = $object->messages()->getMessages();
        $failed = $object->failed();

        $errors = [];
        foreach ($failed as $fieldName => $failedRule) {
            $errors[$this->reworkFieldNameToArray($fieldName)] = [
                'failed_rule' => current(array_keys($failedRule)),
                'message' => (isset($messages[$fieldName])) ? $messages[$fieldName][0] : ''
            ];
        }
        
        return $errors;
    }

    /**
     * Recompiles dot notation to square bracket notation:
     * this.is.an.example => this[is][an][example]
     *
     * @param string $fieldName
     * @return string
     */
    private function reworkFieldNameToArray($fieldName)
    {
        $array = explode('.', $fieldName);
        $newFieldName = $array[0];
        unset($array[0]);

        foreach ($array as $item) {
            $newFieldName .= "[$item]";
        }

        return $newFieldName;
    }
}