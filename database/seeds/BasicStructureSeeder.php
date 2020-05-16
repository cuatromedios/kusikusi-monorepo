<?php

use Illuminate\Database\Seeder;
use App\Models\Entity;
use App\Models\Medium;

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
            $titles[$lang] = "A Kusikusi website";
            $welcomes[$lang] = "A welcome message";
            $descriptions[$lang] = "The site description";
            $slugs[$lang] = $lang === $langs[0] ? "" : str_replace('_', '-', strtolower($lang));
        }
        //The website (root) entity
        $website = new Entity([
            "id" => "website",
            "model" => "website",
            "contents"=> [
                "title" => $names
            ]
        ]);
        $website->save();

        //The favicon entity
        $favicon = new \App\Models\Medium([
            "id" => "favicon",
            "model" => "medium",
            "contents"=> [
                "title" => 'Favicon'
            ]
        ]);
        $favicon->save();
        if (!is_dir ( storage_path("media/".$favicon->id) )) mkdir(storage_path("media/".$favicon->id));
        copy(resource_path('images/icon.png'), storage_path("media/".$favicon->id."/file.png"));
        $website->addRelation([
            "called_entity_id" => $favicon->id,
            "kind" => \Kusikusi\Models\EntityRelation::RELATION_MEDIA,
            "tags" => ['favicon'],
            "position" => 0
        ]);
        $favicon->properties = array_merge($favicon->properties, Medium::getProperties(resource_path('images/icon.png')));
        $favicon->save();

        //Home entity
        $home = new Entity([
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
        $collections = new Entity([
            "id" => "collections",
            "parent_entity_id" => $website->id,
            "model" => "collections",
        ]);
        $collections->save();

        // A container for media entities
        $media = new Entity([
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
        $user = factory(Kusikusi\Models\User::class)->make([
            "name" => $user_name,
            "email" => $user_email,
            "profile" => $user_profile
        ]);
        print("}\n");

        $user->save();

    }
}
