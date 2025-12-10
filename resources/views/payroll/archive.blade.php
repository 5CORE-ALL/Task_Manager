@extends('layouts.main')
@section('page-title')
    {{ __('Archive Employee - Payroll') }}
@endsection
@section('title')
    {{ __('Archive Employee - Payroll') }}
@endsection
@section('page-breadcrumb')
    {{ __('Payroll') }},{{ __('Archive') }},{{ __('Disabled Employees') }}
@endsection
@push('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('layouts.includes.datatable-css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        .table-container {
            margin-bottom: 2rem;
        }
        
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        
        .table th {
            background-color: #495057;
            color: white;
            font-weight: 600;
            border: none;
        }
        
        .table td {
            vertical-align: middle;
            border-top: 1px solid #dee2e6;
        }
        
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.02);
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }
        
        .badge {
            font-size: 0.875em;
        }
        
        .btn-group .btn {
            margin-right: 0.25rem;
        }
        
        .btn-group .btn:last-child {
            margin-right: 0;
        }
        
        .main-content {
            padding: 1rem;
        }
        
        .archive-row {
            background-color: #f8f9fa;
            opacity: 0.8;
        }
        
        .archive-row td {
            color: #6c757d;
        }
        
        .alert-info {
            background-color: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
        }
    </style>
@endpush

@section('page-action')
    <div class="d-flex">
        <a href="{{ route('payroll.index') }}" class="btn btn-sm btn-primary btn-icon">
            <i class="ti ti-arrow-left"></i> {{ __('Back to Payroll') }}
        </a>
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

<div class="main-content">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Archive Employee - Payroll</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('payroll.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Active Payroll
                </a>
            </div>
        </div>
    </div>

    @if($disabledPayrolls->count() > 0)
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle"></i>
            <strong>Archive Information:</strong> This page shows employees who have been disabled from the payroll system. 
            You can restore them back to active payroll by clicking the "Restore" button.
        </div>
    @else
        <div class="alert alert-success" role="alert">
            <i class="bi bi-check-circle"></i>
            <strong>No Archived Employees:</strong> All employees are currently active in the payroll system.
        </div>
    @endif

    <!-- Archive Table -->
    <div class="table-container">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-archive"></i> Archived Employees ({{ $disabledPayrolls->count() }})
                </h5>
            </div>
            <div class="card-body">
                @if($disabledPayrolls->count() > 0)
                    <div class="table-responsive">
                        <table id="archiveTable" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Department') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Month') }}</th>
                                    <th>{{ __('Previous Salary') }}</th>
                                    <th>{{ __('Increment') }}</th>
                                    <th>{{ __('Current Salary') }}</th>
                                    <th>{{ __('Productive Hrs') }}</th>
                                    <th>{{ __('Incentive') }}</th>
                                    <th>{{ __('Payable') }}</th>
                                    <th>{{ __('Advance') }}</th>
                                    <th>{{ __('Total Payable') }}</th>
                                    <th>{{ __('Payment Status') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($disabledPayrolls as $payroll)
                                <tr class="archive-row" data-id="{{ $payroll->id }}">
                                    <td class="name">{{ $payroll->name ?? 'N/A' }}</td>
                                    <td class="dept">{{ $payroll->department ?? 'N/A' }}</td>
                                    <td class="email_address">{{ $payroll->email_address ?? 'N/A' }}</td>
                                    <td class="month">{{ $payroll->month ?? 'August 2025' }}</td>
                                    <td class="sal_previous">{{ $payroll->sal_previous ? number_format($payroll->sal_previous) : '0' }}</td>
                                    <td class="increment">{{ $payroll->increment ? number_format($payroll->increment) : '0' }}</td>
                                    <td class="salary_current">{{ $payroll->salary_current ? number_format($payroll->salary_current) : '0' }}</td>
                                    <td class="productive_hrs">{{ $payroll->productive_hrs ?? '0' }}</td>
                                    <td class="incentive">{{ $payroll->incentive ? number_format($payroll->incentive) : '0' }}</td>
                                    <td class="payable">{{ $payroll->payable ? number_format(round($payroll->payable)) : '0' }}</td>
                                    <td class="advance">{{ $payroll->advance ? number_format($payroll->advance) : '0' }}</td>
                                    <td class="total_payable">{{ $payroll->total_payable ? number_format(round($payroll->total_payable)) : '0' }}</td>
                                    <td class="payment_done">
                                        @if($payroll->payment_done)
                                            <span class="badge bg-success">✓</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" 
                                                    class="btn btn-sm btn-success restore-btn" 
                                                    data-id="{{ $payroll->id }}" 
                                                    data-employee-id="{{ $payroll->employee_id }}" 
                                                    data-name="{{ $payroll->name }}"
                                                    title="Restore to Active Payroll">
                                                <i class="bi bi-arrow-clockwise"></i> Restore
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #6c757d;"></i>
                        <h5 class="mt-3 text-muted">No Archived Employees</h5>
                        <p class="text-muted">All employees are currently active in the payroll system.</p>
                        <a href="{{ route('payroll.index') }}" class="btn btn-primary">
                            <i class="bi bi-arrow-left"></i> Go to Active Payroll
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
@include('layouts.includes.datatable-js')
<script>
$(document).ready(function() {
    // CSRF Token for AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Initialize DataTable
    $('#archiveTable').DataTable({
        responsive: true,
        pageLength: 50,
        order: [[0, 'asc']],
        columnDefs: [
            { orderable: false, targets: [13] } // Disable sorting on Action column
        ]
    });
    
    // Restore button handler
    // Restore button handler
$(document).on('click', '.restore-btn', function() {
    const button = $(this);
    const id = button.data('id');
    const employeeName = button.data('name');
    const row = button.closest('tr');
    
    if(confirm(`Are you sure you want to restore "${employeeName}" back to active payroll?`)) {
        // Show loading state
        button.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Restoring...');
        
        $.ajax({
            url: `{{ route('payroll.restore', ':id') }}`.replace(':id', id),
            method: 'POST',
            success: function(response) {
                if(response.success) {
                    // Show success message
                    const alertDiv = $('<div class="alert alert-success alert-dismissible fade show">' +
                        '<i class="bi bi-check-circle"></i>' +
                        '<strong>Success!</strong> "' + employeeName + '" has been restored to active payroll.' +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                        '</div>');
                    $('.main-content').prepend(alertDiv);
                    setTimeout(() => alertDiv.remove(), 3000);
                    
                    // Remove the row from archive table
                    row.fadeOut(300, function() {
                        $(this).remove();
                        
                        // Check if table is empty
                        if ($('#archiveTable tbody tr').length === 0) {
                            location.reload(); // Reload to show empty state
                        }
                    });
                } else {
                    alert('Error: ' + (response.message || 'Failed to restore employee'));
                    button.prop('disabled', false).html('<i class="bi bi-arrow-clockwise"></i> Restore');
                }
            },
            error: function(xhr) {
                console.error('Restore error:', xhr);
                alert('Error restoring employee: ' + (xhr.responseJSON?.message || 'Server error'));
                button.prop('disabled', false).html('<i class="bi bi-arrow-clockwise"></i> Restore');
            }
        });
    }
});
});
</script>
@endpush
@endsection
