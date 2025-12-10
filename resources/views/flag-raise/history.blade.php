@extends('layouts.main')

@section('page-title')
    {{ __('Flag Raise Management') }}
@endsection

@section('page-breadcrumb')
    {{ __('Flag Raise') }}
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-md-4">
        <label for="given_by_search" class="form-label">Given By</label>
        <div class="position-relative">
            <input type="text" class="form-control" id="given_by_search" placeholder="Search employee...">
            <input type="hidden" id="given_by_id">
            <div id="givenByDropdown" class="dropdown-menu position-absolute w-100" style="display: none; max-height: 200px; overflow-y: auto;"></div>
        </div>
    </div>
    <div class="col-md-4">
        <label for="team_member_search" class="form-label">Team Member</label>
        <div class="position-relative">
            <input type="text" class="form-control" id="team_member_search" placeholder="Search employee...">
            <input type="hidden" id="team_member_id">
            <div id="teamMemberDropdown" class="dropdown-menu position-absolute w-100" style="display: none; max-height: 200px; overflow-y: auto;"></div>
        </div>
    </div>
    <div class="col-md-4">
        <label for="flag_type_search" class="form-label">Flag Type</label>
        <select class="form-control" id="flag_type_search">
            <option value="">All Types</option>
            <option value="red">Red</option>
            <option value="green">Green</option>
            <option value="other">Other</option>
        </select>
    </div>
</div>
<table style="width:100%; border-radius:8px; overflow:hidden; box-shadow:0 2px 8px #eee; border-collapse:collapse;">
        <thead>
            <tr style="background: linear-gradient(90deg,#007bff,#00c6ff); color:#fff;">
                <th style="padding:12px; text-align:center;">#</th>
                <th style="padding:12px; text-align:center;">Flag Type</th>
                <th style="padding:12px;">Description</th>
                <th style="padding:12px;">Given By</th>
                <th style="padding:12px;">Team Member</th>
                <th style="padding:12px; text-align:center;">Date</th>
                <th style="padding:12px; text-align:center;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($flags as $flag)
                <tr style="background:{{ $loop->iteration % 2 == 0 ? '#f8f9fa' : '#e3f2fd' }};">
                    <td style="padding:10px; text-align:center;">{{ $loop->iteration }}</td>
                    <td style="padding:10px; text-align:center;">
                        @if($flag->flag_type === 'red')
                            <span style="background:#dc3545; color:#fff; padding:4px 12px; border-radius:20px; font-weight:bold;">Red</span>
                        @elseif($flag->flag_type === 'green')
                            <span style="background:#28a745; color:#fff; padding:4px 12px; border-radius:20px; font-weight:bold;">Green</span>
                        @else
                            <span style="background:#6c757d; color:#fff; padding:4px 12px; border-radius:20px; font-weight:bold;">{{ ucfirst($flag->flag_type) }}</span>
                        @endif
                    </td>
                    <td style="padding:10px;">{{ $flag->description }}</td>
                    <td style="padding:10px;">{{ $flag->givenBy ? $flag->givenBy->name : 'N/A' }}</td>
                    <td style="padding:10px;">{{ $flag->teamMember ? $flag->teamMember->name : 'N/A' }}</td>
                    <td style="padding:10px; text-align:center;">{{ $flag->created_at->format('d M Y, h:i A') }}</td>
                    <td style="padding:10px; text-align:center;">
                        @if(in_array(Auth::user()->email, ['president@5core.com', 'tech-support@5core.com']))
                            <a href="#" class="delete-flag" data-id="{{ $flag->id }}" title="Delete" style="color:#dc3545; font-size:18px;">
                                <i class="fa fa-trash"></i>
                            </a>
                        @else
                            <span style="color:#ccc; font-size:18px; cursor:not-allowed; opacity:0.5;">
                                <i class="fa fa-trash"></i>
                            </span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:20px; color:#dc3545; font-weight:bold;">No flags found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection

@push('scripts')
<script>
let employees = [];

