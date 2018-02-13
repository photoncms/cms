<?php

$factory->define(\Photon\PhotonCms\Core\Entities\Field\Field::class, function (Faker\Generator $faker) {

    return [
        'type' => 'input-text',
        'name' => $faker->name,
        'column_name' => \Illuminate\Support\Str::slug($faker->name,'_'),
        'column_type' => 'string',
        'related_module' => mt_rand(0,1),
        'relation_name' => 'string',
        'pivot_table' => 'string',
        'tooltip_text' => $faker->text(),
        'validation_rules' => 'required',
        'module_id' => factory(\Photon\PhotonCms\Core\Entities\Module\Module::class)->create()->id
    ];

});