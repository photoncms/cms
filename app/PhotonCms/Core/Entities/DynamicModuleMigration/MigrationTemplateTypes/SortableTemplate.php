<?php

namespace Photon\PhotonCms\Core\Entities\DynamicModuleMigration\MigrationTemplateTypes;

use Photon\PhotonCms\Core\Entities\DynamicModuleMigration\MigrationTemplateTypes\DynamicModuleMigrationTemplate;

class SortableTemplate extends DynamicModuleMigrationTemplate
{
    /**
     * Array of default fields for each module.
     * These fields are mandatory for each module in photon, and they are automatically added to the migration.
     *
     * @var array
     */
    protected $defaultFields = [
        [
            'name' => 'id',
            'field_type' => 11,
            'laravel_type' => 'increments'
        ],
        [
            'name' => 'lft',
            'field_type' => 11,
            'laravel_type' => 'integer'
        ],
        [
            'name' => 'rgt',
            'field_type' => 11,
            'laravel_type' => 'integer'
        ],
        [
            'name' => 'parent_id',
            'field_type' => 11,
            'laravel_type' => 'integer',
            'nullable' => true,
            'default' => null
        ],
        [
            'name' => 'depth',
            'field_type' => 11,
            'laravel_type' => 'integer',
            'default' => 0
        ],
        [
            'name' => 'scope_id',
            'field_type' => 11,
            'laravel_type' => 'integer',
            'nullable' => true,
            'default' => null
        ],
        [
            'name' => 'created_at',
            'field_type' => 12,
            'nullable' => true,
            'laravel_type' => 'timestamp'
        ],
        [
            'name' => 'updated_at',
            'field_type' => 12,
            'nullable' => true,
            'laravel_type' => 'timestamp'
        ],
        [
            'name' => 'anchor_text',
            'field_type' => 13,
            'laravel_type' => 'string',
            'parameters' => [2000],
            'indexed' => true
        ],
        [
            'name' => 'anchor_html',
            'field_type' => 2,
            'laravel_type' => 'text'
        ]
    ];

    /**
     * Array of default relations for each module.
     * These relations are mandatory for each module in photon, and they are automatically added to the migration.
     *
     * @var array
     */
    protected $defaultRelations = [
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
}