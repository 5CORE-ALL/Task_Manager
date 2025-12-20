@extends('layouts.main')
@section('page-title')
    {{ __('Payroll Management') }}
@endsection
@section('title')
    {{ __('Payroll Management') }}
@endsection
@section('page-breadcrumb')
    {{ __('Payroll') }},{{ __('Management') }},{{ __('Payroll List') }}
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
        
        .search-container {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
        }
        
        .modal-header.bg-primary {
            background-color: #0d6efd !important;
        }
        
        .btn-close-white {
            filter: invert(1) grayscale(100%) brightness(2);
        }
        
        .alert {
            border: none;
            border-radius: 0.5rem;
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
        
        .main-content {
            padding: 1rem;
        }
        
        @media (max-width: 768px) {
            .btn-toolbar {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .btn-group {
                width: 100%;
            }
            
            .btn-group .btn {
                flex: 1;
            }
        }
        
        /* Disabled row styling */
        .row-disabled {
            background-color: #f8f9fa !important;
            opacity: 0.6;
        }
        
        .row-disabled td {
            color: #6c757d !important;
        }
        
        /* Summary Cards Styling */
        .summary-cards {
            margin-top: 1rem;
            margin-bottom: 1rem;
        }
        
        .summary-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            padding: 0.8rem;
            color: white;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: none;
            /* height: 100%; */
        }
        
        .summary-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }
        
        .summary-card.increment {
            background: linear-gradient(135deg, #939393 0%, #000000 100%);
        }
        
        .summary-card.incentive {
            background: linear-gradient(135deg, #939393 0%, #000000 100%);
        }
        
        .summary-card.advance {
            background: linear-gradient(135deg, #939393 0%, #000000 100%);
        }
        
        .summary-card.extra {
            background: linear-gradient(135deg, #939393 0%, #000000 100%);

        }
        
        .summary-card.net-payable {
            background: linear-gradient(135deg, #939393 0%, #000000 100%);
        }
        
        .summary-card-icon {
            font-size: 1.2rem;
            margin-bottom: 0.3rem;
            opacity: 0.9;
        }
        
        .summary-card-title {
            font-size: 0.7rem;
            font-weight: 600;
            margin-bottom: 0.3rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            line-height: 1;
        }
        
        .summary-card-value {
            font-size: 1rem;
            font-weight: 700;
            margin: 0;
            line-height: 1;
        }
        
        @media (max-width: 768px) {
            .summary-card {
                margin-bottom: 0.5rem;
                padding: 0.6rem;
            }
            
            .summary-card-value {
                font-size: 0.9rem;
            }
            
            .summary-card-title {
                font-size: 0.6rem;
            }
        }
        
        .row-disabled .btn:not(.enable-payroll-btn) {
            pointer-events: none;
            opacity: 0.5;
        }
        
        /* Tab Navigation Styles */
        .nav-tabs {
            border-bottom: 2px solid #dee2e6;
            margin-bottom: 1rem;
        }
        
        .nav-tabs .nav-link {
            border: none;
            border-radius: 0;
            padding: 0.75rem 1.5rem;
            color: #6c757d;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .nav-tabs .nav-link:hover {
            border-color: transparent;
            color: #495057;
            background-color: #f8f9fa;
        }
        
        .nav-tabs .nav-link.active {
            color: #495057;
            background-color: #fff;
            border-color: transparent;
            border-bottom: 2px solid #007bff;
            font-weight: 600;
        }
        
        .tab-content {
            min-height: 400px;
        }
        
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        
        .archive-row {
            background-color: #f8f9fa;
            opacity: 0.8;
        }
        
        .archive-row td {
            color: #6c757d;
        }
        
        /* Sticky/Frozen Columns Styles */
        .table-responsive {
            overflow-x: auto;
            position: relative;
            max-height: 700px; /* Set max height for vertical scrolling */
            overflow-y: auto;   /* Enable vertical scrolling */
        }
        
        #payrollTable {
            position: relative;
        }
        
        /* Sticky Header Styles */
        #payrollTable thead th {
            position: sticky;
            top: 0;
            z-index: 110; /* Higher than other sticky elements */
            background-color: #495057 !important;
            color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Sticky Headers for Archive and Contractual Tables */
        #archive-content .table-responsive,
        #contractual-content .table-responsive {
            max-height: 700px;
            overflow-y: auto;
            overflow-x: auto;
            position: relative;
        }

        #archiveTable thead th,
        #contractualTable thead th {
            position: sticky;
            top: 0;
            z-index: 110;
            background-color: #495057 !important;
            color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        /* Archive Table - Sticky Name Column */
        #archiveTable th:nth-child(1),
        #archiveTable td:nth-child(1) {
            position: sticky;
            left: 0;
            z-index: 100;
            background-color: #f8f9fa;
            border-right: 2px solid #dee2e6;
            white-space: nowrap;
            min-width: 200px;
            max-width: 200px;
            width: 200px;
        }

        /* Archive Table - Header styling for sticky name column */
        #archiveTable thead th:nth-child(1) {
            background-color: #495057 !important;
            color: white !important;
            z-index: 120;
        }

        /* Archive Table - Adjust for striped rows */
        #archiveTable tbody tr:nth-of-type(odd) td:nth-child(1) {
            background-color: #e9ecef;
        }

        /* Archive Table - Adjust for hover effect */
        #archiveTable tbody tr:hover td:nth-child(1) {
            background-color: #dee2e6;
        }

        /* Archive Table - Shadow effect for better visual separation */
        #archiveTable th:nth-child(1)::after,
        #archiveTable td:nth-child(1)::after {
            content: '';
            position: absolute;
            top: 0;
            right: -2px;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to right, rgba(0,0,0,0.1), transparent);
            pointer-events: none;
        }

        /* Contractual Table - Sticky Name Column */
        #contractualTable th:nth-child(1),
        #contractualTable td:nth-child(1) {
            position: sticky;
            left: 0;
            z-index: 100;
            background-color: #f8f9fa;
            border-right: 2px solid #dee2e6;
            white-space: nowrap;
            min-width: 200px;
            max-width: 200px;
            width: 200px;
        }

        /* Contractual Table - Header styling for sticky name column */
        #contractualTable thead th:nth-child(1) {
            background-color: #495057 !important;
            color: white !important;
            z-index: 120;
        }

        /* Contractual Table - Adjust for striped rows */
        #contractualTable tbody tr:nth-of-type(odd) td:nth-child(1) {
            background-color: #e9ecef;
        }

        /* Contractual Table - Adjust for hover effect */
        #contractualTable tbody tr:hover td:nth-child(1) {
            background-color: #dee2e6;
        }

        /* Contractual Table - Shadow effect for better visual separation */
        #contractualTable th:nth-child(1)::after,
        #contractualTable td:nth-child(1)::after {
            content: '';
            position: absolute;
            top: 0;
            right: -2px;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to right, rgba(0,0,0,0.1), transparent);
            pointer-events: none;
        }

        /* Archive Row Styling */
        .archive-row td:nth-child(1) {
            background-color: #f8f9fa !important;
            color: #6c757d !important;
        }
        /* Ensure all other cells have lower z-index */
        #payrollTable th,
        #payrollTable td {
            position: relative;
            z-index: 1;
        }
        
        /* Make first column (SL NO) sticky */
        #payrollTable th:nth-child(1),
        #payrollTable td:nth-child(1) {
            position: sticky;
            left: 0;
            z-index: 100;
            background-color: #f8f9fa;
            border-right: 2px solid #dee2e6;
        }
        
        /* Make second column (Name) sticky */
        #payrollTable th:nth-child(2),
        #payrollTable td:nth-child(2) {
            position: sticky;
            left: 100px; /* Width of first column + some padding */
            z-index: 99;
            background-color: #f8f9fa;
            border-right: 2px solid #dee2e6;
        }
        
        /* Header styling for sticky columns with sticky header */
        #payrollTable thead th:nth-child(1),
        #payrollTable thead th:nth-child(2) {
            background-color: #495057 !important;
            color: white !important;
            z-index: 120; /* Higher than sticky header */
        }
        
        /* Adjust for striped rows */
        #payrollTable tbody tr:nth-of-type(odd) td:nth-child(1),
        #payrollTable tbody tr:nth-of-type(odd) td:nth-child(2) {
            background-color: #e9ecef;
        }
        
        /* Adjust for hover effect */
        #payrollTable tbody tr:hover td:nth-child(1),
        #payrollTable tbody tr:hover td:nth-child(2) {
            background-color: #dee2e6;
        }
        
        /* Adjust for disabled rows */
        .row-disabled td:nth-child(1),
        .row-disabled td:nth-child(2) {
            background-color: #f8f9fa !important;
            color: #6c757d !important;
        }
        
        /* Shadow effect for better visual separation */
        #payrollTable th:nth-child(2)::after,
        #payrollTable td:nth-child(2)::after {
            content: '';
            position: absolute;
            top: 0;
            right: -2px;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to right, rgba(0,0,0,0.1), transparent);
            pointer-events: none;
        }
        
        /* Ensure text doesn't wrap in sticky columns */
        #payrollTable th:nth-child(1),
        #payrollTable td:nth-child(1),
        #payrollTable th:nth-child(2),
        #payrollTable td:nth-child(2) {
            white-space: nowrap;
            min-width: fit-content;
        }
        
        /* Set specific widths for better control */
        #payrollTable th:nth-child(1),
        #payrollTable td:nth-child(1) {
            width: 45px;
            min-width: 45px;
            max-width: 45px;
        }
        
        #payrollTable th:nth-child(2),
        #payrollTable td:nth-child(2) {
            width: 150px;
            min-width: 150px;
            max-width: 150px;
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
        <!-- <h1 class="h2">Payroll Management</h1> -->
        <div class="btn-toolbar mb-2 mb-md-0">
            <!--<div class="btn-group me-2">-->
            <!--    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPayrollModal">-->
            <!--        <i class="bi bi-plus-circle"></i> Add Payroll Entry-->
            <!--    </button>-->
            <!--</div>-->
            <!--<div class="btn-group me-2">-->
            <!--    <button type="button" class="btn btn-outline-secondary">-->
            <!--        <i class="bi bi-download"></i> Export-->
            <!--    </button>-->
            <!--    <button type="button" class="btn btn-outline-secondary">-->
            <!--        <i class="bi bi-printer"></i> Print-->
            <!--    </button>-->
            <!--</div>-->
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="card mb-3">
        <div class="card-body p-0">
            <ul class="nav nav-tabs" id="payrollTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="payroll-tab" data-bs-toggle="tab" data-bs-target="#payroll-content" type="button" role="tab" aria-controls="payroll-content" aria-selected="true">
                        <i class="bi bi-currency-dollar"></i> Payroll
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="archive-tab" data-bs-toggle="tab" data-bs-target="#archive-content" type="button" role="tab" aria-controls="archive-content" aria-selected="false">
                        <i class="bi bi-archive"></i> Archive
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="contractual-tab" data-bs-toggle="tab" data-bs-target="#contractual-content" type="button" role="tab" aria-controls="contractual-content" aria-selected="false">
                        <i class="bi bi-file-earmark-person"></i> Contractual
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="tab-content" id="payrollTabsContent">
        <!-- Payroll Tab (Active) -->
        <div class="tab-pane fade show active" id="payroll-content" role="tabpanel" aria-labelledby="payroll-tab">
            <!-- Payroll Table -->
            <div class="table-container position-relative">
                <div class="loading-overlay d-none" id="payroll-loading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
        <div class="card">
            <div class="card-header">
                <!-- <h5 class="card-title mb-0">
                    <i class="bi bi-currency-dollar"></i> Payroll Records
                </h5> -->
                <!-- Search Bars -->
                <div class="row mt-3 mb-2">
                    <div class="col-md-2">
                        <label for="searchMonth" class="form-label">
                            <i class="bi bi-calendar"></i> Month
                        </label>
                        <select id="monthSelect" class="form-control">
                            <option value="January 2025" {{ (isset($selectedMonth) && $selectedMonth == 'January 2025') ? 'selected' : '' }}>January</option>
                            <option value="February 2025" {{ (isset($selectedMonth) && $selectedMonth == 'February 2025') ? 'selected' : '' }}>February</option>
                            <option value="March 2025" {{ (isset($selectedMonth) && $selectedMonth == 'March 2025') ? 'selected' : '' }}>March</option>
                            <option value="April 2025" {{ (isset($selectedMonth) && $selectedMonth == 'April 2025') ? 'selected' : '' }}>April</option>
                            <option value="May 2025" {{ (isset($selectedMonth) && $selectedMonth == 'May 2025') ? 'selected' : '' }}>May</option>
                            <option value="June 2025" {{ (isset($selectedMonth) && $selectedMonth == 'June 2025') ? 'selected' : '' }}>June</option>
                            <option value="July 2025" {{ (isset($selectedMonth) && $selectedMonth == 'July 2025') ? 'selected' : '' }}>July</option>
                            <option value="August 2025" {{ (!isset($selectedMonth) || $selectedMonth == 'August 2025') ? 'selected' : '' }}>August</option>
                            <option value="September 2025" {{ (isset($selectedMonth) && $selectedMonth == 'September 2025') ? 'selected' : '' }}>September</option>
                            <option value="October 2025" {{ (isset($selectedMonth) && $selectedMonth == 'October 2025') ? 'selected' : '' }}>October</option>
                            <option value="November 2025" {{ (isset($selectedMonth) && $selectedMonth == 'November 2025') ? 'selected' : '' }}>November</option>
                            <option value="December 2025" {{ (isset($selectedMonth) && $selectedMonth == 'December 2025') ? 'selected' : '' }}>December</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="searchName" class="form-label">
                            <i class="bi bi-person"></i> Name
                        </label>
                        <input type="text" id="searchName" class="form-control" placeholder="Search by Name">
                    </div>
                    <div class="col-md-2">
                        <label for="searchDept" class="form-label">
                            <i class="bi bi-building"></i> Department
                        </label>
                        <input type="text" id="searchDept" class="form-control" placeholder="Search by Department">
                    </div>
                    <div class="col-md-2">
                        <label for="searchEmail" class="form-label">
                            <i class="bi bi-envelope"></i> Email
                        </label>
                        <input type="text" id="searchEmail" class="form-control" placeholder="Search by Email">
                    </div>
                    <div class="col-md-2">
                        <label for="searchPaymentStatus" class="form-label">
                            <i class="bi bi-check-circle"></i> Payment Status
                        </label>
                        <select id="searchPaymentStatus" class="form-control">
                            <option value="">All Status</option>
                            <option value="Done">Done</option>
                            <option value="Pending">Pending</option>
                        </select>
                    </div>
                    <div class="col-md-2">
    <label class="form-label d-block">&nbsp;</label>
    <div class="btn-group">
        <button type="button" class="btn btn-outline-secondary" id="exportPayrollBtn" data-bs-toggle="tooltip" title="Export">
            <i class="bi bi-download"></i>
        </button>
        <button type="button" class="btn btn-outline-secondary" id="printPayrollBtn" data-bs-toggle="tooltip" title="Print">
            <i class="bi bi-printer"></i>
        </button>
        <button type="button" class="btn btn-outline-primary" id="copyBankDetailsBtn" data-bs-toggle="tooltip" title="Copy Bank Details from Previous Month">
            <i class="bi bi-bank"></i>
        </button>
    </div>
