@extends('layouts.main')
@section('page-title')
    {{ __('Report Form List') }}
@endsection
@section('title')
    {{ __('Report Form List') }}
@endsection
@section('page-breadcrumb')
    {{ __('Report') }},{{ __('Form Details') }},{{ __('Report List') }}
@endsection
@push('css')
    @include('layouts.includes.datatable-css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        /* ...existing CSS from your report.blade.php... */
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
        <h1 class="h2">Forms & reports</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFormModal">
                    <i class="bi bi-plus-circle"></i> Add Form
                </button>
            </div>
            <div class="btn-group me-2">
                <button type="button" class="btn btn-outline-secondary">
                    <i class="bi bi-download"></i> Export
                </button>
                <button type="button" class="btn btn-outline-secondary">
                    <i class="bi bi-printer"></i> Print
                </button>
            </div>
        </div>
    </div>
    <!-- First Table -->
    <div class="table-container">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-table"></i> Forms
                </h5>
                <!-- Search Bars -->
                <div class="row mt-3 mb-2">
                    
                    <div class="col-md-4">
                        <label for="searchFormName" class="form-label"><i class="bi bi-file-text"></i> Form Name</label>
                        <input type="text" id="searchFormName" class="form-control" placeholder="Search by Form Name">
                    </div>
                    <div class="col-md-4">
                        <label for="searchAssignorName" class="form-label"><i class="bi bi-person"></i> Assignor Name</label>
                        <input type="text" id="searchAssignorName" class="form-control" placeholder="Search by Assignor Name">
                    </div>
                    <div class="col-md-4">
                        <label for="searchAssigneeName" class="form-label"><i class="bi bi-person-check"></i> Assignee Name</label>
                        <input type="text" id="searchAssigneeName" class="form-control" placeholder="Search by Assignee Name">
                    </div>
                    

                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="formsTable">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">Form Name</th>
                                <th scope="col">Category</th>
                                <th scope="col">Status</th>
                                <th scope="col">Frequency</th>
                                <th scope="col">Assignor Name</th>
                                <th scope="col">Assignee Name</th>
                                <th scope="col">Form View</th>
                                <th scope="col">Report View</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($forms as $form)
                            <tr data-id="{{ $form->id }}">
                                <td class="form_name">{{ $form->form_name }}</td>
                                <td class="category">{{ $form->category }}</td>
                                <td class="status"><span class="badge {{ $form->status == 'Active' ? 'bg-success' : ($form->status == 'Inactive' ? 'bg-danger' : 'bg-warning') }}">{{ $form->status }}</span></td>
                                <td class="frequency">{{ $form->frequency ?? '-' }}</td>
                                <td class="owner_name">{{ $form->owner_name }}</td>
                                <td class="assignee_name">{{ $form->assignee ?? '-' }}</td>
                                <td class="form_link">
                                    @if($form->enable_form_view)
                                        @if($form->form_link)
                                            <a href="{{ $form->form_link }}" class="btn btn-link" target="_blank">View</a>
                                        @else
                                            <a href="#" class="btn btn-link">View</a>
                                        @endif
                                    @else
                                        <span class="text-muted">Disabled</span>
                                    @endif
                                </td>
                                <td class="report_link">
                                    @if($form->enable_report_view)
                                        @if($form->report_link)
                                            <a href="{{ $form->report_link }}" class="btn btn-link" target="_blank">View</a>
                                        @else
                                            <a href="#" class="btn btn-link">View</a>
                                        @endif
                                    @else
                                        <span class="text-muted">Disabled</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-warning edit-form-btn" data-id="{{ $form->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger delete-form-btn" data-id="{{ $form->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Form Modal -->
<div class="modal fade" id="addFormModal" tabindex="-1" aria-labelledby="addFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addFormModalLabel">
                    <i class="bi bi-plus-circle"></i> <span id="modalTitle">Add New Form</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addFormForm">
                    <input type="hidden" id="formId" name="id" />
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="formName" class="form-label">
                                    <i class="bi bi-file-text"></i> Form Name
                                </label>
                                <input type="text" class="form-control" id="formName" name="form_name" placeholder="Enter form name" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="formCategory" class="form-label">
                                    <i class="bi bi-tag"></i> Category
                                </label>
                                <input type="text" class="form-control" id="formCategory" name="category" placeholder="Enter category" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="formFrequency" class="form-label">
                                    <i class="bi bi-calendar-event"></i> Frequency
                                </label>
                                <select class="form-select" id="formFrequency" name="frequency">
                                    <option value="">Select Frequency</option>
                                    <option value="Daily">Daily</option>
                                    <option value="Weekly">Weekly</option>
                                    <option value="Monthly">Monthly</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="formStatus" class="form-label">
                                    <i class="bi bi-activity"></i> Status
                                </label>
                                <select class="form-select" id="formStatus" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                    <option value="Draft">Draft</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ownerName" class="form-label">
                                    <i class="bi bi-person"></i> Assignor Name
                                </label>
                                <input type="text" class="form-control" id="ownerName" name="owner_name" placeholder="Enter owner name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="assignee" class="form-label">
                                    <i class="bi bi-person-check"></i> Assignee Name
                                </label>
                                <input type="text" class="form-control" id="assignee" name="assignee" placeholder="Enter assignee name">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="formLink" class="form-label">
                                    <i class="bi bi-link-45deg"></i> Form Link
                                </label>
                                <input type="url" class="form-control" id="formLink" name="form_link" placeholder="Enter form URL">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="formReportLink" class="form-label">
                                    <i class="bi bi-file-earmark-bar-graph"></i> Form Report Link
                                </label>
                                <input type="url" class="form-control" id="formReportLink" name="report_link" placeholder="Enter report URL">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-gear"></i> Form Settings
                        </label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="enableFormView" name="enable_form_view" checked>
                                    <label class="form-check-label" for="enableFormView">
                                        Enable Form View
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="enableReportView" name="enable_report_view" checked>
                                    <label class="form-check-label" for="enableReportView">
                                        Enable Report View
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle"></i>
                        <strong>Note:</strong> The form will be added to the forms table once you click <span id="modalAction">"Add Form"</span>.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancel
                </button>
                <button type="button" class="btn btn-primary" id="saveFormBtn">
                    <i class="bi bi-check-circle"></i> <span id="modalBtnText">Add Form</span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Add/Edit logic
let editMode = false;
let editingId = null;

// Search/Filter logic
function filterTable() {
    const formName = document.getElementById('searchFormName').value.toLowerCase();
    const assignorName = document.getElementById('searchAssignorName').value.toLowerCase();
    const assigneeName = document.getElementById('searchAssigneeName').value.toLowerCase();
    const rows = document.querySelectorAll('#formsTable tbody tr');
    rows.forEach(row => {
        const formNameText = row.querySelector('.form_name').textContent.toLowerCase();
        const assignorNameText = row.querySelector('.owner_name').textContent.toLowerCase();
        const assigneeNameText = row.querySelector('.assignee_name').textContent.toLowerCase();
        let show = true;
        if (formName && !formNameText.includes(formName)) show = false;
        if (assignorName && !assignorNameText.includes(assignorName)) show = false;
        if (assigneeName && !assigneeNameText.includes(assigneeName)) show = false;
        row.style.display = show ? '' : 'none';
    });
}
document.getElementById('searchFormName').addEventListener('input', filterTable);
document.getElementById('searchAssignorName').addEventListener('input', filterTable);
document.getElementById('searchAssigneeName').addEventListener('input', filterTable);

// Open modal for edit
$(document).on('click', '.edit-form-btn', function() {
    editMode = true;
    editingId = $(this).data('id');
    const row = $(this).closest('tr');
    $('#formId').val(editingId);
    $('#formName').val(row.find('.form_name').text());
    $('#formCategory').val(row.find('.category').text());
    $('#formFrequency').val(row.find('.frequency').text());
    $('#formStatus').val(row.find('.status span').text());
    $('#ownerName').val(row.find('.owner_name').text());
    // For links and checkboxes, you may need to fetch from DB or store in data-attributes for full accuracy
    $('#formLink').val(row.find('.form_link a').attr('href') || '');
    $('#formReportLink').val(row.find('.report_link a').attr('href') || '');
    $('#assignee').val(row.find('.assignee_name').text());
    $('#enableFormView').prop('checked', row.find('.form_link a').length > 0);
    $('#enableReportView').prop('checked', row.find('.report_link a').length > 0);
    $('#modalTitle').text('Edit Form');
    $('#modalBtnText').text('Update Form');
    $('#modalAction').text('"Update Form"');
    $('#addFormModal').modal('show');
});

// Reset modal on close
$('#addFormModal').on('hidden.bs.modal', function () {
    editMode = false;
    editingId = null;
    $('#addFormForm')[0].reset();
    $('#formId').val('');
    $('#modalTitle').text('Add New Form');
    $('#modalBtnText').text('Add Form');
    $('#modalAction').text('"Add Form"');
});

// Save (add or update)
document.getElementById('saveFormBtn').addEventListener('click', function() {
    const formData = new FormData(document.getElementById('addFormForm'));
    formData.set('enable_form_view', document.getElementById('enableFormView').checked ? 1 : 0);
    formData.set('enable_report_view', document.getElementById('enableReportView').checked ? 1 : 0);
    let url = editMode ? "{{ url('report-view') }}/" + editingId : "{{ route('report.store') }}";
    let method = 'POST'; // Always use POST
    if(editMode) formData.append('_method', 'PUT');
    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            if(editMode) {
                // Update row in table
                const row = $(`#formsTable tr[data-id='${editingId}']`);
                row.find('.form_name').text(data.form.form_name);
                row.find('.category').text(data.form.category);
                row.find('.status span').text(data.form.status)
                    .removeClass('bg-success bg-danger bg-warning')
                    .addClass(data.form.status === 'Active' ? 'bg-success' : (data.form.status === 'Inactive' ? 'bg-danger' : 'bg-warning'));
                row.find('.owner_name').text(data.form.owner_name);
                row.find('.frequency').text(data.form.frequency ?? '-');
                row.find('.form_link').html(data.form.enable_form_view ? (data.form.form_link ? `<a href="${data.form.form_link}" class="btn btn-link" target="_blank">View</a>` : '<a href="#" class="btn btn-link">View</a>') : '<span class="text-muted">Disabled</span>');
                row.find('.report_link').html(data.form.enable_report_view ? (data.form.report_link ? `<a href="${data.form.report_link}" class="btn btn-link" target="_blank">View</a>` : '<a href="#" class="btn btn-link">View</a>') : '<span class="text-muted">Disabled</span>');
            } else {
                // Add new row to table
                const form = data.form;
                const tableBody = document.querySelector('#formsTable tbody');
                let statusBadgeClass = 'bg-success';
                if (form.status === 'Inactive') statusBadgeClass = 'bg-danger';
                if (form.status === 'Draft') statusBadgeClass = 'bg-warning';
                let formViewButton = form.enable_form_view ? (form.form_link ? `<a href="${form.form_link}" class="btn btn-link" target="_blank">View</a>` : '<a href="#" class="btn btn-link">View</a>') : '<span class="text-muted">Disabled</span>';
                let reportViewButton = form.enable_report_view ? (form.report_link ? `<a href="${form.report_link}" class="btn btn-link" target="_blank">View</a>` : '<a href="#" class="btn btn-link">View</a>') : '<span class="text-muted">Disabled</span>';
                const newRow = document.createElement('tr');
                newRow.setAttribute('data-id', form.id);
                newRow.innerHTML = `
                    <td class="form_name">${form.form_name}</td>
                    <td class="category">${form.category}</td>
                    <td class="status"><span class="badge ${statusBadgeClass}">${form.status}</span></td>
                    <td class="frequency">${form.frequency ?? '-'}</td>
                    <td class="owner_name">${form.owner_name}</td>
                    <td class="assignee_name">${form.assignee ?? '-'} </td>
                    <td class="form_link">${formViewButton}</td>
                    <td class="report_link">${reportViewButton}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-warning edit-form-btn" data-id="${form.id}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-danger delete-form-btn" data-id="${form.id}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                `;
                tableBody.appendChild(newRow);
            }
            document.getElementById('addFormForm').reset();
            const modal = bootstrap.Modal.getInstance(document.getElementById('addFormModal'));
            modal.hide();
            // Show success message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show';
            alertDiv.innerHTML = `
                <i class="bi bi-check-circle"></i>
                <strong>Success!</strong> Form "${formData.get('form_name')}" has been ${editMode ? 'updated' : 'added'} successfully.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.main-content').insertBefore(alertDiv, document.querySelector('.table-container'));
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 3000);
        } else {
            alert('Failed to save form.');
        }
    });
});

// Delete logic
$(document).on('click', '.delete-form-btn', function() {
    const id = $(this).data('id');
    if(confirm('Are you sure you want to delete this form?')) {
        let formData = new FormData();
        formData.append('_method', 'DELETE');
        fetch("{{ url('report-view') }}/" + id, {
            method: 'POST', // Always use POST
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                $(`#formsTable tr[data-id='${id}']`).remove();
                // Show delete success message
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-warning alert-dismissible fade show';
                alertDiv.innerHTML = `
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Deleted!</strong> Form has been removed successfully.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.querySelector('.main-content').insertBefore(alertDiv, document.querySelector('.table-container'));
                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 3000);
            } else {
                alert('Failed to delete form.');
            }
        });
    }
});
</script>
@endpush
@endsection
