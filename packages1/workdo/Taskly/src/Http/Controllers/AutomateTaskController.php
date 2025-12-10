<?php
namespace Workdo\Taskly\Http\Controllers;


use App\Models\EmailTemplate;
use App\Models\Invoice;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Workdo\Taskly\Entities\ActivityLog;
use Workdo\Taskly\Entities\BugComment;
use Workdo\Taskly\Entities\BugFile;
use Workdo\Taskly\Entities\BugReport;
use Workdo\Taskly\Entities\BugStage;
use Workdo\Taskly\Entities\ClientProject;
use Workdo\Taskly\Entities\Comment;
use Workdo\Taskly\Entities\Milestone;
use Workdo\Taskly\Entities\Project;
use Workdo\Taskly\Entities\ProjectFile;
use Workdo\Taskly\Entities\Stage;
use Workdo\Taskly\Entities\SubTask;
use Workdo\Taskly\Entities\AutomateTask;
use Workdo\Taskly\Entities\TaskFile;
use Workdo\Taskly\Entities\UserProject;
use App\Traits\LogsTaskActivity;
use Workdo\Taskly\Entities\VenderProject;
use Workdo\TimeTracker\Entities\TimeTracker;
use Workdo\Taskly\Events\CreateBug;
use Workdo\Taskly\Events\CreateMilestone;
use Workdo\Taskly\Events\CreateProject;
use Workdo\Taskly\Events\CreateTask;
use Workdo\Taskly\Events\CreateTaskComment;
use Workdo\Taskly\Events\DestroyBug;
use Workdo\Taskly\Events\DestroyMilestone;
use Workdo\Taskly\Events\DestroyProject;
use Workdo\Taskly\Events\DestroyTask;
use Workdo\Taskly\Events\DestroyTaskComment;
use Workdo\Taskly\Events\UpdateBug;
use Workdo\Taskly\Events\UpdateMilestone;
use Workdo\Taskly\Events\UpdateProject;
use Workdo\Taskly\Events\UpdateTask;
use Workdo\Taskly\Events\UpdateTaskStage;
use Workdo\Taskly\Events\ProjectInviteUser;
use Workdo\Taskly\Events\ProjectShareToClient;
use Workdo\Taskly\Events\ProjectUploadFiles;
use Workdo\Taskly\Events\UpdateBugStage;
use Workdo\Account\Entities\Bill;
use Workdo\Documents\Entities\Document;
use Workdo\Retainer\Entities\Retainer;
use App\Models\Proposal;
use App\Models\Purchase;
use App\Models\SchedulerLog;
use App\Models\SchedulerErrorReport;
use Workdo\Taskly\DataTables\ProjectBugDatatable;
use Workdo\Taskly\DataTables\ProjectDatatable;
use Workdo\Taskly\DataTables\ProjectAutomateTaskDatatable;
use App\Imports\AutoTaskImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use Workdo\Taskly\Traits\TaskTraits;


class AutomateTaskController extends Controller
{
    //
    
    use LogsTaskActivity;
    use TaskTraits;

