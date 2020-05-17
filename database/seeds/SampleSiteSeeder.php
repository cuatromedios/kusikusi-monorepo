<?php

use Illuminate\Database\Seeder;
use App\Models\Entity;
use Faker\Generator as Faker;

class SampleSiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $media_per_home = 1;
        $sections_count = 2;
        $media_per_section = 1;
        $pages_count = 2;
        $media_per_page = 2;

        $home = Entity::where('id', 'home')->first();
        for ($s = 0; $s < $sections_count; $s++) {
            $section = factory(App\Models\Entity::class)->make([
                "model" => "section",
                "parent_entity_id" => $home->id
            ]);
            $section->save();
            for ($m = 0; $m < $media_per_section; $m++) {
                $medium = factory(App\Models\Entity::class)->states('medium')->make();
                $medium->save();
                $section->addRelation([
                    "called_entity_id" => $medium->id,
                    "kind" => \Kusikusi\Models\EntityRelation::RELATION_MEDIA,
                    "tags" => $m == 0 ? ['icon', 'social'] : ['gallery'],
                    "position" => $m
                ]);
            }
            for ($p = 0; $p < $pages_count; $p++) {
                $page = factory(App\Models\Entity::class)->make([
                    "model" => "page",
                    "parent_entity_id" => $section->id,
                    "properties" => []
                ]);
                $page->save();
                for ($m = 0; $m < $media_per_page; $m++) {
                    $medium = factory(App\Models\Entity::class)->states('medium')->make();
                    $medium->save();
                    $page->addRelation([
                        "called_entity_id" => $medium->id,
                        "kind" => \Kusikusi\Models\EntityRelation::RELATION_MEDIA,
                        "tags" => $m == 0 ? ['icon', 'social'] : ['slider'],
                        "position" => $m
                    ]);
                }
            }
        }
        for ($m = 0; $m < $media_per_home; $m++) {
            $medium = factory(App\Models\Entity::class)->states('medium')->make();
            $medium->save();
            $home->addRelation([
                "called_entity_id" => $medium->id,
                "kind" => \Kusikusi\Models\EntityRelation::RELATION_MEDIA,
                "tags" => ['hero'],
                "position" => $m
            ]);
        }
    }
}
