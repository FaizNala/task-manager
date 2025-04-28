<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Models\Task;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();

        // Query untuk tasks
        $taskQuery = Task::query();
        // Query untuk projects
        $projectQuery = Project::query();

        // Filter berdasarkan role
        if ($user->hasRole('manager')) {
            $taskQuery->whereHas('project', fn($q) => $q->where('manager_id', $user->id));
            $projectQuery->where('manager_id', $user->id);
        } elseif ($user->hasRole('staff')) {
            $taskQuery->where('assigned_to', $user->id);
            $projectQuery->whereHas('tasks', fn($q) => $q->where('assigned_to', $user->id));
        }

        // Hitung total
        $totalProjects = $projectQuery->count();
        $totalTasks = $taskQuery->count();
        $toDoTasks = (clone $taskQuery)->where('status', 'to_do')->count();
        $inProgressTasks = (clone $taskQuery)->where('status', 'in_progress')->count();
        $doneTasks = (clone $taskQuery)->where('status', 'done')->count();

        // Deadline tasks (untuk notifikasi)
        $upcomingDeadlines = (clone $taskQuery)
            ->where('deadline', '>=', now())
            ->where('deadline', '<=', now()->addDay())
            ->count();

        return [
            Stat::make('Total Projects', $totalProjects)
                ->description($user->hasRole('admin') ? 'All projects' : 'Your projects')
                ->descriptionIcon('heroicon-o-folder')
                ->color('primary'),

            Stat::make('Total Tasks', $totalTasks)
                ->description($user->hasRole('admin') ? 'All tasks' : 'Your tasks')
                ->descriptionIcon('heroicon-o-document-check')
                ->color('primary'),

            Stat::make('To Do', $toDoTasks)
                ->description('Tasks to do')
                ->descriptionIcon('heroicon-m-clock')
                ->color('gray'),

            Stat::make('In Progress', $inProgressTasks)
                ->description('Tasks in progress')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('warning'),

            Stat::make('Done', $doneTasks)
                ->description('Completed tasks')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Upcoming Deadlines', $upcomingDeadlines)
                ->description('Due in next 24 hours')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger')
                ->extraAttributes([
                    'class' => 'animate-pulse', // Efek visual untuk deadline
                ]),
        ];
    }
}
