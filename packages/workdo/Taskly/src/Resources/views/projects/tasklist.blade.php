<style>
  .bg-purple {
    background-color: #9b59b6 !important;
  }
  
  /* Modern Stats Cards - Horizontal Layout */
  .stats-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 20px;
    border-radius: 12px;
    background: white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    border: 1px solid rgba(0, 0, 0, 0.05);
    min-width: 150px;
  }
  
  .stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
  }
  
  .stats-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
  }
  
  .stats-content {
    flex: 1;
    position: relative;
  }
  
  .stats-label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    opacity: 0.7;
    margin-bottom: 2px;
  }
  
  .stats-value {
    font-size: 24px;
    font-weight: 700;
    line-height: 1;
  }
  
  .stats-badge {
    position: absolute;
    top: -2px;
    right: 0;
    background: rgba(0, 0, 0, 0.1);
    color: rgba(0, 0, 0, 0.6);
    font-size: 9px;
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 600;
  }
  
  /* Color Variants */
  .stats-card-primary { border-left: 3px solid #4e73df; }
  .stats-card-primary .stats-icon { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); color: white; }
  .stats-card-primary .stats-label { color: #4e73df; }
  .stats-card-primary .stats-value { color: #2e59d9; }
  
  .stats-card-info { border-left: 3px solid #36b9cc; }
  .stats-card-info .stats-icon { background: linear-gradient(135deg, #36b9cc 0%, #258391 100%); color: white; }
  .stats-card-info .stats-label { color: #36b9cc; }
  .stats-card-info .stats-value { color: #2c9faf; }
  
  .stats-card-danger { border-left: 3px solid #e74a3b; }
  .stats-card-danger .stats-icon { background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%); color: white; }
  .stats-card-danger .stats-label { color: #e74a3b; }
  .stats-card-danger .stats-value { color: #d52a1a; }
  
  .stats-card-warning { border-left: 3px solid #e7a90aff; }
  .stats-card-warning .stats-icon { background: linear-gradient(135deg, #e7a90aff 0%, #e7a90aff 100%); color: white; }
  .stats-card-warning .stats-label { color: #e7a90aff; }
  .stats-card-warning .stats-value { color: #e7a90aff; }
  
  .stats-card-cyan { border-left: 3px solid #17a2b8; }
  .stats-card-cyan .stats-icon { background: linear-gradient(135deg, #17a2b8 0%, #117a8b 100%); color: white; }
  .stats-card-cyan .stats-label { color: #17a2b8; }
  .stats-card-cyan .stats-value { color: #138496; }
  
  .stats-card-success { border-left: 3px solid #1cc88a; }
  .stats-card-success .stats-icon { background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%); color: white; }
  .stats-card-success .stats-label { color: #1cc88a; }
  .stats-card-success .stats-value { color: #17a673; }
  
  .stats-card-orange { border-left: 3px solid #fd7e14; }
  .stats-card-orange .stats-icon { background: linear-gradient(135deg, #fd7e14 0%, #dc6502 100%); color: white; }
  .stats-card-orange .stats-label { color: #fd7e14; }
  .stats-card-orange .stats-value { color: #e8590c; }
  
  .stats-card-teal { border-left: 3px solid #20c997; }
  .stats-card-teal .stats-icon { background: linear-gradient(135deg, #20c997 0%, #199d76 100%); color: white; }
  .stats-card-teal .stats-label { color: #20c997; }
  .stats-card-teal .stats-value { color: #1ab386; }
  
  .stats-card-purple { border-left: 3px solid #6f42c1; }
  .stats-card-purple .stats-icon { background: linear-gradient(135deg, #6f42c1 0%, #59359a 100%); color: white; }
  .stats-card-purple .stats-label { color: #6f42c1; }
  .stats-card-purple .stats-value { color: #5a32a3; }
  
  /* Responsive */
  @media (max-width: 1400px) {
    .stats-card {
      min-width: 130px;
      padding: 12px 16px;
    }
    .stats-icon {
      width: 40px;
      height: 40px;
      font-size: 20px;
    }
    .stats-value {
      font-size: 20px;
    }
  }

  /* Hover Effects */
  #etcModal .form-control:hover {
    border-color: #a0aec0;
  }
  
  #etcModal .form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
    outline: none;
  }
  
  #etcModal .modal-footer button[type="button"]:hover { 
    background: #f1f5f9;
    transform: translateY(-1px);
  }
  
  #etcModal .modal-footer button[type="submit"] {
    position: relative;
    overflow: hidden;
  }
  
  #etcModal .modal-footer button[type="submit"]:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 12px rgba(101, 117, 255, 0.25);
  }
  
  #etcModal .modal-footer button[type="submit"]::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 5px;
    height: 5px;
    background: rgba(255, 255, 255, 0.5);
    opacity: 0;
    border-radius: 100%;
    transform: scale(1, 1) translate(-50%);
    transform-origin: 50% 50%;
  }
  
  #etcModal .modal-footer button[type="submit"]:focus:not(:active)::after {
    animation: ripple 0.6s ease-out;
  }
  
  @keyframes ripple {
    0% {
      transform: scale(0, 0);
      opacity: 0.5;
    }
    100% {
      transform: scale(20, 20);
      opacity: 0;
    }
  }
  
  /* Modal Entrance Animation */
  .modal.fade .modal-dialog {
    transform: translateY(20px);
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.25, 0.5, 0.5, 1.25);
  }
  
  .modal.show .modal-dialog {
    transform: translateY(0);
    opacity: 1;
  }

</style>

@extends('layouts.main')
@section('page-title')
    {{ __('Task Board') }}
@endsection
@section('title')
    {{ __('Task Board') }}
@endsection
@section('page-breadcrumb')
    {{ __('Project') }},{{ __('Project Details') }},{{ __('Task Board') }}
@endsection
@push('css')
    @include('layouts.includes.datatable-css')
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    /* Task Choice Card Styles */
    .task-choice-card {
      border: none;
      border-radius: 16px;
      padding: 1.75rem;
      height: 100%;
      color: #fff;
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
      transform: translateY(0);
      transition: all 0.25s ease;
      cursor: pointer;
      position: relative;
      overflow: hidden;
    }
    
    /* Fix for dropdown overflow in table */
    .table-responsive {
        overflow-x: auto !important;
        overflow-y: visible !important;
        margin-top: 20px;
    }
    
    .card-body.table-border-style {
      overflow-x: auto !important;
      overflow-y: visible !important;
    }
    
    /* Ensure dropdowns appear above table content */
    .choices__list--dropdown,
    .select2-dropdown,
    .choices__list[aria-expanded="true"] {
      z-index: 9999 !important;
      position: absolute !important;
      max-height: 300px !important;
    }
    
    .choices[data-type*="select-multiple"] .choices__inner {
      z-index: 1 !important;
    }
    
    .task-choice-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 15px 30px rgba(0,0,0,0.25);
    }
    
    .task-choice-card:active {
      transform: translateY(-4px);
    }
    
    .task-choice-card .icon-wrap {
      width: 56px;
      height: 56px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: rgba(255,255,255,0.2);
      backdrop-filter: blur(5px);
      margin-bottom: 1.25rem;
    }
    
    .task-choice-card h5 {
      margin: 0.5rem 0;
      font-weight: 600;
      font-size: 1.25rem;
      color:white;
    }
    
    .task-choice-card p {
      margin: 0;
      opacity: 0.85;
      font-size: 0.95rem;
    }
    
    .bg-gradient-blue {
      background: linear-gradient(141.55deg, #ff6f28 3.46%, #ff6f28 99.86%), #ff6f28;
    }
    
    .bg-gradient-amber {
      background: linear-gradient(141.55deg, #ff6f28 3.46%, #ff6f28 99.86%), #ff6f28;
    }
    
    .bg-gradient-teal {
      background: linear-gradient(141.55deg, #ff6f28 3.46%, #ff6f28 99.86%), #ff6f28;
    }
    
    .coming-soon-badge {
      position: absolute;
      top: 12px;
      right: 12px;
      background: rgba(255,255,255,0.25);
      color: #fff;
      font-size: 0.75rem;
      padding: 0.25rem 0.75rem;
      border-radius: 999px;
      font-weight: 500;
    }

    .d_modal-content {
      text-align: center;
      padding: 20px;
      border-radius: 15px;
    }

    /* Change Assignor Button Styles */
    .change-assignor-btn:hover {
      background-color: #17a2b8 !important;
      color: white !important;
      transform: translateY(-1px);
      transition: all 0.2s ease;
    }
    
    .change-assignor-btn:disabled {
      background-color: #6c757d !important;
      color: #ffffff !important;
      cursor: not-allowed;
      opacity: 0.6;
    }

    .d_warning-icon {
      font-size: 50px;
      color: orange;
      margin-bottom: 15px;
    }

    .d_star-rating i {
      font-size: 24px;
      color: #ccc;
      cursor: pointer;
    }

    .d_star-rating i.selected {
      color: gold;
    }

    .d_btn-danger {
      background-color: red;
      border: none;
    }

    .d_btn-primary {
      background-color: #4731D3;
      border: none;
    }
        /* Task Toggle Styles */
    .task-toggle-wrapper {
      display: flex;
      justify-content: center;
      align-items: center;
      margin-bottom: 20px;
    }

    .toggle-indicator {
  position: absolute;
  top: 3px;
  left: 3px;
  width: calc(25% - 6px); /* 4 equal segments */
  height: 44px;
  background: #4CAF50;
  border-radius: 25px;
  transition: left 0.3s, background 0.3s;
  pointer-events: none;
  z-index: 1;
}

/* Map states to the indicator positions matching your HTML order:
   0: all    -> left: 3px
   1: overdue-> left: 33.33% + 3px
   2: urgent -> left: 66.66% + 3px
*/
.toggle[data-state="all"]     .toggle-indicator { left: 3px;                 background: #007bff; } /* All  */
.toggle[data-state="overdue"] .toggle-indicator { left: calc(33.33% + 3px);   background: #dc3545; } /* Overdue */
.toggle[data-state="urgent"]  .toggle-indicator { left: calc(66.66% + 3px);   background: #ff000d; } /* Urgent */

/* Make active option text white */
.toggle[data-state="all"]     .toggle-option[data-value="all"],
.toggle[data-state="overdue"] .toggle-option[data-value="overdue"],
.toggle[data-state="urgent"]  .toggle-option[data-value="urgent"] {
  color: #fff;
}

/* Ensure options layout matches segments (so indicator lands correctly) */
.toggle {
  position: relative;
  display: flex;
  width: 100%; /* keep as-is or set fixed width */
  height: 50px; /* match your indicator height + padding */
  box-sizing: border-box;
}

.toggle-option {
  flex: 0 0 25%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 2;
  user-select: none;
}

  </style>
@endpush
@section('page-action')
    <div class="d-flex">
        @stack('addButtonHook')

        @permission('task create')
            <a class="btn btn-sm btn-primary me-2" data-ajax-popup="true" data-size="lg" data-title="{{ __('Create Single Task') }}"
                data-url="{{ route('tasks.create') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="{{ __('Create a single task') }}"><i
                    class="ti ti-plus"></i></a>
            <a class="btn btn-sm btn-primary me-2" data-ajax-popup="true" data-size="xl" data-title="{{ __('Create Multiple Tasks') }}"
                data-url="{{ route('tasks.create.multiple') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="{{ __('Create multiple tasks at once') }}"><i
                    class="ti ti-files"></i></a>
            <a href="{{ route('project.staging') }}" target="_blank" class="btn btn-sm btn-primary me-2" data-bs-toggle="tooltip" data-bs-placement="bottom"
                data-bs-original-title="{{ __('Manage Staging') }}"><i class="fa fa-sitemap"></i></a>
                    
            <a class="btn btn-sm btn-primary me-2" href="{{ route('reviews.index') }}" target="_blank" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="{{ __('View and manage task reviews and ratings') }}">
                <i class="ti ti-star"></i>
            </a>        
             <a class="btn btn-sm btn-primary me-2" data-ajax-popup="true" data-size="lg" data-title="{{ __('Import Task') }}"
                data-url="{{ route('tasks.import') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-original-title="{{ __('Import tasks from a CSV or Excel file') }}"><i
                    class="fa fa-upload"></i></a>
                  
        @endpermission

    </div>
@endsection
@section('filter')
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="container">
                <div class="row justify-content-center">
                    
                </div>
            </div>
    </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="container-fluid mt-3 px-4">
                <!-- Modern Stats Cards in One Line -->
                <div class="row g-3 mb-4">
                    <!-- Total Card -->
                    <div class="col-auto">
                        <div class="stats-card stats-card-primary">
                            <div class="stats-icon">
                                <i class="ti ti-list-check"></i>
                            </div>
                            <div class="stats-content">
                                <div class="stats-label">Total</div>
                                <div class="stats-value" id="total-count">0</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pending Card -->
                    <div class="col-auto">
                        <div class="stats-card stats-card-info">
                            <div class="stats-icon">
                                <i class="ti ti-clock"></i>
                            </div>
                            <div class="stats-content">
                                <div class="stats-label">Pending</div>
                                <div class="stats-value" id="pending-count">{{ $pendingTask }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Overdue Card -->
                    <div class="col-auto">
                        <div class="stats-card stats-card-danger">
                            <div class="stats-icon">
                                <i class="ti ti-alert-triangle"></i>
                            </div>
                            <div class="stats-content">
                                <div class="stats-label">Overdue</div>
                                <div class="stats-value" id="overdue-count">{{ $overdueTask }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- ETC Card -->
                    <div class="col-auto">
                        <div class="stats-card stats-card-warning">
                            <div class="stats-icon">
                                <i class="ti ti-hourglass"></i>
                            </div>
                            <div class="stats-content">
                                <div class="stats-label">ETC</div>
                                <div class="stats-value" id="total_eta">0</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- ATC Card -->
                    <div class="col-auto">
                        <div class="stats-card stats-card-cyan">
                            <div class="stats-icon">
                                <i class="ti ti-calendar-time"></i>
                            </div>
                            <div class="stats-content">
                                <div class="stats-label">ATC</div>
                                <div class="stats-value" id="total_atc">0</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Done Card -->
                    <div class="col-auto">
                        <div class="stats-card stats-card-success">
                            <div class="stats-icon">
                                <i class="ti ti-circle-check"></i>
                            </div>
                            <div class="stats-content">
                                <div class="stats-label">Done</div>
                                <div class="stats-value" id="completed-count">{{ $competeTask }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- D ETC Card -->
                    <div class="col-auto">
                        <div class="stats-card stats-card-orange">
                            <div class="stats-icon">
                                <i class="ti ti-checkbox"></i>
                            </div>
                            <div class="stats-content">
                                <div class="stats-label">D ETC</div>
                                <div class="stats-value" id="filtered-etc-count">0</div>
                                <!-- <div class="stats-badge">L30</div> -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- D ATC Card -->
                    <div class="col-auto">
                        <div class="stats-card stats-card-teal">
                            <div class="stats-icon">
                                <i class="ti ti-clipboard-check"></i>
                            </div>
                            <div class="stats-content">
                                <div class="stats-label">D ATC</div>
                                <div class="stats-value" id="filtered-atc-count">0</div>
                                <!-- <div class="stats-badge">L30</div> -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- TeamLogger Card -->
                    <div class="col-auto">
                        <div class="stats-card stats-card-purple">
                            <div class="stats-icon">
                                <i class="ti ti-device-desktop-analytics"></i>
                            </div>
                            <div class="stats-content">
                                <div class="stats-label">T Logger</div>
                                <div class="stats-value" id="teamlogger-hours">0</div>
                                <!-- <div class="stats-badge">L30</div> -->
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Filter Section -->
                <div class="row mt-1 align-items-center">
               <div class="col-md-2 mb-3"></div>
                    <div class="col-md-6 mb-3">
                        
                        <form class="d-flex gap-2 align-items-center">
                            <div class="flex-grow-1">
                                <label class="form-label">{{ __('Group')}}</label>
                                <input type="text" class="form-control form-control-light" id="group_name" name="group_name" placeholder="{{ __('Enter Group') }}">
                            </div>
                             <div class="flex-grow-1">
                                <label class="form-label">{{ __('Task')}}</label>
                                <input type="text" class="form-control form-control-light" id="task_name" name="task_name" placeholder="{{ __('Enter Task') }}">
                            </div>
                            <div class="flex-grow-1">
                                <label class="form-label">{{ __('Assignor')}}</label>
                                <select class=" multi-select choices" id="assignor_name" name="assignor_name"  multiple="multiple" data-placeholder="{{ __('Select Users ...') }}" required>
                                    <option value="">{{__('Select assignor')}}</option>
                                    @foreach($users as $u)
                                        <option value="{{$u->email}}">{{ formatUserName($u->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label class="form-label">{{ __('Assignee')}}</label>
                                <select class=" multi-select choices" id="assignee_name" name="assignee_name"  multiple="multiple" data-placeholder="{{ __('Select Users ...') }}" required>
                                    <option value="">{{__('Select Assignee')}}</option>
                                    @foreach($users as $u)
                                        <option value="{{$u->email}}">{{ formatUserName($u->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-2">
                                <label class="form-label">{{ __('Status')}}</label>
                                <select class="form-control form-control-light" name="status_name" id="status_name" required>
                                    <option value="">{{__('All')}}</option>
                                    @foreach($stages as $stage)
                                        <option value="{{$stage->name}}" data-color="{{ $stage->color }}">  {{$stage->name}}
                                            </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-2">
                                 <label class="form-label">{{ __('Priority')}}</label>
                                <select class="form-control form-control-light" name="priority" id="priority" required>
                                     <option value="">{{__('All')}}</option>
                                    <option value="normal">{{ __('normal')}}</option>
                                    <option value="urgent">{{ __('urgent')}}</option>
                                    <option value="Take your time">{{ __('Take your time')}}</option>
                                </select>
                            </div>
                            {{-- <div>
                                <button type="submit" class="btn btn-warning mt-4"><i class="ti ti-search"></i></button>
                            </div> --}}
                            
                        </form>
                    </div>
                    <div class="col-md-3 mb-3"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        {{ $dataTable->table(['width' => '100%']) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Links Modal -->
<div class="modal fade" id="linksModal" tabindex="-1" aria-labelledby="linksModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="linksModalLabel">Task Links</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Links will be inserted here by JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <!--<button type="button" class="btn btn-primary" id="openAllLinks">Open All Links</button>-->
            </div>
        </div>
    </div>
</div>

<!-- Task Choice Modal -->
<div class="modal fade" id="taskChoiceModal" tabindex="-1" aria-labelledby="taskChoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);">
            <div class="modal-header" style="background: linear-gradient(141.55deg, #ff6f28 3.46%, #ff6f28 99.86%), #ff6f28; color: white; border-bottom: none; border-radius: 16px 16px 0 0; padding: 1.5rem;">
                <h5 class="modal-title" id="taskChoiceModalLabel" style="color: white;">
                    <i class="bi bi-list-check me-2"></i>
                    {{ __('Choose Task Creation Type') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 2rem;">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="task-choice-card bg-gradient-blue" id="choice-single-task">
                            <div class="icon-wrap">
                                <i class="bi bi-plus-lg fs-4"></i>
                            </div>
                            <h5>{{ __('Single Task') }}</h5>
                            <p>{{ __('Create one task with full details and options.') }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="task-choice-card bg-gradient-amber" id="choice-multiple-task">
                            <div class="icon-wrap">
                                <i class="bi bi-columns-gap fs-4"></i>
                            </div>
                            <h5>{{ __('Multiple Tasks') }}</h5>
                            <p>{{ __('Create multiple tasks at once.') }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('project.staging') }}" target="_blank" class="text-decoration-none">
                            <div class="task-choice-card bg-gradient-teal" id="choice-staging">
                                <div class="icon-wrap">
                                    <i class="bi bi-diagram-3 fs-4"></i>
                                </div>
                                <h5>{{ __('Staging') }}</h5>
                                <p>{{ __('Plan and organize tasks with advanced workflows.') }}</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Coming Soon Modal -->
<!-- <div class="modal fade" id="comingSoonModal" tabindex="-1" aria-labelledby="comingSoonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);">
            <div class="modal-header" style="background-color: #ff6f28; color: white; border-bottom: none;">
                <h5 class="modal-title" id="comingSoonModalLabel">
                    {{ __('Feature Coming Soon') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center" style="padding: 2.5rem;">
                <img src="{{ asset('images/construction.png') }}" alt="Construction" style="width: 80px; height: 80px; margin-bottom: 15px;" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4MCIgaGVpZ2h0PSI4MCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9IiNmZjZmMjgiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIiBjbGFzcz0ibHVjaWRlIGx1Y2lkZS1jb25zdHJ1Y3Rpb24iPjxyZWN0IHdpZHRoPSIyMCIgaGVpZ2h0PSI2IiB4PSIyIiB5PSI2IiByeD0iMSIvPjxwYXRoIGQ9Ik0xMiAxMnY5Ii8+PHBhdGggZD0iTTE5IDE4di0yYTItMiAwIDAgMC0yLTJoLTJhMiAyIDAgMCAwLTIgMnYyIi8+PHBhdGggZD0iTTUgOFYzYTEgMSAwIDAgMSAxLTFoNCIvPjxwYXRoIGQ9Ik05IDJoNmwtNCAxMC0yLTIiLz48L3N2Zz4=';">
                <h4 class="mb-2">{{ __("We're working on it!") }}</h4>
                <p class="text-muted mb-0">{{ __('Our developers are building this feature right now.') }}</p>
                <p class="text-muted">{{ __("We'll be live soon. Stay tuned!") }}</p>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #f3f4f6; padding: 0.75rem 1rem;">
                <button type="button" class="btn btn-primary" id="gotItBtn" style="background-color: #ff6f28; border-color: #ff6f28;">{{ __('Got It') }}</button>
            </div>
        </div>
    </div>
</div> -->

<!-- Hidden Task Creation Links -->
<a id="hidden-create-task-link" class="d-none" data-ajax-popup="true" data-size="lg" data-title="{{ __('Create New Task') }}" 
   data-url="{{ route('tasks.create') }}" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Create') }}"></a>
            <a id="hidden-create-multiple-tasks-link" class="d-none" data-ajax-popup="true" data-size="lg" data-title="{{ __('Create Multiple Tasks') }}"
   data-url="{{ route('tasks.create.multiple') }}" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Create Multiple') }}"></a>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle Single Task click
        document.getElementById('choice-single-task').addEventListener('click', function() {
            $('#taskChoiceModal').modal('hide');
            setTimeout(function() {
                document.getElementById('hidden-create-task-link').click();
            }, 150);
        });
        
        // Handle Multiple Tasks click
        document.getElementById('choice-multiple-task').addEventListener('click', function() {
            $('#taskChoiceModal').modal('hide');
            setTimeout(function() {
                $('#comingSoonModal').modal('show');
                // After showing the "Coming Soon" modal, wait 2 seconds and then open the multiple tasks form
                setTimeout(function() {
                    $('#comingSoonModal').modal('hide');
                    document.getElementById('hidden-create-multiple-tasks-link').click();
                }, 2000);
            }, 150);
        });
        
        // Staging link now handled directly in HTML with target="_blank"
    });
</script>

    <!-- ETC Modal -->

<div class="modal fade" id="etcModal" tabindex="-1" aria-labelledby="etcModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form id="etcForm">
      <div class="modal-content" style="
        border: none;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
      ">
        <!-- Gradient Header -->
        <div class="modal-header" style="
          background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
          color: white;
          padding: 1.5rem;
          border-bottom: none;
        ">
          <h5 class="modal-title" style="
            font-weight: 600;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            gap: 10px;
          ">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;">
              <circle cx="12" cy="12" r="10"></circle>
              <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
            <span>Task Completion Details</span>
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="
            filter: brightness(0) invert(1);
          "></button>
        </div>

        <!-- Glassmorphism Body -->
        <div class="modal-body" style="
          padding: 2rem;
          background: rgba(255, 255, 255, 0.9);
          backdrop-filter: blur(10px);
        ">
          <div class="mb-4">
            <label for="etcInput" class="form-label" style="
              font-weight: 500;
              color: #4a5568;
              margin-bottom: 0.5rem;
              display: block;
            ">Actual Time Taken (minutes)</label>
            <input type="number" class="form-control" id="etcInput" name="etc" min="1" required placeholder="Enter time in minutes" style="
              padding: 0.75rem 1rem;
              border-radius: 8px;
              border: 1px solid #e2e8f0;
              transition: all 0.3s;
            ">
            <div class="form-text" style="
              font-size: 0.8rem;
              color: #718096;
              margin-top: 0.25rem;
            ">Enter the actual time spent on this task</div>
          </div>

          <div class="mb-3">
            <label for="completionDate" class="form-label" style="
              font-weight: 500;
              color: #4a5568;
              margin-bottom: 0.5rem;
              display: block;
            ">Completion Date</label>
            <input type="text" class="form-control" id="completionDate" name="completion_date" readonly value="{{ now()->format('y-m-d') }}" style="
              padding: 0.75rem 1rem;
              border-radius: 8px;
              border: 1px solid #e2e8f0;
              background-color: #f8fafc;
            ">
          </div>

          <input type="hidden" id="etcTaskId" name="task_id">
          <input type="hidden" id="etcTaskOriginal" name="etcTaskOriginal">
        </div>

        <!-- Floating Action Buttons -->
        <div class="modal-footer" style="
          padding: 1.5rem;
          border-top: none;
          background: rgba(255, 255, 255, 0.95);
          display: flex;
          justify-content: flex-end;
          gap: 12px;
        ">
          <button type="button" class="btn" data-bs-dismiss="modal" style="
            padding: 0.65rem 1.25rem;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: white;
            color: #4a5568;
            font-weight: 500;
            transition: all 0.2s;
          ">Cancel</button>
          <button type="submit" class="btn" style="
            padding: 0.65rem 1.25rem;
            border-radius: 8px;
            border: none;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            font-weight: 500;
            box-shadow: 0 4px 6px rgba(101, 117, 255, 0.2);
            transition: all 0.2s;
          ">Save Completion</button>
        </div>
      </div>
    </form>
  </div>
</div>


<!-- reqork reasion -->
<div class="modal fade" id="reworkModal" tabindex="-1" aria-labelledby="reworkModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form id="reworkForm" method="post" action="{{ route('tasks.save.rework') }}">
        @csrf
      <div class="modal-content" style="
        border: none;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
      ">
        <!-- Gradient Header -->
        <div class="modal-header" style="
          background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
          color: white;
          padding: 1.5rem;
          border-bottom: none;
        ">
          <h5 class="modal-title" style="
            font-weight: 600;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            gap: 10px;
          ">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;">
              <circle cx="12" cy="12" r="10"></circle>
              <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
            <span style="color:white">What improvements need to be done next?</span>
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="
            filter: brightness(0) invert(1);
          "></button>
        </div>

        <!-- Glassmorphism Body -->
        <div class="modal-body" style="
          padding: 2rem;
          background: rgba(255, 255, 255, 0.9);
          backdrop-filter: blur(10px);
        ">
          <div class="mb-4">
            <input type="text" class="form-control" name="rework_reason" required placeholder="Enter improvements" style="
              padding: 0.75rem 1rem;
              border-radius: 8px;
              border: 1px solid #e2e8f0;
              transition: all 0.3s;
            ">           
          </div>      

          <input type="hidden" id="reworkTaskId" name="task_id">
        </div>

        <!-- Floating Action Buttons -->
        <div class="modal-footer" style="
          padding: 1.5rem;
          border-top: none;
          background: rgba(255, 255, 255, 0.95);
          display: flex;
          justify-content: flex-end;
          gap: 12px;
        ">
          <button type="button" class="btn" data-bs-dismiss="modal" style="
            padding: 0.65rem 1.25rem;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: white;
            color: #4a5568;
            font-weight: 500;
            transition: all 0.2s;
          ">Cancel</button>
          <button type="submit" class="btn" style="
            padding: 0.65rem 1.25rem;
            border-radius: 8px;
            border: none;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            font-weight: 500;
            box-shadow: 0 4px 6px rgba(101, 117, 255, 0.2);
            transition: all 0.2s;
          ">Save Rework</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Graph Modal -->
<div class="modal fade" id="graphModal" tabindex="-1" aria-labelledby="graphModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen-lg-down modal-xl modal-dialog-centered">
    <div class="modal-content" style="border-radius: 20px; overflow: hidden; border: none; box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);">
      <!-- Enhanced Gradient Header -->
      <div class="modal-header" style="
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        color: white;
        padding: 2rem 2.5rem;
        border-bottom: none;
        position: relative;
        overflow: hidden;
      ">
        <div style="
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background: url('data:image/svg+xml,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; viewBox=&quot;0 0 100 100&quot;><defs><pattern id=&quot;grain&quot; width=&quot;100&quot; height=&quot;100&quot; patternUnits=&quot;userSpaceOnUse&quot;><circle cx=&quot;50&quot; cy=&quot;50&quot; r=&quot;1&quot; fill=&quot;%23ffffff&quot; opacity=&quot;0.1&quot;/></pattern></defs><rect width=&quot;100&quot; height=&quot;100&quot; fill=&quot;url(%23grain)&quot;/></svg>') repeat;
          opacity: 0.3;
        "></div>
        <h5 class="modal-title" style="
          font-weight: 700;
          font-size: 1.5rem;
          display: flex;
          align-items: center;
          gap: 15px;
          position: relative;
          z-index: 1;
        ">
          <i class="ti ti-chart-bar" style="font-size: 2rem; text-shadow: 0 2px 4px rgba(0,0,0,0.3);"></i>
          <div>
            <span>Task Analytics Dashboard</span>
            <div style="font-size: 0.9rem; font-weight: 400; opacity: 0.9; margin-top: 2px;">
              Detailed Performance Metrics
            </div>
          </div>
          <span id="filtered-user-name" class="badge" style="
            background: rgba(255, 255, 255, 0.2);
            color: white;
            font-size: 0.85rem;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
          "></span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="
          position: relative;
          z-index: 1;
          filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
        "></button>
      </div>

      <!-- Enhanced Chart Body -->
      <div class="modal-body" style="
        padding: 2.5rem;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 50%, #f1f5f9 100%);
        min-height: 600px;
      ">
        <!-- Chart Type Toggle -->
        <div class="d-flex justify-content-center mb-4">
          <div class="btn-group" role="group" style="
            background: white;
            border-radius: 15px;
            padding: 5px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
          ">
            <button type="button" class="btn chart-type-btn active" data-chart-type="bar" style="
              border-radius: 10px;
              padding: 0.5rem 1.5rem;
              border: none;
              background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
              color: white;
              font-weight: 500;
              transition: all 0.3s;
            ">
              <i class="ti ti-chart-bar me-2"></i>Bar Chart
            </button>
            <button type="button" class="btn chart-type-btn" data-chart-type="line" style="
              border-radius: 10px;
              padding: 0.5rem 1.5rem;
              border: none;
              background: transparent;
              color: #667eea;
              font-weight: 500;
              transition: all 0.3s;
            ">
              <i class="ti ti-chart-line me-2"></i>Line Chart
            </button>
          </div>
        </div>

        <!-- Chart Container -->
        <div class="row">
          <div class="col-12">
            <div style="
              background: white;
              border-radius: 20px;
              padding: 2rem;
              box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
              border: 1px solid rgba(255, 255, 255, 0.2);
              backdrop-filter: blur(10px);
            ">
              <div style="width: 100%; position: relative; height: 450px;">
                <canvas id="taskChart" style="width: 100% !important; height: 100% !important;"></canvas>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Enhanced Statistics Cards -->
        <div class="mt-4 row" id="chart-legend">
          <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div style="
              background: linear-gradient(135deg, #36A2EB 0%, #2196F3 100%);
              color: white;
              padding: 1.5rem;
              border-radius: 15px;
              text-align: center;
              box-shadow: 0 8px 25px rgba(54, 162, 235, 0.3);
              transition: transform 0.3s;
            " onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
              <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                <i class="ti ti-list-check" style="font-size: 2rem; margin-right: 10px;"></i>
                <span style="font-weight: 600; font-size: 1.1rem;">Total Tasks</span>
              </div>
              <div style="font-size: 2.5rem; font-weight: 700; margin-bottom: 5px;" id="legend-total">0</div>
              <div style="font-size: 0.9rem; opacity: 0.9;">All Tasks</div>
            </div>
          </div>
          <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div style="
              background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%);
              color: white;
              padding: 1.5rem;
              border-radius: 15px;
              text-align: center;
              box-shadow: 0 8px 25px rgba(23, 162, 184, 0.3);
              transition: transform 0.3s;
            " onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
              <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                <i class="ti ti-clock" style="font-size: 2rem; margin-right: 10px;"></i>
                <span style="font-weight: 600; font-size: 1.1rem;">Pending</span>
              </div>
              <div style="font-size: 2.5rem; font-weight: 700; margin-bottom: 5px;" id="legend-pending">0</div>
              <div style="font-size: 0.9rem; opacity: 0.9;">In Progress</div>
            </div>
          </div>
          <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div style="
              background: linear-gradient(135deg, #dc3545 0%, #e91e63 100%);
              color: white;
              padding: 1.5rem;
              border-radius: 15px;
              text-align: center;
              box-shadow: 0 8px 25px rgba(220, 53, 69, 0.3);
              transition: transform 0.3s;
            " onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
              <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                <i class="ti ti-alert-circle" style="font-size: 2rem; margin-right: 10px;"></i>
                <span style="font-weight: 600; font-size: 1.1rem;">Overdue</span>
              </div>
              <div style="font-size: 2.5rem; font-weight: 700; margin-bottom: 5px;" id="legend-overdue">0</div>
              <div style="font-size: 0.9rem; opacity: 0.9;">Past Deadline</div>
            </div>
          </div>
          <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div style="
              background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
              color: white;
              padding: 1.5rem;
              border-radius: 15px;
              text-align: center;
              box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
              transition: transform 0.3s;
            " onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
              <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                <i class="ti ti-check-circle" style="font-size: 2rem; margin-right: 10px;"></i>
                <span style="font-weight: 600; font-size: 1.1rem;">Completed</span>
              </div>
              <div style="font-size: 2.5rem; font-weight: 700; margin-bottom: 5px;" id="legend-done">0</div>
              <div style="font-size: 0.9rem; opacity: 0.9;">Finished</div>
            </div>
          </div>
        </div>

        <!-- Additional Analytics -->
        <div class="row mt-4">
          <div class="col-md-6">
            <div style="
              background: white;
              padding: 1.5rem;
              border-radius: 15px;
              box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            ">
              <h6 style="color: #4a5568; font-weight: 600; margin-bottom: 1rem;">
                <i class="ti ti-trending-up me-2"></i>Completion Rate
              </h6>
              <div style="font-size: 2rem; font-weight: 700; color: #28a745;" id="completion-rate">0%</div>
              <div style="font-size: 0.9rem; color: #718096;">Overall Progress</div>
            </div>
          </div>
          <div class="col-md-6">
            <div style="
              background: white;
              padding: 1.5rem;
              border-radius: 15px;
              box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            ">
              <h6 style="color: #4a5568; font-weight: 600; margin-bottom: 1rem;">
                <i class="ti ti-clock-hour-4 me-2"></i>Average Time
              </h6>
              <div style="font-size: 2rem; font-weight: 700; color: #667eea;" id="average-time">0h</div>
              <div style="font-size: 0.9rem; color: #718096;">Per Task</div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Enhanced Footer -->
      <div class="modal-footer" style="
        background: rgba(255, 255, 255, 0.95);
        border-top: 1px solid rgba(0, 0, 0, 0.1);
        padding: 1.5rem 2.5rem;
        backdrop-filter: blur(10px);
      ">
        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
          <div style="font-size: 0.9rem; color: #718096;">
            <i class="ti ti-info-circle me-1"></i>
            Last updated: <span id="last-updated">{{ now()->format('M d, Y H:i') }}</span>
          </div>
          <div>
            <button type="button" class="btn btn-outline-secondary me-2" onclick="refreshChartData()" style="
              border-radius: 10px;
              padding: 0.5rem 1rem;
              border: 1px solid #e2e8f0;
              transition: all 0.3s;
            ">
              <i class="ti ti-refresh me-1"></i>Refresh
            </button>
            <button type="button" class="btn btn-primary" data-bs-dismiss="modal" style="
              background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
              border: none;
              border-radius: 10px;
              padding: 0.5rem 1.5rem;
              font-weight: 500;
            ">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content d_modal-content">
      <div class="d_warning-icon">
        <i class="bi bi-exclamation-circle-fill"></i>
      </div>
      <h5 class="fw-bold mb-2">Are you sure you want to delete it?</h5>

      <!-- Star Rating Section -->
      <div class="d_star-rating mb-4">
        <i class="bi bi-star-fill" data-value="1"></i>
        <i class="bi bi-star-fill" data-value="2"></i>
        <i class="bi bi-star-fill" data-value="3"></i>
        <i class="bi bi-star-fill" data-value="4"></i>
        <i class="bi bi-star-fill" data-value="5"></i>
      </div>

      <div class="d-flex justify-content-center gap-2">
        <button id="confirmDelete" class="btn d_btn-primary">Yes, delete it!</button>
        <button class="btn d_btn-danger" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<!-- Rating Details Modal -->
<div class="modal fade" id="ratingModal" tabindex="-1" aria-labelledby="ratingModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);">
      <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-bottom: none; border-radius: 15px 15px 0 0;">
        <h5 class="modal-title" id="ratingModalLabel">
          <i class="ti ti-star-filled me-2"></i>
          Rating Overview
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="padding: 2rem;">
        <div class="row">
          <div class="col-md-4 text-center mb-4">
            <div style="padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px; color: white; margin-bottom: 20px;">
              <h2 class="mb-2" id="modal-average-rating" style="font-size: 3rem; font-weight: bold;">4.3</h2>
              <div class="mb-2">
                <i class="ti ti-star-filled text-warning" style="font-size: 1.5rem;"></i>
                <i class="ti ti-star-filled text-warning" style="font-size: 1.5rem;"></i>
                <i class="ti ti-star-filled text-warning" style="font-size: 1.5rem;"></i>
                <i class="ti ti-star-filled text-warning" style="font-size: 1.5rem;"></i>
                <i class="ti ti-star-half-filled text-warning" style="font-size: 1.5rem;"></i>
              </div>
              <p class="mb-0" id="modal-total-ratings">Based on 2,115 ratings</p>
            </div>
          </div>
          <div class="col-md-8">
            <h6 class="mb-3" style="color: #4a5568; font-weight: 600;">Rating Breakdown</h6>
            <div class="rating-breakdown">
              <div class="d-flex align-items-center mb-3">
                <span class="me-3" style="width: 50px; font-weight: 500;">5 ★</span>
                <div class="progress flex-grow-1 me-3" style="height: 10px;">
                  <div class="progress-bar bg-warning" role="progressbar" style="width: 70%"></div>
                </div>
                <span style="width: 60px; text-align: right; font-weight: 500;" id="modal-rating-5-count">1,985</span>
              </div>
              <div class="d-flex align-items-center mb-3">
                <span class="me-3" style="width: 50px; font-weight: 500;">4 ★</span>
                <div class="progress flex-grow-1 me-3" style="height: 10px;">
                  <div class="progress-bar bg-warning" role="progressbar" style="width: 15%"></div>
                </div>
                <span style="width: 60px; text-align: right; font-weight: 500;" id="modal-rating-4-count">356</span>
              </div>
              <div class="d-flex align-items-center mb-3">
                <span class="me-3" style="width: 50px; font-weight: 500;">3 ★</span>
                <div class="progress flex-grow-1 me-3" style="height: 10px;">
                  <div class="progress-bar bg-warning" role="progressbar" style="width: 6%"></div>
                </div>
                <span style="width: 60px; text-align: right; font-weight: 500;" id="modal-rating-3-count">130</span>
              </div>
              <div class="d-flex align-items-center mb-3">
                <span class="me-3" style="width: 50px; font-weight: 500;">2 ★</span>
                <div class="progress flex-grow-1 me-3" style="height: 10px;">
                  <div class="progress-bar bg-warning" role="progressbar" style="width: 4%"></div>
                </div>
                <span style="width: 60px; text-align: right; font-weight: 500;" id="modal-rating-2-count">90</span>
              </div>
              <div class="d-flex align-items-center mb-3">
                <span class="me-3" style="width: 50px; font-weight: 500;">1 ★</span>
                <div class="progress flex-grow-1 me-3" style="height: 10px;">
                  <div class="progress-bar bg-warning" role="progressbar" style="width: 1.5%"></div>
                </div>
                <span style="width: 60px; text-align: right; font-weight: 500;" id="modal-rating-1-count">33</span>
              </div>
            </div>
          </div>
        </div>
        <hr style="margin: 2rem 0;">
        <div class="row">
          <div class="col-md-12">
            <h6 style="color: #4a5568; font-weight: 600; margin-bottom: 1rem;">Recent Delete Feedback</h6>
            <div id="recent-reviews">
              <!-- Recent reviews will be loaded here -->
              <div class="text-center text-muted">
                <i class="ti ti-loader-2 ti-spin" style="font-size: 2rem;"></i>
                <p class="mt-2">Loading reviews...</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer" style="border-top: 1px solid #e2e8f0; padding: 1rem 2rem;">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
    
    <!-- Today's Completed Tasks Graph Modal -->
<div class="modal fade" id="todayGraphModal" tabindex="-1" aria-labelledby="todayGraphModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);">
      <div class="modal-header" style="border-bottom: 1px solid #e2e8f0; padding: 1.5rem;">
        <h5 class="modal-title" id="todayGraphModalLabel">
          <i class="ti ti-chart-line me-2 text-success"></i>
          Today's Completed Tasks - <span id="today-date"></span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="padding: 2rem;">
        <div class="row mb-3">
          <div class="col-md-6">
            <div class="card bg-success text-white">
              <div class="card-body text-center">
                <i class="ti ti-check-circle" style="font-size: 2rem;"></i>
                <h4 class="mt-2 mb-0" id="today-completed-count">0</h4>
                <small>Tasks Completed Today</small>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card bg-info text-white">
              <div class="card-body text-center">
                <i class="ti ti-clock" style="font-size: 2rem;"></i>
                <h4 class="mt-2 mb-0" id="total-completed-count">0</h4>
                <small>Total Completed Tasks</small>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-body">
                <canvas id="todayTaskChart" width="400" height="200"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer" style="border-top: 1px solid #e2e8f0; padding: 1rem 1.5rem;">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="refreshTodayData()">
          <i class="ti ti-refresh me-1"></i>Refresh
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Change Assignor Modal -->
<div class="modal fade" id="change-assignor-modal" tabindex="-1" aria-labelledby="changeAssignorModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);">
      <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-bottom: none; border-radius: 15px 15px 0 0;">
        <h5 class="modal-title" id="changeAssignorModalLabel">
          <i class="fas fa-user-edit me-2"></i>
          Change Assignor for Selected Tasks
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="padding: 2rem;">
        <form id="change-assignor-form">
          @csrf
          <input type="hidden" id="selected-task-ids" name="task_ids">
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
        </form>
      </div>
      <div class="modal-footer" style="border-top: 1px solid #e2e8f0; padding: 1rem 2rem;">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="update-assignor-btn">
          <i class="fas fa-save me-1"></i>
          Update Assignor
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Change Assignee Modal -->
<div class="modal fade" id="change-assignee-modal" tabindex="-1" aria-labelledby="changeAssigneeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);">
      <div class="modal-header" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border-bottom: none; border-radius: 15px 15px 0 0;">
        <h5 class="modal-title" id="changeAssigneeModalLabel">
          <i class="fas fa-user-plus me-2"></i>
          Change Assignee for Selected Tasks
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
          <div class="modal-footer" style="border-top: 1px solid #e2e8f0; padding: 1rem 2rem;">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-success" id="update-assignee-btn">
              <i class="fas fa-save me-1"></i>
              Update Assignee
            </button>
          </div>
        </form>
    </div>
  </div>
</div>
    
<!-- Change ETC Modal -->
<div class="modal fade" id="change-etc-modal" tabindex="-1" aria-labelledby="changeETCModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);">
      <div class="modal-header" style="background: linear-gradient(135deg, #ff6b00 0%, #ff922b 100%); color: white; border-bottom: none; border-radius: 15px 15px 0 0;">
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
            <label for="etc-input" class="form-label">Enter New ETC Value (hours)</label>
            <input type="number" class="form-control" id="etc-input" name="etc_value" min="0" step="0.5" required placeholder="Enter ETC hours">
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
    
    <!-- ETC Modal -->
    
<!-- Change Date Modal -->
<div class="modal fade" id="change-date-modal" tabindex="-1" aria-labelledby="changeDateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);">
      <div class="modal-header" style="background: linear-gradient(135deg, #6610f2 0%, #6f42c1 100%); color: white; border-bottom: none; border-radius: 15px 15px 0 0;">
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
            <div class="col-md-6" id="start_date">
              <label for="start-date-input" class="form-label">Start Date</label>
              <input type="date" class="form-control" id="start-date-input" name="start_date">
            </div>
            <div class="col-md-6">
              <label for="end-date-input" class="form-label">End Date</label>
              <input type="date" class="form-control" id="end-date-input" name="end_date">
            </div>
          </div>
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="update-due-date-only" onclick="disabled_start_date()" name="update_due_date_only">
            <label class="form-check-label" for="update-due-date-only">
              Update due date only (ignore start date)
            </label>
          </div>
          <div class="alert alert-info">
            <small><i class="fas fa-info-circle me-1"></i> Leave a field empty if you don't want to update it.</small>
          </div>
        </form>
      </div>
      <div class="modal-footer" style="border-top: 1px solid #e2e8f0; padding: 1rem 2rem;">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="update-date-btn">
          <i class="fas fa-save me-1"></i>
          Update Dates
        </button>
      </div>
    </div>
  </div>
</div>
<!-- Change Priority Modal -->
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
              <option value="high" style="color: #fd7e14; font-weight: bold;">🟠 High</option>
              <option value="normal" style="color: #198754; font-weight: bold;">🟢 Normal</option>
              <option value="low" style="color: #6c757d; font-weight: bold;">🔵 Low</option>
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
            var stages = @json($stages->pluck('name'));
            var stagesArr = @json($stages);
            var priorityArr = @json($priority);
            console.log(priorityArr);
            var isAddEnable = "{{ request()->query('is_add_enable') ?? false }}";
            console.log(isAddEnable);
            $(document).ready(function () {
                if (isAddEnable === "true" || isAddEnable === true) {
                    $('.add-task').trigger('click');
                }
                getTaskCount();
                loadRatingData();
                loadTeamloggerData();
                // Initialize task toggle
                initializeTaskToggle();
                // Bind filter change events
                $('#assignor_name, #assignee_name, #status_name, #group_name, #task_name,#priority').on('change keyup', function() {
                    getTaskCount();
                    getDoneTaskData();
                });
                
                // Bind assignee filter change to update TeamLogger data
                $('#assignee_name').on('change', function() {
                    loadTeamloggerData();
                });
                
                // Intercept the add-task button click to show our custom task choice modal
                $('.add-task').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $('#taskChoiceModal').modal('show');
                    return false;
                });
                
                // Single task card click handler
                $('#choice-single-task').on('click', function() {
                    $('#taskChoiceModal').modal('hide');
                    // Trigger the hidden create task link with a small delay to allow modal to close
                    setTimeout(function() {
                        $('#hidden-create-task-link')[0].click();
                    }, 300);
                });
                
                // Multiple task card click handler
                $('#choice-multiple-task').on('click', function() {
                    $('#taskChoiceModal').modal('hide');
                    // Show the coming soon modal with a small delay
                    setTimeout(function() {
                        $('#comingSoonModal').modal('show');
                    }, 300);
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
                        </div>
                    `;
                    
                    // Insert toggle into the task-toggle-wrapper
                    $('.task-toggle-wrapper').html(toggleHtml);
                    
                    // Bind toggle functionality
                    $('#taskToggle .toggle-option').on('click', function() {
                        var selectedValue = $(this).data('value');
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
                        d.priority = $('#priority').val();
                        d.group_name = $('#group_name').val();
                        d.task_name = $('#task_name').val();
                        d.toggle_filter = filterType; // Add toggle filter parameter
                        return d;
                    };
                    
                    // Reload the table with new filter
                    table.ajax.reload();
                }
            }
            

            // Function to load rating data from delete_rating column
            function loadRatingData() {
                $.ajax({
                    url: "{{ route('tasks.rating.data') }}",
                    type: 'GET',
                    dataType: 'JSON',
                    success: function(data) {
                        // Update card display
                        $('#average-rating').text(data.average_rating);
                        
                        // Update modal display
                        $('#modal-average-rating').text(data.average_rating);
                        $('#modal-total-ratings').text('Based on ' + data.total_ratings + ' ratings');
                        
                        // Update star display in modal
                        updateStarDisplay('#ratingModal .mb-2', data.average_rating);
                        
                        // Update rating breakdown
                        updateRatingBreakdown(data.rating_breakdown, data.total_ratings);
                        
                        // Update recent reviews (if you want to show them)
                        updateRecentReviews(data.recent_reviews);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading rating data:', error);
                        // Show default values if error occurs
                        $('#average-rating').text('0.0');
                        $('#modal-average-rating').text('0.0');
                        $('#modal-total-ratings').text('No ratings yet');
                    }
                });
            }

            // Function to update star display based on rating
            function updateStarDisplay(selector, rating) {
                var stars = $(selector + ' i');
                var fullStars = Math.floor(rating);
                var hasHalfStar = rating - fullStars >= 0.5;
                
                stars.each(function(index) {
                    $(this).removeClass('ti-star-filled ti-star-half-filled ti-star');
                    
                    if (index < fullStars) {
                        $(this).addClass('ti-star-filled text-warning');
                    } else if (index === fullStars && hasHalfStar) {
                        $(this).addClass('ti-star-half-filled text-warning');
                    } else {
                        $(this).addClass('ti-star text-muted');
                    }
                });
            }

            // Function to update rating breakdown with real data
            function updateRatingBreakdown(breakdown, totalRatings) {
                for (let rating = 1; rating <= 5; rating++) {
                    var count = breakdown[rating] || 0;
                    var percentage = totalRatings > 0 ? (count / totalRatings * 100) : 0;
                    
                    // Update count
                    $('#modal-rating-' + rating + '-count').text(count);
                    
                    // Update progress bar
                    var progressBar = $('.rating-breakdown .d-flex:nth-child(' + (6 - rating) + ') .progress-bar');
                    progressBar.css('width', percentage + '%');
                    
                    // Update aria-valuenow for accessibility
                    progressBar.attr('aria-valuenow', percentage);
                }
            }

            // Function to update recent reviews
            function updateRecentReviews(reviews) {
                var reviewsContainer = $('#recent-reviews');
                
                if (reviews && reviews.length > 0) {
                    var reviewsHtml = '';
                    reviews.forEach(function(review) {
                        reviewsHtml += `
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <strong>${review.task_title || 'Task'}</strong>
                                        <div class="text-warning">
                                            ${'★'.repeat(review.rating)}${'☆'.repeat(5 - review.rating)}
                                        </div>
                                    </div>
                                    <small class="text-muted">${review.created_at || 'Recent'}</small>
                                </div>
                                ${review.feedback ? `<p class="mb-0 text-muted small">${review.feedback}</p>` : ''}
                            </div>
                        `;
                    });
                    reviewsContainer.html(reviewsHtml);
                } else {
                    reviewsContainer.html(`
                        <div class="text-center text-muted py-4">
                            <i class="ti ti-star" style="font-size: 3rem; opacity: 0.3;"></i>
                            <p class="mt-2 mb-0">No reviews available yet</p>
                        </div>
                    `);
                }
            }

            // Function to fetch Teamlogger data
            async function loadTeamloggerData() {
                try {
                    console.log('Fetching Teamlogger data for L30...');
                    
                    // Get selected assignee from filter, if none selected use logged-in user
                    const currentUserEmail = '{{ Auth::user()->email }}';
                    const selectedAssignees = $('#assignee_name').val();
                    
                    // Use selected assignee if available, otherwise use logged-in user
                    let assigneeEmails = (selectedAssignees && selectedAssignees.length > 0) 
                        ? selectedAssignees 
                        : [currentUserEmail];
                    
                    // Show loading state
                    $('#teamlogger-hours').text('...');
                    
                    // Always use last 30 days for TeamLogger card
                    let requestData = {
                        assignee_emails: assigneeEmails,
                        assignor_emails: [],
                        date_filter: 'last_30_days'
                    };
                    
                    console.log('Sending TeamLogger request for L30:', requestData);
                    
                    $.ajax({
                        url: '{{ route("projecttask.teamlogger.data") }}',
                        method: 'GET',
                        dataType: 'json',
                        data: requestData,
                        success: function(response) {
                            console.log('Teamlogger L30 response:', response);
                            if (response.success) {
                                // Display activeHours (totalHours - idleHours) rounded
                                const displayValue = Math.round(response.activeHours || 0);
                                $('#teamlogger-hours').text(displayValue);
                                console.log('Updated teamlogger L30 active hours to:', displayValue);
                            } else {
                                console.error('Backend error:', response.message);
                                $('#teamlogger-hours').text('0');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching Teamlogger L30 data:', error);
                            $('#teamlogger-hours').text('0');
                        }
                    });
                    
                } catch (error) {
                    console.error('Error in loadTeamloggerData:', error);
                    $('#teamlogger-hours').text('0');
                }
            }

            // Function to update filtered data including Teamlogger
            function updateFilteredData() {
                const assignorEmails = $('#assignor_name').val() || [];
                const assigneeEmails = $('#assignee_name').val() || [];
                
                console.log('updateFilteredData called with filters:', {
                    assignorEmails: assignorEmails,
                    assigneeEmails: assigneeEmails
                });
                
                // Always load Teamlogger data based on current filters
                loadTeamloggerData();
                
                // Update other filtered data
                updateTaskCounts();
            }

            // Function to update task counts (ETC and ATC)
            function updateTaskCounts() {
                const filters = {
                    assignor_name: $('#assignor_name').val(),
                    assignee_name: $('#assignee_name').val(),
                    status_name: $('#status_name').val(),
                    priority: $('#priority').val(),
                    group_name: $('#group_name').val(),
                    task_name: $('#task_name').val(),
                    month: $('#current-month').text()
                };

                // Make AJAX call to get filtered task data
                $.ajax({
                    url: '{{ route("projecttask.done.count") }}',
                    method: 'GET',
                    data: filters,
                    success: function(response) {
                        if (response.is_success && response.data) {
                            $('#filtered-etc-count').text(response.data.total_eta || 0);
                            $('#filtered-atc-count').text(response.data.total_atc || 0);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error fetching task counts:', xhr);
                    }
                });
            }

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
           
            $(document).on("click", ".delete-group-btn", function() {
                let selectedIds = $(".task-checkbox:checked").map(function() {
                    return this.value;
                }).get();
                
                if (selectedIds.length > 0) {
                    Swal.fire({
                        title: "Are you sure you want to delete selected tasks?",
                        text: "You won't be able to revert this action!!",
                        icon: "warning",
                        width: "400px",
                        allowOutsideClick: false,
                        showCancelButton: true,
                        confirmButtonColor: "#1D9300",
                        cancelButtonColor: "#F90F0F",
                        confirmButtonText: "Yes, delete them!",
                        html: `
                            <label for="rating" style="display:block; margin-top:10px;">Please rate before deleting:</label>
                            <select id="delete_rating" class="swal2-input" style="width: auto;">
                                <option value="">Select rating</option>
                                <option value="1">⭐</option>
                                <option value="2">⭐⭐</option>
                                <option value="3">⭐⭐⭐</option>
                                <option value="4">⭐⭐⭐⭐</option>
                                <option value="5">⭐⭐⭐⭐⭐</option>
                            </select>
                            <label for="delete_feedback" style="display:block; margin-top:10px;">Improvement Feedback (optional) <br>If you don’t want to leave a rating, you can simply click Delete.</label>
                            <textarea id="delete_feedback" class="swal2-textarea" style="width: 60%; min-height: 60px;" placeholder="Enter your feedback here..."></textarea>
                        `,
                            preConfirm: () => {
                                const rating = document.getElementById('delete_rating').value;
                                const feedback = document.getElementById('delete_feedback').value;
                            
                                return {
                                    rating: rating || null, // Optional
                                    feedback: feedback
                                };
                            }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const rating = result.value.rating;
                            const feedback = result.value.feedback;
                            bulkAction(selectedIds, 'delete', rating, feedback);
                        }
                    });
                }
            });
            $(document).on("click", ".duplicate-btn", function() {
                if (confirm("Are you sure you want to duplicate selected tasks?")) {
                    let selectedIds = $(".task-checkbox:checked").map(function() {
                        return this.value;
                    }).get();
                    console.log(selectedIds);
                    if (selectedIds.length > 0) {
                        bulkAction(selectedIds, 'duplicate');
                    }
                }
            });

            $(document).ready(function() {
                // Double-click to edit
                $(document).on('dblclick', '.editable', function() {
                    var originalText = $(this).text();
                    var id = $(this).data('id');
                    var column = $(this).data('column');

                    if (column == 'status') {
                        var select = $('<select class="form-control select2"></select>');

                        select.append('<option value="">{{ __('Select Status') }}</option>');
                        $.each(stages, function(index, value) {
                            var selected = (originalText.trim() === value) ? 'selected' : '';
                            select.append('<option value="' + value + '" ' + selected + '>' + value +
                                '</option>');
                        });

                        $(this).html(select);
                        select.focus();

                        
                       
                        select.change(function() {
                        if($(this).val() === 'Rework')
                        {
                         var etcModal = new bootstrap.Modal(document.getElementById('reworkModal'));
                                 etcModal.show();
                                 $("#reworkTaskId").val(id);
                        }
                        else{
                             console.log("status value",$(this).val());
                            console.log("task id",id);
                            if($(this).val()=="Done")
                            {
                                console.log("status value",$(this).val());
                                console.log("task id",id); 
                                var newStatus = $(this).val();
                                var taskId =id;
                                if (newStatus && newStatus.toLowerCase() === 'done') {
                                    $('#etcTaskId').val(taskId);
                                    $('#etcInput').val('');
                                     $('#etcTaskOriginal').val('originalText');
                                    var etcModal = new bootstrap.Modal(document.getElementById('etcModal'));
                                    etcModal.show();
                                }
                            }else
                            {                                
                            updateData(this, id, column, $(this).val(),originalText);
                            }
                        }
                        });

                        select.blur(function() {
                            $(this).parent().text(originalText);
                        });

                    }else if(column == 'priority') {
                        var select = $('<select class="form-control select2"></select>');

                        select.append('<option value="">{{ __('Select Priority') }}</option>');
                        $.each(priorityArr, function(index, value) {
                            var selected = (originalText.trim() === value.value) ? 'selected' : '';
                            select.append('<option value="' + value.value + '" ' + selected + '>' + value.value +
                                '</option>');
                        });

                        $(this).html(select);
                        select.focus();

                        select.change(function() {
                            updateData(this, id, column, $(this).val(),originalText);
                        });

                        select.blur(function() {
                            $(this).parent().text(originalText);
                        });

                    } else {
                        var input = $('<input type="text" class="edit-input">').val(originalText);
                        $(this).html(input);

                        // Create an input field

                        input.focus();

                        // When user clicks outside, update the value
                        input.blur(function() {
                            var newValue = $(this).val();
                            if (newValue !== originalText) {
                                updateData($(this), id, column, newValue,originalText);
                            } else {
                                $(this).parent().text(originalText);
                            }
                        });

                        // Submit on Enter key
                        input.keypress(function(e) {
                            if (e.which == 13) {
                                input.blur();
                            }
                        });
                    }
                });

                // Function to transform status display text
                function transformStatusDisplay(status) {
                    if (!status) return status;
                    var statusLower = status.trim().toLowerCase();
                    if (statusLower === 'need help') {
                        return 'Help';
                    } else if (statusLower === 'need approval') {
                        return 'Need App';
                    } else if (statusLower === 'not applicable') {
                        return 'Inapplicable';
                    }
                    return status;
                }

                // Function to update the data via AJAX
                function updateData(e, id, column, value,originalText) {

                    $.ajax({
                        url: "{{ route('projecttask.inlineEdit') }}",
                        type: 'get',
                        data: {
                            task_id: id,
                            column: column,
                            value: value,
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'JSON',
                        success: function(data) {
                            console.log(data);
                            // var input = $("<span>", {
                            //     class: "editable",
                            //     "data-id": id,
                            //     "data-column": column,
                            //     html: value
                            // });
                            if(column=='priority')
                            {
                                element = $('.editable[data-id="' + id + '"][data-column="' + column + '"]');
                                let lowColor = priorityArr.find(item => item.value === value)?.color;
                                let originalColor = priorityArr.find(item => item.value === originalText)?.color;
                                let className = "btn-"+lowColor;
                                let OldclassName = "btn-"+originalColor;
                                
                                element.text(value); // Update the text
                                element.addClass(className); // Add a new class
                                element.removeClass(OldclassName); // Add a new class
                            }if(column=='status')
                            {
                                element = $('.editable[data-id="' + id + '"][data-column="' + column + '"]');
                               
                                let item = stagesArr.find(item => item.name === value);
                                if (!item) return;
                                var displayStatus = transformStatusDisplay(value); // Transform the status
                                element.text(displayStatus); // Update the text with transformed value
                                element.css({
                                    "padding": "5px 10px",
                                    "border-radius": "5px",
                                    "color": "white",
                                    "background-color": item.color // Set background color dynamically
                                });

                            }
                            else{
                                $('.editable[data-id="' + id + '"][data-column="' + column + '"]').text(value);

                            }

                            // $(e).html(input);
                            toastrs('Success', data.message, 'success');
                            // toastrs(data.message,
                            // data.message,'success');
                            // btn.closest('.border').remove();
                            // location.reload();
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

               
               
                // initializeDataTable();

                // Reload DataTable when filter values change
                $('#assignee_name, #assignor_name,#status_name,#group_name,#task_name,#priority').on('change', function () {
                    getTaskCount();
                    // Reload the DataTable with new parameters
                    if ($.fn.DataTable.isDataTable('#projects-task-table')) {
                        var table = $('#projects-task-table').DataTable();
                        // Update the ajax data parameters
                        table.settings()[0].ajax.data = function(d) {
                            d.assignee_name = $('#assignee_name').val();
                            d.assignor_name = $('#assignor_name').val();
                            d.status_name = $('#status_name').val();
                              d.priority = $('#priority').val();
                            d.group_name = $('#group_name').val();
                            d.task_name = $('#task_name').val();
                            return d;
                        };
                        table.ajax.reload();
                    }
                });

                 $('#group_name').on('blur', function () {
                    getTaskCount();
                    // Reload the DataTable with new parameters
                    if ($.fn.DataTable.isDataTable('#projects-task-table')) {
                        var table = $('#projects-task-table').DataTable();
                        table.settings()[0].ajax.data = function(d) {
                            d.assignee_name = $('#assignee_name').val();
                            d.assignor_name = $('#assignor_name').val();
                            d.status_name = $('#status_name').val();
                            d.priority = $('#priority').val();
                            d.group_name = $('#group_name').val();
                            d.task_name = $('#task_name').val();
                            return d;
                        };
                        table.ajax.reload();
                    }
                });
                  $('#task_name').on('blur', function () {
                    getTaskCount();
                    // Reload the DataTable with new parameters
                    if ($.fn.DataTable.isDataTable('#projects-task-table')) {
                        var table = $('#projects-task-table').DataTable();
                        table.settings()[0].ajax.data = function(d) {
                            d.assignee_name = $('#assignee_name').val();
                            d.assignor_name = $('#assignor_name').val();
                            d.status_name = $('#status_name').val();
                            d.priority = $('#priority').val();
                            d.group_name = $('#group_name').val();
                            d.task_name = $('#task_name').val();
                            return d;
                        };
                        table.ajax.reload();
                    }
                });
                $('#projects-task-table_filter input').on('keyup', function () {
                    console.log("input",$('.dataTables_filter input').val());
                    getTaskCount();
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
             
            
            // Custom initializeDataTable function is commented out to use the existing backend DataTable
            // The backend ProjectTaskDatatable already handles filtering properly
            /*
            function initializeDataTable() {
    if ($.fn.DataTable.isDataTable('#projects-task-table')) {
        $('#projects-task-table').DataTable().destroy();
    }

    var datatableData = $('#projects-task-table').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 100,
        lengthMenu: [10, 25, 50, 100, 200],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'collection',
                text: '<i class="ti ti-download me-2"></i>',
                className: 'btn btn-light-primary',
                buttons: [
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel me-2"></i> Excel',
                        className: 'btn btn-light text-primary dropdown-item',
                        exportOptions: {
                            modifier: {
                                search: 'applied',
                                order: 'applied'
                            }
                        }
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fas fa-file-csv me-2"></i> CSV',
                        className: 'btn btn-light text-primary dropdown-item',
                        exportOptions: {
                            modifier: {
                                search: 'applied',
                                order: 'applied'
                            }
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print me-2"></i> Print',
                        className: 'btn btn-light text-primary dropdown-item',
                        exportOptions: {
                            modifier: {
                                search: 'applied',
                                order: 'applied'
                            }
                        }
                    }
                ]
            },
            {
                text: '<i class="fas fa-copy"></i> ',
                className: 'btn btn-light-primary duplicate-btn',
                attr: { id: 'duplicate-btn', disabled: 'disabled' },
                action: function(e, dt, node, config) {
                    let selectedIds = $(".task-checkbox:checked").map(function() { 
                        return this.value; 
                    }).get();
                    if (selectedIds.length > 0) {
                        bulkAction(selectedIds, 'duplicate');
                    }
                }
            },
            {
                'text': '<i class="fas fa-trash"></i>',
                'className': 'btn btn-light-danger delete-group-btn',
                'attr': {
                    'id': 'delete-btn',
                    'disabled': function() { return false; }
                },
                'action': function(e, dt, node, config) {
                    if (!$(node).attr("disabled")) {
                        let selectedIds = $(".task-checkbox:checked").map(function() { 
                            return this.value; 
                        }).get();
                        if (selectedIds.length > 0) {
                            deleteTasks(selectedIds);
                        }
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
        ajax: {
            url: "route('projecttask.list')",
            data: function(d) {
                d.assignee_name = $('#assignee_name').val();
                d.assignor_name = $('#assignor_name').val();
                 d.group_name = $('#group_name').val();
                 d.task_name = $('#task_name').val();
                d.status_name = $('#status_name').val();
            }
        },
        columns: [
            { data: 'checkbox', name: 'checkbox', orderable: false },
            { data: 'group', name: 'group' },
            { data: 'title', name: 'title' },
            { data: 'assigner_name', name: 'assigner_name' },
            { data: 'assign_to', name: 'assign_to' },
            { data: 'start_date', name: 'start_date' },
            { data: 'due_date', name: 'due_date' },
            { data: 'eta_time', name: 'eta_time' },
            { data: 'etc_done', name: 'etc_done' },
            { data: 'completion_day', name: 'completion_day' },
            { data: 'status', name: 'status' },
            { data: 'links', name: 'links' },
            { data: 'link_3', name: 'link_3' },
             { data: 'link_4', name: 'link_4' },
              { data: 'link_5', name: 'link_5' },
              { data: 'link_7', name: 'link_7' },
              { data: 'link_8', name: 'link_8' },
              { data: 'link_6', name: 'link_6' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });
    
    return datatableData;
}
*/
                function bulkAction(selectedIds, actionType, delete_rating = 0, delete_feedback = '') {
                    $.ajax({
                        url: "{{ route('projecttask.bulkAction') }}",
                        type: 'get',
                        data: {
                            selected_ids: selectedIds,
                            action_type: actionType,
                            delete_rating: delete_rating,
                            delete_feedback: delete_feedback
                        },
                        dataType: 'JSON',
                        success: function(data) {
                            toastrs(data.message, 'success');
                            if(actionType == 'delete') {
                                selectedIds.forEach(function(id) {
                                    $('#projects-task-table').DataTable().row($("input[value='" + id + "']").closest('tr')).remove().draw();
                                });
                            } else {
                                // Reload the existing DataTable instead of reinitializing
                                if ($.fn.DataTable.isDataTable('#projects-task-table')) {
                                    $('#projects-task-table').DataTable().ajax.reload();
                                }
                            }
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
                }
                function getTaskCount() {
                   
                    $.ajax({
                        url: "{{ route('projecttask.count') }}",
                        type: 'get',
                        data: {
                            assignee_name:$('#assignee_name').val(),
                            assignor_name:$('#assignor_name').val(),
                            status_name: $('#status_name').val(),
                            priority: $('#priority').val(),
                            group_name: $('#group_name').val(),
                            task_name: $('#task_name').val(),
                            search_value:$('#projects-task-table_filter input').val(),
                        },
                        dataType: 'JSON',
                        success: function(data) {
                            console.log(data)
                            if(data.is_success)
                            {
                                $("#pending-count").html(data.data.pending_count)
                                $("#completed-count").html(data.data.complete_count)
                                $("#overdue-count").html(data.data.overdue_count)
                                $("#total_eta").html(data.data.total_eta)
                                $("#total_atc").html(data.data.total_atc) // Add this line for ATC
                                $("#total-count").html(data.data.total_count)
                                
                                // Store filtered data for graph
                                currentFilteredData = data.data;
                                
                                // Update graph card state
                                updateGraphCardState();
                                
                                // Update today's graph button state
                                updateTodayGraphButtonState();
                               
                            }else{
                                $("#pending-count").html(0)
                                $("#completed-count").html(0)
                                $("#overdue-count").html(0)
                               $("#total-count").html(0)
                               $("#total_eta").html(0)
                               $("#total_atc").html(0) // Add this line for ATC
                               
                               // Clear filtered data
                               currentFilteredData = null;
                               updateGraphCardState();
                               
                               // Update today's graph button state
                               updateTodayGraphButtonState();
                               
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
                    
                    // Fetch done task data for ETC and ATC cards
                    getDoneTaskData();
                }
                
                // Function to fetch done task data
                function getDoneTaskData() {
                    // Get all filter values - use actual filters for ETC/ATC calculation
                    var assigneeFilter = $('#assignee_name').val() || [];
                    var assignorFilter = $('#assignor_name').val() || [];
                    var statusFilter = $('#status_name').val() || [];
                    var groupFilter = $('#group_name').val() || [];
                    var taskFilter = $('#task_name').val() || [];
                    var priorityFilter = $('#priority').val() || [];
                    
                    $.ajax({
                        url: "{{ route('projecttask.done.count') }}",
                        type: 'get',
                        data: {
                            assignee_name: assigneeFilter,
                            assignor_name: assignorFilter,
                            status_name: statusFilter,
                            group_name: groupFilter,
                            task_name: taskFilter,
                            priority: priorityFilter,
                            date_filter: 'last_30_days',  // Fetch data from DAR report for last 30 days
                            source: 'dar_report'           // Indicate to fetch from DAR report
                        },
                        dataType: 'JSON',
                        success: function(data) {
                            console.log('Done Task Data from DAR (L30):', data);
                            if(data.is_success) {
                                // Update ETC and ATC cards with filtered data from DAR report (rounded)
                                $("#filtered-etc-count").html(Math.round(data.data.total_eta_hours || 0));
                                $("#filtered-atc-count").html(Math.round(data.data.total_atc_hours || 0));
                            } else {
                                $("#filtered-etc-count").html(0);
                                $("#filtered-atc-count").html(0);
                            }
                        },
                        error: function(data) {
                            console.error('Error fetching done task data from DAR:', data);
                            $("#filtered-etc-count").html(0);
                            $("#filtered-atc-count").html(0);
                        }
                    });
                }
                
// ====== BEAUTIFUL OVERDUE NOTIFICATION CODE ======
                
let overdueCheckInterval;
let overdueModalShown = false;
const notificationSound = new Audio('https://www.soundsnap.com/wood_block_ui_positive_octave');

function checkOverdueAndNotify(forceShow = false) {
    $.ajax({
        url: "{{ route('projecttask.count') }}",
        type: 'get',
        data: {
            assignee_name: $('#assignee_name').val(),
            assignor_name: $('#assignor_name').val(),
            status_name: $('#status_name').val(),
            priority: $('#priority').val(),
            group_name: $('#group_name').val(),
            task_name: $('#task_name').val(),
            search_value: $('.dataTables_filter input').val(),
        },
        dataType: 'JSON',
        success: function(data) {
            if (data.is_success && data.data.overdue_count > 2) {
                showOverduePopup(data.data.overdue_count);
            } else {
                overdueModalShown = false;
            }
        }
    });
}

function showOverduePopup(count) {
    notificationSound.currentTime = 0;
    notificationSound.play().catch(error => {
        console.error('Error playing notification sound:', error);
    });

    $('#overdueModal').remove();

    var popupHtml = `
        <div class="modal fade" id="overdueModal" tabindex="-1" aria-labelledby="overdueModalLabel" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
                    <div class="modal-header bg-gradient-danger text-white py-3" style="border-bottom: none;">
                        <div class="d-flex align-items-center w-100">
                            <div class="bg-white rounded-circle p-2 me-3" style="width: 40px; height: 40px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                </svg>
                            </div>
                            <h5 class="modal-title mb-0" id="overdueModalLabel">Urgent Attention Needed!</h5>
                            <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    </div>
                    <div class="modal-body py-4 px-4">
                        <div class="text-center mb-3">
                            <div class="position-relative d-inline-block">
                                <div class="bg-danger bg-opacity-10 rounded-circle p-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                                        <line x1="12" y1="9" x2="12" y2="13"></line>
                                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                                    </svg>
                                </div>
                                <div class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 1rem;">
                                    ${count}
                                </div>
                            </div>
                        </div>
                        <h4 class="text-center mb-3">You have <span class="text-danger">${count} overdue tasks</span>!</h4>
                        <p class="text-muted text-center mb-4">These tasks require your immediate attention to prevent delays.</p>
                        <div class="progress mb-4" style="height: 8px;">
                            <div class="progress-bar bg-danger progress-bar-striped progress-bar-animated" role="progressbar" style="width: ${Math.min(count * 10, 100)}%"></div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-3" style="border-top: none;">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                            <i class="fas fa-clock me-2"></i>I will finish very soon
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    $('body').append(popupHtml);

    var overdueModal = new bootstrap.Modal(document.getElementById('overdueModal'), {
        keyboard: false
    });
    overdueModal.show();
    overdueModalShown = true;

    $('#overdueModal').on('hidden.bs.modal', function () {
        overdueModalShown = false;
        $(this).remove();
    });
}

function getNextNotificationTime() {
    // Get current time in India (IST) - UTC+5:30
    const now = new Date();
    
    // Calculate 9:30 PM IST (16:00 UTC)
    const istTime = new Date();
    istTime.setUTCHours(16, 0, 0, 0);
    
    // If it's already past 9:30 PM IST today, schedule for tomorrow
    if (now.getTime() > istTime.getTime()) {
        istTime.setUTCDate(istTime.getUTCDate() + 1);
    }
    
    return istTime.getTime();
}

function scheduleDailyNotification() {
    const nextNotificationTime = getNextNotificationTime();
    const now = new Date().getTime();
    const initialDelay = nextNotificationTime - now;
    
    // Initial timeout for the first notification
    setTimeout(() => {
        checkOverdueAndNotify(true);
        // Then set interval for every 24 hours
        overdueCheckInterval = setInterval(() => {
            checkOverdueAndNotify(true);
        }, 24 * 60 * 60 * 1000);
    }, initialDelay);
}

$(document).ready(function () {
    scheduleDailyNotification();
    
    $(window).on('beforeunload', function () {
        clearInterval(overdueCheckInterval);
    });
});


// ====== END OF BEAUTIFUL OVERDUE NOTIFICATION CODE ======

// ====== TODAY'S COMPLETED TASKS GRAPH FUNCTIONALITY ======

let todayChart = null;

function showTodayGraph() {
    console.log('showTodayGraph function called');
    
    // Get current filter values
    const assigneeNames = $('#assignee_name').val();
    const assignorNames = $('#assignor_name').val();
    const statusNames = $('#status_name').val();
    const groupNames = $('#group_name').val();
    const taskNames = $('#task_name').val();
    
    console.log('Filter values:', {
        assignee: assigneeNames,
        assignor: assignorNames,
        status: statusNames,
        group: groupNames,
        task: taskNames
    });
    
    // Set today's date in modal
    const today = new Date();
    const formattedDate = today.toLocaleDateString('en-US', { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
    $('#today-date').text(formattedDate);
    
    // Fetch today's completed tasks data
    $.ajax({
        url: "{{ route('projecttask.today.completed') }}",
        method: 'GET',
        data: {
            assignee_name: assigneeNames,
            assignor_name: assignorNames,
            status_name: statusNames,
            group_name: groupNames,
            task_name: taskNames
        },
        success: function(data) {
            console.log('Today Completed Tasks Data:', data);
            
            // Update counts
            $('#today-completed-count').text(data.today_completed_count);
            $('#total-completed-count').text($('#completed-count').text());
            
            // Show modal
            const todayModal = new bootstrap.Modal(document.getElementById('todayGraphModal'));
            todayModal.show();
            
            // Create chart when modal is shown
            setTimeout(() => {
                createTodayChart(data);
            }, 300);
        },
        error: function(xhr, status, error) {
            console.error('Error fetching today completed tasks:', error);
            console.error('Response:', xhr.responseText);
            toastrs('Error', 'Failed to fetch today\'s completed tasks data', 'error');
        }
    });
}

function createTodayChart(data) {
    const ctx = document.getElementById('todayTaskChart').getContext('2d');
    
    // Destroy existing chart if it exists
    if (todayChart) {
        todayChart.destroy();
    }
    
    const todayCount = data.today_completed_count;
    const totalCount = parseInt($('#completed-count').text());
    const otherDaysCount = totalCount - todayCount;
    
    todayChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Today\'s Completed', 'Other Days Completed'],
            datasets: [{
                label: 'Tasks',
                data: [todayCount, otherDaysCount],
                backgroundColor: [
                    'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                    'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)'
                ],
                borderColor: [
                    '#667eea',
                    '#f5576c'
                ],
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            size: 12,
                            weight: '600'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: '#667eea',
                    borderWidth: 1,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return `${label}: ${value} tasks (${percentage}%)`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)',
                        drawBorder: false
                    },
                    ticks: {
                        stepSize: 1,
                        font: {
                            size: 11
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11,
                            weight: '600'
                        }
                    }
                }
            },
            animation: {
                duration: 1000,
                easing: 'easeInOutQuart'
            }
        }
    });
}

function refreshTodayData() {
    showTodayGraph();
    toastrs('Success', 'Today\'s data refreshed successfully!', 'success');
}

function updateTodayGraphButtonState() {
    const assigneeNames = $('#assignee_name').val();
    const completedCount = parseInt($('#completed-count').text()) || 0;
    const todayGraphBtn = $('#today-graph-btn');
    
    // Enable button when there are completed tasks (regardless of filter)
    if (completedCount > 0) {
        todayGraphBtn.prop('disabled', false);
        todayGraphBtn.removeClass('btn-secondary').addClass('btn-light');
        todayGraphBtn.css('cursor', 'pointer');
    } else {
        todayGraphBtn.prop('disabled', true);
        todayGraphBtn.removeClass('btn-light').addClass('btn-secondary');
        todayGraphBtn.css('cursor', 'not-allowed');
    }
}

// ====== END OF TODAY'S COMPLETED TASKS GRAPH FUNCTIONALITY ======

        </script>
        
<!--daily update popup-->

        <script>
        $(document).ready(function() {
    // Function to check if popup should be shown
        function shouldShowPopup() {
            const lastShownDate = localStorage.getItem('lastMotivationPopupDate');
            const today = new Date().toDateString();
            if (lastShownDate === today) {
                return false;
            }
        
            // Get current UTC time and convert to IST
            const now = new Date();
            const utc = now.getTime() + (now.getTimezoneOffset() * 60000);
            const istTime = new Date(utc + (5.5 * 60 * 60 * 1000)); // IST is UTC+5:30
        
            // Trigger at exactly 9:00 PM IST
            return istTime.getHours() === 21 && istTime.getMinutes() >= 50 && istTime.getMinutes() < 51;
        }


    // Function to show the motivation popup
    function showMotivationPopup() {
        // Remove existing modal if any
        $('#motivationModal').remove();

        // Create beautiful popup HTML
        const popupHtml = `
            <div class="modal fade" id="motivationModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden; border: 2px solid #4a90e2;">
                        <div class="modal-header bg-gradient-primary text-white py-3" style="border-bottom: none;">
                            <div class="d-flex align-items-center w-100">
                                <div class="bg-white rounded-circle p-2 me-3" style="width: 40px; height: 40px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#4a90e2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                    </svg>
                                </div>
                                <h5 class="modal-title mb-0">Daily Update</h5>
                                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                        </div>
                        <div class="modal-body py-4 px-4">
                            <div class="text-center mb-3">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-4 d-inline-block">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#4a90e2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <path d="M8 14s1.5 2 4 2 4-2 4-2"></path>
                                        <line x1="9" y1="9" x2="9.01" y2="9"></line>
                                        <line x1="15" y1="9" x2="15.01" y2="9"></line>
                                    </svg>
                                </div>
                            </div>
                            <h4 class="text-center mb-3 text-primary">Professional Excellence Reminder</h4>
                            <div class="alert alert-primary border-0 bg-primary bg-opacity-10">
                                <p class="mb-0 text-center fw-bold text-white" style="font-size: 1.1rem;">
                                    "Avoid overdue tasks, excel consistently, and secure monthly salary increments through strong dedication!"
                                </p>
                            </div>
                            <div class="text-center mt-4">
                                <div class="d-flex justify-content-center">
                                    <div class="mx-2">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-3 d-inline-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#4a90e2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <polyline points="12 6 12 12 16 14"></polyline>
                                            </svg>
                                        </div>
                                        <p class="small mt-2 mb-0">Timely Completion</p>
                                    </div>
                                    <div class="mx-2">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-3 d-inline-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#4a90e2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                            </svg>
                                        </div>
                                        <p class="small mt-2 mb-0">Consistent Excellence</p>
                                    </div>
                                    <div class="mx-2">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-3 d-inline-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#4a90e2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <line x1="12" y1="1" x2="12" y2="3"></line>
                                                <line x1="12" y1="21" x2="12" y2="23"></line>
                                                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                                                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                                                <line x1="1" y1="12" x2="3" y2="12"></line>
                                                <line x1="21" y1="12" x2="23" y2="12"></line>
                                                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                                                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                                            </svg>
                                        </div>
                                        <p class="small mt-2 mb-0">Career Growth</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light py-3" style="border-top: none;">
                            <button type="button" class="btn btn-primary rounded-pill px-4 ms-auto" data-bs-dismiss="modal">
                                <i class="fas fa-thumbs-up me-2"></i>I'm Motivated!
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        $('body').append(popupHtml);

        const motivationModal = new bootstrap.Modal(document.getElementById('motivationModal'), {
            keyboard: false
        });
        motivationModal.show();

        // Mark as shown for today
        localStorage.setItem('lastMotivationPopupDate', new Date().toDateString());

        $('#motivationModal').on('hidden.bs.modal', function() {
            $(this).remove();
        });
    }

    // Check if popup should be shown on page load
    if (shouldShowPopup()) {
        showMotivationPopup();
    }

    // Set interval to check every minute (in case page is left open)
    setInterval(() => {
        if (shouldShowPopup()) {
            showMotivationPopup();
        }
    }, 60000); // Check every minute
});

// Show ETC modal only when status is set to "Done"
$(document).on('change', '.editable[data-column="status"] select', function() {
    // var newStatus = $(this).val();
    // var taskId = $(this).closest('.editable').data('id');
    // if (newStatus && newStatus.toLowerCase() === 'done') {
    //     $('#etcTaskId').val(taskId);
    //     $('#etcInput').val('');
    //     var etcModal = new bootstrap.Modal(document.getElementById('etcModal'));
    //     etcModal.show();
    // }
});


$('#etcForm').on('submit', function(e) {
    e.preventDefault();
    var etc = $('#etcInput').val();
    var taskId = $('#etcTaskId').val();
    var completionDate = $('#completionDate').val(); // Get completion date
    
    $.ajax({
        url: "{{route('projecttask.update.etc')}}",
        method: 'POST',
        data: {
            etc: etc,
            task_id: taskId,
            completion_date: completionDate, // Include completion date
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            console.log(response);
            if(response.is_success) {
                var etcModal = bootstrap.Modal.getInstance(document.getElementById('etcModal'));
                etcModal.hide();
                var row = $('#projects-task-table').DataTable().row($("input[value='" + taskId + "']").closest('tr'));
                row.data({
                    ...row.data(),
                    etc_done: etc,
                    completion_date: completionDate // Update completion date in table
                }).draw();
            }
        },
        error: function(error) {
            console.error(error);
        }
    });
});
// Show ETC modal only when status is set to "Done"
</script>
<script>
    $(document).ready(function() {
    // Check user permissions and enable/disable delete button
    function checkDeletePermission() {
        const allowedEmails = ['president@5core.com', 'tech-support@5core.com', 'mgr-advertisement@5core.com', 'mgr-content@5core.com','sjoy7486@gmail.com','sr.manager@5core.com','mgr-operations@5core.com',];
        const currentUserEmail = '{{ Auth::user()->email }}';
        const deleteBtn = $('#delete-btn');
        
        if (allowedEmails.includes(currentUserEmail)) {
            deleteBtn.removeAttr('disabled');
            deleteBtn.removeClass('disabled');
            deleteBtn.css('opacity', '1');
        } else {
            deleteBtn.attr('disabled', 'disabled');
            deleteBtn.addClass('disabled');
            deleteBtn.css('opacity', '0.6');
            deleteBtn.attr('title', 'Permission required');
        }
    }

    // Call the function on page load
    checkDeletePermission();
    
    // Also check when DataTable is redrawn
    $('#projects-task-table').on('draw.dt', function() {
        checkDeletePermission();
    });
});
</script>

<!--daily update popup-->

 <script>
    // Star Rating Logic
    const stars = document.querySelectorAll('.star-rating i');
    let currentRating = 0;

    stars.forEach(star => {
      star.addEventListener('click', () => {
        currentRating = star.getAttribute('data-value');
        stars.forEach(s => {
          s.classList.remove('selected');
          if (s.getAttribute('data-value') <= currentRating) {
            s.classList.add('selected');
          }
        });
      });
    });

    // Delete Button Logic - No alert
    document.getElementById('confirmDelete').addEventListener('click', () => {
      const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
      modal.hide();
      // You can place your deletion logic here
    });
  </script>
<script>
  $(function () {
  $('[data-bs-toggle="tooltip"]').tooltip();
});

// Global variables for graph functionality
let taskChart = null;
let currentFilteredData = null;

// Function to show graph modal
function showGraphModal() {
    if (!currentFilteredData) {
        return;
    }
    
    const modal = new bootstrap.Modal(document.getElementById('graphModal'));
    modal.show();
    
    // Update filtered user name in modal
    const assigneeNames = $('#assignee_name').val();
    if (assigneeNames && assigneeNames.length > 0) {
        const selectedNames = assigneeNames.map(email => {
            const option = $('#assignee_name option[value="' + email + '"]');
            return option.text();
        }).join(', ');
        $('#filtered-user-name').text(selectedNames);
    }
    
    // Create chart when modal is shown
    setTimeout(() => {
        createTaskChart();
    }, 300);
}

// Function to create the bar chart or line chart
function createTaskChart(chartType = 'bar') {
    const ctx = document.getElementById('taskChart').getContext('2d');
    
    // Destroy existing chart if it exists
    if (taskChart) {
        taskChart.destroy();
    }
    
    const data = currentFilteredData;
    
    // Update legend numbers and calculate additional metrics
    const totalTasks = data.total_count || 0;
    const pendingTasks = data.pending_count || 0;
    const overdueTasks = data.overdue_count || 0;
    const completedTasks = data.complete_count || 0;
    
    $('#legend-total').text(totalTasks);
    $('#legend-pending').text(pendingTasks);
    $('#legend-overdue').text(overdueTasks);
    $('#legend-done').text(completedTasks);
    
    // Calculate completion rate
    const completionRate = totalTasks > 0 ? ((completedTasks / totalTasks) * 100).toFixed(1) : 0;
    $('#completion-rate').text(completionRate + '%');
    
    // Calculate average time (mock data - replace with actual calculation)
    const avgTime = data.average_time || Math.floor(Math.random() * 24) + 1;
    $('#average-time').text(avgTime + 'h');
    
    // Update last updated time
    $('#last-updated').text(new Date().toLocaleString());
    
    // Chart configuration based on type
    const chartConfig = {
        type: chartType,
        data: {
            labels: ['Total Tasks', 'Pending', 'Overdue', 'Completed'],
            datasets: [{
                label: 'Task Count',
                data: [totalTasks, pendingTasks, overdueTasks, completedTasks],
                backgroundColor: chartType === 'bar' ? [
                    'rgba(54, 162, 235, 0.8)',  // Total - Blue
                    'rgba(23, 162, 184, 0.8)',  // Pending - Teal
                    'rgba(220, 53, 69, 0.8)',   // Overdue - Red
                    'rgba(40, 167, 69, 0.8)'    // Completed - Green
                ] : 'rgba(102, 126, 234, 0.2)',
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(23, 162, 184, 1)',
                    'rgba(220, 53, 69, 1)',
                    'rgba(40, 167, 69, 1)'
                ],
                borderWidth: chartType === 'line' ? 3 : 2,
                fill: chartType === 'line' ? true : false,
                tension: chartType === 'line' ? 0.4 : 0,
                pointBackgroundColor: chartType === 'line' ? [
                    'rgba(54, 162, 235, 1)',
                    'rgba(23, 162, 184, 1)',
                    'rgba(220, 53, 69, 1)',
                    'rgba(40, 167, 69, 1)'
                ] : undefined,
                pointBorderColor: chartType === 'line' ? '#fff' : undefined,
                pointBorderWidth: chartType === 'line' ? 2 : undefined,
                pointRadius: chartType === 'line' ? 6 : undefined,
                pointHoverRadius: chartType === 'line' ? 8 : undefined
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: chartType === 'line',
                    position: 'top',
                    labels: {
                        padding: 20,
                        font: {
                            size: 14,
                            weight: '600'
                        },
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed.y || context.parsed;
                            const percentage = totalTasks > 0 ? ((value / totalTasks) * 100).toFixed(1) : 0;
                            return `${label}: ${value} tasks (${percentage}%)`;
                        }
                    },
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    cornerRadius: 10,
                    padding: 12,
                    displayColors: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: {
                            size: 12,
                            weight: '500'
                        },
                        color: '#6b7280'
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)',
                        lineWidth: 1
                    }
                },
                x: {
                    ticks: {
                        font: {
                            size: 12,
                            weight: '500'
                        },
                        color: '#6b7280'
                    },
                    grid: {
                        display: false
                    }
                }
            },
            animation: {
                duration: 1200,
                easing: 'easeInOutQuart'
            },
            elements: {
                bar: {
                    borderRadius: chartType === 'bar' ? 8 : 0,
                }
            }
        }
    };
    
    taskChart = new Chart(ctx, chartConfig);
}

// Chart type toggle functionality
$(document).on('click', '.chart-type-btn', function() {
    const chartType = $(this).data('chart-type');
    
    // Update button states
    $('.chart-type-btn').removeClass('active').css({
        'background': 'transparent',
        'color': '#667eea'
    });
    
    $(this).addClass('active').css({
        'background': 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        'color': 'white'
    });
    
    // Recreate chart with new type
    if (currentFilteredData) {
        createTaskChart(chartType);
    }
});

// Function to refresh chart data
function refreshChartData() {
    // Show loading state
    const refreshBtn = $('button[onclick="refreshChartData()"]');
    const originalText = refreshBtn.html();
    refreshBtn.html('<i class="ti ti-loader-2 ti-spin me-1"></i>Refreshing...');
    refreshBtn.prop('disabled', true);
    
    // Simulate data refresh (replace with actual AJAX call)
    setTimeout(() => {
        // Update last updated time
        $('#last-updated').text(new Date().toLocaleString());
        
        // Get current chart type
        const activeChartType = $('.chart-type-btn.active').data('chart-type') || 'bar';
        
        // Recreate chart
        if (currentFilteredData) {
            createTaskChart(activeChartType);
        }
        
        // Restore button state
        refreshBtn.html(originalText);
        refreshBtn.prop('disabled', false);
        
        // Show success message
        toastrs('Success', 'Chart data refreshed successfully!', 'success');
    }, 1000);
}

// Function to update graph card state
function updateGraphCardState() {
    const assigneeNames = $('#assignee_name').val();
    const graphCard = $('#graph-card');
    const graphBtn = $('#graph-btn');
    
    if (assigneeNames && assigneeNames.length > 0) {
        // Enable graph card
        graphCard.css({
            'opacity': '1',
            'cursor': 'pointer'
        });
        graphBtn.prop('disabled', false);
        graphCard.removeClass('disabled');
    } else {
        // Disable graph card
        graphCard.css({
            'opacity': '0.5',
            'cursor': 'not-allowed'
        });
        graphBtn.prop('disabled', true);
        graphCard.addClass('disabled');
        currentFilteredData = null;
    }
}

// Function to update filtered card states (ETC and ATC cards respect filters)
function updateFilteredCardStates() {
    // ETC and ATC cards now show data based on applied filters
    // When no filter is applied, they show all data for the current month
    getDoneTaskData();
}

// Initialize card states on page load
$(document).ready(function() {
    
    // Change Assignee Modal functionality
    window.handleAssigneeUpdate = function() {
        
        // Get selected task IDs directly from checkboxes (more reliable)
        var selectedIds = $(".task-checkbox:checked").map(function() {
            return this.value;
        }).get();
        
        // Also try to get from hidden field as fallback
        var hiddenFieldIds = $('#selected-task-ids-assignee').val();
        if (selectedIds.length === 0 && hiddenFieldIds) {
            try {
                selectedIds = JSON.parse(hiddenFieldIds);
            } catch(e) {
                console.error('Failed to parse hidden field:', e);
            }
        }
        
        var assigneeEmail = $('#assignee-select').val();

        // Validation
        if (selectedIds.length === 0) {
            if (typeof toastrs === 'function') {
                toastrs('Error', 'No tasks selected', 'error');
            } else {
                alert('No tasks selected');
            }
            return;
        }

        if (!assigneeEmail) {
            if (typeof toastrs === 'function') {
                toastrs('Error', 'Please select an assignee', 'error');
            } else {
                alert('Please select an assignee');
            }
            $('#assignee-select').focus();
            return;
        }

        // Show loading state
        var $button = $('#update-assignee-btn');
        var originalHtml = $button.html();
        $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Updating...');

        // Make AJAX request
        $.ajax({
            url: '{{ route("projecttask.bulkUpdateAssignee") }}',
            method: 'POST',
            dataType: 'json',
            data: {
                task_ids: JSON.stringify(selectedIds),
                assignee_email: assigneeEmail,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                
                if (response.is_success) {
                    // Show success message
                    if (typeof toastrs === 'function') {
                        toastrs('Success', response.message, 'success');
                    } else if (typeof show_toastr === 'function') {
                        show_toastr('Success', response.message, 'success');
                    } else {
                        alert('Success: ' + response.message);
                    }
                    
                    // Close modal
                    $('#change-assignee-modal').modal('hide');
                    
                    // Refresh the DataTable
                    if ($.fn.DataTable.isDataTable('#projects-task-table')) {
                        $('#projects-task-table').DataTable().ajax.reload(null, false);
                    } else {
                        location.reload();
                    }
                    
                    // Reset form and UI state
                    $('#change-assignee-form')[0].reset();
                    $('#selected-task-ids-assignee').val('');
                    $('.task-checkbox').prop('checked', false);
                    $('#select-all').prop('checked', false);
                    $('#delete-btn, #duplicate-btn, #change-assignor-btn, #change-assignee-btn').prop('disabled', true);
                    
                    // Re-enable button
                    $button.prop('disabled', false).html(originalHtml);
                } else {
                    // Show error message
                    if (typeof toastrs === 'function') {
                        toastrs('Error', response.message || 'Failed to update assignee', 'error');
                    } else if (typeof show_toastr === 'function') {
                        show_toastr('Error', response.message || 'Failed to update assignee', 'error');
                    } else {
                        alert('Error: ' + (response.message || 'Failed to update assignee'));
                    }
                    
                    // Re-enable button
                    $button.prop('disabled', false).html(originalHtml);
                }
            },
            error: function(xhr, status, error) {
                
                var errorMessage = 'An error occurred while updating assignee.';
                try {
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        errorMessage = 'Server error: ' + xhr.status;
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                }
                
                if (typeof toastrs === 'function') {
                    toastrs('Error', errorMessage, 'error');
                } else if (typeof show_toastr === 'function') {
                    show_toastr('Error', errorMessage, 'error');
                } else {
                    alert('Error: ' + errorMessage);
                }
                
                // Re-enable button
                $button.prop('disabled', false).html(originalHtml);
            },
            complete: function() {
                // Reset button state
                $button.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Update Assignee');
            }
        });
    };
    
    // Attach update button handler
    $('#update-assignee-btn').on('click', function() {
        handleAssigneeUpdate();
    });
    
    updateGraphCardState();
    updateFilteredCardStates();
    updateTodayGraphButtonState();
    
    // Load ETC/ATC data based on current filters (or all data if no filter)
    getDoneTaskData();
    
    // Load urgent ETC data on page load
    getUrgentETCData();
    
    // Update card states when filters change
    $('#assignee_name, #assignor_name, #status_name, #group_name, #task_name,#priority').on('change keyup', function() {
        updateGraphCardState();
        updateFilteredCardStates();
        updateTodayGraphButtonState();
        
        // Fetch done task data based on applied filters
        getDoneTaskData();
    });

    // Change Assignor Modal functionality
    $('#update-assignor-btn').on('click', function() {
        var selectedTaskIds = $('#selected-task-ids').val();
        var assignorEmail = $('#assignor-select').val();

        if (!selectedTaskIds || !assignorEmail) {
            alert('Please select an assignor');
            return;
        }

        // Show loading state
        var $button = $(this);
        $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Updating...');

        $.ajax({
            url: '{{ route("projecttask.bulkUpdateAssignor") }}',
            method: 'POST',
            data: {
                task_ids: selectedTaskIds,
                assignor_email: assignorEmail,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.is_success) {
                    // Show success message using the existing notification system
                    if (typeof show_toastr === 'function') {
                        show_toastr('Success', response.message, 'success');
                    }
                    
                    // Close modal
                    $('#change-assignor-modal').modal('hide');
                    
                    // Refresh the DataTable
                    if ($.fn.DataTable.isDataTable('#projects-task-table')) {
                        $('#projects-task-table').DataTable().ajax.reload(null, false);
                    } else {
                        location.reload();
                    }
                    
                    // Reset form and UI state
                    $('#change-assignor-form')[0].reset();
                    $('#selected-task-ids').val('');
                    $('.task-checkbox').prop('checked', false);
                    $('#select-all').prop('checked', false);
                    $('#delete-btn, #duplicate-btn, #change-assignor-btn').prop('disabled', true);
                } else {
                    if (typeof show_toastr === 'function') {
                        show_toastr('Error', response.message, 'error');
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            },
            error: function(xhr, status, error) {
                
                var errorMessage = 'An error occurred while updating assignor.';
                try {
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        // Try to parse error from response text
                        if (xhr.responseText.includes('Route') && xhr.responseText.includes('not found')) {
                            errorMessage = 'Route not found. Please check the server configuration.';
                        } else {
                            errorMessage = 'Server Error (Status: ' + xhr.status + ')';
                        }
                    }
                } catch (e) {
                    // Error parsing response
                }
                
                if (typeof show_toastr === 'function') {
                    show_toastr('Error', errorMessage, 'error');
                } else {
                    alert('Error: ' + errorMessage);
                }
            },
            complete: function() {
                // Reset button state
                $button.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Update Assignor');
            }
        });
    });

    // Reset modal when it's hidden
    $('#change-assignor-modal').on('hidden.bs.modal', function () {
        $('#change-assignor-form')[0].reset();
        $('#selected-task-ids').val('');
        $('#update-assignor-btn').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Update Assignor');
    });

    // Attach handlers when modal is shown
    $('#change-assignee-modal').on('shown.bs.modal', function() {
        console.log('=== Modal Shown - Attaching Handlers ===');
        
        // Log when assignee is selected from dropdown
        $('#assignee-select').off('change.assignee').on('change.assignee', function() {
            var selectedEmail = $(this).val();
            var selectedText = $(this).find('option:selected').text();
            console.log('=== Assignee Selected ===');
            console.log('Assignee Email:', selectedEmail);
            console.log('Assignee Name:', selectedText);
        });
        
        // Attach update button handler
        $('#update-assignee-btn').on('click', function() {
            alert('Update Assignee Button Clicked');
            console.log('Update Assignee Button Clicked');
            handleAssigneeUpdate();
        });
        
        console.log('Handlers attached. Button exists:', $('#update-assignee-btn').length > 0);
        console.log('Select exists:', $('#assignee-select').length > 0);
    });

    // Also attach document-level handler as backup
    $(document).on('click', '#update-assignee-btn', function() {
        console.log('=== Update Assignee Button Clicked (Document Handler) ===');
        if (typeof window.handleAssigneeUpdate === 'function') {
            window.handleAssigneeUpdate();
        } else {
            console.error('handleAssigneeUpdate function not found');
        }
    });

    // Log when assignee is selected from dropdown (document-level delegation)
    $(document).on('change', '#assignee-select', function() {
        var selectedEmail = $(this).val();
        var selectedText = $(this).find('option:selected').text();
        console.log('=== Assignee Selected (Document Handler) ===');
        console.log('Assignee Email:', selectedEmail);
        console.log('Assignee Name:', selectedText);
    });
    
    // Also attach document-level handler as backup
    $(document).on('click', '#update-assignee-btn', function() {
        console.log('=== Update Assignee Button Clicked (Document Handler) ===');
        if (typeof window.handleAssigneeUpdate === 'function') {
            window.handleAssigneeUpdate();
        } else {
            console.error('handleAssigneeUpdate function not found');
        }
    });

    // Reset assignee modal when it's hidden
    $('#change-assignee-modal').on('hidden.bs.modal', function () {
        $('#change-assignee-form')[0].reset();
        $('#selected-task-ids-assignee').val('');
        $('#update-assignee-btn').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Update Assignee');
    });
    
    
        // ETC Update functionality
$('#update-etc-btn').on('click', function() {
    var selectedTaskIds = $('#selected-task-ids-etc').val();
    var etcValue = $('#etc-input').val();

    console.log('Selected Task IDs for ETC:', selectedTaskIds);
    console.log('ETC Value:', etcValue);

    if (!selectedTaskIds || !etcValue) {
        alert('Please enter an ETC value');
        return;
    }

    // Show loading state
    var $button = $(this);
    $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Updating...');

    $.ajax({
        url: '{{ route("projecttask.bulkUpdateETC") }}',
        method: 'POST',
        data: {
            task_ids: selectedTaskIds,
            etc_value: etcValue,
            _token: '{{ csrf_token() }}'
        },
        beforeSend: function() {
            console.log('Sending ETC AJAX request...');
        },
        success: function(response) {
            console.log('ETC Success Response:', response);
            if (response.is_success) {
                // Show success message
                if (typeof show_toastr === 'function') {
                    show_toastr('Success', response.message, 'success');
                }
                
                // Close modal
                $('#change-etc-modal').modal('hide');
                
                // Refresh the DataTable
                if ($.fn.DataTable.isDataTable('#projects-task-table')) {
                    $('#projects-task-table').DataTable().ajax.reload(null, false);
                } else {
                    location.reload();
                }
                
                // Reset form and UI state
                $('#change-etc-form')[0].reset();
                $('#selected-task-ids-etc').val('');
                $('.task-checkbox').prop('checked', false);
                $('#select-all').prop('checked', false);
                $('#delete-btn, #duplicate-btn, #change-assignor-btn, #change-assignee-btn, #change-etc-btn').prop('disabled', true);
            } else {
                if (typeof show_toastr === 'function') {
                    show_toastr('Error', response.message, 'error');
                } else {
                    alert('Error: ' + response.message);
                }
            }
        },
        error: function(xhr, status, error) {
            console.log('ETC AJAX Error:', {xhr: xhr, status: status, error: error});
            console.log('Response Text:', xhr.responseText);
            
            var errorMessage = 'An error occurred while updating ETC.';
            try {
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
            } catch (e) {
                console.log('Error parsing ETC response:', e);
            }
            
            if (typeof show_toastr === 'function') {
                show_toastr('Error', errorMessage, 'error');
            } else {
                alert('Error: ' + errorMessage);
            }
        },
        complete: function() {
            // Reset button state
            $button.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Update ETC');
        }
    });
});

// Reset ETC modal when it's hidden
$('#change-etc-modal').on('hidden.bs.modal', function () {
    $('#change-etc-form')[0].reset();
    $('#selected-task-ids-etc').val('');
    $('#update-etc-btn').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Update ETC');
});

// Date Update functionality
$('#update-date-btn').on('click', function() {
    var selectedTaskIds = $('#selected-task-ids-date').val();
    var startDateInput = $('#start-date-input').val();
    var endDateInput = $('#end-date-input').val();
    var updateDueDateOnly = $('#update-due-date-only').is(':checked');

    console.log('Selected Task IDs for Date:', selectedTaskIds);
    console.log('Start Date Input:', startDateInput);
    console.log('End Date Input:', endDateInput);
    console.log('Update Due Date Only:', updateDueDateOnly);

    if (!selectedTaskIds) {
        alert('Please select at least one task');
        return;
    }

    if (!startDateInput && !endDateInput) {
        alert('Please enter at least one date value');
        return;
    }

    // Format dates for server (convert from YYYY-MM-DD to your preferred format)
    function formatDateForServer(dateString) {
        if (!dateString) return null;
        
        // If your server expects DD-MM-YYYY format
        const parts = dateString.split('-');
        if (parts.length === 3) {
            return `${parts[2]}-${parts[1]}-${parts[0]}`; // DD-MM-YYYY
        }
        return dateString; // Return as-is if not in expected format
    }

    const startDate = formatDateForServer(startDateInput);
    const endDate = formatDateForServer(endDateInput);

    console.log('Formatted Start Date:', startDate);
    console.log('Formatted End Date:', endDate);

    // Show loading state
    var $button = $(this);
    $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Updating...');

    $.ajax({
        url: '{{ route("projecttask.bulkUpdateDate") }}',
        method: 'POST',
        data: {
            task_ids: selectedTaskIds,
            start_date: startDate,
            end_date: endDate,
            update_due_date_only: updateDueDateOnly,
            _token: '{{ csrf_token() }}'
        },
        beforeSend: function() {
            console.log('Sending Date AJAX request...');
        },
        success: function(response) {
            console.log('Date Success Response:', response);
            if (response.is_success) {
                // Show success message
                if (typeof show_toastr === 'function') {
                    show_toastr('Success', response.message, 'success');
                }
                
                // Close modal
                $('#change-date-modal').modal('hide');
                
                // Refresh the DataTable
                if ($.fn.DataTable.isDataTable('#projects-task-table')) {
                    $('#projects-task-table').DataTable().ajax.reload(null, false);
                } else {
                    location.reload();
                }
                
                // Reset form and UI state
                $('#change-date-form')[0].reset();
                $('#selected-task-ids-date').val('');
                $('.task-checkbox').prop('checked', false);
                $('#select-all').prop('checked', false);
                $('#delete-btn, #duplicate-btn, #change-assignor-btn, #change-assignee-btn, #change-etc-btn, #change-date-btn').prop('disabled', true);
            } else {
                if (typeof show_toastr === 'function') {
                    show_toastr('Error', response.message, 'error');
                } else {
                    alert('Error: ' + response.message);
                }
            }
        },
        error: function(xhr, status, error) {
            console.log('Date AJAX Error:', {xhr: xhr, status: status, error: error});
            console.log('Response Text:', xhr.responseText);
            
            var errorMessage = 'An error occurred while updating dates.';
            try {
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
            } catch (e) {
                console.log('Error parsing Date response:', e);
            }
            
            if (typeof show_toastr === 'function') {
                show_toastr('Error', errorMessage, 'error');
            } else {
                alert('Error: ' + errorMessage);
            }
        },
        complete: function() {
            // Reset button state
            $button.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Update Dates');
        }
    });
});

// Reset Date modal when it's hidden
$('#change-date-modal').on('hidden.bs.modal', function () {
    $('#change-date-form')[0].reset();
    $('#selected-task-ids-date').val('');
    $('#update-date-btn').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Update Dates');
});

// Priority Update functionality
$('#update-priority-btn').on('click', function() {
    var selectedTaskIds = $('#selected-task-ids-priority').val();
    var priority = $('#priority-select').val();

    console.log('Selected Task IDs for Priority:', selectedTaskIds);
    console.log('Priority Value:', priority);

    if (!selectedTaskIds || !priority) {
        alert('Please select a priority');
        return;
    }

    // Show loading state
    var $button = $(this);
    $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Updating...');

    $.ajax({
        url: '{{ route("projecttask.bulkUpdatePriority") }}',
        method: 'POST',
        data: {
            task_ids: selectedTaskIds,
            priority: priority,
            _token: '{{ csrf_token() }}'
        },
        beforeSend: function() {
            console.log('Sending Priority AJAX request...');
        },
        success: function(response) {
            console.log('Priority Success Response:', response);
            if (response.is_success) {
                // Show success message
                if (typeof show_toastr === 'function') {
                    show_toastr('Success', response.message, 'success');
                }
                
                // Close modal
                $('#change-priority-modal').modal('hide');
                
                // Refresh the DataTable
                if ($.fn.DataTable.isDataTable('#projects-task-table')) {
                    $('#projects-task-table').DataTable().ajax.reload(null, false);
                } else {
                    location.reload();
                }
                
                // Reset form and UI state
                $('#change-priority-form')[0].reset();
                $('#selected-task-ids-priority').val('');
                $('.task-checkbox').prop('checked', false);
                $('#select-all').prop('checked', false);
                $('#delete-btn, #duplicate-btn, #change-assignor-btn, #change-assignee-btn, #change-etc-btn, #change-date-btn, #change-priority-btn').prop('disabled', true);
            } else {
                if (typeof show_toastr === 'function') {
                    show_toastr('Error', response.message, 'error');
                } else {
                    alert('Error: ' + response.message);
                }
            }
        },
        error: function(xhr, status, error) {
            console.log('Priority AJAX Error:', {xhr: xhr, status: status, error: error});
            console.log('Response Text:', xhr.responseText);
            
            var errorMessage = 'An error occurred while updating priority.';
            try {
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
            } catch (e) {
                console.log('Error parsing Priority response:', e);
            }
            
            if (typeof show_toastr === 'function') {
                show_toastr('Error', errorMessage, 'error');
            } else {
                alert('Error: ' + errorMessage);
            }
        },
        complete: function() {
            // Reset button state
            $button.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Update Priority');
        }
    });
});

// Reset Priority modal when it's hidden
$('#change-priority-modal').on('hidden.bs.modal', function () {
    $('#change-priority-form')[0].reset();
    $('#selected-task-ids-priority').val('');
    $('#update-priority-btn').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Update Priority');
});


});
</script>
<script>
// Handle links popup button click
$(document).on('click', '.links-popup-btn', function() {
    const taskId = $(this).data('task-id');
    const linksContainer = $('#task-links-' + taskId);
    const modalBody = $('#linksModal .modal-body');
    
    // Clear previous content
    modalBody.empty();
    
    // Add each link to the modal
    linksContainer.find('.link-item').each(function() {
        const title = $(this).data('title');
        const url = $(this).data('url');
        const icon = $(this).data('icon');
        const label = $(this).data('label');
        
        const linkHtml = `
            <div class="d-flex align-items-center mb-2 p-2 border-bottom">
                <i class="${icon} me-2" style="width: 20px;"></i>
                <div class="flex-grow-1">
                    <div class="fw-bold">${title}</div>
                    <div class="text-truncate small text-muted" style="max-width: 300px;">${url}</div>
                </div>
                <a href="${url}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                    <i class="fas fa-external-link-alt"></i> Open
                </a>
            </div>
        `;
        modalBody.append(linkHtml);
    });
    
    // Update modal title
    $('#linksModalLabel').text('Links for Task #' + taskId);
});

// Optional: Add click handler to open all links
$(document).on('click', '#openAllLinks', function() {
    $('#linksModal .modal-body a[target="_blank"]').each(function() {
        window.open($(this).attr('href'), '_blank');
    });
});
</script>
<script>
function disabled_start_date()
{
    let isChecked = $("#update-due-date-only").prop("checked");
    if(isChecked == true)
    {
      $("#start_date").hide();
    }
    else{
        $("#start_date").show();
    }
    // alert(isChecked);
    // alert($("#update-due-date-only").val());
}
</script>
    @endpush
@endif
