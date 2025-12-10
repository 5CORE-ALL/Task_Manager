@extends('layouts.main')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('page-title')
    {{ __('Done Clear Tasks') }}
@endsection
@section('page-breadcrumb')
    {{ __('Done Clear') }}
@endsection

@section('page-action')
    <div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#doneClearModal">
            <i class="ti ti-plus"></i>
            {{ __('Clear Done Task') }}
        </button>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>{{ __('Done Clear Tasks') }}</h5>
                    <small class="text-muted">{{ __('Total: ') . count($doneClears) }}</small>
                </div>
                <div class="card-body">
                    @if($doneClears->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped" id="doneClearTable">
                                <thead>
                                    <tr>
                                        <th>{{ __('#ID') }}</th>
                                        <th>{{ __('Assignor') }}</th>
                                        <th>{{ __('Assignee') }}</th>
                                        <th>{{ __('Description') }}</th>
                                        <th>{{ __('Priority') }}</th>
                                        <th>{{ __('Created Date') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="doneClearTableBody">
                                    @foreach($doneClears as $index => $doneClear)
                                        <tr data-id="{{ $doneClear->id }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $doneClear->assignor_name }}</td>
                                            <td>{{ $doneClear->assignee_name }}</td>
                                            <td>{{ $doneClear->description }}</td>
                                            <td>
                                                <span class="badge bg-{{ $doneClear->priority === 'high' ? 'danger' : ($doneClear->priority === 'medium' ? 'warning' : 'success') }}">
                                                    {{ ucfirst($doneClear->priority) }}
                                                </span>
                                            </td>
                                            <td>{{ $doneClear->created_at->format('d M Y H:i') }}</td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm delete-task" data-id="{{ $doneClear->id }}">
                                                    <i class="ti ti-trash"></i>
                                                    {{ __('Delete') }}
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="ti ti-folder-x" style="font-size: 48px; color: #6c757d;"></i>
                            </div>
                            <h5 class="text-muted">{{ __('No Done Clear tasks found') }}</h5>
                            <p class="text-muted">{{ __('Create your first Done Clear task using the button above.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Simple Modal -->
    <div class="modal fade" id="doneClearModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Clear Done Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="simpleForm">
                    <div class="modal-body">
                        @csrf
                        
                        <!-- Assignor (readonly) -->
                        <div class="mb-3">
                            <label class="form-label">Assignor Name (Current User)</label>
                            <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
                        </div>

                        <!-- Assignee -->
                        <div class="mb-3">
                            <label class="form-label">Assignee Name *</label>
                            <select name="assignee_id" id="assignee_id" class="form-control" required>
                                <option value="">Select Assignee</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Priority -->
                        <div class="mb-3">
                            <label class="form-label">Priority</label>
                            <select name="priority" id="priority" class="form-control">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    @if($doneClears->count() > 0)
    $('#doneClearTable').DataTable();
    @endif

    // Reset form when modal opens
    $('#doneClearModal').on('show.bs.modal', function() {
        // Reset form completely
        $('#simpleForm')[0].reset();
        $('#assignee_id').val('');
        $('#priority').val('medium');
        
        console.log('Modal opened - form reset');
    });

    // Simple form submission
    $('#simpleForm').on('submit', function(e) {
        e.preventDefault();
        
        // Prevent double submission
        let submitBtn = $(this).find('button[type="submit"]');
        if (submitBtn.prop('disabled')) {
            console.log('Form already submitting, prevented double submission');
            return;
        }
        
        // Get values directly from DOM elements - more reliable
        let assigneeId = document.getElementById('assignee_id').value;
        let description = "check clear my done task"; // Predefined message
        let priority = document.getElementById('priority').value;
        
        let formData = {
            assignor_id: {{ Auth::user()->id }},
            assignee_id: assigneeId,
            description: description,
            priority: priority,
            _token: '{{ csrf_token() }}'
        };
        
        // Basic validation with debug info
        console.log('Form submission attempt:', {assigneeId, description, priority});
        
        if (!assigneeId || assigneeId === '') {
            alert('Please select an assignee');
            return;
        }
        
        // Disable submit button to prevent double submission
        submitBtn.prop('disabled', true).text('Creating...');
        
        // Get fresh CSRF token for each request
        let csrfToken = $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}';
        
        console.log('Using CSRF token:', csrfToken);
        
        // Submit with fresh data each time
        $.ajax({
            url: '{{ route("done-clear.store") }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: {
                assignor_id: {{ Auth::user()->id }},
                assignee_id: assigneeId,
                description: description,
                priority: priority,
                _token: csrfToken,
                timestamp: Date.now() // Add timestamp to make each request unique
            },
            success: function(response) {
                console.log('Server response:', response);
                
                // Always re-enable button first
                submitBtn.prop('disabled', false).text('Create Task');
                
                if (response.success) {
                    alert('✅ Task created successfully!');
                    $('#doneClearModal').modal('hide');
                    
                    // Clear form completely
                    $('#simpleForm')[0].reset();
                    
                    // Force reload to get fresh data and CSRF token
                    window.location.href = window.location.href + '?refresh=' + Date.now();
                } else {
                    alert('❌ Error: ' + response.message);
                }
            },
            error: function(xhr) {
                console.error('AJAX Error:', xhr);
                
                // Always re-enable button
                submitBtn.prop('disabled', false).text('Create Task');
                
                let errorMsg = 'Failed to create task. Please try again.';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.responseJSON.errors) {
                        errorMsg = Object.values(xhr.responseJSON.errors).join(', ');
                    }
                }
                
                console.error('Error details:', errorMsg);
                alert('❌ Error: ' + errorMsg);
            }
        });
    });

    // Clean up when modal is hidden
    $('#doneClearModal').on('hidden.bs.modal', function() {
        // Reset form completely
        $('#simpleForm')[0].reset();
        $('#assignee_id').val('');
        $('#priority').val('medium');
        
        // Re-enable submit button in case it was disabled
        $('#simpleForm button[type="submit"]').prop('disabled', false).text('Create Task');
        
        console.log('Modal closed - form cleaned up');
    });

    // Delete task
    $(document).on('click', '.delete-task', function() {
        let taskId = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this task?')) {
            $.ajax({
                url: "{{ route('done-clear.destroy', '') }}/" + taskId,
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Failed to delete task');
                    }
                },
                error: function() {
                    alert('Failed to delete task');
                }
            });
        }
    });
});
</script>
@endpush
