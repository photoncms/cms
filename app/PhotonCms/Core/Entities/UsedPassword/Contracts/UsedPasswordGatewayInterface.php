<?php

namespace Photon\PhotonCms\Core\Entities\UsedPassword\Contracts;

use Photon\PhotonCms\Core\Entities\UsedPassword\UsedPassword;

interface UsedPasswordGatewayInterface
{

    /**
     * Retrieves all used passwords for the specified user.
     *
     * @param int $userId
     * @return Collection
     */
    public function retrieveByUserId($userId);

    /**
     * Persists a UsedPassword instance.
     *
     * @param \Photon\PhotonCms\Core\Entities\UsedPassword\UsedPassword $usedPassword
     * @return boolean
     */
    public function persist(UsedPassword $usedPassword);

    /**
     * Deletes a any old passwords which are over the password number limit per user.
     *
     * @param int $limit
     * @param int $userId
     * @return boolean
     */
    public function deleteOld($limit, $userId);
}