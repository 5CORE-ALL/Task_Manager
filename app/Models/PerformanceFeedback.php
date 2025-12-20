<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceFeedback extends Model
{
    use HasFactory;

    protected $table = 'performance_feedback';

    protected $fillable = [
        'employee_id',
        'performance_management_id',
        'given_by',
        'period_type',
        'period',
        'feedback_date',
        'communication_skill',
        'teamwork',
        'problem_solving',
        'initiative',
        'quality_of_work',
        'reliability',
        'adaptability',
        'leadership',
        'custom_parameters',
        'strengths',
        'areas_for_improvement',
        'general_feedback',
        'goals',
        'workspace_id'
    ];

    protected $casts = [
        'feedback_date' => 'date',
        'communication_skill' => 'decimal:2',
        'teamwork' => 'decimal:2',
        'problem_solving' => 'decimal:2',
        'initiative' => 'decimal:2',
        'quality_of_work' => 'decimal:2',
        'reliability' => 'decimal:2',
        'adaptability' => 'decimal:2',
        'leadership' => 'decimal:2',
        'custom_parameters' => 'array',
    ];

    /**
     * Get the employee (user) for this feedback
     */
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id', 'id');
    }

    /**
     * Get the user who gave this feedback
     */
    public function givenBy()
    {
        return $this->belongsTo(User::class, 'given_by', 'id');
    }

    /**
     * Get the performance management record
     */
    public function performanceManagement()
    {
        return $this->belongsTo(PerformanceManagement::class, 'performance_management_id');
    }
}
