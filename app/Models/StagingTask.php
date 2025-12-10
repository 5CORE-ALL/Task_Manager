<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StagingTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'group',
        'task',
        'assignor_id',
        'assignee_id',
        'start_date',
        'end_date',
        'status',
        'priority',
        'task_note',
        'eta_time',
        'l1','l2','l3','l4','l5','l6','l7'
    ];

    public function event()
    {
        return $this->belongsTo(Staging::class, 'event_id');
    }

    public function assignor()
    {
        return $this->belongsTo(User::class, 'assignor_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }
}
