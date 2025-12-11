<style>
  .bg-purple {
    background-color: #9b59b6 !important;
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
    {{ __('Missed Task') }}
@endsection
@section('title')
    {{ __('Missed Task') }}
@endsection
@section('page-breadcrumb')
    {{ __('Project') }},{{ __('Project Details') }},{{ __('Missed Task') }}
@endsection
@push('css')
    @include('layouts.includes.datatable-css')
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
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

    /* Toggle Filter Styles */
    .toggle-filter {
      transition: all 0.3s ease;
    }
    .toggle-filter.active {
      background-color: var(--bs-primary) !important;
      color: white !important;
      border-color: var(--bs-primary) !important;
    }
    .toggle-filter:hover {
      transform: translateY(-2px);
    }
  </style>
@endpush
@section('page-action')
    <div class="d-flex">
        @stack('addButtonHook')

        @permission('task create')
            <a class="btn btn-sm btn-primary me-2 add-task" data-ajax-popup="true" data-size="lg" data-title="{{ __('Create New Task') }}"
                data-url="{{ route('tasks.create') }}" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Create') }}"><i
                    class="ti ti-plus"></i></a>
                    
            <a class="btn btn-sm btn-primary me-2" href="{{ route('reviews.index') }}" target="_blank" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Reviews') }}">
                <i class="ti ti-star"></i>
            </a>        
             <a class="btn btn-sm btn-primary me-2" data-ajax-popup="true" data-size="lg" data-title="{{ __('Import Task') }}"
                data-url="{{ route('tasks.import') }}" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Import') }}"><i
                    class="fa fa-upload"></i></a>
                  
        @endpermission

    </div>
@endsection
@section('filter')
@endsection

@section('content')
   
    <div class="row">
        <div class="col-xl-12">
            <div class="container mt-1">
                <div class="row mt-1 align-items-center">
                   
                 <div class="col-md-1 mb-3">
                     <!-- Toggle Filter Buttons -->
                     <div class="btn-group" role="group" aria-label="Task Filter" style="margin-bottom: 20px;">
                         <button type="button" class="btn btn-sm btn-outline-primary toggle-filter active" data-filter="all">
                             {{ __('All') }}
                         </button>
                         <button type="button" class="btn btn-sm btn-outline-danger toggle-filter" data-filter="overdue">
                             {{ __('Overdue') }}
                         </button>
                         <button type="button" class="btn btn-sm btn-outline-warning toggle-filter" data-filter="urgent">
                             {{ __('Urgent') }}
                         </button>
                     </div>
                     </div>
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
                                    <option value="">{{__('Status')}}</option>
                                    @foreach($stages as $stage)
                                        <option value="{{$stage->name}}" data-color="{{ $stage->color }}">  {{$stage->name}}
                                            </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-2">
                                 <label class="form-label">{{ __('Priority')}}</label>
                                <select class="form-control form-control-light" name="priority" id="priority">
                                    <option value="">{{ __('All Priority')}}</option>
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
                    <div class="table-responsive overflow_hidden">
                        {{ $dataTable->table(['width' => '100%']) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    
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
    
    <!-- ETC Modal -->
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
                if (isAddEnable) {
                    $('.add-task').trigger('click');
                }
                getTaskCount();
                loadRatingData();
                loadTeamloggerData();
                
                // Bind filter change events
                $('#assignor_name, #assignee_name, #status_name, #group_name, #task_name,#priority').on('change keyup', function() {
                    updateFilteredData();
                });
            });

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
                    console.log('Fetching Teamlogger data via backend...');
                    
                    // Get current filter values
                    const assigneeEmails = $('#assignee_name').val() || [];
                    const assignorEmails = $('#assignor_name').val() || [];
                    
                    // Show loading state
                    $('#teamlogger-hours').text('...');
                    
                    console.log('Sending request with filters:', {
                        assignee_emails: assigneeEmails,
                        assignor_emails: assignorEmails
                    });
                    
                    $.ajax({
                        url: '{{ route("projecttask.teamlogger.data") }}',
                        method: 'GET',
                        dataType: 'json',
                        data: {
                            assignee_emails: assigneeEmails,
                            assignor_emails: assignorEmails
                        },
                        success: function(response) {
                            console.log('Teamlogger backend response:', response);
                            if (response.success) {
                                $('#teamlogger-hours').text(response.totalHours || '0');
                                console.log('Updated teamlogger hours to:', response.totalHours);
                                console.log('Target emails:', response.targetEmails);
                                console.log('Found emails:', response.foundEmails);
                            } else {
                                console.error('Backend error:', response.message);
                                $('#teamlogger-hours').text('0');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching Teamlogger data from backend:', error);
                            console.error('Response:', xhr.responseText);
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
                                element.text(value); // Update the text
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
                            d.group_name = $('#group_name').val();
                            d.task_name = $('#task_name').val();
                            d.priority = $('#priority').val();
                            d.toggle_filter = $('.toggle-filter.active').data('filter') || 'all';
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
                            d.group_name = $('#group_name').val();
                            d.task_name = $('#task_name').val();
                             d.priority = $('#priority').val();
                            d.toggle_filter = $('.toggle-filter.active').data('filter') || 'all';
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
                            d.group_name = $('#group_name').val();
                            d.task_name = $('#task_name').val();
                             d.priority = $('#priority').val();
                            d.toggle_filter = $('.toggle-filter.active').data('filter') || 'all';
                            return d;
                        };
                        table.ajax.reload();
                    }
                });
                $('#projects-task-table_filter input').on('keyup', function () {
                    console.log("input",$('.dataTables_filter input').val());
                    getTaskCount();
                });
                
                // Toggle filter button click handler
                $('.toggle-filter').on('click', function() {
                    // Remove active class from all buttons
                    $('.toggle-filter').removeClass('active');
                    // Add active class to clicked button
                    $(this).addClass('active');
                    
                    // Get filter value
                    var filterValue = $(this).data('filter');
                    
                    // Reload DataTable with toggle filter
                    if ($.fn.DataTable.isDataTable('#projects-task-table')) {
                        var table = $('#projects-task-table').DataTable();
                        table.settings()[0].ajax.data = function(d) {
                            d.assignee_name = $('#assignee_name').val();
                            d.assignor_name = $('#assignor_name').val();
                            d.status_name = $('#status_name').val();
                            d.group_name = $('#group_name').val();
                            d.task_name = $('#task_name').val();
                            d.priority = $('#priority').val();
                            d.toggle_filter = filterValue;
                            return d;
                        };
                        table.ajax.reload();
                        getTaskCount();
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
                              group_name: $('#group_name').val(),
                              task_name: $('#task_name').val(),
                                priority: $('#priority').val(),
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
                    
                    // Fetch urgent ETC data
                    getUrgentETCData();
                }
                
                // Function to fetch done task data
                function getDoneTaskData() {
                    // Get assignee filter value, but if empty, use current user's email for ETC/ATC cards
                    var assigneeFilter = $('#assignee_name').val();
                    var currentUserEmail = '{{ Auth::user()->email }}';
                    
                    // For ETC/ATC cards, always show current user's data if no specific assignee is selected
                    var assigneeForDoneData = (assigneeFilter && assigneeFilter.length > 0) ? assigneeFilter : [currentUserEmail];
                    
                    $.ajax({
                        url: "{{ route('projecttask.done.count') }}",
                        type: 'get',
                        data: {
                            assignee_name: assigneeForDoneData,
                            assignor_name: $('#assignor_name').val() || [],
                            month: new Date().getMonth() + 1, // Current month (1-12)
                            group_name: $('#group_name').val(),
                            task_name: $('#task_name').val()
                        },
                        dataType: 'JSON',
                        success: function(data) {
                            console.log('Done Task Data:', data);
                            if(data.is_success) {
                                // Update ETC and ATC cards with filtered data
                                $("#filtered-etc-count").html(data.data.total_eta || 0);
                                $("#filtered-atc-count").html(data.data.total_atc || 0);
                            } else {
                                $("#filtered-etc-count").html(0);
                                $("#filtered-atc-count").html(0);
                            }
                        },
                        error: function(data) {
                            console.error('Error fetching done task data:', data);
                            $("#filtered-etc-count").html(0);
                            $("#filtered-atc-count").html(0);
                        }
                    });
                }
                
                // Function to fetch urgent ETC data
                function getUrgentETCData() {
                    $.ajax({
                        url: "{{ route('projecttask.urgent.etc') }}",
                        type: 'get',
                        data: {
                            assignee_name: $('#assignee_name').val(),
                            assignor_name: $('#assignor_name').val(),
                            status_name: $('#status_name').val(),
                            group_name: $('#group_name').val(),
                            task_name: $('#task_name').val()
                        },
                        dataType: 'JSON',
                        success: function(data) {
                            console.log('Urgent ETC Data:', data);
                            $("#urgent-etc-count").html(data.urgent_etc_hours || 0);
                        },
                        error: function(data) {
                            console.error('Error fetching urgent ETC data:', data);
                            $("#urgent-etc-count").html(0);
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

// Function to update filtered card states (ETC and ATC cards are always enabled)
function updateFilteredCardStates() {
    // ETC and ATC cards are always enabled and show user's own data
    // No need to disable them since users should always see their own current month data
}

// Initialize card states on page load
$(document).ready(function() {
    updateGraphCardState();
    updateFilteredCardStates();
    updateTodayGraphButtonState();
    
    // Load current user's own ETC/ATC data for current month on page load
    getDoneTaskData();
    
    // Load urgent ETC data on page load
    getUrgentETCData();
    
    // Update card states when filters change
    $('#assignee_name, #assignor_name, #status_name, #group_name, #task_name').on('change keyup', function() {
        updateGraphCardState();
        updateFilteredCardStates();
        updateTodayGraphButtonState();
        
        // Always fetch done task data when filters change (will show user's own data if no assignee selected)
        getDoneTaskData();
        
        // Always fetch urgent ETC data when filters change
        getUrgentETCData();
    });

    // Change Assignor Modal functionality
    $('#update-assignor-btn').on('click', function() {
        var selectedTaskIds = $('#selected-task-ids').val();
        var assignorEmail = $('#assignor-select').val();

        console.log('Selected Task IDs:', selectedTaskIds);
        console.log('Assignor Email:', assignorEmail);
        
        // Test the route URL
        console.log('Route URL:', '{{ route("projecttask.bulkUpdateAssignor") }}');

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
            beforeSend: function() {
                console.log('Sending AJAX request...');
            },
            success: function(response) {
                console.log('Success Response:', response);
                if (response.is_success) {
                    // Show success message using the existing notification system
                    if (typeof show_toastr === 'function') {
                        show_toastr('Success', response.message, 'success');
                    } else {
                        alert('Success: ' + response.message);
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
                console.log('AJAX Error:', {xhr: xhr, status: status, error: error});
                console.log('Response Text:', xhr.responseText);
                console.log('Status Code:', xhr.status);
                
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
                    console.log('Error parsing response:', e);
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

    // Change Assignee Modal functionality
    $('#update-assignee-btn').on('click', function() {
        var selectedTaskIds = $('#selected-task-ids-assignee').val();
        var assigneeEmail = $('#assignee-select').val();

        console.log('Selected Task IDs for assignee:', selectedTaskIds);
        console.log('Assignee Email:', assigneeEmail);
        
        // Test the route URL
        console.log('Route URL:', '{{ route("projecttask.bulkUpdateAssignee") }}');

        if (!selectedTaskIds || !assigneeEmail) {
            alert('Please select an assignee');
            return;
        }

        // Show loading state
        var $button = $(this);
        $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Updating...');

        $.ajax({
            url: '{{ route("projecttask.bulkUpdateAssignee") }}',
            method: 'POST',
            data: {
                task_ids: selectedTaskIds,
                assignee_email: assigneeEmail,
                _token: '{{ csrf_token() }}'
            },
            beforeSend: function() {
                console.log('Sending assignee AJAX request...');
            },
            success: function(response) {
                console.log('Assignee Success Response:', response);
                if (response.is_success) {
                    // Show success message using the existing notification system
                    if (typeof show_toastr === 'function') {
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
                } else {
                    if (typeof show_toastr === 'function') {
                        show_toastr('Error', response.message, 'error');
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.log('Assignee AJAX Error:', {xhr: xhr, status: status, error: error});
                console.log('Response Text:', xhr.responseText);
                console.log('Status Code:', xhr.status);
                
                var errorMessage = 'An error occurred while updating assignee.';
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
                    console.log('Error parsing assignee response:', e);
                }
                
                if (typeof show_toastr === 'function') {
                    show_toastr('Error', errorMessage, 'error');
                } else {
                    alert('Error: ' + errorMessage);
                }
            },
            complete: function() {
                // Reset button state
                $button.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Update Assignee');
            }
        });
    });

    // Reset assignee modal when it's hidden
    $('#change-assignee-modal').on('hidden.bs.modal', function () {
        $('#change-assignee-form')[0].reset();
        $('#selected-task-ids-assignee').val('');
        $('#update-assignee-btn').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Update Assignee');
    });

});
</script>
    @endpush
@endif