    public function TaskList(ProjectAutomateTaskDatatable $dataTable)
    {
        
        if (\Auth::user()->isAbleTo('automate-task manage')) {
            $currentWorkspace = getActiveWorkSpace();
            $objUser = Auth::user();
            $users = User::select('users.*')->get();
            $stages = Stage::where('workspace_id', '=', $currentWorkspace)->orderBy('order')->get();

            if($objUser->hasRole('company') || $objUser->hasRole('Manager All Access') || $objUser->hasRole('hr'))
            {
                    $totalTask = AutomateTask::count();
                    $totalETAmin = collect(AutomateTask::where('eta_time',">",0)->pluck('eta_time')->toArray())->sum();
            }else
            {
                $email = $objUser->email;
                    $totalTask = AutomateTask::where(function ($query) use ($email) {
                                $query->where('assignor', 'like', "%$email%")
                                    ->orWhere('assign_to', 'like', "%$email%");
                            })->count();
               $totalETAmin = collect(AutomateTask::where('status','done')->where(function ($query) use ($email) {
                                    $query->where('assignor', 'like', "%$email%")
                                        ->orWhere('assign_to', 'like', "%$email%");
                                })->where('eta_time',">",0)->pluck('eta_time')->toArray())->sum();
            }
            $totalEtaHours = number_format($totalETAmin/60);
            $totalMonthlyHours = round(collect(AutomateTask::where('schedule_type','monthly')->pluck('eta_time')->toArray())->sum());
            $totalWeeklyHours = round(collect(AutomateTask::where('schedule_type','weekly')->pluck('eta_time')->toArray())->sum() / (60 * 6));
            $totalDailyHours = round(collect(AutomateTask::where('schedule_type','daily')->pluck('eta_time')->toArray())->sum() / (60 * 25));

            return $dataTable->render('taskly::projects.automate-task.tasklist',compact(
                'currentWorkspace',
                'stages',
                'users',
                'totalTask',
                'totalETAmin',
                'totalEtaHours',
                'totalMonthlyHours',
                'totalWeeklyHours',
                'totalDailyHours'
            ));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }


    
    public function taskCreate(Request $request)
    {
        $objUser          = Auth::user();
        $currentWorkspace = getActiveWorkSpace();
        if (module_is_active('CustomField')) {
            $customFields =  \Workdo\CustomField\Entities\CustomField::where('workspace_id', getActiveWorkSpace())->where('module', '=', 'taskly')->where('sub_module', 'tasks')->get();
        } else {
            $customFields = null;
        }
       

        $users = User::select('users.*')->get();
        $stages = Stage::where('workspace_id', '=', $currentWorkspace)->orderBy('order')->get();

        return view('taskly::projects.automate-task.taskCreate', compact('currentWorkspace', 'customFields',  'users','stages'));
    }

    public function taskStore(Request $request)
    {
        // return "sdasd";
        $request->validate(
            [
                'title' => 'required',
                'assign_to' => 'required',
                'assignor' => 'required',
                'priority' => 'required',
                'description' => 'nullable',
            ]
        );
        $objUser          = Auth::user();
        $currentWorkspace = getActiveWorkSpace();

        // if ($objUser->hasRole('client')) {
        //     $project = Project::where('projects.workspace', '=', $currentWorkspace)->first();
        // } else {
        //     $project = Project::select('projects.*')->join('user_projects', 'user_projects.project_id', '=', 'projects.id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $request->project_id)->first();
        // }
        // if ($project) {
            $post  = $request->all();
            // return "dsdsds";
            $stage = Stage::where('workspace_id', '=', $currentWorkspace)->orderBy('order')->first();
            if ($stage) {
                $post['status']    = $stage->name;
                $post['assign_to'] = implode(",", $request->assign_to);
                $post['assignor'] = $request->assignor;
                $post['link1'] = $request->link1;
                $post['group'] = $request->group;
                $post['schedule_time'] = $request->schedule_time;
                $post['schedule_type'] = $request->schedule_type;
                if ($request->schedule_type == 'monthly') {
                    $post['schedule_days'] = is_array($request->schedule_days) 
                        ? $request->schedule_days[0] 
                        : $request->schedule_days;
                } elseif ($request->schedule_type == 'weekly') {
                    $post['schedule_days'] = is_array($request->schedule_days) 
                        ? $request->schedule_days[0] 
                        : $request->schedule_days;
                } else {
                    $post['schedule_days'] = 0;
                }
                $post['link2'] = $request->link2;
                $post['link3'] = $request->link3;
                $post['link4'] = $request->link4;
                $post['link5'] = $request->link5;
                
                $post['workspace'] = getActiveWorkSpace();
                $task              = AutomateTask::create($post);
                ActivityLog::create(
                    [
                        'user_id' => Auth::user()->id,
                        'user_type' => get_class(Auth::user()),
                        'log_type' => 'Create Automate Task',
                        'remark' => json_encode(['title' => $task->title]),
                    ]
                );

                // event(new CreateTask($request, $task));
                $returnUrl =route('projecttask.automate.list',['is_add_enable'=>'true']);
                return redirect($returnUrl)->with('success', __('The task has been created successfully.'));
                return redirect()->back()->with('success', __('The task has been created successfully.'));
            } else {
                return redirect()->back()->with('error', __('Please add stages first.'));
            }
        // } else {
        //     return redirect()->back()->with('error', __("You can't Add Task!"));
        // }
    }
    public function taskEdit($taskId)
    {
        $objUser          = Auth::user();
        $currentWorkspace = getActiveWorkSpace();

        $users           = User::select('users.*')->get();
        $task            = AutomateTask::find($taskId);

        $stages = Stage::where('workspace_id', '=', getActiveWorkSpace())->where('created_by',creatorId())->get();
    
            $customFields = null;
        $task->assign_to = explode(",", $task->assign_to);

        return view('taskly::projects.automate-task.taskEdit', compact('currentWorkspace', 'users', 'task', 'customFields','stages'));
    }
    public function autoMateTaskImport(Request $request)
    {
        $objUser          = Auth::user();
        $currentWorkspace = getActiveWorkSpace();
        if ($request->isMethod('post')) {
            $request->validate([
                'file' => 'required',
            ]);
            Excel::import(new AutoTaskImport(), request()->file('file'));
            $redirectUrl = route('projecttask.list');
            $message = "Automate Task Imported successfully";
            return response()->json([
                'status'        =>  true,
                'response_code' =>  200,
                'message'       =>  $message,
                'data'          =>  ['redirect_url'=>$redirectUrl]
            ], 200);
        }

        return view('taskly::projects.automate-task.import', compact('currentWorkspace', 'objUser'));
    }
    public function taskUpdate(Request $request, $taskID)
    {
        $request->validate(
                [
                    'title' => 'required',
                    'assign_to' => 'required',
                    'assignor' => 'required',
                    'priority' => 'required',
                    'description' => 'nullable',
                ]
            );
            $currentWorkspace = getActiveWorkSpace();
            if(!empty($request->stage_id))
            {
             $stage = Stage::where('workspace_id', '=', $currentWorkspace)->where('name',$request->stage_id)->orderBy('order')->first();
            }else
            {
             $stage = Stage::where('workspace_id', '=', $currentWorkspace)->orderBy('order')->first();
            }
            $objUser          = Auth::user();
            $currentWorkspace = getActiveWorkSpace();
            $post              = $request->all();
            $post['status']    = $stage->name;
            $post['assignor'] = $request->assignor;
            $post['link1'] = $request->link1;
            $post['group'] = $request->group;
            $post['link2'] = $request->link2;
            $post['link3'] = $request->link3;
            $post['link4'] = $request->link4;
            $post['link5'] = $request->link5;
            $post['schedule_time'] = $request->schedule_time;
            $post['schedule_type'] = $request->schedule_type;
            $post['schedule_type'] = $request->schedule_type;
                if ($request->schedule_type == 'monthly') {
                    $post['schedule_days'] = is_array($request->schedule_days) 
                        ? $request->schedule_days[0] 
                        : $request->schedule_days;
                } elseif ($request->schedule_type == 'weekly') {
                    $post['schedule_days'] = is_array($request->schedule_days) 
                        ? $request->schedule_days[0] 
                        : $request->schedule_days;
                } else {
                    $post['schedule_days'] = 0;
                }
            $post['assign_to'] = implode(",", $request->assign_to);
            $task              = AutomateTask::find($taskID);
            $task->update($post);
            
            $task = AutomateTask::with(['stage'])->find($taskID);
            $message = "The task details are updated successfully.";
            $returnUrl =route('projecttask.list');
            return response()->json([
                'status'        =>  true,
                'response_code' =>  200,
                'message'       =>  $message,
                'data'          =>  ['task'=>$task,'redirect_url'=>$returnUrl]
            ], 200);
            // event(new UpdateTask($request, $task));

            return redirect()->back()->with('success', __('The task details are updated successfully.'));
       
    }
    public function taskDestroy($taskID)
    {
        // event(new DestroyTask($taskID));

        AutomateTask::where('id', $taskID)->delete();
        $message ="__('The task has been deleted.')";
        return response()->json([
            'status'        =>  true,
            'response_code' =>  200,
            'message'       =>  $message,
             'data'          =>  ['delete_id'=>$taskID]
        ], 200);
    }
    public function bulkAction(Request $request)
    {
        $selectedIds = $request->selected_ids;
        $actionType = $request->action_type;
        if($actionType == 'delete')
        {
            AutomateTask::whereIn('id',$selectedIds)->delete();
        }
        return response()->json(
            [
                'is_success' => true,
                'message' =>"Successfully deleted",
            ],
            200
        );
    }
     public function taskPauseResume(Request $request)
    {
        $id = $request->tid;
        $value = $request->value;
        $task =  AutomateTask::where('id',$id)->first();
        $task->is_pause = !$task->is_pause;
        $task->save();
        if($task->is_pause==1)
        {
            $message ="Resumed Successfully";
        }else
        {
            $message ="Paused Successfully";
        }
        return response()->json(
            [
                'is_success' => true,
                'message' =>$message,
            ],
            200
        );
    }


    public function calculateEtaSummary()
{
    $tasks = Task::all();

    $totalMinutes = 0;
    $details = [];

    foreach ($tasks as $task) {
        $eta = (int) $task->eta_time;
        $occurrences = 0;

        switch (strtolower($task->schedule_type)) {
            case 'daily':
                $occurrences = 25;
                break;

            case 'weekly':
                $occurrences = 4;
                break;

            case 'monthly':
                $occurrences = 1;
                break;

            default:
                $occurrences = 0;
        }

        $taskMinutes = $eta * $occurrences;
        $totalMinutes += $taskMinutes;

        $details[] = [
            'id' => $task->id,
            'title' => $task->title,
            'schedule_type' => $task->schedule_type,
            'eta_time' => $eta,
            'occurrences' => $occurrences,
            'total_task_minutes' => $taskMinutes,
        ];
    }

    $totalHours = round($totalMinutes / 60, 2);

    return response()->json([
        'total_minutes' => $totalMinutes,
        'total_hours' => $totalHours,
        'tasks_summary' => $details,
    ]);
}


     public function taskCountData(Request $request)
    {
            $objUser = Auth::user();
        
            $assignee_name = $request->assignee_name;
            $assignor_name = $request->assignor_name;
            $status_name = $request->status_name;
            $group_name = $request->group_name;
            $task_name = $request->task_name;
            $searchValue ="";
            if (request()->has('search_value') && !empty(request()->input('search_value'))) {
                $searchValue = request()->input('search_value');
            }
            $workspaceId = getActiveWorkSpace();
            if($objUser->hasRole('company') || $objUser->hasRole('Manager All Access') || $objUser->hasRole('hr') )
            {
        
                    $taskBaseQuery = AutomateTask::join('stages', 'stages.name', '=', 'automate_tasks.status')
                        ->leftJoin('users as assignor_users', 'assignor_users.email', '=', 'automate_tasks.assignor')
                        ->where('automate_tasks.workspace', $workspaceId)
                        ->select('automate_tasks.*', 'stages.name as stage_name', 'assignor_users.name as assigner_name','eta_time')
                        ->distinct('automate_tasks.id');
                        if (!empty($searchValue)) {
                            $taskBaseQuery->where(function ($query) use ($searchValue) {
                                // Search by assignor name
                                $query->where('assignor_users.name', 'like', "%$searchValue%")
                                    // Search by users in assign_to field (using FIND_IN_SET)
                                    ->orWhereRaw("
                                        EXISTS (
                                            SELECT 1 
                                            FROM users 
                                            WHERE FIND_IN_SET(users.email, automate_tasks.assign_to) 
                                            AND users.name LIKE ?
                                        )", ["%$searchValue%"])
                                    // Search by task title
                                    ->orWhere('automate_tasks.title', 'like', "%$searchValue%")
                                    // Search by group name
                                    ->orWhere('automate_tasks.group', 'like', "%$searchValue%");
                            });
                        }
                        

                    $completedTask = (clone $taskBaseQuery)
                        ->where('automate_tasks.status', 'Done');
                         $pendingTask = (clone $taskBaseQuery)
                        ->where('automate_tasks.status', '!=', 'Done')
                        ->where('automate_tasks.status', '!=', '');
                 
                    $overdueTask = (clone $taskBaseQuery)
                        ->where('automate_tasks.status', '!=', 'Done')
                        ->where('automate_tasks.status', '!=', '')
                        ->where('automate_tasks.due_date', '<', now());
                    $totalTask = (clone $taskBaseQuery);
                    
                    $weeklyTask = (clone $taskBaseQuery);
                     $dailyTask = (clone $taskBaseQuery);
                      $monthyTask = (clone $taskBaseQuery);
                    if($assignor_name && count($assignor_name) ){
                        $completedTask->where('assignor', 'like', "%$assignor_name[0]%");
                        $pendingTask->where('assignor', 'like', "%$assignor_name[0]%");
                         $overdueTask->where('assignor', 'like', "%$assignor_name[0]%");
                        $totalTask->where('assignor', 'like', "%$assignor_name[0]%");
                          $monthyTask->where('assignor', 'like', "%$assignor_name[0]%");
                            $dailyTask->where('assignor', 'like', "%$assignor_name[0]%");
                              $weeklyTask->where('assignor', 'like', "%$assignor_name[0]%");
                    }
                   if($group_name && !empty($group_name) ){
                        $completedTask->where('group', 'like', "%$group_name%");
                        $pendingTask->where('group', 'like', "%$group_name%");
                        $overdueTask->where('group', 'like', "%$group_name%");
                        $totalTask->where('group', 'like', "%$group_name%");
                        $monthyTask->where('group', 'like', "%$group_name%");
                        $dailyTask->where('group', 'like', "%$group_name%");
                        $weeklyTask->where('group', 'like', "%$group_name%");
                    }
                    if($task_name && !empty($task_name) ){
                        $completedTask->where('group', 'like', "%$task_name%");
                        $pendingTask->where('group', 'like', "%$task_name%");
                        $overdueTask->where('group', 'like', "%$task_name%");
                        $totalTask->where('group', 'like', "%$task_name%");
                        $monthyTask->where('group', 'like', "%$task_name%");
                        $dailyTask->where('group', 'like', "%$task_name%");
                        $weeklyTask->where('group', 'like', "%$task_name%");
                    }
                    if($assignee_name && count($assignee_name) ){
                        $completedTask->where('assign_to', 'like', "%$assignee_name[0]%");
                        $pendingTask->where('assign_to', 'like', "%$assignee_name[0]%");
                        $overdueTask->where('assign_to', 'like', "%$assignee_name[0]%");
                        $totalTask->where('assign_to', 'like', "%$assignee_name[0]%");
                        $monthyTask->where('assign_to', 'like', "%$assignee_name[0]%");
                        $dailyTask->where('assign_to', 'like', "%$assignee_name[0]%");
                        $weeklyTask->where('assign_to', 'like', "%$assignee_name[0]%");
                    }
                   
                    $completedTask = $completedTask->count();
                    $pendingTask =$pendingTask->count();
                    
                    $totalETAmin = collect($totalTask->where('eta_time',">",0)->pluck('eta_time')->toArray())->sum();
                    $totalWeeklyETAmin =collect($weeklyTask->where('eta_time',">",0)->where('schedule_type','weekly')->pluck('eta_time')->toArray())->sum();
                    $totalMonthlyETAmin =collect( $monthyTask->where('eta_time',">",0)->where('schedule_type','monthly')->pluck('eta_time')->toArray())->sum();
                    $totalDailyETAmin =collect( $dailyTask->where('eta_time',">",0)->where('schedule_type','daily')->pluck('eta_time')->toArray())->sum();

                    $overdueTask = $overdueTask->count();

                    $totalTask = $totalTask->count();
            }else
            {
                      $email = $objUser->email;
                    $workspaceId = getActiveWorkSpace();
                    
                    // Base query with all common filters
                   
                    $taskBaseQuery = AutomateTask::join('stages', 'stages.name', '=', 'automate_tasks.status')
                        ->leftJoin('users as assignor_users', 'assignor_users.email', '=', 'automate_tasks.assignor')
                        ->where('automate_tasks.workspace', $workspaceId)
                        ->where(function ($query) use ($email) {
                            $query->whereRaw("FIND_IN_SET(?, automate_tasks.assign_to)", [$email])
                                  ->orWhere('automate_tasks.assignor', $email);
                        })
                        ->select('automate_tasks.*', 'stages.name as stage_name', 'assignor_users.name as assigner_name','eta_time')
                        ->distinct('automate_tasks.id');
                    
                    // Total Tasks
                    $totalTask = (clone $taskBaseQuery)->count('automate_tasks.id');
                    
                    // Completed Tasks
                    $completedTask = (clone $taskBaseQuery)
                        ->where('automate_tasks.status', 'Done')
                        ->count('automate_tasks.id');
                    
                    // Pending Tasks
                    $pendingTask = (clone $taskBaseQuery)
                        ->where('automate_tasks.status', '!=', 'Done')
                        ->where('automate_tasks.status', '!=', '')
                        ->count('automate_tasks.id');
                    
                    // Overdue Tasks
                    $overdueTask = (clone $taskBaseQuery)
                        ->where('automate_tasks.status', '!=', 'Done')
                        ->where('automate_tasks.status', '!=', '')
                        ->where('automate_tasks.due_date', '<', now())
                        ->count('automate_tasks.id');
                    $totalWeeklyETAmin = collect((clone $taskBaseQuery)->where('schedule_type','weekly')->where('eta_time',">",0)->pluck('eta_time')->toArray())->sum();
                    $totalMonthlyETAmin = collect((clone $taskBaseQuery)->where('schedule_type','monthly')->where('eta_time',">",0)->pluck('eta_time')->toArray())->sum();
                    $totalMonthlyETAmins = collect((clone $taskBaseQuery)->where('schedule_type','monthly')->where('eta_time',">",0)->pluck('eta_time')->toArray())->sum();

                    $totalDailyETAmin = collect((clone $taskBaseQuery)->where('schedule_type','daily')->where('eta_time',">",0)->pluck('eta_time')->toArray())->sum();

                     $totalETAmin = collect((clone $taskBaseQuery)->where('eta_time',">",0)->pluck('eta_time')->toArray())->sum();
            }
            
        // Calculate total minutes for each schedule type
        $dailyTotalMinutes = $totalDailyETAmin;  // Total minutes for daily tasks
        $weeklyTotalMinutes = $totalWeeklyETAmin; // Total minutes for weekly tasks  
        $monthlyTotalMinutes = $totalMonthlyETAmin; // Total minutes for monthly tasks
        
        // Calculate the multiplied values based on your formula
        $monthlyCalculated = $monthlyTotalMinutes * 1;  // monthly tasks * 1
        $weeklyCalculated = $weeklyTotalMinutes * 4;    // weekly tasks * 4
        $dailyCalculated = $dailyTotalMinutes * 25;     // daily tasks * 25
        
        // Calculate total minutes: month + week + day
        $totalAllMinutes = $monthlyCalculated + $weeklyCalculated + $dailyCalculated;
        
        // Final results based on your formula
        $finalMonthlyHours = $totalAllMinutes > 0 ? round($totalAllMinutes / 60) : 0;  // Total minutes ÷ 60
        $finalWeeklyHours = $finalMonthlyHours > 0 ? round(($finalMonthlyHours / 25) * 6) : 0;  // (Monthly ÷ 25) × 6
        $finalDailyHours = $finalWeeklyHours > 0 ? round($finalWeeklyHours / 6) : 0;  // Weekly ÷ 6
        
       // Debug information (you can remove this later)
        \Log::info('Task Count Debug:', [
            'daily_minutes' => $dailyTotalMinutes,
            'weekly_minutes' => $weeklyTotalMinutes,
            'monthly_minutes' => $monthlyTotalMinutes,
            'monthly_calc' => $monthlyCalculated,
            'weekly_calc' => $weeklyCalculated,
            'daily_calc' => $dailyCalculated,
            'total_all_minutes' => $totalAllMinutes,
            'final_monthly' => $finalMonthlyHours,
            'final_weekly' => $finalWeeklyHours,
            'final_daily' => $finalDailyHours
        ]);

        return response()->json(
            [
                'is_success' => true,
                'data' =>['complete_count'=>$completedTask,
                'pending_count'=>$pendingTask,'overdue_count'=>$overdueTask,
                'total_count'=>$totalTask,'total_eta'=>round($totalETAmin/60),
                'total_weekly_eta'=>$finalWeeklyHours,
                'total_monthly_eta'=>$finalMonthlyHours,
                'total_daily_eta'=>$finalDailyHours]
            ],
            200
        );

    }

public function taskReport(Request $request)
{ 
    if (!\Auth::user()->isAbleTo('automate-task manage')) {
        return redirect()->back()->with('error', 'Permission Denied');
    }

    $currentWorkspace = getActiveWorkSpace();
    $objUser = Auth::user(); 

    // Build base query
    $tasksQuery = AutomateTask::where('workspace', $currentWorkspace)
                              ->where('is_pause', 0);

    // Role-based visibility
    if (! $objUser->hasRole('client') && ! $objUser->hasRole('company') &&
        ! $objUser->hasRole('Manager All Access') && ! $objUser->hasRole('hr')) {
        $tasksQuery->where(function ($q) use ($objUser) {
            $q->whereRaw("FIND_IN_SET(?, assign_to)", [$objUser->email])
              ->orWhere('assignor', $objUser->email);
        });
    }

    // Filters
    if ($request->filled('report_type') && in_array($request->report_type, ['daily','weekly','monthly'])) {
        $tasksQuery->where('schedule_type', $request->report_type);
    }
    if ($request->filled('assignee')) {
        $tasksQuery->where('assign_to', 'like', "%{$request->assignee}%");
    }
    if ($request->filled('assignor')) {
        $tasksQuery->where('assignor', $request->assignor);
    }
    $dateColumn = 'created_at';
    if ($request->filled('from_date')) {
        $tasksQuery->whereDate($dateColumn, '>=', $request->from_date);
    }
    if ($request->filled('to_date')) {
        $tasksQuery->whereDate($dateColumn, '<=', $request->to_date);
    }

    // Fetch automate tasks
    $autoTasks = $tasksQuery->orderBy('created_at', 'desc')->get();

    // If no automate tasks, return quickly
    if ($autoTasks->isEmpty()) {
        $users = User::select('users.*')->get();
        $stages = Stage::where('workspace_id', $currentWorkspace)->orderBy('order')->get();
        return view('taskly::projects.automate-task.report', compact(
            'users','stages','autoTasks'
        ));
    }

    // Collect automate_task IDs
    $autoIds = $autoTasks->pluck('id')->toArray();

    // Single query: get last created_at per automate_task_id from tasks table
   $lastRuns = DB::table('tasks')
    ->select(
        'automate_task_id',
        DB::raw('MAX(created_at) AS last_run'),
        DB::raw('MAX(completion_date) AS last_completion')
    )
    ->whereIn('automate_task_id', $autoIds)
    ->groupBy('automate_task_id')
    ->get()
    ->keyBy('automate_task_id'); // returns collection keyed by automate_task_id

    // Transform collection: compute status using last_run (if present)
   $autoTasks->transform(function ($auto) use ($lastRuns) {
    $row = $lastRuns->get($auto->id);
    $lastRun = $row && $row->last_run ? Carbon::parse($row->last_run) : null;
    $lastCompletion = $row && $row->last_completion ? Carbon::parse($row->last_completion) : null;

    $auto->status = $this->determineTaskStatus($auto, $lastRun, $lastCompletion);
    return $auto;
});
if ($request->filled('scheduled_type')) {
    $autoTasks = $autoTasks->where('status', $request->scheduled_type);
}
    // summary counts
//     $dailyCount   = $autoTasks->where('schedule_type', 'daily')->count();
//     // $dailyCount   = $autoTasks->where('schedule_type', 'like', '%daily%')->count();

// $weeklyCount  = $autoTasks->where('schedule_type', 'weekly')->count();
// $monthlyCount = $autoTasks->where('schedule_type', 'monthly')->count();

//fired all task at once
if($request->filled('fired_all') == 1)
{
    $total_not_fired_task_list = $autoTasks->where('status', 'not_fired')->pluck('id');
   
    foreach ($total_not_fired_task_list as $taskId) {
        // dd($taskId);
        $this->saveTask($taskId);
    }
}
// Status counts
    $scheduledTasks = $autoTasks->where('status', 'scheduled')->count();
    $firedTasks     = $autoTasks->where('status', 'fired')->count();
    $notFiredTasks  = $autoTasks->where('status', 'not_fired')->count();
    if($request->report_type == 'daily')
    {
        $dailyCount = $scheduledTasks+$firedTasks+$notFiredTasks;
        $weeklyCount = 0;
        $monthlyCount = 0;
    }
    elseif($request->report_type == 'weekly')
    {
         $dailyCount = 0;
        $weeklyCount = $scheduledTasks+$firedTasks+$notFiredTasks;
        $monthlyCount = 0;
    }
    elseif($request->report_type == 'monthly')
    {
      $dailyCount = 0;
        $weeklyCount = 0;
        $monthlyCount = $scheduledTasks+$firedTasks+$notFiredTasks;  
    }
    else{
      $dailyCount   = $autoTasks->where('schedule_type', 'daily')->count();
   $weeklyCount  = $autoTasks->where('schedule_type', 'weekly')->count();
$monthlyCount = $autoTasks->where('schedule_type', 'monthly')->count();
    }
    $users = User::select('users.*')->get();
    $stages = Stage::where('workspace_id', '=', $currentWorkspace)->orderBy('order')->get();
    return view('taskly::projects.automate-task.report', compact('dailyCount','weeklyCount','monthlyCount',
        'users','stages','autoTasks','scheduledTasks','firedTasks','notFiredTasks'
    ));
}

/**
 * Determine status by looking up real Task records created by scheduler.
 */
private function determineTaskStatus($autoTask, $lastExecuted = null,$lastCompletion = null)
{
     $now = Carbon::now();
    $type = strtolower($autoTask->schedule_type ?? '');
     $now = Carbon::now();
$todayDay = (int) $now->day;
$schedDay = (int) $autoTask->schedule_days;
           $created = Carbon::parse($autoTask->created_at);
$createdWithin30Days = $created->lt($now) && $created->gte($now->copy()->subDays(30));

if ($type === 'monthly' && $schedDay > $todayDay && $createdWithin30Days) {
    return 'scheduled';
}
   

    // normalize schedule_time - may be time-only or full datetime
    $scheduleTimeRaw = $autoTask->schedule_time ?? null;
    $scheduleTime = null;
    if ($scheduleTimeRaw) {
        try {
            // If time-only like "09:30" => combine with today
            if (preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $scheduleTimeRaw)) {
                $parts = explode(':', $scheduleTimeRaw);
                $hour = (int)$parts[0];
                $min  = (int)$parts[1];
                $sec  = isset($parts[2]) ? (int)$parts[2] : 0;
                $scheduleTime = Carbon::create($now->year, $now->month, $now->day, $hour, $min, $sec);
            } else {
                $scheduleTime = Carbon::parse($scheduleTimeRaw);
            }
        } catch (\Exception $e) {
            $scheduleTime = null;
        }
    }
   

    // If lastExecuted exists, check whether it's in the same period
    if ($lastExecuted) {
        if ($type === 'daily' && $lastExecuted->isSameDay($now)) {
            return 'fired';
        }
        if ($type === 'weekly' && $lastExecuted->isSameWeek($now)) {
            return 'fired';
        }
        if ($type === 'monthly' && $lastExecuted->isSameMonth($now)) {
            return 'fired';
        }
        // dd($autoTask->id);      

    }

    // If completion_date exists, is in the past, and not older than 1 month -> scheduled
    if ($lastCompletion) {
       
        // ensure we compare dates as Carbon instances
        if ($lastCompletion->lt($now)) {
            // not older than 1 month (>= now - 1 month)
            $oneMonthAgo = $now->copy()->subMonth();
            if ($lastCompletion->gte($oneMonthAgo)) {
                return 'scheduled';
            }
            // else: completion_date is older than 1 month -> treat as not fired (missed)
        }
        // if completion_date is in future (unlikely) mark as scheduled
        if ($lastCompletion->gt($now)) {
            return 'scheduled';
        }
    }

    // 3) If schedule_time exists and is in the future => scheduled (upcoming)
    if ($scheduleTime && $now->lt($scheduleTime)) {
        return 'scheduled';
    }

    // No evidence of firing in this period and not scheduled for future -> not fired
    return 'not_fired';
}
// scheduler list
public function allSchedulerList()
{
     $timeoutHours = 10;
    $timeoutThreshold = Carbon::now()->subHours($timeoutHours);

    // fetch raw rows (all columns you need)
    // $rows = DB::table('scheduler_logs')
    //     ->select('id','command','status','started_at','finished_at','created_at','error')
    //     ->orderBy('created_at')
    //     ->get();
    
    $rows = DB::table('scheduler_logs')
    ->select('id', 'command', 'status', 'started_at', 'finished_at', 'created_at', 'error')
    ->where('created_at', '>=', Carbon::now()->subDays(3)) // last 3 days
    // ->orderBy('created_at', 'desc') // test first
    ->get();

    // attach canon_command in PHP and group
    $rows = $rows->map(function($r) {
        $r->canon_command = $this->normalizeCommand($r->command);
        // ensure started_at fallback to created_at for calculation
        $r->started_at = $r->started_at ?: $r->created_at;
        return $r;
    });

    $grouped = $rows->groupBy('canon_command');

    $reportRows = collect();

    foreach ($grouped as $canon => $items) {
        // same sequential-processing logic you already used (copy/paste your previous foreach logic)
        // I'll provide a compact version that implements your rules:
        $runs = [];
        $active = null;

        foreach ($items as $r) {
            $rowCreated = Carbon::parse($r->created_at);
            $rowStarted = Carbon::parse($r->started_at);
            $rowFinished = $r->finished_at ? Carbon::parse($r->finished_at) : null;

            if ($r->status === 'running') {
                // if ($active !== null) {
                //     // cancel previous active
                //     $active['status'] = 'cancelled';
                //     $active['finished_at'] = $rowCreated->toDateTimeString();
                //     $active['runtime_seconds'] = Carbon::parse($active['finished_at'])->diffInSeconds(Carbon::parse($active['started_at']));
                //     $runs[] = $active;
                // }
                $active = [
                    'id' => $r->id,
                    'command' => $canon,
                    'raw_command' => $r->command,
                    'status' => 'running',
                    'started_at' => $rowStarted->toDateTimeString(),
                    'finished_at' => null,
                    'created_at' => $rowCreated->toDateTimeString(),
                    'error' => $r->error,
                ];
            } elseif ($r->status === 'success') {
                if ($active !== null) {
                    $active['status'] = 'success';
                    $active['finished_at'] = $rowFinished ? $rowFinished->toDateTimeString() : $rowCreated->toDateTimeString();
                    $active['runtime_seconds'] = Carbon::parse($active['finished_at'])->diffInSeconds(Carbon::parse($active['started_at']));
                    $runs[] = $active;
                    $active = null;
                } else {
                    // a success with no active run
                    $runs[] = [
                        'id' => $r->id,
                        'command' => $canon,
                        'raw_command' => $r->command,
                        'status' => 'success',
                        'started_at' => $rowStarted->toDateTimeString(),
                        'finished_at' => $rowFinished ? $rowFinished->toDateTimeString() : $rowCreated->toDateTimeString(),
                        'created_at' => $rowCreated->toDateTimeString(),
                        'runtime_seconds' => ($rowFinished ?: $rowCreated)->diffInSeconds($rowStarted),
                        'error' => $r->error,
                    ];
                }
            } else {
                // failed or other status
                if ($r->status === 'failed' && $active !== null) {
                    $active['status'] = 'failed';
                    $active['finished_at'] = $rowFinished ? $rowFinished->toDateTimeString() : $rowCreated->toDateTimeString();
                    $active['runtime_seconds'] = Carbon::parse($active['finished_at'])->diffInSeconds(Carbon::parse($active['started_at']));
                    $active['error'] = $r->error;
                    $runs[] = $active;
                    $active = null;
                } else {
                    $runs[] = [
                        'id' => $r->id,
                        'command' => $canon,
                        'raw_command' => $r->command,
                        'status' => $r->status,
                        'started_at' => $rowStarted->toDateTimeString(),
                        'finished_at' => $rowFinished ? $rowFinished->toDateTimeString() : $rowCreated->toDateTimeString(),
                        'created_at' => $rowCreated->toDateTimeString(),
                        'runtime_seconds' => ($rowFinished ?: $rowCreated)->diffInSeconds($rowStarted),
                        'error' => $r->error,
                    ];
                }
            }
        } // end per-item loop

        // finalize any active
        if ($active !== null) {
            $started = Carbon::parse($active['started_at']);
            // if ($started->lt($timeoutThreshold)) {
            //     $active['status'] = 'failed';
            //     $active['finished_at'] = Carbon::now()->toDateTimeString();
            //     $active['runtime_seconds'] = Carbon::parse($active['finished_at'])->diffInSeconds($started);
            // } else {
                $active['status'] = 'running';
                $active['finished_at'] = null;
                $active['runtime_seconds'] = Carbon::now()->diffInSeconds($started);
            // }
            $runs[] = $active;
            $active = null;
        }

        // pick most recent run by created_at
    // sort runs by created_at (FIFO)
usort($runs, function($a, $b){
    return strtotime($a['created_at']) <=> strtotime($b['created_at']);
});

// push all processed runs for this canonical command into reportRows
foreach ($runs as $run) {
    $reportRows->push((object)[
        'command' => $canon,        
        'raw_command' => $run['raw_command'] ?? null,
        'status' => $run['status'] ?? 'unknown',
        'started_at' => $run['started_at'] ?? null,
        'finished_at' => $run['finished_at'] ?? null,
        'runtime_seconds' => $run['runtime_seconds'] ?? null,
        'runtime' => isset($run['runtime_seconds']) ? gmdate('H:i:s', max(0,$run['runtime_seconds'])) : null,
        'error' => $run['error'] ?? null,
        'created_at' => $run['created_at'] ?? null,
        // optional: a unique run id to show in UI
        'run_id' => $run['id'] ?? null,
    ]);
}
    }
    // dd($reportRows);
  return view('taskly::projects.schedular_report.schedular_list',compact('reportRows'));
}

 protected function normalizeCommand(string $raw): string
    {
        $s = trim(preg_replace('/\s+/', ' ', $raw));
        // remove surrounding quotes for tokens
        $s = preg_replace("/(^'|'$|^\"|\"$)/", '', $s);

        if (preg_match('/\bartisan\b(.*)$/i', $s, $m)) {
            $after = trim($m[1]);
            $after = preg_replace("/^[\"']+|[\"']+$/", '', $after);
            // remove flags starting with --
            $after = preg_split('/\s+--/', $after)[0];
            // return the base job (first token)
            return explode(' ', $after, 2)[0] ?: $after;
        }

        // fallback: last token
        $parts = preg_split('/\s+/', $s);
        $last = end($parts) ?: $s;
        $last = preg_replace("/(^'|'$|^\"|\"$)/", '', $last);
        $last = preg_split('/\s+--/', $last)[0];

        return $last ?: $s;
    }

    // refire missed task
    public function reFire(Request $request)
    {
      $taskId = $request->id;
      $this->saveTask($taskId);
      return response()->json(['success' => true]);
    }
    public function create_scheduler_error_report(Request $request)
    {
        $validated = $request->validate([
            'no_issue_found' => 'nullable|boolean',
            'issue_found_and_fixed' => 'nullable|boolean',
            'corrective_action_applied' => 'nullable|boolean',
            'remarks' => 'nullable|string|max:1000',
        ]);

        SchedulerErrorReport::create([
            'no_issue_found' => $request->boolean('no_issue_found'),
            'issue_found_and_fixed' => $request->boolean('issue_found_and_fixed'),
            'corrective_action_applied' => $request->boolean('corrective_action_applied'),
            'remarks' => $request->remarks,
        ]);

        return redirect()->back()->with('success', 'Scheduler Error Report submitted successfully!');
    }
    public function show_scheduler_error_report()
    {
       $reports = SchedulerErrorReport::latest()->get(['id', 'no_issue_found', 'issue_found_and_fixed', 'corrective_action_applied', 'remarks', 'created_at']);

    $reports->transform(function ($item) {
        $item->formatted_date = $item->created_at->format('d M Y, h:i A');
        return $item;
    });

    return response()->json(['data' => $reports]);
    }
}
