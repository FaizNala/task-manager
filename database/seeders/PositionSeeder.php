<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    public function run()
    {
        $positions = [
            ['name' => 'Frontend Developer', 'department' => 'IT'],
            ['name' => 'Backend Developer', 'department' => 'IT'],
            ['name' => 'UI/UX Designer', 'department' => 'IT'],
            ['name' => 'HR Manager', 'department' => 'HR'],
            ['name' => 'HR Staff', 'department' => 'HR'],
            ['name' => 'Project Manager', 'department' => 'Operations'],
            ['name' => 'IT Manager', 'department' => 'IT'],
            ['name' => 'Finance Manager', 'department' => 'Finance'],
            ['name' => 'Finance Staff', 'department' => 'Finance'],
            ['name' => 'Marketing Manager', 'department' => 'Marketing'],
            ['name' => 'Marketing Staff', 'department' => 'Marketing'],
            ['name' => 'Sales Manager', 'department' => 'Sales'],
            ['name' => 'Sales Staff', 'department' => 'Sales'],
            ['name' => 'Customer Support', 'department' => 'Support'],
            ['name' => 'Data Analyst', 'department' => 'IT'],
            ['name' => 'System Administrator', 'department' => 'IT'],
            ['name' => 'Network Engineer', 'department' => 'IT'],
        ];

        foreach ($positions as $position) {
            Position::create($position);
        }
    }
}
