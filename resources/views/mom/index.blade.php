@extends('layouts.main')
@section('page-title')
    {{ __('MOM List') }}
@endsection
@section('title')
    {{ __('MOM List') }}
@endsection
@section('page-breadcrumb')
    {{ __('MOM') }},{{ __('Minutes of Meeting') }},{{ __('MOM List') }}
@endsection
@push('css')
    @include('layouts.includes.datatable-css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        .table-container {
            margin-bottom: 2rem;
        }
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
        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
        }
        .table-responsive {
            border-radius: 0 0 10px 10px;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            border-top: none;
            padding: 1rem 0.75rem;
        }
        .table td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
        }
        .btn-group .btn {
            margin: 0 2px;
        }
        .badge {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
        }
        .modal-header {
            border-bottom: 1px solid #dee2e6;
        }
        .modal-footer {
            border-top: 1px solid #dee2e6;
        }
        .search-section {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
    </style>
@endpush
@section('page-action')
    <div class="d-flex">
        <!--<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createMomModal">-->
        <!--    <i class="bi bi-plus-circle"></i> Create MOM-->
        <!--</button>-->
        @stack('addButtonHook')
    </div>
@endsection
@section('filter')
@endsection
@php
    $currentUser = Auth::user();
    $currentUserEmail = $currentUser ? strtolower($currentUser->email) : '';
@endphp

@section('content')
<script>
    window.currentUserEmail = @json($currentUserEmail);
</script>
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Minutes of Meeting (MOM)</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createMomModal">
                    <i class="bi bi-plus-circle"></i> Create MOM
                </button>
            </div>
            <div class="btn-group me-2">
                <button type="button" class="btn btn-outline-secondary">
                    <i class="bi bi-download"></i> Export
                </button>
                <button type="button" class="btn btn-outline-secondary">
                    <i class="bi bi-printer"></i> Print
                </button>
            </div>
        </div>
    </div>

    <!-- MOMs Table -->
    <div class="table-container">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-calendar-event"></i> Minutes of Meeting
                </h5>
                <!-- Search Bars -->
                <div class="row mt-3 mb-2">
                    <div class="col-md-3">
                        <label for="searchMeetingName" class="form-label"><i class="bi bi-calendar-event"></i> Meeting Name</label>
                        <input type="text" id="searchMeetingName" class="form-control" placeholder="Search by Meeting Name">
                    </div>
                    <div class="col-md-3">
                        <label for="searchHost" class="form-label"><i class="bi bi-person"></i> Host Name</label>
                        <input type="text" id="searchHost" class="form-control" placeholder="Search by Host Name">
                    </div>
                    <div class="col-md-3">
                        <label for="searchLocation" class="form-label"><i class="bi bi-geo-alt"></i> Location</label>
                        <input type="text" id="searchLocation" class="form-control" placeholder="Search by Location">
                    </div>
                    <div class="col-md-3">
                        <label for="searchDate" class="form-label"><i class="bi bi-calendar"></i> Date</label>
                        <input type="date" id="searchDate" class="form-control">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="momsTable">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">Meeting Name</th>
                                <th scope="col">Date</th>
                                <th scope="col">Location</th>
                                <th scope="col">Host Name</th>
                                <th scope="col">Assignees</th>
                                <th scope="col">Agenda</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($moms as $mom)
                            <tr data-id="{{ $mom->id }}">
                                <td class="meeting_name">{{ $mom->meeting_name }}</td>
                                <td class="meeting_date">{{ \Carbon\Carbon::parse($mom->meeting_date)->format('d M Y') }}</td>
                                <td class="location">{{ $mom->location }}</td>
                                <td class="host_name">{{ $mom->host_name }}</td>
                                <td class="assignees">
                                    @if($mom->assignees)
                                        @php
                                            $assigneeList = explode(',', $mom->assignees);
                                            $displayList = array_slice($assigneeList, 0, 2);
                                            $remainingCount = count($assigneeList) - 2;
                                        @endphp
                                        {{ implode(', ', $displayList) }}
                                        @if($remainingCount > 0)
                                            <span class="badge bg-secondary">+{{ $remainingCount }} more</span>
                                        @endif
                                    @else
                                        <span class="text-muted">No assignees</span>
                                    @endif
                                </td>
                                <td class="agenda">
                                    <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ $mom->agenda }}
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-info view-mom-btn" data-id="{{ $mom->id }}">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        @if($mom->host_email === $currentUserEmail)
                                        <button type="button" class="btn btn-danger delete-mom-btn" data-id="{{ $mom->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<!-- View MOM Modal -->
<div class="modal fade" id="viewMomModal" tabindex="-1" aria-labelledby="viewMomModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="viewMomModalLabel">
                    <i class="bi bi-eye"></i> View Minutes of Meeting
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Meeting Name:</strong>
                        <p id="viewMeetingName"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Date:</strong>
                        <p id="viewMeetingDate"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Location:</strong>
                        <p id="viewLocation"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Host:</strong>
                        <p id="viewHost"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <strong>Assignees:</strong>
                        <p id="viewAssignees"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <strong>Agenda:</strong>
                        <div id="viewAgenda" style="white-space: pre-wrap; background-color: #f8f9fa; padding: 15px; border-radius: 5px;"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Create MOM Modal -->
