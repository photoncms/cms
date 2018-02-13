<?php

namespace Photon\PhotonCms\Core\Helpers;

class NumberHelper
{
    public static function toStringWithLeadingZeros($number, $numberOfLeadingZeros = 0)
    {
        $string = (string) $number;

        if ($numberOfLeadingZeros > 0 && strlen($string) < $numberOfLeadingZeros) {
            $string = '0'.$string;
            $string = self::toStringWithLeadingZeros($string, $numberOfLeadingZeros);
        }

        return $string;
    }
}