<?php

namespace Photon\PhotonCms\Core\Helpers;

use Photon\PhotonCms\Core\Helpers\StringConversionsHelper;
use Photon\PhotonCms\Core\Helpers\NumberHelper;

class CodeHelper
{

    /**
     * Used throughout Photon to generate a user confirmation code.
     *
     * @return string
     */
    public static function generateConfirmationCode()
    {
        return str_random(30);
    }

    /**
     * Used throughout Photon to generate a user invitation code.
     *
     * @return string
     */
    public static function generateInvitationCode()
    {
        return str_random(30);
    }

    public static function generateModuleUID()
    {
        // ToDo: This method can be upgraded so it uses a pointer if a module UID has already been created during a current thread. For example, difference parameter is the time in seconds, which is far
        // too small to be enough to generate multiple UIDs during a single application lifetime. (Sasa|03/2017)
        $seedString = config('photon.uid_seed_string');
        if (!$seedString) {
            $seedString = self::generateRandomString(39);
        }
        $convertedSeed = StringConversionsHelper::stringToASCIISum($seedString);
        $convertedSeed = NumberHelper::toStringWithLeadingZeros($convertedSeed, 4);

        return $convertedSeed.time();
    }

    public static function generateRandomString($length = 256)
    {
        $pool = array_merge(range(0,9), range('a', 'z'),range('A', 'Z'));

        $string = '';
        for($i=0; $i < $length; $i++) {
            $string .= $pool[mt_rand(0, count($pool) - 1)];
        }
        return $string;
    }
}