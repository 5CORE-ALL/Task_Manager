<?php

namespace Workdo\Taskly\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Workdo\Taskly\Entities\Task;

use Yajra\DataTables\EloquentDataTable;
    
use Yajra\DataTables\Html\Builder as HtmlBuilder;

use Yajra\DataTables\Html\Button;

use Yajra\DataTables\Html\Column;

use Yajra\DataTables\Html\Editor\Editor;

use Yajra\DataTables\Html\Editor\Fields;

use Yajra\DataTables\Services\DataTable;

use Carbon\Carbon;

class ProjectTaskDatatable extends DataTable

{

    /**

     * Build the DataTable class.

     *

     * @param QueryBuilder $query Results from query() method.

     */

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {

        $rowcolumn = ['title', 'priority', 'status', 'assign_to', 'assigner_name', 'start_date', 'end_date', 'group', 'all_links','links','link_3','link_4','link_5','link_6','link_7','link_8','link_9','created_at', 'checkbox', 'due_date','completion_day','imagefile'];

        $dataTable = (new EloquentDataTable($query))

            ->editColumn('status', function (Task $task) {

                $stage = $task->stage;

                $html = '';

                // Replace status text values
                $displayStatus = $task->stage_name;
                if ($displayStatus === 'Need Help') {
                    $displayStatus = 'Help';
                } elseif ($displayStatus === 'Need Approval') {
                    $displayStatus = 'Need App';
                } elseif ($displayStatus === 'Not Applicable') {
                    $displayStatus = 'Inapplicable';
                }

                // Fixed width badge to keep consistent length
                $color = $stage ? $stage->color : '#6c757d';
                $html .= '<div> <span class="editable" data-id="' . $task->id . '" data-column="status" style="padding: 5px 10px;border-radius: 5px;color:white;cursor:pointer;background-color:' . $color . ';display:inline-block;min-width:100px;text-align:center;"> ' . $displayStatus . '</span></div>';
                
                // Show rework reason if status is Rework and rework_reason exists
                if ($displayStatus === 'Rework' && !empty($task->rework_reason)) {
                    $reworkReason = htmlspecialchars($task->rework_reason, ENT_QUOTES);
                    $html .= '<div style="margin-top: 5px; font-size: 11px; color: #6c757d; max-width: 200px; word-wrap: break-word;" title="' . $reworkReason . '" data-bs-toggle="tooltip" data-bs-placement="top">';
                    $html .= '<i class="fas fa-info-circle" style="color: #ffc107;"></i> ';
                    $html .= '<span style="font-style: italic;">' . (strlen($reworkReason) > 50 ? substr($reworkReason, 0, 50) . '...' : $reworkReason) . '</span>';
                    $html .= '</div>';
                }

                return $html;

            })

            ->editColumn('title', function (Task $task) {

                $sub_tasks = $task->sub_tasks;

                

                $taskHtml = '<div><span class="editable" style="word-wrap: break-word; white-space: normal; max-width: 200px;" 

                             title="' . htmlspecialchars($task->title, ENT_QUOTES) . '" 

                             data-id="' . $task->id . '" 

                             data-column="title">' . $task->title . '</span>';

                

                foreach ($sub_tasks as $sub) {

                    $taskHtml .= '<a data-size="lg" 

                                      data-url="' . route('tasks.show', [$task->id]) . '" 

                                      data-bs-toggle="tooltip"

                                      title="' . __('View') . '" 

                                      data-ajax-popup="true" 

                                      data-title="' . __('View') . '"

                                      class="mx-3 btn btn-sm align-items-center text-white bg-warning">

                                      ' . htmlspecialchars($sub->name, ENT_QUOTES) . '

                                  </a>';

                }

                 $taskHtml .= '</div>';

                return $taskHtml;

            })

            ->editColumn('group', function (Task $task) {

                return '<span class="editable" style="word-wrap: break-word; white-space: normal; max-width: 200px;" title="' .

                    htmlspecialchars($task->group, ENT_QUOTES) . '" data-id="' . $task->id . '" data-column="group">' .

                    $task->group .

                    '</span>';

            })

            // ->editColumn('start_date', function (Task $task) {

            //     return $task->start_date ?  date('m-d', strtotime($task->start_date)) : "";

            // })
            ->editColumn('start_date', function (Task $task) {
                $dateStr = $task->start_date ? date('d-M', strtotime($task->start_date)) : "";
                return $dateStr ? '<span style="color: black; font-weight: bold;">' . $dateStr . '</span>' : "";
            })
            ->addColumn('checkbox', function (Task $task) {

                $html = '<input type="checkbox" class="task-checkbox" value="' . $task->id . '">';

                return $html;

            })->editColumn('priority', function (Task $task) {

                $color = match (strtolower($task->priority)) {

                    'urgent' => 'danger',

                    'Take your time' => 'warning',

                    'normal' => 'success',

                    default => 'info',

                };

                $html = '';

                $html .= '<div> <span  data-id="' . $task->id . '" data-column="priority" class="editable btn btn-sm btn-' . $color . '"> ' . ucfirst($task->priority) . '</div>';

                return $html;

            })

            ->editColumn('schedule_time', function (Task $task) {

                $scheduleTime = "";

                if ($task->task_type == 'automate_task') {

                    $scheduleTime = $task->schedule_time ?  date('H:i a', strtotime($task->schedule_time)) : "";

                }

                return $scheduleTime;

            })

            // ->editColumn('due_date',function(Task $task){

            //     return $task->due_date ?   date('Y-m-d', strtotime($task->due_date)) : "";

            // })

            //       ->editColumn('due_date', function(Task $task) {

            //     return $task->due_date 

            //         ? '<span style="color: black; font-weight: bold;">' . date('m-d', strtotime($task->due_date)) . '</span>' 

            //         : "";

            // })

            ->editColumn('due_date', function (Task $task) {

                $dueDate = $task->due_date ? date('d-M', strtotime($task->due_date)) : "";

                // Get user's overdue duration, default to 0 if not set
                $userOverdueDays = 0;
                if ($task->assign_to) {
                    $user = \App\Models\User::where('email', $task->assign_to)->first();
                    $userOverdueDays = $user ? ($user->overdue_duration_days ?? 0) : 0;
                }

                // Calculate overdue threshold: now minus user's overdue days
                $overdueThreshold = time() - ($userOverdueDays * 86400);

                $color = (strtotime($task->due_date) < $overdueThreshold) ? 'red' : 'black';

                return $dueDate ? '<span style="color: ' . $color . '; font-weight: bold;">' . $dueDate . '</span>' : "";

            })

            // ->editColumn('due_date', function (Task $task) {

            //     $startDate = $task->start_date ? strtotime($task->start_date) : null;

            

            //     if ($startDate) {

            //         $daysToAdd = 3; // Number of days to add (excluding Sundays)

            //         $adjustedDueDate = $startDate;

            

            //         for ($i = 1; $i <= $daysToAdd; $i++) {

            //             $adjustedDueDate = strtotime('+1 day', $adjustedDueDate);

            //             // If the adjusted due date falls on a Sunday, skip it by adding one more day

            //             while (date('N', $adjustedDueDate) == 7) { // 7 = Sunday

            //                 $adjustedDueDate = strtotime('+1 day', $adjustedDueDate);

            //             }

            //         }

            

            //         $dueDateFormatted = date('m-d', $adjustedDueDate); // Format as "m-d"

            //         $dueDateTimestamp = $adjustedDueDate; // Keep the timestamp for comparison

            //     } else {

            //         $dueDateFormatted = "";

            //         $dueDateTimestamp = null;

            //     }

            

            //     // Determine the color based on whether the due date has passed

            //     $color = ($dueDateTimestamp && $dueDateTimestamp < time()) ? 'red' : 'black';

            

            //     return $dueDateFormatted ? '<span style="color: ' . $color . '; font-weight: bold;">' . $dueDateFormatted . '</span>' : "";

            // })

            ->filterColumn('assigner_name', function ($query, $keyword) {

                $query->whereRaw("EXISTS (SELECT 1 FROM users WHERE FIND_IN_SET(users.email, tasks.assignor) AND users.name LIKE ?)", ["%$keyword%"]);

            })

            ->filterColumn('assign_to', function ($query, $keyword) {

                $query->whereRaw("EXISTS (SELECT 1 FROM users WHERE FIND_IN_SET(users.email, tasks.assign_to) AND users.name LIKE ?)", ["%$keyword%"]);

            })

            // ->addColumn('assigner_name',function(Task $task){

            //     $user = $task->assignorUser();

            //     $html ='';

            //     if (check_file($user?->avatar) == false) {

            //         $path = asset('uploads/user-avatar/avatar.png');

            //     } else {

            //         $path = get_file($user?->avatar);

            //     }

            //     $html .= '<img  src="' . $path . '" data-bs-toggle="tooltip"  title="' . $user?->name . '" data-bs-placement="top"  class="rounded-circle" width="25" height="25">';

            //     return $html;

            // })

            ->addColumn('assigner_name', function (Task $task) {

                $html = '';

                foreach ($task->assignorUsers() as $user) {

                    if (check_file($user->avatar) == false) {

                        $path = asset('uploads/user-avatar/avatar.png');

                    } else {

                        $path = get_file($user->avatar);

                    }

                    $html .= '<div class="d-flex align-items-center gap-2">';

                    $html .= '<img src="' . $path . '" data-bs-toggle="tooltip" title="' . $user->name . '" data-bs-placement="top" class="rounded-circle" width="40" height="40">';

                    $html .= '<span>' . formatUserName($user->name) . '</span>';

                    $html .= '</div>';

                }

                return $html;

            })

            ->addColumn('links', function (Task $task) {
                $links = '';
                $exportData = [];
                
                if ($task->link1) {
                    $links .= '<a href="' . $task->link1 . '" target="_blank" class="mx-1" data-bs-toggle="tooltip" title="' . $task->link1 . '">
                              <i class="fas fa-link"></i>
                          </a>';
                    $exportData[] = 'L1: ' . $task->link1;
                }
                if ($task->link2) {
                    $links .= '<a href="' . $task->link2 . '" target="_blank" class="mx-1" data-bs-toggle="tooltip" title="' . $task->link2 . '">
                              <i class="fas fa-external-link-alt"></i>
                          </a>';
                    $exportData[] = 'L2: ' . $task->link2;
                }
                
                // Add hidden span with export data
                if (!empty($exportData)) {
                    $exportText = implode(' | ', $exportData);
                    $links .= '<span style="display:none;" class="export-data">' . $exportText . '</span>';
                }
                
                return $links ?: '';
            })

             ->addColumn('link_3', function (Task $task) {

                $links = '';

                if ($task->link3) {

                    $links .= '<a href="' . $task->link3 . '" target="_blank" class="mx-1" data-bs-toggle="tooltip" title="' . $task->link3 . '">

                              <i class="fas fa-link"></i>

                          </a>';
                    $links .= '<span style="display:none;" class="export-data">' . $task->link3 . '</span>';

                }

               

                return $links;

            })

            ->editColumn('completion_day', function (Task $task) {
                if ($task->start_date && $task->completion_date && $task->completion_date != '0000-00-00' && $task->completion_date != '0000-00-00 00:00:00') {
                    try {
                        $startDate = \Carbon\Carbon::parse($task->start_date);
                        $completionDate = \Carbon\Carbon::parse($task->completion_date);
                        $days = $startDate->diffInDays($completionDate);
                        return $days . ' Days';
                    } catch (\Exception $e) {
                        return '-';
                    }
                }
                return '-';
            })
             ->addColumn('link_4', function (Task $task) {

                $links = '';

                if ($task->link4) {

                    $links .= '<a href="' . $task->link4 . '" target="_blank" class="mx-1" data-bs-toggle="tooltip" title="' . $task->link4 . '">

                              <i class="fas fa-link"></i>

                          </a>';
                    $links .= '<span style="display:none;" class="export-data">' . $task->link4 . '</span>';

                }

               

                return $links;

            })

             ->addColumn('link_5', function (Task $task) {

                $links = '';

                if ($task->link5) {

                    $links .= '<a href="' . $task->link5 . '" target="_blank" class="mx-1" data-bs-toggle="tooltip" title="' . $task->link5 . '">

                              <i class="fas fa-link"></i>

                          </a>';
                    $links .= '<span style="display:none;" class="export-data">' . $task->link5 . '</span>';

                }

               

                return $links;

            })
            ->addColumn('link_7', function (Task $task) {

                $links = '';

                if ($task->link7) {

                    $links .= '<a href="' . $task->link7 . '" target="_blank" class="mx-1" data-bs-toggle="tooltip" title="' . $task->link7 . '">

                              <i class="fas fa-link"></i>

                          </a>';
                    $links .= '<span style="display:none;" class="export-data">' . $task->link7 . '</span>';

                }

               

                return $links;

            })
            ->addColumn('link_8', function (Task $task) {

                $links = '';

                if ($task->link8) {

                    $links .= '<a href="' . $task->link8 . '" target="_blank" class="mx-1" data-bs-toggle="tooltip" title="' . $task->link8 . '">

                              <i class="fas fa-link"></i>

                          </a>';
                    $links .= '<span style="display:none;" class="export-data">' . $task->link8 . '</span>';

                }

               

                return $links;

            })
            ->addColumn('link_6', function (Task $task) {

                $links = '';

                if ($task->link6) {

                    $links .= '<a href="' . $task->link6 . '" target="_blank" class="mx-1" data-bs-toggle="tooltip" title="' . $task->link6 . '">

                              <i class="fas fa-link"></i>

                          </a>';
                    $links .= '<span style="display:none;" class="export-data">' . $task->link6 . '</span>';

                }

               

                return $links;

            })
            ->addColumn('imagefile', function (Task $task) {

                $images = '';

                if (count($task->taskFiles)>0 && $task->taskFiles ) {

                    $images .= '<a href="' . get_file($task->taskFiles[0]->file) . '" target="_blank" class="mx-1" data-bs-toggle="tooltip" title="' . $task->taskFiles[0]->name . '">

                              <i class="fas fa-link"></i>

                          </a>';
                    $images .= '<span style="display:none;" class="export-data">' . get_file($task->taskFiles[0]->file) . '</span>';

                }

                return $images;

            })
            ->addColumn('link_9', function (Task $task) {

                $links = '';

                if ($task->link9) {

                    $links .= '<a href="' . $task->link9 . '" target="_blank" class="mx-1" data-bs-toggle="tooltip" title="' . $task->link9 . '">

                              <i class="fas fa-link"></i>

                          </a>';
                    $links .= '<span style="display:none;" class="export-data">' . $task->link9 . '</span>';

                }

               

                return $links;

            })
            ->editColumn('assign_to', function (Task $task) {

                $html = '';

                foreach ($task->users() as $user) {

                    if (check_file($user->avatar) == false) {

                        $path = asset('uploads/user-avatar/avatar.png');

                    } else {

                        $path = get_file($user->avatar);

                    }

                    $html .= '<div class="d-flex align-items-center gap-2">';

                    $html .= '<img src="' . $path . '" data-bs-toggle="tooltip" title="' . $user->name . '" data-bs-placement="top" class="rounded-circle" width="40" height="40">';

                    $html .= '<span>' . formatUserName($user->name) . '</span>';

                    $html .= '</div>';

                }

                return $html;
            })
            
->addColumn('all_links', function (Task $task) {
    $linksHtml = '';
    $exportData = [];
    
    // Define link types with icons and titles
    $linkTypes = [
        'link1' => ['icon' => 'fas fa-link', 'title' => 'Link 1', 'label' => 'L1'],
        'link2' => ['icon' => 'fas fa-external-link-alt', 'title' => 'Link 2', 'label' => 'L2'],
        'link3' => ['icon' => 'fas fa-graduation-cap', 'title' => 'Training Link', 'label' => 'TL'],
        'link4' => ['icon' => 'fas fa-video', 'title' => 'Video Link', 'label' => 'VL'],
        'link5' => ['icon' => 'fas fa-file-alt', 'title' => 'Forms', 'label' => 'Forms'],
        'link6' => ['icon' => 'fas fa-tasks', 'title' => 'Checklist Link', 'label' => 'CL'],
        'link7' => ['icon' => 'fas fa-chart-bar', 'title' => 'Form Report Link', 'label' => 'FR'],
        'link8' => ['icon' => 'fas fa-cogs', 'title' => 'Process', 'label' => 'PROCESS'],
        'link9' => ['icon' => 'fas fa-file-contract', 'title' => 'PL Link', 'label' => 'PL']
    ];
    
    // Count how many links exist
    $linkCount = 0;
    foreach ($linkTypes as $field => $linkInfo) {
        if ($task->$field) {
            $linkCount++;
            $exportData[] = $linkInfo['label'] . ': ' . $task->$field;
        }
    }
    
    // Only show hamburger if there are links
    if ($linkCount > 0) {
        $linksHtml = '<button type="button" class="btn btn-sm btn-outline-primary links-popup-btn" 
                     data-task-id="' . $task->id . '" data-bs-toggle="modal" data-bs-target="#linksModal"
                     title="' . $linkCount . ' Links Available">
                     <i class="fas fa-bars"></i>
                  </button>';
        
        // Add hidden div with all links data for this task
        $linksHtml .= '<div class="task-links-data" id="task-links-' . $task->id . '" style="display: none;">';
        foreach ($linkTypes as $field => $linkInfo) {
            if ($task->$field) {
                $linksHtml .= '<div class="link-item" data-title="' . $linkInfo['title'] . '" 
                              data-url="' . htmlspecialchars($task->$field) . '" 
                              data-icon="' . $linkInfo['icon'] . '" 
                              data-label="' . $linkInfo['label'] . '"></div>';
            }
        }
        $linksHtml .= '</div>';
    }
    
    // Add hidden span with export data
    if (!empty($exportData)) {
        $exportText = implode(' | ', $exportData);
        $linksHtml .= '<span style="display:none;" class="export-data">' . $exportText . '</span>';
    }
    
    return $linksHtml ?: '';
})

           ->editColumn('completion_date', function (Task $task) {
                if (empty($task->completion_date) || $task->completion_date === '0000-00-00' || $task->completion_date === '0000-00-00 00:00:00' || is_null($task->completion_date)) {
                    return '';
                }
                
                $date = $task->completion_date;
                
                // Handle different date formats
                if (preg_match('/^\d{4}-\d{2}-\d{2}/', $date)) {
                    // Format: yyyy-mm-dd or yyyy-mm-dd hh:mm:ss
                    return date('d-M', strtotime($date));
                } elseif (preg_match('/^\d{2}-\d{2}$/', $date)) {
                    // Format: mm-dd (already in desired format)
                    return $date;
                } elseif (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}/', $date)) {
                    // Format: m/d/yyyy or mm/dd/yyyy
                    return date('d-M', strtotime($date));
                } else {
                    // Try to parse any other format
                    $timestamp = strtotime($date);
                    if ($timestamp !== false) {
                        return date('d-M', $timestamp);
                    }
                    return '';
                }

    });
        if (\Laratrust::hasPermission('task show') || \Laratrust::hasPermission('task edit') || \Laratrust::hasPermission('task delete')) {

            $dataTable->addColumn('action', function (Task $task) {

                return view('taskly::projects.task_action', compact('task'));

            });

            $rowcolumn[] = 'action';

        }

