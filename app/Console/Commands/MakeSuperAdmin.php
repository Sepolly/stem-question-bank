<?php

namespace App\Console\Commands;

use App\Services\UserService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MakeSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make-super-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(UserService $userService)
    {
        $name = $this->ask('name');
        $email = $this->ask('email');
        $password = generateUniquePassword();

        $user = $userService->addUser(
            data: [
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
            ],
            role: 'super admin',
        );

        if (! $user) {
            $this->error('error creating super admin');
        }
        $this->info('super admin created.');
    }
}
