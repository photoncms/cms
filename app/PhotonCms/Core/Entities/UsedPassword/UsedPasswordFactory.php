<?php

namespace Photon\PhotonCms\Core\Entities\UsedPassword;

use Carbon\Carbon;

class UsedPasswordFactory
{

    /**
     * Makes a new UsedPassword instance with default data.
     *
     * @param array $data
     * @return \Photon\PhotonCms\Core\Entities\UsedPassword\UsedPassword
     */
    public static function make($data)
    {
        $newUsedPassword = new UsedPassword($data);
        $newUsedPassword->created_at = Carbon::now();

        return $newUsedPassword;
    }
}