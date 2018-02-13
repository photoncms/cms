<?php

namespace Photon\PhotonCms\Core\Entities\User;

use Photon\PhotonCms\Core\Entities\User\Contracts\UserGatewayInterface;
use Photon\PhotonCms\Core\Exceptions\PhotonException;

/**
 * Decouples buisiness logic from object storage, manipulation and internal logic over User entity.
 */
class UserRepository
{
    /**
     * Retrieves a user through user gateway.
     *
     * @param int $id
     * @param UserGatewayInterface $gateway
     * @return User
     */
    public function find($id, UserGatewayInterface $gateway)
    {
        return $gateway->retrieve($id);
    }

    /**
     * Saves a user from data. New user is created, or an existing one is updated.
     *
     * @param array $data
     * @param UserGatewayInterface $gateway
     * @return User
     * @throws PhotonException
     */
    public function saveFromData($data, UserGatewayInterface $gateway)
    {
        if (isset($data['id']) && is_numeric($data['id']) && $data['id'] > 0) {
            $user = $gateway->retrieve($data['id']);

            if (is_null($user)) {
                throw new PhotonException('USER_NOT_FOUND', ['id' => $data['id']]);
            }

            if (isset($data['email']) && $data['email'] !== '') {
                $user->email = $data['email'];
            }

            if (isset($data['first_name']) && $data['first_name'] !== '') {
                $user->first_name = $data['first_name'];
            }

            if (isset($data['last_name']) && $data['last_name'] !== '') {
                $user->last_name = $data['last_name'];
            }
        }
        else {
            $user = UserFactory::make($data);
        }

        if ($gateway->persist($user)) {
            return $user;
        }
        else {
            throw new PhotonException('USER_SAVE_FROM_DATA_FAILED', ['data' => $data]);
        }
    }
}