<?php

namespace Photon\PhotonCms\Core\Entities\Module;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    /**
     * Fields allowed to be mass assigned
     *
     * @var array
     */
    protected $fillable = ['id', 'type', 'name', 'model_name', 'table_name', 'anchor_text', 'anchor_html', 'icon', 'category', 'reporting', 'lazy_loading', 'slug', 'max_depth'];

    /**
     * If false prevents auto ID assignment. This is important when we generate a uid and manually assign it.
     *
     * @var boolean
     */
    public $incrementing = false;

    public function modelMetaData()
    {
        return $this->hasMany('Photon\PhotonCms\Core\Entities\ModelMetaData\ModelMetaData', 'module_id');
    }

    /**
     * Returns related module fields
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fields()
    {
        return $this->hasMany('Photon\PhotonCms\Core\Entities\Field\Field', 'module_id', 'id');
    }

    /**
     * self-referential 1-N.
     * ToDo: check if is deprecated (Sasa|06/2016)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function categoryModule()
    {
        return $this->belongsTo('Photon\PhotonCms\Core\Entities\Module\Module', 'category');
    }
}
