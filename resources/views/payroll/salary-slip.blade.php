@extends('layouts.main')
@section('page-title')
    {{ __('My Salary Slips') }}
@endsection
@section('title')
    {{ __('My Salary Slips') }}
@endsection
@section('page-breadcrumb')
    {{ __('Payroll') }},{{ __('Salary Slips') }}
@endsection
@push('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('layouts.includes.datatable-css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        .salary-slip-card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            margin-bottom: 1.5rem;
        }
        
        .salary-slip-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .salary-slip-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 0.375rem 0.375rem 0 0;
            padding: 1.5rem;
        }
        
        .salary-amount {
            font-size: 1.5rem;
            font-weight: bold;
            color: #ffffff;
        }
        
        .month-badge {
            background: #e7f3ff;
            color: #2c5aa0;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 1rem;
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
        
        .employee-info {
            background: #f8f9fa;
            border-radius: 0.375rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        
        .info-label {
            font-weight: 600;
            color: #495057;
        }
        
        .info-value {
            color: #6c757d;
        }
        
        .download-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            font-weight: 600;
            transition: all 0.2s ease-in-out;
        }
        
        .download-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
            color: white;
        }
        
        .salary-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .detail-item {
            background: white;
            padding: 1rem;
            border-radius: 0.375rem;
            border-left: 4px solid #667eea;
        }
        
        .detail-label {
            font-size: 0.875rem;
            color: #6c757d;
            margin-bottom: 0.25rem;
        }
        
        .detail-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: #495057;
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
            <i class="bi bi-receipt"></i> My Salary Slips
        </h1>
    </div>

    @if($employee)
        <!-- Employee Information -->
        <div class="employee-info">
            <h5 class="mb-3">
                <i class="bi bi-person-circle"></i> Employee Information
            </h5>
            <div class="row">
                <div class="col-md-4">
                    <div class="info-item">
                        <span class="info-label">Name:</span>
                        <span class="info-value">{{ $employee->name ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-item">
                        <span class="info-label">Department:</span>
                        <span class="info-value">{{ $employee->department ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-item">
                        <span class="info-label">Email:</span>
                        <span class="info-value">{{ $employee->email_address ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Salary Slips List -->
    <div class="row">
        @if($payrolls && $payrolls->count() > 0)
            @foreach($payrolls as $payroll)
                <div class="col-md-6 col-lg-4">
                    <div class="card salary-slip-card">
                        <div class="salary-slip-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-1">
                                        <i class="bi bi-calendar-month"></i> {{ $payroll->month ?? 'N/A' }}
                                    </h5>
                                    <small class="opacity-75">Salary Slip</small>
                                </div>
                                <div class="text-end">
                                    <div class="salary-amount">₹{{ number_format($payroll->total_payable ?? 0, 0) }}</div>
                                    <small class="opacity-75">Total Payable</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="salary-details">
                                <div class="detail-item">
                                    <div class="detail-label">Previous Salary</div>
                                    <div class="detail-value">₹{{ number_format($payroll->sal_previous ?? 0, 0) }}</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Current Salary</div>
                                    <div class="detail-value">₹{{ number_format($payroll->salary_current ?? 0, 0) }}</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Productive Hours</div>
                                    <div class="detail-value">{{ round($payroll->productive_hrs ?? 0) }} hrs</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Approved Hours</div>
                                    <div class="detail-value">{{ round($payroll->approved_hrs ?? 0) }} hrs</div>
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
                            
                            <div class="mt-3 d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="bi bi-clock"></i> Generated: {{ $payroll->created_at ? $payroll->created_at->format('d M Y') : 'N/A' }}
                                </small>
                                <button type="button" class="btn download-btn" onclick="downloadSlip({{ $payroll->id }})">
                                    <i class="bi bi-download"></i> Download PDF
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="card">
                    <div class="card-body no-slips">
                        <i class="bi bi-inbox"></i>
                        <h4>No Salary Slips Available</h4>
                        <p>You don't have any processed salary slips yet.<br>
                        Salary slips will appear here once your payroll has been processed and marked as done.</p>
                    </div>
                </div>
            </div>
        @endif
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
});

function downloadSlip(payrollId) {
    // Open PDF in new window/tab
    const url = `{{ route('payroll.generate-pdf', ':id') }}`.replace(':id', payrollId);
    window.open(url, '_blank');
}
</script>
@endpush
@endsection
