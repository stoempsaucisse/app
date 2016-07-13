<?php

use Symfony\Component\Security\Acl\Permission\MaskBuilderInterface as MaskBuilderContract;

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

$factory->define(Microffice\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => bcrypt(str_random(10)),
        'remember_token' => bcrypt(str_random(10)),
    ];
});

$factory->define(Microffice\AccessControl\Acl::class, function (Faker\Generator $faker) {
    return [
        'user_id' => null,
    ];
});

$factory->define(Microffice\AccessControl\Ace::class, function (Faker\Generator $faker) {
    return [
        'object' => Microffice\User::class,
        'object_id' => null,
        'mask' => app(MaskBuilderContract::class)
                    ->add('view'),
    ];
});
