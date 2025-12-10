<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incentive extends Model
{
    use HasFactory;

    protected $fillable = [
        'giver_id',
        'receiver_id',
        'amount',
        // 'start_date',
        'end_date',
        'description',
        'status',
        'notification_status',
        'workspace_id',
        'created_by',
        // Keep existing fields for backward compatibility
        'employee_id',
        'employee_name',
        'department',
        'incentive_month',
        'requested_incentive',
        'incentive_reason',
        'approved_incentive',
        'approval_reason',
        'approved_by',
        'review_date',
        'workspace'
    ];

    protected $casts = [
        // 'start_date' => 'date',
        'end_date' => 'date',
        'amount' => 'decimal:2',
        'review_date' => 'date',
        'requested_incentive' => 'decimal:2',
        'approved_incentive' => 'decimal:2',
    ];

    // NEW: Relationships for new incentive system
    public function giver()
    {
        return $this->belongsTo(User::class, 'giver_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    // Relationships
    public function employee()
    {
        return $this->belongsTo(\Workdo\Hrm\Entities\Employee::class);
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    // Scopes
    public function scopeForWorkspace($query, $workspace)
    {
        return $query->where('workspace', $workspace);
    }
}