        return $dataTable->rawColumns($rowcolumn);

    }

    /**

     * Get the query source of dataTable.

     */

    public function query(Task $model)

    {

        $task = $model->select('tasks.*', 'stages.name as stage_name')

            ->join('stages', 'stages.name', '=', 'tasks.status')

            ->where('deleted_at',NULL)

            // Exclude automate missed tasks (is_automate_task = 1 AND is_missed = 1)
            // Keep: manual tasks (is_automate_task = 0) OR automate tasks that are not missed (is_missed = 0)
            ->where(function($query) {
                $query->where('is_missed', 0)
                      ->orWhere('is_automate_task', 0);
            })

            ->where('tasks.workspace', getActiveWorkSpace())

            ->distinct() // Remove duplicate rows

            ->orderByRaw("
                CASE
                    WHEN tasks.start_date IS NULL OR tasks.start_date = '' THEN '9999-99-99'
                    WHEN tasks.start_date REGEXP '^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}' THEN
                        LEFT(tasks.start_date, 10)  -- Extract date part from datetime
                    WHEN tasks.start_date REGEXP '^[0-9]{1,2}-[0-9]{1,2}-[0-9]{4}' THEN
                        CONCAT(
                            RIGHT(LEFT(tasks.start_date, 10), 4),
                            '-',
                            LPAD(SUBSTRING_INDEX(SUBSTRING_INDEX(LEFT(tasks.start_date, 10), '-', 2), '-', -1), 2, '0'),
                            '-',
                            LPAD(SUBSTRING_INDEX(LEFT(tasks.start_date, 10), '-', 1), 2, '0')
                        )
                    ELSE '9999-99-99'
                END ASC
            ");

        $objUser = Auth::user();
        
        // Check if user is privileged (can see all tasks)
        // Only these specific roles should have full access to all tasks
        // Get all user roles and check against privileged role names (case-insensitive)
        $userRoles = $objUser->roles->pluck('name')->map(function($role) {
            return strtolower(trim($role));
        })->toArray();
        
        $privilegedRoles = ['client', 'company', 'manager all access', 'hr'];
        $isPrivileged = false;
        foreach ($privilegedRoles as $privilegedRole) {
            if (in_array(strtolower($privilegedRole), $userRoles)) {
                $isPrivileged = true;
                break;
            }
        }
        
        // If not privileged, filter to show only tasks where user is assignor OR assignee
        // This applies to all non-privileged roles including "software executive"
        if (!$isPrivileged) {
            if (isset($objUser) && $objUser) {
                $task->where(function ($query) use ($objUser) {
                    $query->whereRaw("FIND_IN_SET(?, assign_to)", [$objUser->email])
                        ->orWhere('assignor', $objUser->email);
                });
            }
        }

        // code change for assignee and assignor override 

        

        // if (request()->has('assignee_name') && !empty(request()->input('assignee_name'))) {

        //     $assigneeName = request()->input('assignee_name')[0];

        //     $task->where(function ($query) use ($assigneeName) {

        //         $query->where('assign_to', 'like', "%$assigneeName%");

        //     });

        // }

        

        if (request()->has('assignee_name') && !empty(request()->input('assignee_name'))) {

            $assigneeEmails = request()->input('assignee_name');

            $task->where(function ($query) use ($assigneeEmails) {

                foreach ($assigneeEmails as $email) {

                    if ($email === 'NULL') {

                        // Check for NULL, empty, or tasks where no valid users exist for the assign_to emails
                        $query->orWhere(function ($q) {

                            $q->whereNull('assign_to')

                              ->orWhere('assign_to', '')

                              ->orWhereRaw("LENGTH(assign_to) > 0 AND (
                                  SELECT COUNT(*) FROM users
                                  WHERE FIND_IN_SET(users.email, tasks.assign_to)
                              ) = 0");

                        });

                    } else {

                        $query->orWhereRaw("FIND_IN_SET(?, assign_to)", [$email]);

                    }

                }

            });

        }

        // if (request()->has('assignor_name') && !empty(request()->input('assignor_name'))) {

        //     $assignorName = request()->input('assignor_name')[0];

        //     $task->where(function ($query) use ($assignorName) {

        //         $query->where('assignor', 'like', "%$assignorName%");

        //     });

        // }

        

        

        

        if (request()->has('assignor_name') && !empty(request()->input('assignor_name'))) {

            $assignorEmails = request()->input('assignor_name');

            $task->where(function ($query) use ($assignorEmails) {

                foreach ($assignorEmails as $email) {

                    if ($email === 'NULL') {

                        // Check for NULL, empty, or tasks where no valid users exist for the assignor emails
                        $query->orWhere(function ($q) {

                            $q->whereNull('assignor')

                              ->orWhere('assignor', '')

                              ->orWhereRaw("LENGTH(assignor) > 0 AND (
                                  SELECT COUNT(*) FROM users
                                  WHERE FIND_IN_SET(users.email, tasks.assignor)
                              ) = 0");

                        });

                    } else {

                        $query->orWhereRaw("FIND_IN_SET(?, assignor)", [$email]);

                    }

                }

            });

        }

// code change for assignee and assignor override 
        if (request()->has('status_name') && !empty(request()->input('status_name'))) {

            $statusName = request()->input('status_name');

            $task->where(function ($query) use ($statusName) {

                $query->where('status', 'like', "%$statusName%");

            });

        }
        if (request()->has('priority') && !empty(request()->input('priority'))) {

            $priority = request()->input('priority');

            $task->where(function ($query) use ($priority) {

                $query->where('priority', 'like', "%$priority%");

            });

        }
         if (request()->has('group_name') && !empty(request()->input('group_name'))) {

            $group_name = request()->input('group_name');

            $task->where(function ($query) use ($group_name) {

                $query->where('group', 'like', "%$group_name%");

            });

        }

        if (request()->has('task_name') && !empty(request()->input('task_name'))) {

            $task_name = request()->input('task_name');

            $task->where(function ($query) use ($task_name) {

                $query->where('title', 'like', "%$task_name%");

            });

        }

        // Add a condition to search by assignee names

        if ((request()->has('search.value') && !empty(request()->input('search.value'))) ||
            (request()->has('search_value') && !empty(request()->input('search_value')))) {

            $searchValue = request()->input('search.value') ?: request()->input('search_value');

            $task->where(function ($query) use ($searchValue) {

                $query->whereRaw("EXISTS (SELECT 1 FROM users WHERE FIND_IN_SET(users.email, tasks.assignor) AND users.name LIKE ?)", ["%$searchValue%"])

                    ->orWhereRaw("EXISTS (SELECT 1 FROM users WHERE FIND_IN_SET(users.email, tasks.assign_to) AND users.name LIKE ?)", ["%$searchValue%"])

                     ->orWhere('tasks.title', 'like', "%$searchValue%") // Search by task name

                    ->orWhere('tasks.group', 'like', "%$searchValue%") // Search by group name

                    ->orWhere('tasks.description', 'like', "%$searchValue%") // Search by description

                    ->orWhere('tasks.priority', 'like', "%$searchValue%") // Search by priority

                    ->orWhere('stages.name', 'like', "%$searchValue%") // Search by status

                    ->orWhere('tasks.start_date', 'like', "%$searchValue%") // Search by start date

                    ->orWhere('tasks.due_date', 'like', "%$searchValue%") // Search by due date

                    ->orWhere('tasks.eta_time', 'like', "%$searchValue%") // Search by ETA time

                    ->orWhere('tasks.etc_done', 'like', "%$searchValue%") // Search by ETC done

                    ->orWhere('tasks.completion_date', 'like', "%$searchValue%") // Search by completion date

                    ->orWhere('tasks.completion_day', 'like', "%$searchValue%") // Search by completion day

                    ->orWhere('tasks.assign_to', 'like', "%$searchValue%") // Search by assign_to emails

                    ->orWhere('tasks.assignor', 'like', "%$searchValue%"); // Search by assignor emails

            });

        }
// Add toggle filter support
if (request()->has('toggle_filter') && !empty(request()->input('toggle_filter'))) {
    $toggleFilter = request()->input('toggle_filter');
   $currentDate = Carbon::now();

if ($toggleFilter === 'overdue') {
    // Show all overdue tasks (due date before or equal to now)
    $task->where('tasks.due_date', '<=', $currentDate);
} elseif ($toggleFilter === 'normal') {
    // Show normal tasks (due date after now OR no due date)
    // $task->where(function($query) use ($currentDate) {
    //     $query->where('tasks.due_date', '>=', $currentDate)
    //           ->orWhereNull('tasks.due_date');
    // });
    $task->where('tasks.priority', 'normal');
} elseif ($toggleFilter === 'urgent') {
    $task->where('tasks.priority', 'urgent');
}

    // For 'all', no extra filtering
}

        return $task;

    }

    /**

     * Optional method if you want to use the html builder.

     */

    public function html(): HtmlBuilder

    {

        $dataTable = $this->builder()

            ->setTableId('projects-task-table')

            ->columns($this->getColumns())

            ->minifiedAjax()

            ->searching(false) // Disable DataTable's built-in search box

            ->language([

                "paginate" => [

                    "next" => '<i class="ti ti-chevron-right"></i>',

                    "previous" => '<i class="ti ti-chevron-left"></i>'

                ],

                'lengthMenu' => "_MENU_" . __('Entries Per Page'),

                "searchPlaceholder" => __('Search...'),

                "search" => "",

                "info" => __('Showing _START_ to _END_ of _TOTAL_ entries')

            ])

            ->lengthMenu([25, 50, 100, 200]) // Pagination options

            ->pageLength(100) // Show 100 records per page 

            ->initComplete('function() {

                var table = this;

                var checkboxes = $(\'#projects-task-table tbody\').on(\'change\', \'.task-checkbox\', function() {

                    if ($(".task-checkbox:checked").length > 0) {

                        $("#delete-btn, #duplicate-btn").prop("disabled", false);

                    } else {

                        $("#delete-btn, #duplicate-btn").prop("disabled", true);

                    }

                });

                $("#select-all").on("click", function() {

                    $(".task-checkbox").prop("checked", this.checked).trigger("change");

                });

            }');

        $exportButtonConfig = [

            'extend' => 'collection',

            'className' => 'btn btn-light-secondary dropdown-toggle',

            'text' => '<i class="ti ti-download me-2" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Export"></i>',

            'buttons' => [

                [

                    'extend' => 'print',

                    'text' => '<i class="fas fa-print me-2"></i> ' . __('Print'),

                    'className' => 'btn btn-light text-primary dropdown-item',

                    'exportOptions' => [
                        'columns' => ':visible:not(.no-export)',
                        'format' => [
                            'body' => 'function(data, row, column, node) {
                                var exportSpan = $(node).find(".export-data");
                                if (exportSpan.length > 0) {
                                    return exportSpan.text();
                                }
                                return $(node).text();
                            }'
                        ]
                    ],

                ],

                [

                    'extend' => 'csv',

                    'text' => '<i class="fas fa-file-csv me-2"></i> ' . __('CSV'),

                    'className' => 'btn btn-light text-primary dropdown-item',

                    'exportOptions' => [
                        'columns' => ':visible:not(.no-export)',
                        'format' => [
                            'body' => 'function(data, row, column, node) {
                                var exportSpan = $(node).find(".export-data");
                                if (exportSpan.length > 0) {
                                    return exportSpan.text();
                                }
                                return $(node).text();
                            }'
                        ]
                    ],

                ],

                [

                    'extend' => 'excel',

                    'text' => '<i class="fas fa-file-excel me-2"></i> ' . __('Excel'),

                    'className' => 'btn btn-light text-primary dropdown-item',

                    'exportOptions' => [
                        'columns' => ':visible:not(.no-export)',
                        'format' => [
                            'body' => 'function(data, row, column, node) {
                                var exportSpan = $(node).find(".export-data");
                                if (exportSpan.length > 0) {
                                    return exportSpan.text();
                                }
                                return $(node).text();
                            }'
                        ]
                    ],

                ]

            ],

        ];

        $buttonsConfig = array_merge([

            $exportButtonConfig,

            [

                'text' => '<i class="fas fa-copy" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Duplicate"></i>',

                'className' => 'btn btn-light-primary  duplicate-btn ',

                'attr' => ['id' => 'duplicate-btn', 'disabled' => 'disabled'],

                'action' => 'function(e, dt, node, config) {

                    let selectedIds = $(".task-checkbox:checked").map(function() { return this.value; }).get();

                    if (selectedIds.length > 0) {

                        duplicateTasks(selectedIds);

                    }

                }'

            ],

            

            [

            'text' => '<i class="fas fa-trash"></i>',

            'className' => 'btn btn-light-danger delete-group-btn',

            'attr' => [

                'id' => 'delete-btn',

                'disabled' => 'disabled',

                'data-allowed-emails' => 'president@5core.com,tech-support@5core.com','mgr-advertisement@5core.com', 'mgr-content@5core.com', 'sjoy7486@gmail.com','sr.manager@5core.com','software9@5core.com','software2@5core.com','ritu.kaur013@gmail.com','support@5core.com','mgr-operations@5core.com','inventory@5core.com'

            ],

            'action' => 'function(e, dt, node, config) {

                if (!$(node).attr("disabled")) {

                    let selectedIds = $(".task-checkbox:checked").map(function() { 

                        return this.value; 

                    }).get();

                    if (selectedIds.length > 0) {

                        deleteTasks(selectedIds);

                    }

                }

            }'

        ],
        [
            'text' => '<i class="fas fa-tasks" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="Update Status"></i>',
            'className' => 'btn btn-light-info bulk-status-update-btn',
            'attr' => ['id' => 'bulk-status-update-btn', 'disabled' => 'disabled'],
            'action' => 'function(e, dt, node, config) {
                if (!$(node).attr("disabled")) {
                    let selectedIds = $(".task-checkbox:checked").map(function() { 
                        return this.value; 
                    }).get();
                    console.log("Bulk Status Update Button clicked - Selected IDs:", selectedIds);
                    if (selectedIds.length > 0) {
                        $("#selected-task-ids-status").val(selectedIds.join(","));
                        console.log("Setting status hidden field value:", selectedIds.join(","));
                        $("#change-status-modal").modal("show");
                    } else {
                        toastr.warning("Please select at least one task to update status.");
                    }
                }
            }'
        ],
        [
            'text' => '<i class="fas fa-user-edit" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="Bulk Update Assignor"></i>',
            'className' => 'btn btn-light-warning bulk-assignor-update-btn',
            'attr' => ['id' => 'bulk-assignor-update-btn', 'disabled' => 'disabled'],
            'action' => 'function(e, dt, node, config) {
                if (!$(node).attr("disabled")) {
                    let selectedIds = $(".task-checkbox:checked").map(function() { 
                        return this.value; 
                    }).get();
                    console.log("Bulk Assignor Update Button clicked - Selected IDs:", selectedIds);
                    if (selectedIds.length > 0) {
                        $("#selected-task-ids-assignor").val(selectedIds.join(","));
                        console.log("Setting assignor hidden field value:", selectedIds.join(","));
                        $("#change-assignor-modal").modal("show");
                    } else {
                        toastr.warning("Please select at least one task to update assignor.");
                    }
                }
            }'
        ],
        [
            'text' => '<i class="fas fa-user-plus" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="Bulk Update Assignee"></i>',
            'className' => 'btn btn-light-success bulk-assignee-update-btn',
            'attr' => ['id' => 'bulk-assignee-update-btn', 'disabled' => 'disabled'],
            'action' => 'function(e, dt, node, config) {
                if (!$(node).attr("disabled")) {
                    let selectedIds = $(".task-checkbox:checked").map(function() { 
                        return this.value; 
                    }).get();
                    console.log("Bulk Assignee Update Button clicked - Selected IDs:", selectedIds);
                    if (selectedIds.length > 0) {
                        $("#selected-task-ids-assignee").val(selectedIds.join(","));
                        console.log("Setting assignee hidden field value:", selectedIds.join(","));
                        $("#change-assignee-modal").modal("show");
                    } else {
                        toastr.warning("Please select at least one task to update assignee.");
                    }
                }
            }'
        ],
        [
            'text' => '<i class="fas fa-clock" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="Bulk Update ETC"></i>',
            'className' => 'btn btn-light-secondary bulk-etc-update-btn',
            'attr' => ['id' => 'bulk-etc-update-btn', 'disabled' => 'disabled'],
            'action' => 'function(e, dt, node, config) {
                if (!$(node).attr("disabled")) {
                    let selectedIds = $(".task-checkbox:checked").map(function() { 
                        return this.value; 
                    }).get();
                    console.log("Bulk ETC Update Button clicked - Selected IDs:", selectedIds);
                    if (selectedIds.length > 0) {
                        $("#selected-task-ids-etc").val(selectedIds.join(","));
                        console.log("Setting ETC hidden field value:", selectedIds.join(","));
                        $("#change-etc-modal").modal("show");
                    } else {
                        toastr.warning("Please select at least one task to update ETC.");
                    }
                }
            }'
        ],
        [
            'text' => '<i class="fas fa-calendar" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="Bulk Update Dates/TID"></i>',
            'className' => 'btn btn-light-purple bulk-date-update-btn',
            'attr' => ['id' => 'bulk-date-update-btn', 'disabled' => 'disabled'],
            'action' => 'function(e, dt, node, config) {
                if (!$(node).attr("disabled")) {
                    let selectedIds = $(".task-checkbox:checked").map(function() { 
                        return this.value; 
                    }).get();
                    console.log("Bulk Date Update Button clicked - Selected IDs:", selectedIds);
                    if (selectedIds.length > 0) {
                        $("#selected-task-ids-date").val(selectedIds.join(","));
                        console.log("Setting date hidden field value:", selectedIds.join(","));
                        $("#change-date-modal").modal("show");
                    } else {
                        toastr.warning("Please select at least one task to update date.");
                    }
                }
            }'
        ],
        [
            'text' => '<i class="fas fa-flag" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="Bulk Update Priority"></i>',
            'className' => 'btn btn-light-danger bulk-priority-update-btn',
            'attr' => ['id' => 'bulk-priority-update-btn', 'disabled' => 'disabled'],
            'action' => 'function(e, dt, node, config) {
                if (!$(node).attr("disabled")) {
                    let selectedIds = $(".task-checkbox:checked").map(function() { 
                        return this.value; 
                    }).get();
                    console.log("Bulk Priority Update Button clicked - Selected IDs:", selectedIds);
                    if (selectedIds.length > 0) {
                        $("#selected-task-ids-priority").val(selectedIds.join(","));
                        console.log("Setting priority hidden field value:", selectedIds.join(","));
                        $("#change-priority-modal").modal("show");
                    } else {
                        toastr.warning("Please select at least one task to update priority.");
                    }
                }
            }'
        ],

            // [

            //     'extend' => 'reset',

            //     'className' => 'btn btn-light-danger',

            // ],

            // [

            //     'extend' => 'reload',

            //     'className' => 'btn btn-light-warning',

            // ],

        ]);
        
        $currentUser = Auth::user();
        $currentUserEmail = $currentUser ? $currentUser->email : '';
        $dataTable->parameters([
            
            "fixedHeader" => true,
            "scrollY" => '100vh',
            "scrollCollapse" => true,
            "serverSide" => true,
            "processing" => true,
            "dom" =>  "

        

         <'dataTable-top row'<'col-sm-1'l><'col-sm-4 dataTable-botton table-btn dataTable-botton dataTable-search tb-search d-flex justify-content-end gap-2'B><'col-sm-2'<'task-toggle-wrapper'>>>

        <'dataTable-container'<'col-sm-12'tr>>

        <'dataTable-bottom row'<'col-5'i><'col-7'p>>",

            'buttons' => $buttonsConfig,

            "drawCallback" => 'function( settings ) {

                var tooltipTriggerList = [].slice.call(

                    document.querySelectorAll("[data-bs-toggle=tooltip]")

                  );

                  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {

                    return new bootstrap.Tooltip(tooltipTriggerEl);

                  });

                  var popoverTriggerList = [].slice.call(

                    document.querySelectorAll("[data-bs-toggle=popover]")

                  );

                  var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {

                    return new bootstrap.Popover(popoverTriggerEl);

                  });

                  var toastElList = [].slice.call(document.querySelectorAll(".toast"));

                  var toastList = toastElList.map(function (toastEl) {

                    return new bootstrap.Toast(toastEl);

                  });

            }',

            "rowCallback" => 'function(row, data, index) {
                // List of admin/support emails
                var adminEmails = [
                    "president@5core.com",
                    "support@5core.com",
                    "tech-support@5core.com"
                ];
                var currentUserEmail = "{$currentUserEmail}";
                // If current user is NOT admin/support, show color
                if (!adminEmails.includes(currentUserEmail)) {
                    if (data.task_type=="automate_task") {
                        $(row).addClass("automate-task");
                    }
                }
            }'
            

        ]);

        $dataTable->language([

            'buttons' => [

                'create' => __('Create'),

                'export' => __('Export'),

                'print' => __('Print'),

                'reset' => __('Reset'),

                'reload' => __('Reload'),

                'excel' => __('Excel'),

                'csv' => __('CSV'),

            ]

        ]);

        return $dataTable;

    }

    /**

     * Get the dataTable columns definition.

     */

    public function getColumns(): array

    {

        $column = [

            Column::make('checkbox')->title('<input type="checkbox" id="select-all">')

                ->orderable(false)

                ->searchable(false)

                ->exportable(false)

                ->printable(false)

                ->className('no-export'),

            Column::make('group')->title(__('Group'))->searchable(true),

            Column::make('title')->title(__('Task')),

            Column::make('assigner_name')->title(__('Assignor')),

            Column::make('assign_to')->title(__('Assignee'))->printable(false),

            Column::make('start_date')->title('<span title="Task Initiation Date">TID</span>')->html()->exportable(false)->searchable(false)->name('start_date'),

            Column::make('due_date')->title('<span title="Overdue Date">OVERDUE</span>')->html()->exportable(false)->searchable(false),

            Column::make('eta_time')->title('<span title="Estimate Time Count">ETC</span>')->html()->exportable(false)->searchable(false),

            Column::make('etc_done')->title('<span title="Actual Time Count">ATC</span>')->html()->exportable(false)->searchable(false),
            // Column::make('completion_date')->title(__('C Date'))->exportable(false)->searchable(false),
            Column::make('completion_day')->title('<span title="Completion Day">C DAY</span>')->html()->exportable(false)->searchable(false),

            Column::make('status')->title(__('Status'))->exportable(true)->name('stages.name'),
            
            Column::make('priority')->title(__('Priority')),
            
            Column::make('imagefile')->title(__('Image'))->exportable(true),

            // In your getColumns() method, replace all the link columns with:
            Column::make('all_links')->title(__('Links'))->html()->exportable(true)->addClass('extra-col'),
            // Column::make('links')->title('<span title="Link 1 & Link 2">L1 & L2</span>')->html()->exportable(true),

            // Column::make('link_3')->title('<span title="Training Link">TL</span>')->html()->exportable(true),
            // Column::make('link_8')->title(__('PROCESS'))->exportable(true),

            // Column::make('link_4')->title('<span title="Video Link">VL</span>')->html()->exportable(true),

            // Column::make('link_5')->title(__('Forms'))->exportable(true),
            // Column::make('link_7')->title('<span title="Form Report Link">FR</span>')->html()->exportable(true),
            //  Column::make('link_6')->title('<span title="Checklist Link">CL</span>')->html()->exportable(true),
            //  Column::make('link_9')->title('<span title="PL Link">PL</span>')->html()->exportable(true)

            

            // Column::make('file')->title(__('File')),

            // Column::make('schedule_type')->title(__('Type')),

            // Column::make('schedule_time')->title(__('Create Time'))

        ];

        if (\Laratrust::hasPermission('task show') || \Laratrust::hasPermission('task edit') || \Laratrust::hasPermission('task delete')) {

            $action = [

                Column::computed('action')

                    ->exportable(false)

                    ->printable(false)

                    ->width(60)

                    ->addClass('no-export')

            ];

            $column = array_merge($column, $action);

        }

        return $column;

    }

    /**

     * Get the filename for export.

     */

    protected function filename(): string

    {

        return 'Task_' . date('YmdHis');

    }

}


