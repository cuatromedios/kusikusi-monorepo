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
        $home = Entity::create(['id' => 'home', 'model' => 'Home', 'view' => 'home']);
        EntityContent::createFor('home', [
            'title' => 'Kusikusi',
            'slug' =>'',
            'welcome' => 'A brand new Kusikusi Website'
        ]);
        $home->touch();
    }
}
