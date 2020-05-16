<?php

use Illuminate\Database\Seeder;
use App\Models\Entity;

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
