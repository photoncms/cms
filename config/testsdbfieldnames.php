<?php

return [
    'database_field_names' => [
        'fields' => [
            'id' => 'id',
            'type' => 'type',
            'name' => 'name',
            'related_module' => 'related_module',
            'relation_name' => 'relation_name',
            'pivot_table' => 'pivot_table',
            'column_name' => 'column_name',
            'tooltip_text' => 'tooltip_text',
            'validation_rules' => 'validation_rules',
            'module_id' => 'module_id',
            'order' => 'order',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at'
        ],
        'modules' => [
            'id' => 'id',
            'category' => 'category',
            'type' => 'type',
            'name' => 'name',
            'model_name' => 'model_name',
            'table_name' => 'table_name',
            'anchor_text' => 'anchor_text',
            'icon' => 'icon',
            'reporting' => 'reporting',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at'
        ],
        'users' => [
            'id' => 'id',
            'module_parent' => 'module_parent',
            'email' => 'email',
            'password' => 'password',
            'confirmed' => 'confirmed',
            'confirmation_code' => 'confirmation_code',
            'remember_token' => 'remember_token',
            'api_token' => 'token',
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at'
        ],
        'nodes' => [
            'id' => 'id',
            'lft' => 'lft',
            'rgt' => 'rgt',
            'parent_id' => 'parent_id',
            'depth' => 'depth',
            'scope_id' => 'scope_id',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at'
        ],
        'dynamic_modules' => [
            'id' => 'id',
            'lft' => 'lft',
            'rgt' => 'rgt',
            'parent_id' => 'parent_id',
            'depth' => 'depth',
            'scope_id' => 'scope_id',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at',
            'anchor_text' => 'anchor_text',
            'has_children' => 'has_children'
        ],
        'field_types' => [
            'id' => 'id',
            'type' => 'type',
            'title' => 'title',
            'laravel_type' => 'laravel_type',
            'is_system' => 'is_system'
        ],
        'module_types' => [
            'id' => 'id',
            'type' => 'type',
            'title' => 'title'
        ],
        'menus' => [
            'id' => 'id',
            'name' => 'name',
            'title' => 'title',
            'is_system' => 'is_system',
            'description' => 'description',
            'link_types' => 'link_types',
            'max_depth' => 'max_depth',
            'created_at' => 'created_at',
            'updated_at' => 'updated_at'
        ],
        'menu_items' => [
            'id' => 'id',
            'lft' => 'lft',
            'rgt' => 'rgt',
            'parent_id' => 'parent_id',
            'depth' => 'depth',
            'menu_id' => 'menu_id',
            'menu_name' => 'menu_name', // request input
            'menu_link_type_id' => 'menu_link_type_id',
            'menu_link_type_name' => 'menu_link_type_name', // request input
            'title' => 'title',
            'resource_data' => 'resource_data',
            'slug' => 'slug',
            'link' => 'link', // response output
            'created_at' => 'created_at',
            'updated_at' => 'updated_at'

        ],
        'menu_link_types' => [
            'id' => 'id',
            'name' => 'name',
            'title' => 'title',
            'is_system' => 'is_system'
        ]
    ]
];