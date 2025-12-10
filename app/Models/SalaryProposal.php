<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryProposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'employee_name',
        'department',
        'review_month',
        'proposal_type',
        'proposed_amount',
        'comments',
        'approved_by',
        'approval_status',
        'workspace',
        'created_by'
    ];

    protected $casts = [
        'proposed_amount' => 'decimal:2',
    ];

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
