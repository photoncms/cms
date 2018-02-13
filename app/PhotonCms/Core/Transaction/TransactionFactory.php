<?php

namespace Photon\PhotonCms\Core\Transaction;

use Photon\PhotonCms\Core\Transaction\TransactionController;

class TransactionFactory
{
    /**
     * Makes a new instance of a trancastion controller.
     *
     * @param string $name
     * @return TransactionController
     */
    public static function make($name = '')
    {
        return new TransactionController($name);
    }
}