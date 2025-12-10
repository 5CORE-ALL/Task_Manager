@extends('layouts.main')

@section('page-title')
    {{ __('Task Activity Report') }}
@endsection

@section('page-breadcrumb')
    {{ __('Task Activity Report') }}
@endsection

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-lg-9 col-md-9 col-sm-12">
                        <h5>{{ __('Task Activity Report') }}</h5>
                    </div>
                </div>
            </div>
            
            <!-- Filter Section -->
            <div class="card-body">
                <form method="GET" action="{{ route('task.activity.report') }}" class="mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="activity_type">{{ __('Activity Type') }}</label>
                                <select name="activity_type" class="form-control" id="activity_type">
                                    <option value="">{{ __('All Activities') }}</option>
                                    <option value="create" {{ request('activity_type') == 'create' ? 'selected' : '' }}>{{ __('Create') }}</option>
                                    <option value="edit" {{ request('activity_type') == 'edit' ? 'selected' : '' }}>{{ __('Edit') }}</option>
                                    <option value="delete" {{ request('activity_type') == 'delete' ? 'selected' : '' }}>{{ __('Delete') }}</option>
                                    <option value="restore" {{ request('activity_type') == 'restore' ? 'selected' : '' }}>{{ __('Restore') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date_from">{{ __('From Date') }}</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" id="date_from">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date_to">{{ __('To Date') }}</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" id="date_to">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="task_name">{{ __('Task Name') }}</label>
                                <input type="text" name="task_name" class="form-control" placeholder="Search task name..." value="{{ request('task_name') }}" id="task_name">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                            <a href="{{ route('task.activity.report') }}" class="btn btn-secondary">{{ __('Reset') }}</a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-body table-border-style">
                <div class="table-responsive">
                    <table class="table mb-0 dataTable">
                        <thead>
                            <tr>
                                <th>{{ __('SL No') }}</th>
                                <th>{{ __('Task Name') }}</th>
                                <th>{{ __('Activity Type') }}</th>
                                <th>{{ __('User Name') }}</th>
                                <th>{{ __('User Email') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Time') }}</th>
                                <th>{{ __('IP Address') }}</th>
                                <th>{{ __('Details') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($activities->count() > 0)
                                @foreach($activities as $index => $activity)
                                    <tr>
                                        <td>{{ $activities->firstItem() + $index }}</td>
                                        <td>{{ $activity->task_name }}</td>
                                        <td>
                                            @if($activity->activity_type == 'create')
                                                <span class="badge bg-success">{{ __('Create') }}</span>
                                            @elseif($activity->activity_type == 'edit')
                                                <span class="badge bg-info">{{ __('Edit') }}</span>
                                            @elseif($activity->activity_type == 'delete')
                                                <span class="badge bg-danger">{{ __('Delete') }}</span>
                                            @elseif($activity->activity_type == 'restore')
                                                <span class="badge bg-warning">{{ __('Restore') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $activity->user_name }}</td>
                                        <td>{{ $activity->user_email }}</td>
                                        <td>{{ $activity->activity_date->format('d-m-Y') }}</td>
                                        <td>{{ $activity->activity_date->format('H:i:s') }}</td>
                                        <td>{{ $activity->ip_address }}</td>
                                        <td>{{ $activity->details ?? '-' }}</td>
                                        <td>
                                            @if($activity->activity_type == 'delete')
                                                <button type="button" class="btn btn-sm btn-success restore-btn" 
                                                        data-id="{{ $activity->id }}" 
                                                        data-task-name="{{ $activity->task_name }}">
                                                    {{ __('Restore') }}
                                                </button>
                                            @endif
                                            
                                            <button type="button" class="btn btn-sm btn-danger delete-activity-btn" 
                                                    data-id="{{ $activity->id }}">
                                                {{ __('Delete Record') }}
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="10" class="text-center">{{ __('No activity records found.') }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Pagination Section -->
            @if($activities->hasPages())
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="pagination-info">
                                <span class="text-muted small">
                                    {{ __('Showing') }} {{ $activities->firstItem() }} {{ __('to') }} {{ $activities->lastItem() }} 
                                    {{ __('of') }} {{ $activities->total() }} {{ __('results') }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end">
                                {{ $activities->appends(request()->query())->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Restore Confirmation Modal -->
<div class="modal fade" id="restoreModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Confirm Restore') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>{{ __('Are you sure you want to restore this task?') }}</p>
                <p><strong>{{ __('Task:') }}</strong> <span id="restore-task-name"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-success" id="confirm-restore">{{ __('Restore') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Confirm Delete') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>{{ __('Are you sure you want to delete this activity record?') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-danger" id="confirm-delete">{{ __('Delete') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
.card-footer {
    background-color: #f8f9fa;
    border-top: 1px solid #dee2e6;
    padding: 15px 20px;
}

.pagination-info {
    margin: 0;
    line-height: 2.5;
}

.pagination {
    margin: 0;
    justify-content: flex-end;
}

.page-link {
    color: #007bff;
    background-color: #fff;
    border: 1px solid #dee2e6;
    padding: 0.5rem 0.75rem;
    margin-left: -1px;
    line-height: 1.25;
    text-decoration: none;
}

.page-item.active .page-link {
    z-index: 3;
    color: #fff;
    background-color: #007bff;
    border-color: #007bff;
}

.page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    cursor: auto;
    background-color: #fff;
    border-color: #dee2e6;
}

.page-link:hover {
    color: #0056b3;
    text-decoration: none;
    background-color: #e9ecef;
    border-color: #dee2e6;
}

.page-link:focus {
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

/* Responsive pagination */
@media (max-width: 768px) {
    .card-footer .row {
        text-align: center;
    }
    
    .card-footer .col-md-6:first-child {
        margin-bottom: 10px;
    }
    
    .pagination {
        justify-content: center;
    }
}
</style>
<script>
$(document).ready(function() {
    // Restore functionality
    $('.restore-btn').on('click', function() {
        const activityId = $(this).data('id');
        const taskName = $(this).data('task-name');
        
        $('#restore-task-name').text(taskName);
        $('#restoreModal').modal('show');
        
        $('#confirm-restore').off('click').on('click', function() {
            $.ajax({
                url: '{{ url("task-activity-report") }}/' + activityId + '/restore',
                type: 'POST',
                data: {
                    '_token': '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#restoreModal').modal('hide');
                    location.reload();
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseJSON.error);
                }
            });
        });
    });
    
    // Delete functionality
    $('.delete-activity-btn').on('click', function() {
        const activityId = $(this).data('id');
        
        $('#deleteModal').modal('show');
        
        $('#confirm-delete').off('click').on('click', function() {
            $.ajax({
                url: '{{ url("task-activity-report") }}/' + activityId,
                type: 'DELETE',
                data: {
                    '_token': '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#deleteModal').modal('hide');
                    location.reload();
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseJSON.error);
                }
            });
        });
    });
});
</script>
@endpush
