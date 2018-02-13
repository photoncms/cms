<?php

namespace Photon\PhotonCms\Core\Entities\User;

use Photon\PhotonCms\Core\Entities\User\Contracts\UserInterface;
use Photon\PhotonCms\Core\Entities\User\Contracts\UserGatewayInterface;

/**
 * Decouples repository from data sources.
 */
class UserGateway implements UserGatewayInterface
{
    /**
     * Retrieves a user by ID.
     *
     * @param int $id
     * @return User
     */
    public function retrieve($id)
    {
        return User::find($id);
    }

    /**
     * Persists a user.
     *
     * @param User $user
     * @return boolean
     */
    public function persist($user)
    {
        $user->save();
        return true;
    }
}