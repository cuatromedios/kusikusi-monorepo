<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $email = "admin@example.com";
        $password = uniqid('', true);
        $this->command->info('--- CRETENDIALS:');
        $this->command->info('  - Email: '. $email);
        $this->command->info('  - Passowrd: '. $password);
        $user = new User([
            "name" => "Administrator",
            "email" => $email,
            "password" => bcrypt($password),
        ]);
        $user->save();
    }
}