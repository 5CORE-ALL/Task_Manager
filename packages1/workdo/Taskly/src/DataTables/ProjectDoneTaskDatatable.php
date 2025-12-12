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


class ProjectDoneTaskDatatable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */


    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rowcolumn = ['title', 'priority', 'status', 'assign_to', 'assigner_name', 'start_date', 'end_date', 'group', 'links', 'created_at', 'checkbox', 'due_date', 'completion_date'];
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
                $html .= '<div> <span class="editable" data-id="' . $task->id . '" colspan="4" data-column="status" style="padding: 5px 10px;border-radius: 5px;color:white;cursor:pointer;background-color:' . $color . ';display:inline-block;min-width:100px;text-align:center;"> ' . $displayStatus . '</span></div>';
                return $html;
            })


            ->editColumn('title', function (Task $task) {
                $sub_tasks = $task->sub_tasks;
                
                $taskHtml = '<div><span class="editable" colspan="4" style="word-wrap: break-word; white-space: normal; max-width: 200px;" 
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

            ->editColumn('start_date', function (Task $task) {
                return $task->start_date ?  date('m-d', strtotime($task->start_date)) : "";
            })
            ->addColumn('checkbox', function (Task $task) {
                $html = '<input type="checkbox" class="task-checkbox" value="' . $task->id . '">';
                return $html;
            })->editColumn('priority', function (Task $task) {
                $color = match (strtolower($task->priority)) {
                    'high' => 'danger',
                    'medium' => 'warning',
                    'low' => 'success',
                    default => 'secondary',
                };
                $html = '';
                $html .= '<div> <span  data-id="' . $task->id . '" data-column="priority" colspan="4" class="editable btn btn-sm btn-' . $color . '"> ' . ucfirst($task->priority) . '</div>';
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
                $dueDate = $task->due_date ? date('m-d', strtotime($task->due_date)) : "";
                $color = (strtotime($task->due_date) < time()) ? 'red' : 'black'; // Blue if due date has passed, red otherwise

                return $dueDate ? '<span style="color: ' . $color . '; font-weight: bold;">' . $dueDate . '</span>' : "";
            })
             ->editColumn('completion_date', function (Task $task) {
                $formattedDate = $task->completion_date && $task->completion_date != '0000-00-00' && $task->completion_date != '0000-00-00 00:00:00' 
                    ? date('m-d', strtotime($task->completion_date)) 
                    : "-";
                return '<span data-raw-date="' . ($task->completion_date ?: '') . '">' . $formattedDate . '</span>';
            })
            ->addColumn('teamlogger', function (Task $task) {
                // Return teamlogger data if it exists, otherwise return empty
                return $task->teamlogger ?? '';
            })


            ->filterColumn('assigner_name', function ($query, $keyword) {
                $query->where('assignor_users.name', 'like', "%$keyword%");
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

                if ($task->link1) {
                    $links .= '<a href="' . $task->link1 . '" target="_blank" class="mx-1" data-bs-toggle="tooltip" title="' . $task->link1 . '">
                              <i class="fas fa-link"></i>
                          </a>';
                }

                if ($task->link2) {
                    $links .= '<a href="' . $task->link2 . '" target="_blank" class="mx-1" data-bs-toggle="tooltip" title="' . $task->link2 . '">
                              <i class="fas fa-external-link-alt"></i>
                          </a>';
                }

                return $links;
            })
            ->addColumn('link_7', function (Task $task) {
                $links = '';
                if ($task->link7) {
                    $links .= '<a href="' . $task->link7 . '" target="_blank" class="mx-1" data-bs-toggle="tooltip" title="' . $task->link7 . '">
                              <i class="fas fa-link"></i>
                          </a>';
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
            ->addColumn('completion_day', function (Task $task) {
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
        $task = $model->select('tasks.*', 'stages.name as stage_name', 'assignor_users.name as assigner_name')
            ->join('stages', 'stages.name', '=', 'tasks.status')
            ->where('deleted_at','!=',NULL)->where('is_missed',0)
            ->where('status', "Done")
            ->leftJoin('users as assignor_users', 'assignor_users.email', '=', 'tasks.assignor')
            ->where('tasks.workspace', getActiveWorkSpace())->groupBy('tasks.id');

        $objUser = Auth::user();
                // Define allowed emails that can see all done tasks
        $allowedEmails = [
            'president@5core.com',
            'hr@5core.com',
            'tech-support@5core.com',
            'support@5core.com',
            'mgr-advertisement@5core.com',
            'mgr-content@5core.com',
            'ritu.kaur013@gmail.com',
            'sjoy7486@gmail.com',
            'ecomm2@5core.com',
            'sr.manager@5core.com',
            'inventory@5core.com',
        ];
        
        $currentUserEmail = $objUser->email ?? '';
        $hasEmailAccess = in_array($currentUserEmail, $allowedEmails);
        
        // If user is NOT in allowed emails list, only show their own tasks
        // if (!$hasEmailAccess) {
        //     $task->where(function ($query) use ($objUser) {
        //         $query->whereRaw("FIND_IN_SET(?, assign_to)", [$objUser->email]);
        //             //   ->orWhereRaw("FIND_IN_SET(?, assignor)", [$objUser->email]);
        //     });
        // }
        // If user IS in allowed emails list, they can see all tasks (no additional filter needed)
        
        if (request()->has('assignee_name') && !empty(request()->input('assignee_name'))) {
            $assigneeEmails = request()->input('assignee_name');
            $task->where(function ($query) use ($assigneeEmails) {
                foreach ($assigneeEmails as $email) {
                    $query->orWhereRaw("FIND_IN_SET(?, assign_to)", [$email]);
                }
            });
        }
        if (request()->has('assignor_name') && !empty(request()->input('assignor_name'))) {
            $assignorEmails = request()->input('assignor_name');
            $task->where(function ($query) use ($assignorEmails) {
                foreach ($assignorEmails as $email) {
                    $query->orWhereRaw("FIND_IN_SET(?, assignor)", [$email]);
                }
            });
        }
        // Use start_date for month filter instead of deleted_at
        if (request()->has('month') && !empty(request()->input('month'))) {
            $month = request()->input('month');
            $task->whereMonth('tasks.completion_date', $month);
        }

        // Date Filter (completion_date)
        if (request()->has('date_filter') && !empty(request()->input('date_filter'))) {
            $dateFilter = request()->input('date_filter');
            $carbon = \Carbon\Carbon::now();
            if ($dateFilter === 'today') {
                $task->whereDate('tasks.completion_date', $carbon->toDateString());
            } elseif ($dateFilter === 'yesterday') {
                $task->whereDate('tasks.completion_date', $carbon->copy()->subDay()->toDateString());
            } elseif ($dateFilter === 'this_week') {
                $task->whereBetween('tasks.completion_date', [$carbon->copy()->startOfWeek()->toDateString(), $carbon->copy()->endOfWeek()->toDateString()]);
            } elseif ($dateFilter === 'this_month') {
                $task->whereMonth('tasks.completion_date', $carbon->month)
                     ->whereYear('tasks.completion_date', $carbon->year);
            } elseif ($dateFilter === 'previous_month') {
                $prevMonth = $carbon->copy()->subMonth();
                $task->whereMonth('tasks.completion_date', $prevMonth->month)
                     ->whereYear('tasks.completion_date', $prevMonth->year);
            } elseif ($dateFilter === 'last_30_days') {
                $task->whereBetween('tasks.completion_date', [$carbon->copy()->subDays(30)->toDateString(), $carbon->toDateString()]);
            } elseif ($dateFilter === 'custom') {
                // Handle custom date range
                $startDate = request()->input('start_date');
                $endDate = request()->input('end_date');
                
                if (!empty($startDate) && !empty($endDate)) {
                    // Ensure proper date format and handle both date and datetime
                    $startDate = \Carbon\Carbon::parse($startDate)->startOfDay()->format('Y-m-d H:i:s');
                    $endDate = \Carbon\Carbon::parse($endDate)->endOfDay()->format('Y-m-d H:i:s');
                    
                    $task->whereBetween('tasks.completion_date', [$startDate, $endDate]);
                } elseif (!empty($startDate)) {
                    $startDate = \Carbon\Carbon::parse($startDate)->format('Y-m-d');
                    $task->whereDate('tasks.completion_date', '>=', $startDate);
                } elseif (!empty($endDate)) {
                    $endDate = \Carbon\Carbon::parse($endDate)->format('Y-m-d');
                    $task->whereDate('tasks.completion_date', '<=', $endDate);
                }
            }
        }
        if (request()->has('group_name') && !empty(request()->input('group_name'))) {
            $groupName = request()->input('group_name');
            $task->where('tasks.group', 'like', "%$groupName%");
        }
        // Add task_name filter for DataTable
        if (request()->has('task_name') && !empty(request()->input('task_name'))) {
            $taskName = request()->input('task_name');
            $task->where('tasks.title', 'like', "%$taskName%");
        }

        // Add a condition to search by assignee names
        if (request()->has('search.value') && !empty(request()->input('search.value'))) {
            $searchValue = request()->input('search.value');
            $task->where(function ($query) use ($searchValue) {
                $query->where('assignor_users.name', 'like', "%$searchValue%")
                    ->orWhereRaw("EXISTS (SELECT 1 FROM users WHERE FIND_IN_SET(users.email, tasks.assign_to) AND users.name LIKE ?)", ["%$searchValue%"]);
            });
        }
        // Order by completion_date DESC (newest first)
        $task->orderBy('tasks.completion_date', 'DESC');
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
                    'exportOptions' => ['columns' => [0, 1, 3]],
                ],
                [
                    'extend' => 'csv',
                    'text' => '<i class="fas fa-file-csv me-2"></i> ' . __('CSV'),
                    'className' => 'btn btn-light text-primary dropdown-item',
                    'exportOptions' => ['columns' => [0, 1, 3]],
                ],
                [
                    'extend' => 'excel',
                    'text' => '<i class="fas fa-file-excel me-2"></i> ' . __('Excel'),
                    'className' => 'btn btn-light text-primary dropdown-item',
                    'exportOptions' => ['columns' => [0, 1, 3]],
                ]
            ],
        ];

        $buttonsConfig = array_merge([
            $exportButtonConfig,
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
            }',
            "rowCallback" => 'function(row, data, index) {
                if (data.task_type=="automate_task") { // Replace with your column field
                    $(row).addClass("automate-task");
                }
                
                // Track completion date changes for divider rows
                var table = this.api();
                if (index > 0) {
                    var prevData = table.row(index - 1).data();
                    // Check if completion date is different from previous row
                    if (prevData && data.completion_date && prevData.completion_date !== data.completion_date) {
                        // Apply a top border to the current row instead of inserting a new row
                        $(row).css("border-top", "5px solid #00c4f5ff");
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
          
            Column::make('group')->title(__('Group'))->searchable(true),
            Column::make('title')->title(__('Task'))->width('600px'),
            Column::make('assigner_name')->title(__('Assignor')),
            Column::make('assign_to')->title(__('Assignee'))->printable(false),
            Column::make('start_date')->title(__('TID'))->exportable(false)->searchable(false),
            Column::make('due_date')->title(__('Due'))->exportable(false)->searchable(false),
            Column::make('eta_time')->title(__('ETC'))->exportable(false)->searchable(false),
            Column::make('etc_done')->title(__('ATC'))->exportable(false)->searchable(false),
            Column::make('completion_date')->title(__('TCD'))->exportable(false)->searchable(false),
            Column::make('completion_day')->title(__('CDAYS'))->exportable(false)->searchable(false),
            // Column::make('teamlogger')->title(__('Teamlogger'))->exportable(false)->searchable(false)->visible(false),

            Column::make('status')->title(__('Status'))->name('stages.name'),
            Column::make('priority')->title(__('Priority')),
            Column::make('links')->title(__('L1 & L2')),
            Column::make('link7')->title(__('FR')),
            // Column::make('file')->title(__('File')),
            // Column::make('schedule_type')->title(__('Type')),
            // Column::make('schedule_time')->title(__('Create Time'))
        ];
        
        //  if (\Laratrust::hasPermission('task show') || \Laratrust::hasPermission('task edit') || \Laratrust::hasPermission('task delete')) {
        //     $action = [
        //         Column::computed('action')
        //             ->exportable(false)
        //             ->printable(false)
        //             ->width(60)

        //     ];
        //     $column = array_merge($column, $action);
        // }
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
