<?php

namespace Workdo\Taskly\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class DoneClear extends Model
{
    use HasFactory;

    protected $table = 'done_clears';

    protected $fillable = [
        'assignor_id',
        'assignor_name',
        'assignee_id', 
        'assignee_name',
        'description',
        'priority',
        'created_by',
        'workspace',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship with assignor (User who assigns)
    public function assignor()
    {
        return $this->belongsTo(User::class, 'assignor_id');
    }

    // Relationship with assignee (User who is assigned)
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    // Relationship with creator
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
