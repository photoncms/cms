<?php

$factory->define(\Photon\PhotonCms\Core\Entities\Module\Module::class, function (Faker\Generator $faker) {

    $types = [
        'single_entry',
        'non_sortable',
        'sortable',
        'multilevel_sortable'
    ];

    $name = $faker->name;

    return [
        'type' => $types[mt_rand(0,4)],
        'name' => $name,
        'icon' => 'icon-folder-open-alt',
        'model_name' =>  strtolower(\Illuminate\Support\Str::slug($name, '_')),
        'table_name' =>  strtolower(\Illuminate\Support\Str::slug($name, '_')),
//        'anchor_text' =>  strtolower(\Illuminate\Support\Str::slug($name, '_'))
    ];

});