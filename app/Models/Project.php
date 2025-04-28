<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'manager_id',
        'start_date',
        'end_date',
        'status',
        'auto_update_status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'auto_update_status' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($project) {
            if ($project->auto_update_status ?? true) {
                $project->updateStatusBasedOnTasks();
            }
        });
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function getTasksCountAttribute()
    {
        return $this->tasks()
            ->select('status', \DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');
    }

    public function updateStatusBasedOnTasks()
    {
        if ($this->tasks()->count() === 0) {
            $this->status = 'not_started';
            return;
        }

        $totalTasks = $this->tasks()->count();
        $completedTasks = $this->tasks()->where('status', 'done')->count();
        $inProgressTasks = $this->tasks()->where('status', 'in_progress')->count();

        if ($completedTasks === $totalTasks) {
            $this->status = 'completed';
        } elseif ($inProgressTasks > 0 || $completedTasks > 0) {
            $this->status = 'in_progress';
        } else {
            $this->status = 'not_started';
        }
    }
    
    // Scope untuk filter berdasarkan status
    public function scopeStatus(Builder $query, string $status)
    {
        return $query->where('status', $status);
    }
}
