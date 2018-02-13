<?php

namespace Photon\PhotonCms\Core\Entities\ChangeReport\Contracts;

interface ChangeReportInterface
{

    /**
     * Returns the change name.
     *
     * @return string
     */
    public function getChangeName();

    /**
     * Sets change type name.
     *
     * @param string $changeType
     * @throws PhotonException
     */
    public function setChangeName($changeType);

    /**
     * Returns change report data.
     *
     * @return array
     */
    public function getData();

    /**
     * Sets change report data
     *
     * @param array $data
     */
    public function setData(array $data);

    /**
     * Compiles report data into an array.
     * ToDo: Should be moved to a transformer (Sasa|03/2016)
     *
     * @return array
     */
    public function toArray();
}