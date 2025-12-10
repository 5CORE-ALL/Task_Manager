@extends('layouts.main')
@php
    // Additional security check - redirect if user is not authorized to see admin view
    $currentUser = Auth::user();
    $isAuthorized = $currentUser && in_array(strtolower($currentUser->email), ['president@5core.com', 'hr@5core.com']);
    
    if (!$isAuthorized) {
        abort(403, 'Access denied. Only authorized personnel can view this page.');
    }
@endphp
@section('page-title')
    {{ __('All Salary Slips - Admin View') }}
@endsection
@section('title')
    {{ __('All Salary Slips - Admin View') }}
@endsection
@section('page-breadcrumb')
    {{ __('Payroll') }},{{ __('All Salary Slips') }}
@endsection
@push('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('layouts.includes.datatable-css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        .employee-section {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 2rem;
            border-radius: 0.5rem;
            overflow: hidden;
        }
        
        .employee-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            position: relative;
        }
        
        .employee-name {
            font-size: 1.25rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .employee-details {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .salary-slip-card {
            border: 1px solid #e9ecef;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        
        .salary-slip-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }
        
        .salary-slip-header {
            background: #f8f9fa;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: between;
            align-items: center;
        }
        
        .month-badge {
            background: #e7f3ff;
            color: #2c5aa0;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-weight: 600;
            font-size: 0.875rem;
        }
        
        .salary-amount {
            font-size: 1.25rem;
            font-weight: bold;
            color: #2c5aa0;
        }
        
        .salary-details {
            padding: 1.5rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
        }
        
        .detail-item {
            text-align: center;
            padding: 0.75rem;
            background: #f8f9fa;
            border-radius: 0.375rem;
            border-left: 3px solid #667eea;
        }
        
        .detail-label {
            font-size: 0.75rem;
            color: #6c757d;
            margin-bottom: 0.25rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .detail-value {
            font-size: 1rem;
            font-weight: 600;
            color: #495057;
        }
        
        .no-slips {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }
        
        .no-slips i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        .download-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.2s ease-in-out;
        }
        
        .download-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
            color: white;
        }
        
        .admin-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .stats-card {
            background: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            color: #667eea;
        }
        
        .stats-label {
            color: #6c757d;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }
    </style>
@endpush

@section('page-action')
    <div class="d-flex">
        @stack('addButtonHook')
    </div>
@endsection

@section('filter')
@endsection

@section('content')
<div class="main-content">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="bi bi-shield-check"></i> All Salary Slips - Admin View
        </h1>
        <div class="admin-badge">
            <i class="bi bi-person-check"></i> Admin Access
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-number">{{ $groupedPayrolls->count() }}</div>
                <div class="stats-label">Employees with Salary Slips</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-number">{{ $groupedPayrolls->flatten()->count() }}</div>
                <div class="stats-label">Total Salary Slips</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-number">₹{{ number_format($groupedPayrolls->flatten()->sum('total_payable'), 0) }}</div>
                <div class="stats-label">Total Payroll Amount</div>
            </div>
        </div>
    </div>

    @if($groupedPayrolls && $groupedPayrolls->count() > 0)
        @foreach($groupedPayrolls as $employeeId => $payrolls)
            @php
                $employee = $employees->get($employeeId);
            @endphp
            <div class="employee-section">
                <div class="employee-header">
                    <div class="employee-name">
                        <i class="bi bi-person-circle"></i> {{ $employee->name ?? 'Unknown Employee' }}
                    </div>
                    <div class="employee-details">
                        <strong>Department:</strong> {{ $employee->department ?? 'N/A' }} | 
                        <strong>Email:</strong> {{ $employee->email_address ?? 'N/A' }} | 
                        <strong>Total Slips:</strong> {{ $payrolls->count() }}
                    </div>
                </div>
                
                <div class="p-3">
                    <div class="row">
                        @foreach($payrolls as $payroll)
                            <div class="col-md-6 col-lg-4">
                                <div class="salary-slip-card">
                                    <div class="salary-slip-header">
                                        <div>
                                            <span class="month-badge">
                                                <i class="bi bi-calendar-month"></i> {{ $payroll->month ?? 'N/A' }}
                                            </span>
                                        </div>
                                        <div class="text-end">
                                            <div class="salary-amount">₹{{ number_format($payroll->total_payable ?? 0, 0) }}</div>
                                        </div>
                                    </div>
                                    
                                    <div class="salary-details">
                                        <div class="detail-item">
                                            <div class="detail-label">Previous</div>
                                            <div class="detail-value">₹{{ number_format($payroll->sal_previous ?? 0, 0) }}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Current</div>
                                            <div class="detail-value">₹{{ number_format($payroll->salary_current ?? 0, 0) }}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Incentive</div>
                                            <div class="detail-value">₹{{ number_format($payroll->incentive ?? 0, 0) }}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Advance</div>
                                            <div class="detail-value">₹{{ number_format($payroll->advance ?? 0, 0) }}</div>
                                        </div>
                                    </div>
                                    
                                    <div class="p-3 pt-0 d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="bi bi-clock"></i> {{ $payroll->created_at ? $payroll->created_at->format('d M Y') : 'N/A' }}
                                        </small>
                                        <button type="button" class="btn download-btn" onclick="downloadSlip({{ $payroll->id }})">
                                            <i class="bi bi-download"></i> Download
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="card">
            <div class="card-body no-slips">
                <i class="bi bi-inbox"></i>
                <h4>No Salary Slips Available</h4>
                <p>No processed salary slips found in the system.<br>
                Salary slips will appear here once payroll has been processed and marked as done.</p>
            </div>
        </div>
    @endif
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
});

function downloadSlip(payrollId) {
    // Open PDF in new window/tab
    const url = `{{ route('payroll.generate-pdf', ':id') }}`.replace(':id', payrollId);
    window.open(url, '_blank');
}

// Add search functionality
function filterEmployees() {
    const searchInput = document.getElementById('searchEmployee');
    if (searchInput) {
        const searchValue = searchInput.value.toLowerCase();
        const employeeSections = document.querySelectorAll('.employee-section');
        
        employeeSections.forEach(section => {
            const employeeName = section.querySelector('.employee-name').textContent.toLowerCase();
            const employeeEmail = section.querySelector('.employee-details').textContent.toLowerCase();
            
            if (employeeName.includes(searchValue) || employeeEmail.includes(searchValue)) {
                section.style.display = 'block';
            } else {
                section.style.display = 'none';
            }
        });
    }
}
</script>
@endpush
@endsection
