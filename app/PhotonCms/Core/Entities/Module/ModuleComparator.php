<?php

namespace Photon\PhotonCms\Core\Entities\Module;

use App;
use Photon\PhotonCms\Core\InstanceComparator\Contracts\InstanceComparatorInterface;
use Photon\PhotonCms\Core\Transform\TransformationController;
use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Photon\PhotonCms\Core\Entities\ChangeReport\ChangeReportFactory;

/**
 * Compares instances of two Module-s.
 */
class ModuleComparator implements InstanceComparatorInterface
{

    /**
     * Compare method for the Module entity.
     *
     * Returns an array of differences along with the change type (add, delete, update).
     * Expects two input parameters with instances of Module.
     *
     * @return Photon\PhotonCms\Core\Entities\ChangeReport\ChangeReport
     */
    public function compare()
    {
        $input = func_get_args();
        if (!isset($input[0]) && !($input[0] instanceof Module)) {
            throw new PhotonException('NOT_INSTANCE_OF_MODULE', ['given' => get_class($input[0]), 'expected' => '\Photon\PhotonCms\Core\Entities\Module\Module']);
        }
        if (!isset($input[1]) && !($input[1] instanceof Module)) {
            throw new PhotonException('NOT_INSTANCE_OF_MODULE', ['given' => get_class($input[1]), 'expected' => '\Photon\PhotonCms\Core\Entities\Module\Module']);
        }
        
        $first = $input[0];
        $second = $input[1];
        
        $changeType = null;
        if ($first->isEmpty) {
            $changeType = 'add';
        }
        else if ($second->isEmpty) {
            $changeType = 'delete';
        }
        else {
            $changeType = 'update';
        }

        $data = [];
        $first = App::call(
            function ($data, TransformationController $transformationController) {
                return $transformationController->objectFullTransformConverted($data);
            },
            ['data' => $first]
        );

        $second = App::call(
            function ($data, TransformationController $transformationController) {
                return $transformationController->objectFullTransformConverted($data);
            },
            ['data' => $second]
        );

        foreach ($first as $key => $item) {
            if ($item !== $second[$key]) {
                $data[$key] = [
                    'old' => $item,
                    'new' => $second[$key]
                ];
            }
        }

        return ChangeReportFactory::make($changeType, $data);
    }
}