<?php

namespace Database\Seeders;

use App\Models\Position;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Truncate tables
        Position::truncate();
        User::truncate();
        Role::truncate();
        Permission::truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('role_has_permissions')->truncate();
        Project::truncate();
        Task::truncate();

        // Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Run the seeders
        $this->call([
            PositionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            ProjectSeeder::class,
            TaskSeeder::class,
        ]);
    }
}
