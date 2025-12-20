@extends('layouts.main')

@section('page-title')
    {{ __('Performance Management') }}
@endsection

@section('page-breadcrumb')
    {{ __('Performance Management') }}
@endsection

@push('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .score-badge {
            font-size: 1.1em;
            padding: 0.5rem 1rem;
        }
        .score-excellent { background-color: #28a745; color: white; }
        .score-good { background-color: #17a2b8; color: white; }
        .score-average { background-color: #ffc107; color: #000; }
        .score-poor { background-color: #dc3545; color: white; }
    </style>
@endpush

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>{{ __('Performance Management') }}</h5>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <label for="period_type" class="form-label">{{ __('Period Type') }}</label>
                        <select class="form-control" id="period_type" name="period_type">
                            <option value="monthly" {{ $periodType == 'monthly' ? 'selected' : '' }}>{{ __('Monthly') }}</option>
                            <option value="weekly" {{ $periodType == 'weekly' ? 'selected' : '' }}>{{ __('Weekly') }}</option>
                            <option value="quarterly" {{ $periodType == 'quarterly' ? 'selected' : '' }}>{{ __('Quarterly') }}</option>
                            <option value="yearly" {{ $periodType == 'yearly' ? 'selected' : '' }}>{{ __('Yearly') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="period" class="form-label">{{ __('Period') }}</label>
                        <select class="form-control" id="period" name="period">
                            <option value="">{{ __('All Periods') }}</option>
                            @php
                                // Generate last 12 months
                                $currentYear = date('Y');
                                $currentMonth = date('m');
                                for ($i = 0; $i < 12; $i++) {
                                    $date = Carbon\Carbon::create($currentYear, $currentMonth, 1)->subMonths($i);
                                    $periodValue = $date->format('Y-m');
                                    $periodLabel = $date->format('F Y');
                                    $selected = ($period == $periodValue) ? 'selected' : '';
                                    echo "<option value=\"{$periodValue}\" {$selected}>{$periodLabel}</option>";
                                }
                            @endphp
                        </select>
                    </div>
                    @if($isPrivileged)
                    <div class="col-md-3">
                        <label for="employee_filter" class="form-label">{{ __('Employee') }}</label>
                        <select class="form-control" id="employee_filter" name="employee_filter">
                            <option value="">{{ __('All Employees') }}</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="button" class="btn btn-primary" id="generateBtn">
                            <i class="ti ti-plus"></i> {{ __('Generate Performance Data') }}
                        </button>
                    </div>
                </div>

                <!-- Performance Records Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="performanceTable">
                        <thead>
                            <tr>
                                <th>{{ __('Employee') }}</th>
                                <th>{{ __('Period') }}</th>
                                <th>{{ __('ETC Hours') }}</th>
                                <th>{{ __('ATC Hours') }}</th>
                                <th>{{ __('Working Hours') }}</th>
                                <th>{{ __('Productive Hours') }}</th>
                                <th>{{ __('Tasks Completed') }}</th>
                                <th>{{ __('Completion Rate') }}</th>
                                <th>{{ __('Overall Score') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($performanceRecords as $record)
                                <tr>
                                    <td>{{ $record->employee->name ?? 'N/A' }}</td>
                                    <td>{{ $record->period }}</td>
                                    <td>{{ number_format($record->etc_hours, 2) }}</td>
                                    <td>{{ number_format($record->atc_hours, 2) }}</td>
                                    <td>{{ number_format($record->total_working_hours, 2) }}</td>
                                    <td>{{ number_format($record->productive_hours, 2) }}</td>
                                    <td>{{ $record->tasks_completed }}</td>
                                    <td>{{ number_format($record->task_completion_rate, 2) }}%</td>
                                    <td>
                                        <span class="badge score-badge {{ $record->overall_score >= 80 ? 'score-excellent' : ($record->overall_score >= 60 ? 'score-good' : ($record->overall_score >= 40 ? 'score-average' : 'score-poor')) }}">
                                            {{ number_format($record->overall_score, 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('performance.report', $record->id) }}" class="btn btn-sm btn-primary">
                                            <i class="ti ti-eye"></i> {{ __('View Report') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">{{ __('No performance records found. Generate performance data to get started.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Generate Performance Modal -->
<div class="modal fade" id="generateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Generate Performance Data') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="generateForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_employee_id" class="form-label">{{ __('Employee') }} <span class="text-danger">*</span></label>
                                <select class="form-control" id="modal_employee_id" name="employee_id" required>
                                    <option value="">{{ __('Select Employee') }}</option>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_period_type" class="form-label">{{ __('Period Type') }} <span class="text-danger">*</span></label>
                                <select class="form-control" id="modal_period_type" name="period_type" required>
                                    <option value="monthly">{{ __('Monthly') }}</option>
                                    <option value="weekly">{{ __('Weekly') }}</option>
                                    <option value="quarterly">{{ __('Quarterly') }}</option>
                                    <option value="yearly">{{ __('Yearly') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_period" class="form-label">{{ __('Period') }} <span class="text-danger">*</span></label>
                                <select class="form-control" id="modal_period" name="period" required>
                                    <option value="">{{ __('Select Period') }}</option>
                                    @php
                                        // Generate last 24 months for selection
                                        $currentYear = date('Y');
                                        $currentMonth = date('m');
                                        for ($i = 0; $i < 24; $i++) {
                                            $date = Carbon\Carbon::create($currentYear, $currentMonth, 1)->subMonths($i);
                                            $periodValue = $date->format('Y-m');
                                            $periodLabel = $date->format('F Y');
                                            echo "<option value=\"{$periodValue}\">{$periodLabel}</option>";
                                        }
                                    @endphp
                                </select>
                                <small class="form-text text-muted">{{ __('Select the period for performance calculation') }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_start_date" class="form-label">{{ __('Start Date') }} <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="modal_start_date" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_end_date" class="form-label">{{ __('End Date') }} <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="modal_end_date" name="end_date" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Generate') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Global error handler to catch and suppress addEventListener errors from other scripts
    (function() {
        'use strict';
        if (typeof window === 'undefined' || !window.addEventListener) return;
        
        try {
            window.addEventListener('error', function(e) {
                // Catch addEventListener errors from other scripts and prevent them from breaking the page
                if (e.message && (e.message.includes('addEventListener') || e.message.includes('Cannot read properties of null'))) {
                    console.warn('Suppressed error from another script:', e.message, e.filename, e.lineno);
                    e.preventDefault();
                    e.stopPropagation();
                    return true; // Prevent default error handling
                }
            }, true);
        } catch (err) {
            // Silently fail if we can't set up the error handler
        }
    })();

    // Standard jQuery ready - ensures jQuery and DOM are ready
    // Wrap in try-catch to prevent errors from breaking the page
    (function initPerformanceScript() {
        'use strict';
        
        // Wait for jQuery and document to be available
        if (typeof jQuery === 'undefined' || typeof document === 'undefined' || !document || !document.body) {
            console.warn('jQuery or document not loaded yet, waiting...');
            setTimeout(initPerformanceScript, 100);
            return;
        }
        
        try {
            jQuery(function($) {
                console.log('Performance Management script initialized');
                
                // Helper function for notifications
                function showNotification(title, message, type) {
                    if (typeof show_toastr !== 'undefined') {
                        show_toastr(title, message, type);
                    } else if (typeof toastrs !== 'undefined') {
                        toastrs(title, message, type);
                    } else if (typeof showToast !== 'undefined') {
                        showToast(type, title, message);
                    } else {
                        // Fallback to alert
                        alert(title + ': ' + message);
                    }
                }

                // Generate button click
                $('#generateBtn').on('click', function(e) {
                    e.preventDefault();
                    console.log('Generate button clicked');
                    
                    // Try Bootstrap 5 first, then fallback to Bootstrap 4
                    const modalElement = document.getElementById('generateModal');
                    if (modalElement) {
                        // Bootstrap 5
                        if (typeof bootstrap !== 'undefined') {
                            const modal = new bootstrap.Modal(modalElement);
                            modal.show();
                        } else if ($.fn.modal) {
                            // Bootstrap 4 fallback
                            $('#generateModal').modal('show');
                        } else {
                            console.error('Bootstrap modal not available');
                            alert('Modal functionality not available. Please refresh the page.');
                        }
                    } else {
                        console.error('Modal element not found');
                        alert('Modal not found. Please refresh the page.');
                    }
                });

                // Generate form submit
                $('#generateForm').on('submit', function(e) {
                    e.preventDefault();
                    
                    // Disable submit button to prevent double submission
                    const submitBtn = $(this).find('button[type="submit"]');
                    submitBtn.prop('disabled', true).text('Generating...');
                    
                    console.log('Submitting performance generation form...');
                    console.log('Form data:', $(this).serialize());
                    
                    // Get CSRF token
                    const csrfToken = $('meta[name="csrf-token"]').attr('content');
                    if (!csrfToken) {
                        console.error('CSRF token not found');
                        alert('Security token missing. Please refresh the page.');
                        submitBtn.prop('disabled', false).text('{{ __("Generate") }}');
                        return;
                    }
                    
                    $.ajax({
                        url: '{{ route("performance.generate") }}',
                        method: 'POST',
                        data: $(this).serialize(),
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        success: function(response) {
                            console.log('Success response:', response);
                            if (response.success) {
                                showNotification('Success', response.message, 'success');
                                // Hide modal - Bootstrap 5 or 4
                                const modalElement = document.getElementById('generateModal');
                                if (modalElement && typeof bootstrap !== 'undefined') {
                                    const modal = bootstrap.Modal.getInstance(modalElement);
                                    if (modal) modal.hide();
                                } else if ($.fn.modal) {
                                    $('#generateModal').modal('hide');
                                }
                                setTimeout(() => {
                                    location.reload();
                                }, 1500);
                            } else {
                                showNotification('Error', response.message || 'Failed to generate performance data', 'error');
                                submitBtn.prop('disabled', false).text('{{ __("Generate") }}');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', {
                                status: status,
                                error: error,
                                response: xhr.responseText,
                                statusCode: xhr.status
                            });
                            
                            let errorMsg = 'An error occurred while generating performance data';
                            
                            if (xhr.responseJSON) {
                                if (xhr.responseJSON.message) {
                                    errorMsg = xhr.responseJSON.message;
                                } else if (xhr.responseJSON.errors) {
                                    const errors = Object.values(xhr.responseJSON.errors).flat();
                                    errorMsg = errors.join(', ');
                                }
                            } else if (xhr.responseText) {
                                console.error('Response text:', xhr.responseText);
                                // Try to parse as HTML error
                                if (xhr.responseText.includes('419')) {
                                    errorMsg = 'Session expired. Please refresh the page and try again.';
                                }
                            }
                            
                            showNotification('Error', errorMsg, 'error');
                            submitBtn.prop('disabled', false).text('{{ __("Generate") }}');
                        }
                    });
                });

                // Filter change
                $('#period_type, #period, #employee_filter').on('change', function() {
                    let periodType = $('#period_type').val();
                    let period = $('#period').val();
                    let employee = $('#employee_filter').val();
                    let url = '{{ route("performance.index") }}?period_type=' + periodType + '&period=' + period;
                    if (employee) {
                        url += '&employee_id=' + employee;
                    }
                    window.location.href = url;
                });
            }); // End of jQuery ready function
        } catch (error) {
            console.error('Performance Management script error:', error);
            // Don't break the page if there's an error
        }
    })(); // End of initPerformanceScript IIFE
</script>
@endpush

