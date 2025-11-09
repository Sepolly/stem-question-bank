<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define permissions
        $permissions = [
            // Question management
            'question.create',
            'question.view',
            'question.update',
            'question.delete',
            'question.approve',
            'question.import',
            'question.export',

            // User management
            'user.create',
            'user.view',
            'user.update',
            'user.delete',
            'role.assign',

            // Subject & topic management
            'subject.create',
            'subject.view',
            'subject.update',
            'subject.delete',
            'topic.create',
            'topic.view',
            'topic.update',
            'topic.delete',

            // Event management
            'event.create',
            'event.view',
            'event.update',
            'event.delete',

            // System & monitoring
            'settings.manage',
            'logs.view',
            'analytics.view',
        ];

        // Create permissions if not exist
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'super admin']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $contributor = Role::firstOrCreate(['name' => 'contributor']);
        $viewer = Role::firstOrCreate(['name' => 'viewer']);


        // Assign permissions
        $admin->syncPermissions(Permission::all());

        $contributor->syncPermissions([
            'question.create',
            'question.view',
            'question.update',
            'event.view',
            'subject.view',
            'topic.view',
        ]);

        $viewer->syncPermissions([
            'question.view',
            'event.view',
            'subject.view',
            'topic.view',
        ]);
    }
}
