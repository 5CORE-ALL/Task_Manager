<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Increment Records</title>
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
            background: linear-gradient(to right, #6f42c1, #0d6efd);
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
        .proposal-increase { background-color: #d4edda; color: #155724; }
        .proposal-no-increase { background-color: #f8d7da; color: #721c24; }
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
                    <h2 class="mb-0"><i class="bi bi-graph-up-arrow me-2"></i>Increment Records</h2>
                    <p class="mb-0 mt-2">Track and manage all salary increment proposals</p>
                </div>
                <div>
                    <a href="{{ route('salary.increment') }}" class="btn-back">
                        <i class="bi bi-plus-circle"></i>
                        Add New Proposal
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

            @if(isset($proposals) && count($proposals) > 0)
                <div class="table-responsive">
                    <table id="proposalTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Employee</th>
                                <th>Department</th>
                                <th>Review Month</th>
                                <th>Proposal Type</th>
                                <th>Proposed Amount</th>
                                <th>Status</th>
                                <th>Approved By</th>
                                <th>Comments</th>
                                <th>Submitted Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proposals as $proposal)
                                <tr>
                                    <td><span class="badge bg-secondary">#{{ $proposal->id }}</span></td>
                                    <td>
                                        <strong>{{ $proposal->employee_name ?? 'N/A' }}</strong>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $proposal->department ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $proposal->review_month ? \Carbon\Carbon::parse($proposal->review_month)->format('M Y') : 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($proposal->proposal_type === 'increase')
                                            <span class="badge proposal-increase">
                                                <i class="bi bi-arrow-up"></i> Increase
                                            </span>
                                        @elseif($proposal->proposal_type === 'no_increase')
                                            <span class="badge proposal-no-increase">
                                                <i class="bi bi-dash"></i> No Increase
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($proposal->proposal_type ?? 'N/A') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($proposal->proposed_amount)
                                            <span class="fw-bold text-success">₹{{ number_format($proposal->proposed_amount, 2) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $status = $proposal->approval_status ?? 'pending';
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
                                        <span class="text-muted">{{ $proposal->approved_by ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted" title="{{ $proposal->comments }}">
                                            {{ Str::limit($proposal->comments ?? 'N/A', 30) }}
                                        </small>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $proposal->created_at ? $proposal->created_at->format('d M Y, H:i') : 'N/A' }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button class="btn btn-outline-primary btn-edit" 
                                                    data-id="{{ $proposal->id }}" 
                                                    data-employee="{{ $proposal->employee_name }}"
                                                    data-department="{{ $proposal->department }}"
                                                    data-month="{{ $proposal->review_month }}"
                                                    data-type="{{ $proposal->proposal_type }}"
                                                    data-amount="{{ $proposal->proposed_amount }}"
                                                    data-comments="{{ $proposal->comments }}"
                                                    title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            
                                            @if($proposal->approval_status === 'pending' || $proposal->approval_status === null)
                                                <button class="btn btn-outline-success btn-approve" 
                                                        data-id="{{ $proposal->id }}" 
                                                        title="Approve">
                                                    <i class="bi bi-check"></i>
                                                </button>
                                                <button class="btn btn-outline-danger btn-reject" 
                                                        data-id="{{ $proposal->id }}" 
                                                        title="Reject">
                                                    <i class="bi bi-x"></i>
                                                </button>
                                            @endif
                                            
                                            <button class="btn btn-outline-danger btn-delete" 
                                                    data-id="{{ $proposal->id }}" 
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
                    <h4 class="text-muted mt-3">No Increment Records Found</h4>
                    <p class="text-muted">No salary increment proposals have been submitted yet.</p>
                    <a href="{{ route('salary.increment') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Add First Increment Proposal
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
                    <i class="bi bi-pencil-square me-2"></i>Edit Increment Proposal
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
                            <label class="form-label">Review Month</label>
                            <input type="month" class="form-control" id="edit_month" name="review_month">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Proposal Type</label>
                            <select class="form-select" id="edit_type" name="proposal_type">
                                <option value="increase">Salary Increase</option>
                                <option value="no_increase">No Increase</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Proposed Amount (₹)</label>
                            <input type="number" class="form-control" id="edit_amount" name="proposed_amount">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Comments</label>
                            <textarea class="form-control" id="edit_comments" name="comments" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>Update Proposal
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
                        <label class="form-label">Approval/Rejection Comments</label>
                        <textarea class="form-control" id="status_comments" name="approval_comments" rows="3" required></textarea>
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
    if ($('#proposalTable').length) {
        $('#proposalTable').DataTable({
            responsive: true,
            pageLength: 25,
            order: [[0, 'desc']],
            columnDefs: [
                { targets: [8, 9, 10], orderable: false }
            ],
            language: {
                search: "Search records:",
                lengthMenu: "Show _MENU_ records per page",
                info: "Showing _START_ to _END_ of _TOTAL_ increment records",
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
        $('#edit_type').val(data.type);
        $('#edit_amount').val(data.amount || '');
        $('#edit_comments').val(data.comments);
        $('#editModal').modal('show');
    });

    // Edit form submission
    $('#editForm').on('submit', function(e) {
        e.preventDefault();
        const formData = {
            id: $('#edit_id').val(),
            review_month: $('#edit_month').val(),
            proposal_type: $('#edit_type').val(),
            proposed_amount: $('#edit_amount').val() || null,
            comments: $('#edit_comments').val()
        };

        $.ajax({
            url: `{{ url('/salary/increment') }}/${formData.id}/update`,
            method: 'PUT',
            data: formData,
            success: function(response) {
                $('#editModal').modal('hide');
                showAlert('success', 'Proposal updated successfully!');
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                console.log('Error details:', xhr);
                let errorMessage = 'Error updating proposal. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showAlert('danger', errorMessage);
            }
        });
    });

    // Approve button click
    $(document).on('click', '.btn-approve', function() {
        const id = $(this).data('id');
        $('#status_id').val(id);
        $('#status_action').val('approved');
        $('#statusModalLabel').text('Approve Increment Proposal');
        $('#statusModalHeader').removeClass('bg-danger').addClass('bg-success text-white');
        $('#statusSubmitBtn').removeClass('btn-danger').addClass('btn-success').text('Approve');
        $('#statusModal').modal('show');
    });

    // Reject button click
    $(document).on('click', '.btn-reject', function() {
        const id = $(this).data('id');
        $('#status_id').val(id);
        $('#status_action').val('rejected');
        $('#statusModalLabel').text('Reject Increment Proposal');
        $('#statusModalHeader').removeClass('bg-success').addClass('bg-danger text-white');
        $('#statusSubmitBtn').removeClass('btn-success').addClass('btn-danger').text('Reject');
        $('#statusModal').modal('show');
    });

    // Status form submission
    $('#statusForm').on('submit', function(e) {
        e.preventDefault();
        const formData = {
            id: $('#status_id').val(),
            approval_status: $('#status_action').val(),
            approval_comments: $('#status_comments').val()
        };

        $.ajax({
            url: `{{ url('/salary/increment') }}/${formData.id}/status`,
            method: 'PUT',
            data: formData,
            success: function(response) {
                $('#statusModal').modal('hide');
                showAlert('success', `Proposal ${formData.approval_status} successfully!`);
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                console.log('Error details:', xhr);
                let errorMessage = 'Error updating status. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showAlert('danger', errorMessage);
            }
        });
    });

    // Delete button click
    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        if (confirm('Are you sure you want to delete this increment proposal? This action cannot be undone.')) {
            $.ajax({
                url: `{{ url('/salary/increment') }}/${id}/delete`,
                method: 'DELETE',
                success: function(response) {
                    showAlert('success', 'Proposal deleted successfully!');
                    setTimeout(() => location.reload(), 1500);
                },
                error: function(xhr) {
                    console.log('Error details:', xhr);
                    let errorMessage = 'Error deleting proposal. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
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
