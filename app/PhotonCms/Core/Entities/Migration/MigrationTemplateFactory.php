<?php

namespace Photon\PhotonCms\Core\Entities\Migration;

class MigrationTemplateFactory
{
    
    /**
     * Makes an instance of a base MigrationTemplate.
     *
     * @return \Photon\PhotonCms\Core\Entities\MigrationOld\MigrationTemplate
     */
    public function make()
    {
        return new MigrationTemplate();
    }
}