// CSRF token for AJAX and set base-url meta if not present
if ($('meta[name="base-url"]').length === 0) {
    var base = '{{ url("") }}';
    $('head').append('<meta name="base-url" content="' + base + '">');
}
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$(document).ready(function() {
    // Load employees for dropdowns
    loadEmployees();

    // Delete flag
    $(document).on('click', '.delete-flag', function(e) {
        e.preventDefault();
        if (!confirm('Are you sure you want to delete this flag?')) return;
        var flagId = $(this).data('id');
        var row = $(this).closest('tr');
        // Use base URL for AJAX to work in subfolders
        var baseUrl = $('meta[name="base-url"]').attr('content') || '';
        $.ajax({
            url: baseUrl + '/flag-raise/' + flagId,
            type: 'DELETE',
            success: function(res) {
                row.remove();
            },
            error: function(xhr) {
                alert('Failed to delete flag.');
            }
        });
    });

    // Given By search
    $('#given_by_search').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        const dropdown = $('#givenByDropdown');
        if (searchTerm.length === 0) {
            dropdown.hide();
            $('#given_by_id').val('');
            filterTable();
            return;
        }
        const filtered = employees.filter(e => e.name.toLowerCase().includes(searchTerm));
        if (filtered.length > 0) {
            let html = '';
            filtered.forEach(e => {
                html += `<a class="dropdown-item givenby-item" href="#" data-id="${e.id}" data-name="${e.name}">${e.name}</a>`;
            });
            dropdown.html(html).show();
        } else {
            dropdown.html('<span class="dropdown-item-text text-muted">No employees found</span>').show();
        }
        filterTable();
    });
    $(document).on('click', '.givenby-item', function(e) {
        e.preventDefault();
        $('#given_by_search').val($(this).data('name'));
        $('#given_by_id').val($(this).data('id'));
        $('#givenByDropdown').hide();
        filterTable();
    });

    // Team Member search
    $('#team_member_search').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        const dropdown = $('#teamMemberDropdown');
        if (searchTerm.length === 0) {
            dropdown.hide();
            $('#team_member_id').val('');
            filterTable();
            return;
        }
        const filtered = employees.filter(e => e.name.toLowerCase().includes(searchTerm));
        if (filtered.length > 0) {
            let html = '';
            filtered.forEach(e => {
                html += `<a class="dropdown-item teammember-item" href="#" data-id="${e.id}" data-name="${e.name}">${e.name}</a>`;
            });
            dropdown.html(html).show();
        } else {
            dropdown.html('<span class="dropdown-item-text text-muted">No employees found</span>').show();
        }
        filterTable();
    });
    $(document).on('click', '.teammember-item', function(e) {
        e.preventDefault();
        $('#team_member_search').val($(this).data('name'));
        $('#team_member_id').val($(this).data('id'));
        $('#teamMemberDropdown').hide();
        filterTable();
    });

    // Flag Type search
    $('#flag_type_search').on('change', function() {
        filterTable();
    });

    // Hide dropdowns when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#given_by_search, #givenByDropdown').length) {
            $('#givenByDropdown').hide();
        }
        if (!$(e.target).closest('#team_member_search, #teamMemberDropdown').length) {
            $('#teamMemberDropdown').hide();
        }
    });
});

function loadEmployees() {
    $.ajax({
        url: '{{ route("reviews.employees") }}',
        method: 'GET',
        success: function(response) {
            employees = response.employees || response;
        },
        error: function(xhr, status, error) {
            console.error('Error loading employees:', error);
        }
    });
}

function filterTable() {
    const givenBy = $('#given_by_search').val().toLowerCase();
    const teamMember = $('#team_member_search').val().toLowerCase();
    const flagType = $('#flag_type_search').val();

    $('tbody tr').each(function() {
        const row = $(this);
        const givenByText = row.find('td:nth-child(4)').text().toLowerCase();
        const teamMemberText = row.find('td:nth-child(5)').text().toLowerCase();
        let flagTypeText = row.find('td:nth-child(2)').text().toLowerCase();
        // For colored span, get text inside span
        flagTypeText = row.find('td:nth-child(2) span').text().toLowerCase() || flagTypeText;

        let showRow = true;
        if (givenBy && !givenByText.includes(givenBy)) {
            showRow = false;
        }
        if (teamMember && !teamMemberText.includes(teamMember)) {
            showRow = false;
        }
        if (flagType && flagType !== 'other' && flagTypeText !== flagType) {
            showRow = false;
        }
        if (flagType === 'other' && (flagTypeText === 'red' || flagTypeText === 'green')) {
            showRow = false;
        }
        if (showRow) {
            row.show();
        } else {
            row.hide();
        }
    });
}
</script>
@endpush