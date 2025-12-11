<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Multi-Task Assignment Form</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --light-bg: #f8fafc;
            --border: #e2e8f0;
        }

        body {
            background: #f8fafc;
            min-height: 100vh;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .form-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            max-width: 900px;
            margin: 0 auto;
            overflow: hidden;
        }

        .form-header {
            background: #fff;
            padding: 20px 30px;
            color: #1e293b;
            text-align: center;
            border-bottom: 1px solid #e2e8f0;
        }

        .form-header h2 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .form-header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-size: 14px;
        }

        .form-body {
            padding: 25px;
            background: #fff;
        }

        .assignor-section {
            background: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border-left: 4px solid var(--primary);
        }

        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 15px;
            margin-top: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e2e8f0;
        }

        .section-title:first-child {
            margin-top: 0;
        }

        .section-icon {
            display: none; /* Hide icons for cleaner look */
        }

        .task-container {
            background: white;
            border-radius: 6px;
            margin-bottom: 15px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: all 0.2s ease;
            border: 1px solid #e2e8f0;
        }

        .task-container:hover {
            border-color: #cbd5e1;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .task-header {
            background: #f8fafc;
            padding: 15px 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.3s ease;
            border-bottom: 1px solid #e2e8f0;
        }

        .task-header:hover {
            background: #f1f5f9;
        }

        .task-title {
            color: #1e293b;
            font-weight: 600;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .task-number {
            background: #6366f1;
            color: white;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 600;
        }

        .collapse-icon {
            transition: transform 0.3s ease;
            font-size: 20px;
        }

        .task-container.collapsed .collapse-icon {
            transform: rotate(-90deg);
        }

        .task-content {
            padding: 20px;
            display: block;
            background: white;
        }

        .task-container.collapsed .task-content {
            display: none;
        }

        .form-label {
            font-weight: 600;
            color: #475569;
            margin-bottom: 8px;
            font-size: 14px;
        }

        /* Match single task form styling - consistent width */
        .form-control-light, .form-control.form-control-light, 
        .form-control.form-control-light input,
        .form-control.form-control-light select,
        .form-control.form-control-light textarea {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 12px;
            transition: all 0.2s ease;
            font-size: 14px;
            background-color: #fff;
            width: 100%;
            box-sizing: border-box;
        }

        .form-control-light:focus, .form-control.form-control-light:focus,
        .form-control.form-control-light input:focus,
        .form-control.form-control-light select:focus,
        .form-control.form-control-light textarea:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            outline: none;
        }
        
        /* Ensure all form groups have consistent width */
        .form-group {
            width: 100%;
        }
        
        .form-group .form-control,
        .form-group .form-control-light,
        .form-group select,
        .form-group input,
        .form-group textarea {
            width: 100%;
        }

        /* Choices.js styling - match single task form exactly */
        .multi-select.choices {
            margin-bottom: 0;
            width: 100%;
        }

        .choices__inner {
            background-color: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 12px;
            min-height: 38px;
            width: 100%;
            box-sizing: border-box;
            font-size: 14px;
        }

        .choices__input {
            background-color: transparent;
            margin-bottom: 0;
            font-size: 14px;
            width: 100%;
            border: none;
            padding: 0;
        }

        .choices__list--dropdown {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .choices__item--selectable {
            padding: 8px 12px;
            font-size: 14px;
        }

        .choices__item--selectable.is-highlighted {
            background-color: #f1f5f9;
        }
        
        .choices__item--choice {
            padding: 8px 12px;
        }
        
        .choices__button {
            border: none;
            background: transparent;
            padding: 0 4px;
        }
        
        /* Match form-control-light styling for choices */
        .choices.is-focused .choices__inner {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }
        
        /* Input group styling for duration field */
        .input-group {
            width: 100%;
        }
        
        .input-group .form-control {
            width: 100%;
        }

        .input-group-text {
            border: 2px solid var(--border);
            background: white;
            border-left: none;
            border-radius: 0 10px 10px 0;
        }

        .input-group .form-control {
            border-right: none;
            border-radius: 10px 0 0 10px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success), #059669);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger), #dc2626);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .btn-light {
            background: #e2e8f0;
            color: #475569;
        }

        .btn-light:hover {
            background: #cbd5e1;
        }

        .add-task-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 16px;
            padding: 15px;
            border: 2px dashed var(--success);
            background: rgba(16, 185, 129, 0.05);
            color: var(--success);
        }

        .add-task-btn:hover {
            background: var(--success);
            color: white;
            border-style: solid;
        }

        .remove-task-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            font-size: 13px;
        }

        .modal-footer {
            padding: 25px 30px;
            background: var(--light-bg);
            border-top: 2px solid var(--border);
            gap: 12px;
        }

        .text-danger {
            color: var(--danger);
            margin-left: 3px;
        }

        .task-counter {
            background: #f0fdf4;
            color: #059669;
            padding: 10px 15px;
            border-radius: 6px;
            text-align: center;
            margin-bottom: 15px;
            font-weight: 600;
            border: 1px solid #bbf7d0;
        }

        .link-item {
            background: #f8fafc;
            padding: 15px;
            border-radius: 10px;
            border: 2px solid var(--border);
            transition: all 0.3s ease;
        }

        .link-item:hover {
            border-color: var(--primary);
            background: white;
        }

        .links-container-wrapper {
            margin-bottom: 15px;
        }

        .add-link-btn {
            border: 2px dashed var(--success);
            color: var(--success);
            background: rgba(16, 185, 129, 0.05);
            padding: 10px;
            transition: all 0.3s ease;
        }

        .add-link-btn:hover {
            background: rgba(16, 185, 129, 0.1);
            border-color: var(--success);
            color: var(--success);
            transform: translateY(-2px);
        }

        .remove-link-btn {
            padding: 10px !important;
            height: 45px;
        }

        .bulk-actions {
            background: #f8fafc;
            padding: 15px 20px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
        }

        .bulk-actions-title {
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quick-action-btn {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid;
            margin-right: 10px;
            margin-bottom: 10px;
        }

        .duplicate-task-btn {
            background: linear-gradient(135deg, var(--info), #2563eb);
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            margin-left: 10px;
            transition: all 0.3s ease;
        }

        .duplicate-task-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        .link-preview {
            font-size: 11px;
            color: #64748b;
            margin-top: 5px;
            word-break: break-all;
        }

        .keyboard-shortcut {
            font-size: 11px;
            background: #e2e8f0;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: monospace;
            margin-left: 8px;
        }

        @media (max-width: 768px) {
            .form-body {
                padding: 20px;
            }
            
            .task-content {
                padding: 20px;
            }
            
            .assignor-section {
                padding: 20px;
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .task-container {
            animation: slideIn 0.3s ease;
        }

        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }
        
        /* Status dropdown styling - match single task form */
        .status-select option {
            padding: 8px 12px;
            margin: 2px 0;
            border-radius: 4px;
        }
        
        .status-select option:hover {
            opacity: 0.8;
            transform: scale(1.02);
            transition: all 0.2s ease;
        }
        
        .status-select option {
            color: #000 !important;
        }
        
        /* Specific colors for common statuses */
        .status-select option[value="Todo"] {
            background-color:rgb(106, 193, 255) !important;
        }
        .status-select option[value="In Progress"] {
            background-color:rgb(248, 193, 14) !important;
        }
        .status-select option[value="Done"] {
            background-color:rgb(23, 252, 42) !important;
        }
        .status-select option[value="Need Help"] {
            background-color:rgb(255, 65, 255) !important;
        }
        .status-select option[value="Urgent"] {
            background-color:rgb(219, 0, 22) !important;
        }
        .status-select option[value="Review"] {
            background-color: #e1f5fe !important;
        }
        .status-select option[value="Hold"] {
            background-color: #f3e5f5 !important;
        }
        .status-select option[value="Need Approval"] {
            background-color:rgb(179, 255, 0) !important;
        }
        .status-select option[value="Not Started"] {
            background-color:rgb(251, 255, 0) !important;
        }
        .status-select option[value="Working"] {
            background-color:rgb(184, 4, 255) !important;
        }
        .status-select option[value="Monitor"] {
            background-color:rgb(118, 87, 255) !important;
        }
        .status-select option[value="Dependent"] {
            background-color:rgb(255, 133, 133) !important;
        }
        .status-select option[value="Approved"] {
            background-color:rgb(255, 230, 0) !important;
        }
        .status-select option[value="Rework"] {
            background-color:rgb(134, 34, 143) !important;
        }
        .status-select option[value="Q-Task"] {
            background-color:rgb(226, 131, 144) !important;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <form class="needs-validation" method="post" action="{{ route('tasks.save.multiple') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-body">                
                <!-- Task Counter -->
                <div class="task-counter">
                    Total Tasks: <span id="taskCount">1</span>
                    <span class="keyboard-shortcut">Ctrl+Enter</span> to add task
                </div>

                <!-- Bulk Actions -->
                <div class="bulk-actions">
                    <div class="bulk-actions-title">
                        <span>⚡</span>
                        Quick Actions
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Apply Same Values to All Tasks:</label>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="quick-action-btn btn-outline-primary" id="sameAssignorBtn">
                                📋 Same Assignor
                            </button>
                            <button type="button" class="quick-action-btn btn-outline-success" id="sameAssigneeBtn">
                                👤 Same Assignee
                            </button>
                            <button type="button" class="quick-action-btn btn-outline-info" id="sameGroupBtn">
                                📁 Same Group
                            </button>
                            <button type="button" class="quick-action-btn btn-outline-warning" id="samePriorityBtn">
                                ⚡ Same Priority
                            </button>
                            <button type="button" class="quick-action-btn btn-outline-secondary" id="sameStatusBtn">
                                📊 Same Status
                            </button>
                    <button type="button" class="quick-action-btn btn-outline-danger" id="clearLinksBtn">
                        🗑️ {{ __('Clear All Links') }}
                    </button>
                        </div>
                    </div>
                </div>

                <!-- Tasks Container -->
                <div id="tasksContainer">
                    <!-- Task 1 (Template) -->
                    <div class="task-container" data-task-number="1">
                        <!-- <div class="task-header" onclick="toggleTask('task1')"> -->
                            <div class="task-header">
                            <div class="task-title">
                                <span class="task-number">Task 1</span>
                                <span class="collapse-icon">▼</span>
                            </div>
                            <div style="display: flex; gap: 10px;">
                                <button type="button" class="duplicate-task-btn" title="Duplicate this task">
                                    📋 Duplicate
                                </button>
                                <button type="button" class="btn btn-sm btn-danger remove-task-btn" style="display:none;">
                                    ✕ Remove
                                </button>
                            </div>
                        </div>
                        <div class="task-content">
                            <!-- Assignment Details in Each Task -->
                            <div class="section-title">
                                Assignment Details
                            </div>
                            <div class="row mb-3">
                                <div class="form-group col-md-6">
                                    <label class="form-label">{{ __('Assignor')}}<x-required></x-required></label>
                                    <select class="multi-select choices assignor-select" id="assignor_task_1" name="assign_by[]" multiple="multiple" data-placeholder="{{ __('Select Users ...') }}" required>
                                        @foreach($users as $u)
                                            <option value="{{$u->email}}" @if($u->email==auth()->user()->email) selected @endif>{{ formatUserName($u->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">{{ __('Assign To')}}<x-required></x-required></label>
                                    <select class="multi-select choices assignee-select" id="assign_to_task_1" name="assign_to[]" multiple="multiple" data-placeholder="{{ __('Select Users ...') }}" required>
                                        <option value="all_members">{{ __('All Members') }}</option>
                                        <option value="all_managers">{{ __('All Managers') }}</option>
                                        @foreach($users as $u)
                                            <option value="{{$u->email}}">{{ formatUserName($u->name) }}</option>
                                        @endforeach
                                    </select>
                                    <p class="text-danger d-none user-validation">{{__('Assign To field is required.')}}</p>
                                </div>
                            </div>

                            <!-- Basic Info -->
                            <div class="section-title">
                                Basic Information
                            </div>
                            <div class="row mb-3">
                                <div class="form-group col-md-6">
                                    <label class="form-label">{{ __('Group')}}</label>
                                    <input type="text" class="form-control form-control-light" name="group[]" id="task-group" placeholder="{{ __('Enter Group')}}" maxlength="25">
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">{{ __('Task')}}<x-required></x-required></label>
                                    <input type="text" class="form-control form-control-light" name="title[]" placeholder="{{ __('Enter Task')}}" required>
                                </div>
                            </div>

                            <!-- Task Details -->
                            <div class="section-title">
                                Task Configuration
                            </div>
                            <div class="row mb-3">
                                <div class="form-group col-md-6">
                                    <label class="form-label">{{ __('Priority')}}</label>
                                    <select class="form-control form-control-light priority-select" name="priority[]" required>
                                        <option value="normal">{{ __('normal')}}</option>
                                        <option value="urgent">{{ __('urgent')}}</option>
                                        <option value="Take your time">{{ __('Take your time')}}</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">{{ __('Status')}}</label>
                                    <select class="form-control form-control-light status-select" name="stage_id[]" id="task-stage">
                                        <option value="">{{__('Select Status')}}</option>
                                        @foreach($stages as $stage)
                                            <option value="{{$stage->name}}" data-color="{{ $stage->color }}">{{$stage->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">{{ __('ETC (Min)')}}<x-required></x-required></label>
                                    <input type="number" class="form-control form-control-light etc-field" name="eta_time[]" 
                                           placeholder="{{ __('Enter ETA Time')}}" required 
                                           min="1" oninput="this.value = Math.abs(this.value.replace(/[^0-9]/g, '').slice(0, 4));">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="form-group col-md-6">
                                    <label class="form-label">{{ __('Duration')}}<x-required></x-required></label>
                                    <div class='input-group'>
                                        <input type='text' class="form-control form-control-light task-duration" id="duration_task_1" name="duration[]" required autocomplete="off" placeholder="Select date range" />
                                        <input type="hidden" class="task-start-date" name="start_date[]">
                                        <input type="hidden" class="task-due-date" name="due_date[]">
                                        <span class="input-group-text"><i class="feather icon-calendar"></i></span>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">{{ __('Description')}}</label>
                                    <textarea class="form-control form-control-light" name="description[]" rows="1" placeholder="Enter Description"></textarea>
                                </div>
                            </div>

                            <!-- Links Section - Match single task form structure -->
                            <div class="row mb-3">
                                <div class="form-group col-md-6">
                                    <label class="form-label">{{ __('L1')}}</label>
                                    <input type="text" class="form-control form-control-light link1-field" name="link1[]" placeholder="{{ __('Enter L1')}}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label">{{ __('L2')}}</label>
                                    <input type="text" class="form-control form-control-light link2-field" name="link2[]" placeholder="{{ __('Enter L2')}}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label">{{ __('Training Link')}}</label>
                                    <input type="text" class="form-control form-control-light link3-field" name="link3[]" placeholder="{{ __('Enter training Note')}}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label">{{ __('Video Link')}}</label>
                                    <input type="text" class="form-control form-control-light link4-field" name="link4[]" placeholder="{{ __('Enter video Note')}}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label">{{ __('Form Link')}}</label>
                                    <input type="text" class="form-control form-control-light link5-field" name="link5[]" placeholder="{{ __('Enter form Note')}}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label">{{ __('Form Report Link')}}</label>
                                    <input type="text" class="form-control form-control-light link7-field" name="link7[]" placeholder="{{ __('Enter form Note')}}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label">{{ __('Checklist Link')}}</label>
                                    <input type="text" class="form-control form-control-light link6-field" name="link6[]" placeholder="{{ __('Enter checklist link')}}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label">{{ __('PL')}}</label>
                                    <input type="text" class="form-control form-control-light link9-field" name="link9[]" placeholder="{{ __('Enter PL link')}}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label">{{ __('PROCESS')}}</label>
                                    <input type="text" class="form-control form-control-light link8-field" name="link8[]" placeholder="{{ __('Enter form Note')}}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Add Task Button -->
                <button type="button" class="btn add-task-btn" id="addTaskBtn">
                    <span style="font-size: 20px;">+</span>
                    Add Another Task
                </button>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-light" onclick="resetForm()">
                    🔄 Reset Form
                </button>
                <button type="submit" class="btn btn-primary">
                    ✓ Create Tasks
                </button>
            </div>
        </form>
    </div>

    <link rel="stylesheet" href="{{ asset('packages/workdo/Taskly/src/Resources/assets/libs/bootstrap-daterangepicker/daterangepicker.css')}} ">
    <script src="{{ asset('packages/workdo/Taskly/src/Resources/assets/libs/moment/min/moment.min.js')}}"></script>
    <script src="{{ asset('packages/workdo/Taskly/src/Resources/assets/libs/bootstrap-daterangepicker/daterangepicker.js')}}"></script>
    
    <script>
let taskCounter = 0;

// Utilities
function q(sel, ctx=document){ return ctx.querySelector(sel); }
function qAll(sel, ctx=document){ return Array.from(ctx.querySelectorAll(sel)); }

// Init - Use both DOMContentLoaded and jQuery ready for compatibility
function initializeForm() {
  taskCounter = qAll('.task-container').length || 1;
  
  // Wait for jQuery and moment to be loaded
  function initWhenReady() {
    if (typeof jQuery !== 'undefined' && typeof moment !== 'undefined' && jQuery.fn.daterangepicker) {
      initAllDatePickers();
      initializeMultiSelects();
      initializeStatusColors();
      updateStatistics();
      console.log('Form initialized.');
    } else {
      setTimeout(initWhenReady, 100);
    }
  }
  
  initWhenReady();
}

// Initialize Choices.js for multi-select fields - use same pattern as single task form
function initializeMultiSelects(context = document) {
  // Use the same initialization pattern as single task form (via common_bind or direct)
  if (typeof Choices === 'undefined') {
    setTimeout(() => initializeMultiSelects(context), 100);
    return;
  }
  
  // Find all choices selects that need initialization
  const choicesSelects = context.querySelectorAll('.multi-select.choices:not([data-choices-initialized])');
  
  choicesSelects.forEach(select => {
    // Skip if no ID (required for Choices.js in this app)
    if (!select.id) return;
    
    // Destroy existing Choices instance if any
    if (select.choices) {
      try {
        select.choices.destroy();
      } catch (e) {
        console.warn('Error destroying Choices:', e);
      }
    }
    
    // Remove any Choices.js generated elements
    const choicesContainer = select.parentElement.querySelector('.choices');
    if (choicesContainer && choicesContainer !== select && !choicesContainer.classList.contains('choices__inner')) {
      choicesContainer.remove();
    }
    
    select.setAttribute('data-choices-initialized', 'true');
    
    try {
      // Use same configuration as single task form
      new Choices('#' + select.id, {
        removeItemButton: true,
        searchEnabled: true,
        placeholder: true,
        placeholderValue: select.getAttribute('data-placeholder') || 'Please Select',
        loadingText: 'Loading...',
      });
    } catch (error) {
      console.error('Error initializing Choices:', error);
      select.removeAttribute('data-choices-initialized');
    }
  });
}

// Initialize status dropdown colors
function initializeStatusColors(context = document) {
  const statusSelects = context.querySelectorAll('.status-select');
  statusSelects.forEach(select => {
    Array.from(select.options).forEach(option => {
      if (option.dataset.color) {
        option.style.backgroundColor = option.dataset.color;
        const bgColor = option.dataset.color;
        const r = parseInt(bgColor.substr(1,2), 16);
        const g = parseInt(bgColor.substr(3,2), 16);
        const b = parseInt(bgColor.substr(5,2), 16);
        const brightness = (r * 299 + g * 587 + b * 114) / 1000;
        option.style.color = brightness > 128 ? '#000' : '#fff';
      }
    });
  });
}

// Initialize datepickers - fresh approach matching single task form exactly
function initAllDatePickers() {
  if (typeof jQuery === 'undefined' || typeof moment === 'undefined' || !jQuery.fn.daterangepicker) {
    setTimeout(initAllDatePickers, 100);
    return;
  }
  
  jQuery('.task-duration:not(.initialized)').each(function() {
    var $durationField = jQuery(this);
    var fieldId = $durationField.attr('id');
    
    if (!$fieldId) {
      // Generate unique ID if not present
      var taskNum = $durationField.closest('.task-container').attr('data-task-number') || '1';
      fieldId = 'duration_task_' + taskNum;
      $durationField.attr('id', fieldId);
    }
    
    $durationField.addClass('initialized');
    
    // Destroy existing daterangepicker if any
    if ($durationField.data('daterangepicker')) {
      $durationField.data('daterangepicker').remove();
    }
    
    var start = moment('{{ date('Y-m-d') }}', 'YYYY-MM-DD HH:mm:ss');
    var end = moment(start).add(4, 'days');
    
    var $taskContainer = $durationField.closest('.task-container');
    var $startDateField = $taskContainer.find('.task-start-date');
    var $dueDateField = $taskContainer.find('.task-due-date');
    
    function cb(start, end) {
      $durationField.val(start.format('MMM D, YY hh:mm A') + ' - ' + end.format('MMM D, YY hh:mm A'));
      if ($startDateField.length) $startDateField.val(start.format('YYYY-MM-DD HH:mm:ss'));
      if ($dueDateField.length) $dueDateField.val(end.format('YYYY-MM-DD HH:mm:ss'));
    }
    
    // Initialize daterangepicker exactly like single task form
    $durationField.daterangepicker({
      autoApply: true,
      timePicker: true,
      autoUpdateInput: false,
      startDate: start,
      endDate: end,
      locale: {
        format: 'MMMM D, YYYY hh:mm A',
        applyLabel: "{{__('Apply')}}",
        cancelLabel: "{{__('Cancel')}}",
        fromLabel: "{{__('From')}}",
        toLabel: "{{__('To')}}",
        daysOfWeek: [
          "{{__('Sun')}}",
          "{{__('Mon')}}",
          "{{__('Tue')}}",
          "{{__('Wed')}}",
          "{{__('Thu')}}",
          "{{__('Fri')}}",
          "{{__('Sat')}}"
        ],
        monthNames: [
          "{{__('January')}}",
          "{{__('February')}}",
          "{{__('March')}}",
          "{{__('April')}}",
          "{{__('May')}}",
          "{{__('June')}}",
          "{{__('July')}}",
          "{{__('August')}}",
          "{{__('September')}}",
          "{{__('October')}}",
          "{{__('November')}}",
          "{{__('December')}}"
        ],
      }
    }, cb);
    
    cb(start, end);
  });
}

// Function to refresh CSRF token
function refreshCsrfToken() {
  // Try to get token from parent window (if loaded in modal)
  let csrfToken = null;
  try {
    if (window.parent && window.parent !== window) {
      const parentMeta = window.parent.document.querySelector('meta[name="csrf-token"]');
      if (parentMeta) {
        csrfToken = parentMeta.getAttribute('content');
      }
    }
  } catch (e) {
    // Cross-origin or other error, ignore
  }
  
  // Fallback to current document
  if (!csrfToken) {
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (meta) {
      csrfToken = meta.getAttribute('content');
    }
  }
  
  // Update token in form
  if (csrfToken) {
    const tokenInput = document.querySelector('input[name="_token"]');
    if (tokenInput) {
      tokenInput.value = csrfToken;
    }
  }
}

// Initialize on DOM ready - use jQuery ready exactly like single task form
jQuery(function() {
  // Refresh CSRF token first
  refreshCsrfToken();
  
  // Initialize datepickers
  initAllDatePickers();
  
  // Initialize Choices.js dropdowns
  initializeMultiSelects();
  
  // Initialize status colors
  initializeStatusColors();
  
  // Update statistics
  updateStatistics();
  
  // Also initialize when modal is shown (for AJAX-loaded modals)
  jQuery(document).on('shown.bs.modal', '.modal', function() {
    // Refresh CSRF token when modal is shown
    refreshCsrfToken();
    
    setTimeout(function() {
      initAllDatePickers();
      initializeMultiSelects();
      initializeStatusColors();
    }, 300);
  });
});

// Bulk Actions for Assignor/Assignee
document.getElementById('sameAssignorBtn')?.addEventListener('click', function() {
  const firstTask = document.querySelector('.task-container');
  if (!firstTask) return;
  
  const firstAssignor = firstTask.querySelector('.assignor-select');
  if (!firstAssignor) {
    alert('Please select an Assignor in the first task first!');
    return;
  }
  
  // Get selected values from Choices.js instance or native select
  let selectedValues = [];
  if (firstAssignor.choices) {
    selectedValues = firstAssignor.choices.getValue(true);
  } else {
    Array.from(firstAssignor.selectedOptions).forEach(opt => selectedValues.push(opt.value));
  }
  
  if (selectedValues.length === 0) {
    alert('Please select an Assignor in the first task first!');
    return;
  }
  
  // Set values for all assignor selects
  const allAssignors = document.querySelectorAll('.assignor-select');
  allAssignors.forEach(select => {
    if (select.choices) {
      // Use Choices.js API
      select.choices.setValue(selectedValues);
    } else {
      // Native select
      Array.from(select.options).forEach(opt => {
        opt.selected = selectedValues.includes(opt.value);
      });
    }
  });
  
  alert('All Assignors set successfully!');
});

document.getElementById('sameAssigneeBtn')?.addEventListener('click', function() {
  const firstTask = document.querySelector('.task-container');
  if (!firstTask) return;
  
  const firstAssignee = firstTask.querySelector('.assignee-select');
  if (!firstAssignee) {
    alert('Please select an Assignee in the first task first!');
    return;
  }
  
  // Get selected values from Choices.js instance or native select
  let selectedValues = [];
  if (firstAssignee.choices) {
    selectedValues = firstAssignee.choices.getValue(true);
  } else {
    Array.from(firstAssignee.selectedOptions).forEach(opt => selectedValues.push(opt.value));
  }
  
  if (selectedValues.length === 0) {
    alert('Please select an Assignee in the first task first!');
    return;
  }
  
  // Set values for all assignee selects
  const allAssignees = document.querySelectorAll('.assignee-select');
  allAssignees.forEach(select => {
    if (select.choices) {
      // Use Choices.js API
      select.choices.setValue(selectedValues);
    } else {
      // Native select
      Array.from(select.options).forEach(opt => {
        opt.selected = selectedValues.includes(opt.value);
      });
    }
  });
  
  alert('All Assignees set successfully!');
});

document.getElementById('sameGroupBtn')?.addEventListener('click', function() {
  const firstTask = document.querySelector('.task-container');
  if (!firstTask) return;
  
  const firstGroup = firstTask.querySelector('input[name="group[]"]');
  if (!firstGroup || !firstGroup.value.trim()) {
    alert('Please enter a Group in the first task first!');
    return;
  }
  
  const selectedValue = firstGroup.value;
  const allGroups = document.querySelectorAll('input[name="group[]"]');
  
  allGroups.forEach(input => {
    input.value = selectedValue;
  });
  
  alert('All Groups set to: ' + selectedValue);
});

document.getElementById('samePriorityBtn')?.addEventListener('click', function() {
  const firstTask = document.querySelector('.task-container');
  if (!firstTask) return;
  
  const firstPriority = firstTask.querySelector('.priority-select');
  if (!firstPriority || !firstPriority.value) {
    alert('Please select a Priority in the first task first!');
    return;
  }
  
  const selectedValue = firstPriority.value;
  const allPriorities = document.querySelectorAll('.priority-select');
  
  allPriorities.forEach(select => {
    select.value = selectedValue;
  });
  
  alert('All Priorities set to: ' + firstPriority.options[firstPriority.selectedIndex].text);
});

document.getElementById('sameStatusBtn')?.addEventListener('click', function() {
  const firstTask = document.querySelector('.task-container');
  if (!firstTask) return;
  
  const firstStatus = firstTask.querySelector('.status-select');
  if (!firstStatus || !firstStatus.value) {
    alert('Please select a Status in the first task first!');
    return;
  }
  
  const selectedValue = firstStatus.value;
  const allStatuses = document.querySelectorAll('.status-select');
  
  allStatuses.forEach(select => {
    select.value = selectedValue;
  });
  
  alert('All Statuses set to: ' + firstStatus.options[firstStatus.selectedIndex].text);
});

// Clear All Links
document.getElementById('clearLinksBtn')?.addEventListener('click', function() {
  if (!confirm('Are you sure you want to clear all links from all tasks?')) return;
  
  document.querySelectorAll('.link1-field, .link2-field, .link3-field, .link4-field, .link5-field, .link6-field, .link7-field, .link8-field, .link9-field').forEach(input => {
    input.value = '';
  });
  
  updateStatistics();
  alert('All links cleared successfully!');
});

// DatePickers - simplified function for dynamically added tasks
function initializeDatePickers(context = document) {
  initAllDatePickers();
}

// Event Delegation
document.addEventListener('click', function(e){
  // Duplicate Task
  const dupBtn = e.target.closest('.duplicate-task-btn');
  if (dupBtn) {
    e.preventDefault();
    e.stopPropagation();
    const srcTask = dupBtn.closest('.task-container');
    if (!srcTask) return;

    const clone = srcTask.cloneNode(true);
    taskCounter++;
    const newId = 'task' + taskCounter;
    clone.id = newId;
    clone.setAttribute('data-task-number', taskCounter);

    const taskNumberSpan = clone.querySelector('.task-number');
    if (taskNumberSpan) taskNumberSpan.textContent = 'Task ' + taskCounter;
    
    // Update IDs for cloned elements
    const assignorSelect = clone.querySelector('.assignor-select');
    const assigneeSelect = clone.querySelector('.assignee-select');
    const durationField = clone.querySelector('.task-duration');
    
    if (assignorSelect) assignorSelect.id = 'assignor_task_' + taskCounter;
    if (assigneeSelect) assigneeSelect.id = 'assign_to_task_' + taskCounter;
    if (durationField) durationField.id = 'duration_task_' + taskCounter;

    const removeTaskBtn = clone.querySelector('.remove-task-btn');
    if (removeTaskBtn) removeTaskBtn.style.display = 'flex';

    const tasksContainer = document.getElementById('tasksContainer');
    tasksContainer.appendChild(clone);
    initializeNewTaskAfterAppend(clone);

    renumberTasks();
    updateStatistics();
    clone.scrollIntoView({behavior:'smooth', block:'center'});
    return;
  }

  // Add New Task
  const addTaskBtn = e.target.closest('#addTaskBtn, .add-task-btn');
  if (addTaskBtn) {
    e.preventDefault();
    const firstTask = document.querySelector('.task-container');
    if (!firstTask) return;

    const newTask = firstTask.cloneNode(true);
    taskCounter++;
    newTask.id = 'task' + taskCounter;
    newTask.setAttribute('data-task-number', taskCounter);

    clearInputsInTask(newTask);
    
    // Update IDs for cloned elements
    const assignorSelect = newTask.querySelector('.assignor-select');
    const assigneeSelect = newTask.querySelector('.assignee-select');
    const durationField = newTask.querySelector('.task-duration');
    
    if (assignorSelect) assignorSelect.id = 'assignor_task_' + taskCounter;
    if (assigneeSelect) assigneeSelect.id = 'assign_to_task_' + taskCounter;
    if (durationField) durationField.id = 'duration_task_' + taskCounter;

    const removeBtnTask = newTask.querySelector('.remove-task-btn');
    if (removeBtnTask) removeBtnTask.style.display = 'flex';

    const tnum = newTask.querySelector('.task-number');
    if (tnum) tnum.textContent = 'Task ' + taskCounter;

    document.getElementById('tasksContainer').appendChild(newTask);
    initializeNewTaskAfterAppend(newTask);

    renumberTasks();
    updateStatistics();
    newTask.scrollIntoView({behavior:'smooth', block:'center'});
    return;
  }

  // Toggle task collapse
  const header = e.target.closest('.task-header');
  if (header && !e.target.closest('button')) {
    const task = header.closest('.task-container');
    if (!task) return;
    task.classList.toggle('collapsed');
    return;
  }

  // Remove Task
  const removeTaskBtn = e.target.closest('.remove-task-btn');
  if (removeTaskBtn) {
    e.stopPropagation();
    const taskToRemove = removeTaskBtn.closest('.task-container');
    if (!taskToRemove) return;
    
    if (!confirm('Are you sure you want to remove this task?')) return;
    
    taskToRemove.remove();
    renumberTasks();
    updateStatistics();
    return;
  }
});

// Status change handler for urgent priority (like single task form)
document.addEventListener('change', function(e){
  if (e.target && e.target.matches('.status-select')) {
    if (e.target.value === 'Urgent') {
      const taskContainer = e.target.closest('.task-container');
      const durationField = taskContainer ? taskContainer.querySelector('.task-duration') : null;
      if (durationField && jQuery(durationField).data('daterangepicker')) {
        const urgentStart = moment();
        const urgentEnd = moment().add(1, 'days');
        const picker = jQuery(durationField).data('daterangepicker');
        picker.setStartDate(urgentStart);
        picker.setEndDate(urgentEnd);
        
        const startDateField = taskContainer.querySelector('.task-start-date');
        const dueDateField = taskContainer.querySelector('.task-due-date');
        if (startDateField) jQuery(startDateField).val(urgentStart.format('YYYY-MM-DD HH:mm:ss'));
        if (dueDateField) jQuery(dueDateField).val(urgentEnd.format('YYYY-MM-DD HH:mm:ss'));
        jQuery(durationField).val(urgentStart.format('MMM D, YY hh:mm A') + ' - ' + urgentEnd.format('MMM D, YY hh:mm A'));
      }
    }
  }
});

// Helper: clear inputs inside a task
function clearInputsInTask(task) {
  const inputs = task.querySelectorAll('input:not([type="hidden"]), textarea');
  inputs.forEach(input => {
    if (input.type === 'text' || input.type === 'number' || input.tagName === 'TEXTAREA') {
      input.value = '';
    } else if (input.tagName === 'INPUT' && (input.type === 'checkbox' || input.type === 'radio')) {
      input.checked = false;
    }
  });
  
  // Clear multi-select fields (Choices.js) - destroy and recreate
  const multiSelects = task.querySelectorAll('.multi-select');
  multiSelects.forEach(sel => {
    // Destroy existing Choices instance
    if (sel.choices) {
      sel.choices.destroy();
    }
    
    // Remove Choices.js generated elements
    const choicesContainer = sel.parentElement.querySelector('.choices');
    if (choicesContainer && choicesContainer !== sel) {
      choicesContainer.remove();
    }
    
    // Clear selections
    Array.from(sel.options).forEach(opt => opt.selected = false);
    
    // Remove initialization flag so it can be reinitialized
    sel.removeAttribute('data-choices-initialized');
  });
  
  // Clear regular selects
  const selects = task.querySelectorAll('select:not(.multi-select)');
  selects.forEach(sel => { 
    sel.selectedIndex = 0;
    // Trigger change event for status to reset colors
    if (sel.classList.contains('status-select')) {
      sel.dispatchEvent(new Event('change'));
    }
  });
}

// Link preview functions removed - using individual input fields now

// initialize new task after append
function initializeNewTaskAfterAppend(newTask) {
  if (!newTask) return;
  
  // Clear any initialized class from duration fields
  const durationFields = newTask.querySelectorAll('.task-duration');
  durationFields.forEach(field => field.classList.remove('initialized'));
  
  // Destroy any existing Choices instances and remove generated elements
  const multiSelects = newTask.querySelectorAll('.multi-select');
  multiSelects.forEach(select => {
    // Destroy existing Choices instance
    if (select.choices) {
      try {
        select.choices.destroy();
      } catch (e) {
        console.warn('Error destroying Choices:', e);
      }
    }
    
    // Remove Choices.js generated elements
    const choicesContainer = select.parentElement.querySelector('.choices');
    if (choicesContainer && choicesContainer !== select && !choicesContainer.classList.contains('choices__inner')) {
      choicesContainer.remove();
    }
    
    // Remove initialization flag
    select.removeAttribute('data-choices-initialized');
  });
  
  // Wait for jQuery, moment, and Choices to be ready, then initialize
  function initNewTask() {
    if (typeof jQuery !== 'undefined' && typeof moment !== 'undefined' && jQuery.fn.daterangepicker && typeof Choices !== 'undefined') {
      // Small delay to ensure DOM is ready
      setTimeout(() => {
        initAllDatePickers();
        initializeMultiSelects(newTask);
        initializeStatusColors(newTask);
      }, 200);
    } else {
      setTimeout(initNewTask, 100);
    }
  }
  
  initNewTask();
}

// renumber tasks
function renumberTasks() {
  const tasks = qAll('.task-container');
  tasks.forEach((task, index) => {
    const num = index + 1;
    task.id = 'task' + num;
    task.setAttribute('data-task-number', num);
    
    // Update IDs for all elements in this task
    const assignorSelect = task.querySelector('.assignor-select');
    const assigneeSelect = task.querySelector('.assignee-select');
    const durationField = task.querySelector('.task-duration');
    
    if (assignorSelect) assignorSelect.id = 'assignor_task_' + num;
    if (assigneeSelect) assigneeSelect.id = 'assign_to_task_' + num;
    if (durationField) {
      durationField.id = 'duration_task_' + num;
      // Reinitialize datepicker if it was already initialized
      if (durationField.classList.contains('initialized') && typeof jQuery !== 'undefined' && jQuery.fn.daterangepicker) {
        if (jQuery(durationField).data('daterangepicker')) {
          jQuery(durationField).data('daterangepicker').remove();
        }
        durationField.classList.remove('initialized');
      }
    }
    
    const tn = task.querySelector('.task-number');
    if (tn) tn.textContent = 'Task ' + num;
    const removeBtn = task.querySelector('.remove-task-btn');
    if (removeBtn) {
      if (num === 1) removeBtn.style.display = 'none';
      else removeBtn.style.display = 'flex';
    }
  });
  taskCounter = tasks.length;
  const taskCountEl = document.getElementById('taskCount');
  if (taskCountEl) taskCountEl.textContent = taskCounter;
  
  // Reinitialize datepickers after renumbering
  if (typeof jQuery !== 'undefined' && typeof moment !== 'undefined' && jQuery.fn.daterangepicker) {
    setTimeout(() => {
      initAllDatePickers();
    }, 100);
  }
}

// Statistics
function updateStatistics() {
  const totalTasks = qAll('.task-container').length;
  const taskCountEl = document.getElementById('taskCount');
  if (taskCountEl) taskCountEl.textContent = totalTasks;

  let totalLinks = 0;
  qAll('.link1-field, .link2-field, .link3-field, .link4-field, .link5-field, .link6-field, .link7-field, .link8-field, .link9-field').forEach(inp => { 
    if (inp.value.trim()) totalLinks++; 
  });
  
  let totalETC = 0;
  qAll('.etc-field').forEach(inp => { totalETC += parseInt(inp.value || 0, 10); });
}

// Links are now handled as individual fields, no need to collect

// Toggle task function (called from onclick)
function toggleTask(taskId) {
  const task = document.getElementById(taskId);
  if (task) task.classList.toggle('collapsed');
}

// Reset form
function resetForm() {
  if (!confirm('Are you sure you want to reset the entire form? All data will be lost.')) return;
  
  const form = document.querySelector('form.needs-validation');
  if (form) form.reset();
  
  // Keep only first task
  const tasks = qAll('.task-container');
  tasks.forEach((task, index) => {
    if (index > 0) task.remove();
  });
  
  // Clear all inputs in first task
  const firstTask = document.querySelector('.task-container');
  if (firstTask) {
    clearInputsInTask(firstTask);
    // Reinitialize date picker for first task
    const durationField = firstTask.querySelector('.task-duration');
    if (durationField) {
      durationField.classList.remove('initialized');
      initializeDatePickers(firstTask);
    }
  }
  
  taskCounter = 1;
  renumberTasks();
  updateStatistics();
  alert('Form has been reset!');
}

// Form submit validation
document.querySelector('form.needs-validation')?.addEventListener('submit', function(e){
  e.preventDefault();
  
        // Validate all required fields
  const tasks = qAll('.task-container');
  let isValid = true;
  let errorMessage = '';
  
  tasks.forEach((task, index) => {
    const title = task.querySelector('input[name="title[]"]');
    const assignTo = task.querySelector('select[name="assign_to[]"]');
    const assignBy = task.querySelector('select[name="assign_by[]"]');
    const group = task.querySelector('input[name="group[]"]');
    const priority = task.querySelector('select[name="priority[]"]');
    const duration = task.querySelector('.task-duration');
    const etaTime = task.querySelector('input[name="eta_time[]"]');
    
    if (!title || !title.value.trim()) {
      isValid = false;
      errorMessage = `Task ${index + 1}: Title is required`;
      return;
    }
    
    // Check assignTo (Choices.js or native)
    let assignToSelected = false;
    if (assignTo) {
      if (assignTo.choices) {
        assignToSelected = assignTo.choices.getValue(true).length > 0;
      } else {
        assignToSelected = assignTo.selectedOptions.length > 0;
      }
    }
    if (!assignToSelected) {
      isValid = false;
      errorMessage = `Task ${index + 1}: Assign To is required`;
      return;
    }
    
    // Check assignBy (Choices.js or native)
    let assignBySelected = false;
    if (assignBy) {
      if (assignBy.choices) {
        assignBySelected = assignBy.choices.getValue(true).length > 0;
      } else {
        assignBySelected = assignBy.selectedOptions.length > 0;
      }
    }
    if (!assignBySelected) {
      isValid = false;
      errorMessage = `Task ${index + 1}: Assignor is required`;
      return;
    }
    
    if (!group || !group.value.trim()) {
      isValid = false;
      errorMessage = `Task ${index + 1}: Group is required`;
      return;
    }
    if (!priority || !priority.value) {
      isValid = false;
      errorMessage = `Task ${index + 1}: Priority is required`;
      return;
    }
    if (!duration || !duration.value.trim()) {
      isValid = false;
      errorMessage = `Task ${index + 1}: Duration is required`;
      return;
    }
    if (!etaTime || !etaTime.value || parseInt(etaTime.value) < 1) {
      isValid = false;
      errorMessage = `Task ${index + 1}: ETC (Min) must be at least 1`;
      return;
    }
  });
  
  if (!isValid) {
    alert(errorMessage);
    this.classList.add('was-validated');
    return;
  }
  
  // Format duration fields properly
  formatDurationFields();
  
  // Ensure CSRF token is up to date
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                    document.querySelector('input[name="_token"]')?.value;
  if (csrfToken) {
    const tokenInput = this.querySelector('input[name="_token"]');
    if (tokenInput) {
      tokenInput.value = csrfToken;
    } else {
      // Add token if missing
      const hiddenInput = document.createElement('input');
      hiddenInput.type = 'hidden';
      hiddenInput.name = '_token';
      hiddenInput.value = csrfToken;
      this.appendChild(hiddenInput);
    }
  }
  
  // Submit the form
  this.submit();
});

// Format duration fields to "start to end" format for backend
function formatDurationFields() {
  qAll('.task-container').forEach(taskContainer => {
    const durationField = taskContainer.querySelector('.task-duration');
    const startDateField = taskContainer.querySelector('.task-start-date');
    const dueDateField = taskContainer.querySelector('.task-due-date');
    
    if (durationField && startDateField && dueDateField) {
      // Get values from hidden fields (already set by daterangepicker callback)
      const startValue = jQuery(startDateField).val();
      const dueValue = jQuery(dueDateField).val();
      
      if (startValue && dueValue) {
        // Format for backend: "YYYY-MM-DD HH:mm:ss to YYYY-MM-DD HH:mm:ss"
        durationField.value = startValue + ' to ' + dueValue;
      }
    }
  });
}
    </script>
</body>
</html>