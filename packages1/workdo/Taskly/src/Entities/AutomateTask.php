<?php

namespace Workdo\Taskly\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AutomateTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'priority',
        'group',
        'schedule_type',
        'schedule_time',
        'schedule_days',
        'description',
        'start_date',
        'due_date',
        'assign_to',
        'split_tasks',
        'assignor',
          'eta_time',
        'link1',
         'link3',
        'link2',
        'link4',
        'link5',
        'link7',
        'link6',
        'assignor',
        'status',
        'order',
        'workspace',
    ];

    public function project()
    {
        return $this->hasOne('Workdo\Taskly\Entities\Project', 'id', 'project_id');
    }

    public function users()
    {
        return User::whereIn('email',explode(',',$this->assign_to))->get();
    }
    public function assignorUser()
    {
        return User::where('email',$this->assignor)->first();
    }
    
    public function assignorUsers()
    {
        if (empty($this->assignor)) {
            return collect([]);
        }
        return User::whereIn('email',explode(',',$this->assignor))->get();
    }

    public function comments()
    {
        return $this->hasMany('Workdo\Taskly\Entities\Comment', 'task_id', 'id')->orderBy('id', 'DESC');
    }

    public function taskFiles()
    {
        return $this->hasMany('Workdo\Taskly\Entities\TaskFile', 'task_id', 'id')->orderBy('id', 'DESC');
    }

    public function milestones()
    {
        return $this->hasOne('Workdo\Taskly\Entities\Milestone', 'id', 'milestone_id');
    }

    public function milestone()
    {
        return $this->milestone_id ? Milestone::find($this->milestone_id) : null;
    }

    public function stage()
    {
        return $this->hasOne('Workdo\Taskly\Entities\Stage', 'name', 'status');
    }

    public function sub_tasks()
    {
        return $this->hasMany('Workdo\Taskly\Entities\SubTask', 'task_id', 'id')->orderBy('id', 'DESC');
    }

    public function taskCompleteSubTaskCount()
    {
        return $this->sub_tasks->where('status', '=', '1')->count();
    }

    public function taskTotalSubTaskCount()
    {
        return $this->sub_tasks->count();
    }

    public function subTaskPercentage()
    {
        $completedChecklist = $this->taskCompleteSubTaskCount();
        $allChecklist = max($this->taskTotalSubTaskCount(), 1);

        $percentageNumber = ceil(($completedChecklist / $allChecklist) * 100);
        $percentageNumber = $percentageNumber > 100 ? 100 : ($percentageNumber < 0 ? 0 : $percentageNumber);

        return (int) number_format($percentageNumber);
    }

    public static function getUsersData()
    {
        $zoommeetings = \DB::table('automate_tasks')->get();

        $employeeIds = [];
        foreach ($zoommeetings as $item) {
            $employees = explode(',', $item->assign_to);
            foreach ($employees as $employee) {
                $employeeIds[] = $employee;
            }
        }
        $data = [];
        $users =  User::whereIn('id', array_unique($employeeIds))->get();
        foreach($users as $user)
        {

            $data[$user->id]['name']        = $user->name;
            $data[$user->id]['avatar']      = $user->avatar;
        }
        return $data;

    }
}
