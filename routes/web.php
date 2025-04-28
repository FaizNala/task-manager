<?php

use Illuminate\Support\Facades\Route;
use App\Exports\TasksExport;
use App\Models\Task;
use Maatwebsite\Excel\Facades\Excel;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/reports/export', function () {
    $tasks = Task::query();

    if (request()->has('project_id')) {
        $tasks->where('project_id', request('project_id'));
    }
    if (request()->has('start_date') && request()->has('end_date')) {
        $tasks->whereBetween('deadline', [request('start_date'), request('end_date')]);
    }
    if (request()->has('manager_id')) {
        $tasks->whereHas('project', fn($q) => $q->where('manager_id', request('manager_id')));
    }

    if (request()->has('user_id')) {
        $tasks->where('assigned_to', request('user_id'));
    }

    if (auth()->user()->hasRole('manager')) {
        $tasks->whereHas('project', fn($q) => $q->where('manager_id', auth()->id()));
    } elseif (auth()->user()->hasRole('staff')) {
        $tasks->where('assigned_to', auth()->id());
    }

    return Excel::download(new TasksExport($tasks->get()), 'tasks-export-' . now()->format('Y-m-d') . '.xlsx');
})->name('filament.admin.resources.reports.export');


