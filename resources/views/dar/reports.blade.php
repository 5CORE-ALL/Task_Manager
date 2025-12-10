@extends('layouts.main')

@section('page-title')
    {{ __('DAR Reports') }}
@endsection

@section('page-breadcrumb')
    {{ __('DAR Reports') }}
@endsection


@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header card-body table-border-style">
                <div class="table-responsive overflow_hidden">


                    <!-- DAR Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-success mb-3">
                                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                    <span>Employees Filled DAR</span>
                                    <input type="date" class="form-control form-control-sm w-auto" id="summary_date" value="{{ date('Y-m-d') }}" style="min-width:120px;">
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title"><span id="filledCount">{{ isset($filledEmployees) ? count($filledEmployees) : 0 }}</span> Employees</h5>
                                    <div style="max-height:200px;overflow-y:auto;border:1px solid #e0e0e0;border-radius:5px;padding:5px;background:#f8fff8;">
                    <ul class="mb-0" id="filledList">
                        @if(isset($filledEmployees) && count($filledEmployees) > 0)
                            @foreach($filledEmployees as $emp)
                                <li>{{ $emp->name }} <span style="color:green;font-size:1.2em;">&#10004;</span></li>
                            @endforeach
                        @else
                            <li>No employees filled DAR.</li>
                        @endif
                    </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-danger mb-3">
                                <div class="card-header bg-danger text-white">Employees Missed DAR</div>
                                <div class="card-body">
                                    <h5 class="card-title"><span id="missedCount">{{ isset($missedEmployees) ? count($missedEmployees) : 0 }}</span> Employees</h5>
                                    <div style="max-height:200px;overflow-y:auto;border:1px solid #e0e0e0;border-radius:5px;padding:5px;background:#fff8f8;">
                                        <ul class="mb-0" id="missedList">
                                            @if(isset($missedEmployees) && count($missedEmployees) > 0)
                                                @foreach($missedEmployees as $emp)
                                                    <li>{{ $emp->name }}</li>
                                                @endforeach
                                            @else
                                                <li>All employees filled DAR.</li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="start_date" class="form-label">{{ __('Start Date') }}</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ date('Y-m-d') }}" autocomplete="off">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label">{{ __('End Date (Optional)') }}</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" autocomplete="off">
                        </div>
                        <div class="col-md-3">
                            <label for="employee_id" class="form-label">{{ __('Select Employee') }}</label>
                            <select class="form-control" id="employee_id" name="employee_id">
                                <option value="">{{ __('Choose Employee') }}</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-primary" id="fetchReport">
                                <i class="ti ti-search"></i> {{ __('Get Report') }}
                            </button>
                        </div>
                    </div>
                    <!-- Report Data Section -->
                    <div id="reportSection">
                        <div class="card">
                            <div class="card-header">
                                <h5 id="reportTitle">{{ __('Daily Activity Report') }}</h5>
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <strong>{{ __('Employee') }}:</strong> <span id="employeeName" class="text-primary fw-bold">-</span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong id="dateLabel">{{ __('Report Date') }}:</strong> <span id="reportDate" class="text-info fw-bold">-</span>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <strong>{{ __('Total Time') }}:</strong> <span id="totalTime" class="badge bg-primary fs-6">-</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="tasksTable">
                                        <thead>
                                            <tr>
                                                <th>{{ __('#') }}</th>
                                                <th>{{ __('Group') }}</th>
                                                <th>{{ __('Task Description') }}</th>
                                                <th>{{ __('Time Spent (Minutes)') }}</th>
                                                <th>{{ __('Task Status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($allReports) && count($allReports) > 0)
                                                @foreach($allReports as $report)
                                                    @foreach($report->tasks as $index => $task)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>{{ $task->group_name }}</td>
                                                            <td>{{ $task->description }}</td>
                                                            <td>{{ $task->time_spent }}</td>
                                                            <td><span class="badge {{ $task->status == 'Complete' ? 'bg-success' : ($task->status == 'Pending' ? 'bg-warning' : ($task->status == 'In Progress' ? 'bg-info' : 'bg-secondary')) }}">{{ $task->status }}</span></td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="4" class="text-center">No report data found.</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- No Data Message -->
                    <div id="noDataMessage" style="display: none;">
                        <div class="alert alert-info">
                            <i class="ti ti-info-circle"></i>
                            {{ __('No DAR found for the selected date and employee.') }}
                        </div>
                    </div>

                    <!-- Loading Message -->
                    <div id="loadingMessage" style="display: none;">
                        <div class="alert alert-warning">
                            <i class="ti ti-loader"></i>
                            {{ __('Loading report data...') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    #employeeName, #reportDate, #totalTime {
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
        color: inherit !important;
    }
    
    #totalTime {
        min-width: 60px;
        text-align: center;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Store date values when they change
    let storedStartDate = '';
    let storedEndDate = '';
    
    // Capture date values when they change
    $('#start_date').on('change input', function() {
        storedStartDate = this.value;
        console.log('Start date stored:', storedStartDate);
    });
    
    $('#end_date').on('change input', function() {
        storedEndDate = this.value;
        console.log('End date stored:', storedEndDate);
    });
    
    // Summary date change event
    $('#summary_date').on('change', function() {
        fetchSummaryData();
    });

    // Also fetch summary on page load for current date
    fetchSummaryData();

    // Add date validation
    $('#start_date, #end_date').on('change', function() {
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();
        
        if (startDate && endDate && new Date(endDate) < new Date(startDate)) {
            alert('End date cannot be before start date.');
            $('#end_date').val('');
            storedEndDate = '';
        }
    });

    function fetchSummaryData() {
        const summaryDate = $('#summary_date').val();
        $.ajax({
            url: '{{ route("dar.reports.summary") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                report_date: summaryDate
            },
            success: function(response) {
                // Filled
                $('#filledCount').text(response.filled.length);
                let filledHtml = '';
                if (response.filled.length > 0) {
                    response.filled.forEach(function(emp) {
                        filledHtml += `<li>${emp.name} <span style="color:#1877F2;font-size:1.2em;vertical-align:middle;">&#x2714;&#xFE0F;</span></li>`;
                    });
                } else {
                    filledHtml = '<li>No employees filled DAR.</li>';
                }
                $('#filledList').html(filledHtml);
                // Missed
                $('#missedCount').text(response.missed.length);
                let missedHtml = '';
                if (response.missed.length > 0) {
                    response.missed.forEach(function(emp) {
                        missedHtml += `<li>${emp.name}</li>`;
                    });
                } else {
                    missedHtml = '<li>All employees filled DAR.</li>';
                }
                $('#missedList').html(missedHtml);
            }
        });
    }

    $('#fetchReport').on('click', function() {
        // Force trigger change events to capture current values
        $('#start_date').trigger('change');
        $('#end_date').trigger('change');
        
        // Small delay to ensure change events are processed
        setTimeout(function() {
            // Get values using multiple methods to ensure we capture them
            const startDate = document.getElementById('start_date').value || $('#start_date').val() || storedStartDate || '';
            const endDate = document.getElementById('end_date').value || $('#end_date').val() || storedEndDate || '';
            const employeeId = $('#employee_id').val();

            console.log('=== DIRECT ELEMENT ACCESS ===');
            console.log('Start Date Element Value:', document.getElementById('start_date').value);
            console.log('End Date Element Value:', document.getElementById('end_date').value);
            console.log('Start Date jQuery Val:', $('#start_date').val());
            console.log('End Date jQuery Val:', $('#end_date').val());
            console.log('Stored Start Date:', storedStartDate);
            console.log('Stored End Date:', storedEndDate);
            console.log('Final Start Date:', startDate);
            console.log('Final End Date:', endDate);

            // Basic validation
            if (!startDate || !employeeId) {
                alert('Please select both start date and employee.');
                return;
            }

            // Show loading message
            $('#loadingMessage').show();
            $('#noDataMessage').hide();

            // Prepare request data
            let requestData = {
                _token: '{{ csrf_token() }}',
                start_date: startDate,
                employee_id: employeeId,
                end_date: endDate || ''  // Always send end_date, even if empty
            };

        console.log('Request Data:', requestData);
        console.log('Start Date:', startDate);
        console.log('End Date:', endDate);
        console.log('Are dates different?', startDate !== endDate);
        console.log('End date has value?', endDate && endDate.trim() !== '');

        $.ajax({
            url: '{{ route("dar.reports.data") }}',
            method: 'POST',
            data: requestData,
            success: function(response) {
                $('#loadingMessage').hide();
                
                console.log('Response received:', response);
                console.log('Is Range?', response.data ? response.data.is_range : 'undefined');
                console.log('Date Range:', response.data ? response.data.date_range : 'undefined');
                console.log('Number of tasks:', response.data && response.data.tasks ? response.data.tasks.length : 0);
                
                if (response.success) {
                    $('#employeeName').text(response.data.employee_name);
                    $('#reportDate').text(response.data.date_range);
                    $('#totalTime').text(response.data.total_time_formatted);
                    
                    // Update header titles based on date range
                    if (response.data.is_range) {
                        $('#reportTitle').text('Daily Activity Report - Date Range');
                        $('#dateLabel').text('Date Range:');
                    } else {
                        $('#reportTitle').text('Daily Activity Report');
                        $('#dateLabel').text('Report Date:');
                    }
                    
                    // Set table header based on whether it's a date range or single date
                    let tableHeader = '';
                    if (response.data.is_range) {
                        tableHeader = `
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Date</th>
                                    <th>Group Name</th>
                                    <th>Task Description</th>
                                    <th>Time Spent (Min)</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        `;
                    } else {
                        tableHeader = `
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Group Name</th>
                                    <th>Task Description</th>
                                    <th>Time Spent (Min)</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        `;
                    }
                    
                    $('#tasksTable').html(tableHeader);
                    const tbody = $('#tasksTable tbody');
                    tbody.empty();
                    
                    if (response.data.tasks && response.data.tasks.length > 0) {
                        response.data.tasks.forEach(function(task, index) {
                            const statusBadge = getStatusBadge(task.status);
                            let row = '';
                            
                            if (response.data.is_range) {
                                row = `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${task.report_date}</td>
                                        <td>${task.group_name}</td>
                                        <td>${task.description}</td>
                                        <td>${task.time_spent}</td>
                                        <td>${statusBadge}</td>
                                    </tr>
                                `;
                            } else {
                                row = `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${task.group_name}</td>
                                        <td>${task.description}</td>
                                        <td>${task.time_spent}</td>
                                        <td>${statusBadge}</td>
                                    </tr>
                                `;
                            }
                            tbody.append(row);
                        });
                    } else {
                        const colspan = response.data.is_range ? '6' : '5';
                        tbody.append(`
                            <tr>
                                <td colspan="${colspan}" class="text-center">No tasks found for this date range.</td>
                            </tr>
                        `);
                    }
                } else {
                    $('#employeeName').text('-');
                    $('#reportDate').text('-');
                    $('#totalTime').text('-');
                    $('#tasksTable').html(`
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Group Name</th>
                                <th>Task Description</th>
                                <th>Time Spent (Min)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center">No tasks found for this date.</td>
                            </tr>
                        </tbody>
                    `);
                    $('#noDataMessage').show();
                }
            },
            error: function(xhr) {
                $('#loadingMessage').hide();
                let message = 'Error fetching report data.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                alert(message);
            }
        });
    });

    function getStatusBadge(status) {
        let badgeClass = '';
        switch(status) {
            case 'Complete':
                badgeClass = 'bg-success';
                break;
            case 'Pending':
                badgeClass = 'bg-warning';
                break;
            case 'In Progress':
                badgeClass = 'bg-info';
                break;
            default:
                badgeClass = 'bg-secondary';
        }
        return `<span class="badge ${badgeClass}">${status}</span>`;
    }
});
</script>
@endpush
