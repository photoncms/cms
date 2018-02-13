<?php

namespace Photon\PhotonCms\Core\Entities\ChangeReport;

use Photon\PhotonCms\Core\Exceptions\PhotonException;
use Photon\PhotonCms\Core\Entities\ChangeReport\Contracts\ChangeReportInterface;

class ChangeReport implements ChangeReportInterface
{

    /**
     * Type of the change being performed on an entity.
     *
     * @var string
     */
    private $changeType = '';

    /**
     * Collection of changed data.
     *
     * @var array
     */
    private $data = [];

    /**
     * Available/allowed change types.
     *
     * @var array
     */
    private $availableChangeTypes = [
        'add',
        'delete',
        'update',
        'run'
    ];

    /**
     * Class constructor.
     *
     * @param string $changeType
     * @param array $data
     */
    public function __construct($changeType = null, $data = null)
    {
        if ($changeType !== null) {
            $this->setChangeName($changeType);
        }
        if ($data !== null) {
            $this->setData($data);
        }
    }

    /**
     * Returns the change name.
     *
     * @return string
     */
    public function getChangeName()
    {
        return $this->changeType;
    }

    /**
     * Sets change type name.
     *
     * Change names are restricted to a specific set of possibilities in $this->availableChangeTypes.
     *
     * @param string $changeType
     * @throws PhotonException
     */
    public function setChangeName($changeType)
    {
        if (in_array($changeType, $this->availableChangeTypes)) {
            $this->changeType = $changeType;
        }
        else {
            throw new PhotonException('ILLEGAL_CHANGE_TYPE', ['changeType' => $changeType]);
        }
    }

    /**
     * Returns change report data.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Sets change report data
     *
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * Compiles report data into an array.
     * ToDo: Should be moved to a transformer (Sasa|03/2016)
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'change_type' => $this->changeType,
            'data' => $this->data
        ];
    }
}