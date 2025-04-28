<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run()
    {
        $managers = User::role('manager')->get();

        $projects = [
            [
                'name' => 'Pengembangan Aplikasi Mobile',
                'description' => 'Pembangunan aplikasi mobile untuk layanan pelanggan',
                'start_date' => Carbon::now()->subMonths(1),
                'end_date' => Carbon::now()->addMonths(2),
            ],
            [
                'name' => 'Migrasi Server ke Cloud',
                'description' => 'Pemindahan infrastruktur server ke AWS Cloud',
                'start_date' => Carbon::now()->subWeeks(2),
                'end_date' => Carbon::now()->addMonths(3),
            ],
            [
                'name' => 'Pelatihan Karyawan Q3',
                'description' => 'Program pengembangan skill untuk karyawan kuartal 3',
                'start_date' => Carbon::now()->addDays(7),
                'end_date' => Carbon::now()->addMonths(1),
            ],
            [
                'name' => 'Redesign Website Perusahaan',
                'description' => 'Pembaruan tampilan dan fungsionalitas website utama',
                'start_date' => Carbon::now()->subMonths(3),
                'end_date' => Carbon::now()->subWeeks(1),
            ],
            [
                'name' => 'Implementasi ERP Baru',
                'description' => 'Penerapan sistem ERP terbaru untuk semua departemen',
                'start_date' => Carbon::now()->subMonths(2),
                'end_date' => Carbon::now()->addMonths(2),
            ],
            [
                'name' => 'Audit Keamanan Jaringan',
                'description' => 'Pemeriksaan keamanan jaringan perusahaan secara menyeluruh',
                'start_date' => Carbon::now()->subWeeks(3),
                'end_date' => Carbon::now()->addWeeks(4),
            ],
            [
                'name' => 'Pembangunan Data Warehouse',
                'description' => 'Pengembangan sistem data warehouse untuk analisis data',
                'start_date' => Carbon::now()->addWeeks(2),
                'end_date' => Carbon::now()->addMonths(4),
            ],
            [
                'name' => 'Upgrade Infrastruktur IT',
                'description' => 'Peningkatan perangkat keras dan perangkat lunak infrastruktur IT',
                'start_date' => Carbon::now()->subMonths(1),
                'end_date' => Carbon::now()->addMonths(1),
            ],
            [
                'name' => 'Penerapan Kebijakan Privasi Data',
                'description' => 'Implementasi kebijakan privasi data sesuai regulasi terbaru',
                'start_date' => Carbon::now()->subMonths(4),
                'end_date' => Carbon::now()->subMonths(1),
            ],
            [
                'name' => 'Pembaruan Sistem',
                'description' => 'Pembaruan sistem aplikasi internal perusahaan',
                'start_date' => Carbon::now()->subWeeks(4),
                'end_date' => Carbon::now()->addWeeks(4),
            ],
            [
                'name' => 'Pengembangan Sistem Pembayaran',
                'description' => 'Pembangunan sistem pembayaran online untuk e-commerce',
                'start_date' => Carbon::now()->addDays(14),
                'end_date' => Carbon::now()->addMonths(3),
            ],
        ];

        foreach ($projects as $projectData) {
            $manager = $managers->random();
            Project::create(array_merge($projectData, [
                'manager_id' => $manager->id,
                'status' => $projectData['status'] ?? 'not_started',
                'auto_update_status' => true
            ]));
        }
    }
}