</div>
                </div>
            </div>
            <div class="card-body">
                <!-- Summary Cards -->
                <div class="summary-cards">
                    <div class="row g-2">
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <div class="card summary-card increment">
                                
                                <div class="summary-card-title">Total Increment</div>
                                <div class="summary-card-value" id="totalIncrement">₹0</div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <div class="card summary-card incentive">
                               
                                <div class="summary-card-title">Total Incentive</div>
                                <div class="summary-card-value" id="totalIncentive">₹0</div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <div class="card summary-card advance">
                               
                                <div class="summary-card-title">Total Advance</div>
                                <div class="summary-card-value" id="totalAdvance">₹0</div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <div class="card summary-card extra">
                                
                                <div class="summary-card-title">Total Extra</div>
                                <div class="summary-card-value" id="totalExtra">₹0</div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-6 col-md-8 col-sm-12">
                            <div class="card summary-card net-payable">
                               
                                <div class="summary-card-title">Total Net Payable</div>
                                <div class="summary-card-value" id="totalNetPayable">₹0</div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="payrollTable">
                        <thead class="table-dark" style="background-color: cyan;">
                            <tr>
                                <th scope="col">Sl no</th>
                                <th scope="col">Name</th>
                                <th scope="col">Sal Pre</th>
                                <th scope="col">Incr</th>
                                <th scope="col">Sal</th>
                                <th scope="col">TTL Hr</th>
                                <th scope="col">Idle</th>
                                <th scope="col">TL</th>
                                 <th scope="col">ETC</th>
                                <th scope="col">ATC</th>
                                <th scope="col">ATC/TL %</th>
                                <th scope="col">ETC/TL %</th>
                                <th scope="col">ATC/ETC %</th>
                                <th scope="col">Avg Time</th>
                                <th scope="col">Overdue</th>
                                <th scope="col">Approved</th>
                                <th scope="col">Status</th>
                                <th scope="col">Incentive</th>
                                <th scope="col">Adv</th>
                                <th scope="col">Extra</th>
                                <th scope="col">Net Payable</th>
                                <th scope="col">Paid</th>
                                <th scope="col">Action</th>
                                 <th scope="col">Email</th>
                                <th scope="col">Dept</th>
                                <th scope="col">Month</th>
                                <th scope="col">Payable</th>
                                <th scope="col">Bank 1</th>
                                <th scope="col">Bank 2</th>
                                <th scope="col">UP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($payrolls) && $payrolls->count() > 0)
                                @foreach($payrolls as $index => $payroll)
                                    @if(!isset($payroll->email_address) || $payroll->email_address !== 'president@5core.com')
                                    <tr data-id="{{ $payroll->id ?? $payroll->employee_id }}" 
                                        class="{{ (isset($payroll->is_enabled) && !$payroll->is_enabled) ? 'row-disabled' : '' }}"
                                        data-sal-previous="{{ $payroll->sal_previous ?? 0 }}"
                                        data-salary-current="{{ $payroll->salary_current ?? 0 }}">
                                        <td class="sl_no">{{ $index + 1 }}</td>
                                        <td class="name">{{ $payroll->name ?? 'N/A' }}</td>
                                        <td class="sal_previous">{{ $payroll->sal_previous ? number_format($payroll->sal_previous, 0) : '0' }}</td>
                                        <td class="increment text-center">{{ $payroll->increment ? number_format($payroll->increment, 0) : '0' }}</td>
                                        <td class="salary_current text-center">{{ $payroll->salary_current ? number_format($payroll->salary_current, 0) : '0' }}</td>
                                        <td class="total_hours text-center">{{ $payroll->total_hours ? ceil($payroll->total_hours) : 0 }}</td>
                                        <td class="idle_hours text-center">{{ $payroll->idle_hours ? ceil($payroll->idle_hours) : 0 }}</td>
                                        <td class="productive_hrs text-center">{{ $payroll->productive_hrs ?? '0' }}</td>
                                         <td class="etc_hours">{{ $payroll->etc_hours ?? '0' }}</td>
                                        <td class="atc_hours" style="color: {{ (($payroll->atc_hours ?? 0) < (($payroll->productive_hrs ?? 0) * 0.9)) ? 'red' : 'inherit' }};">
                                            {{ $payroll->atc_hours ?? '0' }}
                                        </td>
                                        <td class="atc_tl_percentage">
                                            @if($payroll->productive_hrs && $payroll->productive_hrs > 0)
                                                {{ number_format(($payroll->atc_hours / $payroll->productive_hrs) * 100, 2) }}%
                                            @else
                                                0%
                                            @endif
                                        </td>
                                        <td class="etc_tl_percentage">
                                            @if($payroll->productive_hrs && $payroll->productive_hrs > 0)
                                                {{ number_format(($payroll->etc_hours / $payroll->productive_hrs) * 100, 2) }}%
                                            @else
                                                0%
                                            @endif
                                        </td>
                                        <td class="atc_etc_percentage">
                                            @if($payroll->etc_hours && $payroll->etc_hours > 0)
                                                {{ number_format(($payroll->atc_hours / $payroll->etc_hours) * 100, 2) }}%
                                            @else
                                                0%
                                            @endif
                                        </td>
                                        <td class="avg_time">
                                            {{ number_format($payroll->productive_hrs / 25, 1) }}
                                        </td>
                                        <td class="overdue">
                                            {{ $payroll->overdue_count ?? '0' }}
                                        </td>
                                            <td class="approved_hrs">
                                                <input type="number" 
                                                       class="form-control form-control-sm approved-hrs-input" 
                                                       value="{{ $payroll->productive_hrs ?? '0' }}" 
                                                       data-payroll-id="{{ $payroll->id ?? $payroll->employee_id }}"
                                                       data-employee-id="{{ $payroll->employee_id }}"
                                                       style="width: 70px; font-size: 12px;"
                                                       min="0" 
                                                       step="1">
                                            </td>
                                        <td class="approval_status">
                                            <select class="form-select form-select-sm approval-status-dropdown" 
                                                    data-payroll-id="{{ $payroll->id ?? $payroll->employee_id }}"
                                                    data-employee-id="{{ $payroll->employee_id }}"
                                                    style="width: 110px; font-size: 12px;">
                                                <option value="pending" {{ ($payroll->approval_status ?? 'pending') == 'pending' ? 'selected' : '' }}>Not Approved</option>
                                                <option value="approved" {{ ($payroll->approval_status ?? 'pending') == 'approved' ? 'selected' : '' }}>Approved</option>
                                            </select>
                                        </td>
                                        <td class="incentive">
                                            <input type="number" 
                                                   class="form-control form-control-sm incentive-input" 
                                                   value="{{ $payroll->incentive ? intval($payroll->incentive) : 0 }}" 
                                                   data-payroll-id="{{ $payroll->id ?? $payroll->employee_id }}"
                                                   data-employee-id="{{ $payroll->employee_id }}"
                                                   style="width: 70px; font-size: 12px;"
                                                   min="0" 
                                                   step="1">
                                        </td>
                                        <td class="advance">
                                            <input type="number" 
                                                   class="form-control form-control-sm advance-input" 
                                                   value="{{ $payroll->advance ? intval($payroll->advance) : 0 }}" 
                                                   data-payroll-id="{{ $payroll->id ?? $payroll->employee_id }}"
                                                   data-employee-id="{{ $payroll->employee_id }}"
                                                   style="width: 70px; font-size: 12px;"
                                                   min="0" 
                                                   step="1">
                                        </td>
                                        <td class="extra">
                                            <input type="number" 
                                                   class="form-control form-control-sm extra-input" 
                                                   value="{{ $payroll->extra ? intval($payroll->extra) : 0 }}" 
                                                   data-payroll-id="{{ $payroll->id ?? $payroll->employee_id }}"
                                                   data-employee-id="{{ $payroll->employee_id }}"
                                                   style="width: 70px; font-size: 12px;"
                                                   min="0" 
                                                   step="1">
                                        </td>
                                        <td class="total_payable">{{ $payroll->total_payable ? number_format($payroll->total_payable, 0) : '0' }}</td>
                                        <td class="payment_done">
                                            @if($payroll->payment_done)
                                                <span class="badge bg-success">✓</span>
                                            @else
                                                <span class="badge bg-warning">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <!-- Always show edit/delete buttons -->
                                                <button type="button" class="btn btn-warning btn-sm edit-payroll-btn" data-id="{{ $payroll->id ?? $payroll->employee_id }}" data-employee-id="{{ $payroll->employee_id }}" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <!-- <button type="button" class="btn btn-danger btn-sm delete-payroll-btn" data-id="{{ $payroll->id ?? $payroll->employee_id }}" data-employee-id="{{ $payroll->employee_id }}" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button> -->
                                                @if(!$payroll->payment_done)
                                                    <button type="button" class="btn btn-success btn-sm mark-done-btn" data-id="{{ $payroll->id ?? $payroll->employee_id }}" data-employee-id="{{ $payroll->employee_id }}" title="Mark as Done">
                                                        <i class="bi bi-check-circle"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-info btn-sm generate-pdf-btn" data-id="{{ $payroll->id ?? $payroll->employee_id }}" data-employee-id="{{ $payroll->employee_id }}" title="Generate PDF">
                                                        <i class="bi bi-file-earmark-pdf"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-primary btn-sm send-email-btn" data-id="{{ $payroll->id ?? $payroll->employee_id }}" data-employee-id="{{ $payroll->employee_id }}" data-name="{{ $payroll->name }}" data-email="{{ $payroll->email_address }}" data-month="{{ $payroll->month }}" title="Send Salary Slip by Email">
                                                        <i class="bi bi-envelope"></i>
                                                    </button>
                                                @endif
                                                
                                                <!-- Enable/Disable buttons -->
                                                @if(isset($payroll->is_enabled) && !$payroll->is_enabled)
                                                    <button type="button" class="btn btn-outline-success btn-sm enable-payroll-btn" data-id="{{ $payroll->id ?? $payroll->employee_id }}" data-employee-id="{{ $payroll->employee_id }}" title="Enable">
                                                        <i class="bi bi-check-circle-fill"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-outline-danger btn-sm disable-payroll-btn" data-id="{{ $payroll->id ?? $payroll->employee_id }}" data-employee-id="{{ $payroll->employee_id }}" title="Archive">
                                                        <i class="bi bi-x-circle-fill"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-warning btn-sm archive-contractual-btn" data-id="{{ $payroll->id ?? $payroll->employee_id }}" data-employee-id="{{ $payroll->employee_id }}" title="Archive as Contractual">
                                                        <i class="bi bi-file-earmark-person"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                         <td class="email_address">{{ $payroll->email_address ?? 'N/A' }}</td>
                                        <td class="dept">{{ $payroll->department ?? 'N/A' }}</td>
                                        <td class="month">{{ $payroll->month ?? 'August' }}</td>
                                        <td class="payable">{{ $payroll->payable ? number_format($payroll->payable, 0) : '0' }}</td>
                                        <td class="bank1">{{ $payroll->bank1 ?? '' }}</td>
                                        <td class="bank2">{{ $payroll->bank2 ?? '' }}</td>
                                        <td class="up">{{ $payroll->up ?? '' }}</td>

                                    </tr>
                                    @endif
                                @endforeach
                            @else
                                <tr data-id="sample">
                                    <td colspan="22" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox"></i><br>
                                        No employees found. Please add employees first.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
        </div>
        
        <!-- Archive Tab -->
        <div class="tab-pane fade" id="archive-content" role="tabpanel" aria-labelledby="archive-tab">
            <div class="table-container position-relative">
                <div class="loading-overlay d-none" id="archive-loading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-archive"></i> Archived Employees
                            <span id="archive-count" class="badge bg-secondary ms-2">0</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="archive-alert" class="alert alert-info d-none" role="alert">
                            <i class="bi bi-info-circle"></i>
                            <strong>Archive Information:</strong> This page shows employees who have been disabled from the payroll system. 
                            You can restore them back to active payroll by clicking the "Restore" button.
                        </div>
                        <div id="archive-empty" class="alert alert-success d-none" role="alert">
                            <i class="bi bi-check-circle"></i>
                            <strong>No Archived Employees:</strong> All employees are currently active in the payroll system.
                        </div>
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
                                <tbody id="archive-table-body">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Contractual Tab -->
        <div class="tab-pane fade" id="contractual-content" role="tabpanel" aria-labelledby="contractual-tab">
            <div class="table-container position-relative">
                <div class="loading-overlay d-none" id="contractual-loading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-file-earmark-person"></i> Contractual Employees
                            <span id="contractual-count" class="badge bg-secondary ms-2">0</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Month Filter for Contractual Section -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="contractualMonthSelect" class="form-label">
                                    <i class="bi bi-calendar"></i> Month
                                </label>
                                <select id="contractualMonthSelect" class="form-control">
                                    <option value="January 2025">January</option>
                                    <option value="February 2025">February</option>
                                    <option value="March 2025">March</option>
                                    <option value="April 2025">April</option>
                                    <option value="May 2025">May</option>
                                    <option value="June 2025">June</option>
                                    <option value="July 2025">July</option>
                                    <option value="August 2025" selected>August</option>
                                    <option value="September 2025">September</option>
                                    <option value="October 2025">October</option>
                                    <option value="November 2025">November</option>
                                    <option value="December 2025">December</option>
                                </select>
                            </div>
                        </div>
                        <div id="contractual-alert" class="alert alert-info d-none" role="alert">
                            <i class="bi bi-info-circle"></i>
                            <strong>Contractual Information:</strong> This page shows employees who have been moved to contractual status. 
                            You can restore them back to active payroll by clicking the "Restore" button.
                        </div>
                        <div id="contractual-empty" class="alert alert-success d-none" role="alert">
                            <i class="bi bi-check-circle"></i>
                            <strong>No Contractual Employees:</strong> All employees are currently in regular payroll or archived status.
                        </div>
                        <div class="table-responsive">
                            <table id="contractualTable" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Department') }}</th>
                                        <th>{{ __('Email') }}</th>
                                        <th>{{ __('Month') }}</th>
                                        <th>{{ __('Number of Blog/Videos') }}</th>
                                        <th>{{ __('Rate') }}</th>
                                        <th>{{ __('Advance') }}</th>
                                        <th>{{ __('Payable') }}</th>
                                        <th>{{ __('Total Payable') }}</th>
                                        <th>{{ __('Payment Status') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="contractual-table-body">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Payroll Modal -->
<div class="modal fade" id="addPayrollModal" tabindex="-1" aria-labelledby="addPayrollModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addPayrollModalLabel">
                    <i class="bi bi-plus-circle"></i> <span id="modalTitle">Add New Payroll Entry</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addPayrollForm">
                    <input type="hidden" id="payrollId" name="id" />
                    
                    <!-- Employee Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2">
                                <i class="bi bi-person"></i> Employee Information
                            </h6>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="employeeName" class="form-label">
                                    <i class="bi bi-person"></i> Employee
                                </label>
                                <select class="form-select" id="employeeSelect" name="employee_id">
                                    <option value="">Select Employee</option>
                                </select>
                                <input type="hidden" id="employeeName" name="name">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="dept" class="form-label">
                                    <i class="bi bi-building"></i> Department
                                </label>
                                <input type="text" class="form-control" id="dept" name="department" placeholder="Auto-filled from employee" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="emailAddress" class="form-label">
                                    <i class="bi bi-envelope"></i> Email Address
                                </label>
                                <input type="email" class="form-control" id="emailAddress" name="email_address" placeholder="Auto-filled from employee" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="month" class="form-label">
                                    <i class="bi bi-calendar"></i> Month
                                </label>
                                <input type="text" class="form-control" id="month" name="month" value="August 2025" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Salary Details -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2">
                                <i class="bi bi-currency-dollar"></i> Salary Details
                            </h6>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="salPrevious" class="form-label">
                                    <i class="bi bi-cash"></i> Previous Salary
                                </label>
                                <input type="number" class="form-control" id="salPrevious" name="sal_previous" placeholder="0" step="1">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="increment" class="form-label">
                                    <i class="bi bi-plus-circle"></i> Increment
                                </label>
                                <input type="number" class="form-control" id="increment" name="increment" placeholder="0" step="1">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="salaryCurrent" class="form-label">
                                    <i class="bi bi-cash-coin"></i> Current Salary
                                </label>
                                <input type="number" class="form-control" id="salaryCurrent" name="salary_current" placeholder="0" step="1" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Performance & Payment Details -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2">
                                <i class="bi bi-calculator"></i> Performance & Payment Details
                            </h6>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="productiveHrs" class="form-label">
                                    <i class="bi bi-clock"></i> Productive Hours (TeamLogger)
                                </label>
                                <input type="number" class="form-control" id="productiveHrs" name="productive_hrs" placeholder="Auto-filled from TeamLogger" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="approvedHrs" class="form-label">
                                    <i class="bi bi-check-circle"></i> Approved Hours
                                </label>
                                <input type="number" class="form-control" id="approvedHrs" name="approved_hrs" placeholder="0" step="1">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="approvalStatus" class="form-label">
                                    <i class="bi bi-shield-check"></i> Approval Status
                                </label>
                                <select class="form-select" id="approvalStatus" name="approval_status">
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="incentive" class="form-label">
                                    <i class="bi bi-star"></i> Incentive
                                </label>
                                <input type="number" class="form-control" id="incentive" name="incentive" placeholder="0" step="1">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="advance" class="form-label">
                                    <i class="bi bi-arrow-up-circle"></i> Advance
                                </label>
                                <input type="number" class="form-control" id="advance" name="advance" placeholder="0" step="1">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="payable" class="form-label">
                                    <i class="bi bi-cash-stack"></i> Payable
                                </label>
                                <input type="number" class="form-control" id="payable" name="payable" placeholder="0" step="1" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Total & Payment Status -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2">
                                <i class="bi bi-receipt"></i> Final Details
                            </h6>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="totalPayable" class="form-label">
                                    <i class="bi bi-calculator"></i> Total Payable
                                </label>
                                <input type="number" class="form-control" id="totalPayable" name="total_payable" placeholder="0" step="1" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="extra" class="form-label">
                                    <i class="bi bi-plus-square"></i> Extra
                                </label>
                                <input type="number" class="form-control" id="extra" name="extra" placeholder="0" step="1">
                            </div>
                        </div>
                         <div class="col-md-4">
                            <div class="mb-3">
                                <label for="paymentDone" class="form-label">
                                    <i class="bi bi-check-circle"></i> Payment Status
                                </label>
                                <select class="form-select" id="paymentDone" name="payment_done">
                                    <option value="0">Pending</option>
                                    <option value="1">Done</option>
                                </select>
                                <input type="hidden" id="teamloggerHrs" name="teamlogger_hrs" value="0">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bank Details -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2">
                                <i class="bi bi-bank"></i> Bank & Additional Details
                            </h6>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="bank1" class="form-label">
                                    <i class="bi bi-bank"></i> Bank 1 (B1)
                                </label>
                                <textarea class="form-control" id="bank1" name="bank1" rows="3" placeholder="Enter Bank 1 details"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="bank2" class="form-label">
                                    <i class="bi bi-bank"></i> Bank 2 (B2)
                                </label>
                                <textarea class="form-control" id="bank2" name="bank2" rows="3" placeholder="Enter Bank 2 details"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="up" class="form-label">
                                    <i class="bi bi-arrow-up"></i> UPI ID
                                </label>
                                <input type="text" class="form-control" id="up" name="up" placeholder="Enter UPI details">
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle"></i>
                        <strong>Note:</strong> The payroll entry will be added to the payroll table once you click <span id="modalAction">"Add Payroll Entry"</span>.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" id="savePayrollBtn">
                    <i class="bi bi-check-circle"></i> <span id="modalBtnText">Add Payroll Entry</span>
                </button>
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
$('#payrollTable').DataTable({
    searching: false,    // disable global search box
    lengthChange: false, // disable page length dropdown
    paging: false,       // disable pagination
    pageLength: 100,     // show 100 rows by default
    initComplete: function () {
        // Apply the search only for column filters
        this.api().columns().every(function (colIdx) {
            var column = this;
            
            // Skip action column
            if (colIdx === 17) return; 
            
            // Create header filters
            var cell = $('.filters th').eq($(column.header()).index());
            var title = $(column.header()).text();
            $(cell).html('<input type="text" placeholder="Filter ' + title + '" />');
            
            // On keyup in the filter input
            $('input', cell).on('keyup change', function () {
                if (column.search() !== this.value) {
                    column
                        .search(this.value)
                        .draw();
                }
            });
        });
    }
});

    
    // Calculate and update summary cards
    function updateSummaryCards() {
        let totalIncrement = 0;
        let totalIncentive = 0;
        let totalAdvance = 0;
        let totalExtra = 0;
        let totalNetPayable = 0;
        
        // Loop through all table rows
        $('#payrollTable tbody tr').each(function() {
            const row = $(this);
            
            // Skip if it's the "no data" row
            if (row.find('td[colspan]').length > 0) {
                return;
            }
            
            // Get values from table cells (remove commas and parse as float)
            const increment = parseFloat(row.find('.increment').text().replace(/[₹,]/g, '')) || 0;
            const incentive = parseFloat(row.find('.incentive-input').val()) || 0;
            const advance = parseFloat(row.find('.advance-input').val()) || 0;
            const extra = parseFloat(row.find('.extra-input').val()) || 0;
            const payable = parseFloat(row.find('.payable').text().replace(/[₹,]/g, '')) || 0;
            
            totalIncrement += increment;
            totalIncentive += incentive;
            totalAdvance += advance;
            totalExtra += extra;
            totalNetPayable += payable;
        });
        
        // Update the summary cards with formatted values
        $('#totalIncrement').text('₹' + totalIncrement.toLocaleString('en-IN'));
        $('#totalIncentive').text('₹' + totalIncentive.toLocaleString('en-IN'));
        $('#totalAdvance').text('₹' + totalAdvance.toLocaleString('en-IN'));
        $('#totalExtra').text('₹' + totalExtra.toLocaleString('en-IN'));
        $('#totalNetPayable').text('₹' + totalNetPayable.toLocaleString('en-IN'));
    }
    
    // Update summary cards on page load
    updateSummaryCards();
    
    // Update summary cards when input values change
    $(document).on('input', '.incentive-input, .advance-input, .extra-input', function() {
        updateSummaryCards();
    });
    
    // Tab switching functionality
    let currentActiveTab = 'payroll';
    let archiveDataLoaded = false;
    let contractualDataLoaded = false;
    
    // Handle tab switching
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        const targetTab = $(e.target).attr('data-bs-target');
        const tabName = targetTab.replace('#', '').replace('-content', '');
        
        currentActiveTab = tabName;
        
        // Get current month based on active tab
        let selectedMonth;
        if (tabName === 'contractual') {
            selectedMonth = $('#contractualMonthSelect').val();
        } else {
            selectedMonth = $('#monthSelect').val();
        }
        
        // Load data only if not already loaded (archive data is cumulative across months)
        if (tabName === 'archive' && !archiveDataLoaded) {
            loadArchiveData(selectedMonth);
        } else if (tabName === 'contractual' && !contractualDataLoaded) {
            loadContractualData(selectedMonth);
        }
    });
    
    // Load archive data
    function loadArchiveData(month) {
        showLoading('archive');
        
        $.ajax({
            url: '{{ route("payroll.api.archive") }}',
            method: 'GET',
            data: { month: month },
            success: function(response) {
                if (response.success) {
                    populateArchiveTable(response.data);
                    updateArchiveCount(response.data.length);
                    archiveDataLoaded = true;
                } else {
                    showAlert('archive', 'Error loading archive data: ' + response.message, 'danger');
                }
            },
            error: function(xhr) {
                showAlert('archive', 'Error loading archive data. Please try again.', 'danger');
                console.error('Archive data load error:', xhr);
            },
            complete: function() {
                hideLoading('archive');
            }
        });
    }
    
    // Load contractual data
    function loadContractualData(month) {
        showLoading('contractual');
        
        $.ajax({
            url: '{{ route("payroll.api.contractual") }}',
            method: 'GET',
            data: { month: month },
            success: function(response) {
                if (response.success) {
                    populateContractualTable(response.data);
                    updateContractualCount(response.data.length);
                    contractualDataLoaded = true;
                } else {
                    showAlert('contractual', 'Error loading contractual data: ' + response.message, 'danger');
                }
            },
            error: function(xhr) {
                showAlert('contractual', 'Error loading contractual data. Please try again.', 'danger');
                console.error('Contractual data load error:', xhr);
            },
            complete: function() {
                hideLoading('contractual');
            }
        });
    }
    
    // Populate archive table
    function populateArchiveTable(data) {
        const tbody = $('#archive-table-body');
        tbody.empty();
        
        if (data.length === 0) {
            $('#archive-alert').addClass('d-none');
            $('#archive-empty').removeClass('d-none');
            tbody.append('<tr><td colspan="14" class="text-center text-muted py-4"><i class="bi bi-inbox"></i><br>No archived employees found.</td></tr>');
        } else {
            $('#archive-alert').removeClass('d-none');
            $('#archive-empty').addClass('d-none');
            
            data.forEach(payroll => {
                const row = `
                    <tr class="archive-row" data-id="${payroll.id}">
                        <td class="name">${payroll.name || 'N/A'}</td>
                        <td class="dept">${payroll.department || 'N/A'}</td>
                        <td class="email_address">${payroll.email_address || 'N/A'}</td>
                        <td class="month">${payroll.month || 'August 2025'}</td>
                        <td class="sal_previous">${payroll.sal_previous ? Number(payroll.sal_previous).toLocaleString() : '0'}</td>
                        <td class="increment">${payroll.increment ? Number(payroll.increment).toLocaleString() : '0'}</td>
                        <td class="salary_current">${payroll.salary_current ? Number(payroll.salary_current).toLocaleString() : '0'}</td>
                        <td class="productive_hrs">${payroll.productive_hrs || '0'}</td>
                        <td class="incentive">${payroll.incentive ? Number(payroll.incentive).toLocaleString() : '0'}</td>
                        <td class="payable">${payroll.payable ? Number(Math.round(payroll.payable)).toLocaleString() : '0'}</td>
                        <td class="advance">${payroll.advance ? Number(payroll.advance).toLocaleString() : '0'}</td>
                        <td class="total_payable">${payroll.total_payable ? Number(Math.round(payroll.total_payable)).toLocaleString() : '0'}</td>
                        <td class="payment_done">
                            ${payroll.payment_done ? '<span class="badge bg-success">✓</span>' : '<span class="badge bg-warning">Pending</span>'}
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" 
                                        class="btn btn-sm btn-success restore-btn" 
                                        data-id="${payroll.id}" 
                                        data-employee-id="${payroll.employee_id}" 
                                        data-name="${payroll.name}"
                                        title="Restore to Active Payroll">
                                    <i class="bi bi-arrow-clockwise"></i> Restore Payroll
                                </button>
                                <button type="button" 
                                        class="btn btn-sm btn-warning move-to-contractual-btn" 
                                        data-id="${payroll.id}" 
                                        data-employee-id="${payroll.employee_id}" 
                                        data-name="${payroll.name}"
                                        title="Move to Contractual">
                                    <i class="bi bi-briefcase"></i> Move to Contractual
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                tbody.append(row);
            });
        }
    }
    
    // Populate contractual table
    function populateContractualTable(data) {
        const tbody = $('#contractual-table-body');
        tbody.empty();
        
        if (data.length === 0) {
            $('#contractual-alert').addClass('d-none');
            $('#contractual-empty').removeClass('d-none');
            tbody.append('<tr><td colspan="11" class="text-center text-muted py-4"><i class="bi bi-inbox"></i><br>No contractual employees found.</td></tr>');
        } else {
            $('#contractual-alert').removeClass('d-none');
            $('#contractual-empty').addClass('d-none');
            
            data.forEach(payroll => {
                // Calculate payable: Number of Videos * Rate
                const blogsVideos = parseFloat(payroll.number_of_blogs_videos) || 0;
                const rate = parseFloat(payroll.rate) || 0;
                const advance = parseFloat(payroll.advance) || 0;
                const calculatedPayable = blogsVideos * rate;
                const calculatedTotalPayable = calculatedPayable - advance;
                
                const row = `
                    <tr class="contractual-row" data-id="${payroll.id}">
                        <td>${payroll.name || 'N/A'}</td>
                        <td>${payroll.department || 'N/A'}</td>
                        <td>${payroll.email_address || 'N/A'}</td>
                        <td>${payroll.month || 'N/A'}</td>
                        <td class="blogs-videos-cell">
                            <input type="number" class="form-control form-control-sm blogs-videos-input" 
                                   data-payroll-id="${payroll.id}" 
                                   value="${blogsVideos}" 
                                   min="0" 
                                   style="width: 80px; display: inline-block;">
                        </td>
                        <td class="rate-cell">
                            <input type="number" class="form-control form-control-sm rate-input" 
                                   data-payroll-id="${payroll.id}" 
                                   value="${rate}" 
                                   min="0" 
                                   step="0.01"
                                   style="width: 100px; display: inline-block;">
                        </td>
                        <td class="advance-cell">
                            <input type="number" class="form-control form-control-sm advance-input" 
                                   data-payroll-id="${payroll.id}" 
                                   value="${advance}" 
                                   min="0" 
                                   step="0.01"
                                   style="width: 100px; display: inline-block;">
                        </td>
                        <td>₹${Math.round(calculatedPayable).toLocaleString('en-IN')}</td>
                        <td>₹${Math.round(calculatedTotalPayable).toLocaleString('en-IN')}</td>
                        <td>
                            ${payroll.payment_done ? 
                                '<span class="badge bg-success">Done</span>' : 
                                '<span class="badge bg-warning">Pending</span>'
                            }
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary btn-sm restore-contractual-btn" 
                                        data-id="${payroll.id}" data-name="${payroll.name}" title="Restore to Payroll">
                                    <i class="bi bi-arrow-clockwise"></i> Restore Payroll
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm move-to-archive-btn" 
                                        data-id="${payroll.id}" data-name="${payroll.name}" title="Move to Archive">
                                    <i class="bi bi-archive"></i> Move to Archive
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                tbody.append(row);
            });
        }
    }
    
    // Update archive count
    function updateArchiveCount(count) {
        $('#archive-count').text(count);
    }
    
    // Update contractual count
    function updateContractualCount(count) {
        $('#contractual-count').text(count);
    }
    
    // Show loading overlay
    function showLoading(tab) {
        $(`#${tab}-loading`).removeClass('d-none');
    }
    
    // Hide loading overlay
    function hideLoading(tab) {
        $(`#${tab}-loading`).addClass('d-none');
    }
    
    // Show alert
    function showAlert(tab, message, type = 'info') {
        const alertDiv = $(`<div class="alert alert-${type} alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>`);
        $(`#${tab}-content .card-body`).prepend(alertDiv);
        setTimeout(() => alertDiv.remove(), 5000);
    }
    
    // Handle restore button click in archive tab
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
                            
                            // Update count
                            const remainingRows = $('#archive-table-body tr').length - 1;
                            updateArchiveCount(remainingRows);
                            
                            // Check if table is empty
                            if (remainingRows === 0) {
                                $('#archive-alert').addClass('d-none');
                                $('#archive-empty').removeClass('d-none');
                                $('#archive-table-body').append('<tr><td colspan="14" class="text-center text-muted py-4"><i class="bi bi-inbox"></i><br>No archived employees found.</td></tr>');
                            }
                        });
                        
                        // Reset payroll tab data flag so it reloads fresh data
                        if (currentActiveTab === 'payroll') {
                            // Optionally reload payroll data or let user manually refresh
                        }
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
    
    // Handle move to contractual button click
    $(document).on('click', '.move-to-contractual-btn', function() {
        const button = $(this);
        const id = button.data('id');
        const employeeName = button.data('name');
        const row = button.closest('tr');
        
        if(confirm(`Are you sure you want to move "${employeeName}" from archive to contractual status?`)) {
            // Show loading state
            button.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Moving...');
            
            $.ajax({
                url: `{{ route('payroll.move-to-contractual', ':id') }}`.replace(':id', id),
                method: 'POST',
                success: function(response) {
                    if(response.success) {
                        // Show success message
                        const alertDiv = $('<div class="alert alert-success alert-dismissible fade show">' +
                            '<i class="bi bi-check-circle"></i>' +
                            '<strong>Success!</strong> "' + employeeName + '" has been moved from archive to contractual status.' +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                            '</div>');
                        $('.main-content').prepend(alertDiv);
                        setTimeout(() => alertDiv.remove(), 3000);
                        
                        // Remove the row from archive table
                        row.fadeOut(300, function() {
                            $(this).remove();
                            
                            // Update count
                            const remainingRows = $('#archive-table-body tr').length - 1;
                            updateArchiveCount(remainingRows);
                            
                            // Check if table is empty
                            if (remainingRows === 0) {
                                $('#archive-alert').addClass('d-none');
                                $('#archive-empty').removeClass('d-none');
                                $('#archive-table-body').append('<tr><td colspan="14" class="text-center text-muted py-4"><i class="bi bi-inbox"></i><br>No archived employees found.</td></tr>');
                            }
                        });
                        
                        // Reset contractual tab data flag so it reloads fresh data
                        contractualDataLoaded = false;
                        
                        // If currently on contractual tab, reload data
                        if (currentActiveTab === 'contractual') {
                            const selectedMonth = $('#contractualMonthSelect').val();
                            contractualDataLoaded = false; // Reset flag to force reload
                            loadContractualData(selectedMonth);
                        }
                    } else {
                        alert('Error: ' + (response.message || 'Failed to move employee to contractual'));
                        button.prop('disabled', false).html('<i class="bi bi-briefcase"></i> Move to Contractual');
                    }
                },
                error: function(xhr) {
                    console.error('Move to contractual error:', xhr);
                    alert('Error moving employee to contractual: ' + (xhr.responseJSON?.message || 'Server error'));
                    button.prop('disabled', false).html('<i class="bi bi-briefcase"></i> Move to Contractual');
                }
            });
        }
    });
    
    // Handle restore from contractual button click
    $(document).on('click', '.restore-contractual-btn', function() {
        const button = $(this);
        const id = button.data('id');
        const employeeName = button.data('name');
        const row = button.closest('tr');
        
        if(confirm(`Are you sure you want to restore "${employeeName}" back to active payroll from contractual status?`)) {
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
                            '<strong>Success!</strong> "' + employeeName + '" has been restored to active payroll from contractual status.' +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                            '</div>');
                        $('.main-content').prepend(alertDiv);
                        setTimeout(() => alertDiv.remove(), 3000);
                        
                        // Remove the row from contractual table
                        row.fadeOut(300, function() {
                            $(this).remove();
                            
                            // Update count
                            const remainingRows = $('#contractual-table-body tr').length - 1;
                            updateContractualCount(remainingRows);
                            
                            // Check if table is empty
                            if (remainingRows === 0) {
                                $('#contractual-alert').addClass('d-none');
                                $('#contractual-empty').removeClass('d-none');
                                $('#contractual-table-body').append('<tr><td colspan="14" class="text-center text-muted py-4"><i class="bi bi-inbox"></i><br>No contractual employees found.</td></tr>');
                            }
                        });
                    } else {
                        alert('Error: ' + (response.message || 'Failed to restore employee'));
                        button.prop('disabled', false).html('<i class="bi bi-arrow-clockwise"></i> Restore');
                    }
                },
                error: function(xhr) {
                    console.error('Restore from contractual error:', xhr);
                    alert('Error restoring employee: ' + (xhr.responseJSON?.message || 'Server error'));
                    button.prop('disabled', false).html('<i class="bi bi-arrow-clockwise"></i> Restore');
                }
            });
        }
    });
    
    // Handle move to archive button click
    $(document).on('click', '.move-to-archive-btn', function() {
        const button = $(this);
        const id = button.data('id');
        const employeeName = button.data('name');
        const row = button.closest('tr');
        
        if(confirm(`Are you sure you want to move "${employeeName}" from contractual to archive status?`)) {
            // Show loading state
            button.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Moving...');
            
            $.ajax({
                url: `{{ route('payroll.move-to-archive', ':id') }}`.replace(':id', id),
                method: 'POST',
                success: function(response) {
                    if(response.success) {
                        // Show success message
                        const alertDiv = $('<div class="alert alert-success alert-dismissible fade show">' +
                            '<i class="bi bi-check-circle"></i>' +
                            '<strong>Success!</strong> "' + employeeName + '" has been moved from contractual to archive status.' +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                            '</div>');
                        $('.main-content').prepend(alertDiv);
                        setTimeout(() => alertDiv.remove(), 3000);
                        
                        // Remove the row from contractual table
                        row.fadeOut(300, function() {
                            $(this).remove();
                            
                            // Update count
                            const remainingRows = $('#contractual-table-body tr').length - 1;
                            updateContractualCount(remainingRows);
                            
                            // Check if table is empty
                            if (remainingRows === 0) {
                                $('#contractual-alert').addClass('d-none');
                                $('#contractual-empty').removeClass('d-none');
                                $('#contractual-table-body').append('<tr><td colspan="14" class="text-center text-muted py-4"><i class="bi bi-inbox"></i><br>No contractual employees found.</td></tr>');
                            }
                        });
                        
                        // Reset archive tab data flag so it reloads fresh data
                        archiveDataLoaded = false;
                        
                        // If currently on archive tab, reload data
                        if (currentActiveTab === 'archive') {
                            const selectedMonth = $('#monthSelect').val();
                            loadArchiveData(selectedMonth);
                        }
                    } else {
                        alert('Error: ' + (response.message || 'Failed to move employee to archive'));
                        button.prop('disabled', false).html('<i class="bi bi-archive"></i> Move to Archive');
                    }
                },
                error: function(xhr) {
                    console.error('Move to archive error:', xhr);
                    alert('Error moving employee to archive: ' + (xhr.responseJSON?.message || 'Server error'));
                    button.prop('disabled', false).html('<i class="bi bi-archive"></i> Move to Archive');
                }
            });
        }
    });
    
    // Month dropdown change handler (existing one - modified for tab support)
    $('#monthSelect').on('change', function() {
        const selectedMonth = $(this).val();
        
        // Reset loaded flags for non-payroll tabs to ensure fresh data
        archiveDataLoaded = false;
        contractualDataLoaded = false;
        
        // Handle based on current active tab
        if (currentActiveTab === 'payroll') {
            // Show loading state for payroll
            $('#payrollTable tbody').html('<tr><td colspan="25" class="text-center">Loading...</td></tr>');
            
            // Get today's current month
            const today = new Date();
            const currentMonthName = today.toLocaleString('default', { month: 'long' });
            const currentYear = today.getFullYear();
            const todayMonth = `${currentMonthName} ${currentYear}`;
            
            // Get previous month
            const prevMonth = getPreviousMonth(selectedMonth);
            
            // Only ask about copying bank details if selected month equals today's current month
            if (selectedMonth === todayMonth) {
                // Ask if bank details should be copied
                if (confirm(`Would you like to copy Bank 1, Bank 2, and UPI details from ${prevMonth} to ${selectedMonth}?`)) {
                    // Show loading state
                    $('#payrollTable tbody').html('<tr><td colspan="25" class="text-center">Copying bank details and loading data...</td></tr>');
                    
                    // Copy bank details before reloading page
                    $.ajax({
                        url: '{{ route("payroll.copy-bank-details") }}',
                        method: 'POST',
                        data: {
                            current_month: selectedMonth,
                            previous_month: prevMonth
                        },
                        complete: function() {
                            // Reload page with selected month for payroll tab
                            window.location.href = `{{ route('payroll.index') }}?month=${encodeURIComponent(selectedMonth)}`;
                        }
                    });
                } else {
                    // Reload page with selected month for payroll tab without copying bank details
                    window.location.href = `{{ route('payroll.index') }}?month=${encodeURIComponent(selectedMonth)}`;
                }
            } else {
                // For past months, just reload without asking about bank details
                window.location.href = `{{ route('payroll.index') }}?month=${encodeURIComponent(selectedMonth)}`;
            }
        } else if (currentActiveTab === 'archive') {
            // Reload archive data (will show cumulative archived employees)
            loadArchiveData(selectedMonth);
        } else if (currentActiveTab === 'contractual') {
            // Reload contractual data for new month using contractual month filter
            const contractualMonth = $('#contractualMonthSelect').val();
            contractualDataLoaded = false; // Reset flag to force reload
            loadContractualData(contractualMonth);
        }
    });
    
    // Contractual month filter change handler
    $('#contractualMonthSelect').on('change', function() {
        const selectedMonth = $(this).val();
        contractualDataLoaded = false; // Reset flag to force reload
        loadContractualData(selectedMonth);
    });
    
    // Initialize contractual month filter with main month filter value on page load
    $(document).ready(function() {
        // Get month from URL parameter or main month filter
        const urlParams = new URLSearchParams(window.location.search);
        const monthFromUrl = urlParams.get('month');
        const mainMonth = monthFromUrl || $('#monthSelect').val();
        
        if (mainMonth) {
            $('#contractualMonthSelect').val(mainMonth);
        }
    });
    
    // Load employees on page load
    loadEmployees();
    
    function loadEmployees() {
        $.ajax({
            url: '{{ route("payroll.employees") }}',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const select = $('#employeeSelect');
                    select.empty().append('<option value="">Select Employee</option>');
                    
                    response.employees.forEach(function(employee) {
                        select.append(`<option value="${employee.id}" data-name="${employee.name}" data-email="${employee.email}" data-department="${employee.department_name || 'N/A'}">${employee.name} (${employee.email})</option>`);
                    });
                }
            },
            error: function(xhr) {
                console.error('Error loading employees:', xhr.responseText);
                alert('Error loading employees. Please refresh the page.');
            }
        });
    }
    
    // Handle employee selection
    $('#employeeSelect').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        if (selectedOption.val()) {
            $('#employeeName').val(selectedOption.data('name'));
            $('#dept').val(selectedOption.data('department'));
            $('#emailAddress').val(selectedOption.data('email'));
            
            // Only trigger calculations if we're in add mode (not edit mode)
            if (!editMode) {
                calculateSalary();
            }
        } else {
            $('#employeeName').val('');
            $('#dept').val('');
            $('#emailAddress').val('');
        }
    });
    
    // Calculate Current Salary, Payable, and Total Payable automatically
    function calculateSalary() {
        const salPrevious = parseFloat($('#salPrevious').val()) || 0;
        const increment = parseFloat($('#increment').val()) || 0;
        const salaryCurrent = salPrevious + increment;
        $('#salaryCurrent').val(salaryCurrent);
        
        calculatePayable();
    }
    
