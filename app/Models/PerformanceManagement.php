<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceManagement extends Model
{
    use HasFactory;

    protected $table = 'performance_management';

    protected $fillable = [
        'employee_id',
        'user_id',
        'period_type',
        'period',
        'start_date',
        'end_date',
        'etc_hours',
        'atc_hours',
        'total_working_hours',
        'productive_hours',
        'tasks_completed',
        'avg_task_duration_minutes',
        'avg_task_duration_days',
        'total_tasks_assigned',
        'total_tasks_completed',
        'task_completion_rate',
        'efficiency_score',
        'productivity_score',
        'task_performance_score',
        'timeliness_score',
        'overall_score',
        'workspace_id',
        'created_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'etc_hours' => 'decimal:2',
        'atc_hours' => 'decimal:2',
        'total_working_hours' => 'decimal:2',
        'productive_hours' => 'decimal:2',
        'avg_task_duration_minutes' => 'decimal:2',
        'avg_task_duration_days' => 'decimal:2',
        'task_completion_rate' => 'decimal:2',
        'efficiency_score' => 'decimal:2',
        'productivity_score' => 'decimal:2',
        'task_performance_score' => 'decimal:2',
        'timeliness_score' => 'decimal:2',
        'overall_score' => 'decimal:2',
    ];

    /**
     * Get the employee (user) for this performance record
     */
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id', 'id');
    }

    /**
     * Get the user who created this record
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Get feedback records for this performance
     */
    public function feedbacks()
    {
        return $this->hasMany(PerformanceFeedback::class, 'performance_management_id');
    }
}
