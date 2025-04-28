<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Daftar permission yang akan dibuat
        $permissions = [
            // Project
            'view_project',
            'view_any_project', // <<< Tambahkan ini
            'create_project',
            'update_project',
            'delete_project',

            // Task
            'view_task',
            'view_any_task', // <<< Tambahkan ini
            'create_task',
            'update_task',
            'delete_task',
            'update_task_details', // <<< Tambahkan ini

            // User
            'view_user',
            'view_any_user', // <<< Tambahkan ini
            'create_user',
            'update_user',
            'delete_user',

            // Report (custom)
            'view_report',
            'view_any_report', // <<< Tambahkan ini
            'export_report',

            // Settings (custom)
            'manage_settings',

            // Position
            'view_position',
            'view_any_position', // <<< Tambahkan ini
            'create_position',
            'update_position',
            'delete_position',

            // Role
            'view_role',
            'view_any_role', // <<< Tambahkan ini
            'create_role',
            'update_role',
            'delete_role',

        ];

        // Buat permission
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles dan assign permissions
        // 1. Admin: semua permission
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        // 2. Manager: project & task & report
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $manager->syncPermissions([
            'view_project',
            'view_any_project',
            'create_project',
            'update_project',
            'view_task',
            'view_any_task',
            'create_task',
            'update_task',
            'delete_task',
            'update_task_details',
            'view_report',
            'view_any_report',
            'export_report',
        ]);

        // 3. Staff: hanya task
        $staff = Role::firstOrCreate(['name' => 'staff']);
        $staff->syncPermissions([
            'view_task',
            'view_any_task',
            'update_task',
        ]);
    }
}
