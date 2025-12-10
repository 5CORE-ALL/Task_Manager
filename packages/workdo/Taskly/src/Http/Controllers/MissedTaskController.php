<?php

namespace Workdo\Taskly\Http\Controllers;

use App\Models\EmailTemplate;
use App\Models\Invoice;
use App\Models\User;
use Workdo\Hrm\Entities\Employee;
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
use App\Imports\TaskImport;
use App\Traits\SendSmsTraits;
use App\Traits\LogsTaskActivity;
use Maatwebsite\Excel\Facades\Excel;

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
use Workdo\Taskly\Entities\Task;
use Workdo\Taskly\Entities\TaskFile;
use Workdo\Taskly\Entities\UserProject;
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
use Workdo\Taskly\DataTables\ProjectBugDatatable;
use Workdo\Taskly\DataTables\ProjectDatatable;
use Workdo\Taskly\DataTables\ProjectMissedTaskDatatable;
use Workdo\Taskly\DataTables\ProjectDoneTaskDatatable;
use Workdo\Taskly\Traits\TaskTraits;


class MissedTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
     use TaskTraits;
     use SendSmsTraits;
     use LogsTaskActivity;
   






    public function missedTaskList(ProjectMissedTaskDatatable $dataTable)
    {
        if (\Auth::user()->isAbleTo('task manage')) {
            $currentWorkspace = getActiveWorkSpace();
            $objUser = Auth::user();
            $stages = Stage::where('workspace_id', '=', $currentWorkspace)->orderBy('order')->get();
            $users = User::select('users.*')->get();
           
            if($objUser->hasRole('company') || $objUser->hasRole('Manager All Access') || $objUser->hasRole('hr') )
            {
                    $competeTask = Task::where('status','Done')->where('status',"!=","")->where('workspace', getActiveWorkSpace())->where('deleted_at',NULL)->count();
                    $pendingTask = Task::whereNotIn('status',['Done'])->where('status',"!=","")->where('workspace', getActiveWorkSpace())->where('deleted_at',NULL)->count();
                    // FIXED: Overdue = past due date AND not done
                     $overdueTask = Task::where('status', '!=', 'Done')
                           ->where('status',"!=","")
                           ->where('workspace', getActiveWorkSpace())
                           ->where('due_date', '<', now())
                           ->where('deleted_at',NULL)
                           ->count();
            }else
            {
                $email = $objUser->email;
                    $competeTask = Task::where('status','Done')->where('status',"!=","")->where('workspace', getActiveWorkSpace())->where(function ($query) use ($email) {
                                $query->where('assignor', 'like', "%$email%")
                                    ->orWhere('assign_to', 'like', "%$email%");
                            })->where('deleted_at',NULL)->count();
                    $pendingTask = Task::whereNotIn('status',['Done'])->where('status',"!=","")->where('workspace', getActiveWorkSpace())->where(function ($query) use ($email) {
                                   $query->where('assignor', 'like', "%$email%")
                                        ->orWhere('assign_to', 'like', "%$email%");
                                    })->where('deleted_at',NULL)->count();
                    // FIXED: Overdue = past due date AND not done
                       $overdueTask = Task::where('status', '!=', 'Done')
                                    ->where('status',"!=","")
                                    ->where('workspace', getActiveWorkSpace())
                                    ->where('due_date', '<', now())
                                    ->where(function ($query) use ($email) {
                                        $query->where('assignor', 'like', "%$email%")
                                              ->orWhere('assign_to', 'like', "%$email%");
                                    })->where('deleted_at',NULL)->count();
            }
                // Calculate total tasks
            $totalTask = $competeTask + $pendingTask;
            $priority = collect([
                ['value'=>"urgent", 'color' => 'danger'],
                ['value'=>"Take your time", 'color' => 'warning'],
                ['value'=>"normal", 'color' => 'success']
            ]);
            $users = User::select('users.*')->get();
         
            return $dataTable->render('taskly::projects.missed-task.tasklist',compact('currentWorkspace','stages','users','competeTask','pendingTask','overdueTask','totalTask','priority'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function getTodayCompletedTasks(Request $request)
    {
        $currentWorkspace = getActiveWorkSpace();
        $objUser = Auth::user();
        $today = now()->format('Y-m-d');
        
        // Base query for today's completed tasks
        // If completion_date doesn't exist, we'll use updated_at as fallback
        $query = Task::where('status', 'Done')
            ->where('workspace', $currentWorkspace)
            ->where('deleted_at', NULL)
            ->where(function($q) use ($today) {
                $q->whereDate('completion_date', $today)
                  ->orWhere(function($subQ) use ($today) {
                      $subQ->whereNull('completion_date')
                           ->whereDate('updated_at', $today);
                  });
            });
            
        // Apply assignee filter if provided
        if ($request->has('assignee_name') && !empty($request->input('assignee_name'))) {
            $assigneeEmails = $request->input('assignee_name');
            $query->where(function ($q) use ($assigneeEmails) {
                foreach ($assigneeEmails as $email) {
                    $q->orWhere('assign_to', 'like', "%$email%");
                }
            });
        }
        
        // Apply assignor filter if provided
        if ($request->has('assignor_name') && !empty($request->input('assignor_name'))) {
            $assignorEmails = $request->input('assignor_name');
            $query->where(function ($q) use ($assignorEmails) {
                foreach ($assignorEmails as $email) {
                    $q->orWhere('assignor', 'like', "%$email%");
                }
            });
        }
        
        // Apply other filters if needed
        if ($request->has('status_name') && !empty($request->input('status_name'))) {
            $statusName = $request->input('status_name');
            $query->where('status', 'like', "%$statusName%");
        }
        
        if ($request->has('group_name') && !empty($request->input('group_name'))) {
            $groupName = $request->input('group_name');
            $query->where('group', 'like', "%$groupName%");
        }
        
        if ($request->has('task_name') && !empty($request->input('task_name'))) {
            $taskName = $request->input('task_name');
            $query->where('title', 'like', "%$taskName%");
        }
        
        // If user doesn't have admin roles, filter by their own tasks
        if (!$objUser->hasRole('company') && !$objUser->hasRole('Manager All Access') && !$objUser->hasRole('hr')) {
            $email = $objUser->email;
            $query->where(function ($q) use ($email) {
                $q->where('assignor', 'like', "%$email%")
                  ->orWhere('assign_to', 'like', "%$email%");
            });
        }
        
        $todayCompletedCount = $query->count();
        
        return response()->json([
            'today_completed_count' => $todayCompletedCount,
            'date' => $today
        ]);
    }

    public function getUrgentETCData(Request $request)
    {
        $currentWorkspace = getActiveWorkSpace();
        $objUser = Auth::user();
        
        // Base query for urgent status tasks
        $query = Task::where('status', 'Urgent')
            ->where('workspace', $currentWorkspace)
            ->where('deleted_at', NULL);
            
        // Apply assignee filter if provided
        if ($request->has('assignee_name') && !empty($request->input('assignee_name'))) {
            $assigneeEmails = $request->input('assignee_name');
            $query->where(function ($q) use ($assigneeEmails) {
                foreach ($assigneeEmails as $email) {
                    $q->orWhere('assign_to', 'like', "%$email%");
                }
            });
        }
        
        // Apply assignor filter if provided
        if ($request->has('assignor_name') && !empty($request->input('assignor_name'))) {
            $assignorEmails = $request->input('assignor_name');
            $query->where(function ($q) use ($assignorEmails) {
                foreach ($assignorEmails as $email) {
                    $q->orWhere('assignor', 'like', "%$email%");
                }
            });
        }
        
        // Apply status filter if provided
        if ($request->has('status_name') && !empty($request->input('status_name'))) {
            $statusName = $request->input('status_name');
            $query->where('status', 'like', "%$statusName%");
        }
        
        // Apply group filter if provided
        if ($request->has('group_name') && !empty($request->input('group_name'))) {
            $groupName = $request->input('group_name');
            $query->where('group', 'like', "%$groupName%");
        }
        
        // Apply task name filter if provided
        if ($request->has('task_name') && !empty($request->input('task_name'))) {
            $taskName = $request->input('task_name');
            $query->where('title', 'like', "%$taskName%");
        }
        
        // If user doesn't have admin roles, filter by their own tasks
        if (!$objUser->hasRole('company') && !$objUser->hasRole('Manager All Access') && !$objUser->hasRole('hr')) {
            $email = $objUser->email;
            $query->where(function ($q) use ($email) {
                $q->where('assignor', 'like', "%$email%")
                  ->orWhere('assign_to', 'like', "%$email%");
            });
        }
        
        // Calculate total ETC hours for urgent tasks
        $urgentTasks = $query->get();
        $totalETCMinutes = 0;
        
        foreach ($urgentTasks as $task) {
            if (!empty($task->eta_time)) {
                // Check if eta_time contains colon (hours:minutes format)
                if (strpos($task->eta_time, ':') !== false) {
                    // Convert eta_time from "hours:minutes" to minutes and add to total
                    $etcParts = explode(':', $task->eta_time);
                    if (count($etcParts) >= 2) {
                        $hours = (int)$etcParts[0];
                        $minutes = (int)$etcParts[1];
                        $totalETCMinutes += ($hours * 60) + $minutes;
                    }
                } else {
                    // eta_time is just minutes
                    $totalETCMinutes += (int)$task->eta_time;
                }
            }
        }
        
        // Convert minutes to hours (round up to show at least 1 hour if there are minutes)
        $totalETCHours = $totalETCMinutes > 0 ? max(1, round($totalETCMinutes / 60)) : 0;
        
        return response()->json([
            'urgent_etc_hours' => $totalETCHours,
            'urgent_task_count' => $urgentTasks->count()
        ]);
    }

    public function BugList(ProjectBugDatatable $dataTable,$project_id)
    {
        if (\Auth::user()->isAbleTo('bug manage'))
        {
            $currentWorkspace = getActiveWorkSpace();

            $objUser = Auth::user();
            if ($objUser->hasRole('client')) {
                $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $project_id)->first();
            } else {
                $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $project_id)->first();
            }

            return $dataTable->render('taskly::projects.bug_report_list',compact('currentWorkspace', 'project'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function copyproject($id)
    {
        if (Auth::user()->isAbleTo('project create')) {
            $project = Project::find($id);
            return view('taskly::projects.copy', compact('project'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function copyprojectstore(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('project create')) {
            $project                          = Project::find($id);

            $duplicate                          = new Project();
            $duplicate['name']                  = $project->name;
            $duplicate['status']                = $project->status;
            $duplicate['image']                 = $project->image;
            $duplicate['description']           = $project->description;
            $duplicate['start_date']            = $project->start_date;
            $duplicate['end_date']              = $project->end_date;
            $duplicate['is_active']             = $project->is_active;
            $duplicate['currency']              = $project->currency;
            $duplicate['project_progress']      = $project->project_progress;
            $duplicate['progress']              = $project->progress;
            $duplicate['task_progress']         = $project->task_progress;
            $duplicate['tags']                  = $project->tags;
            $duplicate['estimated_hrs']         = $project->estimated_hrs;
            $duplicate['workspace']             = getActiveWorkSpace();
            $duplicate['created_by']            = creatorId();
            $duplicate->save();



            if (isset($request->user) && in_array("user", $request->user)) {
                $users = UserProject::where('project_id', $project->id)->get();
                foreach ($users as $user) {
                    $users = new UserProject();
                    $users['user_id'] = $user->user_id;
                    $users['project_id'] = $duplicate->id;
                    $users->save();
                }
            } else {
                $objUser = Auth::user();
                $users              = new UserProject();
                $users['user_id']   = $objUser->id;
                $users['project_id'] = $duplicate->id;
                $users->save();
            }

            if (isset($request->client) && in_array("client", $request->client)) {
                $clients = ClientProject::where('project_id', $project->id)->get();
                foreach ($clients as $client) {
                    $clients = new ClientProject();
                    $clients['client_id'] = $client->client_id;
                    $clients['project_id'] = $duplicate->id;
                    $clients->save();
                }
            }

            if (isset($request->task) && in_array("task", $request->task)) {
                $tasks = Task::where('project_id', $project->id)->get();
                foreach ($tasks as $task) {
                    $project_task                   = new Task();
                    $project_task['title']          = $task->title;
                    $project_task['priority']       = $task->priority;
                    $project_task['project_id']     = $duplicate->id;
                    $project_task['description']    = $task->description;
                    $project_task['start_date']     = $task->start_date;
                    $project_task['due_date']       = $task->due_date;
                    $project_task['milestone_id']   = $task->milestone_id;
                    $project_task['status']         = $task->status;
                    $project_task['assign_to']      = $task->assign_to;
                    $project_task['workspace']      = getActiveWorkSpace();
                    $project_task->save();

                    if (in_array("sub_task", $request->task)) {
                        $sub_tasks = SubTask::where('task_id', $task->id)->get();
                        foreach ($sub_tasks as $sub_task) {
                            $subtask                = new SubTask();
                            $subtask['name']        = $sub_task->name;
                            $subtask['due_date']    = $sub_task->due_date;
                            $subtask['task_id']     = $project_task->id;
                            $subtask['user_type']   = $sub_task->user_type;
                            $subtask['created_by']  = $sub_task->created_by;
                            $subtask['status']      = $sub_task->status;
                            $subtask->save();
                        }
                    }
                    if (in_array("task_comment", $request->task)) {
                        $task_comments = Comment::where('task_id', $task->id)->get();
                        foreach ($task_comments as $task_comment) {
                            $comment                = new Comment();
                            $comment['comment']     = $task_comment->comment;
                            $comment['created_by']  = $task_comment->created_by;
                            $comment['task_id']     = $project_task->id;
                            $comment['user_type']   = $task_comment->user_type;
                            $comment->save();
                        }
                    }
                    if (in_array("task_files", $request->task)) {
                        $task_files = TaskFile::where('task_id', $task->id)->get();
                        foreach ($task_files as $task_file) {
                            $file               = new TaskFile();
                            $file['file']       = $task_file->file;
                            $file['name']       = $task_file->name;
                            $file['extension']  = $task_file->extension;
                            $file['file_size']  = $task_file->file_size;
                            $file['created_by'] = $task_file->created_by;
                            $file['task_id']    = $project_task->id;
                            $file['user_type']  = $task_file->user_type;
                            $file->save();
                        }
                    }
                }
            }

            if (isset($request->bug) && in_array("bug", $request->bug)) {
                $bugs = BugReport::where('project_id', $project->id)->get();
                foreach ($bugs as $bug) {
                    $project_bug                   = new BugReport();
                    $project_bug['title']          = $bug->title;
                    $project_bug['priority']       = $bug->priority;
                    $project_bug['description']    = $bug->description;
                    $project_bug['assign_to']      = $bug->assign_to;
                    $project_bug['project_id']     = $duplicate->id;
                    $project_bug['status']         = $bug->status;
                    $project_bug['order']          = $bug->order;
                    $project_bug->save();

                    if (in_array("bug_comment", $request->bug)) {
                        $bug_comments = BugComment::where('bug_id', $bug->id)->get();
                        foreach ($bug_comments as $bug_comment) {
                            $bugcomment                 = new BugComment();
                            $bugcomment['comment']      = $bug_comment->comment;
                            $bugcomment['created_by']   = $bug_comment->created_by;
                            $bugcomment['bug_id']       = $project_bug->id;
                            $bugcomment['user_type']    = $bug_comment->user_type;
                            $bugcomment->save();
                        }
                    }
                    if (in_array("bug_files", $request->bug)) {
                        $bug_files = BugFile::where('bug_id', $bug->id)->get();
                        foreach ($bug_files as $bug_file) {
                            $bugfile               = new BugFile();
                            $bugfile['file']       = $bug_file->file;
                            $bugfile['name']       = $bug_file->name;
                            $bugfile['extension']  = $bug_file->extension;
                            $bugfile['file_size']  = $bug_file->file_size;
                            $bugfile['created_by'] = $bug_file->created_by;
                            $bugfile['bug_id']     = $project_bug->id;
                            $bugfile['user_type']  = $bug_file->user_type;
                            $bugfile->save();
                        }
                    }
                }
            }
            if (isset($request->milestone) && in_array("milestone", $request->milestone)) {
                $milestones = Milestone::where('project_id', $project->id)->get();
                foreach ($milestones as $milestone) {
                    $post                   = new Milestone();
                    $post['project_id']     = $duplicate->id;
                    $post['title']          = $milestone->title;
                    $post['status']         = $milestone->status;
                    $post['cost']           = $milestone->cost;
                    $post['summary']        = $milestone->summary;
                    $post['progress']       = $milestone->progress;
                    $post['start_date']     = $milestone->start_date;
                    $post['end_date']       = $milestone->end_date;
                    $post->save();
                }
            }
            if (isset($request->project_file) && in_array("project_file", $request->project_file)) {
                $project_files = ProjectFile::where('project_id', $project->id)->get();
                foreach ($project_files as $project_file) {
                    $ProjectFile                = new ProjectFile();
                    $ProjectFile['project_id']  = $duplicate->id;
                    $ProjectFile['file_name']   = $project_file->file_name;
                    $ProjectFile['file_path']   = $project_file->file_path;
                    $ProjectFile->save();
                }
            }
            if (isset($request->activity) && in_array('activity', $request->activity)) {
                $where_in_array = [];
                if (isset($request->milestone) && in_array("milestone", $request->milestone)) {
                    array_push($where_in_array, "Create Milestone");
                }
                if (isset($request->task) && in_array("task", $request->task)) {
                    array_push($where_in_array, "Create Task", "Move");
                }
                if (isset($request->bug) && in_array("bug", $request->bug)) {
                    array_push($where_in_array, "Create Bug", "Move Bug");
                }
                if (isset($request->client) && in_array("client", $request->client)) {
                    array_push($where_in_array, "Share with Client");
                }
                if (isset($request->user) && in_array("user", $request->user)) {
                    array_push($where_in_array, "Invite User");
                }
                if (isset($request->project_file) && in_array("project_file", $request->project_file)) {
                    array_push($where_in_array, "Upload File");
                }
                if (count($where_in_array) > 0) {
                    $activities = ActivityLog::where('project_id', $project->id)->whereIn('log_type', $where_in_array)->get();
                    foreach ($activities as $activity) {
                        $activitylog                = new ActivityLog();
                        $activitylog['user_id']     = $activity->user_id;
                        $activitylog['user_type']   = $activity->user_type;
                        $activitylog['project_id']  = $duplicate->id;
                        $activitylog['log_type']    = $activity->log_type;
                        $activitylog['remark']      = $activity->remark;
                        $activitylog->save();
                    }
                }
            }
            return redirect()->back()->with('success', 'Project Created Successfully');
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function fileImportExport()
    {
        if (Auth::user()->isAbleTo('project import')) {
            return view('taskly::projects.import');
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function fileImport(Request $request)
    {
        if (Auth::user()->isAbleTo('project import')) {
            session_start();

            $error = '';

            $html = '';

            if ($request->file->getClientOriginalName() != '') {
                $file_array = explode(".", $request->file->getClientOriginalName());

                $extension = end($file_array);
                if ($extension == 'csv') {
                    $file_data = fopen($request->file->getRealPath(), 'r');

                    $file_header = fgetcsv($file_data);
                    $html .= '<table class="table table-bordered"><tr>';

                    for ($count = 0; $count < count($file_header); $count++) {
                        $html .= '
                                <th>
                                    <select name="set_column_data" class="form-control set_column_data" data-column_number="' . $count . '">
                                    <option value="">Set Count Data</option>
                                    <option value="name">Name</option>
                                    <option value="status">Status</option>
                                    <option value="description">Description</option>
                                    <option value="start_date">Start Date</option>
                                    <option value="end_date">End Date</option>
                                    <option value="budget">Budget</option>
                                    </select>
                                </th>
                                ';
                    }
                    $html .= '</tr>';
                    $limit = 0;
                    while (($row = fgetcsv($file_data)) !== false) {
                        $limit++;

                        $html .= '<tr>';

                        for ($count = 0; $count < count($row); $count++) {
                            $html .= '<td>' . $row[$count] . '</td>';
                        }

                        $html .= '</tr>';

                        $temp_data[] = $row;
                    }
                    $_SESSION['file_data'] = $temp_data;
                } else {
                    $error = 'Only <b>.csv</b> file allowed';
                }
            } else {

                $error = 'Please Select CSV File';
            }
            $output = array(
                'error' => $error,
                'output' => $html,
            );

            return json_encode($output);
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    public function fileImportModal()
    {
        if (Auth::user()->isAbleTo('project import')) {
            return view('taskly::projects.import_modal');
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function projectImportdata(Request $request)
    {
        if (Auth::user()->isAbleTo('project import')) {
            session_start();
            $html = '<h3 class="text-danger text-center">Below data is not inserted</h3></br>';
            $flag = 0;
            $html .= '<table class="table table-bordered"><tr>';
            $file_data = $_SESSION['file_data'];

            unset($_SESSION['file_data']);

            $user = Auth::user();


            foreach ($file_data as $row) {
                $projects = Project::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->Where('name', 'like', $row[$request->name])->get();

                if ($projects->isEmpty()) {

                    try {
                        $project = Project::create([
                            'name' => $row[$request->name],
                            'status' => $row[$request->status],
                            'description' => $row[$request->description],
                            'start_date' => $row[$request->start_date],
                            'end_date' => $row[$request->end_date],
                            'budget' => $row[$request->budget],
                            'created_by' => creatorId(),
                            'workspace' => getActiveWorkSpace(),
                        ]);
                        UserProject::create([
                            'user_id' => creatorId(),
                            'project_id' => $project->id,
                            'is_active' => 1,
                        ]);
                    } catch (\Exception $e) {
                        $flag = 1;
                        $html .= '<tr>';

                        $html .= '<td>' . $row[$request->name] . '</td>';
                        $html .= '<td>' . $row[$request->status] . '</td>';
                        $html .= '<td>' . $row[$request->description] . '</td>';
                        $html .= '<td>' . $row[$request->start_date] . '</td>';
                        $html .= '<td>' . $row[$request->end_date] . '</td>';
                        $html .= '<td>' . $row[$request->budget] . '</td>';

                        $html .= '</tr>';
                    }
                } else {
                    $flag = 1;
                    $html .= '<tr>';

                    $html .= '<td>' . $row[$request->name] . '</td>';
                    $html .= '<td>' . $row[$request->status] . '</td>';
                    $html .= '<td>' . $row[$request->description] . '</td>';
                    $html .= '<td>' . $row[$request->start_date] . '</td>';
                    $html .= '<td>' . $row[$request->end_date] . '</td>';
                    $html .= '<td>' . $row[$request->budget] . '</td>';

                    $html .= '</tr>';
                }
            }

            $html .= '
                            </table>
                            <br />
                            ';
            if ($flag == 1) {

                return response()->json([
                    'html' => true,
                    'response' => $html,
                ]);
            } else {
                return response()->json([
                    'html' => false,
                    'response' => 'Data Imported Successfully',
                ]);
            }
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }
    public function CopylinkSetting($id)
    {
        if (Auth::user()->isAbleTo('project setting')) {
            $project = Project::find($id);
            return view('taskly::projects.copylink_setting', compact('project'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function CopylinkSettingSave(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('project setting')) {
            $data = [];
            $data['basic_details']  = isset($request->basic_details) ? 'on' : 'off';
            $data['member']  = isset($request->member) ? 'on' : 'off';
            $data['client']  = isset($request->client) ? 'on' : 'off';
            $data['vendor']  = isset($request->vendor) ? 'on' : 'off';
            $data['milestone']  = isset($request->milestone) ? 'on' : 'off';
            $data['activity']  = isset($request->activity) ? 'on' : 'off';
            $data['attachment']  = isset($request->attachment) ? 'on' : 'off';
            $data['bug_report']  = isset($request->bug_report) ? 'on' : 'off';
            $data['task']  = isset($request->task) ? 'on' : 'off';
            $data['invoice']  = isset($request->invoice) ? 'on' : 'off';
            $data['bill']  = isset($request->bill) ? 'on' : 'off';
            $data['documents']  = isset($request->documents) ? 'on' : 'off';
            $data['timesheet']  = isset($request->timesheet) ? 'on' : 'off';
            $data['progress']  = isset($request->progress) ? 'on' : 'off';
            $data['retainer']  = isset($request->retainer) ? 'on' : 'off';
            $data['proposal']  = isset($request->proposal) ? 'on' : 'off';
            $data['password_protected']  = isset($request->password_protected) ? 'on' : 'off';
            $data['procurement']  = isset($request->procurement) ? 'on' : 'off';

            $project = Project::find($id);
            if (isset($request->password_protected) && $request->password_protected == 'on') {
                $project->password = base64_encode($request->password);
            } else {
                $project->password = null;
            }
            $project->copylinksetting = (count($data) > 0) ? json_encode($data) : null;
            $project->save();

            return redirect()->back()->with('success', __('Copy Link Setting Save Successfully!'));
        } else {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }
    public function PasswordCheck(Request $request, $id = null, $lang = null)
    {
        $id_de = Crypt::decrypt($id);
        $project = Project::find($id_de);
        if (!empty($project->copylinksetting) && json_decode($project->copylinksetting)->password_protected == 'on') {
            if (!empty($request->password) && $request->password == base64_decode($project->password)) {
                \Session::put('checked_' . $project->id, $project->id);
                return redirect()->route('project.shared.link', [$id, $lang]);
            } else {
                return redirect()->route('project.shared.link', [$id, $lang])->with('error', __('Password is wrong! Please enter valid password'));
            }
        }
    }
    public function ProjectSharedLink($id = null, $lang = null)
    {
        try {
            $id_de = Crypt::decrypt($id);
        } catch (\Throwable $th) {
            return redirect()->route('login', [$lang]);
        }
        $project = Project::find($id_de);
        $company_id = $project->created_by;
        $workspace_id = $project->workspace;
        $project_id = \Session::get('checked_' . $id_de);
        if ($lang == '') {
            $lang = !empty(company_setting('defult_language', $company_id, $workspace_id)) ? company_setting('defult_language', $company_id, $workspace_id) : 'en';
        }
        \App::setLocale($lang);

        if (!empty($project->copylinksetting) && json_decode($project->copylinksetting)->password_protected == 'on' && $project_id != $id_de) {
            return view('taskly::projects.password_check', compact('company_id', 'workspace_id', 'id', 'lang'));
        }
        if ($project) {
            $bills = [];
            if (module_is_active('Account')) {
                $bills = Bill::where('workspace', '=', getActiveWorkSpace($company_id))->where('bill_module', '=', 'taskly')->where('category_id', '=', $project->id)->get();
            }

            $documents = [];

            if (module_is_active('Documents')) {
                $documents = Document::with(['Type', 'user', 'Project'])->where('project_id', $project->id)->where('created_by',$project->created_by)->where('workspace_id', getActiveWorkSpace($company_id))->get();
            }

            //chartdata
            $chartData = $this->getProjectChart(
                [
                    'workspace_id' => $workspace_id,
                    'project_id' => $project->id,
                    'duration' => 'week',
                ]
            );
            if (date('Y-m-d') == $project->end_date || date('Y-m-d') >= $project->end_date) {
                $daysleft = 0;
            } else {
                $daysleft = round((((strtotime($project->end_date) - strtotime(date('Y-m-d'))) / 24) / 60) / 60);
            }

            $stages = $statusClass = [];

            if ($project) {
                $stages = Stage::where('workspace_id', '=', $workspace_id)->orderBy('order')->get();
                foreach ($stages as $status) {
                    $statusClass[] = 'task-list-' . str_replace(' ', '_', $status->id);
                    $task          = Task::where('project_id', '=', $id_de);
                    $task->orderBy('order');
                    $status['tasks'] = $task->where('status', '=', $status->id)->get();
                }
            }

            $bug_stages = BugStage::where('workspace_id', '=', $workspace_id)->orderBy('order')->get();
            foreach ($bug_stages as &$status) {
                $statusClass[] = 'task-list-' . str_replace(' ', '_', $status->id);
                $bug           = BugReport::where('project_id', '=', $id_de);
                $bug->orderBy('order');

                $status['bugs'] = $bug->where('status', '=', $status->id)->get();
            }
            $invoices = Invoice::where('workspace', '=', $workspace_id)->where('invoice_module', '=', 'taskly')->where('category_id', $project_id)->get();
            $retainers =[];
            if(module_is_active('Retainer'))
            {
                $retainers = Retainer::where('workspace', '=', $workspace_id)->where('account_type', '=', 'Projects')->where('category_id', $project->id)->get();
            }

            $proposals= Proposal::where('workspace', '=', $workspace_id)->where('account_type', '=', 'Projects')->where('category_id', $project->id)->get();

            return view('taskly::projects.sharedlink', compact('company_id', 'workspace_id', 'project', 'id', 'lang', 'chartData', 'daysleft', 'stages', 'bug_stages', 'invoices', 'bills', 'documents','retainers','proposals'));

        } else {
            return redirect()->route('project.shared.link', [$id, $lang])->with('error', __('Project not found Please check the link!'));
        }
    }
    public function ProjectLinkTaskShow($taskID)
    {
        if ($taskID) {
            $task    = Task::find($taskID);
            return view('taskly::projects.linktaskShow', compact('task', 'project'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }
    public function ProjectLinkbugReportShow($projectID, $bug_id)
    {
        if (!empty($projectID) && !empty($bug_id)) {
            $project = Project::find($projectID);
            $bug     = BugReport::find($bug_id);
            return view('taskly::projects.link_bug_report_show', compact('bug', 'project'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }


    public function calendar(Request $request,$id){
        $project = Project::find($id);
        $tasks    = Task::where('workspace', $project->workspace)->where('project_id' ,$id);
        $current_month_task = Task::select('title', 'start_date', 'due_date', 'created_at')->where('workspace', getActiveWorkSpace())
        ->whereRaw('MONTH(start_date) = ? AND MONTH(due_date) = ?', [date('m'), date('m')])->where('project_id' ,$id)
        ->get();
        if (!empty($request->start_date)) {
            $tasks->where('start_date', '>=', $request->start_date);
        }
        if (!empty($request->due_date)) {
            $tasks->where('due_date', '<=', $request->due_date);
        }
        $tasks = $tasks->get();
        $arrTask = [];
        foreach ($tasks as $task) {
            $arr['id']        = $task['id'];
            $arr['title']     = $task['title'];
            $arr['start']     = $task['start_date'];
            $arr['end']       = date('Y-m-d', strtotime($task['due_date'] . ' +1 day'));
            $arr['className'] = 'event-danger task-edit';
            $arr['url']       = route('tasks.edit', [$id,$task['id']]);
            $arrTask[]    = $arr;
        }
        $arrTask =  json_encode($arrTask);

        return view('taskly::projects.calendar', compact('current_month_task','arrTask' ,'id'));
    }


    public function finance(Request $request ,$id){
        $project = Project::find($id);
        $query = Proposal::where('workspace',getActiveWorkSpace())->where('account_type' ,'Projects')->where('category_id',$id);
        $proposals = $query->with('customers')->get();
        return view('taskly::projects.proposal' ,compact('project' ,'id' ,'proposals'));

    }

    public function proposal(Request $request ,$id){
        $project = Project::find($id);
        $query = Proposal::where('workspace',getActiveWorkSpace())->where('account_type' ,'Projects')->where('category_id',$id);
        $proposals = $query->with('customers')->get();

        return view('taskly::projects.proposal', compact( 'id','project','proposals'));

    }

    public function purchases(Request $request ,$id){
        $project = Project::find($id);

        $purchases = Purchase::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->with('vender', 'category', 'user')->get();

        return view('taskly::projects.purchases', compact( 'id'));
    }
     public function taskCountData(Request $request)
    {
            $objUser = Auth::user();
        
            $assignee_name = $request->assignee_name;
            $assignor_name = $request->assignor_name;
            $status_name = $request->status_name;
             $group_name = $request->group_name;
             $task_name = $request->task_name;
              $priority = $request->priority;
            $searchValue ="";
            if (request()->has('search_value') && !empty(request()->input('search_value'))) {
                $searchValue = request()->input('search_value');
            }
            $workspaceId = getActiveWorkSpace();
            if($objUser->hasRole('company') || $objUser->hasRole('Manager All Access') || $objUser->hasRole('hr') )
            {
                    $taskBaseQuery = Task::join('stages', 'stages.name', '=', 'tasks.status')
                        ->leftJoin('users as assignor_users', 'assignor_users.email', '=', 'tasks.assignor')
                        ->where('tasks.workspace', $workspaceId)
                        ->whereNull('tasks.deleted_at')
                        ->select('tasks.*', 'stages.name as stage_name', 'assignor_users.name as assigner_name','eta_time','etc_done')
                        ->distinct('tasks.id');
                        if (!empty($searchValue)) {
                            $taskBaseQuery->where(function ($query) use ($searchValue) {
                                // Search by assignor name
                                $query->where('assignor_users.name', 'like', "%$searchValue%")
                                    // Search by users in assign_to field (using FIND_IN_SET)
                                    ->orWhereRaw("
                                        EXISTS (
                                            SELECT 1 
                                            FROM users 
                                            WHERE FIND_IN_SET(users.email, tasks.assign_to) 
                                            AND users.name LIKE ?
                                        )", ["%$searchValue%"])
                                    // Search by task title
                                    ->orWhere('tasks.title', 'like', "%$searchValue%")
                                    // Search by group name
                                    ->orWhere('tasks.group', 'like', "%$searchValue%");
                            });
                        }

                    $completedTask = (clone $taskBaseQuery)
                        ->where('tasks.status', 'Done');
                         $pendingTask = (clone $taskBaseQuery)
                        ->where('tasks.status', '!=', 'Done')
                        ->where('tasks.status', '!=', '');
                 
                    $overdueTask = (clone $taskBaseQuery)
                        ->where('tasks.status', '!=', '')
                        ->where('tasks.status', '!=', '')
                        ->where('tasks.due_date', '<', now());
                    $totalTask = (clone $taskBaseQuery);
                     $totalETAmin = (clone $taskBaseQuery);
                      $totalATCMin = (clone $taskBaseQuery);
                  
                    if($assignor_name && count($assignor_name) ){
                        $completedTask->where('assignor', 'like', "%$assignor_name[0]%");
                        $pendingTask->where('assignor', 'like', "%$assignor_name[0]%");
                         $overdueTask->where('assignor', 'like', "%$assignor_name[0]%");
                        $totalTask->where('assignor', 'like', "%$assignor_name[0]%");
                        $totalETAmin->where('assignor', 'like', "%$assignor_name[0]%");
                        $totalATCMin->where('assignor', 'like', "%$assignor_name[0]%");

                    }
                    if($group_name && !empty($group_name) ){
                        $completedTask->where('group', 'like', "%$group_name%");
                        $pendingTask->where('group', 'like', "%$group_name%");
                         $overdueTask->where('group', 'like', "%$group_name%");
                        $totalTask->where('group', 'like', "%$group_name%");
                        $totalETAmin->where('group', 'like', "%$group_name%");
                        $totalATCMin->where('group', 'like', "%$group_name%");
                    }
                     if($task_name && !empty($task_name) ){
                        $completedTask->where('title', 'like', "%$task_name%");
                        $pendingTask->where('title', 'like', "%$task_name%");
                        $overdueTask->where('title', 'like', "%$task_name%");
                        $totalTask->where('title', 'like', "%$task_name%");
                        $totalETAmin->where('title', 'like', "%$task_name%");
                        $totalATCMin->where('title', 'like', "%$task_name%");
                    }
                    if($priority && !empty($priority) ){
                        $completedTask->where('priority', 'like', "%$priority%");
                        $pendingTask->where('priority', 'like', "%$priority%");
                        $overdueTask->where('priority', 'like', "%$priority%");
                        $totalTask->where('priority', 'like', "%$priority%");
                        $totalETAmin->where('priority', 'like', "%$priority%");
                        $totalATCMin->where('priority', 'like', "%$priority%");
                    }
                    if($assignee_name && count($assignee_name) ){
                        $completedTask->where('assign_to', 'like', "%$assignee_name[0]%");
                        $pendingTask->where('assign_to', 'like', "%$assignee_name[0]%");
                        $overdueTask->where('assign_to', 'like', "%$assignee_name[0]%");
                        $totalTask->where('assign_to', 'like', "%$assignee_name[0]%");
                        $totalETAmin->where('assign_to', 'like', "%$assignee_name[0]%");
                        $totalATCMin->where('assign_to', 'like', "%$assignee_name[0]%");

                    }
                   
                    $completedTask = $completedTask->count();
                    $pendingTask =$pendingTask->count();
                    
                    $totalETAmin = collect($totalETAmin->where('eta_time',">",0)->pluck('eta_time')->toArray())->sum();
                    
                    $totalATCMin = collect($totalATCMin->where('etc_done',">",0)->pluck('etc_done')->toArray())->sum();
                    $overdueTask = $overdueTask->count();

                    $totalTask = $totalTask->count();
            }else
            {
                      $email = $objUser->email;
                    $workspaceId = getActiveWorkSpace();
                    
                    // Base query with all common filters
                    $taskBaseQuery = Task::join('stages', 'stages.name', '=', 'tasks.status')
                        ->leftJoin('users as assignor_users', 'assignor_users.email', '=', 'tasks.assignor')
                        ->where('tasks.workspace', $workspaceId)
                        ->whereNull('tasks.deleted_at')
                        ->where(function ($query) use ($email) {
                            $query->whereRaw("FIND_IN_SET(?, tasks.assign_to)", [$email])
                                  ->orWhere('tasks.assignor', $email);
                        })
                        ->select('tasks.*', 'stages.name as stage_name', 'assignor_users.name as assigner_name','eta_time','etc_done')
                        ->distinct('tasks.id');
                    
                    // Total Tasks
                    $totalTask = (clone $taskBaseQuery)->count('tasks.id');
                    
                    // Completed Tasks
                    $completedTask = (clone $taskBaseQuery)
                        ->where('tasks.status', 'Done')
                        ->count('tasks.id');
                    
                    // Pending Tasks
                    $pendingTask = (clone $taskBaseQuery)
                        ->where('tasks.status', '!=', 'Done')
                        ->where('tasks.status', '!=', '')
                        ->count('tasks.id');
                    
                    // Overdue Tasks
                    $overdueTask = (clone $taskBaseQuery)
                        ->where('tasks.status', '!=', '')
                        ->where('tasks.status', '!=', '')
                        ->where('tasks.due_date', '<', now())
                        ->count('tasks.id');
                        
                     $totalETAmin = collect((clone $taskBaseQuery)->where('eta_time',">",0)->pluck('eta_time')->toArray())->sum();
                     $totalATCMin = collect((clone $taskBaseQuery)->where('etc_done',">",0)->pluck('etc_done')->toArray())->sum();
            }
            
        // $totalETAHours = number_format($totalETAmin/60,2);
        $totalETAHours = round($totalETAmin/60);
        $totalATCHours = round($totalATCMin/60);

        return response()->json(
            [
                'is_success' => true,
                'data' =>['complete_count'=>$completedTask,'pending_count'=>$pendingTask,'overdue_count'=>$overdueTask,'total_count'=>$totalTask,'total_eta'=>$totalETAHours,'total_atc' => $totalATCHours]
            ],
            200
        );
    }
        public function doneTasklist(ProjectDoneTaskDatatable $dataTable)
        {
            if (\Auth::user()->isAbleTo('task manage')) {
                $currentWorkspace = getActiveWorkSpace();
                $objUser = Auth::user();
                $stages = Stage::where('workspace_id', '=', $currentWorkspace)->orderBy('order')->get();
                $users = User::select('users.*')->get();
               
                if($objUser->hasRole('company') || $objUser->hasRole('Manager All Access') || $objUser->hasRole('hr') )
                {
                        $competeTask = Task::where('status','Done')->whereNotNull('deleted_at')->count();
                        $pendingTask = Task::whereNotIn('status',['Done'])->whereNotNull('deleted_at')->count();
                        $totalETAmin = collect(Task::where('status','Done')->whereNotNull('deleted_at')->where('eta_time',">",0)->pluck('eta_time')->toArray())->sum();
                        $totalATCMin = collect(Task::where('status','Done')->whereNotNull('deleted_at')->where('eta_time',">",0)->pluck('etc_done')->toArray())->sum();
                        
                        // Calculate initial average completion days for all users
                        $tasksWithCompletionDays = Task::where('status','Done')->whereNotNull('deleted_at')
                            ->whereNotNull('start_date')
                            ->whereNotNull('completion_date')
                            ->where('completion_date', '!=', '0000-00-00')
                            ->where('completion_date', '!=', '0000-00-00 00:00:00')
                            ->get();
                            
                        $totalCompletionDays = 0;
                        $validTasksCount = 0;
                        
                        foreach ($tasksWithCompletionDays as $task) {
                            try {
                                $startDate = \Carbon\Carbon::parse($task->start_date);
                                $completionDate = \Carbon\Carbon::parse($task->completion_date);
                                $days = $startDate->diffInDays($completionDate);
                                $totalCompletionDays += $days;
                                $validTasksCount++;
                            } catch (\Exception $e) {
                                // Skip this task if dates are invalid
                                continue;
                            }
                        }
                        
                        $avgCompletionDays = $validTasksCount > 0 ? round($totalCompletionDays / $validTasksCount, 1) : 0;
                        
                }else
                {
                    $email = $objUser->email;
                        $competeTask = Task::where('status','done')->whereNotNull('deleted_at')->where(function ($query) use ($email) {
                                    $query->where('assignor', 'like', "%$email%")
                                        ->orWhere('assign_to', 'like', "%$email%");
                                })->count();
                        $pendingTask = Task::whereNotIn('status',['done'])->whereNotNull('deleted_at')->where(function ($query) use ($email) {
                                   $query->where('assignor', 'like', "%$email%")
                                        ->orWhere('assign_to', 'like', "%$email%");
                                })->count();
                                
                     $totalETAmin = collect(Task::where('status','Done')->whereNotNull('deleted_at')->where(function ($query) use ($email) {
                                    $query->where('assignor', 'like', "%$email%")
                                        ->orWhere('assign_to', 'like', "%$email%");
                                })->where('eta_time',">",0)->pluck('eta_time')->toArray())->sum();
                    $totalATCMin = collect(Task::where('status','Done')->whereNotNull('deleted_at')->where(function ($query) use ($email) {
                                    $query->where('assignor', 'like', "%$email%")
                                        ->orWhere('assign_to', 'like', "%$email%");
                                })->where('etc_done',">",0)->pluck('etc_done')->toArray())->sum();
                                
                    // Calculate initial average completion days for user's tasks
                    $tasksWithCompletionDays = Task::where('status','Done')->whereNotNull('deleted_at')
                        ->where(function ($query) use ($email) {
                            $query->where('assignor', 'like', "%$email%")
                                  ->orWhere('assign_to', 'like', "%$email%");
                        })
                        ->whereNotNull('start_date')
                        ->whereNotNull('completion_date')
                        ->where('completion_date', '!=', '0000-00-00')
                        ->where('completion_date', '!=', '0000-00-00 00:00:00')
                        ->get();
                        
                    $totalCompletionDays = 0;
                    $validTasksCount = 0;
                    
                    foreach ($tasksWithCompletionDays as $task) {
                        try {
                            $startDate = \Carbon\Carbon::parse($task->start_date);
                            $completionDate = \Carbon\Carbon::parse($task->completion_date);
                            $days = $startDate->diffInDays($completionDate);
                            $totalCompletionDays += $days;
                            $validTasksCount++;
                        } catch (\Exception $e) {
                            // Skip this task if dates are invalid
                            continue;
                        }
                    }
                    
                    $avgCompletionDays = $validTasksCount > 0 ? round($totalCompletionDays / $validTasksCount, 1) : 0;
                }
            
                $priority = collect([
                    ['value'=>"urgent", 'color' => 'danger'],
                    ['value'=>"Take your time", 'color' => 'warning'],
                    ['value'=>"normal", 'color' => 'success']
                ]);
                $users = User::select('users.*')->get();
                $totalETAmin = number_format($totalETAmin/60,2);
                $totalATCMin = number_format($totalATCMin/60,2);
                return $dataTable->render('taskly::projects.done-task.donetasklist',compact('currentWorkspace','totalETAmin','stages','users','competeTask','pendingTask','priority','totalATCMin','avgCompletionDays'));
            } else {
                return redirect()->back()->with('error', 'permission Denied');
            }
        }
public function doneTaskCountData(Request $request)
{
    $objUser = Auth::user();

    $assignee_name = $request->assignee_name;
    $assignor_name = $request->assignor_name;
    $status_name = $request->status_name;
    $group_name = $request->group_name;
    $task_name = $request->task_name;
    $month = $request->month;
    $searchValue = "";
    if ($request->has('search_value') && !empty($request->input('search_value'))) {
        $searchValue = $request->input('search_value');
    }
    $workspaceId = getActiveWorkSpace();
$dateFilter = $request->input('date_filter');
    if($objUser->hasRole('company') || $objUser->hasRole('Manager All Access') || $objUser->hasRole('hr') )
    {
        $taskBaseQuery =  Task::select('tasks.*', 'stages.name as stage_name', 'assignor_users.name as assigner_name')
            ->join('stages', 'stages.name', '=', 'tasks.status')
            ->whereNotNull('deleted_at')
            ->where('status', "Done")
            ->leftJoin('users as assignor_users', 'assignor_users.email', '=', 'tasks.assignor')
            ->where('tasks.workspace', $workspaceId)
            ->groupBy('tasks.id');
        
             // Date filter on completion_date
        if ($dateFilter == 'today') {
            $taskBaseQuery->whereDate('tasks.completion_date', now()->toDateString());
        } elseif ($dateFilter == 'yesterday') {
            $taskBaseQuery->whereDate('tasks.completion_date', now()->subDay()->toDateString());
        } elseif ($dateFilter == 'this_week') {
            $taskBaseQuery->whereBetween('tasks.completion_date', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($dateFilter == 'this_month') {
            $taskBaseQuery->whereMonth('tasks.completion_date', now()->month)
                          ->whereYear('tasks.completion_date', now()->year);
        } elseif ($dateFilter == 'previous_month') {
            $taskBaseQuery->whereMonth('tasks.completion_date', now()->subMonth()->month)
                          ->whereYear('tasks.completion_date', now()->subMonth()->year);
        } elseif ($dateFilter == 'last_30_days') {
            $taskBaseQuery->whereBetween('tasks.completion_date', [now()->subDays(30)->toDateString(), now()->toDateString()]);
        } elseif ($dateFilter == 'custom') {
            // Handle custom date range
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            
            if (!empty($startDate) && !empty($endDate)) {
                // Ensure proper date format and handle both date and datetime
                $startDate = \Carbon\Carbon::parse($startDate)->startOfDay()->format('Y-m-d H:i:s');
                $endDate = \Carbon\Carbon::parse($endDate)->endOfDay()->format('Y-m-d H:i:s');
                $taskBaseQuery->whereBetween('tasks.completion_date', [$startDate, $endDate]);
            } elseif (!empty($startDate)) {
                $startDate = \Carbon\Carbon::parse($startDate)->format('Y-m-d');
                $taskBaseQuery->whereDate('tasks.completion_date', '>=', $startDate);
            } elseif (!empty($endDate)) {
                $endDate = \Carbon\Carbon::parse($endDate)->format('Y-m-d');
                $taskBaseQuery->whereDate('tasks.completion_date', '<=', $endDate);
            }
        }
        // Search value logic
        if (!empty($searchValue)) {
            $taskBaseQuery->where(function ($query) use ($searchValue) {
                $query->where('assignor_users.name', 'like', "%$searchValue%")
                    ->orWhereRaw("
                        EXISTS (
                            SELECT 1 
                            FROM users 
                            WHERE FIND_IN_SET(users.email, tasks.assign_to) 
                            AND users.name LIKE ?
                        )", ["%$searchValue%"])
                    ->orWhere('tasks.title', 'like', "%$searchValue%")
                    ->orWhere('tasks.group', 'like', "%$searchValue%");
            });
        }
        // Month filter - filter by completion_date instead of start_date
        if (!empty($month)) {
            $taskBaseQuery->whereMonth('tasks.completion_date', $month);
        }

        // Assignor filter
        if ($assignor_name && is_array($assignor_name) && count($assignor_name)) {
            $taskBaseQuery->where(function($q) use ($assignor_name) {
                foreach ($assignor_name as $email) {
                    $q->orWhere('tasks.assignor', 'like', "%$email%");
                }
            });
        }

        // Assignee filter
        if ($assignee_name && is_array($assignee_name) && count($assignee_name)) {
            $taskBaseQuery->where(function($q) use ($assignee_name) {
                foreach ($assignee_name as $email) {
                    $q->orWhere('tasks.assign_to', 'like', "%$email%");
                }
            });
        }

        // Group filter
        if ($group_name && !empty($group_name)) {
            $taskBaseQuery->where('tasks.group', 'like', "%$group_name%");
        }

        // Task name filter
        if ($task_name && !empty($task_name)) {
            $taskBaseQuery->where('tasks.title', 'like', "%$task_name%");
        }

        $totalETAmin = collect((clone $taskBaseQuery)->where('eta_time',">",0)->pluck('eta_time')->toArray())->sum();
        $totalATCMin = collect((clone $taskBaseQuery)->where('etc_done',">",0)->pluck('etc_done')->toArray())->sum();
        
        // Calculate average completion days
        $tasksWithCompletionDays = (clone $taskBaseQuery)->whereNotNull('start_date')
            ->whereNotNull('completion_date')
            ->where('completion_date', '!=', '0000-00-00')
            ->where('completion_date', '!=', '0000-00-00 00:00:00')
            ->get();
        
        $totalCompletionDays = 0;
        $validTasksCount = 0;
        
        foreach ($tasksWithCompletionDays as $task) {
            try {
                $startDate = \Carbon\Carbon::parse($task->start_date);
                $completionDate = \Carbon\Carbon::parse($task->completion_date);
                $days = $startDate->diffInDays($completionDate);
                $totalCompletionDays += $days;
                $validTasksCount++;
            } catch (\Exception $e) {
                // Skip this task if dates are invalid
                continue;
            }
        }
        
        $avgCompletionDays = $validTasksCount > 0 ? round($totalCompletionDays / $validTasksCount, 1) : 0;

    } else {
        $email = $objUser->email;
        $taskBaseQuery =  Task::select('tasks.*', 'stages.name as stage_name', 'assignor_users.name as assigner_name')
            ->join('stages', 'stages.name', '=', 'tasks.status')
            ->whereNotNull('deleted_at')
            ->where('status', "Done")
            ->where(function ($query) use ($email) {
                $query->whereRaw("FIND_IN_SET(?, tasks.assign_to)", [$email])
                      ->orWhere('tasks.assignor', $email);
            })
            ->leftJoin('users as assignor_users', 'assignor_users.email', '=', 'tasks.assignor')
            ->where('tasks.workspace', $workspaceId)
            ->groupBy('tasks.id');
        // Date filter on completion_date
        if ($dateFilter == 'today') {
            $taskBaseQuery->whereDate('tasks.completion_date', now()->toDateString());
        } elseif ($dateFilter == 'yesterday') {
            $taskBaseQuery->whereDate('tasks.completion_date', now()->subDay()->toDateString());
        } elseif ($dateFilter == 'this_week') {
            $taskBaseQuery->whereBetween('tasks.completion_date', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($dateFilter == 'this_month') {
            $taskBaseQuery->whereMonth('tasks.completion_date', now()->month)
                          ->whereYear('tasks.completion_date', now()->year);
        } elseif ($dateFilter == 'previous_month') {
            $taskBaseQuery->whereMonth('tasks.completion_date', now()->subMonth()->month)
                          ->whereYear('tasks.completion_date', now()->subMonth()->year);
        }elseif ($dateFilter == 'last_30_days') {
            $taskBaseQuery->whereBetween('tasks.completion_date', [now()->subDays(30)->toDateString(), now()->toDateString()]);
        } elseif ($dateFilter == 'custom') {
            // Handle custom date range
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            
            if (!empty($startDate) && !empty($endDate)) {
                // Ensure proper date format and handle both date and datetime
                $startDate = \Carbon\Carbon::parse($startDate)->startOfDay()->format('Y-m-d H:i:s');
                $endDate = \Carbon\Carbon::parse($endDate)->endOfDay()->format('Y-m-d H:i:s');
                $taskBaseQuery->whereBetween('tasks.completion_date', [$startDate, $endDate]);
            } elseif (!empty($startDate)) {
                $startDate = \Carbon\Carbon::parse($startDate)->format('Y-m-d');
                $taskBaseQuery->whereDate('tasks.completion_date', '>=', $startDate);
            } elseif (!empty($endDate)) {
                $endDate = \Carbon\Carbon::parse($endDate)->format('Y-m-d');
                $taskBaseQuery->whereDate('tasks.completion_date', '<=', $endDate);
            }
        }
        // Month filter
        if (!empty($month)) {
            $taskBaseQuery->whereMonth('tasks.completion_date', $month);
        }

        // Assignor filter
        if ($assignor_name && is_array($assignor_name) && count($assignor_name)) {
            $taskBaseQuery->where(function($q) use ($assignor_name) {
                foreach ($assignor_name as $email) {
                    $q->orWhere('tasks.assignor', 'like', "%$email%");
                }
            });
        }

        // Assignee filter
        if ($assignee_name && is_array($assignee_name) && count($assignee_name)) {
            $taskBaseQuery->where(function($q) use ($assignee_name) {
                foreach ($assignee_name as $email) {
                    $q->orWhere('tasks.assign_to', 'like', "%$email%");
                }
            });
        }

        // Group filter
        if ($group_name && !empty($group_name)) {
            $taskBaseQuery->where('tasks.group', 'like', "%$group_name%");
        }

        // Task name filter
        if ($task_name && !empty($task_name)) {
            $taskBaseQuery->where('tasks.title', 'like', "%$task_name%");
        }

        $totalETAmin = collect((clone $taskBaseQuery)->where('eta_time',">",0)->pluck('eta_time')->toArray())->sum();
        $totalATCMin = collect((clone $taskBaseQuery)->where('etc_done',">",0)->pluck('etc_done')->toArray())->sum();
        
        // Calculate average completion days
        $tasksWithCompletionDays = (clone $taskBaseQuery)->whereNotNull('start_date')
            ->whereNotNull('completion_date')
            ->where('completion_date', '!=', '0000-00-00')
            ->where('completion_date', '!=', '0000-00-00 00:00:00')
            ->get();
        
        $totalCompletionDays = 0;
        $validTasksCount = 0;
        
        foreach ($tasksWithCompletionDays as $task) {
            try {
                $startDate = \Carbon\Carbon::parse($task->start_date);
                $completionDate = \Carbon\Carbon::parse($task->completion_date);
                $days = $startDate->diffInDays($completionDate);
                $totalCompletionDays += $days;
                $validTasksCount++;
            } catch (\Exception $e) {
                // Skip this task if dates are invalid
                continue;
            }
        }
        
        $avgCompletionDays = $validTasksCount > 0 ? round($totalCompletionDays / $validTasksCount, 1) : 0;
    }

    $totalETAHours = round($totalETAmin/60);
    $totalATCHours = round($totalATCMin/60);

    return response()->json(
        [
            'is_success' => true,
            'data' =>['total_eta'=>$totalETAHours,'total_atc' => $totalATCHours, 'avg_completion_days' => $avgCompletionDays, 'total_tasks_with_completion' => $validTasksCount]
        ],
        200
    );
}

public function taskRatingData(Request $request)
{
    $objUser = Auth::user();
    $workspaceId = getActiveWorkSpace();

    // Base query for tasks
    $taskBaseQuery = Task::where('workspace', $workspaceId);

    // Apply user permissions
    if (!$objUser->hasRole('client') && !$objUser->hasRole('company') && !$objUser->hasRole('Manager All Access') && !$objUser->hasRole('hr')) {
        $taskBaseQuery->where(function ($query) use ($objUser) {
            $query->whereRaw("FIND_IN_SET(?, assign_to)", [$objUser->email])
                ->orWhere('assignor', $objUser->email);
        });
    }

    // Get all tasks with delete_rating
    $ratedTasks = (clone $taskBaseQuery)->whereNotNull('delete_rating')
        ->where('delete_rating', '>', 0)
        ->get();

    $totalRatings = $ratedTasks->count();
    
    if ($totalRatings == 0) {
        return response()->json([
            'average_rating' => '0.0',
            'total_ratings' => 0,
            'rating_breakdown' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0],
            'recent_reviews' => []
        ]);
    }

    // Calculate average rating
    $totalRatingSum = $ratedTasks->sum('delete_rating');
    $averageRating = round($totalRatingSum / $totalRatings, 1);

    // Calculate rating breakdown
    $ratingBreakdown = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
    foreach ($ratedTasks as $task) {
        $rating = (int) $task->delete_rating;
        if ($rating >= 1 && $rating <= 5) {
            $ratingBreakdown[$rating]++;
        }
    }

    // Get recent reviews (tasks with feedback)
    $recentReviews = (clone $taskBaseQuery)->whereNotNull('delete_rating')
        ->where('delete_rating', '>', 0)
        ->whereNotNull('delete_feedback')
        ->where('delete_feedback', '!=', '')
        ->orderBy('updated_at', 'desc')
        ->limit(5)
        ->get()
        ->map(function ($task) {
            return [
                'task_title' => $task->title,
                'rating' => (int) $task->delete_rating,
                'feedback' => $task->delete_feedback,
                'created_at' => $task->updated_at->diffForHumans()
            ];
        });

    return response()->json([
        'average_rating' => number_format($averageRating, 1),
        'total_ratings' => $totalRatings,
        'rating_breakdown' => $ratingBreakdown,
        'recent_reviews' => $recentReviews
    ]);
}


    public function invoice(Request $request ,$id){
        $project = Project::find($id);
        $query = Invoice::where('workspace', getActiveWorkSpace())->where('account_type','Taskly')->where('category_id',$id);
        $invoices = $query->with('customers')->orderBy('id', 'desc')->get();
        return view('taskly::projects.invoice', compact( 'project' ,'id' ,'invoices'));
    }
      public function taskImport(Request $request)
    {
        $objUser          = Auth::user();
        $currentWorkspace = getActiveWorkSpace();
        if ($request->isMethod('post')) {
            $request->validate([
                'file' => 'required',
            ]);
            Excel::import(new TaskImport(), request()->file('file'));
            $redirectUrl = route('projecttask.list');
            $message = "Task Imported successfully";
            return response()->json([
                'status'        =>  true,
                'response_code' =>  200,
                'message'       =>  $message,
                'data'          =>  ['redirect_url'=>$redirectUrl]
            ], 200);
        }

        return view('taskly::projects.import', compact('currentWorkspace', 'objUser'));
    }

public function taskTracklist(Request $request)
{
    $currentWorkspace = getActiveWorkSpace();
    $objUser = Auth::user();
    $stages = Stage::where('workspace_id', '=', $currentWorkspace)->orderBy('order')->get();
    
    // Get users with their employee and designation information
    $users = User::with(['employee', 'employee.designation','employee.department'])
        ->select('users.*')
        ->whereNotIn('email', ['company@example.com', 'president@5core.com', 'superadmin@example.com'])
        ->get();
    
    if($objUser->hasRole('company') || $objUser->hasRole('Manager All Access') || $objUser->hasRole('hr')) 
    {
        $competeTask = Task::where('status','done')->count();
        $taskRating = Task::where('status','done')->sum('delete_rating');
        $pendingTask = Task::whereNotIn('status',['done'])->count();
    } else {
        $email = $objUser->email;
        $competeTask = Task::where('status','done')->where('assign_to', 'like', "%$email%")->count();
        $taskRating = Task::where('status','done')->where('assign_to', 'like', "%$email%")->sum('delete_rating');
        $pendingTask = Task::whereNotIn('status',['done'])->where('assign_to', 'like', "%$email%")->count();
    }

    $priority = collect([
        ['value'=>"urgent", 'color' => 'danger'],
        ['value'=>"Take your time", 'color' => 'warning'],
        ['value'=>"normal", 'color' => 'success']
    ]);
    
    $users = User::with('employee')->select('users.*')
        ->whereNotIn('email',['company@example.com','president@5core.com','superadmin@example.com'])
        ->get();
    
    $resultData = [];
    $workspaceId = getActiveWorkSpace();
    
    foreach ($users as $key => $value) {
        $email = $value->email;
        
        // Only count tasks where the user is in assign_to (assignee)
        $totalCount = Task::where('workspace', $workspaceId)
            ->whereNull('deleted_at')
            ->whereRaw("FIND_IN_SET(?, assign_to)", [$email])
            ->count();
            
        $doneCount = Task::where('workspace', $workspaceId)
            ->whereNull('deleted_at')
            ->where('status', 'Done')
            ->whereRaw("FIND_IN_SET(?, assign_to)", [$email])
            ->count();
            
        $pendingCount = Task::where('workspace', $workspaceId)
            ->whereNull('deleted_at')
            ->where('status', '!=', 'Done')
            ->where('status', '!=', '')
            ->whereRaw("FIND_IN_SET(?, assign_to)", [$email])
            ->count();
            
        $overdueCount = Task::where('workspace', $workspaceId)
            ->whereNull('deleted_at')
            ->where('status', '!=', 'Done')
            ->where('status', '!=', '')
            ->where('due_date', '<', now())
            ->whereRaw("FIND_IN_SET(?, assign_to)", [$email])
            ->count();
            
        $doneRatingSum = Task::where('workspace', $workspaceId)
            ->where('delete_rating', '>', 0)
            ->whereNotNull('deleted_at')
            ->whereRaw("FIND_IN_SET(?, assign_to)", [$email]) // Only tasks where user is assignee
            ->sum('delete_rating');
            
        $assigneeEtaSum = Task::where('workspace', $workspaceId)
            ->whereNull('deleted_at')
            ->whereRaw("FIND_IN_SET(?, assign_to)", [$email])
            ->where('eta_time', '>', 0)
            ->sum('eta_time');


            // L30: ETA from last 30 days
        $assigneeEtaSumL30 = Task::where('workspace', $workspaceId)
            ->whereNull('deleted_at')
            ->whereRaw("FIND_IN_SET(?, assign_to)", [$email])
            ->where('eta_time', '>', 0)
            ->where('created_at', '>=', now()->subDays(30))
            ->sum('eta_time');

        $assigneeEtaSumL7 = Task::where('workspace', $workspaceId)
            ->whereNull('deleted_at')
            ->whereRaw("FIND_IN_SET(?, assign_to)", [$email])
            ->where('eta_time', '>', 0)
            ->where('created_at', '>=', now()->subDays(7))
            ->sum('eta_time');

            $resultData[$key] = [
            'name' => $value->name,
            'total_count' => $totalCount,
            'pending_count' => $pendingCount,
            'overdue_count' => $overdueCount,
            'delete_rating' => $doneRatingSum,
            'manager_name' => $value?->employee?->manager_name,
            'dept' => $value?->employee?->department?->name ?? 'N/A',
            'designation' => $value?->employee?->designation?->name ?? 'N/A',
            'done_count' => $doneCount,
            'eta_sum' => number_format($assigneeEtaSum / 60, 2),      // total in hours
            'eta_sum_l30' => number_format($assigneeEtaSumL30 / 60, 2), // last 30 days
            'eta_sum_l7' => number_format($assigneeEtaSumL7 / 60, 2),   // last 7 days
        ];

    }
    
    usort($resultData, function($a, $b) {
        return $b['overdue_count'] <=> $a['overdue_count'];
    });
    
    $sorted = collect($resultData)->sortByDesc('overdue_count')->values();
    $resultData = $sorted->toArray();

    return view('taskly::projects.track-task.list',compact('currentWorkspace','resultData','stages','users','competeTask','pendingTask','priority'));
}

public function getTeamloggerData(Request $request)
{
    try {
        // Get assignee emails from request, fallback to current user
        $assigneeEmails = $request->input('assignee_emails', []);
        $assignorEmails = $request->input('assignor_emails', []);
        $currentUserEmail = Auth::user()->email;
        
        // If no specific emails provided, use current user
        $targetEmails = [];
        if (!empty($assigneeEmails)) {
            $targetEmails = array_merge($targetEmails, $assigneeEmails);
        }
        if (!empty($assignorEmails)) {
            $targetEmails = array_merge($targetEmails, $assignorEmails);
        }
        if (empty($targetEmails)) {
            $targetEmails = [$currentUserEmail];
        }
        
        // Remove duplicates
        $targetEmails = array_unique($targetEmails);
        
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api2.teamlogger.com/api/employee_summary_report?startTime=1754006400000&endTime=1756684799000',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vaGlwZXJyLmNvbSIsInN1YiI6IjYyNDJhZjhhNmJlMjQ2YzQ5MTcwMmFiYjgyYmY5ZDYwIiwiYXVkIjoic2VydmVyIn0.mRzusxn0Ws9yD7Qmxu9QcFCNiLOnoEXSjy90edAFK4U',
                'Content-Type: application/json'
            ),
        ));
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        if ($httpCode === 200 && $response) {
            $data = json_decode($response, true);
            $totalHours = 0;
            $foundEmails = [];
            
            // Try different response structures and sum hours for target emails
            if (isset($data['data']) && is_array($data['data'])) {
                foreach ($data['data'] as $employee) {
                    if (isset($employee['email']) && in_array($employee['email'], $targetEmails)) {
                        $hours = isset($employee['totalHours']) ? $employee['totalHours'] : 0;
                        $totalHours += $hours;
                        $foundEmails[] = $employee['email'];
                    }
                }
            } elseif (isset($data['employees']) && is_array($data['employees'])) {
                foreach ($data['employees'] as $employee) {
                    if (isset($employee['email']) && in_array($employee['email'], $targetEmails)) {
                        $hours = isset($employee['totalHours']) ? $employee['totalHours'] : 0;
                        $totalHours += $hours;
                        $foundEmails[] = $employee['email'];
                    }
                }
            } elseif (isset($data['totalHours']) && count($targetEmails) === 1 && $targetEmails[0] === $currentUserEmail) {
                // Single user response structure
                $totalHours = $data['totalHours'];
                $foundEmails[] = $currentUserEmail;
            } elseif (is_array($data)) {
                foreach ($data as $employee) {
                    if (isset($employee['email']) && in_array($employee['email'], $targetEmails)) {
                        $hours = isset($employee['totalHours']) ? $employee['totalHours'] : 0;
                        $totalHours += $hours;
                        $foundEmails[] = $employee['email'];
                    }
                }
            }
            
            return response()->json([
                'success' => true,
                'totalHours' => round($totalHours, 2),
                'targetEmails' => $targetEmails,
                'foundEmails' => $foundEmails,
                'currentUserEmail' => $currentUserEmail
            ]);
            
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch data from Teamlogger API',
                'httpCode' => $httpCode
            ]);
        }
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}
}
