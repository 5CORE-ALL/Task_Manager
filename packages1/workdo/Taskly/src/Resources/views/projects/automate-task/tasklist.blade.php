@extends('layouts.main')
@section('page-title')
    {{ __('Automate Task Board') }}
@endsection
@section('title')
    {{ __('Automate Task Board') }}
@endsection
@section('page-breadcrumb')
    {{ __('Project') }},{{ __('Project Details') }},{{ __(' Automate Task Board') }}
@endsection

@push('css')
    @include('layouts.includes.datatable-css')
    <style>
        /* Task Toggle Styles - Enhanced and Beautiful */
        .task-toggle-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            padding: 8px 12px;
            background: #f8f9fa;
            border-radius: 8px;
            height: 42px;
        }

        .toggle-indicator {
            position: absolute;
            top: 3px;
            left: 3px;
            width: calc(25% - 6px);
            height: calc(100% - 6px);
            background: #6c757d;
            border-radius: 6px;
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            pointer-events: none;
            z-index: 1;
        }

        .toggle[data-state="all"] .toggle-indicator { 
            left: 3px; 
            background: #6c757d; 
        }
        .toggle[data-state="overdue"] .toggle-indicator { 
            left: calc(25% + 3px); 
            background: #dc3545; 
        }
        .toggle[data-state="urgent"] .toggle-indicator { 
            left: calc(50% + 3px); 
            background: #ffc107; 
        }
        .toggle[data-state="flag"] .toggle-indicator { 
            left: calc(75% + 3px); 
            background: #fd7e14; 
        }

        .toggle[data-state="all"] .toggle-option[data-value="all"],
        .toggle[data-state="overdue"] .toggle-option[data-value="overdue"],
        .toggle[data-state="urgent"] .toggle-option[data-value="urgent"],
        .toggle[data-state="flag"] .toggle-option[data-value="flag"] {
            color: #fff;
            font-weight: 700;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .toggle {
            position: relative;
            display: flex;
            width: 100%;
            max-width: 600px;
            height: 100%;
            background: #fff;
            border-radius: 6px;
            box-sizing: border-box;
            padding: 3px;
            margin: 0 auto;
        }

        .toggle-option {
            flex: 0 0 25%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 2;
            user-select: none;
            font-weight: 600;
            font-size: 13px;
            color: #6c757d;
            transition: all 0.3s ease;
            border-radius: 6px;
            position: relative;
            height: 100%;
        }

        .toggle-option:hover {
            color: #495057;
            transform: translateY(-2px);
        }

        .toggle-option:active {
            transform: scale(0.98);
        }
    </style>
@endpush
@section('page-action')
    <div class="d-flex">
        @stack('addButtonHook')
      
        @permission('task create')
            <a class="btn btn-sm btn-primary me-2 add-task" data-ajax-popup="true" data-size="lg" data-title="{{ __('Create New Task') }}"
                data-url="{{ route('automate.tasks.create') }}" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Create') }}"><i
                    class="ti ti-plus"></i></a>
                    
             <a class="btn btn-sm btn-primary me-2" data-ajax-popup="true" data-size="lg" data-title="{{ __('Import Automated Task') }}"
                data-url="{{ route('automate.tasks.import') }}" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Import') }}"><i
                    class="fa fa-upload"></i></a>
                    
             <a class="btn btn-sm btn-info me-2" href="{{ route('automate.tasks.report') }}" target="_blank" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Report') }}"><i
                    class="ti ti-file-report"></i></a>
        @endpermission
        
    </div>
@endsection
@section('filter')
@endsection

@section('content')
    {{-- Bulk Actions Bar with Filter Section --}}
    <div class="card mb-3" style="position: sticky; top: 0; z-index: 1000; border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <div class="card-body py-2 px-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div id="bulk-actions-bar" style="display: none; flex: 1;" class="d-flex align-items-center gap-2">
                    <span class="text-dark me-3" style="font-weight: 600;">
                        <i class="fas fa-tasks me-2"></i>
                        <span id="selected-count">0</span> task(s) selected
                    </span>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-sm btn-primary" id="bulk-status-update-btn-top" disabled data-bs-toggle="tooltip" data-bs-placement="top" title="Update Status">
                            <i class="fas fa-tasks me-1"></i> Status
                        </button>
                        <button type="button" class="btn btn-sm btn-primary" id="bulk-assignor-update-btn-top" disabled data-bs-toggle="tooltip" data-bs-placement="top" title="Update Assignor">
                            <i class="fas fa-user-edit me-1"></i> Assignor
                        </button>
                        <button type="button" class="btn btn-sm btn-primary" id="bulk-assignee-update-btn-top" disabled data-bs-toggle="tooltip" data-bs-placement="top" title="Update Assignee">
                            <i class="fas fa-user-plus me-1"></i> Assignee
                        </button>
                        <button type="button" class="btn btn-sm btn-primary" id="bulk-etc-update-btn-top" disabled data-bs-toggle="tooltip" data-bs-placement="top" title="Update ETC">
                            <i class="fas fa-clock me-1"></i> ETC
                        </button>
                        <button type="button" class="btn btn-sm btn-primary" id="bulk-date-update-btn-top" disabled data-bs-toggle="tooltip" data-bs-placement="top" title="Update Dates">
                            <i class="fas fa-calendar me-1"></i> Dates
                        </button>
                        <button type="button" class="btn btn-sm btn-primary" id="bulk-priority-update-btn-top" disabled data-bs-toggle="tooltip" data-bs-placement="top" title="Update Priority">
                            <i class="fas fa-flag me-1"></i> Priority
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" id="delete-btn-top" disabled data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Selected">
                            <i class="fas fa-trash me-1"></i> Delete
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary" id="clear-selection-btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Clear Selection">
                            <i class="fas fa-times me-1"></i> Clear
                        </button>
                    </div>
                </div>
                <div class="task-toggle-wrapper" style="flex-shrink: 0;">
                    <!-- Toggle will be inserted here by JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="container mt-5">
                <div class="row mt-5 align-items-center">
                   
                    <div class="col-md-3">
                        <div class="card text-white bg-success mb-3">
                            <div class="card-body text-center">
                                <h5 class="card-title">Total Tasks</h5>
                                <h2 class="card-text" id="pending-count">{{ $totalTask }}</h2>
                            </div>
                        </div>
                    </div>
                                    <!-- New ETA(hrs) Card -->
                <!--<div class="col-md-3">-->
                <!--    <div class="card text-white bg-primary mb-3">-->
                <!--        <div class="card-body text-center">-->
                <!--            <h5 class="card-title">ETC (hrs)</h5>-->
                <!--            <h2 class="card-text" id="eta-hours">{{ $totalEtaHours ?? 0 }}</h2> -->
                <!--        </div>-->
                <!--    </div>-->
                <!--</div>-->
                
                <!-- End ETA(hrs) Card -->

    <!-- Weekly Card -->
                <div class="col-md-3">
                    <div class="card text-white bg-warning mb-3">
                        <div class="card-body text-center">
                            <h5 class="card-title">Weekly</h5>
                            <h2 class="card-text" id="weekly-count">0</h2>
                        </div>
                    </div>
                </div>
                <!--  <div class="col-md-2">-->
                <!--    <div class="card text-white bg-info mb-3">-->
                <!--        <div class="card-body text-center">-->
                <!--            <h5 class="card-title">Daily(Min)</h5>-->
                <!--            <h2 class="card-text" id="wday-count">0</h2>-->
                <!--        </div>-->
                <!--    </div>-->
                <!--</div>-->
                 <div class="col-md-3">
                    <div class="card text-white bg-warning mb-3">
                        <div class="card-body text-center">
                            <h5 class="card-title">Monthly</h5>
                            <h2 class="card-text" id="monthly-count">0</h2>
                        </div>
                    </div>
                </div>
                  <div class="col-md-3">
                    <div class="card text-white bg-info mb-3">
                        <div class="card-body text-center">
                            <h5 class="card-title">Daily</h5>
                            <h2 class="card-text" id="mday-count">0</h2>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-12 mb-2">
                        <form class="d-flex gap-2 align-items-center">
                           
                             <div class="flex-grow-1">
                                <label class="form-label">{{ __('Group')}}</label>
                                <input type="text" class="form-control form-control-light" id="group_name" name="group_name" placeholder="{{ __('Enter Group') }}">
                            </div>
                             <div class="flex-grow-1">
                                <label class="form-label">{{ __('Title')}}</label>
                                <input type="text" class="form-control form-control-light" id="task_name" name="task_name" placeholder="{{ __('Enter title') }}">
                            </div>
                            <div class="flex-grow-1">
                                <label class="form-label">{{ __('Assignor')}}</label>
                                <select class=" multi-select choices" id="assignor_name" multiple="multiple" name="assignor_name" data-placeholder="{{ __('Select Users ...') }}" required>
                                    <option value="">{{__('Select assignor')}}</option>
                                    <option value="NULL">{{__('No Assignor (NULL)')}}</option>
                                    @foreach($users as $u)
                                        <option value="{{$u->email}}">{{$u->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label class="form-label">{{ __('Assignee')}}</label>
                                <select class=" multi-select choices" id="assignee_name" multiple="multiple" name="assignee_name" data-placeholder="{{ __('Select Users ...') }}" required>
                                    <option value="">{{__('Select Assignee')}}</option>
                                    <option value="NULL">{{__('No Assignee (NULL)')}}</option>
                                    @foreach($users as $u)
                                        <option value="{{$u->email}}">{{$u->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label class="form-label">{{ __('Status')}}</label>
                                <select class="form-control form-control-light" name="status_name" id="status_name" required>
                                    <option value="">{{__('Select Status')}}</option>
                                    @foreach($stages as $stage)
                                        <option value="{{$stage->name}}" data-color="{{ $stage->color }}">  {{$stage->name}}
                                            </option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- <div>
                                <button type="submit" class="btn btn-warning mt-4"><i class="ti ti-search"></i></button>
                            </div> --}}
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive overflow_hidden">
                        {{ $dataTable->table(['width' => '100%']) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('layouts.includes.datatable-js')
    {{ $dataTable->scripts() }}
@endpush
@if ($currentWorkspace)
    @push('scripts')
        <!-- third party js -->
        <script src="{{ asset('js/letter.avatar.js') }}"></script>

        <!-- third party js ends -->
        <script>
            var isAddEnable = "{{ request()->query('is_add_enable') ?? false }}";
            console.log(isAddEnable);
            $(document).ready(function () {
                if (isAddEnable) {
                    $('.add-task').trigger('click');
                }
            });
            $(document).on('click', '#form-comment button', function(e) {
                var comment = $.trim($("#form-comment textarea[name='comment']").val());
                if (comment != '') {
                    $.ajax({
                        url: $("#form-comment").data('action'),
                        data: {
                            comment: comment,
                            _token: "{{ csrf_token() }}"
                        },
                        type: 'POST',
                        success: function(data) {
                            data = JSON.parse(data);

                            if (data.user_type == 'Client') {
                                var avatar = "avatar='" + data.client.name + "'";
                                var html = "<li class='media border-bottom mb-3'>" +
                                    "                    <img class='mr-3 avatar-sm rounded-circle img-thumbnail hight_img' width='60' " +
                                    avatar + " alt='" + data.client.name + "'>" +
                                    "                    <div class='media-body mb-2'>" +
                                    "                    <div class='float-left'>" +
                                    "                        <h5 class='mt-0 mb-1 form-control-label'>" +
                                    data.client.name + "</h5>" +
                                    "                        " + data.comment +
                                    "                    </div>" +
                                    "                    </div>" +
                                    "                </li>";
                            } else {
                                var avatar = (data.user.avatar) ?
                                    "src='{{ asset('') }}" + data.user.avatar + "'" :
                                    "avatar='" + data.user.name + "'";
                                var html = "<li class='media border-bottom mb-3'>" +
                                    "                    <div class='col-1'>" +
                                    "                        <img class='mr-3 avatar-sm rounded-circle img-thumbnail hight_img ' width='60' " +
                                    avatar + " alt='" + data.user.name + "'>" +
                                    "                    </div>" +
                                    "                    <div class='col media-body mb-2'>" +
                                    "                        <h5 class='mt-0 mb-1 form-control-label'>" +
                                    data.user.name + "</h5>" +
                                    "                        " + data.comment +
                                    "                    </div>" +
                                    "                    <div class='col text-end'>" +
                                    "                           <a href='#' class='delete-icon action-btn btn-danger mt-1 btn btn-sm d-inline-flex align-items-center delete-comment' data-url='" +
                                    data.deleteUrl + "'>" +
                                    "                               <i class='ti ti-trash'></i>" +
                                    "                           </a>" +
                                    "                     </div>" +
                                    "                </li>";
                            }

                            $("#task-comments").prepend(html);
                            LetterAvatar.transform();
                            $("#form-comment textarea[name='comment']").val('');
                            toastrs('{{ __('Success') }}', '{{ __('Comment Added Successfully!') }}',
                                'success');
                        },
                        error: function(data) {
                            toastrs('{{ __('Error') }}', '{{ __('Some Thing Is Wrong!') }}', 'error');
                        }
                    });
                } else {
                    toastrs('{{ __('Error') }}', '{{ __('Please write comment!') }}', 'error');
                }
            });
            $(document).on("click", ".delete-comment", function() {
                if (confirm('{{ __('Are you sure ?') }}')) {
                    var btn = $(this);
                    $.ajax({
                        url: $(this).attr('data-url'),
                        type: 'DELETE',
                        dataType: 'JSON',
                        success: function(data) {
                            toastrs('{{ __('Success') }}', '{{ __('Comment Deleted Successfully!') }}',
                                'success');
                            btn.closest('.media').remove();
                        },
                        error: function(data) {
                            data = data.responseJSON;
                            if (data.message) {
                                toastrs('{{ __('Error') }}', data.message, 'error');
                            } else {
                                toastrs('{{ __('Error') }}', '{{ __('Some Thing Is Wrong!') }}',
                                    'error');
                            }
                        }
                    });
                }
            });
            $(document).on("click", ".delete-group-btn", function() {
                if (confirm("Are you sure you want to delete selected tasks?")) {
                    let selectedIds = $(".task-checkbox:checked").map(function() {
                        return this.value;
                    }).get();
                    console.log(selectedIds);
                    if (selectedIds.length > 0) {
                        bulkAction(selectedIds, 'delete');
                    }
                }
            });
           
            $(document).on('click', '#form-subtask button', function(e) {
                e.preventDefault();

                var name = $.trim($("#form-subtask input[name=name]").val());
                var due_date = $.trim($("#form-subtask input[name=due_date]").val());
                if (name == '' || due_date == '') {
                    toastrs('{{ __('Error') }}', '{{ __('Please enter fields!') }}', 'error');
                    return false;
                }

                $.ajax({
                    url: $("#form-subtask").data('action'),
                    type: 'POST',
                    data: {
                        name: name,
                        due_date: due_date,
                    },
                    dataType: 'JSON',
                    success: function(data) {
                        toastrs('{{ __('Success') }}', '{{ __('Sub Task Added Successfully!') }}',
                            'success');

                        var html = '<li class="list-group-item py-3">' +
                            '    <div class="form-check form-switch d-inline-block">' +
                            '        <input type="checkbox" class="form-check-input" name="option" id="option' +
                            data.id + '" value="' + data.id + '" data-url="' + data.updateUrl + '">' +
                            '        <label class="custom-control-label form-control-label" for="option' +
                            data.id + '">' + data.name + '</label>' +
                            '    </div>' +
                            '    <div class="float-end">' +
                            '        <a href="#" class=" action-btn btn-danger  btn btn-sm d-inline-flex align-items-center delete-comment delete-icon delete-subtask" data-url="' +
                            data.deleteUrl + '">' +
                            '            <i class="ti ti-trash"></i>' +
                            '        </a>' +
                            '    </div>' +
                            '</li>';

                        $("#subtasks").prepend(html);
                        $("#form-subtask input[name=name]").val('');
                        $("#form-subtask input[name=due_date]").val('');
                        $("#form-subtask").collapse('toggle');
                    },
                    error: function(data) {
                        data = data.responseJSON;
                        if (data.message) {
                            toastrs('{{ __('Error') }}', data.message, 'error');
                            $('#file-error').text(data.errors.file[0]).show();
                        } else {
                            toastrs('{{ __('Error') }}', '{{ __('Some Thing Is Wrong!') }}', 'error');
                        }
                    }
                });
            });
            $(document).on("change", "#subtasks input[type=checkbox]", function() {
                $.ajax({
                    url: $(this).attr('data-url'),
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    dataType: 'JSON',
                    success: function(data) {
                        toastrs('{{ __('Success') }}', '{{ __('Subtask Updated Successfully!') }}',
                            'success');
                    },
                    error: function(data) {
                        data = data.responseJSON;
                        if (data.message) {
                            toastrs('{{ __('Error') }}', data.message, 'error');
                        } else {
                            toastrs('{{ __('Error') }}', '{{ __('Some Thing Is Wrong!') }}', 'error');
                        }
                    }
                });
            });
            $(document).on("click", ".delete-subtask", function() {
                if (confirm('{{ __('Are you sure ?') }}')) {
                    var btn = $(this);
                    $.ajax({
                        url: $(this).attr('data-url'),
                        type: 'DELETE',
                        dataType: 'JSON',
                        success: function(data) {
                            toastrs('{{ __('Success') }}', '{{ __('Subtask Deleted Successfully!') }}',
                                'success');
                            btn.closest('.list-group-item').remove();
                        },
                        error: function(data) {
                            data = data.responseJSON;
                            if (data.message) {
                                toastrs('{{ __('Error') }}', data.message, 'error');
                            } else {
                                toastrs('{{ __('Error') }}', '{{ __('Some Thing Is Wrong!') }}',
                                    'error');
                            }
                        }
                    });
                }
            });
            // $("#form-file").submit(function(e){
            $(document).on('submit', '#form-file', function(e) {
                e.preventDefault();

                $.ajax({
                    url: $("#form-file").data('url'),
                    type: 'POST',
                    data: new FormData(this),
                    dataType: 'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(data) {
                        toastrs('Success', '{{ __('File Upload Successfully!') }}', 'success');

                        var delLink = '';

                        if (data.deleteUrl.length > 0) {
                            delLink =
                                "<a href='#' class=' action-btn btn-danger  btn btn-sm d-inline-flex align-items-center delete-comment delete-icon delete-comment-file'  data-url='" +
                                data.deleteUrl + "'>" +
                                "                                        <i class='ti ti-trash'></i>" +
                                "                                    </a>";
                        }

                        var html = "<div class='card mb-1 shadow-none border'>" +
                            "                        <div class='card-body p-3'>" +
                            "                            <div class='row align-items-center'>" +
                            "                                <div class='col-auto'>" +
                            "                                    <div class='avatar-sm'>" +
                            "                                        <span class='avatar-title text-uppercase'>" +
                            "  <img src='{{ asset('uploads/tasks/') }}/" +
                            data.file +
                            "' width='60px' height='60px' >" +
                            "                                        </span>" +
                            "                                    </div>" +
                            "                                </div>" +
                            "                                <div class='col pl-0'>" +
                            "                                    <a href='#' class='text-muted form-control-label'>" +
                            data.name + "</a>" +
                            "                                    <p class='mb-0'>" + data.file_size +
                            "</p>" +
                            "                                </div>" +
                            "                                <div class='col-auto'>" +
                            "                                    <a download href='{{ asset('/uploads/tasks/') }}/" +
                            data.file +
                            "' class='edit-icon action-btn btn-primary  btn btn-sm d-inline-flex align-items-center mx-1'>" +
                            "                                        <i class='ti ti-download'></i>" +
                            "                                    </a>" +
                            delLink +
                            "                                </div>" +
                            "                            </div>" +
                            "                        </div>" +
                            "                    </div>";
                        $("#comments-file").prepend(html);
                    },
                    error: function(data) {
                        data = data.responseJSON;
                        if (data.message) {
                            toastrs('{{ __('Error') }}', data.message, 'error');
                            $('#file-error').text(data.errors.file[0]).show();
                        } else {
                            toastrs('{{ __('Error') }}', '{{ __('Some Thing Is Wrong!') }}', 'error');
                        }
                    }
                });
            });
            $(document).on("click", ".delete-comment-file", function() {
                if (confirm('{{ __('Are you sure ?') }}')) {
                    var btn = $(this);
                    $.ajax({
                        url: $(this).attr('data-url'),
                        type: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        dataType: 'JSON',
                        success: function(data) {
                            toastrs('{{ __('Success') }}', '{{ __('File Deleted Successfully!') }}',
                                'success');
                            btn.closest('.border').remove();
                        },
                        error: function(data) {
                            data = data.responseJSON;
                            if (data.message) {
                                toastrs('{{ __('Error') }}', data.message, 'error');
                            } else {
                                toastrs('{{ __('Error') }}', '{{ __('Some Thing Is Wrong!') }}',
                                    'error');
                            }
                        }
                    });
                }
            });
        </script>
        <script>
         $(document).ready(function () {
                 $('#select-all').on('change', function () {
                    let isChecked = $(this).is(':checked');
                    $('.task-checkbox').prop('checked', isChecked);
                });

                // Initialize toggle filter
                initializeTaskToggle();
                
                // initializeDataTable();
                 $('#assignee_name, #assignor_name,#status_name,#group_name,#task_name').on('change', function () {
                    getTaskCount();
                    initializeDataTable();
                });
                 $('#group_name').on('blur', function () {
                    getTaskCount();
                    initializeDataTable();
                });
                 $('#task_name').on('blur', function () {
                    getTaskCount();
                    initializeDataTable();
                });

              
            });
            
            // Initialize task toggle
            function initializeTaskToggle() {
                // Wait for DataTable to be fully initialized
                setTimeout(function() {
                    // Create toggle HTML
                    var toggleHtml = `
                        <div class="toggle" data-state="all" id="taskToggle">
                            <div class="toggle-indicator"></div>
                            <div class="toggle-option" data-value="all">All</div>
                            <div class="toggle-option" data-value="overdue">Overdue</div>
                            <div class="toggle-option" data-value="urgent">Urgent</div>
                            <div class="toggle-option" data-value="flag">Flag</div>
                        </div>
                    `;
                    
                    // Insert toggle into the task-toggle-wrapper
                    $('.task-toggle-wrapper').html(toggleHtml);
                    
                    // Bind toggle functionality
                    $('#taskToggle .toggle-option').on('click', function() {
                        var selectedValue = $(this).data('value');
                        var currentState = $('#taskToggle').attr('data-state');
                        
                        // If clicking the same option, toggle it off (set to 'all')
                        if (currentState === selectedValue && selectedValue !== 'all') {
                            selectedValue = 'all';
                        }
                        
                        $('#taskToggle').attr('data-state', selectedValue);
                        console.log("Selected:", selectedValue);
                        
                        // Apply filtering based on selected option
                        filterTasksByToggle(selectedValue);
                    });
                }, 1000);
            }
            
            // Filter tasks based on toggle selection
            function filterTasksByToggle(filterType) {
                if ($.fn.DataTable.isDataTable('#projects-task-table')) {
                    var table = $('#projects-task-table').DataTable();
                    
                    // Update the AJAX data to include toggle filter
                    table.settings()[0].ajax.data = function(d) {
                        d.assignee_name = $('#assignee_name').val();
                        d.assignor_name = $('#assignor_name').val();
                        d.status_name = $('#status_name').val();
                        d.group_name = $('#group_name').val();
                        d.task_name = $('#task_name').val();
                        d.toggle_filter = filterType;
                        return d;
                    };
                    
                    // Reload the table with new filter
                    table.ajax.reload();
                    getTaskCount();
                }
            }
               
                
            $(document).on("click", ".task-checkbox", function() {
                let selectedIds = $(".task-checkbox:checked").map(function() {
                    return this.value;
                }).get();
                console.log(selectedIds);
                 let allChecked = $('.task-checkbox').length === $('.task-checkbox:checked').length;
                $('#select-all').prop('checked', allChecked);
                if (selectedIds.length > 0) {
                    // duplicateTasks(selectedIds);
                }
            });
           
            function initializeDataTable() {
                    if ($.fn.DataTable.isDataTable('#projects-task-table')) {
                        $('#projects-task-table').DataTable().destroy();
                    }

                    $('#projects-task-table').DataTable({
                        processing: true,
                        serverSide: true,
                        pageLength: 100, // Show 100 records per page
                        lengthMenu: [10, 25, 50, 100, 200], // Allow user to select different page lengths
                        dom: 'Bfrtip',
                        buttons: [
                            {
                                extend: 'copy',
                                text: '<i class="fas fa-copy"></i> Copy',
                                className: 'btn btn-light-primary'
                            },
                            {
                                text: '<i class="fas fa-trash"></i> ',
                                className: 'btn btn-light-danger delete-group-btn',
                                attr: { id: 'delete-btn', disabled: 'disabled' },
                                action: function (e, dt, node, config) {
                                    let selectedIds = $(".task-checkbox:checked").map(function () { return this.value; }).get();
                                    if (selectedIds.length > 0) {
                                    }
                                }
                            },
                            {
                                extend: 'reset',
                                text: '<i class="fas fa-undo"></i> ',
                                className: 'btn btn-light-danger'
                            },
                            {
                                extend: 'reload',
                                text: '<i class="fas fa-sync-alt"></i> ',
                                className: 'btn btn-light-warning'
                            }
                        ],
                        drawCallback: function(settings) {
                            // Initialize toggle filter if not already initialized
                            setTimeout(function() {
                                if ($('.task-toggle-wrapper').html().trim() === '' || !$('#taskToggle').length) {
                                    initializeTaskToggle();
                                }
                            }, 100);
                            
                            // Reinitialize tooltips
                            var tooltipTriggerList = [].slice.call(document.querySelectorAll("[data-bs-toggle=tooltip]"));
                            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                                return new bootstrap.Tooltip(tooltipTriggerEl);
                            });
                        },
                        ajax: {
                            url: "{{ route('projecttask.automate.list') }}",
                            data: function (d) {
                                d.assignee_name = $('#assignee_name').val();
                                d.assignor_name = $('#assignor_name').val();
                                d.group_name = $('#group_name').val();
                                d.task_name = $('#task_name').val();
                                d.status_name = $('#status_name').val();
                                var toggleState = $('#taskToggle').attr('data-state') || 'all';
                                if (toggleState !== 'all') {
                                    d.toggle_filter = toggleState;
                                }
                            }
                        },
                        columns: [
                            { data: 'checkbox', name: 'checkbox' ,orderable: false },
                            { data: 'group', name: 'group' },
                            { data: 'title', name: 'title' },
                            { data: 'assigner_name', name: 'assigner_name' },
                            { data: 'assign_to', name: 'assign_to' },
                            {data:'eta_time',name:'eta_time'},
                             { data: 'status', name: 'status' },
                            { data: 'links', name: 'links' },
                             { data: 'link_3', name: 'link_3' },
                             { data: 'link_4', name: 'link_4' },
                             { data: 'link_5', name: 'link_5' },
                             { data: 'link_7', name: 'link_7' },
                             { data: 'link_6', name: 'link_6' },
                            { data: 'schedule_type', name: 'schedule_type' },
                            { data: 'action', name: 'action', orderable: false, searchable: false }
                        ]
                    });
                }
                
             function bulkAction(selectedIds, actionType) {
                $.ajax({
                    url: "{{ route('projecttask.automate.bulkAction') }}",
                    type: 'get',
                    data: {
                        selected_ids: selectedIds,
                        action_type: actionType,
                    },
                    dataType: 'JSON',
                    success: function(data) {
                        toastrs(data.message,
                            'success');
                        // btn.closest('.border').remove();
                        if(actionType=='delete')
                                {
                                     selectedIds.forEach(function (id) {
                                        $('#projects-task-table').DataTable().row($("input[value='" + id + "']").closest('tr')).remove().draw();
                                    });
                                }else{
                                    initializeDataTable();
                                    getTaskCount();
                                }
                    },
                    error: function(data) {
                        data = data.responseJSON;
                        if (data.message) {
                            toastrs('{{ __('Error') }}', data.message, 'error');
                        } else {
                            toastrs('{{ __('Error') }}', '{{ __('Some Thing Is Wrong!') }}',
                                'error');
                        }
                    }
                });
            }
              function getTaskCount() {
    $.ajax({
        url: "{{ route('projecttask.automate.count') }}",
        type: 'get',
        data: {
            assignee_name: $('#assignee_name').val(),
            assignor_name: $('#assignor_name').val(),
            status_name: $('#status_name').val(),
            group_name: $('#group_name').val(),
            task_name: $('#task_name').val(),
            search_value: $('#projects-task-table_filter input').val(),
        },
        dataType: 'JSON',
        success: function(data) {
            console.log(data);
            if(data.is_success) {
                // Total tasks count (unchanged)
                $("#pending-count").html(data.data.pending_count);
                
                // Weekly cards - now showing ETA minutes
                
                $("#weekly-count").html(data.data.total_weekly_eta);
                $("#wday-count").html(data.data.total_daily_eta);
                
                // Monthly cards - now showing ETA minutes
                $("#monthly-count").html(data.data.total_monthly_eta);
                $("#mday-count").html(data.data.total_daily_eta);
            } else {
                // Reset all counts if error
                $("#pending-count").html(0);
                $("#weekly-count").html(0);
                $("#wday-count").html(0);
                $("#monthly-count").html(0);
                $("#mday-count").html(0);
            }
        },
        error: function(data) {
            toastrs('{{ __('Error') }}', '{{ __('Some Thing Is Wrong!') }}', 'error');
        }
    });
}
             $(document).ready(function () {
                            
                            // initializeDataTable();
            
                            // Reload DataTable when filter values change
                            $('#assignee_name, #assignor_name,#status_name').on('keyup change', function () {
                                initializeDataTable();
                                getTaskCount();
                            });
                        });
        </script>
    @endpush
@endif