// In payroll.blade.php - update the JavaScript to handle teamlogger data
// Add this to your calculatePayable function
function calculatePayable() {
    const salaryCurrent = parseFloat($('#salaryCurrent').val()) || 0;
    const approvedHrs = parseFloat($('#approvedHrs').val()) || 0;
    const incentive = parseFloat($('#incentive').val()) || 0;
    
    // For modal form calculations, always calculate payable regardless of approval status
    // This allows users to see the calculated amounts before finalizing
    let payable = 0;
    if (approvedHrs > 0) {
        // Formula: (Current Salary * Approved Hours / 200) + Incentive
        payable = (salaryCurrent * approvedHrs / 200) + incentive;
    } else if (incentive > 0) {
        // If no approved hours but there's incentive, still show the incentive
        payable = incentive;
    }
    
    $('#payable').val(Math.round(payable));
    
    calculateTotalPayable();
}
    
    function calculateTotalPayable() {
        const payable = parseFloat($('#payable').val()) || 0;
        const incentive = parseFloat($('#incentive').val()) || 0;
        const advance = parseFloat($('#advance').val()) || 0;
        const extra = parseFloat($('#extra').val()) || 0;
        // Total Payable = Payable + Incentive - Advance + Extra
        const totalPayable = payable + incentive - advance + extra;
        $('#totalPayable').val(totalPayable);
    }
    
    // Bind calculation to input changes
    $('#salPrevious, #increment').on('input', calculateSalary);
    $('#approvedHrs, #incentive').on('input', calculatePayable); // Include approvedHrs for payable calculation
    $('#advance').on('input', calculateTotalPayable);
    
    // Also bind to change events for better responsiveness in modal
    $('#incentive').on('change blur', calculatePayable);
    $('#approvedHrs').on('change blur', calculatePayable);
    $('#approvalStatus').on('change', calculatePayable); // Approval status change affects payable
    $('#advance').on('change blur', calculateTotalPayable);
    
    // Handle approved hours input change
    $(document).on('change blur', '.approved-hrs-input', function() {
        const input = $(this);
        const payrollId = input.data('payroll-id');
        const employeeId = input.data('employee-id');
        const approvedHrs = parseInt(input.val()) || 0;
        const row = input.closest('tr');
        
        // Show loading state
        input.prop('disabled', true);
        
        // Get current approval status and update payable amount
        const currentStatus = row.find('.approval-status-dropdown').val() || 'pending';
        updatePayableAmount(row, currentStatus);
        
        // Save approved hours via AJAX
        $.ajax({
            url: '{{ route("payroll.update-approved-hours") }}',
            method: 'POST',
            data: {
                payroll_id: payrollId,
                employee_id: employeeId,
                approved_hrs: approvedHrs
            },
            success: function(response) {
                if (response.success) {
                    // Update the input with the saved value
                    input.val(approvedHrs);
                    
                    // Update payable amounts directly from server response
                    if (response.payable !== undefined && response.total_payable !== undefined) {
                        row.find('.payable').text(response.payable.toLocaleString());
                        row.find('.total_payable').text(response.total_payable.toLocaleString());
                    }
                    
                    // Show brief success feedback
                    input.css('border-color', '#28a745');
                    setTimeout(() => {
                        input.css('border-color', '');
                    }, 1500);
                    
                    console.log('Approved hours updated successfully');
                } else {
                    alert('Error: ' + (response.message || 'Failed to update approved hours'));
                    // Reset to previous value
                    input.val(input.data('previous-value') || 0);
                    // Recalculate with previous value
                    updatePayableAmount(row, currentStatus);
                }
            },
            error: function(xhr) {
                let errorMessage = 'Error updating approved hours. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert(errorMessage);
                console.error('Update approved hours error:', xhr.responseText);
                
                // Reset to previous value
                input.val(input.data('previous-value') || 0);
                // Recalculate with previous value
                updatePayableAmount(row, currentStatus);
            },
            complete: function() {
                input.prop('disabled', false);
            }
        });
    });
    
    // Store previous value when input gets focus
    $(document).on('focus', '.approved-hrs-input', function() {
        $(this).data('previous-value', $(this).val());
    });
    
    // Handle extra amount input change
    $(document).on('change blur', '.extra-input', function() {
        const input = $(this);
        const payrollId = input.data('payroll-id');
        const employeeId = input.data('employee-id');
        const extraAmount = parseFloat(input.val()) || 0;
        const row = input.closest('tr');
        
        // Show loading state
        input.prop('disabled', true);
        
        // Get the current approval status
        const approvalStatusDropdown = row.find('.approval-status-dropdown');
        const approvalStatus = approvalStatusDropdown.val() || 'pending';
        
        // Calculate and update total payable amount immediately
        const payableText = row.find('.payable').text().replace(/,/g, '').trim();
        const payable = parseFloat(payableText) || 0;
        const advanceInput = row.find('.advance-input').val() || '0';
        const advance = parseFloat(advanceInput) || 0;
        
        // Only calculate total payable if approved AND payable > 0
        let newTotalPayable = 0;
        if (approvalStatus === 'approved' && payable > 0) {
            newTotalPayable = payable - advance + extraAmount;
        } else {
            // If not approved or payable is 0, total payable should be 0 regardless of advance/extra
            newTotalPayable = 0;
        }
        
        // Update display immediately
        row.find('.total_payable').text(Math.round(newTotalPayable).toLocaleString());
        
        // Save extra amount via AJAX
        $.ajax({
            url: '{{ route("payroll.update-extra") }}',
            method: 'POST',
            data: {
                payroll_id: payrollId,
                employee_id: employeeId,
                extra: extraAmount
            },
            success: function(response) {
                if (response.success) {
                    // Update the input with the saved value
                    input.val(extraAmount);
                    
                    // Update total payable from server response
                    if (response.total_payable !== undefined) {
                        row.find('.total_payable').text(response.total_payable.toLocaleString());
                    }
                    
                    // Show brief success feedback
                    input.css('border-color', '#28a745');
                    setTimeout(() => {
                        input.css('border-color', '');
                    }, 1500);
                    
                    console.log('Extra amount updated successfully');
                    
                    // Update summary cards
                    updateSummaryCards();
                } else {
                    alert('Error: ' + (response.message || 'Failed to update extra amount'));
                    // Reset to previous value and recalculate
                    input.val(input.data('previous-value') || 0);
                    const previousExtra = parseFloat(input.data('previous-value')) || 0;
                    let originalTotalPayable = 0;
                    if (payable > 0) {
                        originalTotalPayable = payable - advance + previousExtra;
                    } else {
                        originalTotalPayable = 0;
                    }
                    row.find('.total_payable').text(Math.round(originalTotalPayable).toLocaleString());
                }
            },
            error: function(xhr) {
                let errorMessage = 'Error updating extra amount. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert(errorMessage);
                console.error('Update extra amount error:', xhr.responseText);
                
                // Reset to previous value and recalculate
                input.val(input.data('previous-value') || 0);
                const previousExtra = parseFloat(input.data('previous-value')) || 0;
                let originalTotalPayable = 0;
                if (payable > 0) {
                    originalTotalPayable = payable - advance + previousExtra;
                } else {
                    originalTotalPayable = 0;
                }
                row.find('.total_payable').text(Math.round(originalTotalPayable).toLocaleString());
            },
            complete: function() {
                input.prop('disabled', false);
            }
        });
    });
    
    // Store previous value when extra input gets focus
    $(document).on('focus', '.extra-input', function() {
        $(this).data('previous-value', $(this).val());
    });
    
    // Handle incentive input change
    $(document).on('change blur', '.incentive-input', function() {
        const input = $(this);
        const payrollId = input.data('payroll-id');
        const employeeId = input.data('employee-id');
        const incentiveAmount = parseInt(input.val()) || 0;
        const row = input.closest('tr');
        
        // Show loading state
        input.prop('disabled', true);
        
        // Save incentive amount via AJAX
        $.ajax({
            url: '{{ route("payroll.update-incentive") }}',
            method: 'POST',
            data: {
                payroll_id: payrollId,
                employee_id: employeeId,
                incentive: incentiveAmount
            },
            success: function(response) {
                if (response.success) {
                    // Update the input with the saved value
                    input.val(incentiveAmount);
                    
                    // Update payable and total payable from server response
                    if (response.payable !== undefined && response.total_payable !== undefined) {
                        row.find('.payable').text(response.payable.toLocaleString());
                        row.find('.total_payable').text(response.total_payable.toLocaleString());
                    }
                    
                    // Show brief success feedback
                    input.css('border-color', '#28a745');
                    setTimeout(() => {
                        input.css('border-color', '');
                    }, 1500);
                    
                    console.log('Incentive amount updated successfully');
                    
                    // Update summary cards
                    updateSummaryCards();
                } else {
                    alert('Error: ' + (response.message || 'Failed to update incentive amount'));
                    // Reset to previous value
                    input.val(input.data('previous-value') || 0);
                }
            },
            error: function(xhr) {
                let errorMessage = 'Error updating incentive amount. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert(errorMessage);
                console.error('Update incentive amount error:', xhr.responseText);
                
                // Reset to previous value
                input.val(input.data('previous-value') || 0);
            },
            complete: function() {
                input.prop('disabled', false);
            }
        });
    });
    
    // Store previous value when incentive input gets focus
    $(document).on('focus', '.incentive-input', function() {
        $(this).data('previous-value', $(this).val());
    });
    
    // Handle advance input change
    $(document).on('change blur', '.advance-input', function() {
        const input = $(this);
        const payrollId = input.data('payroll-id');
        const employeeId = input.data('employee-id');
        const advanceAmount = parseInt(input.val()) || 0;
        const row = input.closest('tr');
        
        // Show loading state
        input.prop('disabled', true);
        
        // Get the current approval status
        const approvalStatusDropdown = row.find('.approval-status-dropdown');
        const approvalStatus = approvalStatusDropdown.val() || 'pending';
        
        // Calculate and update total payable amount immediately
        const payableText = row.find('.payable').text().replace(/,/g, '').trim();
        const payable = parseFloat(payableText) || 0;
        const extraText = row.find('.extra-input').val() || '0';
        const extra = parseFloat(extraText) || 0;
        
        // Only calculate total payable if approved AND payable > 0
        let newTotalPayable = 0;
        if (approvalStatus === 'approved' && payable > 0) {
            newTotalPayable = payable - advanceAmount + extra;
        } else {
            // If not approved or payable is 0, total payable should be 0 regardless of advance/extra
            newTotalPayable = 0;
        }
        
        // Update display immediately
        row.find('.total_payable').text(Math.round(newTotalPayable).toLocaleString());
        
        // Save advance amount via AJAX
        $.ajax({
            url: '{{ route("payroll.update-advance") }}',
            method: 'POST',
            data: {
                payroll_id: payrollId,
                employee_id: employeeId,
                advance: advanceAmount
            },
            success: function(response) {
                if (response.success) {
                    // Update the input with the saved value
                    input.val(advanceAmount);
                    
                    // Update total payable from server response
                    if (response.total_payable !== undefined) {
                        row.find('.total_payable').text(response.total_payable.toLocaleString());
                    }
                    
                    // Show brief success feedback
                    input.css('border-color', '#28a745');
                    setTimeout(() => {
                        input.css('border-color', '');
                    }, 1500);
                    
                    console.log('Advance amount updated successfully');
                    
                    // Update summary cards
                    updateSummaryCards();
                } else {
                    alert('Error: ' + (response.message || 'Failed to update advance amount'));
                    // Reset to previous value and recalculate
                    input.val(input.data('previous-value') || 0);
                    const previousAdvance = parseFloat(input.data('previous-value')) || 0;
                    let originalTotalPayable = 0;
                    if (payable > 0) {
                        originalTotalPayable = payable - previousAdvance + extra;
                    } else {
                        originalTotalPayable = 0;
                    }
                    row.find('.total_payable').text(Math.round(originalTotalPayable).toLocaleString());
                }
            },
            error: function(xhr) {
                let errorMessage = 'Error updating advance amount. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert(errorMessage);
                console.error('Update advance amount error:', xhr.responseText);
                
                // Reset to previous value and recalculate
                input.val(input.data('previous-value') || 0);
                const previousAdvance = parseFloat(input.data('previous-value')) || 0;
                let originalTotalPayable = 0;
                if (payable > 0) {
                    originalTotalPayable = payable - previousAdvance + extra;
                } else {
                    originalTotalPayable = 0;
                }
                row.find('.total_payable').text(Math.round(originalTotalPayable).toLocaleString());
            },
            complete: function() {
                input.prop('disabled', false);
            }
        });
    });
    
    // Store previous value when advance input gets focus
    $(document).on('focus', '.advance-input', function() {
        $(this).data('previous-value', $(this).val());
    });
    
    // Handle bank1 input change
    $(document).on('change blur', '.bank1-input', function() {
        const input = $(this);
        const payrollId = input.data('payroll-id');
        const employeeId = input.data('employee-id');
        const bank1Value = input.val().trim();
        const row = input.closest('tr');
        
        // Show loading state
        input.prop('disabled', true);
        
        // Save bank1 value via AJAX
        $.ajax({
            url: '{{ route("payroll.update-bank1") }}',
            method: 'POST',
            data: {
                payroll_id: payrollId,
                employee_id: employeeId,
                bank1: bank1Value
            },
            success: function(response) {
                if (response.success) {
                    // Update the input with the saved value
                    input.val(bank1Value);
                    
                    // Show brief success feedback
                    input.css('border-color', '#28a745');
                    setTimeout(() => {
                        input.css('border-color', '');
                    }, 1500);
                    
                    console.log('Bank 1 account updated successfully');
                } else {
                    alert('Error: ' + (response.message || 'Failed to update Bank 1 account'));
                    // Reset to previous value
                    input.val(input.data('previous-value') || '');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Error updating Bank 1 account. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert(errorMessage);
                console.error('Update Bank 1 account error:', xhr.responseText);
                
                // Reset to previous value
                input.val(input.data('previous-value') || '');
            },
            complete: function() {
                input.prop('disabled', false);
            }
        });
    });
    
    // Store previous value when bank1 input gets focus
    $(document).on('focus', '.bank1-input', function() {
        $(this).data('previous-value', $(this).val());
    });
    
    // Handle bank2 input change
    $(document).on('change blur', '.bank2-input', function() {
        const input = $(this);
        const payrollId = input.data('payroll-id');
        const employeeId = input.data('employee-id');
        const bank2Value = input.val().trim();
        const row = input.closest('tr');
        
        // Show loading state
        input.prop('disabled', true);
        
        // Save bank2 value via AJAX
        $.ajax({
            url: '{{ route("payroll.update-bank2") }}',
            method: 'POST',
            data: {
                payroll_id: payrollId,
                employee_id: employeeId,
                bank2: bank2Value
            },
            success: function(response) {
                if (response.success) {
                    // Update the input with the saved value
                    input.val(bank2Value);
                    
                    // Show brief success feedback
                    input.css('border-color', '#28a745');
                    setTimeout(() => {
                        input.css('border-color', '');
                    }, 1500);
                    
                    console.log('Bank 2 account updated successfully');
                } else {
                    alert('Error: ' + (response.message || 'Failed to update Bank 2 account'));
                    // Reset to previous value
                    input.val(input.data('previous-value') || '');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Error updating Bank 2 account. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert(errorMessage);
                console.error('Update Bank 2 account error:', xhr.responseText);
                
                // Reset to previous value
                input.val(input.data('previous-value') || '');
            },
            complete: function() {
                input.prop('disabled', false);
            }
        });
    });
    
    // Store previous value when bank2 input gets focus
    $(document).on('focus', '.bank2-input', function() {
        $(this).data('previous-value', $(this).val());
    });
    
    // Handle approval status dropdown change
    $(document).on('change', '.approval-status-dropdown', function() {
        const dropdown = $(this);
        const payrollId = dropdown.data('payroll-id');
        const employeeId = dropdown.data('employee-id');
        const approvalStatus = dropdown.val();
        const row = dropdown.closest('tr');
        
        // Show loading state
        dropdown.prop('disabled', true);
        
        // Calculate and update payable amount based on approval status
        updatePayableAmount(row, approvalStatus);
        
        // Save approval status via AJAX
        $.ajax({
            url: '{{ route("payroll.update-approval-status") }}',
            method: 'POST',
            data: {
                payroll_id: payrollId,
                employee_id: employeeId,
                approval_status: approvalStatus
            },
            success: function(response) {
                if (response.success) {
                    // Update the dropdown with the saved value
                    dropdown.val(approvalStatus);
                    
                    // Update payable amounts directly from server response
                    if (response.payable !== undefined && response.total_payable !== undefined) {
                        row.find('.payable').text(response.payable.toLocaleString());
                        row.find('.total_payable').text(response.total_payable.toLocaleString());
                    }
                    
                    // Show brief success feedback
                    dropdown.css('border-color', '#28a745');
                    setTimeout(() => {
                        dropdown.css('border-color', '');
                    }, 1500);
                    
                    console.log('Approval status updated successfully');
                } else {
                    alert('Error: ' + (response.message || 'Failed to update approval status'));
                    // Reset to previous value
                    dropdown.val(dropdown.data('previous-value') || 'pending');
                    // Recalculate with previous status
                    updatePayableAmount(row, dropdown.data('previous-value') || 'pending');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Error updating approval status. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert(errorMessage);
                console.error('Update approval status error:', xhr.responseText);
                
                // Reset to previous value
                dropdown.val(dropdown.data('previous-value') || 'pending');
                // Recalculate with previous status
                updatePayableAmount(row, dropdown.data('previous-value') || 'pending');
            },
            complete: function() {
                dropdown.prop('disabled', false);
            }
        });
    });
    
    // Function to update payable amount based on approval status
    function updatePayableAmount(row, approvalStatus) {
        const approvedHrsInput = row.find('.approved-hrs-input');
        const approvedHrs = parseFloat(approvedHrsInput.val()) || 0;
        
        // Get TL HR (productive hours) as fallback
        const productiveHrsText = row.find('.productive_hrs').text().trim();
        const productiveHrs = parseFloat(productiveHrsText) || 0;
        
        const salaryCurrentText = row.find('.salary_current').text().replace(/,/g, '').trim();
        const salaryCurrent = parseFloat(salaryCurrentText) || 0;
        // Get incentive from input field instead of text display
        const incentiveInput = row.find('.incentive-input').val() || 0;
        const incentive = parseFloat(incentiveInput) || 0;
        
        let payable = 0;
        let totalPayable = 0;
        
        // Only calculate if approved and has either approved hours or productive hours
        if (approvalStatus === 'approved') {
            // Use approved hours if entered, otherwise use productive hours as fallback
            const effectiveHours = approvedHrs > 0 ? approvedHrs : productiveHrs;
            
            if (effectiveHours > 0 && salaryCurrent > 0) {
                // Payable = (Salary × Hours / 200) - incentive is NOT included
                payable = (salaryCurrent * effectiveHours / 200);
                // Get advance from input field instead of text display
                const advanceInput = row.find('.advance-input').val() || 0;
                const advance = parseFloat(advanceInput) || 0;
                const extraText = row.find('.extra-input').val() || 0;
                const extra = parseFloat(extraText) || 0;
                // Total Payable = Payable + Incentive - Advance + Extra
                totalPayable = payable + incentive - advance + extra;
            }
        }
        
        // Update the display
        row.find('.payable').text(Math.round(payable).toLocaleString());
        row.find('.total_payable').text(Math.round(totalPayable).toLocaleString());
        
        // Save the updated payable amount via AJAX if needed
        if (row.data('id')) {
            updatePayableInDatabase(row.data('id'), Math.round(payable), Math.round(totalPayable));
        }
    }
    
    // Function to update payable in database
    function updatePayableInDatabase(payrollId, payable, totalPayable) {
        $.ajax({
            url: '{{ route("payroll.update", ":id") }}'.replace(':id', payrollId),
            method: 'PUT',
            data: {
                payable: payable,
                total_payable: totalPayable
            },
            success: function(response) {
                console.log('Payable updated successfully');
            },
            error: function(xhr) {
                console.error('Error updating payable:', xhr.responseText);
            }
        });
    }
    
    // Store previous value when dropdown gets focus
    $(document).on('focus', '.approval-status-dropdown', function() {
        $(this).data('previous-value', $(this).val());
    });
    
    // Update table row with new data
    function updateTableRow(payrollId, data) {
        const row = $(`tr[data-id="${payrollId}"]`);
        if (row.length) {
            row.find('.name').text(data.name || 'N/A');
            row.find('.dept').text(data.department || 'N/A');
            row.find('.email_address').text(data.email_address || 'N/A');
            row.find('.month').text(data.month || 'August 2025');
            row.find('.sal_previous').text(data.sal_previous ? data.sal_previous.toLocaleString() : '0');
            row.find('.increment').text(data.increment ? data.increment.toLocaleString() : '0');
            row.find('.salary_current').text(data.salary_current ? data.salary_current.toLocaleString() : '0');
            row.find('.productive_hrs').text(data.productive_hrs || '0');
            row.find('.incentive').text(data.incentive ? data.incentive.toLocaleString() : '0');
            row.find('.payable').text(data.payable ? Math.round(data.payable).toLocaleString() : '0');
            row.find('.advance').text(data.advance ? data.advance.toLocaleString() : '0');
            row.find('.total_payable').text(data.total_payable ? Math.round(data.total_payable).toLocaleString() : '0');
            
            // Update payment status
            const paymentStatusCell = row.find('.payment_done');
            if (data.payment_done) {
                paymentStatusCell.html('<span class="badge bg-success">✓</span>');
            } else {
                paymentStatusCell.html('<span class="badge bg-warning">Pending</span>');
            }
            
            // Update data-id if it was 'new' before
            if (row.attr('data-id') === 'new') {
                row.attr('data-id', data.id);
                // Update button data-ids
                row.find('.edit-payroll-btn, .delete-payroll-btn, .mark-done-btn').attr('data-id', data.id);
            }
        }
    }
    
    // Search/Filter logic
    function filterTable() {
        const name = $('#searchName').val().toLowerCase();
        const dept = $('#searchDept').val().toLowerCase();
        const email = $('#searchEmail').val().toLowerCase();
        const paymentStatus = $('#searchPaymentStatus').val();
        
        const rows = document.querySelectorAll('#payrollTable tbody tr');
        rows.forEach(row => {
            const nameText = row.querySelector('.name').textContent.toLowerCase();
            const deptText = row.querySelector('.dept').textContent.toLowerCase();
            const emailText = row.querySelector('.email_address').textContent.toLowerCase();
            const paymentStatusText = row.querySelector('.payment_done').textContent.trim();
            
            let show = true;
            if (name && !nameText.includes(name)) show = false;
            if (dept && !deptText.includes(dept)) show = false;
            if (email && !emailText.includes(email)) show = false;
            if (paymentStatus && paymentStatus === 'Done' && !paymentStatusText.includes('✓')) show = false;
            if (paymentStatus && paymentStatus === 'Pending' && paymentStatusText.includes('✓')) show = false;
            
            row.style.display = show ? '' : 'none';
        });
    }
    
    $('#searchName, #searchDept, #searchEmail, #searchPaymentStatus').on('input change', filterTable);
    
    // Print Button Handler
    $('#printPayrollBtn').on('click', function() {
        const selectedMonth = $('#monthSelect').val();
        const searchName = $('#searchName').val();
        const searchDept = $('#searchDept').val();
        const searchEmail = $('#searchEmail').val();
        const searchPaymentStatus = $('#searchPaymentStatus').val();
        
        // Show loading state
        const btn = $(this);
        const originalHtml = btn.html();
        btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Generating...');
        
        // Create form for POST request
        const form = $('<form>', {
            'method': 'POST',
            'action': '{{ route("payroll.print-pdf") }}',
            'target': '_blank'
        });
        
        // Add CSRF token
        form.append($('<input>', {
            'type': 'hidden',
            'name': '_token',
            'value': $('meta[name="csrf-token"]').attr('content')
        }));
        
        // Add form data
        form.append($('<input>', {
            'type': 'hidden',
            'name': 'month',
            'value': selectedMonth
        }));
        
        if (searchName) {
            form.append($('<input>', {
                'type': 'hidden',
                'name': 'searchName',
                'value': searchName
            }));
        }
        
        if (searchDept) {
            form.append($('<input>', {
                'type': 'hidden',
                'name': 'searchDept',
                'value': searchDept
            }));
        }
        
        if (searchEmail) {
            form.append($('<input>', {
                'type': 'hidden',
                'name': 'searchEmail',
                'value': searchEmail
            }));
        }
        
        if (searchPaymentStatus) {
            form.append($('<input>', {
                'type': 'hidden',
                'name': 'searchPaymentStatus',
                'value': searchPaymentStatus
            }));
        }
        
        // Append form to body and submit
        $('body').append(form);
        form.submit();
        form.remove();
        
        // Reset button state after a short delay
        setTimeout(() => {
            btn.prop('disabled', false).html(originalHtml);
        }, 2000);
    });
    
    // Copy Bank Details from Previous Month Button Handler
    $('#copyBankDetailsBtn').on('click', function() {
        if(!confirm("This will copy all Bank 1, Bank 2, and UPI details from the previous month to this month. Continue?")) {
            return;
        }
        
        // Show loading state
        const btn = $(this);
        const originalHtml = btn.html();
        btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i>');
        
        const currentMonth = $('#monthSelect').val();
        
        // Determine previous month
        let prevMonth = getPreviousMonth(currentMonth);
        
        $.ajax({
            url: '{{ route("payroll.copy-bank-details") }}',
            method: 'POST',
            data: {
                current_month: currentMonth,
                previous_month: prevMonth
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    const alertDiv = $(`<div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle"></i>
                        <strong>Success!</strong> Bank details have been copied from ${prevMonth} to ${currentMonth}.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>`);
                    $('.main-content').prepend(alertDiv);
                    
                    // Reload the page to show updated data
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    // Show error message
                    const alertDiv = $(`<div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Error!</strong> ${response.message || 'Failed to copy bank details.'}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>`);
                    $('.main-content').prepend(alertDiv);
                    
                    // Reset button state
                    btn.prop('disabled', false).html(originalHtml);
                }
            },
            error: function(xhr) {
                let errorMessage = 'Error copying bank details. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                // Show error message
                const alertDiv = $(`<div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Error!</strong> ${errorMessage}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>`);
                $('.main-content').prepend(alertDiv);
                
                // Reset button state
                btn.prop('disabled', false).html(originalHtml);
            }
        });
    });
    
    // Function to get previous month
    function getPreviousMonth(currentMonth) {
        // Example: "October 2025" -> "September 2025"
        const months = [
            "January", "February", "March", "April", "May", "June",
            "July", "August", "September", "October", "November", "December"
        ];
        
        // Parse current month
        const parts = currentMonth.split(' ');
        const currentMonthName = parts[0];
        const currentYear = parseInt(parts[1]);
        
        // Find current month index
        const currentMonthIndex = months.findIndex(month => month === currentMonthName);
        
        // Calculate previous month
        let prevMonthIndex = currentMonthIndex - 1;
        let prevYear = currentYear;
        
        if (prevMonthIndex < 0) {
            prevMonthIndex = 11; // December
            prevYear = currentYear - 1;
        }
        
        return months[prevMonthIndex] + " " + prevYear;
    }

// Export Button Handler
    $('#exportPayrollBtn').on('click', function() {
        const selectedMonth = $('#monthSelect').val();
        const searchName = $('#searchName').val();
        const searchDept = $('#searchDept').val();
        const searchEmail = $('#searchEmail').val();
        const searchPaymentStatus = $('#searchPaymentStatus').val();
        
        // Show loading state
        const btn = $(this);
        const originalHtml = btn.html();
        btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Exporting...');
        
        // Create form for POST request
        const form = $('<form>', {
            'method': 'POST',
            'action': '{{ route("payroll.export-excel") }}',
            'target': '_blank'
        });
        
        // Add CSRF token
        form.append($('<input>', {
            'type': 'hidden',
            'name': '_token',
            'value': $('meta[name="csrf-token"]').attr('content')
        }));
        
        // Add form data
        form.append($('<input>', {
            'type': 'hidden',
            'name': 'month',
            'value': selectedMonth
        }));
        
        if (searchName) {
            form.append($('<input>', {
                'type': 'hidden',
                'name': 'searchName',
                'value': searchName
            }));
        }
        
        if (searchDept) {
            form.append($('<input>', {
                'type': 'hidden',
                'name': 'searchDept',
                'value': searchDept
            }));
        }
        
        if (searchEmail) {
            form.append($('<input>', {
                'type': 'hidden',
                'name': 'searchEmail',
                'value': searchEmail
            }));
        }
        
        if (searchPaymentStatus) {
            form.append($('<input>', {
                'type': 'hidden',
                'name': 'searchPaymentStatus',
                'value': searchPaymentStatus
            }));
        }
        
        // Append form to body and submit
        $('body').append(form);
        form.submit();
        form.remove();
        
        // Reset button state after a short delay
        setTimeout(() => {
            btn.prop('disabled', false).html(originalHtml);
        }, 2000);
    });
    
    // Modal handling
    let editMode = false;
    let editingId = null;
    let originalModalValues = null;
    
    // Reset modal on close
    $('#addPayrollModal').on('hidden.bs.modal', function () {
        editMode = false;
        editingId = null;
        originalModalValues = null;
        $('#addPayrollForm')[0].reset();
        $('#payrollId').val('');
        $('#employeeSelect').val('');
        $('#modalTitle').text('Add New Payroll Entry');
        $('#modalBtnText').text('Add Payroll Entry');
        $('#modalAction').text('"Add Payroll Entry"');
    });
    
    // Edit button handler
    $(document).on('click', '.edit-payroll-btn', function() {
        const row = $(this).closest('tr');
        // Check if row is disabled
        if (row.hasClass('row-disabled')) {
            alert('This payroll entry is disabled. Please enable it first to make changes.');
            return;
        }
        
        editMode = true;
        const button = $(this);
        editingId = button.data('id');
        const employeeId = button.data('employee-id');
        
        // Get data from the row
        const employeeName = row.find('.name').text().trim();
        const department = row.find('.dept').text().trim();
        const emailAddress = row.find('.email_address').text().trim();
        const month = row.find('.month').text().trim();
        
        // Find the employee in the dropdown based on name and email
        const employeeSelect = $('#employeeSelect');
        let selectedEmployeeId = employeeId;
        
        employeeSelect.find('option').each(function() {
            const optionText = $(this).text();
            const optionName = $(this).data('name');
            const optionEmail = $(this).data('email');
            
            if (optionName === employeeName && optionEmail === emailAddress) {
                selectedEmployeeId = $(this).val();
                return false; // Break the loop
            }
        });
        
        // Get currently selected month from dropdown as fallback
        const currentSelectedMonth = $('#monthSelect').val() || 'August 2025';
        
        // Populate form with existing data
        $('#payrollId').val(editingId);
        $('#employeeSelect').val(selectedEmployeeId);
        $('#employeeName').val(employeeName);
        $('#dept').val(department);
        $('#emailAddress').val(emailAddress);
        $('#month').val(month || currentSelectedMonth); // Use current month as fallback, not August
        
        // Parse numeric values safely - handle both 0 and empty cases
        const salPreviousText = row.find('.sal_previous').text().replace(/,/g, '').trim();
        const incrementText = row.find('.increment').text().replace(/,/g, '').trim();
        const salaryCurrentText = row.find('.salary_current').text().replace(/,/g, '').trim();
        const productiveHrsText = row.find('.productive_hrs').text().trim();
        const incentiveText = row.find('.incentive').text().replace(/,/g, '').trim();
        const payableText = row.find('.payable').text().replace(/,/g, '').trim();
        const advanceText = row.find('.advance').text().replace(/,/g, '').trim();
        const totalPayableText = row.find('.total_payable').text().replace(/,/g, '').trim();
        
        const salPrevious = salPreviousText && salPreviousText !== '0' ? parseInt(salPreviousText) : '';
        const increment = incrementText && incrementText !== '0' ? parseInt(incrementText) : '';
        const salaryCurrent = salaryCurrentText && salaryCurrentText !== '0' ? parseInt(salaryCurrentText) : '';
        const productiveHrs = productiveHrsText && productiveHrsText !== '0' ? parseInt(productiveHrsText) : '';
        const incentive = incentiveText && incentiveText !== '0' ? parseInt(incentiveText) : '';
        const payable = payableText && payableText !== '0' ? parseInt(payableText) : '';
        const advance = advanceText && advanceText !== '0' ? parseInt(advanceText) : '';
        const totalPayable = totalPayableText && totalPayableText !== '0' ? parseInt(totalPayableText) : '';
        
        // Get approved hours from the table - ONLY if it's a stored value, not TeamLogger data
        // Check if this is from database (manually set) or from TeamLogger (auto-populated)
        const approvedHrsInput = row.find('.approved_hrs input');
        const approvedHrsText = approvedHrsInput.length > 0 ? approvedHrsInput.val() : row.find('.approved_hrs').text().trim();
        
        // Only populate approved hours if it's different from the TeamLogger productive hours
        // This prevents auto-populating with TeamLogger data that creates false changes
        const approvedHrs = (approvedHrsText && approvedHrsText !== '0' && approvedHrsText !== productiveHrs.toString()) ? parseInt(approvedHrsText) : '';
        
        // Get bank1, bank2, and up values from the table
        const bank1Text = row.find('.bank1-input').val() || row.find('.bank1').text().trim();
        const bank2Text = row.find('.bank2-input').val() || row.find('.bank2').text().trim();
        const upText = row.find('.up-input').val() || row.find('.up').text().trim();
        
        // Get extra value from the table
        const extraText = row.find('.extra-input').val() || row.find('.extra').text().replace(/,/g, '').trim();
        const extra = extraText && extraText !== '0' ? parseInt(extraText) : '';
        
        $('#salPrevious').val(salPrevious);
        $('#increment').val(increment);
        $('#salaryCurrent').val(salaryCurrent);
        $('#productiveHrs').val(productiveHrs);
        // Only set approved hours if it's a real user-set value, not TeamLogger auto-population
        $('#approvedHrs').val(approvedHrs || '');
        $('#incentive').val(incentive);
        $('#payable').val(payable);
        $('#advance').val(advance);
        $('#extra').val(extra);
        $('#totalPayable').val(totalPayable);
        $('#bank1').val(bank1Text);
        $('#bank2').val(bank2Text);
        $('#up').val(upText);
        
        // Set payment status
        const paymentDone = row.find('.payment_done').text().trim().includes('✓') ? '1' : '0';
        $('#paymentDone').val(paymentDone);
        
        // Set approval status from table
        const currentApprovalStatus = row.find('.approval-status-dropdown').val() || 'pending';
        $('#approvalStatus').val(currentApprovalStatus);
        
        // Store original values to track changes
        originalModalValues = {
            sal_previous: $('#salPrevious').val(),
            increment: $('#increment').val(),
            salary_current: $('#salaryCurrent').val(),
            productive_hrs: $('#productiveHrs').val(),
            approved_hrs: $('#approvedHrs').val(),
            incentive: $('#incentive').val(),
            payable: $('#payable').val(),
            advance: $('#advance').val(),
            extra: $('#extra').val(),
            total_payable: $('#totalPayable').val(),
            bank1: $('#bank1').val(),
            bank2: $('#bank2').val(),
            up: $('#up').val(),
            payment_done: $('#paymentDone').val(),
            approval_status: $('#approvalStatus').val()
        };
        
        console.log('Original modal values stored:', originalModalValues);
        
        $('#modalTitle').text('Edit Payroll Entry');
        $('#modalBtnText').text('Update Payroll Entry');
        $('#modalAction').text('"Update Payroll Entry"');
        $('#addPayrollModal').modal('show');
    });
    
    // Add payroll button handler (for employees without payroll records)
    $(document).on('click', '.add-payroll-btn', function() {
        editMode = false;
        editingId = null;
        const button = $(this);
        
        // Pre-fill employee data
        $('#employeeSelect').val(button.data('employee-id'));
        $('#employeeName').val(button.data('name'));
        $('#dept').val(button.data('department'));
        $('#emailAddress').val(button.data('email'));
        
        // Get salary data from the table row or data attributes
        const salPrevious = button.data('sal-previous') || 0;
        const salaryCurrent = button.data('salary-current') || salPrevious;
        
        // Pre-fill salary data 
        $('#salPrevious').val(salPrevious);
        $('#salaryCurrent').val(salaryCurrent);
        
        $('#modalTitle').text('Add New Payroll Entry');
        $('#modalBtnText').text('Add Payroll Entry');
        $('#modalAction').text('"Add Payroll Entry"');
        
        $('#addPayrollModal').modal('show');
    });
    
    // Set correct month when modal is shown
    $('#addPayrollModal').on('show.bs.modal', function() {
        const currentSelectedMonth = $('#monthSelect').val() || 'August 2025';
        $('#month').val(currentSelectedMonth);
    });
    
    // Mark as Done button handler
    $(document).on('click', '.mark-done-btn', function() {
        const button = $(this);
        const row = button.closest('tr');
        
        // Check if row is disabled
        if (row.hasClass('row-disabled')) {
            alert('This payroll entry is disabled. Please enable it first to mark payment as done.');
            return;
        }
        
        const id = button.data('id');
        const employeeId = button.data('employee-id');
        const employeeName = row.find('.name').text();
        
        // Check if this employee has a payroll record
        const hasPayrollData = row.find('.sal_previous').text().trim() !== '0' || 
                               row.find('.increment').text().trim() !== '0' || 
                               row.find('.salary_current').text().trim() !== '0';
        
        if (!hasPayrollData) {
            alert('Please create a payroll entry for this employee first before marking payment as done.');
            return;
        }
        
        if(confirm('Mark this payment as done? This will also automatically send the salary slip via email.')) {
            // Show loading state
            button.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i>');
            
            // Use the actual payroll ID, not employee ID
            const payrollId = id;
            
            $.ajax({
                url: `{{ route('payroll.mark-done', ':id') }}`.replace(':id', payrollId),
                method: 'POST',
                success: function(response) {
                    if (response.success) {
                        // Update the payment status in the table
                        row.find('.payment_done').html('<span class="badge bg-success">✓</span>');
                        
                        // Hide the "Mark as Done" button and show "Generate PDF" and "Send Email" buttons
                        button.hide();
                        const pdfButton = `<button type="button" class="btn btn-info btn-sm generate-pdf-btn" data-id="${payrollId}" data-employee-id="${employeeId}" title="Generate PDF">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                          </button>`;
                        const emailButton = `<button type="button" class="btn btn-primary btn-sm send-email-btn" data-id="${payrollId}" data-employee-id="${employeeId}" data-name="${employeeName}" data-email="${row.find('.email_address').text()}" data-month="${row.find('.month').text()}" title="Send Salary Slip by Email">
                                             <i class="bi bi-envelope"></i>
                                           </button>`;
                        button.parent().append(pdfButton + emailButton);
                        
                        // Show success message with email status
                        let message = `Payment for "${employeeName}" has been marked as done.`;
                        let alertClass = 'alert-success';
                        
                        if (response.email_sent) {
                            message += ` Salary slip has been automatically sent to ${row.find('.email_address').text()}.`;
                        } else {
                            message += ` However, the salary slip email could not be sent automatically. You can try sending it manually using the email button.`;
                            alertClass = 'alert-warning';
                        }
                        
                        const alertDiv = $(`<div class="alert ${alertClass} alert-dismissible fade show">` +
                            '<i class="bi bi-check-circle"></i>' +
                            '<strong>Success!</strong> ' + message +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                            '</div>');
                        $('.main-content').prepend(alertDiv);
                        setTimeout(() => alertDiv.remove(), 5000);
                    } else {
                        alert('Error: ' + (response.message || 'Failed to mark payment as done'));
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Error marking payment as done. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                    console.error('Mark as done error:', xhr.responseText);
                },
                complete: function() {
                    // Reset button state
                    button.prop('disabled', false).html('<i class="bi bi-check-circle"></i>');
                }
            });
        }
    });
    
    // Generate PDF button handler
    $(document).on('click', '.generate-pdf-btn', function() {
        const button = $(this);
        const id = button.data('id');
        const employeeId = button.data('employee-id');
        const row = button.closest('tr');
        const employeeName = row.find('.name').text();
        
        // Show loading state
        button.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i>');
        
        // Generate PDF
        window.open(`{{ route('payroll.generate-pdf', ':id') }}`.replace(':id', id), '_blank');
        
        // Reset button after a short delay
        setTimeout(() => {
            button.prop('disabled', false).html('<i class="bi bi-file-earmark-pdf"></i>');
        }, 2000);
    });
    
    // Send Email button handler
    $(document).on('click', '.send-email-btn', function() {
        const button = $(this);
        const id = button.data('id');
        const employeeId = button.data('employee-id');
        const employeeName = button.data('name');
        const employeeEmail = button.data('email');
        const month = button.data('month');
        const row = button.closest('tr');
        
        if(confirm(`Send salary slip via email to "${employeeName}" (${employeeEmail})?`)) {
            // Show loading state
            button.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i>');
            
            $.ajax({
                url: `{{ route('payroll.send-email', ':id') }}`.replace(':id', id),
                method: 'POST',
                data: {
                    employee_name: employeeName,
                    employee_email: employeeEmail,
                    month: month
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        const alertDiv = $('<div class="alert alert-success alert-dismissible fade show">' +
                            '<i class="bi bi-check-circle"></i>' +
                            '<strong>Success!</strong> Salary slip has been sent to "' + employeeName + '" at ' + employeeEmail + '.' +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                            '</div>');
                        $('.main-content').prepend(alertDiv);
                        setTimeout(() => alertDiv.remove(), 5000);
                    } else {
                        alert('Error: ' + (response.message || 'Failed to send email'));
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Error sending email. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                    console.error('Send email error:', xhr.responseText);
                },
                complete: function() {
                    // Reset button
                    button.prop('disabled', false).html('<i class="bi bi-envelope"></i>');
                }
            });
        }
    });
    
    // Delete button handler
    $(document).on('click', '.delete-payroll-btn', function() {
        const button = $(this);
        const row = button.closest('tr');
        
        // Check if row is disabled
        if (row.hasClass('row-disabled')) {
            alert('This payroll entry is disabled. Please enable it first to delete.');
            return;
        }
        
        const id = button.data('id');
        const employeeId = button.data('employee-id');
        const employeeName = row.find('.name').text();
        
        // Check if this employee has a payroll record
        const hasPayrollData = row.find('.sal_previous').text().trim() !== '0' || 
                               row.find('.increment').text().trim() !== '0' || 
                               row.find('.salary_current').text().trim() !== '0';
        
        if (!hasPayrollData) {
            alert('No payroll entry exists for this employee to delete.');
            return;
        }
        
        if(confirm(`Are you sure you want to delete the payroll entry for "${employeeName}"?`)) {
            $.ajax({
                url: `{{ route('payroll.destroy', ':id') }}`.replace(':id', id),
                method: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        // Don't remove the row, just reset the payroll data to show employee without payroll
                        row.find('.sal_previous').text('0');
                        row.find('.increment').text('0');
                        row.find('.salary_current').text('0');
                        row.find('.productive_hrs').text('0');
                        row.find('.incentive').text('0');
                        row.find('.payable').text('0');
                        row.find('.advance').text('0');
                        row.find('.total_payable').text('0');
                        row.find('.payment_done').html('<span class="badge bg-warning">Pending</span>');
                        row.find('.month').text('August 2025');
                        
                        // Update row data-id to employee_id and disable buttons
                        row.attr('data-id', employeeId);
                        row.find('.delete-payroll-btn, .mark-done-btn').prop('disabled', true).addClass('btn-secondary').removeClass('btn-danger btn-success');
                        
                        // Show delete success message
                        const alertDiv = $('<div class="alert alert-warning alert-dismissible fade show">' +
                            '<i class="bi bi-exclamation-triangle"></i>' +
                            '<strong>Deleted!</strong> Payroll entry for "' + employeeName + '" has been removed successfully.' +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                            '</div>');
                        $('.main-content').prepend(alertDiv);
                        setTimeout(() => alertDiv.remove(), 3000);
                    } else {
                        alert('Error: ' + (response.message || 'Failed to delete payroll entry'));
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Error deleting payroll entry. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                    console.error('Delete error:', xhr.responseText);
                }
            });
        }
    });
    
    // Enable Payroll button handler
    $(document).on('click', '.enable-payroll-btn', function() {
        const button = $(this);
        const id = button.data('id');
        const employeeId = button.data('employee-id');
        const row = button.closest('tr');
        const employeeName = row.find('.name').text();
        
        if(confirm(`Are you sure you want to enable payroll entry for "${employeeName}"?`)) {
            $.ajax({
                url: `{{ route('payroll.enable', ':id') }}`.replace(':id', id),
                method: 'POST',
                success: function(response) {
                    if (response.success) {
                        // Remove disabled styling
                        row.removeClass('row-disabled');
                        
                        // Replace Enable button with Disable button
                        button.removeClass('btn-outline-success enable-payroll-btn')
                              .addClass('btn-outline-danger disable-payroll-btn')
                              .attr('title', 'Disable')
                              .html('<i class="bi bi-x-circle-fill"></i>');
                        
                        // Show success message
                        const alertDiv = $('<div class="alert alert-success alert-dismissible fade show">' +
                            '<i class="bi bi-check-circle"></i>' +
                            '<strong>Success!</strong> Payroll entry for "' + employeeName + '" has been enabled.' +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                            '</div>');
                        $('.main-content').prepend(alertDiv);
                        setTimeout(() => alertDiv.remove(), 3000);
                    } else {
                        alert('Error: ' + (response.message || 'Failed to enable payroll entry'));
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Error enabling payroll entry. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                    console.error('Enable error:', xhr.responseText);
                }
            });
        }
    });
    
    // Disable Payroll button handler
   // Disable Payroll button handler
$(document).on('click', '.disable-payroll-btn', function() {
    const button = $(this);
    const id = button.data('id');
    const employeeId = button.data('employee-id');
    const row = button.closest('tr');
    const employeeName = row.find('.name').text();
    
    if(confirm(`Are you sure you want to disable payroll entry for "${employeeName}"? This will move the employee to the Archive Employee section.`)) {
        $.ajax({
            url: `{{ route('payroll.disable', ':id') }}`.replace(':id', id),
            method: 'POST',
            success: function(response) {
                if (response.success) {
                    // Show success message
                    const alertDiv = $('<div class="alert alert-warning alert-dismissible fade show">' +
                        '<i class="bi bi-archive"></i>' +
                        '<strong>Archived!</strong> Payroll entry for "' + employeeName + '" has been moved to archive. ' +
                        '<a href="{{ route('payroll.archive') }}" class="alert-link">View Archive</a>' +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                        '</div>');
                    $('.main-content').prepend(alertDiv);
                    
                    // Remove the row from current table with animation
                    row.fadeOut(300, function() {
                        $(this).remove();
                    });
                    
                    setTimeout(() => alertDiv.remove(), 5000);
                } else {
                    alert('Error: ' + (response.message || 'Failed to disable payroll entry'));
                }
            },
            error: function(xhr) {
                let errorMessage = 'Error disabling payroll entry. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert(errorMessage);
                console.error('Disable error:', xhr.responseText);
            }
        });
    }
});
    
    // Archive as Contractual button handler
    $(document).on('click', '.archive-contractual-btn', function() {
        const button = $(this);
        const id = button.data('id');
        const employeeId = button.data('employee-id');
        const row = button.closest('tr');
        const employeeName = row.find('.name').text();
        
        if(confirm(`Are you sure you want to move "${employeeName}" to contractual status? This will move the employee to the Contractual section.`)) {
            $.ajax({
                url: `{{ route('payroll.archive-contractual', ':id') }}`.replace(':id', id),
                method: 'POST',
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        const alertDiv = $('<div class="alert alert-info alert-dismissible fade show">' +
                            '<i class="bi bi-file-earmark-person"></i>' +
                            '<strong>Moved to Contractual!</strong> "' + employeeName + '" has been moved to contractual status. ' +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                            '</div>');
                        $('.main-content').prepend(alertDiv);
                        
                        // Remove the row from current table with animation
                        row.fadeOut(300, function() {
                            $(this).remove();
                        });
                        
                        // Reset contractual data loaded flag to refresh when tab is clicked
                        contractualDataLoaded = false;
                        
                        setTimeout(() => alertDiv.remove(), 5000);
                    } else {
                        alert('Error: ' + (response.message || 'Failed to move employee to contractual'));
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Error moving employee to contractual. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    alert(errorMessage);
                    console.error('Archive contractual error:', xhr.responseText);
                }
            });
        }
    });
    
    // Save button handler
    $('#savePayrollBtn').on('click', function() {
        const form = $('#addPayrollForm');
        let formData = new FormData(form[0]);
        
        // Add employee_id to form data
        formData.append('employee_id', $('#employeeSelect').val());
        
        // For edit mode, include all required fields plus any changed fields
        if (editMode && originalModalValues) {
            // Create new FormData with all required fields
            const newFormData = new FormData();
            
            // Always include these essential/required fields for validation
            newFormData.append('employee_id', $('#employeeSelect').val());
            newFormData.append('name', $('#employeeName').val());
            newFormData.append('department', $('#dept').val());
            newFormData.append('email_address', $('#emailAddress').val());
            newFormData.append('month', $('#month').val());
            
            // Check each field for changes and include them
            const currentValues = {
                sal_previous: $('#salPrevious').val(),
                increment: $('#increment').val(),
                salary_current: $('#salaryCurrent').val(),
                productive_hrs: $('#productiveHrs').val(),
                approved_hrs: $('#approvedHrs').val(),
                incentive: $('#incentive').val(),
                payable: $('#payable').val(),
                advance: $('#advance').val(),
                extra: $('#extra').val(),
                total_payable: $('#totalPayable').val(),
                bank1: $('#bank1').val(),
                bank2: $('#bank2').val(),
                up: $('#up').val(),
                payment_done: $('#paymentDone').val(),
                approval_status: $('#approvalStatus').val()
            };
            
            // Include all fields (changed or not) to avoid validation issues
            for (const [key, value] of Object.entries(currentValues)) {
                newFormData.append(key, value || '');
                if (value !== originalModalValues[key]) {
                    console.log(`Field ${key} changed from "${originalModalValues[key]}" to "${value}"`);
                }
            }
            
            formData = newFormData;
        }
        
        let url, method;
        
        if (editMode) {
            // Check if we have a real payroll ID or if we're editing an employee without payroll record
            const payrollIdValue = $('#payrollId').val();
            const employeeIdValue = $('#employeeSelect').val();
            
            // If payrollId equals employeeId, it means this employee doesn't have a payroll record yet
            if (payrollIdValue && payrollIdValue !== employeeIdValue && payrollIdValue !== 'new') {
                // Update existing payroll record
                url = `{{ route('payroll.update', '') }}/${payrollIdValue}`;
                method = 'POST';
                formData.append('_method', 'PUT');
            } else {
                // Create new payroll record for this employee
                url = '{{ route('payroll.store') }}';
                method = 'POST';
                
                // Add flag to copy bank details from previous month
                formData.append('copy_bank_details_from_previous_month', '1');
                
                editMode = false; // Treat as new record
            }
        } else {
            // New record
            url = '{{ route('payroll.store') }}';
            method = 'POST';
        }

        
        $.ajax({
            url: url,
            method: method,
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Show success message
                    const action = editMode ? 'updated' : 'added';
                    const employeeName = $('#employeeName').val();
                    
                    const alertDiv = $('<div class="alert alert-success alert-dismissible fade show">' +
                        '<i class="bi bi-check-circle"></i>' +
                        '<strong>Success!</strong> Payroll entry for "' + employeeName + '" has been ' + action + ' successfully.' +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                        '</div>');
                    $('.main-content').prepend(alertDiv);
                    setTimeout(() => alertDiv.remove(), 3000);
                    
                    $('#addPayrollModal').modal('hide');
                    
                    // Always reload the page to ensure fresh data
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    alert('Error: ' + (response.message || 'Unknown error occurred'));
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while saving the payroll entry.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMessage = errors.join('\n');
                }
                alert('Error: ' + errorMessage);
            }
        });
    });
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const selects = document.querySelectorAll(".approval-status-dropdown");

    selects.forEach(select => {
        function updateColor(sel) {
            if (sel.value === "approved") {
                sel.style.backgroundColor = "green";
                sel.style.color = "white";
            } else {
                sel.style.backgroundColor = "red";
                sel.style.color = "white";
            }
        }

        // Initial color
        updateColor(select);

        // Change on selection
        select.addEventListener("change", function () {
            updateColor(this);
        });
    });
});
</script>
<script>
  // Initialize tooltips
  document.addEventListener("DOMContentLoaded", function () {
      var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
      var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl)
      })
      
      // Make Bank1, Bank2, and UP cells editable
      $('#payrollTable tbody tr').each(function() {
          const row = $(this);
          const payrollId = row.data('id');
          const employeeId = $(this).find('button').first().data('employee-id');
          
          if (payrollId && employeeId) {
              // Convert Bank1 cell to editable
              const bank1Cell = row.find('.bank1');
              const bank1Value = bank1Cell.text().trim();
              bank1Cell.html(`<textarea class="form-control form-control-sm bank1-input" data-payroll-id="${payrollId}" data-employee-id="${employeeId}" rows="2" style="width: 140px; font-size: 12px;">${bank1Value}</textarea>`);
              
              // Convert Bank2 cell to editable
              const bank2Cell = row.find('.bank2');
              const bank2Value = bank2Cell.text().trim();
              bank2Cell.html(`<textarea class="form-control form-control-sm bank2-input" data-payroll-id="${payrollId}" data-employee-id="${employeeId}" rows="2" style="width: 140px; font-size: 12px;">${bank2Value}</textarea>`);
              
              // Convert UP cell to editable
              const upCell = row.find('.up');
              const upValue = upCell.text().trim();
              upCell.html(`<input type="text" class="form-control form-control-sm up-input" data-payroll-id="${payrollId}" data-employee-id="${employeeId}" value="${upValue}" style="width: 140px; font-size: 12px;">`);
          }
      });
  });
  
  // Update Bank1 data when changed
  $(document).on('change blur', '.bank1-input', function() {
      const input = $(this);
      const payrollId = input.data('payroll-id');
      const employeeId = input.data('employee-id');
      const bank1Value = input.val() || '';
      
      // Show loading state
      input.prop('disabled', true);
      
      // Save bank1 data via AJAX
      $.ajax({
          url: '{{ route("payroll.update-bank1") }}',
          method: 'POST',
          data: {
              payroll_id: payrollId,
              employee_id: employeeId,
              bank1: bank1Value
          },
          success: function(response) {
              if (response.success) {
                  // Show brief success feedback
                  input.css('border-color', '#28a745');
                  setTimeout(() => {
                      input.css('border-color', '');
                  }, 1500);
              } else {
                  alert('Error: ' + (response.message || 'Failed to update Bank 1 details'));
              }
          },
          error: function(xhr) {
              let errorMessage = 'Error updating Bank 1 details. Please try again.';
              if (xhr.responseJSON && xhr.responseJSON.message) {
                  errorMessage = xhr.responseJSON.message;
              }
              alert(errorMessage);
          },
          complete: function() {
              input.prop('disabled', false);
          }
      });
  });
  
  // Update Bank2 data when changed
  $(document).on('change blur', '.bank2-input', function() {
      const input = $(this);
      const payrollId = input.data('payroll-id');
      const employeeId = input.data('employee-id');
      const bank2Value = input.val() || '';
      
      // Show loading state
      input.prop('disabled', true);
      
      // Save bank2 data via AJAX
      $.ajax({
          url: '{{ route("payroll.update-bank2") }}',
          method: 'POST',
          data: {
              payroll_id: payrollId,
              employee_id: employeeId,
              bank2: bank2Value
          },
          success: function(response) {
              if (response.success) {
                  // Show brief success feedback
                  input.css('border-color', '#28a745');
                  setTimeout(() => {
                      input.css('border-color', '');
                  }, 1500);
              } else {
                  alert('Error: ' + (response.message || 'Failed to update Bank 2 details'));
              }
          },
          error: function(xhr) {
              let errorMessage = 'Error updating Bank 2 details. Please try again.';
              if (xhr.responseJSON && xhr.responseJSON.message) {
                  errorMessage = xhr.responseJSON.message;
              }
              alert(errorMessage);
          },
          complete: function() {
              input.prop('disabled', false);
          }
      });
  });
  
  // Update UP data when changed
  $(document).on('change blur', '.up-input', function() {
      const input = $(this);
      const payrollId = input.data('payroll-id');
      const employeeId = input.data('employee-id');
      const upValue = input.val() || '';
      
      // Show loading state
      input.prop('disabled', true);
      
      // Save UP data via AJAX
      $.ajax({
          url: '{{ route("payroll.update-up") }}',
          method: 'POST',
          data: {
              payroll_id: payrollId,
              employee_id: employeeId,
              up: upValue
          },
          success: function(response) {
              if (response.success) {
                  // Show brief success feedback
                  input.css('border-color', '#28a745');
                  setTimeout(() => {
                      input.css('border-color', '');
                  }, 1500);
              } else {
                  alert('Error: ' + (response.message || 'Failed to update UPI details'));
              }
          },
          error: function(xhr) {
              let errorMessage = 'Error updating UPI details. Please try again.';
              if (xhr.responseJSON && xhr.responseJSON.message) {
                  errorMessage = xhr.responseJSON.message;
              }
              alert(errorMessage);
          },
          complete: function() {
              input.prop('disabled', false);
          }
      });
  });
  
  // Update Number of Blogs/Videos for contractual table
  $(document).on('change blur', '.blogs-videos-input', function() {
      const input = $(this);
      const row = input.closest('tr');
      const payrollId = input.data('payroll-id');
      const blogsVideos = parseFloat(input.val()) || 0;
      const rate = parseFloat(row.find('.rate-input').val()) || 0;
      const advance = parseFloat(row.find('.advance-input').val()) || 0;
      
      // Calculate payable immediately: Number of Videos * Rate
      const payable = blogsVideos * rate;
      const totalPayable = payable - advance;
      
      // Update display immediately
      row.find('td').eq(7).text('₹' + Math.round(payable).toLocaleString('en-IN'));
      row.find('td').eq(8).text('₹' + Math.round(totalPayable).toLocaleString('en-IN'));
      
      // Show loading state
      input.prop('disabled', true);
      
      // Save blogs/videos data via AJAX
      $.ajax({
          url: '{{ route("payroll.update-blogs-videos") }}',
          method: 'POST',
          data: {
              payroll_id: payrollId,
              number_of_blogs_videos: blogsVideos
          },
          success: function(response) {
              if (response.success) {
                  // Show brief success feedback
                  input.css('border-color', '#28a745');
                  setTimeout(() => {
                      input.css('border-color', '');
                  }, 1500);
                  
                  // Update display with server values
                  if (response.payable !== undefined && response.total_payable !== undefined) {
                      row.find('td').eq(7).text('₹' + Math.round(response.payable).toLocaleString('en-IN'));
                      row.find('td').eq(8).text('₹' + Math.round(response.total_payable).toLocaleString('en-IN'));
                  }
              } else {
                  alert('Error: ' + (response.message || 'Failed to update number of blogs/videos'));
                  // Reset to previous value
                  input.val(input.data('previous-value') || 0);
              }
          },
          error: function(xhr) {
              let errorMessage = 'Error updating number of blogs/videos. Please try again.';
              if (xhr.responseJSON && xhr.responseJSON.message) {
                  errorMessage = xhr.responseJSON.message;
              }
              alert(errorMessage);
              // Reset to previous value
              input.val(input.data('previous-value') || 0);
          },
          complete: function() {
              input.prop('disabled', false);
          }
      });
  });
  
  // Update Rate for contractual table
  $(document).on('change blur', '.rate-input', function() {
      const input = $(this);
      const row = input.closest('tr');
      const payrollId = input.data('payroll-id');
      const rate = parseFloat(input.val()) || 0;
      const blogsVideos = parseFloat(row.find('.blogs-videos-input').val()) || 0;
      const advance = parseFloat(row.find('.advance-input').val()) || 0;
      
      // Calculate payable immediately: Number of Videos * Rate
      const payable = blogsVideos * rate;
      const totalPayable = payable - advance;
      
      // Update display immediately
      row.find('td').eq(7).text('₹' + Math.round(payable).toLocaleString('en-IN'));
      row.find('td').eq(8).text('₹' + Math.round(totalPayable).toLocaleString('en-IN'));
      
      // Show loading state
      input.prop('disabled', true);
      
      // Save rate data via AJAX
      $.ajax({
          url: '{{ route("payroll.update-rate") }}',
          method: 'POST',
          data: {
              payroll_id: payrollId,
              rate: rate
          },
          success: function(response) {
              if (response.success) {
                  // Show brief success feedback
                  input.css('border-color', '#28a745');
                  setTimeout(() => {
                      input.css('border-color', '');
                  }, 1500);
                  
                  // Update display with server values
                  if (response.payable !== undefined && response.total_payable !== undefined) {
                      row.find('td').eq(7).text('₹' + Math.round(response.payable).toLocaleString('en-IN'));
                      row.find('td').eq(8).text('₹' + Math.round(response.total_payable).toLocaleString('en-IN'));
                  }
              } else {
                  alert('Error: ' + (response.message || 'Failed to update rate'));
                  // Reset to previous value
                  input.val(input.data('previous-value') || 0);
              }
          },
          error: function(xhr) {
              let errorMessage = 'Error updating rate. Please try again.';
              if (xhr.responseJSON && xhr.responseJSON.message) {
                  errorMessage = xhr.responseJSON.message;
              }
              alert(errorMessage);
              // Reset to previous value
              input.val(input.data('previous-value') || 0);
          },
          complete: function() {
              input.prop('disabled', false);
          }
      });
  });
  
  // Update Advance for contractual table
  $(document).on('change blur', '.advance-input', function() {
      const input = $(this);
      const row = input.closest('tr');
      const payrollId = input.data('payroll-id');
      const advance = parseFloat(input.val()) || 0;
      const blogsVideos = parseFloat(row.find('.blogs-videos-input').val()) || 0;
      const rate = parseFloat(row.find('.rate-input').val()) || 0;
      
      // Calculate payable: Number of Videos * Rate
      const payable = blogsVideos * rate;
      // Calculate total payable: Payable - Advance
      const totalPayable = payable - advance;
      
      // Update display immediately
      row.find('td').eq(8).text('₹' + Math.round(totalPayable).toLocaleString('en-IN'));
      
      // Show loading state
      input.prop('disabled', true);
      
      // Save advance data via AJAX
      $.ajax({
          url: '{{ route("payroll.update-advance") }}',
          method: 'POST',
          data: {
              payroll_id: payrollId,
              advance: advance
          },
          success: function(response) {
              if (response.success) {
                  // Show brief success feedback
                  input.css('border-color', '#28a745');
                  setTimeout(() => {
                      input.css('border-color', '');
                  }, 1500);
                  
                  // Update total payable if returned
                  if (response.total_payable !== undefined) {
                      row.find('td').eq(8).text('₹' + Math.round(response.total_payable).toLocaleString('en-IN'));
                  }
              } else {
                  alert('Error: ' + (response.message || 'Failed to update advance'));
                  // Reset to previous value
                  input.val(input.data('previous-value') || 0);
              }
          },
          error: function(xhr) {
              let errorMessage = 'Error updating advance. Please try again.';
              if (xhr.responseJSON && xhr.responseJSON.message) {
                  errorMessage = xhr.responseJSON.message;
              }
              alert(errorMessage);
              // Reset to previous value
              input.val(input.data('previous-value') || 0);
          },
          complete: function() {
              input.prop('disabled', false);
          }
      });
  });
</script>
@endpush
@endsection
