<?php

namespace Workdo\Taskly\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Workdo\Taskly\Entities\ClientProject;
use Workdo\Taskly\Entities\Stage;
use Workdo\Taskly\Entities\Task;
use Workdo\Taskly\Entities\UserProject;
use Workdo\Taskly\DataTables\ProjectTaskDatatable;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use \App\Models\MyTeam;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
	public function __construct()
	{
		if(module_is_active('GoogleAuthentication'))
		{
			$this->middleware('2fa');
		}
	}

  public function index(ProjectTaskDatatable $dataTable)
  {
		if(Auth::user()->isAbleTo('taskly dashboard manage'))
		{
		  if(!empty($_GET['id']))
		  {
            $userObj = $_GET['id'];
		    $user_data = User::where('id',$userObj)->first();
		    $email = $user_data->email;
		    $user_id = $user_data->id;
		  }
		  else{
               $userObj = Auth::user();
		       $email = $userObj->email;
		       $user_id = $userObj->id;
		  }
		  
		  $inventory_total_avgPft = DB::table('inventories')->where('inv_route','pricing-masters.pricing_masters')->first();
		  $inventory_total_avgRoi = DB::table('inventories')->where('inv_route','pricing-masters.pricing_masters.roiHeader')->first();
		  
		  $inventory_total_inv = DB::table('inventories')->where('inv_route','pricing-masters.pricing_masters.invValueHeader')->first();
		  $inventory_total_lpvalue = DB::table('inventories')->where('inv_route','pricing-masters.pricing_masters.lpValueHeader')->first();

		  $inventory_Stock_missing_listing = DB::table('inventories')->where('inv_route','Stock_missing_listing')->first();
		  
		  $total_avgPft = $inventory_total_avgPft->inv_value;
		  $total_avgRoi = $inventory_total_avgRoi->inv_value;
		  
		  $total_inv = $inventory_total_inv->inv_value;
		  $total_lpv = $inventory_total_lpvalue->inv_value;

		  $total_missing_list = $inventory_Stock_missing_listing->inv_value;

		  $currentMonthStart = Carbon::now()->startOfMonth();
          $currentMonthEnd = Carbon::now()->endOfMonth();

	$totalETAmin = collect(
         Task::where('status', 'Done')
             ->where('is_missed', 0)
             ->whereNotNull('deleted_at')
             ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
             ->where(function ($query) use ($email) {
                 $query->where('assignor', 'like', "%$email%")
                       ->Where('assign_to', 'like', "%$email%");
             })
             ->where('eta_time', '>', 0)
             ->pluck('eta_time')
             ->toArray()
             )->sum();

    $totalATCMin = collect(
        Task::where('status', 'Done')
            ->where('is_missed', 0)
            ->whereNotNull('deleted_at')
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->where(function ($query) use ($email) {
                $query->where('assignor', 'like', "%$email%")
                      ->Where('assign_to', 'like', "%$email%");
            })
            ->where('etc_done', '>', 0)
            ->pluck('etc_done')
            ->toArray()
            )->sum();
                                
            $totalETAmin = number_format($totalETAmin/60,2);
            $totalATCMin = number_format($totalATCMin/60,2);
			
			$currentWorkspace = getActiveWorkSpace();

			 $doneStage = Stage::where('workspace_id', '=', $currentWorkspace)
				->where('created_by', creatorId())
				->where('name', 'Done')
				->first();

				// inventory total l30 sales api call
                // $response = Http::get('https://inventory.5coremanagement.com/api/l30-total-sales');
                // $data = $response->json();
                // $total_l30_sales = $data['data'];
                // $total_l30_sales = 0;
                try {
                    $response = Http::timeout(5)->get('https://inventory.5coremanagement.com/api/l30-total-sales');
                
                    if ($response->successful()) {
                        $data = $response->json();
                
                        // Check if key exists
                        $total_l30_sales = $data['data'] ?? 0;
                    } else {
                        // API returned 4xx or 5xx
                        $total_l30_sales = 0;
                    }
                
                } catch (\Exception $e) {
                    // API not reachable, timeout, network error, invalid JSON, etc.
                    $total_l30_sales = 0;
                }

				
			    $myTask = Task::select('tasks.*', 'assignee_user.name as assignee_name')
                          // join to get assignee's user row
                          ->leftJoin('users as assignee_user', 'assignee_user.email', '=', 'tasks.assign_to')
                          ->whereNull('tasks.deleted_at')
                          ->where('tasks.workspace', $currentWorkspace)
                          ->where(function($q) use ($email) {
                              $q->where('tasks.assign_to', $email);
                          })
                          ->get();

	            //  $assignedTask = Task::select('tasks.*', 'assignor_user.name as assignor_name')
                //  // join to get assignor's user row
                //  ->leftJoin('users as assignor_user', 'assignor_user.email', '=', 'tasks.assignor')
                //  ->whereNull('tasks.deleted_at')
                //  ->where('tasks.workspace', $currentWorkspace)
                //  ->where(function($q) use ($email) {
                //      $q->where('tasks.assignor', $email);
                //  })
                //  ->get();

                 $all_users = user::all();
		         // get my team members
	             $myTeam = DB::table('my_teams')
                 ->join('users', 'users.id', '=', 'my_teams.team_id')
                 ->where('my_teams.tl_id', $user_id)
                 ->get();

		         $myTeam_Count = DB::table('my_teams')
		         ->where('my_teams.tl_id', $user_id)
		         ->count();

			    // Get employees for dropdown
			    if(Auth::user()->hasRole('company')) {
			    	$employees = User::where('created_by', '=', creatorId())->emp()->get();
			    } else {
			    	$employees = collect([Auth::user()]);
			    }

				// total task count
				// $competeTask = Task::where('status','Done')->where('is_missed',0)->where('status',"!=","")->where('workspace', getActiveWorkSpace())->where(function ($query) use ($email) {
                //                 $query->Where('assign_to', 'like', "%$email%");
                //             })->where('deleted_at',NULL)->count();
				    $competeTask = Task::where('status', 'Done')
                                   ->where('is_missed', 0)
                                   ->where('workspace', getActiveWorkSpace())
                                   ->where('assign_to', 'like', "%$email%")
                                   ->whereNull('deleted_at')
                                   ->count();

					$totalTask = Task::where('is_missed', 0)
                                   ->where('workspace', getActiveWorkSpace())
                                   ->where('assign_to', 'like', "%$email%")
                                   ->whereNull('deleted_at')
                                   ->count();

					$assignedTask = Task::where('is_missed', 0)
                                    ->where('workspace', getActiveWorkSpace())
                                    ->where('assignor', 'like', "%$email%")
                                    ->whereNull('deleted_at')
                                    ->count();

                    $pendingTask = Task::whereNotIn('status',['Done'])->where('is_missed',0)->where('status',"!=","")->where('workspace', getActiveWorkSpace())->where(function ($query) use ($email) {
                                   $query->where('assignor', 'like', "%$email%")
                                        ->orWhere('assign_to', 'like', "%$email%");
                                    })->where('deleted_at',NULL)->count();

                    $overdueTask = Task::whereNotIn('status', [''])->where('is_missed',0)->where('status',"!=","")->where('workspace', getActiveWorkSpace())
                                 ->where('due_date', '<', now())
                                 ->where(function ($query) use ($email) {
                                     $query->where('assignor', 'like', "%$email%")
                                           ->orWhere('assign_to', 'like', "%$email%");
                                 })->where('deleted_at',NULL)->count();

					$totalPendingTask = $pendingTask + $overdueTask;

			return $dataTable->render('taskly::index', compact('currentWorkspace', 'totalETAmin', 'total_avgPft', 
			'total_avgRoi', 'total_inv', 'total_lpv', 'total_missing_list', 'totalATCMin', 'myTask',
			 'assignedTask', 'all_users','myTeam','myTeam_Count','total_l30_sales',
			 'totalTask', 'competeTask', 'pendingTask', 'overdueTask', 'totalPendingTask'));
		    }
		else { 
			 return redirect()->back()->with('error', __('Permission Denied.'));
		}
	}

	// get my tasks for summary report
	public function getMyTasks(Request $request)
{
    $userObj = null;

    // Determine user (self or someone else)
    if (!empty($request->id)) {
        $userObj = User::find($request->id);
    } else {
        $userObj = Auth::user();
    }

    if (!$userObj) {
        return response()->json([
            'status' => 404,
            'message' => 'User not found'
        ], 404);
    }

    $email = $userObj->email;
    $currentWorkspace = getActiveWorkSpace();

    // Permissions (same as DataTable)
   $loggedIn = Auth::user();
$canShow   = 'true';
$canEdit   = $loggedIn->can('task edit');
$canDelete = $loggedIn->can('task delete');

	// dd(Auth::user()->allPermissions()->pluck('name'));
// 	dd([
//     'g1' => Auth::guard()->name, // default guard
//     'g2' => Auth::guard('web')->check(),
//     'user' => Auth::user()->email,
//     'perms' => Auth::user()->allPermissions()->pluck('name'),
// ]);



    // type = 1 → my tasks
    // type = 2 → assigned tasks
    $query = Task::select(
                'tasks.*',
                'assignor_user.name as assignor_name',
                'assignee_user.name as assignee_name'
            )
            ->leftJoin('users as assignor_user', 'assignor_user.email', '=', 'tasks.assignor')
            ->leftJoin('users as assignee_user', 'assignee_user.email', '=', 'tasks.assign_to')
            ->whereNull('tasks.deleted_at')
            ->where('tasks.workspace', $currentWorkspace);

    if ($request->type == 1) {
        // MY TASKS → assign_to = me (can be multiple emails)
        $query->where(function ($q) use ($email) {
            $q->where('tasks.assign_to', 'like', "%$email%");
        });
    }

    if ($request->type == 2) {
        // ASSIGNED BY ME → assignor = me
        $query->where(function ($q) use ($email) {
            $q->where('tasks.assignor', 'like', "%$email%");
        });
    }

    $tasks = $query->get();

    // Prepare clean array
    $formatted = $tasks->map(function ($t) {
        return [
            'id'            => $t->id,
            'group'         => $t->group,
            'title'         => $t->title,
            'assignor'      => $t->assignor,
            'assign_to'     => $t->assign_to,
            'assignor_name' => $t->assignor_name,
            'assignee_name' => $t->assignee_name,
            'start_date'    => $t->start_date,
            'due_date'      => $t->due_date,
            'eta_time'      => $t->eta_time,
            'etc_done'      => $t->etc_done,
            'status'        => $t->status,
            'priority'      => $t->priority,
        ];
    });

    return response()->json([
        'status'          => 200,
        'totalTask'       => $formatted,
        'canShow'         => $canShow,
        'canEdit'         => $canEdit,
        'canDelete'       => $canDelete,
        'currentUserEmail'=> auth()->user()->email,
    ]);
}

	// get tasks graph data for my tasks
	public function getTasksGraph(Request $request)
	{
		if(!empty($_GET['id']))
		{
		 $userObj = User::find($_GET['id']);
		}
		else{
		$userObj = Auth::user();
		}
    $currentWorkspace = getActiveWorkSpace();

    // keep your Done stage lookup (you had it)
    $doneStage = Stage::where('workspace_id', '=', $currentWorkspace)
        ->where('created_by', creatorId())
        ->where('name', 'Done')
        ->first();

    $email = $userObj->email;
	 // number of weeks to show (default 12)
    $weeks = (int) $request->input('weeks', 12);
    if ($weeks < 1) $weeks = 12;

    $now = Carbon::now();
    $startOfThisWeek = $now->copy()->startOfWeek(); // Monday as start
    $earliestDate = $startOfThisWeek->copy()->subWeeks($weeks - 1)->startOfDay();    

    $rows = DB::table('tasks')
        ->select(
            DB::raw("YEARWEEK(created_at, 1) AS period_key"),
            DB::raw("SUM(CASE WHEN deleted_at IS NOT NULL THEN 1 ELSE 0 END) AS completed_count"),
            DB::raw("SUM(CASE WHEN deleted_at IS NULL AND due_date >= NOW() THEN 1 ELSE 0 END) AS pending_count"),
            DB::raw("SUM(CASE WHEN deleted_at IS NULL AND due_date < NOW() THEN 1 ELSE 0 END) AS overdue_count")
        )
        ->where('workspace', $currentWorkspace)
        ->where(function ($q) use ($email) {
            $q->where('assignor', $email)
              ->orWhere('assign_to', $email);
        })
        ->whereDate('created_at', '>=', $earliestDate->toDateString())
        ->groupBy('period_key')
        ->orderBy('period_key')
        ->get()
        ->keyBy(function ($item) {
            return (string) $item->period_key;
        });

    // Prepare output arrays (chronological)
    $labels = [];
    $completedCounts = [];
    $pendingCounts = [];
    $overdueCounts = [];

    for ($i = $weeks - 1; $i >= 0; $i--) {
        $weekStart = $startOfThisWeek->copy()->subWeeks($i);
        $year = (int) $weekStart->format('o');      // ISO week-year
        $week = (int) $weekStart->isoWeek();        // week number
        $key = sprintf('%04d%02d', $year, $week);   // YEARWEEK(...,1) format

        // friendly label like 'W42 (2025-10-13)'
        $labels[] = 'W' . $week . ' (' . $weekStart->format('Y-m-d') . ')';

        if (isset($rows[$key])) {
            $r = $rows[$key];
            $completedCounts[] = (int) $r->completed_count;
            $pendingCounts[]   = (int) $r->pending_count;
            $overdueCounts[]   = (int) $r->overdue_count;
        } else {
            $completedCounts[] = 0;
            $pendingCounts[]   = 0;
            $overdueCounts[]   = 0; 
        }
    }	

		$totalTask = Task::select('tasks.*', 'assignee_user.name as assignee_name')
    // join to get assignee's user row
    ->leftJoin('users as assignee_user', 'assignee_user.email', '=', 'tasks.assign_to')
    // ->whereNull('tasks.deleted_at')
    ->where('tasks.workspace', $currentWorkspace)
    ->where(function($q) use ($email) {
        $q->where('tasks.assign_to', $email);
    })
    ->get();

	

    // keep the same single-values counts if you still need them (matching your original logic)
    $completed_tasksCount = $totalTask->whereNotNull('deleted_at')->count();
    $pendingTaskCount     = $totalTask->where('due_date', '>=', now())->whereNull('deleted_at')->count();
    $overdueTaskCount     = $totalTask->where('due_date', '<', now())->whereNull('deleted_at')->count();
    // return a view or JSON depending on how you want to consume it
    // Example: return JSON for chart:
    return response()->json([
        'labels' => $labels,
        'completed' => $completedCounts,
        'pending'   => $pendingCounts,
        'overdue'   => $overdueCounts,
        // also include overall counts if needed by frontend
        'summary' => [
            'completed_tasksCount' => $completed_tasksCount,
            'pendingTaskCount' => $pendingTaskCount,
            'overdueTaskCount' => $overdueTaskCount,
        ],
    ]);

    
	}

	// add my team
