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
                                        <option value="{{$u->email}}">{{ formatUserName($u->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label class="form-label">{{ __('Assignee')}}</label>
                                <select class=" multi-select choices" id="assignee_name" multiple="multiple" name="assignee_name" data-placeholder="{{ __('Select Users ...') }}" required>
                                    <option value="">{{__('Select Assignee')}}</option>
                                    <option value="NULL">{{__('No Assignee (NULL)')}}</option>
                                    @foreach($users as $u)
                                        <option value="{{$u->email}}">{{ formatUserName($u->name) }}</option>
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
                if (isAddEnable === "true" || isAddEnable === true) {
                    // Add a small delay to ensure the button is fully initialized
                    setTimeout(function() {
                        $('.add-task').trigger('click');
                    }, 500);
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
            // Update button states based on task selection
            function updateBulkActionButtons() {
                let selectedIds = $(".task-checkbox:checked").map(function() {
                    return this.value;
                }).get();
                
                // Update selected count
                $('#selected-count').text(selectedIds.length);
                
                // Show/hide bulk actions bar
                if (selectedIds.length > 0) {
                    $('#bulk-actions-bar').fadeIn(200);
                    $('#delete-btn, #bulk-status-update-btn, #bulk-assignor-update-btn, #bulk-assignee-update-btn, #bulk-etc-update-btn, #bulk-date-update-btn, #bulk-priority-update-btn').prop('disabled', false);
                    $('#delete-btn-top, #bulk-status-update-btn-top, #bulk-assignor-update-btn-top, #bulk-assignee-update-btn-top, #bulk-etc-update-btn-top, #bulk-date-update-btn-top, #bulk-priority-update-btn-top').prop('disabled', false);
                } else {
                    $('#bulk-actions-bar').fadeOut(200);
                    $('#delete-btn, #bulk-status-update-btn, #bulk-assignor-update-btn, #bulk-assignee-update-btn, #bulk-etc-update-btn, #bulk-date-update-btn, #bulk-priority-update-btn').prop('disabled', true);
                    $('#delete-btn-top, #bulk-status-update-btn-top, #bulk-assignor-update-btn-top, #bulk-assignee-update-btn-top, #bulk-etc-update-btn-top, #bulk-date-update-btn-top, #bulk-priority-update-btn-top').prop('disabled', true);
                }
            }

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
            
            // Filter persistence using localStorage
            var FILTER_STORAGE_KEY = 'automate_task_list_filters';
            
            // Function to save filters to localStorage
            function saveFilters() {
                var filters = {
                    assignee_name: $('#assignee_name').val() || [],
                    assignor_name: $('#assignor_name').val() || [],
                    status_name: $('#status_name').val() || '',
                    group_name: $('#group_name').val() || '',
                    task_name: $('#task_name').val() || '',
                    toggle_filter: $('#taskToggle').attr('data-state') || 'all'
                };
                
                try {
                    localStorage.setItem(FILTER_STORAGE_KEY, JSON.stringify(filters));
                } catch (e) {
                    console.error('Error saving filters to localStorage:', e);
                }
            }
            
            // Function to restore filters from localStorage
            function restoreFilters() {
                try {
                    var savedFilters = localStorage.getItem(FILTER_STORAGE_KEY);
                    if (savedFilters) {
                        var filters = JSON.parse(savedFilters);
                        
                        // Restore text inputs
                        if (filters.group_name) {
                            $('#group_name').val(filters.group_name);
                        }
                        if (filters.task_name) {
                            $('#task_name').val(filters.task_name);
                        }
                        
                        // Restore single select dropdowns
                        if (filters.status_name) {
                            $('#status_name').val(filters.status_name).trigger('change');
                        }
                        
                        // Restore multi-select dropdowns (Choices.js)
                        // Wait for Choices.js to initialize
                        setTimeout(function() {
                            if (filters.assignee_name && filters.assignee_name.length > 0) {
                                var assigneeChoices = $('#assignee_name')[0].choices;
                                if (assigneeChoices) {
                                    assigneeChoices.setChoiceByValue(filters.assignee_name);
                                } else {
                                    // Fallback if Choices.js not ready
                                    $('#assignee_name').val(filters.assignee_name).trigger('change');
                                }
                            }
                            
                            if (filters.assignor_name && filters.assignor_name.length > 0) {
                                var assignorChoices = $('#assignor_name')[0].choices;
                                if (assignorChoices) {
                                    assignorChoices.setChoiceByValue(filters.assignor_name);
                                } else {
                                    // Fallback if Choices.js not ready
                                    $('#assignor_name').val(filters.assignor_name).trigger('change');
                                }
                            }
                        }, 500);
                        
                        // Restore toggle filter
                        if (filters.toggle_filter && filters.toggle_filter !== 'all') {
                            setTimeout(function() {
                                if ($('#taskToggle').length) {
                                    $('#taskToggle').attr('data-state', filters.toggle_filter);
                                    $('#taskToggle .toggle-option[data-value="' + filters.toggle_filter + '"]').trigger('click');
                                }
                            }, 1000);
                        }
                        
                        // Apply filters to DataTable after a short delay to ensure all elements are ready
                        setTimeout(function() {
                            applyFiltersToDataTable();
                        }, 800);
                    }
                } catch (e) {
                    console.error('Error restoring filters from localStorage:', e);
                }
            }
            
            // Function to apply restored filters to DataTable
            function applyFiltersToDataTable() {
                if ($.fn.DataTable.isDataTable('#projects-task-table')) {
                    var table = $('#projects-task-table').DataTable();
                    // Update the ajax data parameters
                    table.settings()[0].ajax.data = function(d) {
                        d.assignee_name = $('#assignee_name').val();
                        d.assignor_name = $('#assignor_name').val();
                        d.status_name = $('#status_name').val();
                        d.group_name = $('#group_name').val();
                        d.task_name = $('#task_name').val();
                        var toggleState = $('#taskToggle').attr('data-state') || 'all';
                        if (toggleState !== 'all') {
                            d.toggle_filter = toggleState;
                        }
                        return d;
                    };
                    table.ajax.reload();
                    getTaskCount();
                }
            }
            
            // Filter tasks based on toggle selection
            function filterTasksByToggle(filterType) {
                saveFilters(); // Save filters when toggle changes
                if ($.fn.DataTable.isDataTable('#projects-task-table')) {
                    var table = $('#projects-task-table').DataTable();
                    
                    // Update the AJAX data to include toggle filter
                    table.settings()[0].ajax.data = function(d) {
                        d.assignee_name = $('#assignee_name').val();
                        d.assignor_name = $('#assignor_name').val();
                        d.status_name = $('#status_name').val();
                        d.group_name = $('#group_name').val();
                        d.task_name = $('#task_name').val();
                        var toggleState = filterType || 'all';
                        if (toggleState !== 'all') {
                            d.toggle_filter = toggleState;
                        }
                        return d;
                    };
                    
                    // Reload the table with new filter
                    table.ajax.reload();
                    getTaskCount();
                }
            }

         $(document).ready(function () {
                // Restore filters on page load
                restoreFilters();
                
                 $('#select-all').on('change', function () {
                    let isChecked = $(this).is(':checked');
                    $('.task-checkbox').prop('checked', isChecked);
                    updateBulkActionButtons();
                });

                // Initialize toggle filter
                initializeTaskToggle();
                
                // Reload DataTable when filter values change and save to localStorage
                $('#assignee_name, #assignor_name,#status_name,#group_name,#task_name').on('change', function () {
                    saveFilters(); // Save filters when they change
                    getTaskCount();
                    // Reload the DataTable with new parameters
                    if ($.fn.DataTable.isDataTable('#projects-task-table')) {
                        var table = $('#projects-task-table').DataTable();
                        // Update the ajax data parameters
                        table.settings()[0].ajax.data = function(d) {
                            d.assignee_name = $('#assignee_name').val();
                            d.assignor_name = $('#assignor_name').val();
                            d.status_name = $('#status_name').val();
                            d.group_name = $('#group_name').val();
                            d.task_name = $('#task_name').val();
                            var toggleState = $('#taskToggle').attr('data-state') || 'all';
                            if (toggleState !== 'all') {
                                d.toggle_filter = toggleState;
                            }
                            return d;
                        };
                        table.ajax.reload();
                    }
                });
                
                // Save filters when text inputs change (with debounce) and reload DataTable
                var filterSaveTimeout;
                $('#group_name, #task_name').on('keyup', function () {
                    clearTimeout(filterSaveTimeout);
                    filterSaveTimeout = setTimeout(function() {
                        saveFilters();
                        // Reload DataTable with updated filters after typing stops
                        if ($.fn.DataTable.isDataTable('#projects-task-table')) {
                            var table = $('#projects-task-table').DataTable();
                            table.settings()[0].ajax.data = function(d) {
                                d.assignee_name = $('#assignee_name').val();
                                d.assignor_name = $('#assignor_name').val();
                                d.status_name = $('#status_name').val();
                                d.group_name = $('#group_name').val();
                                d.task_name = $('#task_name').val();
                                var toggleState = $('#taskToggle').attr('data-state') || 'all';
                                if (toggleState !== 'all') {
                                    d.toggle_filter = toggleState;
                                }
                                return d;
                            };
                            table.ajax.reload();
                            getTaskCount();
                        }
                    }, 500); // Reload after 500ms of no typing
                });
                
                 $('#group_name').on('blur', function () {
                    saveFilters(); // Save filters when they change
                    getTaskCount();
                    // Reload the DataTable with new parameters
                    if ($.fn.DataTable.isDataTable('#projects-task-table')) {
                        var table = $('#projects-task-table').DataTable();
                        table.settings()[0].ajax.data = function(d) {
                            d.assignee_name = $('#assignee_name').val();
                            d.assignor_name = $('#assignor_name').val();
                            d.status_name = $('#status_name').val();
                            d.group_name = $('#group_name').val();
                            d.task_name = $('#task_name').val();
                            var toggleState = $('#taskToggle').attr('data-state') || 'all';
                            if (toggleState !== 'all') {
                                d.toggle_filter = toggleState;
                            }
                            return d;
                        };
                        table.ajax.reload();
                    }
                });
                 $('#task_name').on('blur', function () {
                    saveFilters(); // Save filters when they change
                    getTaskCount();
                    // Reload the DataTable with new parameters
                    if ($.fn.DataTable.isDataTable('#projects-task-table')) {
                        var table = $('#projects-task-table').DataTable();
                        table.settings()[0].ajax.data = function(d) {
                            d.assignee_name = $('#assignee_name').val();
                            d.assignor_name = $('#assignor_name').val();
                            d.status_name = $('#status_name').val();
                            d.group_name = $('#group_name').val();
                            d.task_name = $('#task_name').val();
                            var toggleState = $('#taskToggle').attr('data-state') || 'all';
                            if (toggleState !== 'all') {
                                d.toggle_filter = toggleState;
                            }
                            return d;
                        };
                        table.ajax.reload();
                    }
                });

              
            });
               
                
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
                            // Update button states after table draw
                            updateBulkActionButtons();
                            
                            // Initialize toggle filter if not already initialized
                            if ($('.task-toggle-wrapper').html().trim() === '') {
                                initializeTaskToggle();
                            }
                            
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
        </script>
    @endpush
@endif

{{-- Bulk Edit Modals --}}
@if ($currentWorkspace)
    {{-- Change Assignor Modal --}}
    <div class="modal fade" id="change-assignor-modal" tabindex="-1" aria-labelledby="changeAssignorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);">
                <div class="modal-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; border-bottom: none; border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title" id="changeAssignorModalLabel">
                        <i class="fas fa-user-edit me-2"></i>
                        Update Assignor for Selected Tasks
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding: 2rem;">
                    <form id="change-assignor-form">
                        @csrf
                        <input type="hidden" id="selected-task-ids-assignor" name="task_ids">
                        <div class="mb-3">
                            <label for="assignor-select" class="form-label">Select New Assignor</label>
                            <select class="form-control" id="assignor-select" name="assignor_email" required>
                                <option value="">Choose Assignor...</option>
                                @if(isset($users))
                                    @foreach($users as $user)
                                        <option value="{{ $user->email }}">{{ formatUserName($user->name) }} ({{ $user->email }})</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="alert alert-info">
                            <small><i class="fas fa-info-circle me-1"></i> This will update the assignor for all selected tasks.</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #e2e8f0; padding: 1rem 2rem;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" id="update-assignor-btn">
                        <i class="fas fa-save me-1"></i>
                        Update Assignor
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Change Assignee Modal --}}
    <div class="modal fade" id="change-assignee-modal" tabindex="-1" aria-labelledby="changeAssigneeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);">
                <div class="modal-header" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; border-bottom: none; border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title" id="changeAssigneeModalLabel">
                        <i class="fas fa-user-plus me-2"></i>
                        Update Assignee for Selected Tasks
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding: 2rem;">
                    <form id="change-assignee-form">
                        @csrf
                        <input type="hidden" id="selected-task-ids-assignee" name="task_ids">
                        <div class="mb-3">
                            <label for="assignee-select" class="form-label">Select New Assignee</label>
                            <select class="form-control" id="assignee-select" name="assignee_email" required>
                                <option value="">Choose Assignee...</option>
                                @if(isset($users))
                                    @foreach($users as $user)
                                        <option value="{{ $user->email }}">{{ formatUserName($user->name) }} ({{ $user->email }})</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="alert alert-info">
                            <small><i class="fas fa-info-circle me-1"></i> This will update the assignee for all selected tasks.</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #e2e8f0; padding: 1rem 2rem;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="update-assignee-btn">
                        <i class="fas fa-save me-1"></i>
                        Update Assignee
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Change ETC Modal --}}
    <div class="modal fade" id="change-etc-modal" tabindex="-1" aria-labelledby="changeETCModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);">
                <div class="modal-header" style="background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%); color: white; border-bottom: none; border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title" id="changeETCModalLabel">
                        <i class="fas fa-clock me-2"></i>
                        Update ETC for Selected Tasks
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding: 2rem;">
                    <form id="change-etc-form">
                        @csrf
                        <input type="hidden" id="selected-task-ids-etc" name="task_ids">
                        <div class="mb-3">
                            <label for="etc-input" class="form-label">Enter New ETC Value (minutes)</label>
                            <input type="number" class="form-control" id="etc-input" name="etc_value" min="0" step="0.5" required placeholder="Enter ETC minutes">
                        </div>
                        <div class="alert alert-info">
                            <small><i class="fas fa-info-circle me-1"></i> This will update the estimated time to complete for all selected tasks.</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #e2e8f0; padding: 1rem 2rem;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" id="update-etc-btn">
                        <i class="fas fa-save me-1"></i>
                        Update ETC
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Change Date Modal --}}
    <div class="modal fade" id="change-date-modal" tabindex="-1" aria-labelledby="changeDateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);">
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-bottom: none; border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title" id="changeDateModalLabel">
                        <i class="fas fa-calendar me-2"></i>
                        Update Dates for Selected Tasks
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding: 2rem;">
                    <form id="change-date-form">
                        @csrf
                        <input type="hidden" id="selected-task-ids-date" name="task_ids">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start-date-input" class="form-label">Start Date (TID)</label>
                                <input type="date" class="form-control" id="start-date-input" name="start_date">
                            </div>
                            <div class="col-md-6">
                                <label for="end-date-input" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end-date-input" name="end_date">
                            </div>
                        </div>
                        <div class="alert alert-info">
                            <small><i class="fas fa-info-circle me-1"></i> Leave a field empty if you don't want to update it. TID = Task Initiation Date.</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #e2e8f0; padding: 1rem 2rem;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-purple" id="update-date-btn">
                        <i class="fas fa-save me-1"></i>
                        Update Dates
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Change Priority Modal --}}
    <div class="modal fade" id="change-priority-modal" tabindex="-1" aria-labelledby="changePriorityModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);">
                <div class="modal-header" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; border-bottom: none; border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title" id="changePriorityModalLabel">
                        <i class="fas fa-flag me-2"></i>
                        Update Priority for Selected Tasks
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding: 2rem;">
                    <form id="change-priority-form">
                        @csrf
                        <input type="hidden" id="selected-task-ids-priority" name="task_ids">
                        <div class="mb-3">
                            <label for="priority-select" class="form-label">Select New Priority</label>
                            <select class="form-control" id="priority-select" name="priority" required>
                                <option value="">Choose Priority...</option>
                                <option value="urgent" style="color: #dc3545; font-weight: bold;">🔴 Urgent</option>
                                <option value="normal" style="color: #198754; font-weight: bold;">🟢 Normal</option>
                                <option value="Take your time" style="color: #fd7e14; font-weight: bold;">🟠 Take your time</option>
                            </select>
                        </div>
                        <div class="alert alert-info">
                            <small><i class="fas fa-info-circle me-1"></i> This will update the priority for all selected tasks.</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #e2e8f0; padding: 1rem 2rem;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="update-priority-btn">
                        <i class="fas fa-save me-1"></i>
                        Update Priority
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Change Status Modal --}}
    <div class="modal fade" id="change-status-modal" tabindex="-1" aria-labelledby="changeStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);">
                <div class="modal-header" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; border-bottom: none; border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title" id="changeStatusModalLabel">
                        <i class="fas fa-tasks me-2"></i>
                        Update Status for Selected Tasks
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding: 2rem;">
                    <form id="change-status-form">
                        @csrf
                        <input type="hidden" id="selected-task-ids-status" name="task_ids">
                        <div class="mb-3">
                            <label for="status-select" class="form-label">Select New Status</label>
                            <select class="form-control" id="status-select" name="status" required>
                                <option value="">Choose Status...</option>
                                @if(isset($stages))
                                    @foreach($stages as $stage)
                                        <option value="{{ $stage->name }}" data-color="{{ $stage->color }}">{{ $stage->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="alert alert-info">
                            <small><i class="fas fa-info-circle me-1"></i> This will update the status for all selected tasks.</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #e2e8f0; padding: 1rem 2rem;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-info" id="update-status-btn">
                        <i class="fas fa-save me-1"></i>
                        Update Status
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).on("click", ".task-checkbox", function() {
                updateBulkActionButtons();
            });

            // Clear selection button
            $('#clear-selection-btn').on('click', function() {
                $('.task-checkbox').prop('checked', false);
                $('#select-all').prop('checked', false);
                updateBulkActionButtons();
            });

            // Connect top buttons to modals (same functionality as DataTable buttons)
            $('#bulk-status-update-btn-top').on('click', function() {
                if (!$(this).prop('disabled')) {
                    let selectedIds = $(".task-checkbox:checked").map(function() { 
                        return this.value; 
                    }).get();
                    if (selectedIds.length > 0) {
                        $("#selected-task-ids-status").val(selectedIds.join(","));
                        $("#change-status-modal").modal("show");
                    }
                }
            });

            $('#bulk-assignor-update-btn-top').on('click', function() {
                if (!$(this).prop('disabled')) {
                    let selectedIds = $(".task-checkbox:checked").map(function() { 
                        return this.value; 
                    }).get();
                    if (selectedIds.length > 0) {
                        $("#selected-task-ids-assignor").val(selectedIds.join(","));
                        $("#change-assignor-modal").modal("show");
                    }
                }
            });

            $('#bulk-assignee-update-btn-top').on('click', function() {
                if (!$(this).prop('disabled')) {
                    let selectedIds = $(".task-checkbox:checked").map(function() { 
                        return this.value; 
                    }).get();
                    if (selectedIds.length > 0) {
                        $("#selected-task-ids-assignee").val(selectedIds.join(","));
                        $("#change-assignee-modal").modal("show");
                    }
                }
            });

            $('#bulk-etc-update-btn-top').on('click', function() {
                if (!$(this).prop('disabled')) {
                    let selectedIds = $(".task-checkbox:checked").map(function() { 
                        return this.value; 
                    }).get();
                    if (selectedIds.length > 0) {
                        $("#selected-task-ids-etc").val(selectedIds.join(","));
                        $("#change-etc-modal").modal("show");
                    }
                }
            });

            $('#bulk-date-update-btn-top').on('click', function() {
                if (!$(this).prop('disabled')) {
                    let selectedIds = $(".task-checkbox:checked").map(function() { 
                        return this.value; 
                    }).get();
                    if (selectedIds.length > 0) {
                        $("#selected-task-ids-date").val(selectedIds.join(","));
                        $("#change-date-modal").modal("show");
                    }
                }
            });

            $('#bulk-priority-update-btn-top').on('click', function() {
                if (!$(this).prop('disabled')) {
                    let selectedIds = $(".task-checkbox:checked").map(function() { 
                        return this.value; 
                    }).get();
                    if (selectedIds.length > 0) {
                        $("#selected-task-ids-priority").val(selectedIds.join(","));
                        $("#change-priority-modal").modal("show");
                    }
                }
            });

            $('#delete-btn-top').on('click', function() {
                if (!$(this).prop('disabled')) {
                    if (confirm("Are you sure you want to delete selected tasks?")) {
                        let selectedIds = $(".task-checkbox:checked").map(function() {
                            return this.value;
                        }).get();
                        if (selectedIds.length > 0) {
                            bulkAction(selectedIds, 'delete');
                        }
                    }
                }
            });

            // Bulk Assignor Update
            $('#update-assignor-btn').on('click', function() {
                var selectedTaskIds = $('#selected-task-ids-assignor').val();
                var assignorEmail = $('#assignor-select').val();

                if (!selectedTaskIds || !assignorEmail) {
                    toastr.error('Please select an assignor');
                    return;
                }

                var $button = $(this);
                var originalHtml = $button.html();
                $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Updating...');

                $.ajax({
                    url: '{{ route("projecttask.automate.bulkUpdateAssignor") }}',
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        task_ids: JSON.stringify(selectedTaskIds.split(',').map(id => parseInt(id.trim())).filter(id => id > 0)),
                        assignor_email: assignorEmail,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.is_success) {
                            toastrs('Success', response.message, 'success');
                            $('#change-assignor-modal').modal('hide');
                            $('#change-assignor-form')[0].reset();
                            $('.task-checkbox').prop('checked', false);
                            $('#select-all').prop('checked', false);
                            updateBulkActionButtons();
                            if ($.fn.DataTable.isDataTable('#projects-task-table')) {
                                $('#projects-task-table').DataTable().ajax.reload(null, false);
                            }
                            getTaskCount();
                        } else {
                            toastrs('Error', response.message, 'error');
                        }
                        $button.prop('disabled', false).html(originalHtml);
                    },
                    error: function(xhr) {
                        var errorMsg = 'An error occurred';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        toastrs('Error', errorMsg, 'error');
                        $button.prop('disabled', false).html(originalHtml);
                    }
                });
            });

            // Bulk Assignee Update
            $('#update-assignee-btn').on('click', function() {
                var selectedTaskIds = $('#selected-task-ids-assignee').val();
                var assigneeEmail = $('#assignee-select').val();

                if (!selectedTaskIds || !assigneeEmail) {
                    toastr.error('Please select an assignee');
                    return;
                }

                var $button = $(this);
                var originalHtml = $button.html();
                $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Updating...');

                $.ajax({
                    url: '{{ route("projecttask.automate.bulkUpdateAssignee") }}',
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        task_ids: JSON.stringify(selectedTaskIds.split(',').map(id => parseInt(id.trim())).filter(id => id > 0)),
                        assignee_email: assigneeEmail,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.is_success) {
                            toastrs('Success', response.message, 'success');
                            $('#change-assignee-modal').modal('hide');
                            $('#change-assignee-form')[0].reset();
                            $('.task-checkbox').prop('checked', false);
                            $('#select-all').prop('checked', false);
                            updateBulkActionButtons();
                            if ($.fn.DataTable.isDataTable('#projects-task-table')) {
                                $('#projects-task-table').DataTable().ajax.reload(null, false);
                            }
                            getTaskCount();
                        } else {
                            toastrs('Error', response.message, 'error');
                        }
                        $button.prop('disabled', false).html(originalHtml);
                    },
                    error: function(xhr) {
                        var errorMsg = 'An error occurred';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        toastrs('Error', errorMsg, 'error');
                        $button.prop('disabled', false).html(originalHtml);
                    }
                });
            });

            // Bulk ETC Update
            $('#update-etc-btn').on('click', function() {
                var selectedTaskIds = $('#selected-task-ids-etc').val();
                var etcValue = $('#etc-input').val();

                if (!selectedTaskIds || !etcValue) {
                    toastr.error('Please enter ETC value');
                    return;
                }

                var $button = $(this);
                var originalHtml = $button.html();
                $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Updating...');

                $.ajax({
                    url: '{{ route("projecttask.automate.bulkUpdateETC") }}',
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        task_ids: JSON.stringify(selectedTaskIds.split(',').map(id => parseInt(id.trim())).filter(id => id > 0)),
                        etc_value: etcValue,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.is_success) {
                            toastrs('Success', response.message, 'success');
                            $('#change-etc-modal').modal('hide');
                            $('#change-etc-form')[0].reset();
                            $('.task-checkbox').prop('checked', false);
                            $('#select-all').prop('checked', false);
                            updateBulkActionButtons();
                            if ($.fn.DataTable.isDataTable('#projects-task-table')) {
                                $('#projects-task-table').DataTable().ajax.reload(null, false);
                            }
                            getTaskCount();
                        } else {
                            toastrs('Error', response.message, 'error');
                        }
                        $button.prop('disabled', false).html(originalHtml);
                    },
                    error: function(xhr) {
                        var errorMsg = 'An error occurred';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        toastrs('Error', errorMsg, 'error');
                        $button.prop('disabled', false).html(originalHtml);
                    }
                });
            });

            // Bulk Date Update
            $('#update-date-btn').on('click', function() {
                var selectedTaskIds = $('#selected-task-ids-date').val();
                var startDate = $('#start-date-input').val();
                var endDate = $('#end-date-input').val();

                if (!selectedTaskIds || (!startDate && !endDate)) {
                    toastr.error('Please select at least one date');
                    return;
                }

                var $button = $(this);
                var originalHtml = $button.html();
                $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Updating...');

                $.ajax({
                    url: '{{ route("projecttask.automate.bulkUpdateDate") }}',
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        task_ids: JSON.stringify(selectedTaskIds.split(',').map(id => parseInt(id.trim())).filter(id => id > 0)),
                        start_date: startDate,
                        end_date: endDate,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.is_success) {
                            toastrs('Success', response.message, 'success');
                            $('#change-date-modal').modal('hide');
                            $('#change-date-form')[0].reset();
                            $('.task-checkbox').prop('checked', false);
                            $('#select-all').prop('checked', false);
                            updateBulkActionButtons();
                            if ($.fn.DataTable.isDataTable('#projects-task-table')) {
                                $('#projects-task-table').DataTable().ajax.reload(null, false);
                            }
                            getTaskCount();
                        } else {
                            toastrs('Error', response.message, 'error');
                        }
                        $button.prop('disabled', false).html(originalHtml);
                    },
                    error: function(xhr) {
                        var errorMsg = 'An error occurred';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        toastrs('Error', errorMsg, 'error');
                        $button.prop('disabled', false).html(originalHtml);
                    }
                });
            });

            // Bulk Priority Update
            $('#update-priority-btn').on('click', function() {
                var selectedTaskIds = $('#selected-task-ids-priority').val();
                var priority = $('#priority-select').val();

                if (!selectedTaskIds || !priority) {
                    toastr.error('Please select a priority');
                    return;
                }

                var $button = $(this);
                var originalHtml = $button.html();
                $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Updating...');

                $.ajax({
                    url: '{{ route("projecttask.automate.bulkUpdatePriority") }}',
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        task_ids: JSON.stringify(selectedTaskIds.split(',').map(id => parseInt(id.trim())).filter(id => id > 0)),
                        priority: priority,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.is_success) {
                            toastrs('Success', response.message, 'success');
                            $('#change-priority-modal').modal('hide');
                            $('#change-priority-form')[0].reset();
                            $('.task-checkbox').prop('checked', false);
                            $('#select-all').prop('checked', false);
                            updateBulkActionButtons();
                            if ($.fn.DataTable.isDataTable('#projects-task-table')) {
                                $('#projects-task-table').DataTable().ajax.reload(null, false);
                            }
                            getTaskCount();
                        } else {
                            toastrs('Error', response.message, 'error');
                        }
                        $button.prop('disabled', false).html(originalHtml);
                    },
                    error: function(xhr) {
                        var errorMsg = 'An error occurred';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        toastrs('Error', errorMsg, 'error');
                        $button.prop('disabled', false).html(originalHtml);
                    }
                });
            });

            // Bulk Status Update
            $('#update-status-btn').on('click', function() {
                var selectedTaskIds = $('#selected-task-ids-status').val();
                var status = $('#status-select').val();

                if (!selectedTaskIds || !status) {
                    toastr.error('Please select a status');
                    return;
                }

                var $button = $(this);
                var originalHtml = $button.html();
                $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Updating...');

                $.ajax({
                    url: '{{ route("projecttask.automate.bulkUpdateStatus") }}',
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        task_ids: JSON.stringify(selectedTaskIds.split(',').map(id => parseInt(id.trim())).filter(id => id > 0)),
                        status: status,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.is_success) {
                            toastrs('Success', response.message, 'success');
                            $('#change-status-modal').modal('hide');
                            $('#change-status-form')[0].reset();
                            $('.task-checkbox').prop('checked', false);
                            $('#select-all').prop('checked', false);
                            updateBulkActionButtons();
                            if ($.fn.DataTable.isDataTable('#projects-task-table')) {
                                $('#projects-task-table').DataTable().ajax.reload(null, false);
                            }
                            getTaskCount();
                        } else {
                            toastrs('Error', response.message, 'error');
                        }
                        $button.prop('disabled', false).html(originalHtml);
                    },
                    error: function(xhr) {
                        var errorMsg = 'An error occurred';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        toastrs('Error', errorMsg, 'error');
                        $button.prop('disabled', false).html(originalHtml);
                    }
                });
            });

            // Reset modals when hidden
            $('#change-assignor-modal, #change-assignee-modal, #change-etc-modal, #change-date-modal, #change-priority-modal, #change-status-modal').on('hidden.bs.modal', function () {
                $(this).find('form')[0].reset();
            });
        </script>
    @endpush
@endif