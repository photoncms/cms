<?php

namespace Photon\PhotonCms\Core\Entities\UsedPassword;

use Photon\PhotonCms\Core\Entities\UsedPassword\Contracts\UsedPasswordGatewayInterface;

class UsedPasswordRepository
{

    /**
     * Saves a UsedPassword instance from data using the specified gateway.
     *
     * @param array $data
     * @param UsedPasswordGatewayInterface $gateway
     * @return boolean
     */
    public function saveFromData($data, UsedPasswordGatewayInterface $gateway)
    {
        $usedPassword = UsedPasswordFactory::make($data);

        $success = $gateway->persist($usedPassword);

        $gateway->deleteOld(config('jwt.max_used_passwords_history'), $data['user_id']);

        return $success;
    }

    /**
     * Retrieves all UsedPassword instances for a specified user using its ID and the specified gateway.
     *
     * @param int $userId
     * @param UsedPasswordGatewayInterface $gateway
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function retrieveByUserId($userId, UsedPasswordGatewayInterface $gateway)
    {
        return $gateway->retrieveByUserId($userId);
    }
}