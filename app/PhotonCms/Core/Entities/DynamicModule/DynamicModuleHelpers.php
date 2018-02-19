<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModule;

use Photon\PhotonCms\Dependencies\Traits\AnchorFields;

/**
 * Contains Dynamic Module entity helper functions.
 *
 * Functionalities which are directly related to the Dynamic Module entity, but aren't performed over Dynamic Module instances.
 */
class DynamicModuleHelpers
{

    use AnchorFields;

    /**
     * Generates item anchor text from the item instance and anchor text stub
     *
     * @param mixed $item
     * @param string $anchorTextStub
     * @return string
     */
    public function generateAnchorTextFromItem($item, $anchorTextStub)
    {
        preg_match_all("/{{([^{}]+)}}/", $anchorTextStub, $anchorTextPlaceholders);

        $placeholderText = $anchorTextStub;

        if (!empty($anchorTextPlaceholders[1])) {
            foreach ($anchorTextPlaceholders[1] as $key => $anchorTextPlaceholder) {
                $anchorTextPlaceholderChain = explode('.', $anchorTextPlaceholder);

                try {
                    $anchorTextExtract = $this->extractAnchorTextFieldValue($item, $anchorTextPlaceholderChain);
                } catch (\Exception $ex) {
                    $anchorTextExtract = null;
                }

                $placeholderText = str_replace($anchorTextPlaceholders[0][$key], $anchorTextExtract, $placeholderText);
            }
        }

        return $placeholderText;
    }

    /**
     * Extracts a field value from anchor text variables for regular values and related values.
     * If the value was not found, function will return null.
     *
     * @param mixed $item
     * @param array $anchorTextPlaceholderChain
     * @return string
     */
    private function extractAnchorTextFieldValue($item, $anchorTextPlaceholderChain)
    {
        $fieldValue = $item;
        foreach ($anchorTextPlaceholderChain as $anchorTextPlaceholderLink) {
            if (end($anchorTextPlaceholderChain) === $anchorTextPlaceholderLink) {
                $functionData = explode("|", $anchorTextPlaceholderLink);
                if(count($functionData) == 2) {
                    $arguments = explode(",", $functionData[0]);
                    $functionName = $functionData[1];

                    if(!method_exists($this, $functionName)) {
                        $fieldValue = "";
                    } else {
                        foreach ($anchorTextPlaceholderChain as $tmpPlaceholderLink) {
                            if($tmpPlaceholderLink == $anchorTextPlaceholderLink)
                                break;
                            $relationName = $tmpPlaceholderLink.'_relation';
                            $item = $item->$relationName;
                        }
                        $fieldValue = $this->$functionName($item, $arguments);
                    }
                } else {
                    $getterName = 'getAttr' . ucfirst($anchorTextPlaceholderLink);
                    // Use dynamic model getters
                    if (method_exists($fieldValue, $getterName) ) {
                        $fieldValue = $fieldValue->$getterName();
                    }
                    // If not use direct attribute approach
                    else {
                        $fieldValue = $fieldValue->$anchorTextPlaceholderLink;
                        if(in_array($anchorTextPlaceholderLink, ["created_at", "updated_at"])) {
                            $fieldValue = $fieldValue->format("d.m.Y H:i");
                        }
                    }
                }
            }
            else {
                $relationName = $anchorTextPlaceholderLink.'_relation';
                $fieldValue = $fieldValue->$relationName;
            }
        }

        return (is_object($fieldValue)) ? null : $fieldValue;
    }
}