<?php

namespace Database\Seeders;

use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run()
    {

        // Admin
        $admin = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@company.id',
            'password' => Hash::make('password123'),
            'position_id' => Position::where('name', 'IT Manager')->first()?->id
        ]);
        $admin->assignRole('admin');

        // Managers
        $managers = [
            [
                'name' => 'Ani Wijaya',
                'email' => 'ani.wijaya@company.id',
                'password' => Hash::make('password123'),
                'position_id' => Position::where('name', 'Project Manager')->first()?->id
            ],
            [
                'name' => 'Bambang Sunaryo',
                'email' => 'bambang.sunaryo@company.id',
                'password' => Hash::make('password123'),
                'position_id' => Position::where('name', 'Project Manager')->first()?->id
            ],
            [
                'name' => 'Dewi Kurnia',
                'email' => 'dewi.kurnia@company.id',
                'password' => Hash::make('password123'),
                'position_id' => Position::where('name', 'HR Manager')->first()?->id
            ],

        ];

        foreach ($managers as $managerData) {
            $manager = User::create($managerData);
            $manager->assignRole('manager');
        }

        // Staff
        $staffMembers = [
            [
                'name' => 'Agus Setiawan',
                'email' => 'agus.setiawan@company.id',
                'password' => Hash::make('password123'),
                'position_id' => Position::where('name', 'Frontend Developer')->first()?->id
            ],
            [
                'name' => 'Citra Ayu',
                'email' => 'citra.ayu@company.id',
                'password' => Hash::make('password123'),
                'position_id' => Position::where('name', 'HR Staff')->first()?->id
            ],
            [
                'name' => 'Eko Prasetyo',
                'email' => 'eko.prasetyo@company.id',
                'password' => Hash::make('password123'),
                'position_id' => Position::where('name', 'Backend Developer')->first()?->id
            ],
            [
                'name' => 'Fitriani',
                'email' => 'fitriani@company.id',
                'password' => Hash::make('password123'),
                'position_id' => Position::where('name', 'UI/UX Designer')->first()?->id
            ],
        ];

        foreach ($staffMembers as $staffData) {
            $staff = User::create($staffData);
            $staff->assignRole('staff');
        }
    }
}
