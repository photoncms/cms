<?php

namespace Photon\PhotonCms\Core\Entities\UsedPassword;

use Photon\PhotonCms\Core\Entities\UsedPassword\Contracts\UsedPasswordGatewayInterface;

class UsedPasswordGateway implements UsedPasswordGatewayInterface
{

    /**
     * Retrieves all used passwords for the specified user.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function retrieveByUserId($userId)
    {
        return UsedPassword::where('user_id', $userId)->get();
    }

    /**
     * Persists a UsedPassword instance.
     *
     * @param \Photon\PhotonCms\Core\Entities\UsedPassword\UsedPassword $usedPassword
     * @return boolean
     */
    public function persist(UsedPassword $usedPassword)
    {
        try {
            $usedPassword->save();
        } catch (\Exception $ex) {
            return false;
        }

        return true;
    }

    /**
     * Deletes a any old passwords which are over the password number limit per user.
     *
     * @param int $limit
     * @param int $userId
     * @return boolean
     */
    public function deleteOld($limit, $userId)
    {
        $usedPasswords = UsedPassword::where('user_id', $userId)->orderBy('created_at', 'desc')->get();

        $counter = 1;
        foreach ($usedPasswords as $usedPassword) {
            if ($counter > $limit) {
                \DB::table('used_passwords')->where('user_id', $userId)->where('password', $usedPassword->password)->delete();
            }
            $counter++;
        }
    }
}