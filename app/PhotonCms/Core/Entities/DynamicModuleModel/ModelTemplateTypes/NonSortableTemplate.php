<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleModel\ModelTemplateTypes;

use Photon\PhotonCms\Core\Entities\DynamicModuleModel\ModelTemplateTypes\DynamicModuleModelTemplate;
use Photon\PhotonCms\Core\Entities\ModelAttribute\ModelAttributeFactory;
use Photon\PhotonCms\Core\Entities\ModelRelation\ModelRelationFactory;

class NonSortableTemplate extends DynamicModuleModelTemplate
{
    /**
     * Array of default fields for each module.
     * These fields are mandatory for each module in photon, and they are automatically added to the migration.
     *
     * @var array
     */
    private static $defaultFields = [
        [
            'name' => 'id',
            'field_type' => 11
        ],
        [
            'name' => 'created_at',
            'field_type' => 12
        ],
        [
            'name' => 'updated_at',
            'field_type' => 12
        ],
        [
            'name' => 'anchor_text',
            'field_type' => 13
        ],
        [
            'name' => 'anchor_html',
            'field_type' => 2
        ]
    ];

    /**
     * Array of default relations for each module.
     * These relations are mandatory for each module in photon, and they are automatically added to the migration.
     *
     * @var array
     */
    private static $defaultRelations = [
        [
            'name' => 'created_by',
            'type' => 7,
            'laravel_type' => 'integer',
            'relation_name' => 'created_by',
            'related_module' => 1,
            'default' => null,
            'nullable' => 1,
            'disabled' => 1
        ],
        [
            'name' => 'updated_by',
            'type' => 7,
            'laravel_type' => 'integer',
            'relation_name' => 'updated_by',
            'related_module' => 1,
            'default' => null,
            'nullable' => 1,
            'disabled' => 1
        ]
    ];

    /**
     * Class constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->addAttributes(ModelAttributeFactory::makeMultiple(self::$defaultFields));
        if (config('photon.use_slugs')) {
            $this->addAttribute(ModelAttributeFactory::make(['name' => 'slug', 'field_type' => 13]));
        }
        
        $this->addRelations(ModelRelationFactory::makeMultipleFromFieldDataArray(self::$defaultRelations, $this->tableName));
        $this->setInheritance('Illuminate\Database\Eloquent\Model');
        $this->addImplementation('Photon\PhotonCms\Core\Entities\DynamicModuleModel\Contracts\CanFakeNodeInterface');
    }
}