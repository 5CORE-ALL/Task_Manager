<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'employee_id',
        'name',
        'department',
        'email_address',
        'month',
        'sal_previous',
        'increment',
        'salary_current',
        'productive_hrs',
        'approved_hrs',
        'approval_status',
        'etc_hours',
        'atc_hours',
        'incentive',
        'payable',
        'advance',
        'extra',
        'total_payable',
        'bank1',
        'bank2',
        'up',
        'payment_done',
        'is_enabled',
        'is_contractual',
        'workspace_id',
        'created_by'
    ];
    
    protected $casts = [
        'payment_done' => 'boolean',
        'is_enabled' => 'boolean',
        'is_contractual' => 'boolean',
        'sal_previous' => 'decimal:2',
        'increment' => 'decimal:2',
        'salary_current' => 'decimal:2',
        'etc_hours' => 'decimal:1',
        'atc_hours' => 'decimal:1',
        'incentive' => 'decimal:2',
        'payable' => 'decimal:2',
        'advance' => 'decimal:2',
        'extra' => 'decimal:2',
        'total_payable' => 'decimal:2',
        // Removed bank1 and bank2 decimal casting - they should be strings for account numbers
    ];
    
    // Relationship to User (Employee)
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
    
    // Relationship to creator
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
