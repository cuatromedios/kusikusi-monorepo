<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->command->error('Please use specific seeds depending on your needs:');
        $this->command->info('- Empty Webstite: php artisan db:seed --class=EmptyWebsiteSeeder');
        $this->command->info('- Blog: php artisan db:seed --class=BlogeSeeder');
    }
}
