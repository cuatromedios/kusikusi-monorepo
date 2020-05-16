<?php

use Illuminate\Database\Seeder;
use App\Models\Entity;
use Kusikusi\Models\User;

class ApiTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_name = "Administrator";
        $user_email = "admin@example.com";
        $user_profile = "admin";
        $user_password = 'Hello123';

        /*print("*** Generated user:\n");
        print("{\n");
        print("  \"email\": \"{$user_email}\",\n");
        print("  \"password\": \"{$user_password}\",\n");*/
        $user = new User([
            "name" => $user_name,
            "email" => $user_email,
            "password" => $user_password,
            "profile" => $user_profile
        ]);
        // print("}\n");

        $user->save();
    }
}
