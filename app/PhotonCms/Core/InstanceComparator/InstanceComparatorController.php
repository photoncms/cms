<?php

namespace Photon\PhotonCms\Core\InstanceComparator;

use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Photon\PhotonCms\Core\Entities\DynamicModule\Contracts\DynamicModuleInterface;

/**
 * Chooses valid comparators to preform compare functionality.
 */
class InstanceComparatorController
{

    /**
     * A map of classes and their respective comparators.
     *
     * If you don't have an entity class, mock one.
     *
     * @var array
     */
    private $comparatorMap = [
        'Photon\\PhotonCms\\Core\\Entities\\Module\\Module' => 'Photon\\PhotonCms\\Core\\Entities\\Module\\ModuleComparator',
        'Photon\\PhotonCms\\Core\\Entities\\Field\\Field' => 'Photon\\PhotonCms\\Core\\Entities\\Field\\FieldComparator',
    ];

    /**
     * Comparator for all dynamic modules.
     *
     * @var string
     */
    private $dynamicModuleComparator = 'Photon\\PhotonCms\\Core\\Entities\\DynamicModule\\DynamicModuleComparator';

    /**
     * Maps a comparator to the passed class instance and calls the comparators' compare() method.
     *
     * Expects at least one input parameter which contains a mappable instance of a class.
     *
     * @return array
     * @throws PhotonException
     */
    public function compare()
    {
        $input = func_get_args();
        $className = (string) get_class($input[0]);
        if ($input[0] instanceof DynamicModuleInterface) {
            $comparator = new $this->dynamicModuleComparator();
            return call_user_func_array([$comparator, 'compare'], $input);
        }
        else if (isset($this->comparatorMap[$className])) {
            $comparator = new $this->comparatorMap[$className]();
            return call_user_func_array([$comparator, 'compare'], $input);
        }

        throw new PhotonException('UNDEFINED_COMPARATOR', ['className' => $className]);
    }
}