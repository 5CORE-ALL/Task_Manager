<?php

namespace Workdo\Taskly\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Workdo\Taskly\Entities\AutomateTask;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Carbon\Carbon;
use App\Models\FlagRaise;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProjectAutomateTaskDatatable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rowcolumn = ['title','priority','status','assign_to','assigner_name','start_date','end_date','group','checkbox','links','link_3','link_4','link_5','link_7','link_6','flag_raise'];
        $dataTable = (new EloquentDataTable($query))
        ->editColumn('status',function(AutomateTask $automateTask){
            $stage = $automateTask->stage;
            $html ='';
            
            // Replace status text values
            $displayStatus = $automateTask->stage_name;
            if ($displayStatus === 'Need Help') {
                $displayStatus = 'Help';
            } elseif ($displayStatus === 'Need Approval') {
                $displayStatus = 'Need App';
            } elseif ($displayStatus === 'Not Applicable') {
                $displayStatus = 'Inapplicable';
            }
            
            // Fixed width badge to keep consistent length
            $color = $stage ? $stage->color : '#6c757d';
            $html .= '<div> <span style="padding: 5px 10px;border-radius: 5px;color:white;background-color:'.$color.';display:inline-block;min-width:100px;text-align:center;"> '.$displayStatus.'</span></div>';
            return $html;
        })
        ->editColumn('title', function (AutomateTask $task) {
                return '<div style="word-wrap: break-word; white-space: normal; max-width: 200px;">' . e($task->title) . '</div>';
            })
        ->editColumn('group', function (AutomateTask $task) {
    return '<div style="word-wrap: break-word; white-space: normal; max-width: 120px;">' . e($task->group) . '</div>';
})

        ->editColumn('schedule_time',function(AutomateTask $task){
            return $task->schedule_time ?  date('H:i a', strtotime($task->schedule_time)) :"";
        })
        ->addColumn('checkbox', function (AutomateTask $task) {
            $html = '<input type="checkbox" class="task-checkbox" value="' . $task->id . '">';
            return $html;
        })
        ->editColumn('start_date',function(AutomateTask $automateTask){
            return $automateTask->start_date ?  date('Y-m-d', strtotime($automateTask->start_date)) :"";
        })
        ->editColumn('due_date',function(AutomateTask $automateTask){
            return $automateTask->due_date ?   date('Y-m-d', strtotime($automateTask->due_date)) : "";
        })
            ->filterColumn('assigner_name', function ($query, $keyword) {
        $query->where('assignor_users.name', 'like', "%$keyword%");
    })
    ->filterColumn('assign_to', function ($query, $keyword) {
        $query->whereRaw("EXISTS (SELECT 1 FROM users WHERE FIND_IN_SET(users.email, automate_tasks.assign_to) AND users.name LIKE ?)", ["%$keyword%"]);
    })
        
        // ->addColumn('assigner_name',function(Task $automateTask){
        //     $user = $automateTask->assignorUser();
        //     $html ='';
            
        //     if (check_file($user?->avatar) == false) {
        //         $path = asset('uploads/user-avatar/avatar.png');
        //     } else {
        //         $path = get_file($user?->avatar);
        //     }
        //     $html .= '<img  src="' . $path . '" data-bs-toggle="tooltip"  title="' . $user?->name . '" data-bs-placement="top"  class="rounded-circle" width="25" height="25">';
           
        //     return $html;
        // })
        
        ->addColumn('assigner_name', function (AutomateTask $automateTask) {
            $html = '';
        
            foreach ($automateTask->assignorUsers() as $user) {
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

        
        
        
        
       ->addColumn('links', function(AutomateTask $automateTask) {
            $links = '';
            $exportData = [];
        
            if ($automateTask->link1) {
                $links .= '<a href="' . $automateTask->link1 . '" target="_blank" class="mx-1" data-bs-toggle="tooltip" title="' . $automateTask->link1 . '">
                              <i class="fas fa-link"></i>
                          </a>';
                $exportData[] = 'L1: ' . $automateTask->link1;
            }
        
            if ($automateTask->link2) {
                $links .= '<a href="' . $automateTask->link2 . '" target="_blank" class="mx-1" data-bs-toggle="tooltip" title="' . $automateTask->link2 . '">
                              <i class="fas fa-external-link-alt"></i>
                          </a>';
                $exportData[] = 'L2: ' . $automateTask->link2;
            }
        
            // Add hidden span with export data
            if (!empty($exportData)) {
                $exportText = implode(' | ', $exportData);
                $links .= '<span style="display:none;" class="export-data">' . $exportText . '</span>';
            }
        
            return $links ?: '';
        })
        ->addColumn('link_3', function(AutomateTask $automateTask) {
            $links = '';
        
            if ($automateTask->link3) {
                $links .= '<a href="' . $automateTask->link3 . '" target="_blank" class="mx-1" data-bs-toggle="tooltip" title="' . $automateTask->link3 . '">
                              <i class="fas fa-link"></i>
                          </a>';
                $links .= '<span style="display:none;" class="export-data">' . $automateTask->link3 . '</span>';
            }
        
            return $links;
        })
         ->addColumn('link_4', function(AutomateTask $automateTask) {
            $links = '';
        
            if ($automateTask->link4) {
                $links .= '<a href="' . $automateTask->link4 . '" target="_blank" class="mx-1" data-bs-toggle="tooltip" title="' . $automateTask->link4 . '">
                              <i class="fas fa-link"></i>
                          </a>';
                $links .= '<span style="display:none;" class="export-data">' . $automateTask->link4 . '</span>';
            }
        
            return $links;
        })
        ->addColumn('link_5', function(AutomateTask $automateTask) {
            $links = '';
        
            if ($automateTask->link5) {
                $links .= '<a href="' . $automateTask->link5 . '" target="_blank" class="mx-1" data-bs-toggle="tooltip" title="' . $automateTask->link5 . '">
                              <i class="fas fa-link"></i>
                          </a>';
                $links .= '<span style="display:none;" class="export-data">' . $automateTask->link5 . '</span>';
            }
        
            return $links;
        })
        ->addColumn('link_7', function(AutomateTask $automateTask) {
            $links = '';
        
            if ($automateTask->link7) {
                $links .= '<a href="' . $automateTask->link7 . '" target="_blank" class="mx-1" data-bs-toggle="tooltip" title="' . $automateTask->link7 . '">
                              <i class="fas fa-link"></i>
                          </a>';
                $links .= '<span style="display:none;" class="export-data">' . $automateTask->link7 . '</span>';
            }
        
            return $links;
        })
        ->addColumn('link_6', function(AutomateTask $automateTask) {
            $links = '';
        
            if ($automateTask->link6) {
                $links .= '<a href="' . $automateTask->link6 . '" target="_blank" class="mx-1" data-bs-toggle="tooltip" title="' . $automateTask->link6 . '">
                              <i class="fas fa-link"></i>
                          </a>';
                $links .= '<span style="display:none;" class="export-data">' . $automateTask->link6 . '</span>';
            }
        
            return $links;
        })
        ->editColumn('assign_to',function(AutomateTask $automateTask){
            $html ='';
            foreach ($automateTask->users() as $user)
            {
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
        ->addColumn('flag_raise', function (AutomateTask $task) {
            // Check if task is flagged by looking for matching flag in flag_raises table
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
                // Match by assignee and task title in description (more flexible matching)
                if (empty($assigneeUserIds)) {
                    return '';
                }
                
                // Escape special characters for LIKE query
                $taskTitleEscaped = str_replace(['%', '_'], ['\%', '\_'], $task->title);
                
                // Match flags where:
                // 1. team_member_id matches any assignee (REQUIRED)
                // 2. AND description contains task title or starts with "Task: {title}"
                $matchingFlag = FlagRaise::whereIn('team_member_id', $assigneeUserIds)
                    ->where(function($query) use ($taskTitleEscaped) {
                        // Check if description contains task title (case-insensitive)
                        $query->where('description', 'like', "%{$taskTitleEscaped}%")
                              // OR check if description starts with "Task: {title}"
                              ->orWhere('description', 'like', "Task: {$taskTitleEscaped}%");
                    })
                    ->first();
                
                if ($matchingFlag) {
                    // Task is flagged - show flag icon with link to flag raise management
                    // Get flag_type directly from the matched flag record in storage
                    $flagType = strtolower(trim($matchingFlag->flag_type ?? 'red'));
                    // Display green color only if flag_type stored in database is 'green', otherwise red
                    $colorClass = ($flagType === 'green') ? 'text-success' : 'text-danger';
                    $flagIcon = '<a href="' . route('flag-raise.history') . '" target="_blank" title="Flag Raise Management" class="' . $colorClass . '" style="text-decoration: none;">
                                    <i class="fas fa-flag" style="font-size: 16px;"></i>
                                </a>';
                    return $flagIcon;
                }
                
                // Task is not flagged - return empty
                return '';
            } catch (\Exception $e) {
                \Log::error("Error checking flag raise for automate task {$task->id}: " . $e->getMessage());
                return '';
            }
        });
        if (\Laratrust::hasPermission('task show') || \Laratrust::hasPermission('task edit') || \Laratrust::hasPermission('task delete')) {
            $dataTable->addColumn('action', function (AutomateTask $task) {
                return view('taskly::projects.automate-task.task_action', compact('task'));
            });
            $rowcolumn[] = 'action';
        }
        return $dataTable->rawColumns($rowcolumn);

    }

    /**
     * Get the query source of dataTable.
     */
public function query(AutomateTask $model)
{
    $automateTask = $model->select('automate_tasks.*', 'stages.name as stage_name', 'assignor_users.name as assigner_name')
        ->join('stages', 'stages.name', '=', 'automate_tasks.status')
        ->leftJoin('users as assignor_users', 'assignor_users.email', '=', 'automate_tasks.assignor')
        ->where('automate_tasks.workspace', getActiveWorkSpace())
        ->groupBy('automate_tasks.id');

    $objUser = Auth::user();
    // if (request()->has('assignee_name') && !empty(request()->input('assignee_name'))) {
    //     $assigneeName = request()->input('assignee_name')[0];
    //     $automateTask->where(function ($query) use ($assigneeName) {
    //         $query->where('assign_to', 'like', "%$assigneeName%");
    //     });
    // }
    if (request()->has('assignee_name') && !empty(request()->input('assignee_name'))) {
        $assigneeEmails = request()->input('assignee_name');
        $automateTask->where(function ($query) use ($assigneeEmails) {
            foreach ($assigneeEmails as $email) {
                if ($email === 'NULL') {
                    // Check for NULL, empty, or tasks where no valid users exist for the assign_to emails
                    $query->orWhere(function ($q) {
                        $q->whereNull('assign_to')
                          ->orWhere('assign_to', '')
                          ->orWhereRaw("LENGTH(assign_to) > 0 AND (
                              SELECT COUNT(*) FROM users
                              WHERE FIND_IN_SET(users.email, automate_tasks.assign_to)
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
    //     $automateTask->where(function ($query) use ($assignorName) {
    //         $query->where('assignor', 'like', "%$assignorName%");
    //     });
    // }
    if (request()->has('assignor_name') && !empty(request()->input('assignor_name'))) {
        $assignorEmails = request()->input('assignor_name');
        $automateTask->where(function ($query) use ($assignorEmails) {
            foreach ($assignorEmails as $email) {
                if ($email === 'NULL') {
                    // Check for NULL, empty, or tasks where no valid users exist for the assignor emails
                    $query->orWhere(function ($q) {
                        $q->whereNull('assignor')
                          ->orWhere('assignor', '')
                          ->orWhereRaw("LENGTH(assignor) > 0 AND (
                              SELECT COUNT(*) FROM users
                              WHERE FIND_IN_SET(users.email, automate_tasks.assignor)
                          ) = 0");
                    });
                } else {
                    $query->orWhereRaw("FIND_IN_SET(?, assignor)", [$email]);
                }
            }
        });
    }
    if (request()->has('status_name') && !empty(request()->input('status_name'))) {
        $statusName = request()->input('status_name');
        $automateTask->where(function ($query) use ($statusName) {
            $query->where('status', 'like', "%$statusName%");
        });
    }
    if (request()->has('group_name') && !empty(request()->input('group_name'))) {
            $group_name = request()->input('group_name');
            $automateTask->where(function ($query) use ($group_name) {
                $query->where('group', 'like', "%$group_name%");
            });
    }
    if (request()->has('task_name') && !empty(request()->input('task_name'))) {
            $task_name = request()->input('task_name');
            $automateTask->where(function ($query) use ($task_name) {
                $query->where('title', 'like', "%$task_name%");
            });
    }
    // Default filtering removed - show all tasks by default when no filters are applied
    // if (!Auth::user()->hasRole('client') && !Auth::user()->hasRole('company') && !Auth::user()->hasRole('Manager All Access') && !Auth::user()->hasRole('hr')) {
    //     if (isset($objUser) && $objUser) {
    //         $automateTask->where(function ($query) use ($objUser) {
    //             $query->whereRaw("FIND_IN_SET(?, assign_to)", [$objUser->email])
    //                 ->orWhere('assignor', $objUser->email);
    //         });
    //     }
    // }

    // Add a condition to search by assignee names
    if (request()->has('search.value') && !empty(request()->input('search.value'))) {
        $searchValue = request()->input('search.value');
        $automateTask->where(function ($query) use ($searchValue) {
            $query->where('assignor_users.name', 'like', "%$searchValue%")
                ->orWhereRaw("EXISTS (SELECT 1 FROM users WHERE FIND_IN_SET(users.email, automate_tasks.assign_to) AND users.name LIKE ?)", ["%$searchValue%"]);
        });
    }

    // Add toggle filter support
    if (request()->has('toggle_filter') && !empty(request()->input('toggle_filter'))) {
        $toggleFilter = request()->input('toggle_filter');
        $currentDate = Carbon::now();

        if ($toggleFilter === 'overdue') {
            // Show all overdue tasks (due date before or equal to now)
            $automateTask->where('automate_tasks.due_date', '<=', $currentDate);
        } elseif ($toggleFilter === 'normal') {
            $automateTask->where('automate_tasks.priority', 'normal');
        } elseif ($toggleFilter === 'urgent') {
            $automateTask->where('automate_tasks.priority', 'urgent');
        } elseif ($toggleFilter === 'flag') {
            // Filter tasks that are marked as flag (visible in flag raise management)
            // A task is flagged if there's a matching flag in the flag_raises table
            // Match based on: flag's team_member_id matches task's assignee AND description contains task title
            $automateTask->whereExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('flag_raises')
                    // Check if flag's team_member_id (assignee) matches any task's assignee
                    ->whereExists(function($subQuery) {
                        $subQuery->select(DB::raw(1))
                            ->from('users as assignee_users')
                            ->whereColumn('assignee_users.id', 'flag_raises.team_member_id')
                            ->whereRaw("FIND_IN_SET(assignee_users.email, automate_tasks.assign_to) > 0");
                    })
                    // AND flag description contains task title (flexible matching)
                    ->where(function($q) {
                        $q->whereRaw("flag_raises.description LIKE CONCAT('%', automate_tasks.title, '%')")
                          ->orWhereRaw("flag_raises.description LIKE CONCAT('Task: ', automate_tasks.title, '%')");
                    });
            });
        }

        // For 'all', no extra filtering
    }

    return $automateTask;
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
            ->orderBy(1)
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
            ->lengthMenu([10, 25, 50, 100]) // Pagination options (10, 25, 50, 100 records per page)
            ->pageLength(50) 
            ->initComplete('function() {
                var table = this;
                var searchInput = $(\'#\'+table.api().table().container().id+\' label input[type="search"]\');
                searchInput.removeClass(\'form-control form-control-sm\');
                searchInput.addClass(\'dataTable-input\');
                var select = $(table.api().table().container()).find(".dataTables_length select").removeClass(\'custom-select custom-select-sm form-control form-control-sm\').addClass(\'dataTable-selector\');
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
                ],
            ],
        ];

        $buttonsConfig = array_merge([
            $exportButtonConfig,
            [
                'text' => '<i class="fas fa-trash"></i> ',
                'className' => 'btn btn-light-danger delete-group-btn',
                'attr' => ['id' => 'delete-btn', 'disabled' => 'disabled'],
                'action' => 'function(e, dt, node, config) {
                    let selectedIds = $(".task-checkbox:checked").map(function() { return this.value; }).get();
                    if (selectedIds.length > 0) {
                    }
                }'
            ],
            [
                'extend' => 'reset',
                'className' => 'btn btn-light-danger',
            ],
            [
                'extend' => 'reload',
                'className' => 'btn btn-light-warning',
            ],
        ]);

        $dataTable->parameters([
            "dom" =>  "
        <'dataTable-top'<'dataTable-dropdown page-dropdown'l><'dataTable-botton table-btn dataTable-search tb-search  d-flex justify-content-end gap-2'Bf>>
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
            Column::make('checkbox')->title('<input type="checkbox" id="select-all">')->orderable(false)
            ->searchable(false)
            ->exportable(false)
            ->printable(false)
            ->className('no-export'),
            Column::make('flag_raise')->title(__('Flag Raised?'))->html()->exportable(false)->searchable(false)->orderable(false),
             Column::make('group')->title(__('Group')),
            Column::make('title')->title(__('Title')),
            Column::make('assigner_name')->title(__('Assigner')),
            Column::make('assign_to')->title(__('Assignee'))->printable(false),
            Column::make('eta_time')->title(__('ETC (Min)')),
            Column::make('status')->title(__('Status'))->name('stages.name'),
            Column::make('links')->title(__('L1 & L2'))->exportable(true),
            Column::make('link_3')->title(__('TL'))->exportable(true),
            Column::make('link_4')->title(__('VL'))->exportable(true),
            Column::make('link_5')->title(__('Forms'))->exportable(true),
            Column::make('link_7')->title(__('FR'))->exportable(true),
            Column::make('link_6')->title(__('CL'))->exportable(true),
            Column::make('schedule_type')->title(__('Type'))
        ];
        if (\Laratrust::hasPermission('task show') || \Laratrust::hasPermission('task edit') || \Laratrust::hasPermission('task delete')) {
            $action = [
                Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('no-export')
                
            ];
            $column = array_merge($column,$action);
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
