<?php

namespace Photon\PhotonCms\Core\Entities\Migration;

use Photon\PhotonCms\Core\Entities\Migration\Contracts\MigrationGatewayInterface;
use Photon\PhotonCms\Core\Entities\Migration\Contracts\MigrationTemplateInterface;
use Photon\PhotonCms\Core\Entities\Migration\Contracts\MigrationCompilerInterface;

/**
 * Decouples buisiness logic from object storage, manipulation and internal logic over Migration entity.
 */
class MigrationRepository
{
    /**
     * Creates a migration file from MigrationTemplate.
     *
     * @param BaseMigrationTemplate $template
     * @param MigrationGatewayInterface $migrationGateway
     * @throws PhotonException
     */
    public function create(MigrationTemplateInterface $template, MigrationCompilerInterface $compiler, MigrationGatewayInterface $gateway) {
        $content = $compiler->compile($template);

        return $gateway->persistFromTemplate($content, $template);
    }

    /**
     * Runs a migration represented by a MigrationTemplate.
     *
     * @param BaseMigrationTemplate $template
     * @param MigrationGatewayInterface $migrationGateway
     */
    public function run(MigrationTemplateInterface $template, MigrationGatewayInterface $gateway){
        $gateway->runFromTemplate($template);
    }

    /**
     * Deletes a migration represented by a MigrationTemplate.
     *
     * @param BaseMigrationTemplate $template
     * @param MigrationGatewayInterface $migrationGateway
     */
    public function delete(MigrationTemplateInterface $template, MigrationGatewayInterface $gateway) {
        $gateway->deleteFromTemplate($template);
    }
}