<div class="modal fade" id="createMomModal" tabindex="-1" aria-labelledby="createMomModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="createMomModalLabel">
                    <i class="bi bi-plus-circle"></i> Create Minutes of Meeting
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createMomForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="meetingName" class="form-label">Meeting Name *</label>
                                <input type="text" class="form-control" id="meetingName" name="meeting_name" placeholder="Enter meeting name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="meetingDate" class="form-label">Date *</label>
                                <input type="date" class="form-control" id="meetingDate" name="meeting_date" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="location" class="form-label">Location *</label>
                        <input type="text" class="form-control" id="location" name="location" placeholder="Enter meeting location" required>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="hostName" class="form-label">Host Name *</label>
                                <select class="form-select" id="hostName" name="host_name" required>
                                    <option value="">Select Host</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->name }}" {{ Auth::user()->name == $employee->name ? 'selected' : '' }}>
                                            {{ $employee->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="assignees" class="form-label">Assignees (Multiple)</label>
                                <select class="form-select" id="assignees" name="assignees[]" multiple>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->name }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple</small>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <label for="agenda" class="form-label">Agenda *</label>
                        <textarea class="form-control" id="agenda" name="agenda" rows="5" placeholder="Enter the meeting agenda" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" id="saveMomBtn">
                    <i class="bi bi-check-circle"></i> Submit
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Set today's date as default when modal opens
$('#createMomModal').on('shown.bs.modal', function () {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('meetingDate').value = today;
});

// Reset modal on close
$('#createMomModal').on('hidden.bs.modal', function () {
    $('#createMomForm')[0].reset();
});

// Save MOM from modal
document.getElementById('saveMomBtn').addEventListener('click', function() {
    const form = document.getElementById('createMomForm');
    const formData = new FormData(form);
    
    const saveMomBtn = document.getElementById('saveMomBtn');
    const originalText = saveMomBtn.innerHTML;
    
    // Disable submit button
    saveMomBtn.disabled = true;
    saveMomBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Creating...';
    
    fetch('{{ route('mom.store') }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => Promise.reject(err));
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('MOM created successfully!');
            $('#createMomModal').modal('hide');
            location.reload();
        } else {
            alert('Error creating MOM: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        let errorMessage = 'Error creating MOM. Please try again.';
        if (error.message) {
            errorMessage = 'Error: ' + error.message;
        } else if (error.errors) {
            errorMessage = 'Validation errors: ' + Object.values(error.errors).flat().join(', ');
        }
        alert(errorMessage);
    })
    .finally(() => {
        // Re-enable submit button
        saveMomBtn.disabled = false;
        saveMomBtn.innerHTML = originalText;
    });
});

// Search/Filter logic
function filterTable() {
    const meetingName = document.getElementById('searchMeetingName').value.toLowerCase();
    const host = document.getElementById('searchHost').value.toLowerCase();
    const location = document.getElementById('searchLocation').value.toLowerCase();
    const date = document.getElementById('searchDate').value;
    const rows = document.querySelectorAll('#momsTable tbody tr');
    
    rows.forEach(row => {
        const meetingNameText = row.querySelector('.meeting_name').textContent.toLowerCase();
        const hostText = row.querySelector('.host_name').textContent.toLowerCase();
        const locationText = row.querySelector('.location').textContent.toLowerCase();
        const dateText = row.querySelector('.meeting_date').textContent;
        
        let show = true;
        
        if (meetingName && !meetingNameText.includes(meetingName)) show = false;
        if (host && !hostText.includes(host)) show = false;
        if (location && !locationText.includes(location)) show = false;
        if (date) {
            const searchDate = new Date(date);
            const rowDate = new Date(dateText);
            if (searchDate.toDateString() !== rowDate.toDateString()) show = false;
        }
        
        row.style.display = show ? '' : 'none';
    });
}

document.getElementById('searchMeetingName').addEventListener('input', filterTable);
document.getElementById('searchHost').addEventListener('input', filterTable);
document.getElementById('searchLocation').addEventListener('input', filterTable);
document.getElementById('searchDate').addEventListener('change', filterTable);

// View MOM details
$(document).on('click', '.view-mom-btn', function() {
    const momId = $(this).data('id');
    
    fetch(`/mom/${momId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }
            
            $('#viewMeetingName').text(data.meeting_name);
            $('#viewMeetingDate').text(new Date(data.meeting_date).toLocaleDateString());
            $('#viewLocation').text(data.location);
            $('#viewHost').text(data.host_name);
            $('#viewAssignees').text(data.assignees || 'No assignees');
            $('#viewAgenda').text(data.agenda);
            
            $('#viewMomModal').modal('show');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading MOM details');
        });
});

// Delete MOM
$(document).on('click', '.delete-mom-btn', function() {
    const momId = $(this).data('id');
    
    if (confirm('Are you sure you want to delete this MOM?')) {
        fetch(`/mom/${momId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.error || 'Error deleting MOM');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting MOM');
        });
    }
});
</script>
@endpush

@endsection
