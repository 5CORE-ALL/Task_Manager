@extends('layouts.main')
@section('page-title')
    {{ __('DAR Report') }}
@endsection
@section('title')
    {{ __('DAR Report') }}
@endsection
@section('page-breadcrumb')
    {{ __('Project') }},{{ __('Project Details') }},{{ __('DAR Report') }}
@endsection
@push('css')
    @include('layouts.includes.datatable-css')
    <style>
        #projects-task-table thead th {
       position: sticky;top: 0;background: white;z-index: 0;/* Keep above the table rows but below dropdowns */
    }
        #custom_date_range {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 8px;
            background-color: #f9f9f9;
        }
        #custom_date_range .form-control {
            font-size: 12px;
        }
        /* Direct style for the date separator row */
        tr.yellow-divider {
            height: 5px !important;
            background-color: #00c4f5ff !important;
            display: table-row !important;
        }
        tr.yellow-divider td {
            padding: 0 !important;
            border: none !important;
            height: 5px !important;
            background-color: #00c4f5ff !important;
        }
        /* Yellow summary row style */
        tr.yellow-summary {
            background-color: #ffd700 !important;
            font-weight: bold;
        }
        tr.yellow-summary td {
            padding: 10px !important;
            background-color: #ffd700 !important;
            border: 1px solid #ddd !important;
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
    <div class="row">
        <div class="col-xl-12">
            <div class="container mt-5">
                <div class="row mt-5 align-items-center">
                    
                    <!-- Teamlogger Card -->
                    <div class="col-md-3">
                        <div class="card bg-warning">
                            <div class="card-body">
                                <h5 class="card-title">Teamlogger</h5>
                                <div class="d-flex align-items-center">
                                    <div class="bg-warning rounded p-2 me-3">
                                        <i class="ti ti-users text-white" style="font-size: 1.5rem;"></i>
                                    </div>
                                    <div>
                                        <h2 class="mb-0" id="teamlogger-count">--</h2>
                                        <span class="text-muted">Active Logins</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- ETC Card -->
                    <div class="col-md-3">
                        <div class="card bg-primary">
                            <div class="card-body">
                                <h5 class="card-title">ETC (Hrs)</h5>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary rounded p-2 me-3">
                                        <i class="ti ti-clock text-white" style="font-size: 1.5rem;"></i>
                                    </div>
                                    <div>
                                        <h2 class="mb-0" id="total-etc-min">{{$totalETAmin}}</h2>
                                        <span class="text-muted">Total Hrs</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info">
                            <div class="card-body">
                                <h5 class="card-title">ATC (Hrs)</h5>
                                <div class="d-flex align-items-center">
                                    <div class="bg-info rounded p-2 me-3">
                                        <i class="ti ti-clock text-white" style="font-size: 1.5rem;"></i>
                                    </div>
                                    <div>
                                        <h2 class="mb-0" id="total-atc-min">{{$totalATCMin}}</h2>
                                        <span class="text-muted">Total Hrs</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success">
                            <div class="card-body">
                                <h5 class="card-title">AVG Day</h5>
                                <div class="d-flex align-items-center">
                                    <div class="bg-success rounded p-2 me-3">
                                        <i class="ti ti-calendar text-white" style="font-size: 1.5rem;"></i>
                                    </div>
                                    <div>
                                        <h2 class="mb-0" id="avg-completion-days">{{$avgCompletionDays ?? 0}}</h2>
                                        <span class="text-muted">Days</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
    <label for="dateFilter">Completion Date:</label>
    <select id="date_filter" name="date_filter" class="form-select">
        <option value="">All Dates</option>
        <option value="today">Today</option>
        <option value="yesterday">Yesterday</option>
        <option value="this_week">This Week</option>
        <option value="this_month">This Month</option>
        <option value="previous_month">Previous Month</option>
        <option value="last_30_days" selected>Last 30 Days</option>
        <option value="custom">Custom Date Range</option>
        <!-- <option value="01">January</option>
        <option value="02">February</option>
        <option value="03">March</option>
        <option value="04">April</option>
        <option value="05">May</option>
        <option value="06">June</option>
        <option value="07">July</option>
        <option value="08">August</option>
        <option value="09">September</option>
        <option value="10">October</option>
        <option value="11">November</option>
        <option value="12">December</option> -->
    </select>
</div>

<!-- Custom Date Range Picker (for "custom" option only) -->
<div class="col-md-3" id="custom_date_range" style="display: none;">
    <div class="row g-2">
        <div class="col-6">
            <label for="start_date" class="form-label small">From:</label>
            <input type="date" id="start_date" name="start_date" class="form-control">
        </div>
        <div class="col-6">
            <label for="end_date" class="form-label small">To:</label>
            <input type="date" id="end_date" name="end_date" class="form-control">
        </div>
    </div>
</div>

                    <div class="col-md-2">
                        <label for="groupInput">Group:</label>
                        <input type="text" id="group_name" name="group_name" class="form-control" placeholder="Enter group name">
                    </div>
                    <div class="col-md-2">
                        <label for="taskInput">Task:</label>
                        <input type="text" id="task_name" name="task_name" class="form-control" placeholder="Enter task name">
                    </div>
                    
                    <div class="col-md-4">
                        <form class="d-flex gap-2 align-items-center">
                            <div class="flex-grow-1">
                                <label class="form-label">{{ __('Assignor')}}</label>
                                <select class=" multi-select choices" id="assignor_name" name="assignor_name"  multiple="multiple" data-placeholder="{{ __('Select Users ...') }}" required>
                                    <option value="">{{__('Select assignor')}}</option>
                                    @foreach($users as $u)
                                        <option value="{{$u->email}}">{{$u->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label class="form-label">{{ __('Assignee')}}</label>
                                <select class=" multi-select choices" id="assignee_name" name="assignee_name"  multiple="multiple" data-placeholder="{{ __('Select Users ...') }}" required>
                                    <option value="">{{__('Select Assignee')}}</option>
                                    @foreach($users as $u)
                                        <option value="{{$u->email}}">{{$u->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                         
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive overflow_hidden">
                        {{ $dataTable->table(['width' => '100%']) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('layouts.includes.datatable-js')
    {{ $dataTable->scripts() }}

    <script>
        // Function to manually apply date dividers
        function applyDateDividers() {
            console.log('Applying date dividers - new method');
            
            // First, remove any existing dividers and summary rows
            $('.yellow-divider, .yellow-summary').remove();
            
            var rows = $('#projects-task-table tbody tr').not('.yellow-divider, .yellow-summary');
            var prevDate = null;
            
            // Create a map of rows by date
            var dateMap = {};
            var dateAssignees = {}; // Store assignees per date
            
            // First pass - gather information
            rows.each(function(index) {
                var currentRow = $(this);
                var dateCell = currentRow.find('td:nth-child(9)'); // Completion date column
                var currentDate = dateCell.text().trim();
                
                if (currentDate && currentDate !== "-") {
                    if (!dateMap[currentDate]) {
                        dateMap[currentDate] = [];
                        dateAssignees[currentDate] = new Set();
                    }
                    dateMap[currentDate].push(currentRow);
                    
                    // Extract assignee emails from the row data (get from filters)
                    // We'll use the global filter values instead of parsing from table
                }
            });
            
            // Sort dates
            var sortedDates = Object.keys(dateMap).sort();
            console.log('Found dates: ', sortedDates);
            
            // Second pass - insert dividers and summary rows
            for (var i = 1; i < sortedDates.length; i++) {
                var date = sortedDates[i];
                var firstRowForDate = dateMap[date][0];
                
                // Calculate totals for previous date group
                var prevDate = sortedDates[i - 1];
                var prevDateRows = dateMap[prevDate];
                var totalETC = 0;
                var totalATC = 0;
                
                prevDateRows.forEach(function(row) {
                    // ETC column is 7th (index 6), ATC column is 8th (index 7)
                    var etcText = row.find('td:nth-child(7)').text().trim();
                    var atcText = row.find('td:nth-child(8)').text().trim();
                    
                    // Parse the numbers (remove any non-numeric characters except decimal point)
                    var etcValue = parseFloat(etcText.replace(/[^\d.-]/g, '')) || 0;
                    var atcValue = parseFloat(atcText.replace(/[^\d.-]/g, '')) || 0;
                    
                    totalETC += etcValue;
                    totalATC += atcValue;
                });
                
                // Convert minutes to hours (divide by 60)
                var etcHours = (totalETC / 60).toFixed(2);
                var atcHours = (totalATC / 60).toFixed(2);
                
                // Create yellow summary row with teamlogger placeholder
                var summaryRow = $('<tr class="yellow-summary">' +
                '<td colspan="2" style="text-align: center;"><strong class="teamlogger-data" data-date="' + prevDate + '">Login: <span class="teamlogger-hours">...</span></strong></td>' +
                    '<td colspan="2" style="text-align: center; padding-right: 10px;">TCD: ' + prevDate + '</td>' +
                    '<td style="text-align: center;" title="ETC: '+totalETC+'(' + etcHours + ' hrs)">ETC: ' + totalETC + ' (' + Math.round(etcHours * 10) / 10 + ' hrs)</td>' +
                    '<td style="text-align: center;" title="ATC: '+totalATC+'(' + atcHours + ' hrs)">ATC: ' + totalATC + ' (' + Math.round(atcHours * 10) / 10 + ' hrs)</td>' +
                    '<td colspan="8"></td>' +
                    '</tr>');
                
                // Insert summary row after the last row of previous date
                var lastRowOfPrevDate = prevDateRows[prevDateRows.length - 1];
                lastRowOfPrevDate.after(summaryRow);
                
                // Create blue divider row after summary
                var dividerRow = $('<tr class="yellow-divider"><td colspan="14"></td></tr>');
                summaryRow.after(dividerRow);
                
                console.log('Inserted summary and divider for date: ', prevDate, 'ETC:', totalETC, 'ATC:', totalATC);
                
                // Fetch teamlogger data for this date
                fetchTeamloggerForDate(prevDate);
            }
            
            // Add summary for the last date group as well
            if (sortedDates.length > 0) {
                var lastDate = sortedDates[sortedDates.length - 1];
                var lastDateRows = dateMap[lastDate];
                var totalETC = 0;
                var totalATC = 0;
                
                lastDateRows.forEach(function(row) {
                    var etcText = row.find('td:nth-child(7)').text().trim();
                    var atcText = row.find('td:nth-child(8)').text().trim();
                    
                    var etcValue = parseFloat(etcText.replace(/[^\d.-]/g, '')) || 0;
                    var atcValue = parseFloat(atcText.replace(/[^\d.-]/g, '')) || 0;
                    
                    totalETC += etcValue;
                    totalATC += atcValue;
                });
                
                var etcHours = (totalETC / 60).toFixed(2);
                var atcHours = (totalATC / 60).toFixed(2);
                
                var summaryRow = $('<tr class="yellow-summary">' +
                '<td colspan="2" style="text-align: center;"><strong class="teamlogger-data" data-date="' + lastDate + '">Login: <span class="teamlogger-hours">...</span></strong></td>' +
                    '<td colspan="2" style="text-align: center; padding-right: 10px;">TCD: ' + lastDate + '</td>' +
                    '<td style="text-align: center;" title="ETC: '+totalETC+'(' + etcHours + ' hrs)">ETC: ' + totalETC + ' (' + Math.round(etcHours * 10) / 10 + ' hrs)</td>' +
                    '<td style="text-align: center;" title="ATC: '+totalATC+'(' + atcHours + ' hrs)">ATC: ' + totalATC + ' (' + Math.round(atcHours * 10) / 10 + ' hrs)</td>' +
                    '<td colspan="8"></td>' +
                    '</tr>');
                
                var lastRowOfLastDate = lastDateRows[lastDateRows.length - 1];
                lastRowOfLastDate.after(summaryRow);
                
                console.log('Inserted summary for last date: ', lastDate, 'ETC:', totalETC, 'ATC:', totalATC);
                
                // Fetch teamlogger data for last date
                fetchTeamloggerForDate(lastDate);
            }
        }
        
        // Function to fetch teamlogger data for a specific date
        function fetchTeamloggerForDate(completionDate) {
            // Parse date in MM-DD format to full date
            var currentYear = new Date().getFullYear();
            var dateParts = completionDate.split('-');
            var fullDate = currentYear + '-' + dateParts[0] + '-' + dateParts[1];

            // alert(fullDate);
            
            // Get current filter values
            var assigneeEmails = $('#assignee_name').val() || [];
            var assignorEmails = $('#assignor_name').val() || [];
            
            console.log('Fetching teamlogger for date:', completionDate, 'Full date:', fullDate);
            console.log('Assignee filter:', assigneeEmails);
            console.log('Assignor filter:', assignorEmails);
            
            $.ajax({
                url: "{{ route('projecttask.teamlogger.by.date') }}",
                type: 'GET',
                data: {
                    completion_date: fullDate,
                    assignee_emails: assigneeEmails,
                    assignor_emails: assignorEmails
                },
                success: function(response) {
                    console.log('Teamlogger response for ' + completionDate + ':', response);
                    if (response.success) {
                        var hours = response.activeHours.toFixed(1) || 0;
                        $('.teamlogger-data[data-date="' + completionDate + '"] .teamlogger-hours').text(hours + ' hrs');
                        
                        // Log debug info if available
                        if (response.debug) {
                            console.log('Debug info for ' + completionDate + ':', response.debug);
                        }
                    } else {
                        console.error('Teamlogger failed for ' + completionDate + ':', response.message);
                        $('.teamlogger-data[data-date="' + completionDate + '"] .teamlogger-hours').text('0 hrs');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Teamlogger error for ' + completionDate + ':', error);
                    console.error('Response:', xhr.responseText);
                    $('.teamlogger-data[data-date="' + completionDate + '"] .teamlogger-hours').text('Error');
                }
            });
        }
        
        $(document).ready(function () {
            initializeDataTable();
            loadTeamloggerData();
            getTaskCount();
            
            // Apply date dividers after a small delay to ensure table is fully loaded
            setTimeout(applyDateDividers, 500);
            
            // Reload DataTable when filter values change
            $('#assignee_name, #assignor_name, #month, #group_name, #date_filter, #start_date, #end_date, #task_name').on('keyup change', function () {
                initializeDataTable();
                updateFilteredData();
            });
            
            // Handle custom date range visibility
            $('#date_filter').on('change', function () {
                if ($(this).val() === 'custom') {
                    $('#custom_date_range').show();
                } else {
                    $('#custom_date_range').hide();
                    $('#start_date, #end_date').val('');
                }
                updateFilteredData();
            });
            
            // Add date validation for custom range
            $('#start_date, #end_date').on('change', function() {
                var startDate = $('#start_date').val();
                var endDate = $('#end_date').val();
                
                if (startDate && endDate && startDate > endDate) {
                    toastrs('{{ __('Error') }}', '{{ __('End date must be after start date') }}', 'error');
                    $(this).val(''); // Clear the invalid date
                    return;
                }
                updateFilteredData();
            });
        });
        
        // Function to fetch Teamlogger data
        async function loadTeamloggerData() {
            try {
                console.log('Fetching Teamlogger data via backend...');
                
                // Get current filter values
                const assigneeEmails = $('#assignee_name').val() || [];
                const assignorEmails = $('#assignor_name').val() || [];
                const dateFilter = $('#date_filter').val();
                const startDate = $('#start_date').val();
                const endDate = $('#end_date').val();
                
                // Show loading state
                $('#teamlogger-count').text('...');
                
                const requestData = {
                    assignee_emails: assigneeEmails,
                    assignor_emails: assignorEmails,
                    date_filter: dateFilter
                };
                
                // Add custom date range if selected
                if (dateFilter === 'custom') {
                    if (startDate) requestData.start_date = startDate;
                    if (endDate) requestData.end_date = endDate;
                }
                
                console.log('Sending request with filters:', requestData);
                
                $.ajax({
                    url: '{{ route("projecttask.teamlogger.data") }}',
                    method: 'GET',
                    dataType: 'json',
                    data: requestData,
                    success: function(response) {
                        console.log('Teamlogger backend response:', response);
                        if (response.success) {
                            // Display activeHours (totalHours - idleHours) instead of just totalHours
                            const displayValue = response.activeHours || '0';
                            $('#teamlogger-count').text(Math.round(displayValue));
                            console.log('Updated teamlogger active hours to:', displayValue);
                            console.log('Total Hours:', response.totalHours, 'Idle Hours:', response.idleHours, 'Active Hours:', response.activeHours);
                            console.log('Target emails:', response.targetEmails);
                            console.log('Found emails:', response.foundEmails);
                        } else {
                            console.error('Backend error:', response.message);
                            $('#teamlogger-count').text('0');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching Teamlogger data from backend:', error);
                        console.error('Response:', xhr.responseText);
                        $('#teamlogger-count').text('0');
                    }
                });
                
            } catch (error) {
                console.error('Error in loadTeamloggerData:', error);
                $('#teamlogger-count').text('0');
            }
        }
        
        // Function to update filtered data including Teamlogger
        function updateFilteredData() {
            const assignorEmails = $('#assignor_name').val() || [];
            const assigneeEmails = $('#assignee_name').val() || [];
            
            console.log('updateFilteredData called with filters:', {
                assignorEmails: assignorEmails,
                assigneeEmails: assigneeEmails
            });
            
            // Always load Teamlogger data based on current filters
            loadTeamloggerData();
            
            // Update other filtered data
            getTaskCount();
        }
        
        function getTaskCount(searchValue = "") {
            var requestData = {
                assignee_name: $('#assignee_name').val() || [],
                assignor_name: $('#assignor_name').val() || [],
                status_name: $('#status_name').val(),
                search_value: searchValue,
                month: $('#month').val(),
                group_name: $('#group_name').val(),
                task_name: $('#task_name').val(),
                date_filter: $('#date_filter').val()
            };
            
            // Only send custom dates when custom is selected
            if ($('#date_filter').val() === 'custom') {
                requestData.start_date = $('#start_date').val();
                requestData.end_date = $('#end_date').val();
                
                // Debug logging
                console.log('getTaskCount with custom dates:', requestData);
            }
            
            $.ajax({
                url: "{{ route('projecttask.done.count') }}",
                type: 'get',
                data: requestData,
                dataType: 'JSON',
                success: function(response) {
                    console.log('Task count response:', response);
                    if (response.is_success && response.data) {
                        $("#total-etc-min").html(Math.round(response.data.total_eta_hours) || 0);
                        $("#total-atc-min").html(Math.round(response.data.total_atc_hours) || 0);
                        $("#avg-completion-days").html(Math.round(response.data.avg_completion_days) || 0);
                        // You can add more UI updates here if needed
                        console.log('Updated done task counts - Total Tasks:', response.data.total_tasks, 'ETA Hours:', response.data.total_eta_hours, 'ATC Hours:', response.data.total_atc_hours, 'AVG Days:', response.data.avg_completion_days);
                    } else {
                        $("#total-etc-min").html(0);
                        $("#total-atc-min").html(0);
                        $("#avg-completion-days").html(0);
                        console.log('No valid data received for task counts');
                    }
                },
                error: function(data) {
                    data = data.responseJSON;

                    if (data.message) {
                        toastrs('{{ __('Error') }}', data.message, 'error');
                    } else {
                        toastrs('{{ __('Error') }}', '{{ __('Some Thing Is Wrong!') }}', 'error');
                    }
                }
            });
        }
        
        function initializeDataTable() {
            if ($.fn.DataTable.isDataTable('#projects-task-table')) {
                $('#projects-task-table').DataTable().destroy();
            }

            var datatableData = $('#projects-task-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 100, // Show 100 records per page
                lengthMenu: [10, 25, 50, 100, 200], // Allow user to select different page lengths
                dom: 'Bfrtip',
                createdRow: function(row, data, dataIndex) {
                    // Add custom attributes to the row if needed
                    $(row).attr('data-completion-date', data.completion_date);
                },
                drawCallback: function(settings) {
                    // Reinitialize tooltips and other Bootstrap components
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll("[data-bs-toggle=tooltip]"));
                    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });
                    
                    // Force apply yellow divider lines with a delay to ensure DOM is updated
                    setTimeout(function() {
                        applyDateDividers();
                    }, 200);
                },
                buttons: [
                 {
                    extend: 'collection',
                    className: 'btn btn-light-secondary dropdown-toggle',
                    text: '<i class="ti ti-download me-2" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Export"></i>',
                    buttons: [
                        {
                            extend: 'print',
                            text: '<i class="fas fa-print me-2"></i> Print',
                            className: 'btn btn-light text-primary dropdown-item',
                            exportOptions: { columns: [0, 1, 3] }
                        },
                        {
                            extend: 'csv',
                            text: '<i class="fas fa-file-csv me-2"></i> CSV',
                            className: 'btn btn-light text-primary dropdown-item',
                            exportOptions: { columns: [0, 1, 3] }
                        },
                        {
                            extend: 'excel',
                            text: '<i class="fas fa-file-excel me-2"></i> Excel',
                            className: 'btn btn-light text-primary dropdown-item',
                            exportOptions: { columns: [0, 1, 3] }
                        }
                    ]
                },
                {
                    extend: 'reset',
                    text: '<i class="fas fa-undo"></i> ',
                    className: 'btn btn-light-danger'
                },
                {
                    extend: 'reload',
                    text: '<i class="fas fa-sync-alt"></i> ',
                    className: 'btn btn-light-warning'
                }
            ],
                ajax: {
                    url: "{{ route('projecttask.done.list') }}",
                    data: function (d) {
                        d.assignee_name = $('#assignee_name').val() || [];
                        d.assignor_name = $('#assignor_name').val() || [];
                        d.month = $('#month').val();
                        d.group_name = $('#group_name').val();
                        d.task_name = $('#task_name').val(); // <-- add this line
                        d.date_filter = $('#date_filter').val();
                        
                        // Only send custom dates when custom is selected
                        if ($('#date_filter').val() === 'custom') {
                            d.start_date = $('#start_date').val();
                            d.end_date = $('#end_date').val();
                            
                            // Debug logging
                            console.log('Custom date filter data:', {
                                date_filter: d.date_filter,
                                start_date: d.start_date,
                                end_date: d.end_date
                            });
                        }
                    }
                },
                columns: [
                    { data: 'group', name: 'group' },
                    { data: 'title', name: 'title' },
                    { data: 'assigner_name', name: 'assigner_name' },
                    { data: 'assign_to', name: 'assign_to' },
                   
                    { data: 'start_date', name: 'start_date' },
                    { data: 'due_date', name: 'due_date' },
                     { data: 'eta_time', name: 'eta_time' },
                    { data: 'etc_done', name: 'etc_done' },
                    { data: 'completion_date', name: 'completion_date' },
                    { data: 'completion_day', name: 'completion_day' },

                    { data: 'status', name: 'status' },
                    { data: 'priority', name: 'priority' },
                    { data: 'links', name: 'links' },
                    { data: 'link7', name: 'link7' },
                ]
            });
            datatableData.on("search.dt", function () {
                let searchValue = datatableData.search();
                getTaskCount(searchValue);
            });
            
            // Apply date dividers after any DataTable redraw
            datatableData.on('draw.dt', function() {
                console.log('Table redrawn - applying date dividers');
                setTimeout(applyDateDividers, 100); // Slight delay to ensure DOM is ready
            });
            
            // Additional events to catch any possible table updates
            datatableData.on('page.dt', function() {
                setTimeout(applyDateDividers, 100);
            });
            
            datatableData.on('length.dt', function() {
                setTimeout(applyDateDividers, 100);
            });
            
            // Fallback timer to keep checking for 5 seconds after load
            var checkCount = 0;
            var dividerInterval = setInterval(function() {
                applyDateDividers();
                checkCount++;
                if (checkCount > 10) { // Check 10 times (5 seconds)
                    clearInterval(dividerInterval);
                }
            }, 500);
        }
    </script>
@endpush