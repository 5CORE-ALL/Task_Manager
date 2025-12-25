<?php

namespace Workdo\Taskly\Http\Controllers\Api;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Workdo\Taskly\Entities\ClientProject;
use Workdo\Taskly\Entities\Stage;
use Workdo\Taskly\Entities\Task;
use Workdo\Taskly\Entities\Project;
use Workdo\Taskly\Entities\UserProject;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\User;
use App\Models\FlagRaise;
use Illuminate\Support\Facades\Log;
use Workdo\Taskly\Entities\ActivityLog;
use Workdo\Taskly\Entities\SubTask;
use Workdo\Taskly\Entities\Comment;
use Workdo\Taskly\Entities\TaskFile;

class TaskApiController extends Controller
{
    public function index(Request $request)
    {

        try {

            $validator = \Validator::make(
                $request->all(), [
                    'workspace_id' => 'required|exists:work_spaces,id',
                    'project_id' => 'required',
                    'status' => 'in:Ongoing,Finished,OnHold',
                ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return response()->json(['status'=>0, 'message'=>$messages->first()],403);
            }

            $objUser            = Auth::user();
            $currentWorkspace   = $request->workspace_id;
            $projectID          = $request->project_id;

            if (Auth::user()->hasRole('client')) {

                $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $projectID)->first();

            } else {

                $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $projectID)->first();
            }

            if ($project) {

                $tasks =  Task::where('project_id', '=', $project->id);

                if(isset($request->page) || isset($request->limit))
                {

                    $tasks->limit($request->limit ?? 10);
                    $tasks->offset($request->page ?? 1);

                }

                $tasks =  $tasks->get()->map(function($task){
                                            return [
                                                'id'                => $task->id,
                                                'title'             => $task->title,
                                                'priority'          => $task->priority,
                                                'description'       => $task->description,
                                                'start_date'        => $task->start_date,
                                                'due_date'          => $task->due_date,
                                                'project_id'        => $task->project_id,
                                                'milestone_id'      => (int) $task->milestone_id,
                                                'order'             => $task->order,
                                                'status'            => $task->status,
												'assign_to'         => $task->users()->map(function($user){
																			return [
																				'id'        => $user->id,
																				'name'      => $user->name,
																				'email'     => $user->email,
																				'avatar'    => check_file($user->avatar) ? get_file($user->avatar) : get_file('uploads/users-avatar/avatar.png'),
																			];
																		}),
                                            ];
                                });

                return response()->json(['status' => 1,'data'  => $tasks]);

            } else {
                return response()->json(['status'=>0,'message'=>'Not found!!!']);
            }

        } catch (\Exception $e) {
            return response()->json(['status'=>0,'message'=>'something went wrong!!!']);
        }
    }

    public function taskboard(Request $request)
    {
        try {

            $validator = \Validator::make(
                $request->all(), [
                    'workspace_id' => 'required',
                    'project_id' => 'required',
                ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return response()->json(['status'=>0, 'message'=>$messages->first()],403);
            }

            $objUser            = Auth::user();
            $currentWorkspace   = $request->workspace_id;
            $projectID          = $request->project_id;

            if (Auth::user()->hasRole('client')) {

                $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $projectID)->first();

            } else {

                $project = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $projectID)->first();
            }

            $stages = $statusClass = [];

            if ($project) {

                $stages = Stage::where('workspace_id', '=', $currentWorkspace)
                                    ->orderBy('order')
                                    ->get()
                                    ->map(function($stage){
                                        return (object) [
                                            'id' => $stage->id,
                                            'name' => $stage->name,
                                            'color' => $stage->color,
                                            'complete' => $stage->complete,
                                            'order' => $stage->order,
                                        ];
                                    });

                foreach ($stages as $key => $stage) {

                    $task          = Task::where('project_id', '=', $projectID);

                    if (!Auth::user()->hasRole('client') && !Auth::user()->hasRole('company')) {
                        if (isset($objUser) && $objUser) {
                            $task->whereRaw("find_in_set('" . $objUser->id . "',assign_to)");
                        }
                    }

                    $task->orderBy('order');

                    $stage->tasks = $task->where('status', '=', $stage->id)
                                                ->get()
                                                ->map(function($task) use ($stages , $key){
                                                    return [
                                                        'id'                => $task->id,
                                                        'title'             => $task->title,
                                                        'priority'          => $task->priority,
                                                        'description'       => $task->description,
                                                        'start_date'        => $task->start_date,
                                                        'due_date'          => $task->due_date,
                                                        'project_id'        => $task->project_id,
                                                        'milestone_id'      => (int) $task->milestone_id,
                                                        'order'             => $task->order,
                                                        'previous_stage'    => isset($stages[$key-1]) ? $stages[$key-1]->id : 0,
                                                        'current_stage'     => $stages[$key]->id,
                                                        'next_stage'        => isset($stages[$key+1]) ? $stages[$key+1]->id : 0,
                                                        'assign_to'         => $task->users()->map(function($user){
                                                                                     return [
                                                                                         'id' => $user->id,
                                                                                         'name' => $user->name,
                                                                                        'email' => $user->email,
                                                                                         'avatar' => check_file($user->avatar) ? get_file($user->avatar) : get_file('uploads/users-avatar/avatar.png'),
                                                                                     ];
                                                                                 }),
                                                    ];
                                                });
                }

                return response()->json([

                    'status' => 1,
                    'data'  => $stages
                ]);

            } else {
                return response()->json(['status'=>0,'message'=>'Not found!!!']);
            }

        } catch (\Exception $e) {
            return response()->json(['status'=>0,'message'=>'something went wrong!!!']);
        }
    }

    public function taskDetails(Request $request)
    {
        try{

            $validator = \Validator::make(
                $request->all(), [
                    'workspace_id'  => 'required',
                    'project_id'  => 'required',
                    'task_id'       => 'required',
                ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return response()->json(['status'=> 0, 'message'=>$messages->first()],403);
            }

            $objUser            = Auth::user();
            $currentWorkspace   = $request->workspace_id;
            $taskID             = $request->task_id;
            $projectID             = $request->project_id;

            $task = Task::where('workspace',$currentWorkspace)->where('project_id',$projectID)->where('id',$taskID)->first();

            $taskDetails = [
                'id'                => $task->id,
                'title'             => $task->title,
                'priority'          => $task->priority,
                'description'       => $task->description,
                'start_date'        => $task->start_date,
                'due_date'          => $task->due_date,
                'project_id'        => $task->project_id,
                'milestone_id'      => (int) $task->milestone_id,
                'order'             => $task->order,
                'status'            => $task->status,
                'assign_to'         => $task->users()->map(function($user){
                                            return [
                                                'id'        => $user->id,
                                                'name'      => $user->name,
                                                'email'     => $user->email,
                                                'avatar'    => check_file($user->avatar) ? get_file($user->avatar) : get_file('uploads/users-avatar/avatar.png'),
                                            ];
                                        }),

            ];

            return response()->json([
                'status' => 1,
                'data'  => $taskDetails,

            ]);

        } catch (\Exception $e) {
            return response()->json(['status'=>0,'message'=>'something went wrong!!!']);
        }
    }

    public function taskCreateAndUpdate(Request $request)
    {

		$objUser            = Auth::user();
		$projectID          = $request->project_id;
        if($request->task_id){

            $validator = \Validator::make(
                $request->all(), [
                    'workspace_id'  => 'required',
                    'project_id'    => 'required',
                    'title'         => 'required',
                    'priority'      => 'required|in:Low,Medium,High',
                    'start_date'    => 'date_format:Y-m-d',
                    'due_date'      => 'date_format:Y-m-d',
                    'assign_to'     => 'required',
                    'task_id'       => 'required',
                ]
            );

            if($validator->fails()) {
                $messages = $validator->getMessageBag();
                return response()->json(['status' => 0, 'message' => $messages->first()], 403);
            }
            $objUser            = Auth::user();
            $projectID          = $request->project_id;
            $taskID             = $request->task_id;
            $currentWorkspace   = $request->workspace_id;

			if ($objUser->hasRole('client')) {
                $project = Project::where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $projectID)->first();
            } else {
                $project = Project::select('projects.*')->join('user_projects', 'user_projects.project_id', '=', 'projects.id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $projectID)->first();
            }

            if($request->assign_to){
                $ids = $request->assign_to;
                foreach ($ids as $id) {
                    if (!UserProject::where('project_id',$projectID)->where('user_id', $id)->exists()) {
                        return response()->json(['status' => 0, 'message' => 'User is not assigned to the project ' . $project->name . '. '] , 403);
                    }
                }
            }



            if ($project) {
                $post              = $request->all();
				$post['milestone_id']   = !empty($request->milestone_id) ? $request->milestone_id : 0;
                $post['assign_to'] = implode(",", $request->assign_to);
                $task              = Task::where('workspace',$currentWorkspace)->where('project_id',$projectID)->where('id',$taskID)->first();
                $task->update($post);

                return response()->json(['status' => 1, 'message' => 'Task Updated Successfully.'] , 200);
            } else {
                return response()->json(['status' => 0, 'message' => 'Project not found!'] , 403);
            }

        }else{

            $validator = \Validator::make(
                $request->all(), [
                    'workspace_id'  => 'required',
                    'project_id'    => 'required',
                    'title'         => 'required',
                    'priority'      => 'required|in:Low,Medium,High',
                    'start_date'    => 'date_format:Y-m-d',
                    'due_date'      => 'date_format:Y-m-d',
                    'assign_to'     => 'required',
                ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return response()->json(['status' => 0, 'message' => $messages->first()] , 403);
            }

			 $objUser            = Auth::user();
            $projectID          = $request->project_id;

            $currentWorkspace   = $request->workspace_id;

			if ($objUser->hasRole('client')) {
                $project = Project::where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $projectID)->first();
            } else {
                $project = Project::select('projects.*')->join('user_projects', 'user_projects.project_id', '=', 'projects.id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace)->where('projects.id', '=', $projectID)->first();
            }

            if($request->assign_to){
                $ids = $request->assign_to;
                foreach ($ids as $id) {
                    if (!UserProject::where('project_id',$projectID)->where('user_id', $id)->exists()) {
                        return response()->json(['status' => 0, 'message' => 'User is not assigned to the project ' . $project->name . '. '] , 403);
                    }
                }
            }

            $objUser            = Auth::user();
            $projectID          = $request->project_id;
            $currentWorkspace   = $request->workspace_id;

            $post = $request->all();

            $stage = Stage::where('workspace_id', '=', $currentWorkspace)->orderBy('order')->first();

            if ($stage) {

                $post['milestone_id']   = !empty($request->milestone_id) ? $request->milestone_id : 0;
                $post['status']         = $stage->id;
                $post['assign_to']      = implode(",", $request->assign_to) ;
                $post['workspace']      = $currentWorkspace;
                $task                   = Task::create($post);

                ActivityLog::create(
                    [
                        'user_id'       => $objUser->id,
                        'user_type'     => get_class($objUser),
                        'project_id'    => $projectID,
                        'log_type'      => 'Create Task',
                        'remark'        => json_encode(['title' => $task->title]),
                    ]
                );

                return response()->json(['status' => 1  , 'message' => 'Task Created Successfully!'], 200);
            } else {
                return response()->json(['status' => 0 , 'message' => 'Please add stages first.'], 200);
            }
        }
    }

    public function taskStageUpdate(Request $request)
    {
        $validator = \Validator::make(
            $request->all(), [
                'workspace_id'  => 'required',
                'project_id'    => 'required',
                'task_id'       => 'required',
                'new_status'    => 'required',
                // 'old_status'    => 'required',
            ]
        );

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();
            return response()->json(['status' => 0, 'message' => $messages->first()] , 403);
        }

        try{

            $objUser            = Auth::user();
            $currentWorkspace   = $request->workspace_id;
            $projectID          = $request->project_id;
            $taskID             = $request->task_id;

            $task         = Task::where('workspace',$currentWorkspace)->where('project_id',$projectID)->where('id',$taskID)->first();

            if ($request->new_status != $task->status) {

                $new_status   = Stage::where('workspace_id',$currentWorkspace)->where('created_by',creatorId())->where('id',$request->new_status)->first();
                $old_status   = Stage::where('workspace_id',$currentWorkspace)->where('created_by',creatorId())->where('id',$task->status)->first();
                $task->status = $request->new_status;
                $task->save();

                ActivityLog::create(
                    [
                        'user_id' => $objUser->id,
                        'user_type' => get_class($objUser),
                        'project_id' => $projectID,
                        'log_type' => 'Move',
                        'remark' => json_encode(
                            [
                                'title'      => $task->title,
                                'old_status' => $old_status->name,
                                'new_status' => $new_status->name,
                            ]
                        ),
                    ]
                );
            }

            return response()->json(['status' => 1 ,'message' => 'Task stage update successfully.']);

        } catch (\Exception $e) {
            return response()->json(['status' => 0 ,'message' => 'something went wrong!!!']);
        }
    }

	public function taskDelete(Request $request)
    {

        $objUser = Auth::user();
        $task              = Task::where('workspace', '=', $request->workspace_id)->where('project_id',$request->project_id)->where('id',$request->task_id)->first();
        if(!$task){
            return response()->json(['status'=>0,'message'=>'Task Not Found!']);
        }
        
        // Check authorization for "Done" tasks created under flag raise management
        if (strtolower($task->status) === 'done') {
            if ($this->isTaskFromFlagRaise($task)) {
                // Only users with full access to flag raise management can delete
                if (!$objUser || !in_array($objUser->email, ['president@5core.com', 'tech-support@5core.com'])) {
                    return response()->json([
                        'status' => 0,
                        'message' => 'Permission denied. Only users with full access to flag raise management can delete "Done" tasks created under flag raise management.'
                    ], 403);
                }
            }
        }
        
        Comment::where('task_id', '=', $task->id)->delete();
        SubTask::where('task_id', '=', $task->id)->delete();
        $TaskFiles = TaskFile::where('task_id', '=', $task->id)->get();

        foreach($TaskFiles as $TaskFile){
            delete_file($TaskFile->file);
            $TaskFile->delete();
        }
        $task->delete();
        return response()->json(['status'=>1, 'message' => 'Task Deleted Successfully!']);

    }

    /**
     * Check if a task was created under flag raise management
     * by checking if there's a matching flag in the flag_raises table
     */
    private function isTaskFromFlagRaise($task)
    {
        try {
            // Get assignor user ID
            $assignorEmails = explode(',', $task->assignor ?? '');
            $assignorEmail = trim($assignorEmails[0] ?? '');
            $assignorUser = null;
            if (!empty($assignorEmail)) {
                $assignorUser = User::where('email', $assignorEmail)->first();
            }
            $givenBy = $assignorUser ? $assignorUser->id : null;
            
            // Get assignee user IDs
            $assigneeEmails = explode(',', $task->assign_to ?? '');
            $assigneeUserIds = [];
            foreach ($assigneeEmails as $email) {
                $assigneeUser = User::where('email', trim($email))->first();
                if ($assigneeUser) {
                    $assigneeUserIds[] = $assigneeUser->id;
                }
            }
            
            // Check if there's a flag matching this task
            // Flag description typically starts with "Task: {title}"
            $flagDescriptionPattern = "Task: {$task->title}%";
            
            $matchingFlag = FlagRaise::where(function($query) use ($givenBy, $assigneeUserIds, $flagDescriptionPattern) {
                if ($givenBy) {
                    $query->where('given_by', $givenBy);
                }
                if (!empty($assigneeUserIds)) {
                    $query->whereIn('team_member_id', $assigneeUserIds);
                }
                $query->where('description', 'like', $flagDescriptionPattern);
            })->first();
            
            return $matchingFlag !== null;
        } catch (\Exception $e) {
            Log::error("Error checking if task is from flag raise: " . $e->getMessage());
            return false;
        }
    }

}
