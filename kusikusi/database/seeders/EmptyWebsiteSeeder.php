<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Kusikusi\Models\Entity;
use Kusikusi\Models\EntityContent;

class EmptyWebsiteSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            AdminSeeder::class,
        ]);
        $website = Entity::create([
            'id' => 'website',
            'model' => 'Website',
            'view' => 'website',
            'properties' => [
                "theme_color" => "#000000",
                "background_color" => "#ffffff"
            ]
        ]);
        EntityContent::createFor('website', [
            'title' => 'Website'
        ]);
        $website->touch();
        $menusContainer = Entity::create([
            'id' => 'menus-container',
            'model' => 'MenusContainer',
            'view' => 'menus-container'
        ]);
        $home = Entity::create([
            'id' => 'home',
            'model' => 'Home',
            'view' => 'home'
        ]);
        EntityContent::createFor('home', [
            'title' => 'Kusikusi',
            'slug' =>'',
            'welcome' => 'A brand new Kusikusi Website'
        ]);
        $home->touch();
    }
}
