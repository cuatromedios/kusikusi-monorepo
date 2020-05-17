<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;
use Faker\Provider\Lorem;
use Illuminate\Support\Str;
use Kusikusi\Models\EntityModel;
use App\Models\Medium;
use Intervention\Image\ImageManagerStatic as Image;
use PUGX\Shortid\Shortid;

$factory->define(EntityModel::class, function (Faker $faker) {
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
$factory->state(EntityModel::class, 'medium', function (Faker $faker) {
    $titles = [];
    $langs = config('cms.langs', ['']);
    foreach ($langs as $lang) {
        $titles[$lang] = $faker->sentence(3);
    }
    echo "Creating image...";
    $id = Shortid::generate(config('cms.short_id_length', 10));
    $width = rand(540, 960);
    $height = rand(540, 960);
    $image = Image::canvas($width, $height, getRandomGrey());
    for ($c = 0; $c < 5; $c++) {
        $image->circle(min($width, $height), rand(0, $width), rand(0, $height), function ($draw) {
            $draw->background(getRandomGrey());
        });
    }
    if (!is_dir ( storage_path("media/".$id) )) mkdir(storage_path("media/".$id));
    $image->save(storage_path("media/$id/file.png"));
    $properties = Medium::getProperties(storage_path("media/$id/file.png"));
    echo " done.\n";
    return [
        "id" => $id,
        "model" => "medium",
        "properties" => $properties,
        "contents" => [
            "title" => $titles
        ]
    ];
});

function getRandomGrey() {
    return "#".str_repeat(dechex(rand(64,204)),3);
}
