@extends('layouts.main')
@section('page-title')
    {{ __('Create MOM') }}
@endsection
@section('title')
    {{ __('Create MOM') }}
@endsection
@section('page-breadcrumb')
    {{ __('MOM') }},{{ __('Minutes of Meeting') }},{{ __('Create') }}
@endsection
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .card {
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom: none;
            padding: 1.5rem;
        }
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #dee2e6;
            padding: 0.75rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 8px;
        }
        .btn-secondary {
            padding: 0.75rem 2rem;
            border-radius: 8px;
        }
        .section-divider {
            border-top: 2px solid #dee2e6;
            margin: 2rem 0;
            position: relative;
        }
        .section-divider::before {
            content: '';
            position: absolute;
            top: -1px;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 2px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .select2-container--default .select2-selection--multiple {
            border-radius: 8px;
            border: 1px solid #dee2e6;
            min-height: 48px;
        }
        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
    </style>
@endpush

@section('page-action')
    <div class="d-flex">
        <a href="{{ route('mom.index') }}" class="btn btn-secondary me-2">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
        @stack('addButtonHook')
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="bi bi-calendar-event"></i> Create Minutes of Meeting (MOM)
                    </h4>
                </div>
                <div class="card-body p-4">
                    <form id="createMomForm">
                        @csrf
                        
                        <!-- Meeting Details Section -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="meetingName" class="form-label">
                                        <i class="bi bi-calendar-event"></i> Meeting Name *
                                    </label>
                                    <input type="text" class="form-control" id="meetingName" name="meeting_name" placeholder="Enter meeting name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="meetingDate" class="form-label">
                                        <i class="bi bi-calendar"></i> Date *
                                    </label>
                                    <input type="date" class="form-control" id="meetingDate" name="meeting_date" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-4">
                                    <label for="location" class="form-label">
                                        <i class="bi bi-geo-alt"></i> Mode of Meeting *
                                    </label>
                                    <input type="text" class="form-control" id="location" name="location" placeholder="Enter meeting location" required>
                                </div>
                            </div>
                        </div>

                        <div class="section-divider"></div>

                        <!-- Participants Section -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="hostName" class="form-label">
                                        <i class="bi bi-person"></i> Host Name *
                                    </label>
                                    <select class="form-select" id="hostName" name="host_name" required>
                                        <option value="">Select Host</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->name }}" {{ Auth::user()->name == $employee->name ? 'selected' : '' }}>
                                                {{ $employee->name }} ({{ $employee->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="assignees" class="form-label">
                                        <i class="bi bi-people"></i> Assignees (Multiple Selection)
                                    </label>
                                    <select class="form-select" id="assignees" name="assignees[]" multiple>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->name }}">{{ $employee->name }} ({{ $employee->email }})</option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple assignees</small>
                                </div>
                            </div>
                        </div>

                        <div class="section-divider"></div>

                        <!-- Agenda Section -->
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-4">
                                    <label for="agenda" class="form-label">
                                        <i class="bi bi-list-ul"></i> Agenda *
                                    </label>
                                    <textarea class="form-control" id="agenda" name="agenda" rows="8" placeholder="Enter the meeting agenda, discussion points, decisions made, action items, etc." required></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Section -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-3">
                                    <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('mom.index') }}'">
                                        <i class="bi bi-x-circle"></i> Cancel
                                    </button>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="bi bi-check-circle"></i> Submit MOM
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2 for assignees
    $('#assignees').select2({
        placeholder: "Select assignees",
        allowClear: true,
        width: '100%'
    });
    
    // Set today's date as default
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('meetingDate').value = today;
});

// Form submission
document.getElementById('createMomForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    
    // Disable submit button
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Creating...';
    
    const formData = new FormData(this);
    
    fetch('{{ route('mom.store') }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('MOM created successfully!');
            window.location.href = '{{ route('mom.index') }}';
        } else {
            alert('Error creating MOM: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error creating MOM. Please try again.');
    })
    .finally(() => {
        // Re-enable submit button
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

// Form validation
function validateForm() {
    const requiredFields = ['meetingName', 'meetingDate', 'location', 'hostName', 'agenda'];
    let isValid = true;
    
    requiredFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    return isValid;
}

// Real-time validation
document.addEventListener('DOMContentLoaded', function() {
    const requiredFields = ['meetingName', 'meetingDate', 'location', 'hostName', 'agenda'];
    
    requiredFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        field.addEventListener('blur', function() {
            if (!this.value.trim()) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
        
        field.addEventListener('input', function() {
            if (this.value.trim()) {
                this.classList.remove('is-invalid');
            }
        });
    });
});
</script>
@endpush

@endsection
