<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Kusikusi\Models\Entity;
use Kusikusi\Models\EntityContent;

class BlogSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            EmptyWebsiteSeeder::class,
        ]);
        $blog = Entity::create(['id' => 'blog', 'model' => 'Blog', 'view' => 'blog', 'parent_entity_id' => 'home']);
        EntityContent::createFor('blog', [
            'title', 'Blog',
            'slug' =>'blog',
            'summary' => 'The blog summary'
        ]);
        $blog->touch();
    }
}