<?php

use Illuminate\Database\Seeder;
use Kusikusi\Models\EntityModel;
use App\Models\Website;
use App\Models\Home;
use App\Models\Medium;
use Kusikusi\Models\User;

class BasicStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define some standard contents for each configured language
        $langs = config('cms.langs', ['']);
        $titles = [];
        $welcomes = [];
        $slugs = [];
        foreach ($langs as $lang) {
            $names[$lang] = "The Website Name";
            $iconNames[$lang] = "The Website Name Icon";
            $logoNames[$lang] = "The Website Name Logo";
            $titles[$lang] = "A Kusikusi website";
            $welcomes[$lang] = "A welcome message";
            $descriptions[$lang] = "The site description";
            $slugs[$lang] = $lang === $langs[0] ? "" : str_replace('_', '-', strtolower($lang));
        }
        //The website (root) entity
        $website = new Website([
            "id" => "website",
            "model" => "website",
            "contents" => [
                "title" => $names
            ],
            "properties" => [
                "theme_color" => "#006BB8",
                "background_color" => "#ffffff"
            ]
        ]);
        $website->save();

        //The favicon entity
        $icon = new \App\Models\Medium([
            "id" => "icon",
            "model" => "medium",
            "contents"=> [
                "title" => $iconNames
            ]
        ]);
        $icon->save();
        if (!is_dir ( storage_path("media/".$icon->id) )) mkdir(storage_path("media/".$icon->id));
        copy(resource_path('images/icon.png'), storage_path("media/".$icon->id."/file.png"));
        $website->addRelation([
            "called_entity_id" => $icon->id,
            "kind" => \Kusikusi\Models\EntityRelation::RELATION_MEDIA,
            "tags" => ['favicon', 'social'],
            "position" => 0
        ]);
        $icon->properties = array_merge($icon->properties, Medium::getProperties(resource_path('images/icon.png')));
        $icon->save();

        //The logo entity
        $logo = new \App\Models\Medium([
            "id" => "logo",
            "model" => "medium",
            "contents"=> [
                "title" => $logoNames
            ]
        ]);
        $logo->save();
        if (!is_dir ( storage_path("media/".$logo->id) )) mkdir(storage_path("media/".$logo->id));
        copy(resource_path('images/logo.svg'), storage_path("media/".$logo->id."/file.svg"));
        $website->addRelation([
            "called_entity_id" => $logo->id,
            "kind" => \Kusikusi\Models\EntityRelation::RELATION_MEDIA,
            "tags" => ['logo'],
            "position" => 0
        ]);
        $logo->properties = array_merge($logo->properties, Medium::getProperties(resource_path('images/icon.svg')));
        $logo->save();

        $website->save();

        //Home entity
        $home = new Home([
            "id" => "home",
            "model" => "home",
            "parent_entity_id" => $website->id,
            "properties" => [],
            "contents"=> [
                "title" => $titles,
                "welcome" => $welcomes,
                "description" => $descriptions,
                "slug" => $slugs
            ]
        ]);
        $home->save();

        // A container for collections, like categories
        $collections = new EntityModel([
            "id" => "collections",
            "parent_entity_id" => $website->id,
            "model" => "collections",
        ]);
        $collections->save();

        // A container for media entities
        $media = new Medium([
            "id" => "media",
            "parent_entity_id" => $website->id,
            "model" => "media",
        ]);
        $media->save();

        //The default admin user
        $user_name = "Administrator";
        $user_email = "admin@example.com";
        $user_profile = "admin";
        print("*** Generated user:\n");
        print("{\n");
        print("  \"email\": \"{$user_email}\",\n");
        $user = factory(User::class)->make([
            "name" => $user_name,
            "email" => $user_email,
            "profile" => $user_profile
        ]);
        print("}\n");

        $user->save();

    }
}
