<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;
use Faker\Provider\Lorem;
use Illuminate\Support\Str;
use App\Models\Entity;

$factory->define(Entity::class, function (Faker $faker) {
    $langs = config('cms.langs', ['']);
    $titles = [];
    $descriptions = [];
    $bodies = [];
    $slugs = [];
    foreach ($langs as $lang) {
        $titles[$lang] = $faker->sentence;
        $descriptions[$lang] = $faker->paragraph;
        $bodies[$lang] = $faker->text(250);
        $slugs[$lang] = Str::slug($titles[$lang]);
    }
    return [
        "model" => "entity",
        "parent_entity_id" => null,
        "properties" => [],
        "contents" => [
            "title" => $titles,
            "description" => $descriptions,
            "body" => $bodies,
            "slug" => $slugs,
        ]
    ];
});
$factory->state(Entity::class, 'medium', function (Faker $faker) {
    $title = $faker->lastName;
    echo "Downloading image...";
    $image = resource_path('images/turtle.png');
    echo " done.\n";
    return [
        "model" => "medium",
        "properties" => [
            "path" =>  $image
        ]
    ];
});
