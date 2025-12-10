<?php
namespace Workdo\Taskly\Entities;

use Illuminate\Database\Eloquent\Model;

class MissedTask extends Model
{
    protected $table = 'missed_tasks';
    protected $fillable = [
        'task_id', 'title', 'task_type', 'schedule_type', 'missed_at', 'workspace'
    ];
}
