<?php

namespace App\Console\Commands;

use App\Services\RoleService;
use App\Services\UserService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MakeAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a super admin';

    public function __construct(
        protected RoleService $roleService,
        protected UserService $userService,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->ask('name');
        $email = $this->ask('email');

        $this->info('creating user...');

        $password = generateUniquePassword();

        $user = $this->userService->addUser([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $admin_role = $this->roleService->getRoleByName('admin');
        $user->assignRole($admin_role);

        $this->info('user created.');

    }
}
