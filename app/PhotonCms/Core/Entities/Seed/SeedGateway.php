<?php

namespace Photon\PhotonCms\Core\Entities\Seed;

use Illuminate\Support\Facades\Artisan;
use Photon\PhotonCms\Core\Entities\Seed\Contracts\SeedTemplateInterface;
use Photon\PhotonCms\Core\Entities\Seed\Contracts\SeedGatewayInterface;

/**
 * Decouples repository from data sources.
 */
class SeedGateway implements SeedGatewayInterface
{

    /**
     * Uses a seed template to create a seed file through iseed.
     *
     * @param SeedTemplateInterface $seed
     */
    public function create(SeedTemplateInterface $seed)
    {
        Artisan::call('iseed', $this->prepareCreationArguments($seed));
    }

    /**
     * Prepares all arguments for artisan call.
     *
     * @param SeedTemplateInterface $seed
     * @return array
     */
    private function prepareCreationArguments(SeedTemplateInterface $seed)
    {

        $arguments['tables'] = implode(',', $seed->getTables());

        if ($seed->usesForce()) {
            $arguments['--force'] = true;
        }

        if ($seed->usesClean()) {
            $arguments['--clean'] = true;
        }

        if ($seed->hasDatabaseName()) {
            $arguments['--database'] = $seed->getDatabaseName();
        }

        if ($seed->usesMax()) {
            $arguments['--max'] = $seed->getMax();
        }

        if ($seed->hasExclusions()) {
            $arguments['--exclude'] = implode(',', $seed->getExclusions());
        }

        if ($seed->hasPreruns()) {
            $arguments['--prerun'] = $this->compileEvents(
                $seed->getPreruns(),
                $seed->getTables()
            );
        }

        if ($seed->hasPostruns()) {
            $arguments['--postrun'] = $this->compileEvents(
                $seed->getPostruns(),
                $seed->getTables()
            );
        }

        if (!$seed->isIndexed()) {
            $arguments['--noindex'] = true;
        }

        return $arguments;
    }

    /**
     * Compiles prerun or postrun events based mapping $events to $tableNames.
     * This is only to be used for prerun and postrun events!
     *
     * @param array $events
     * @param array $tableNames
     * @return string
     */
    private function compileEvents($events, $tableNames)
    {
        $allEvents = [];
        foreach ($tableNames as $tableName) {
            $allEvents[] = (isset($events[$tableName]))
                ? $events[$tableName]
                : '';
        }
        
        return implode(',', $allEvents);
    }
}