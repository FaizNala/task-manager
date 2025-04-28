<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Filament\Widgets\BarChartWidget;
use Illuminate\Support\Facades\DB;

class TasksChart extends BarChartWidget
{
    protected static ?string $heading = 'Tasks Distribution';
    protected static ?string $maxHeight = '300px';
    protected static ?string $pollingInterval = '30s';

    protected function getData(): array
    {
        $user = auth()->user();

        $query = Task::query();

        if ($user->hasRole('manager')) {
            $query->whereHas('project', fn($q) => $q->where('manager_id', $user->id));
        } elseif ($user->hasRole('staff')) {
            $query->where('assigned_to', $user->id);
        }

        $data = $query->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Tasks Count',
                    'data' => [
                        $data['to_do'] ?? 0,
                        $data['in_progress'] ?? 0,
                        $data['done'] ?? 0,
                    ],
                    'backgroundColor' => [
                        '#6b7280',
                        '#f59e0b',
                        '#10b981',
                    ],
                    'borderColor' => [
                        '#374151',
                        '#d97706',
                        '#059669',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => ['To Do', 'In Progress', 'Done'],
        ];
    }
}
