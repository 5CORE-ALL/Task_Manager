<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'giver_id',
        'receiver_id',
        'amount',
        'deduction_date',
        'description',
        'status',
        'notification_status',
        'workspace_id',
        'created_by',
        // Keep existing fields for backward compatibility
        'employee_id',
        'employee_name',
        'department',
        'deduction_month',
        'requested_deduction',
        'deduction_reason',
        'approved_deduction',
        'approval_reason',
        'approved_by',
        'review_date',
        'workspace'
    ];

    protected $casts = [
        'deduction_date' => 'date',
        'amount' => 'decimal:2',
        'review_date' => 'date',
        'requested_deduction' => 'decimal:2',
        'approved_deduction' => 'decimal:2',
    ];

    // Relationships for new deduction system
    public function giver()
    {
        return $this->belongsTo(User::class, 'giver_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    // Backward compatibility relationships
    public function employee()
    {
        return $this->belongsTo(\Workdo\Hrm\Entities\Employee::class);
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    // Scopes
    public function scopeForWorkspace($query, $workspace)
    {
        return $query->where('workspace', $workspace);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
