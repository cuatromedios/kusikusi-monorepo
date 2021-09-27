<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class GenerateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically generate a new admin user with a custom password';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $email = $this->ask('Introduce an email');
        $password = $this->ask('Introduce a password');
        if ($this->confirm('Is the information correct?')) {
            $user = new User([
                "name" => "Administrator",
                "email" => $email,
                "password" => bcrypt($password),
            ]);
            $user->save();
            $this->info('The new admin was created successfully!');
        } else {
            $this->info('Command aborted');
        }
        return 0;
    }
}
