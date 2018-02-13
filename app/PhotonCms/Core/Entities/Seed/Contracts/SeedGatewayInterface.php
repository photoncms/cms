<?php

namespace Photon\PhotonCms\Core\Entities\Seed\Contracts;

interface SeedGatewayInterface
{

    /**
     * Uses a seed template to create a seed.
     *
     * @param SeedTemplateInterface $seed
     */
    public function create(SeedTemplateInterface $seed);
}