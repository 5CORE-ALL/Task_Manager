<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Salary Board</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 20px 20px 0 0;
            position: relative;
            overflow: hidden;
        }
        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="80" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="60" r="1" fill="rgba(255,255,255,0.1)"/></svg>');
            pointer-events: none;
        }
        .page-header .content {
            position: relative;
            z-index: 1;
        }
        .stats-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }
        .stats-icon.primary { background: linear-gradient(135deg, #667eea, #764ba2); color: white; }
        .stats-icon.success { background: linear-gradient(135deg, #56ab2f, #a8e6cf); color: white; }
        .stats-icon.warning { background: linear-gradient(135deg, #f093fb, #f5576c); color: white; }
        .stats-icon.info { background: linear-gradient(135deg, #4facfe, #00f2fe); color: white; }
        
        .table-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
        }
        .table th {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            font-weight: 600;
            border: none;
            padding: 15px 12px;
            color: #495057;
            font-size: 0.9rem;
        }
        .table td {
            padding: 15px 12px;
            vertical-align: middle;
            border-color: #f1f3f4;
        }
        .employee-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .employee-avatar {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 16px;
        }
        .badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.8rem;
        }
        .badge-active { background: linear-gradient(135deg, #56ab2f, #a8e6cf); }
        .badge-inactive { background: linear-gradient(135deg, #ff6b6b, #ffa8a8); }
        .badge-probation { background: linear-gradient(135deg, #f093fb, #f5576c); }
        
        .btn-action {
            padding: 8px 16px;
            border-radius: 10px;
            border: none;
            font-weight: 500;
            transition: all 0.3s ease;
            margin: 2px;
        }
        .btn-view {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        .btn-edit {
            background: linear-gradient(135deg, #56ab2f, #a8e6cf);
            color: white;
        }
        .btn-delete {
            background: linear-gradient(135deg, #ff6b6b, #ffa8a8);
            color: white;
        }
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .salary-amount {
            font-weight: 700;
            font-size: 1.1rem;
        }
        .department-tag {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .nav-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .nav-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            border-radius: 25px;
            padding: 10px 20px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        .nav-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
        }

        .select-all-row {
            background-color: #e3f2fd;
        }
        .select-checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        .email-cell {
            color: #666;
            font-size: 0.9rem;
        }
        .bulk-actions {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            display: none;
        }
        .bulk-actions.show {
            display: block;
        }
        .bulk-action-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            border-radius: 8px;
            padding: 8px 16px;
            margin-right: 10px;
            transition: all 0.3s ease;
        }
        .bulk-action-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
        }
        }
        .currency {
            font-weight: 600;
            color: #2e7d32;
        }
        .employee-name {
            font-weight: 600;
            color: #1976d2;
        }
        .department {
            color: #5d4037;
            font-size: 0.85rem;
        }
        .email {
            color: #424242;
            font-size: 0.8rem;
        }
        .total-payable {
            background-color: #4caf50 !important;
            color: white !important;
            font-weight: bold;
        }
        .payment-done {
            background-color: #4caf50 !important;
            color: white !important;
            font-weight: bold;
        }
        .btn-back {
            background: linear-gradient(to right, #6c757d, #495057);
            border: none;
            color: white;
            border-radius: 25px;
            padding: 10px 20px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-back:hover {
            background: linear-gradient(to right, #495057, #6c757d);
            color: white;
            text-decoration: none;
        }
        .summary-cards {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        .summary-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 10px;
            flex: 1;
            text-align: center;
        }
        .summary-card h5 {
            margin: 0;
            font-size: 1.1rem;
        }
        .summary-card .value {
            font-size: 1.5rem;
            font-weight: bold;
            margin-top: 5px;
        }
        .table-container {
            overflow-x: auto;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        /* Highlight effect for changed values */
        .highlight-change {
            background-color: #fff3cd !important;
            border: 2px solid #ffc107 !important;
            border-radius: 4px;
            transition: all 0.3s ease;
        }
        
        /* Improved styling for editable input */
        .prd-hour-input {
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 4px 8px;
            text-align: center;
            transition: border-color 0.3s ease;
        }
        
        .prd-hour-input:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
    </style>
</head>
<body>

<div class="container-fluid py-4">
    <div class="card">
        <div class="page-header">
            <div class="content">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h2 class="mb-0"><i class="bi bi-people-fill me-2"></i>Salary Board</h2>
                        <p class="mb-0 mt-2 opacity-90">Comprehensive overview of employee salary information</p>
                    </div>
                    <div class="nav-buttons">
                        <a href="{{ route('salary.incentive') }}" class="nav-btn">
                            <i class="bi bi-cash-coin"></i>
                            Add Incentive
                        </a>
                        <a href="{{ route('salary.incentive-records') }}" class="nav-btn">
                            <i class="bi bi-list-ul"></i>
                            Incentive Records
                        </a>
                        <a href="{{ route('salary.increment-records') }}" class="nav-btn">
                            <i class="bi bi-graph-up-arrow"></i>
                            Increment Records
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card">
                        <div class="stats-icon primary">
                            <i class="bi bi-people"></i>
                        </div>
                        <h3 class="h4 mb-1">{{ isset($employees) && is_array($employees) ? count($employees) : 0 }}</h3>
                        <p class="text-muted mb-0">Total Employees</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card">
                        <div class="stats-icon success">
                            <i class="bi bi-currency-rupee"></i>
                        </div>
                        <h3 class="h4 mb-1">₹{{ number_format($totalSalary ?? 0, 0) }}</h3>
                        <p class="text-muted mb-0">Total Monthly Salary</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card">
                        <div class="stats-icon warning">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <h3 class="h4 mb-1">₹{{ number_format($avgSalary ?? 0, 0) }}</h3>
                        <p class="text-muted mb-0">Average Salary</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="stats-card">
                        <div class="stats-icon info">
                            <i class="bi bi-building"></i>
                        </div>
                        <h3 class="h4 mb-1">{{ isset($departments) && is_array($departments) ? count($departments) : 0 }}</h3>
                        <p class="text-muted mb-0">Departments</p>
                    </div>
                </div>
            </div>

            <!-- Department Filter Only -->
            <div class="row mb-4">
                <div class="col-md-6 offset-md-6 text-end">
                    <select id="departmentFilter" class="form-select" style="width: auto; display: inline-block;">
                        <option value="">All Departments</option>
                        @if(isset($departments) && is_array($departments))
                            @foreach($departments as $dept)
                                <option value="{{ $dept }}">{{ $dept }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            <!-- Bulk Actions -->
            <div id="bulkActions" class="bulk-actions">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong id="selectedCount">0</strong> employees selected
                    </div>
                    <div>
                        <button class="bulk-action-btn" onclick="bulkAction('pay')">
                            <i class="bi bi-cash-coin me-1"></i>Mark as Paid
                        </button>
                        <button class="bulk-action-btn" onclick="bulkAction('export')">
                            <i class="bi bi-download me-1"></i>Export Selected
                        </button>
                        <button class="bulk-action-btn" onclick="clearSelection()">
                            <i class="bi bi-x-circle me-1"></i>Clear Selection
                        </button>
                    </div>
                </div>
            </div>

            <!-- Salary Board Table -->
            <div class="table-container">
                @if(isset($employees) && is_array($employees) && count($employees) > 0)
                    <div class="table-responsive">
                        <table id="salaryBoardTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">
                                        <input type="checkbox" id="selectAll" class="select-checkbox" title="Select All">
                                    </th>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Email Address</th>
                                    <th>Basic Salary</th>
                                    <th>Increment</th>
                                    <th>Current Salary</th>
                                    <th>Total Prd Hour</th>
                                    <th>Incentives</th>
                                    <th>Total Salary</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employees as $employee)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="employee-checkbox select-checkbox" value="{{ $employee['id'] ?? 0 }}" data-employee-name="{{ $employee['name'] ?? 'N/A' }}">
                                        </td>
                                        <td>
                                            <div class="employee-info">
                                                <div class="employee-avatar">
                                                    {{ strtoupper(substr($employee['name'] ?? 'N', 0, 2)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $employee['name'] ?? 'N/A' }}</div>
                                                    <small class="text-muted">ID: {{ $employee['employee_id'] ?? 'N/A' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="department-tag">{{ $employee['department'] ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <span class="email-cell">{{ $employee['email'] ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <span class="salary-amount text-primary">₹{{ number_format($employee['basic_salary'] ?? 0, 0) }}</span>
                                        </td>
                                        <td>
                                            <span class="salary-amount text-warning">₹{{ number_format($employee['increment'] ?? 0, 0) }}</span>
                                        </td>
                                        <td>
                                            <span class="salary-amount text-info">₹{{ number_format($employee['current_salary'] ?? 0, 0) }}</span>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control prd-hour-input" 
                                                   value="{{ $employee['total_prd_hour'] ?? 0 }}" 
                                                   data-employee-id="{{ $employee['id'] ?? 0 }}"
                                                   min="0" max="300" 
                                                   style="width: 80px; font-size: 0.9rem;"
                                                   onchange="updateTotalSalary(this)">
                                        </td>
                                        <td>
                                            <span class="salary-amount text-success">₹{{ number_format($employee['incentives'] ?? 0, 0) }}</span>
                                        </td>
                                        <td>
                                            <span class="salary-amount text-dark fw-bold total-salary-{{ $employee['id'] ?? 0 }}">
                                                ₹{{ number_format($employee['total_salary'] ?? 0, 0) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <button class="btn btn-action btn-view btn-sm" onclick="viewEmployee({{ $employee['id'] ?? 0 }})" title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button class="btn btn-action btn-edit btn-sm" onclick="editEmployee({{ $employee['id'] ?? 0 }})" title="Edit Salary">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-action btn-delete btn-sm" onclick="deleteEmployee({{ $employee['id'] ?? 0 }})" title="Remove">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-people display-1 text-muted"></i>
                        <h4 class="text-muted mt-3">No Employee Data Found</h4>
                        <p class="text-muted">No salary information available at the moment.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Employee Detail Modal -->
<div class="modal fade" id="employeeModal" tabindex="-1" aria-labelledby="employeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="employeeModalLabel">
                    <i class="bi bi-person-circle me-2"></i>Employee Salary Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="employeeDetails">
                <!-- Employee details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    let table = $('#salaryBoardTable').DataTable({
        responsive: true,
        pageLength: 25,
        order: [[1, 'asc']], // Employee name column
        columnDefs: [
            { targets: [0, 9], orderable: false }, // Checkbox and Actions columns not orderable
            { targets: [4, 5, 6, 7, 8], className: 'text-end' } // Salary columns right-aligned
        ],
        language: {
            lengthMenu: "Show _MENU_ employees per page",
            info: "Showing _START_ to _END_ of _TOTAL_ employees",
            infoEmpty: "No employees available",
            infoFiltered: "(filtered from _MAX_ total employees)"
        },
        searching: false // Disable search functionality
    });

    // Select All functionality
    $('#selectAll').on('change', function() {
        const isChecked = this.checked;
        $('.employee-checkbox').prop('checked', isChecked);
        updateBulkActions();
    });

    // Individual checkbox functionality
    $(document).on('change', '.employee-checkbox', function() {
        updateSelectAllCheckbox();
        updateBulkActions();
    });

    // Update Select All checkbox based on individual selections
    function updateSelectAllCheckbox() {
        const totalCheckboxes = $('.employee-checkbox').length;
        const checkedCheckboxes = $('.employee-checkbox:checked').length;
        
        if (checkedCheckboxes === 0) {
            $('#selectAll').prop('indeterminate', false).prop('checked', false);
        } else if (checkedCheckboxes === totalCheckboxes) {
            $('#selectAll').prop('indeterminate', false).prop('checked', true);
        } else {
            $('#selectAll').prop('indeterminate', true).prop('checked', false);
        }
    }

    // Update bulk actions visibility and count
    function updateBulkActions() {
        const checkedBoxes = $('.employee-checkbox:checked');
        const count = checkedBoxes.length;
        
        if (count > 0) {
            $('#bulkActions').addClass('show');
            $('#selectedCount').text(count);
        } else {
            $('#bulkActions').removeClass('show');
        }
    }

    // Global search
    $('#globalSearch').on('keyup', function() {
        table.search(this.value).draw();
    });

    // Department filter
    $('#departmentFilter').on('change', function() {
        if (this.value === '') {
            table.column(2).search('').draw(); // Department is still column 2
        } else {
            table.column(2).search(this.value).draw();
        }
    });
});

// Bulk Actions Functions
function bulkAction(action) {
    const selectedEmployees = [];
    $('.employee-checkbox:checked').each(function() {
        selectedEmployees.push({
            id: $(this).val(),
            name: $(this).data('employee-name')
        });
    });
    
    if (selectedEmployees.length === 0) {
        alert('Please select at least one employee.');
        return;
    }
    
    switch(action) {
        case 'pay':
            bulkMarkAsPaid(selectedEmployees);
            break;
        case 'export':
            bulkExport(selectedEmployees);
            break;
        default:
            console.log('Unknown bulk action:', action);
    }
}

function bulkMarkAsPaid(employees) {
    const employeeNames = employees.map(emp => emp.name).join(', ');
    if (confirm(`Mark salary as paid for ${employees.length} employee(s)?\n\nEmployees: ${employeeNames}`)) {
        // Here you would implement the actual payment marking logic
        alert(`Marked ${employees.length} employee(s) as paid successfully!`);
        clearSelection();
    }
}

function bulkExport(employees) {
    const employeeNames = employees.map(emp => emp.name).join(', ');
    alert(`Exporting salary data for ${employees.length} employee(s):\n\n${employeeNames}\n\nThis would generate a downloadable report.`);
}

function clearSelection() {
    $('.employee-checkbox').prop('checked', false);
    $('#selectAll').prop('checked', false).prop('indeterminate', false);
    updateBulkActions();
}

function viewEmployee(id) {
    // Show employee details in modal
    $('#employeeDetails').html(`
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading employee details...</p>
        </div>
    `);
    $('#employeeModal').modal('show');
    
    // Simulate loading employee details
    setTimeout(() => {
        $('#employeeDetails').html(`
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary">Basic Information</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Employee ID:</strong></td><td>EMP${id.toString().padStart(3, '0')}</td></tr>
                        <tr><td><strong>Name:</strong></td><td>Employee ${id}</td></tr>
                        <tr><td><strong>Department:</strong></td><td>Sample Department</td></tr>
                        <tr><td><strong>Position:</strong></td><td>Sample Position</td></tr>
                        <tr><td><strong>Status:</strong></td><td><span class="badge badge-active">Active</span></td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 class="text-primary">Salary Breakdown</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Basic Salary:</strong></td><td>₹45,000</td></tr>
                        <tr><td><strong>Current Incentives:</strong></td><td>₹8,000</td></tr>
                        <tr><td><strong>Total Salary:</strong></td><td class="fw-bold">₹53,000</td></tr>
                        <tr><td><strong>Last Updated:</strong></td><td>July 15, 2025</td></tr>
                    </table>
                </div>
            </div>
        `);
    }, 1000);
}

function editEmployee(id) {
    alert(`Edit salary for Employee ID: ${id}\n\nThis will open the salary edit form.`);
    // Implement edit functionality
}

function deleteEmployee(id) {
    if (confirm(`Are you sure you want to remove Employee ID: ${id} from the salary board?`)) {
        alert(`Employee ID: ${id} removed from salary board.`);
        // Implement delete functionality
    }
}

// Function to update total salary when Total Prd Hour changes
function updateTotalSalary(input) {
    const employeeId = $(input).data('employee-id');
    const totalPrdHour = parseFloat($(input).val()) || 0;
    
    // Get the current salary and incentives from the row
    const row = $(input).closest('tr');
    const currentSalaryText = row.find('td:nth-child(7) .salary-amount').text();
    const incentivesText = row.find('td:nth-child(9) .salary-amount').text();
    
    // Extract numeric values (remove ₹ and commas)
    const currentSalary = parseFloat(currentSalaryText.replace(/[₹,]/g, '')) || 0;
    const incentives = parseFloat(incentivesText.replace(/[₹,]/g, '')) || 0;
    
    // Calculate total salary: (Current Salary * Total Prd Hour / 200) + Incentive
    const totalSalary = (currentSalary * totalPrdHour / 200) + incentives;
    
    // Update the total salary display
    const totalSalaryElement = row.find(`.total-salary-${employeeId}`);
    totalSalaryElement.text('₹' + totalSalary.toLocaleString('en-IN', {maximumFractionDigits: 0}));
    
    // Add visual feedback
    totalSalaryElement.addClass('highlight-change');
    setTimeout(() => {
        totalSalaryElement.removeClass('highlight-change');
    }, 1000);
    
    // Here you could add AJAX call to save the change to backend
    // savePrdHourChange(employeeId, totalPrdHour, totalSalary);
}

// Add some interactive animations
$('.stats-card').hover(
    function() {
        $(this).addClass('shadow-lg');
    },
    function() {
        $(this).removeClass('shadow-lg');
    }
);
</script>

</body>
</html>
