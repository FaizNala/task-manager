<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run()
    {
        $projects = Project::all();
        $staffUsers = User::role('staff')->get();

        $tasks = [
            [
                'title' => 'Analisis Kebutuhan',
                'description' => 'Menganalisis kebutuhan fitur dari stakeholder',
                'status' => 'done',
            ],
            [
                'title' => 'Desain Mockup',
                'description' => 'Membuat desain UI untuk aplikasi mobile',
                'status' => 'in_progress',
            ],
            [
                'title' => 'Pengembangan Modul Login',
                'description' => 'Membuat sistem autentikasi pengguna',
                'status' => 'to_do',
            ],
            [
                'title' => 'Backup Database',
                'description' => 'Membuat backup lengkap database production',
                'status' => 'in_progress',
            ],
            [
                'title' => 'Uji Coba Aplikasi',
                'description' => 'Melakukan uji coba aplikasi di lingkungan staging',
                'status' => 'to_do',
            ],
            [
                'title' => 'Pelatihan Pengguna',
                'description' => 'Memberikan pelatihan kepada pengguna tentang cara menggunakan aplikasi',
                'status' => 'to_do',
            ],
            [
                'title' => 'Penyusunan Laporan Proyek',
                'description' => 'Menyusun laporan akhir proyek untuk diserahkan kepada manajemen',
                'status' => 'to_do',
            ],
            [
                'title' => 'Perbaikan Bug',
                'description' => 'Memperbaiki bug yang ditemukan selama uji coba aplikasi',
                'status' => 'in_progress',
            ],
            [
                'title' => 'Deployment Aplikasi',
                'description' => 'Melakukan deployment aplikasi ke server produksi',
                'status' => 'to_do',
            ],
            [
                'title' => 'Pembuatan Dokumentasi',
                'description' => 'Membuat dokumentasi teknis untuk aplikasi',
                'status' => 'to_do',
            ],
            // tambahin semua task lainmu di sini juga
        ];

        // Hitung jumlah task yang dialokasikan ke project
        $projectTaskCount = [];

        foreach ($tasks as $taskData) {
            $availableProjects = $projects->filter(function ($project) use ($projectTaskCount) {
                // Maksimal 5 task per project
                return ($projectTaskCount[$project->id] ?? 0) < 5;
            });

            if ($availableProjects->isEmpty()) {
                // Kalau semua project sudah penuh
                break;
            }

            $project = $availableProjects->random();
            $assignedUser = $staffUsers->random();

            $startDate = Carbon::parse($project->start_date);
            $endDate = Carbon::parse($project->end_date);
            $daysBetween = $startDate->diffInDays($endDate);

            // Tentukan deadline berdasarkan status task
            if ($taskData['status'] === 'done') {
                // Dekat start_date
                $deadline = $startDate->copy()->addDays(rand(0, floor($daysBetween * 0.3)));
            } elseif ($taskData['status'] === 'in_progress') {
                // Sekitar tengah-tengah
                $deadline = $startDate->copy()->addDays(rand(floor($daysBetween * 0.3), floor($daysBetween * 0.7)));
            } else {
                // Mendekati end_date
                $deadline = $startDate->copy()->addDays(rand(floor($daysBetween * 0.7), $daysBetween));
            }

            Task::create([
                'title' => $taskData['title'],
                'description' => $taskData['description'],
                'status' => $taskData['status'],
                'deadline' => $deadline,
                'project_id' => $project->id,
                'assigned_to' => $assignedUser->id,
            ]);

            // Tambahkan counter task untuk project ini
            $projectTaskCount[$project->id] = ($projectTaskCount[$project->id] ?? 0) + 1;
        }
    }
}
