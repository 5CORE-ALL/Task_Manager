<?php

namespace Workdo\Hrm\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Workdo\Hrm\Entities\Employee;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Str;

class EmployeeDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rowColumn = ['employee_id', 'name', 'email', 'phone', 'department_id', 'designation_id', 'company_doj', 'passport_country', 'country', 'state', 'city','zipcode'];
        $dataTable = (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('employee_id', function (User $employees) {
                if (!empty($employees->employee_id)) {
                    if (\Laratrust::hasPermission('employee show') && $employees->is_disable == 1) {
                        $url = route('employee.show', \Illuminate\Support\Facades\Crypt::encrypt($employees->id));
                        $emp_id = Employee::employeeIdFormat($employees->employee_id);
                        $html = '<a class="btn btn-outline-primary" href="' . $url . '">
                                        ' . $emp_id . '
                                    </a>';
                        return $html;
                    } else {
                        $emp_id = Employee::employeeIdFormat($employees->employee_id);
                        $html = '<a href="#" class="btn btn-outline-primary">' . $emp_id . '</a>';
                        return $html;
                    }
                } else {
                    $html = '--';
                    return $html;
                }
            })
            // ->editColumn('name', function (User $employees) {
            //     return $employees->name ?? '-';
            // })
            ->editColumn('name', function (User $employees) {
                $html = '';
                if ($employees->name) {
                    $firstName = explode(' ', $employees->name)[0];
                    $html .= '<div class="d-flex align-items-center gap-2">';
                    // Check if avatar exists, otherwise use a blank image or placeholder
                    if (check_file($employees->avatar)) {
                        $path = get_file($employees->avatar);
                    } else {
                        $path = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/wcAAgAB/1h8JAAAAABJRU5ErkJggg=='; // Blank image
                    }
                    $html .= '<img src="' . $path . '" data-bs-toggle="tooltip" title="' . $employees->name . '" data-bs-placement="top" class="rounded-circle" width="25" height="25">';
                    $html .= '<span>' . $firstName . '</span>'; // Display only the first name
                    $html .= '</div>';
                } else {
                    $html .= '-';
                }
                return $html;
            })
            ->editColumn('email', function (User $employees) {
                $email = $employees->email ?? '-';
                if ($email !== '-') {
                    $email = wordwrap($email, 20, "<br>", true); // Break after 20 characters
                }
                return $email;
            })
            ->editColumn('branch_id', function (User $employees) {
                return $employees->branches_name ?? '-';
            })
            ->editColumn('department_id', function (User $employees) {
                $department = $employees->departments_name ?? '-';
                if ($department !== '-') {
                    $department = wordwrap($department, 20, "<br>", true); // Break after 20 characters
                }
                return $department;
            })
            ->editColumn('designation_id', function (User $employees) {
                $designation = $employees->designations_name ?? '-';
                if ($designation !== '-') {
                    $designation = wordwrap($designation, 20, "<br>", true); // Break after 20 characters
                }
                return $designation;
            })
            ->editColumn('company_doj', function (User $employees) {
                return $employees->company_doj ? company_date_formate($employees->company_doj ?? '-') : '-';
            })
            ->editColumn('passport_country', function (User $employees) {
                if (!empty($employees->passport_country)) {
                    // Always show link icon for any content
                    $url = filter_var($employees->passport_country, FILTER_VALIDATE_URL) ? $employees->passport_country : '#';
                    $tooltip = filter_var($employees->passport_country, FILTER_VALIDATE_URL) ? 'Open Link' : htmlspecialchars($employees->passport_country);
                    
                    return '<a href="' . $url . '" target="_blank" class="btn btn-sm btn-warning text-white" data-bs-toggle="tooltip" title="' . $tooltip . '" style="border-radius: 50%; padding: 4px 8px;">
                                <i class="fas fa-link"></i>
                            </a>';
                } else {
                    return '-';
                }
            })
            ->editColumn('country', function (User $employees) {
                if (!empty($employees->country)) {
                    // Always show link icon for any content
                    $url = filter_var($employees->country, FILTER_VALIDATE_URL) ? $employees->country : '#';
                    $tooltip = filter_var($employees->country, FILTER_VALIDATE_URL) ? 'Open Link' : htmlspecialchars($employees->country);
                    
                    return '<a href="' . $url . '" target="_blank" class="btn btn-sm btn-warning text-white" data-bs-toggle="tooltip" title="' . $tooltip . '" style="border-radius: 50%; padding: 4px 8px;">
                                <i class="fas fa-link"></i>
                            </a>';
                } else {
                    return '-';
                }
            })
            ->editColumn('state', function (User $employees) {
                if (!empty($employees->state)) {
                    // Always show link icon for any content
                    $url = filter_var($employees->state, FILTER_VALIDATE_URL) ? $employees->state : '#';
                    $tooltip = filter_var($employees->state, FILTER_VALIDATE_URL) ? 'Open Link' : htmlspecialchars($employees->state);
                    
                    return '<a href="' . $url . '" target="_blank" class="btn btn-sm btn-warning text-white" data-bs-toggle="tooltip" title="' . $tooltip . '" style="border-radius: 50%; padding: 4px 8px;">
                                <i class="fas fa-link"></i>
                            </a>';
                } else {
                    return '-';
                }
            })
            ->editColumn('city', function (User $employees) {
                if (!empty($employees->city)) {
                    // Always show link icon for any content
                    $url = filter_var($employees->city, FILTER_VALIDATE_URL) ? $employees->city : '#';
                    $tooltip = filter_var($employees->city, FILTER_VALIDATE_URL) ? 'Open Link' : htmlspecialchars($employees->city);
                    
                    return '<a href="' . $url . '" target="_blank" class="btn btn-sm btn-warning text-white" data-bs-toggle="tooltip" title="' . $tooltip . '" style="border-radius: 50%; padding: 4px 8px;">
                                <i class="fas fa-link"></i>
                            </a>';
                } else {
                    return '-';
                }
            })
            ->editColumn('zipcode', function (User $employees) {
                if (!empty($employees->zipcode)) {
                    // Always show link icon for any content
                    $url = filter_var($employees->zipcode, FILTER_VALIDATE_URL) ? $employees->zipcode : '#';
                    $tooltip = filter_var($employees->zipcode, FILTER_VALIDATE_URL) ? 'Open Link' : htmlspecialchars($employees->zipcode);
                    
                    return '<a href="' . $url . '" target="_blank" class="btn btn-sm btn-warning text-white" data-bs-toggle="tooltip" title="' . $tooltip . '" style="border-radius: 50%; padding: 4px 8px;">
                                <i class="fas fa-link"></i>
                            </a>';
                } else {
                    return '-';
                }
            });
            
        // if (\Laratrust::hasPermission('employee show') || \Laratrust::hasPermission('employee edit') || \Laratrust::hasPermission('employee delete')) {
        //     $dataTable->addColumn('action', function (User $employees) {
        //         return view('hrm::employee.button', compact('employees'));
        //     });
        //     $rowColumn[] = 'action';
        // }
        // In the dataTable() method, modify the action column addition:
// In the dataTable() method, modify the action column addition:
// if (\Laratrust::hasPermission('employee show') || \Laratrust::hasPermission('employee edit') || \Laratrust::hasPermission('employee delete')) {
//     $dataTable->addColumn('action', function (User $employees) {
//         $authUser = Auth::user();
        
//         // Full access for president@5core.com
//         if ($authUser->email === 'president@5core.com') {
//             return view('hrm::employee.button', compact('employees'));
//         }
//         // Regular users can only see their own action button
//         elseif ($employees->user_id == $authUser->id) { // Compare user_id instead of id
//             return view('hrm::employee.button', compact('employees'));
//         }
//         return ''; // Hide for others
//     });
//     $rowColumn[] = 'action';
// }
if (\Laratrust::hasPermission('employee show') || \Laratrust::hasPermission('employee edit') || \Laratrust::hasPermission('employee delete')) {
    $dataTable->addColumn('action', function (User $employees) {
        $authUser = Auth::user();
        
        // List of emails with full access
        $privilegedEmails = [
            'president@5core.com',
            'hr@5core.com',
            'rupak.manna99@gmail.com'
        ];
        
        // Full access for privileged emails
        if (in_array($authUser->email, $privilegedEmails)) {
            return view('hrm::employee.button', compact('employees'));
        }
        // Regular users can only see their own action button
        elseif ($employees->user_id == $authUser->id) {
            return view('hrm::employee.button', compact('employees'));
        }
        return ''; // Hide for others
    });
    $rowColumn[] = 'action';
}
        return $dataTable->rawColumns($rowColumn);
    }

    /**
     * Get the query source of dataTable.
     */
    // public function query(User $model, Request $request): QueryBuilder
    // {
    //     if (!in_array(Auth::user()->type, Auth::user()->not_emp_type)) {
    //         $employees = $model->where('workspace_id', getActiveWorkSpace())
    //             ->leftJoin('employees', 'users.id', '=', 'employees.user_id')
    //             ->leftJoin('branches', 'employees.branch_id', '=', 'branches.id')
    //             ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
    //             ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
    //             ->where('users.id', Auth::user()->id)
    //             ->select('users.*', 'users.id as ID', 'employees.*', 'users.name as name', 'users.email as email', 'users.id as id', 'branches.name as branches_name', 'departments.name as departments_name', 'designations.name as designations_name');
    //     } elseif (Auth::user()->isAbleTo('employee manage')) {
    //         $employees = $model->where('workspace_id', getActiveWorkSpace())
    //             ->leftJoin('employees', 'users.id', '=', 'employees.user_id')
    //             ->leftJoin('branches', 'employees.branch_id', '=', 'branches.id')
    //             ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
    //             ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
    //             ->where('users.created_by', creatorId())->emp()
    //             ->select('users.*', 'users.id as ID', 'employees.*', 'users.name as name', 'users.email as email', 'users.id as id', 'branches.name as branches_name', 'departments.name as departments_name', 'designations.name as designations_name');
    //     }

    //     return $employees;
    // }


    public function query(User $model, Request $request): QueryBuilder
{
    // Fetch all employees in the active workspace
    $employees = $model->where('workspace_id', getActiveWorkSpace())
        ->leftJoin('employees', 'users.id', '=', 'employees.user_id')
        ->leftJoin('branches', 'employees.branch_id', '=', 'branches.id')
        ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
        ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
        ->select(
            'users.*',
            'users.id as ID',
            'employees.*',
            'users.name as name',
            'users.email as email',
            'users.id as id',
            'branches.name as branches_name',
            'departments.name as departments_name',
            'designations.name as designations_name'
        );

    return $employees;
}
    
    
//     public function query(User $model, Request $request): QueryBuilder
// {
//     return $model->where('workspace_id', getActiveWorkSpace())
//         ->leftJoin('employees', 'users.id', '=', 'employees.user_id')
//         ->leftJoin('branches', 'employees.branch_id', '=', 'branches.id')
//         ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
//         ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
//         ->select('users.*', 'employees.*', 'branches.name as branches_name', 'departments.name as departments_name', 'designations.name as designations_name');
// }


    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('employees-table')
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
                ],
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

        // $dataTable->parameters([
        //     "dom" =>  "
        // <'dataTable-top'<'dataTable-dropdown page-dropdown'l><'dataTable-botton table-btn dataTable-search tb-search  d-flex justify-content-end gap-2'Bf>>
        // <'dataTable-container'<'col-sm-12'tr>>
        // <'dataTable-bottom row'<'col-5'i><'col-7'p>>",
        //     'buttons' => $buttonsConfig,
        //     "drawCallback" => 'function( settings ) {
        //         var tooltipTriggerList = [].slice.call(
        //             document.querySelectorAll("[data-bs-toggle=tooltip]")
        //           );
        //           var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        //             return new bootstrap.Tooltip(tooltipTriggerEl);
        //           });
        //           var popoverTriggerList = [].slice.call(
        //             document.querySelectorAll("[data-bs-toggle=popover]")
        //           );
        //           var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        //             return new bootstrap.Popover(popoverTriggerEl);
        //           });
        //           var toastElList = [].slice.call(document.querySelectorAll(".toast"));
        //           var toastList = toastElList.map(function (toastEl) {
        //             return new bootstrap.Toast(toastEl);
        //           });
        //     }'
        // ]);
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
            // 'pageLength' => 50 
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
        $company_settings = getCompanyAllSetting();
        $column = [
            Column::make('id')->name('users.id')->searchable(false)->visible(false)->exportable(false)->printable(false),
            Column::make('No')->title(__('No'))->data('DT_RowIndex')->name('DT_RowIndex')->searchable(false)->orderable(false)->visible(false),
            Column::make('employee_id')->title(__('Employee ID'))->name('users.id')->searchable(true)->visible(false),
            Column::make('name')->title(__('Name'))->name('users.name')->searchable(true),
            Column::make('email')->title(__('Email'))->name('users.email')->searchable(true),
            Column::make('phone')->title(__('phone'))->name('users.phone')->searchable(false),
            // Column::make('branch_id')->title(!empty($company_settings['hrm_branch_name']) ? $company_settings['hrm_branch_name'] : __('Branch'))->name('branches.name'),
            Column::make('department_id')->title(!empty($company_settings['hrm_department_name']) ? $company_settings['hrm_department_name'] : __('Department'))->name('departments.name'),
            Column::make('designation_id')->title(!empty($company_settings['hrm_designation_name']) ? $company_settings['hrm_designation_name'] : __('Designation'))->name(('designations.name')),
            Column::make('passport')->title(__('Responsibility'))->name('employees.passport')->searchable(false),
            Column::make('passport_country')->title(__('Accountability'))->name('employees.passport_country')->searchable(false),
            Column::make('country')->title(__('Tools'))->name('employees.country')->searchable(false),
            Column::make('state')->title(__('R&R'))->name('employees.state')->searchable(false),
            Column::make('city')->title(__('Incentive'))->name('employees.city')->searchable(false),
            Column::make('zipcode')->title(__('FFP/FFQ'))->name('employees.zipcode')->searchable(false),
            // Column::make('company_doj')->title(__('Date Of Joining'))->name('employees.company_doj'),
        ];
        if (
            \Laratrust::hasPermission('employee show') ||
            \Laratrust::hasPermission('employee edit') ||
            \Laratrust::hasPermission('employee delete')
        ) {
            $action = [
                Column::computed('action')
                    ->title(__('Action'))
                    ->exportable(false)
                    ->printable(false)
                    ->width(60)

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
        return 'Employees_' . date('YmdHis');
    }
}