public function myTeamAdd(Request $request)
{
    $team_ids = $request->input('team_id'); // This is now an array of selected user IDs

    if (empty($team_ids) || !is_array($team_ids)) {
        return response()->json([
            'success' => false,
            'message' => 'Please select at least one team member.'
        ]);
    }

    // Get TL (Team Leader)
    if (!empty($request->input('tl_id'))) {
        $userObj = User::find($request->input('tl_id'));
    } else {
        $userObj = Auth::user();
    }

    // Check permissions
    if (
        Auth::user()->hasRole('company') ||
        Auth::user()->hasRole('super admin') ||
        Auth::user()->hasRole('MANAGER') ||
        Auth::user()->hasRole('Manager All Access')
    ) {
        foreach ($team_ids as $id) {
            // Avoid duplicates
            $exists = \App\Models\MyTeam::where('tl_id', $userObj->id)
                ->where('team_id', $id)
                ->exists();

            if (!$exists) {
                $myTeam = new \App\Models\MyTeam();
                $myTeam->tl_id = $userObj->id;
                $myTeam->team_id = $id;
                $myTeam->status = 0;
                $myTeam->save();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Team members added successfully.'
        ]);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'You don’t have the required rights. Please contact the admin for access.'
        ]);
    }
}



	public function getEmployeeTasks(Request $request)
	{
		$employeeId = $request->get('employee_id');
		$currentWorkspace = getActiveWorkSpace();

		if (!$employeeId) {
			return response()->json(['error' => 'Employee ID is required'], 400);
		}

		// Security check: Users can only view their own data unless they are company role
		if (!Auth::user()->hasRole('company') && Auth::user()->id != $employeeId) {
			return response()->json(['error' => 'Permission denied'], 403);
		}

		try {
			$user = User::find($employeeId);
			if (!$user) {
				return response()->json(['error' => 'User not found'], 404);
			}

			$taskQuery = Task::select('tasks.*', 'stages.name as stage_name', 'assignor_users.name as assigner_name', 'tasks.eta_time', 'tasks.etc_done')
				->join('stages', 'stages.name', '=', 'tasks.status')
				->where('deleted_at', NULL)
				->leftJoin('users as assignor_users', 'assignor_users.email', '=', 'tasks.assignor')
				->orderByRaw("CASE WHEN tasks.status = 'urgent' THEN 0 ELSE 1 END")
				->orderBy('tasks.due_date', 'asc')
				->where('tasks.workspace', $currentWorkspace)
				->groupBy('tasks.id');

			// Filtering for selected employee
			$taskQuery->where(function ($query) use ($user) {
				$query->whereRaw("FIND_IN_SET(?, assign_to)", [$user->email])
					->orWhere('assignor', $user->email);
			});

			$allTasks = $taskQuery->get();
			$totalTaskCount = $allTasks->count();

			$doneStage = Stage::where('workspace_id', '=', $currentWorkspace)
				->where('created_by', creatorId())
				->where('name', 'Done')
				->first();

			$completeTaskCount = 0;
			if (!empty($doneStage)) {
				$completeTaskCount = $allTasks->where('stage_name', 'Done')->count();
			}

			$pendingTaskCount = $totalTaskCount - $completeTaskCount;
			$overdueTaskCount = $allTasks->filter(function($task) {
				if (!$task->due_date || $task->due_date === 'Invalid date' || trim($task->due_date) === '') {
					return false;
				}
				try {
					return \Carbon\Carbon::parse($task->due_date)->isPast() && strtolower($task->stage_name) !== 'done';
				} catch (\Exception $e) {
					return false; // If date parsing fails, don't count as overdue
				}
			})->count();

			// ETC/ATC calculation (convert minutes to hours)
			$etcMinutes = 0;
			$atcMinutes = 0;
			foreach ($allTasks as $task) {
				if (isset($task->eta_time) && $task->eta_time > 0) {
					$etcMinutes += intval($task->eta_time);
				}
				if (isset($task->etc_done) && $task->etc_done > 0) {
					$atcMinutes += intval($task->etc_done);
				}
			}
			$etcHours = round($etcMinutes / 60, 1);
			$atcHours = round($atcMinutes / 60, 1);

			$taskStats = [
				'total' => $totalTaskCount,
				'pending' => $pendingTaskCount,
				'completed' => $completeTaskCount,
				'overdue' => $overdueTaskCount,
				'etc_hours' => $etcHours,
				'atc_hours' => $atcHours
			];

			$formattedTasks = $allTasks->take(20)->map(function($task) {
				$isCompleted = strtolower($task->stage_name) === 'done';
				
				// Safe date parsing with validation
				$dueDate = null;
				if ($task->due_date && $task->due_date !== 'Invalid date' && trim($task->due_date) !== '') {
					try {
						$dueDate = \Carbon\Carbon::parse($task->due_date);
					} catch (\Exception $e) {
						$dueDate = null; // If parsing fails, set to null
					}
				}
				
				$isOverdue = $dueDate && $dueDate->isPast() && !$isCompleted;
				return [
					'id' => $task->id,
					'title' => $task->title ?: 'Untitled Task',
					'status' => $task->stage_name ?: 'No Status',
					'priority' => $task->priority ?: 'Normal',
					'due_date' => $task->due_date,
					'due_date_formatted' => $dueDate ? $dueDate->format('M d, Y') : 'No due date',
					'is_overdue' => $isOverdue,
					'progress' => $isCompleted ? 100 : ($task->stage_name === 'To Do' ? 0 : 50),
					'project_name' => 'Project'
				];
			});

			$chartData = [
				'pending' => $pendingTaskCount,
				'overdue' => $overdueTaskCount,
				'completed' => $completeTaskCount
			];

			$recentActivity = [];
			if ($overdueTaskCount > 0) {
				$recentActivity[] = [
					'icon' => 'exclamation-triangle',
					'title' => 'Overdue Tasks',
					'description' => "{$overdueTaskCount} tasks are overdue and need attention",
					'time_ago' => 'Just now'
				];
			}

			return response()->json([
				'success' => true,
				'taskStats' => $taskStats,
				'tasks' => $formattedTasks,
				'chartData' => $chartData,
				'recentActivity' => $recentActivity
			]);

		} catch (\Exception $e) {
			\Log::error('Employee tasks error: ' . $e->getMessage());
			return response()->json([
				'error' => 'An error occurred while fetching employee tasks: ' . $e->getMessage()
			], 500);
		}
	}

    // sales dashbaord
    public function sales_dashboard()
    {
        // inventory total l30 sales api call
                $response = Http::get('https://inventory.5coremanagement.com/api/l30-total-sales');
                $data = $response->json();
                $total_l30_sales = $data['data'];

         $inventory_total_avgPft = DB::table('inventories')->where('inv_route','pricing-masters.pricing_masters')->first();
		  $inventory_total_avgRoi = DB::table('inventories')->where('inv_route','pricing-masters.pricing_masters.roiHeader')->first();
		  
		  $inventory_total_inv = DB::table('inventories')->where('inv_route','pricing-masters.pricing_masters.invValueHeader')->first();
		  $inventory_total_lpvalue = DB::table('inventories')->where('inv_route','pricing-masters.pricing_masters.lpValueHeader')->first();

		  $inventory_Stock_missing_listing = DB::table('inventories')->where('inv_route','Stock_missing_listing')->first();
		  
		  $total_avgPft = $inventory_total_avgPft->inv_value;
		  $total_avgRoi = $inventory_total_avgRoi->inv_value;
		  
		  $total_inv = $inventory_total_inv->inv_value;
		  $total_lpv = $inventory_total_lpvalue->inv_value;

		  $total_missing_list = $inventory_Stock_missing_listing->inv_value;
        return view('taskly::SalesDash.index',compact('total_avgPft','total_avgRoi','total_inv','total_lpv','total_missing_list','total_l30_sales'));
    }
}
