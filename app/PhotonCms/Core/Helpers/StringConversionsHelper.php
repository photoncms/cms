<?php

namespace Photon\PhotonCms\Core\Helpers;

class StringConversionsHelper
{
    /**
     * Converts CamelCase string to snake_case.
     *
     * @param string $input
     * @return string
     */
    public static function camelCaseToSnakeCase($input)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }

    /**
     * Converts snake_case string to CamelCase.
     *
     * @param string $input
     * @return string
     */
    public static function snakeCaseToCamelCase($input)
    {
        return implode('',array_map('ucfirst',explode('_',$input)));
    }

    /**
     * Calculates a sum of codes all ASCII characters from the supplied string
     *
     * @param string $string
     * @return int
     */
    public static function stringToASCIISum($string)
    {
        $value = 0;
        $length = strlen($string);
        for ($i = 0; $i < $length; $i++) {
            $value += ord(substr($string, $i, 1));
        }

        return $value;
    }

    /**
     * Removes any characters from the string which are not alphabet, numeric or _
     *
     * @param string $string
     * @return string
     */
    public static function stringToAlphaNumDash($string)
    {
        return preg_replace('/[^A-Za-z0-9_]/', '', $string);
    }
}