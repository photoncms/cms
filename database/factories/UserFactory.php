<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->defineAs(\Photon\PhotonCms\Dependencies\DynamicModels\User::class, 'newApiUser', function (Faker\Generator $faker) {
    return [
        'first_name' => $faker->name,
        'last_name' => $faker->name,
        'email' => $faker->email,
        'password' => bcrypt(str_random(10)),
        'confirmed' => 0,
        'confirmation_code' => str_random(30),
        'password_created_at' => new \Carbon\Carbon(),
    ];
});

$factory->defineAs(\Photon\PhotonCms\Dependencies\DynamicModels\User::class, 'registeredApiUser', function (Faker\Generator $faker) {
    return [
        'first_name' => $faker->name,
        'last_name' => $faker->name,
        'email' => $faker->email,
        'password' => bcrypt(str_random(10)),
        'confirmed' => 1,
        'confirmation_code' => null,
        'password_created_at' => new \Carbon\Carbon(),
    ];
});