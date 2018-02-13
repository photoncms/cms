<?php

namespace Photon\PhotonCms\Core\Entities\User;

/**
 * Handles object manipulation.
 */
class UserFactory
{
    /**
     * Makes an instance of a user from input data.
     *
     * @param array $data
     * @return \Photon\PhotonCms\Core\Entities\User\User
     */
    public static function make($data)
    {
        return new User($data);
    }
}