<?php

namespace App\Exports;

use App\Models\Task;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TasksExport implements FromCollection, WithHeadings, WithMapping
{
    protected $tasks;

    public function __construct($tasks)
    {
        $this->tasks = $tasks;
    }

    public function collection()
    {
        return $this->tasks;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'Project',
            'Assigned To',
            'Status',
            'Deadline',
            'Created At',
        ];
    }

    public function map($task): array
    {
        return [
            $task->id,
            $task->title,
            $task->project->name,
            $task->assignedTo->name,
            ucfirst(str_replace('_', ' ', $task->status)),
            $task->deadline->format('Y-m-d H:i'),
            $task->created_at->format('Y-m-d H:i'),
        ];
    }
}
