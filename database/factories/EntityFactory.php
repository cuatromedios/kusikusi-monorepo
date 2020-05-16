<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use App\Models\Entity;

$factory->define(Entity::class, function (Faker $faker) {
    $langs = config('cms.langs', ['en_US']);
    $titles = [];
    $summaries = [];
    $bodies = [];
    $slugs = [];
    foreach ($langs as $lang) {
        $personProviderClass = "\\Faker\\Provider\\{$lang}\\Person";
        $companyProviderClass = "\\Faker\\Provider\\{$lang}\\Company";
        $textProviderClass = "\\Faker\\Provider\\{$lang}\\Text";
        $faker->addProvider(new $personProviderClass($faker));
        $faker->addProvider(new $companyProviderClass($faker));
        $faker->addProvider(new $textProviderClass($faker));
        $titles[$lang] = $faker->name;
        $summaries[$lang] = $faker->jobTitle;
        $bodies[$lang] = $faker->text(100);
        $slugs[$lang] = Str::slug($titles[$lang]);
    }
    return [
        "model" => "entity",
        "parent_entity_id" => null,
        "properties" => [],
        "contents" => [
            "title" => $titles,
            "summary" => $summaries,
            "body" => $bodies,
            "slug" => $slugs,
        ]
    ];
});
$factory->state(Entity::class, 'medium', function (Faker $faker) {
    $title = $faker->lastName;
    echo "Downloading image...";
    $image = resource_path('sampleImages/colibri.jpg');
    echo " done.\n";
    return [
        "model" => "entity",
        "parent_entity_id" => null,
        "properties" => [
            "path" =>  $image
        ]
    ];
});
