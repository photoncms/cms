<?php

namespace Photon\PhotonCms\Core\Entities\Field;

use Illuminate\Database\Eloquent\Model;
use Photon\PhotonCms\Core\Entities\FieldType\FieldTypeFactory;

class Field extends Model
{
    protected $fillable = [
        'name',
        'type',
        'related_module',
        'relation_name',
        'pivot_table',
        'column_name',
        'virtual_name',
        'tooltip_text',
        'validation_rules',
        'module_id',
        'order',
        'editable',
        'disabled',
        'hidden',
        'is_system',
        'virtual',
        'lazy_loading',
        'default',
        'local_key',
        'foreign_key',
        'active_entry_filter',
        'is_default_search_choice',
        'can_create_search_choice',
        'nullable',
        'indexed'
    ];

    public function fieldType() {
        return $this->hasOne('\Photon\PhotonCms\Core\Entities\FieldType\FieldType', 'id', 'type');
    }

    public function module() {
        return $this->hasOne('\Photon\PhotonCms\Core\Entities\Module\Module', 'id', 'module_id');
    }

    /**
     * Returns a unique user name after determining which type of the field and how it affects it.
     *
     * @return string
     */
    public function getUniqueName()
    {
        $type = $this->fieldType;
        $type = FieldTypeFactory::makeFromBaseObject($type);

        if ($this->virtual) {
            return $this->virtual_name;
        }
        elseif ($type->isRelation()) {
            return $this->relation_name;
        }
        else {
            return $this->column_name;
        }
    }
}