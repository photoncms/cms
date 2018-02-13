<?php

namespace Photon\PhotonCms\Core\Entities\Seed;

use Photon\PhotonCms\Core\Entities\Seed\Contracts\SeedTemplateInterface;

/**
 * Decouples repository from data sources.
 */
class SeedGatewayReport extends SeedGateway
{

    /**
     * Mocks seed creation.
     *
     * @param SeedTemplateInterface $seed
     */
    public function create(SeedTemplateInterface $seed)
    {
        return true;
    }
}