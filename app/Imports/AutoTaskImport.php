<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Carbon\Carbon;
use Workdo\Taskly\Entities\AutomateTask;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AutoTaskImport implements ToModel, WithHeadingRow
{
   
    /**
     * @param array $row
     *
     * @return User|null
     */
   
    public function model(array $row)
    {

        if(!empty($row['task']) && !empty($row['assignor']) && !empty($row['assignee']))
        {
            $days = NULL;
            if($row['type']=='weekly' || $row['type']=='monthly' )
            {
                if($row['day'])
                {
                    $days = $row['day'];
                }                
            }

           $autoMatetaskArr=[
            'title'=>$row['task'],
            'group'=>$row['group'],
            'assign_to'=>$row['assignee'],
            'eta_time'=>$row['eta_time'] ?? 0,
            'description'=>$row['description'],
            'assignor'=>$row['assignor'],
            'link1'=>$row['link1'],
            'link2'=>$row['link2'],
            'link3'=>$row['tl'],
            'link4'=>$row['vl'],
            'link5'=>$row['fl'],
            'link7'=>$row['fr'],
            'link6'=>$row['cl'],
            'eta_time'=>$row['eta_min'],
               'schedule_type'=>$row['type'],
             'schedule_days'=>$days,
            'schedule_time'=>'12:00:00',
            'workspace'=>getActiveWorkSpace(),
            'status'=>$row['status'],
           ];

          return  AutomateTask::create($autoMatetaskArr // Data to update or insert
            );

          
        }else
        {
            return collect([]);
        }

    }
    // }
}