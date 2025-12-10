@extends('layouts.main')
@section('page-title')
    {{ __('Daily Shipping Checklist Records') }}
@endsection
@section('title')
    {{ __('Daily Shipping Checklist Records') }}
@endsection
@section('page-breadcrumb')
    {{ __('Operations') }},{{ __('Daily Shipping') }},{{ __('Checklist Records') }}
@endsection
@push('css')
    @include('layouts.includes.datatable-css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        .table-container {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-bottom: 20px;
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 20px;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .status-yes {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status-no {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .btn-delete {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        
        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
            color: white;
        }
        
        .search-container {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .table th {
            background: #495057;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.875rem;
            letter-spacing: 0.5px;
        }
        
        .table td {
            vertical-align: middle;
            padding: 12px;
        }
        
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(102, 126, 234, 0.05);
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.1);
        }
        
        .comments-cell {
            max-width: 200px;
            word-wrap: break-word;
            white-space: normal;
        }
    </style>
@endpush

@section('page-action')
    <div class="d-flex">
        <!--<a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#dailyShippingChecklistModal">-->
        <!--    <i class="ti ti-plus"></i> {{ __('Add New Checklist') }}-->
        <!--</a>-->
    </div>
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
    <h1 class="h2">
        <i class="ti ti-package"></i> Daily Shipping Checklist Records
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <!--<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#dailyShippingChecklistModal">-->
            <!--    <i class="ti ti-plus-circle"></i> Add New Checklist-->
            <!--</button>-->
        </div>
        <div class="btn-group me-2">
            <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                <i class="ti ti-printer"></i> Print
            </button>
        </div>
    </div>
</div>

<!-- Search Container -->
<div class="search-container">
    <div class="row">
        <div class="col-md-4">
            <label for="searchUserName" class="form-label"><i class="ti ti-user"></i> User Name</label>
            <input type="text" id="searchUserName" class="form-control" placeholder="Search by User Name">
        </div>
        <div class="col-md-4">
            <label for="searchDate" class="form-label"><i class="ti ti-calendar"></i> Date</label>
            <input type="date" id="searchDate" class="form-control">
        </div>
        <div class="col-md-4">
            <label for="searchStatus" class="form-label"><i class="ti ti-filter"></i> Task Status</label>
            <select id="searchStatus" class="form-control">
                <option value="">All Status</option>
                <option value="Yes">Yes</option>
                <option value="No">No</option>
            </select>
        </div>
    </div>
</div>

<!-- Table Container -->
<div class="table-container">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="ti ti-list"></i> Checklist Records
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="checklistTable">
                    <thead>
                        <tr>
                            <th>User Name</th>
                            <th>Date</th>
                            <th>Orders to Dispatch</th>
                            <th>Labels for Cancelled</th>
                            <th>Labels Voided</th>
                            <th>Random Check</th>
                            <th>Submitted At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($checklists as $checklist)
                        <tr data-id="{{ $checklist->id }}">
                            <td class="user_name">{{ $checklist->user_name }}</td>
                            <td class="checklist_date">{{ \Carbon\Carbon::parse($checklist->checklist_date)->format('M d, Y') }}</td>
                            <td>
                                <span class="status-badge status-{{ strtolower($checklist->task_1) }}">{{ $checklist->task_1 }}</span>
                                @if($checklist->task_1_comments)
                                    <div class="comments-cell mt-1"><small>{{ $checklist->task_1_comments }}</small></div>
                                @endif
                            </td>
                            <td>
                                <span class="status-badge status-{{ strtolower($checklist->task_2) }}">{{ $checklist->task_2 }}</span>
                                @if($checklist->task_2_comments)
                                    <div class="comments-cell mt-1"><small>{{ $checklist->task_2_comments }}</small></div>
                                @endif
                            </td>
                            <td>
                                <span class="status-badge status-{{ strtolower($checklist->task_3) }}">{{ $checklist->task_3 }}</span>
                                @if($checklist->task_3_comments)
                                    <div class="comments-cell mt-1"><small>{{ $checklist->task_3_comments }}</small></div>
                                @endif
                            </td>
                            <td>
                                <span class="status-badge status-{{ strtolower($checklist->task_4) }}">{{ $checklist->task_4 }}</span>
                                @if($checklist->task_4_comments)
                                    <div class="comments-cell mt-1"><small>{{ $checklist->task_4_comments }}</small></div>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($checklist->created_at)->format('M d, Y h:i A') }}</td>
                            <td>
                                <button type="button" class="btn btn-delete btn-sm delete-checklist-btn" data-id="{{ $checklist->id }}" title="Delete">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Search/Filter logic
function filterTable() {
    const userName = document.getElementById('searchUserName').value.toLowerCase();
    const date = document.getElementById('searchDate').value;
    const status = document.getElementById('searchStatus').value;
    const rows = document.querySelectorAll('#checklistTable tbody tr');
    
    rows.forEach(row => {
        const userNameText = row.querySelector('.user_name').textContent.toLowerCase();
        const dateText = row.querySelector('.checklist_date').textContent;
        const statusElements = row.querySelectorAll('.status-badge');
        
        let show = true;
        
        // Filter by user name
        if (userName && !userNameText.includes(userName)) show = false;
        
        // Filter by date
        if (date) {
            const rowDate = new Date(row.querySelector('.checklist_date').textContent);
            const searchDate = new Date(date);
            if (rowDate.toDateString() !== searchDate.toDateString()) show = false;
        }
        
        // Filter by status
        if (status) {
            let hasStatus = false;
            statusElements.forEach(badge => {
                if (badge.textContent.trim() === status) hasStatus = true;
            });
            if (!hasStatus) show = false;
        }
        
        row.style.display = show ? '' : 'none';
    });
}

// Add event listeners for search inputs
document.getElementById('searchUserName').addEventListener('input', filterTable);
document.getElementById('searchDate').addEventListener('change', filterTable);
document.getElementById('searchStatus').addEventListener('change', filterTable);

// Delete logic
$(document).on('click', '.delete-checklist-btn', function() {
    const id = $(this).data('id');
    const userName = $(this).closest('tr').find('.user_name').text();
    
    if(confirm(`Are you sure you want to delete this checklist for ${userName}?`)) {
        let formData = new FormData();
        formData.append('_method', 'DELETE');
        
        fetch("{{ url('shipping-checklist') }}/" + id, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                $(`#checklistTable tr[data-id='${id}']`).remove();
                
                // Show delete success message
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-warning alert-dismissible fade show';
                alertDiv.innerHTML = `
                    <i class="ti ti-trash"></i>
                    <strong>Deleted!</strong> Checklist has been removed successfully.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                
                document.querySelector('.content-page').insertBefore(alertDiv, document.querySelector('.search-container'));
                
                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 3000);
            } else {
                alert('Failed to delete checklist.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the checklist.');
        });
    }
});
</script>
@endpush
@endsection
