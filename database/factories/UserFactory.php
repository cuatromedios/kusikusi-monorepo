<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Kusikusi\Models\User;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    $password = $faker->password(12,14);
    print("  \"password\": \"{$password}\"\n");
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => $password,
        'profile' => "guest"
    ];
});
