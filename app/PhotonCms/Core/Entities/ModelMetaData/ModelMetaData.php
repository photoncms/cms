<?php

namespace Photon\PhotonCms\Core\Entities\ModelMetaData;

use Illuminate\Database\Eloquent\Model;

class ModelMetaData extends Model
{

    protected $table = 'model_meta_data';

    public function metaType()
    {
        return $this->belongsTo('Photon\PhotonCms\Core\Entities\ModelMetaType\ModelMetaType', 'model_meta_type_id');
    }
}