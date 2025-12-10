<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Workdo\Taskly\Entities\Task;
use Workdo\Taskly\Entities\TaskFile;
use Workdo\Taskly\Entities\Stage;
use App\Models\User;
use App\Traits\SendSmsTraits;
use App\Traits\LogsTaskActivity;
use DB;

class TaskApiController extends Controller
{
    use SendSmsTraits;
    use LogsTaskActivity;

    /**
     * Create a new task via API
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createTask(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                 'title' => 'required|string|max:255',
                'assign_to' => 'required|array',
                'assign_to.*' => 'required|string|email',
                'assignor' => 'required|array',
                'assignor.*' => 'required|string|email', 
                'priority' => 'required|in:normal,urgent,Take your time',
                'start_date' => 'required|date',
                'due_date' => 'required|date|after_or_equal:start_date',
                'group' => 'required|string|max:15',
                'stage_id' => 'nullable|string',
                'description' => 'nullable|string',
                'eta_time' => 'required|integer|min:1',
                'link1' => 'nullable|string|url',
                'link2' => 'nullable|string|url',
                'link3' => 'nullable|string|url',
                'link4' => 'nullable|string|url',
                'link5' => 'nullable|string|url',
                'link6' => 'nullable|string|url',
                'link7' => 'nullable|string|url',
                'link8' => 'nullable|string',
                'link9' => 'nullable|string|url',
                'split_tasks' => 'nullable|boolean',
                'workspace_id' => 'nullable|integer',
                'file' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:10240', // 10MB max
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get workspace - always use workspace_id as 6
            $currentWorkspace = 6;
            
            // Get stage
            if (!empty($request->stage_id)) {
                $stage = Stage::where('workspace_id', '=', $currentWorkspace)
                    ->where('name', $request->stage_id)
                    ->orderBy('order')
                    ->first();
            } else {
                $stage = Stage::where('workspace_id', '=', $currentWorkspace)
                    ->orderBy('order')
                    ->first();
            }

            if (!$stage) {
                return response()->json([
                    'success' => false,
                    'message' => 'No stages found. Please add stages first.'
                ], 400);
            }

            // Handle file upload
            $upload = null;
            $fileName = null;
            if ($request->hasFile('file')) {
                $fileName = time() . "_" . $request->file->getClientOriginalName();
                $upload = upload_file($request, 'file', $fileName, 'tasks', []);
            }

            $post = $request->all();
            // $split_tasks = $request->filled('split_tasks') ? 1 : 0;
            $split_tasks = $request->boolean('split_tasks') ? 1 : 0;


            
            // Handle All Members selection
            if (in_array('all_members', $post['assign_to'])) {
                $allUsers = User::where('workspace_id', $currentWorkspace)
                    ->whereNotIn('email', ['company@example.com', 'president@5core.com', 'superadmin@example.com'])
                    ->pluck('email')
                    ->toArray();
                $post['assign_to'] = $allUsers;
            }
            
            // Handle All Managers selection
            if (in_array('all_managers', $post['assign_to'])) {
                $managerEmails = ['tech-support@5core.com', 'support@5core.com', 'mgr-advertisement@5core.com', 'mgr-content@5core.com', 'hr@5core.com','inventory@5core.com'];
                $post['assign_to'] = $managerEmails;
            }

            $createdTasks = [];

            if ($split_tasks) {
                // Create individual tasks for each assignee
                foreach ($post['assign_to'] as $assign_to) {
                    $taskData = [
                        'title' => $post['title'],
                        'description' => $post['description'] ?? '',
                        'status' => $stage->name,
                        'assign_to' => $assign_to,
                        'assignor' => implode(',', $post['assignor']),
                        'priority' => $post['priority'],
                        'start_date' => $post['start_date'],
                        'due_date' => $post['due_date'],
                        'group' => $post['group'],
                        'eta_time' => $post['eta_time'],
                        'link1' => $post['link1'] ?? '',
                        'link2' => $post['link2'] ?? '',
                        'link3' => $post['link3'] ?? '',
                        'link4' => $post['link4'] ?? '',
                        'link5' => $post['link5'] ?? '',
                        'link6' => $post['link6'] ?? '',
                        'link7' => $post['link7'] ?? '',
                        'link8' => $post['link8'] ?? '',
                        'link9' => $post['link9'] ?? '',
                        'split_tasks' => $split_tasks,
                        'workspace' => $currentWorkspace,
                        'created_by' => 0, // Since API might not have authenticated user
                        'is_data_from' => 1,
                    ];

                    $task = Task::create($taskData);
                    $createdTasks[] = $task;

                    // Handle file attachment for each task
                    if ($upload) {
                        $this->createTaskFile($task->id, $upload, $request);
                    }

                    // Log task creation
                    $this->logTaskCreation($task->title, 'Task created via API and assigned to: ' . $assign_to);
                    
                    // Send SMS if method exists
                    if (method_exists($this, 'sendSms')) {
                        // \Log::info('sendSms triggered for task: ' . $task->assign_to);

                        $this->sendSms($task);
                    }
                }
            } else {
                // Create single task with multiple assignees
                $taskData = [
                    'title' => $post['title'],
                    'description' => $post['description'] ?? '',
                    'status' => $stage->name,
                    'assign_to' => implode(',', $post['assign_to']),
                    'assignor' => implode(',', $post['assignor']),
                    'priority' => $post['priority'],
                    'start_date' => $post['start_date'],
                    'due_date' => $post['due_date'],
                    'group' => $post['group'],
                    'eta_time' => $post['eta_time'],
                    'link1' => $post['link1'] ?? '',
                    'link2' => $post['link2'] ?? '',
                    'link3' => $post['link3'] ?? '',
                    'link4' => $post['link4'] ?? '',
                    'link5' => $post['link5'] ?? '',
                    'link6' => $post['link6'] ?? '',
                    'link7' => $post['link7'] ?? '',
                    'link8' => $post['link8'] ?? '',
                    'link9' => $post['link9'] ?? '',
                    'split_tasks' => $split_tasks,
                    'workspace' => $currentWorkspace,
                    'created_by' => 0, // Since API might not have authenticated user
                    'is_data_from' => 1,
                ];

                $task = Task::create($taskData);
                $createdTasks[] = $task;

                // Handle file attachment
                if ($upload) {
                    $this->createTaskFile($task->id, $upload, $request);
                }

                // Log task creation
                $this->logTaskCreation($task->title, 'Task created via API and assigned to: ' . $taskData['assign_to']);
                
                // Send SMS if method exists
                if (method_exists($this, 'sendSms')) {
                    // \Log::info('sendSms triggered for task: ' . $task->assign_to);
                    $this->sendSms($task);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Task created successfully',
                'data' => [
                    'tasks_created' => count($createdTasks),
                    'tasks' => $createdTasks
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the task',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create task file attachment
     * 
     * @param int $taskId
     * @param array $upload
     * @param Request $request
     * @return void
     */
    private function createTaskFile($taskId, $upload, $request)
    {
        $postFile = [
            'task_id' => $taskId,
            'file' => $upload['url'],
            'name' => $request->file->getClientOriginalName(),
            'extension' => $request->file->getClientOriginalExtension(),
            'file_size' => $request->file->getSize(),
            'created_by' => 0, // API user
            'user_type' => 'API',
        ];
        
        TaskFile::create($postFile);
    }

    /**
     * Get all tasks (optional - for testing)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTasks(Request $request)
    {
        try {
            $workspace_id = 6; // Always use workspace_id as 6
            
            $tasks = Task::where('workspace', $workspace_id)
                ->orderBy('created_at', 'desc')
                ->paginate(50);

            return response()->json([
                'success' => true,
                'data' => $tasks
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching tasks',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // save inventory data direct from frontend section
    public function save_inventory(Request $request)
    {
        $save_inv = DB::table('inventories')->where('inv_route', $request->inv_route)->first();

    if (!$save_inv) {
        // ✅ Create new record
        DB::table('inventories')->insert([
            'inv_route' => $request->inv_route,
            'inv_value' => $request->inv_value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    } else {
        // ✅ Update existing record
        DB::table('inventories')
            ->where('inv_route', $request->inv_route)
            ->update([
                'inv_value' => $request->inv_value,
                'updated_at' => now(),
            ]);
    }

    return response()->json([
        'status'  => 200,
        'message' => 'Inventory data saved successfully',
    ]);
        
    }
}
