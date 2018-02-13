<?php

namespace Photon\PhotonCms\Core\Entities\Field\Contracts;

use Photon\PhotonCms\Core\Entities\Field\Field;

interface FieldGatewayInterface
{

    /**
     * Retrieves a Field instance by ID.
     *
     * @param int $id
     * @return Field
     */
    public function retrieve($id);

    /**
     * Persists a Field instance.
     *
     * @param \Photon\PhotonCms\Core\Entities\Field\Field $field
     * @return boolean
     */
    public function persist(Field $field);

    /**
     * Removes a Field instance.
     *
     * @param \Photon\PhotonCms\Core\Entities\Field\Field $field
     * @return boolean
     */
    public function delete(Field $field);
}