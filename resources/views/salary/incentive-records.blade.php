<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Incentive Records</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #e0eafc, #cfdef3);
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }
        .page-header {
            background: linear-gradient(to right, #0d6efd, #6f42c1);
            color: white;
            padding: 20px;
            border-radius: 15px 15px 0 0;
        }
        .status-badge {
            font-size: 0.85rem;
            padding: 0.4rem 0.8rem;
        }
        .badge-pending { background-color: #ffc107; color: #000; }
        .badge-approved { background-color: #198754; }
        .badge-rejected { background-color: #dc3545; }
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-top: none;
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
    </style>
</head>
<body>

<div class="container my-5">
    <div class="card">
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0"><i class="bi bi-cash-coin me-2"></i>Incentive Records</h2>
                    <p class="mb-0 mt-2">Track and manage all incentive claims</p>
                </div>
                <div>
                    <a href="{{ route('salary.incentive') }}" class="btn-back">
                        <i class="bi bi-plus-circle"></i>
                        Add New Incentive
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(isset($incentives) && count($incentives) > 0)
                <div class="table-responsive">
                    <table id="incentiveTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Employee</th>
                                <th>Department</th>
                                <th>Month</th>
                                <th>Requested Amount</th>
                                <th>Approved Amount</th>
                                <th>Status</th>
                                <th>Reviewer</th>
                                <th>Reason</th>
                                <th>Submitted Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($incentives as $incentive)
                                <tr>
                                    <td><span class="badge bg-secondary">#{{ $incentive->id }}</span></td>
                                    <td>
                                        <strong>{{ $incentive->employee_name ?? 'N/A' }}</strong>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $incentive->department ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $incentive->incentive_month ? \Carbon\Carbon::parse($incentive->incentive_month)->format('M Y') : 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-primary">₹{{ number_format($incentive->requested_incentive ?? 0, 2) }}</span>
                                    </td>
                                    <td>
                                        @if($incentive->approved_incentive)
                                            <span class="fw-bold text-success">₹{{ number_format($incentive->approved_incentive, 2) }}</span>
                                        @else
                                            <span class="text-muted">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $status = $incentive->status ?? 'pending';
                                        @endphp
                                        @if($status === 'approved')
                                            <span class="badge status-badge badge-approved">Approved</span>
                                        @elseif($status === 'rejected')
                                            <span class="badge status-badge badge-rejected">Rejected</span>
                                        @else
                                            <span class="badge status-badge badge-pending">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $incentive->approved_by ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted" title="{{ $incentive->incentive_reason }}">
                                            {{ Str::limit($incentive->incentive_reason ?? 'N/A', 30) }}
                                        </small>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $incentive->created_at ? $incentive->created_at->format('d M Y, H:i') : 'N/A' }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button class="btn btn-outline-primary btn-edit" 
                                                    data-id="{{ $incentive->id }}" 
                                                    data-employee="{{ $incentive->employee_name }}"
                                                    data-department="{{ $incentive->department }}"
                                                    data-month="{{ $incentive->incentive_month }}"
                                                    data-requested="{{ $incentive->requested_incentive }}"
                                                    data-approved="{{ $incentive->approved_incentive }}"
                                                    data-reason="{{ $incentive->incentive_reason }}"
                                                    title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            
                                            @php
                                                $status = $incentive->status ?? 'pending';
                                            @endphp
                                            @if($status === 'pending')
                                                <button class="btn btn-outline-success btn-approve" 
                                                        data-id="{{ $incentive->id }}" 
                                                        title="Approve">
                                                    <i class="bi bi-check"></i>
                                                </button>
                                                <button class="btn btn-outline-danger btn-reject" 
                                                        data-id="{{ $incentive->id }}" 
                                                        title="Reject">
                                                    <i class="bi bi-x"></i>
                                                </button>
                                            @endif
                                            
                                            <button class="btn btn-outline-danger btn-delete" 
                                                    data-id="{{ $incentive->id }}" 
                                                    title="Delete">
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
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <h4 class="text-muted mt-3">No Incentive Records Found</h4>
                    <p class="text-muted">No incentive claims have been submitted yet.</p>
                    <a href="{{ route('salary.incentive') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Add First Incentive Claim
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editModalLabel">
                    <i class="bi bi-pencil-square me-2"></i>Edit Incentive Record
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Employee Name</label>
                            <input type="text" class="form-control" id="edit_employee" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Department</label>
                            <input type="text" class="form-control" id="edit_department" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Incentive Month</label>
                            <input type="month" class="form-control" id="edit_month" name="incentive_month">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Requested Amount (₹)</label>
                            <input type="number" class="form-control" id="edit_requested" name="requested_incentive">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Approved Amount (₹)</label>
                            <input type="number" class="form-control" id="edit_approved" name="approved_incentive">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Reason</label>
                            <textarea class="form-control" id="edit_reason" name="incentive_reason" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Update Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Approve/Reject Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" id="statusModalHeader">
                <h5 class="modal-title" id="statusModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="statusForm">
                <div class="modal-body">
                    <input type="hidden" id="status_id" name="id">
                    <input type="hidden" id="status_action" name="action">
                    <div class="mb-3">
                        <label class="form-label">Approved Amount (₹)</label>
                        <input type="number" class="form-control" id="status_amount" name="approved_incentive" min="0">
                        <small class="text-muted">Enter 0 for rejection or actual amount for approval</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Approval/Rejection Reason</label>
                        <textarea class="form-control" id="status_reason" name="approval_reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn" id="statusSubmitBtn">Confirm</button>
                </div>
            </form>
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
    if ($('#incentiveTable').length) {
        $('#incentiveTable').DataTable({
            responsive: true,
            pageLength: 25,
            order: [[0, 'desc']],
            columnDefs: [
                { targets: [8, 9, 10], orderable: false }
            ],
            language: {
                search: "Search records:",
                lengthMenu: "Show _MENU_ records per page",
                info: "Showing _START_ to _END_ of _TOTAL_ incentive records",
                infoEmpty: "No records available",
                infoFiltered: "(filtered from _MAX_ total records)"
            }
        });
    }

    // CSRF Token setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Edit button click
    $(document).on('click', '.btn-edit', function() {
        const data = $(this).data();
        $('#edit_id').val(data.id);
        $('#edit_employee').val(data.employee);
        $('#edit_department').val(data.department);
        $('#edit_month').val(data.month);
        $('#edit_requested').val(data.requested);
        $('#edit_approved').val(data.approved || '');
        $('#edit_reason').val(data.reason);
        $('#editModal').modal('show');
    });

    // Edit form submission
    $('#editForm').on('submit', function(e) {
        e.preventDefault();
        
        const id = $('#edit_id').val();
        const formData = {
            incentive_month: $('#edit_month').val(),
            requested_incentive: $('#edit_requested').val(),
            approved_incentive: $('#edit_approved').val() || null,
            incentive_reason: $('#edit_reason').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        console.log('Submitting edit form:', formData);

        $.ajax({
            url: `{{ url('/salary/incentive') }}/${id}/update`,
            method: 'PUT',
            data: formData,
            success: function(response) {
                console.log('Edit success:', response);
                $('#editModal').modal('hide');
                showAlert('success', 'Record updated successfully!');
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                console.log('Edit error details:', xhr);
                console.log('Response text:', xhr.responseText);
                let errorMessage = 'Error updating record. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    errorMessage = xhr.responseText;
                }
                showAlert('danger', errorMessage);
            }
        });
    });

    // Approve button click
    $(document).on('click', '.btn-approve', function() {
        const id = $(this).data('id');
        console.log('Approve button clicked for ID:', id);
        
        $('#status_id').val(id);
        $('#status_action').val('approve');
        $('#statusModalLabel').text('Approve Incentive');
        $('#statusModalHeader').removeClass('bg-danger').addClass('bg-success text-white');
        $('#statusSubmitBtn').removeClass('btn-danger').addClass('btn-success').text('Approve');
        $('#status_amount').attr('min', '1').attr('required', true).val('');
        $('#status_reason').val('');
        $('#statusModal').modal('show');
    });

    // Reject button click
    $(document).on('click', '.btn-reject', function() {
        const id = $(this).data('id');
        console.log('Reject button clicked for ID:', id);
        
        $('#status_id').val(id);
        $('#status_action').val('reject');
        $('#statusModalLabel').text('Reject Incentive');
        $('#statusModalHeader').removeClass('bg-success').addClass('bg-danger text-white');
        $('#statusSubmitBtn').removeClass('btn-success').addClass('btn-danger').text('Reject');
        $('#status_amount').val('0').attr('min', '0').attr('required', false);
        $('#status_reason').val('');
        $('#statusModal').modal('show');
    });

    // Status form submission
    $('#statusForm').on('submit', function(e) {
        e.preventDefault();
        
        const id = $('#status_id').val();
        const action = $('#status_action').val();
        const approved_amount = $('#status_amount').val();
        const approval_reason = $('#status_reason').val();
        
        console.log('Submitting status update:', {
            id: id,
            action: action,
            approved_incentive: approved_amount,
            approval_reason: approval_reason
        });

        $.ajax({
            url: `{{ url('/salary/incentive') }}/${id}/status`,
            method: 'PUT',
            data: {
                approved_incentive: approved_amount,
                approval_reason: approval_reason,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Success response:', response);
                $('#statusModal').modal('hide');
                showAlert('success', `Incentive ${action}d successfully!`);
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                console.log('Error details:', xhr);
                console.log('Response text:', xhr.responseText);
                let errorMessage = 'Error updating status. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    errorMessage = xhr.responseText;
                }
                showAlert('danger', errorMessage);
            }
        });
    });

    // Delete button click
    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        if (confirm('Are you sure you want to delete this incentive record? This action cannot be undone.')) {
            $.ajax({
                url: `{{ url('/salary/incentive') }}/${id}/delete`,
                method: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('Delete success:', response);
                    showAlert('success', 'Record deleted successfully!');
                    setTimeout(() => location.reload(), 1500);
                },
                error: function(xhr) {
                    console.log('Delete error details:', xhr);
                    console.log('Response text:', xhr.responseText);
                    let errorMessage = 'Error deleting record. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        errorMessage = xhr.responseText;
                    }
                    showAlert('danger', errorMessage);
                }
            });
        }
    });

    // Helper function to show alerts
    function showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('body').append(alertHtml);
        setTimeout(() => {
            $('.alert').alert('close');
        }, 3000);
    }
});
</script>

</body>
</html>
