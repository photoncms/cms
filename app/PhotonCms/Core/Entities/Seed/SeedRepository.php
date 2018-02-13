<?php

namespace Photon\PhotonCms\Core\Entities\Seed;

use Photon\PhotonCms\Core\Entities\Seed\Contracts\SeedTemplateInterface;
use Photon\PhotonCms\Core\Entities\Seed\Contracts\SeedGatewayInterface;

/**
 * Decouples buisiness logic from object storage, manipulation and internal logic over Seed entity.
 */
class SeedRepository
{

    /**
     * Uses a seed template to create a seed file.
     *
     * @param SeedTemplateInterface $seed
     * @param SeedGateway $seedGateway
     */
    public function create(SeedTemplateInterface $seed, SeedGatewayInterface $seedGateway)
    {
        $seedGateway->create($seed);
    }
}