@extends('layouts.main')

@section('page-title')
    {{ __('Performance Report') }} - {{ $performance->employee->name ?? 'N/A' }}
@endsection

@section('page-breadcrumb')
    {{ __('Performance Management') }}, {{ __('Report') }}
@endsection

@push('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <style>
        .score-card {
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .score-excellent { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; }
        .score-good { background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; }
        .score-average { background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); color: #000; }
        .score-poor { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; }
        .metric-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            background: white;
        }
        .metric-label {
            font-size: 0.9em;
            color: #6c757d;
            margin-bottom: 0.5rem;
        }
        .metric-value {
            font-size: 1.5em;
            font-weight: bold;
            color: #212529;
        }
        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 2rem;
        }
    </style>
@endpush

@section('content')
<div class="row">
    <div class="col-sm-12">
        <!-- Header -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>{{ __('Performance Report') }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>{{ __('Employee') }}:</strong> {{ $performance->employee->name ?? 'N/A' }}</p>
                        <p><strong>{{ __('Period') }}:</strong> {{ $performance->period }} ({{ ucfirst($performance->period_type) }})</p>
                        <p><strong>{{ __('Date Range') }}:</strong> {{ $performance->start_date->format('M d, Y') }} - {{ $performance->end_date->format('M d, Y') }}</p>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="score-card {{ $performance->overall_score >= 80 ? 'score-excellent' : ($performance->overall_score >= 60 ? 'score-good' : ($performance->overall_score >= 40 ? 'score-average' : 'score-poor')) }}">
                            <div class="metric-label">{{ __('Overall Performance Score') }}</div>
                            <div class="metric-value">{{ number_format($performance->overall_score, 2) }}/100</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Scores Chart -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>{{ __('Performance Scores') }}</h5>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="scoresChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Metrics Overview -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="metric-card">
                    <div class="metric-label">{{ __('ETC Hours') }}</div>
                    <div class="metric-value">{{ number_format($performance->etc_hours, 2) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="metric-card">
                    <div class="metric-label">{{ __('ATC Hours') }}</div>
                    <div class="metric-value">{{ number_format($performance->atc_hours, 2) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="metric-card">
                    <div class="metric-label">{{ __('Total Working Hours') }}</div>
                    <div class="metric-value">{{ number_format($performance->total_working_hours, 2) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="metric-card">
                    <div class="metric-label">{{ __('Productive Hours') }}</div>
                    <div class="metric-value">{{ number_format($performance->productive_hours, 2) }}</div>
                </div>
            </div>
        </div>

        <!-- Metrics Chart -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>{{ __('Hours Comparison') }}</h5>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="metricsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Task Metrics -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="metric-card">
                    <div class="metric-label">{{ __('Tasks Completed') }}</div>
                    <div class="metric-value">{{ $performance->tasks_completed }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="metric-card">
                    <div class="metric-label">{{ __('Total Tasks Assigned') }}</div>
                    <div class="metric-value">{{ $performance->total_tasks_assigned }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="metric-card">
                    <div class="metric-label">{{ __('Task Completion Rate') }}</div>
                    <div class="metric-value">{{ number_format($performance->task_completion_rate, 2) }}%</div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="metric-card">
                    <div class="metric-label">{{ __('Average Task Duration (Minutes)') }}</div>
                    <div class="metric-value">{{ number_format($performance->avg_task_duration_minutes, 2) }}</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="metric-card">
                    <div class="metric-label">{{ __('Average Task Duration (Days)') }}</div>
                    <div class="metric-value">{{ number_format($performance->avg_task_duration_days, 2) }}</div>
                </div>
            </div>
        </div>

        <!-- Individual Scores -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>{{ __('Individual Performance Scores') }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div class="metric-label">{{ __('Efficiency Score') }}</div>
                            <div class="metric-value">{{ number_format($performance->efficiency_score, 2) }}/100</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div class="metric-label">{{ __('Productivity Score') }}</div>
                            <div class="metric-value">{{ number_format($performance->productivity_score, 2) }}/100</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div class="metric-label">{{ __('Task Performance Score') }}</div>
                            <div class="metric-value">{{ number_format($performance->task_performance_score, 2) }}/100</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div class="metric-label">{{ __('Timeliness Score') }}</div>
                            <div class="metric-value">{{ number_format($performance->timeliness_score, 2) }}/100</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feedback Section -->
        @if($performance->feedbacks->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h5>{{ __('Management Feedback') }}</h5>
            </div>
            <div class="card-body">
                @foreach($performance->feedbacks as $feedback)
                    <div class="mb-4">
                        <h6>{{ __('Feedback by') }}: {{ $feedback->givenBy->name ?? 'N/A' }} ({{ $feedback->feedback_date->format('M d, Y') }})</h6>
                        
                        @if($feedback->communication_skill || $feedback->teamwork || $feedback->problem_solving)
                        <div class="chart-container">
                            <canvas id="feedbackChart{{ $feedback->id }}"></canvas>
                        </div>
                        @endif

                        @if($feedback->strengths)
                        <div class="mb-3">
                            <strong>{{ __('Strengths') }}:</strong>
                            <p>{{ $feedback->strengths }}</p>
                        </div>
                        @endif

                        @if($feedback->areas_for_improvement)
                        <div class="mb-3">
                            <strong>{{ __('Areas for Improvement') }}:</strong>
                            <p>{{ $feedback->areas_for_improvement }}</p>
                        </div>
                        @endif

                        @if($feedback->general_feedback)
                        <div class="mb-3">
                            <strong>{{ __('General Feedback') }}:</strong>
                            <p>{{ $feedback->general_feedback }}</p>
                        </div>
                        @endif

                        @if($feedback->goals)
                        <div class="mb-3">
                            <strong>{{ __('Goals') }}:</strong>
                            <p>{{ $feedback->goals }}</p>
                        </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($isPrivileged)
        <!-- Add Feedback Button -->
        <div class="card mb-4">
            <div class="card-body">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#feedbackModal">
                    <i class="ti ti-plus"></i> {{ __('Add Feedback') }}
                </button>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Feedback Modal -->
@if($isPrivileged)
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Add Performance Feedback') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="feedbackForm">
                <div class="modal-body">
                    <input type="hidden" name="employee_id" value="{{ $performance->employee_id }}">
                    <input type="hidden" name="performance_management_id" value="{{ $performance->id }}">
                    <input type="hidden" name="period" value="{{ $performance->period }}">
                    <input type="hidden" name="period_type" value="{{ $performance->period_type }}">
                    <input type="hidden" name="feedback_date" value="{{ date('Y-m-d') }}">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Communication Skill') }} (0-100)</label>
                            <input type="number" class="form-control" name="communication_skill" min="0" max="100">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Teamwork') }} (0-100)</label>
                            <input type="number" class="form-control" name="teamwork" min="0" max="100">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Problem Solving') }} (0-100)</label>
                            <input type="number" class="form-control" name="problem_solving" min="0" max="100">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Initiative') }} (0-100)</label>
                            <input type="number" class="form-control" name="initiative" min="0" max="100">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Quality of Work') }} (0-100)</label>
                            <input type="number" class="form-control" name="quality_of_work" min="0" max="100">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Reliability') }} (0-100)</label>
                            <input type="number" class="form-control" name="reliability" min="0" max="100">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Adaptability') }} (0-100)</label>
                            <input type="number" class="form-control" name="adaptability" min="0" max="100">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Leadership') }} (0-100)</label>
                            <input type="number" class="form-control" name="leadership" min="0" max="100">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Strengths') }}</label>
                        <textarea class="form-control" name="strengths" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Areas for Improvement') }}</label>
                        <textarea class="form-control" name="areas_for_improvement" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('General Feedback') }}</label>
                        <textarea class="form-control" name="general_feedback" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Goals') }}</label>
                        <textarea class="form-control" name="goals" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save Feedback') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    // Wrap in try-catch to prevent errors
    try {
        jQuery(function($) {
            // Load chart data
            $.ajax({
            url: '{{ route("performance.chart.data", $performance->id) }}',
            method: 'GET',
            success: function(data) {
                // Scores Chart
                const scoresCanvas = document.getElementById('scoresChart');
                if (!scoresCanvas) {
                    console.error('Scores chart canvas not found');
                    return;
                }
                const scoresCtx = scoresCanvas.getContext('2d');
                if (!scoresCtx) {
                    console.error('Could not get 2d context for scores chart');
                    return;
                }
                new Chart(scoresCtx, {
                    type: 'bar',
                    data: {
                        labels: data.scores.labels,
                        datasets: [{
                            label: 'Performance Score (out of 100)',
                            data: data.scores.data,
                            backgroundColor: data.scores.data.map(score => {
                                if (score >= 80) return 'rgba(40, 167, 69, 0.8)';  // Green - Excellent
                                if (score >= 60) return 'rgba(23, 162, 184, 0.8)'; // Blue - Good
                                if (score >= 40) return 'rgba(255, 193, 7, 0.8)';  // Yellow - Average
                                return 'rgba(220, 53, 69, 0.8)';                    // Red - Poor
                            }),
                            borderColor: data.scores.data.map(score => {
                                if (score >= 80) return 'rgba(40, 167, 69, 1)';
                                if (score >= 60) return 'rgba(23, 162, 184, 1)';
                                if (score >= 40) return 'rgba(255, 193, 7, 1)';
                                return 'rgba(220, 53, 69, 1)';
                            }),
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Performance Scores Breakdown',
                                font: { size: 16, weight: 'bold' }
                            },
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ' + context.parsed.y.toFixed(2) + '/100';
                                    }
                                }
                            },
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                title: {
                                    display: true,
                                    text: 'Score (0-100)',
                                    font: { size: 12, weight: 'bold' }
                                },
                                ticks: {
                                    stepSize: 10,
                                    callback: function(value) {
                                        return value + '%';
                                    }
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.1)'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Performance Categories',
                                    font: { size: 12, weight: 'bold' }
                                },
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });

                // Metrics Chart
                const metricsCanvas = document.getElementById('metricsChart');
                if (!metricsCanvas) {
                    console.error('Metrics chart canvas not found');
                    return;
                }
                const metricsCtx = metricsCanvas.getContext('2d');
                if (!metricsCtx) {
                    console.error('Could not get 2d context for metrics chart');
                    return;
                }
                new Chart(metricsCtx, {
                    type: 'bar',
                    data: {
                        labels: data.metrics.labels,
                        datasets: [{
                            label: 'Hours',
                            data: data.metrics.data,
                            backgroundColor: [
                                'rgba(54, 162, 235, 0.8)',  // ETC Hours - Blue
                                'rgba(75, 192, 192, 0.8)',  // ATC Hours - Teal
                                'rgba(255, 159, 64, 0.8)',  // Working Hours - Orange
                                'rgba(153, 102, 255, 0.8)'  // Productive Hours - Purple
                            ],
                            borderColor: [
                                'rgba(54, 162, 235, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(255, 159, 64, 1)',
                                'rgba(153, 102, 255, 1)'
                            ],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Hours Comparison',
                                font: { size: 16, weight: 'bold' }
                            },
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': ' + context.parsed.y.toFixed(2) + ' hours';
                                    }
                                }
                            },
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Hours',
                                    font: { size: 12, weight: 'bold' }
                                },
                                ticks: {
                                    callback: function(value) {
                                        return value + 'h';
                                    }
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.1)'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Hour Types',
                                    font: { size: 12, weight: 'bold' }
                                },
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });

                // Feedback Charts
                @if($performance->feedbacks->count() > 0)
                    @foreach($performance->feedbacks as $feedback)
                        @if($feedback->communication_skill || $feedback->teamwork)
                        const feedbackCanvas{{ $feedback->id }} = document.getElementById('feedbackChart{{ $feedback->id }}');
                        if (feedbackCanvas{{ $feedback->id }}) {
                            const feedbackCtx{{ $feedback->id }} = feedbackCanvas{{ $feedback->id }}.getContext('2d');
                            if (feedbackCtx{{ $feedback->id }}) {
                                new Chart(feedbackCtx{{ $feedback->id }}, {
                            type: 'radar',
                            data: {
                                labels: ['Communication', 'Teamwork', 'Problem Solving', 'Initiative', 'Quality', 'Reliability', 'Adaptability', 'Leadership'],
                                datasets: [{
                                    label: 'Feedback Scores',
                                    data: [
                                        {{ $feedback->communication_skill ?? 0 }},
                                        {{ $feedback->teamwork ?? 0 }},
                                        {{ $feedback->problem_solving ?? 0 }},
                                        {{ $feedback->initiative ?? 0 }},
                                        {{ $feedback->quality_of_work ?? 0 }},
                                        {{ $feedback->reliability ?? 0 }},
                                        {{ $feedback->adaptability ?? 0 }},
                                        {{ $feedback->leadership ?? 0 }}
                                    ],
                                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    borderWidth: 2
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    r: {
                                        beginAtZero: true,
                                        max: 100
                                    }
                                }
                            }
                        });
                            } else {
                                console.error('Could not get 2d context for feedback chart {{ $feedback->id }}');
                            }
                        } else {
                            console.error('Feedback chart canvas {{ $feedback->id }} not found');
                        }
                        @endif
                    @endforeach
                @endif
            }
        });

        // Helper function for notifications
        function showNotification(title, message, type) {
            if (typeof show_toastr !== 'undefined') {
                show_toastr(title, message, type);
            } else if (typeof toastrs !== 'undefined') {
                toastrs(title, message, type);
            } else if (typeof showToast !== 'undefined') {
                showToast(type, title, message);
            } else {
                alert(title + ': ' + message);
            }
        }

        // Feedback form submit
        @if($isPrivileged)
        $('#feedbackForm').on('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true).text('Saving...');
            
            $.ajax({
                url: '{{ route("performance.feedback.store") }}',
                method: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showNotification('Success', response.message, 'success');
                        $('#feedbackModal').modal('hide');
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        showNotification('Error', response.message || 'Failed to save feedback', 'error');
                        submitBtn.prop('disabled', false).text('{{ __("Save Feedback") }}');
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'An error occurred';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        errorMsg = errors.join(', ');
                    }
                    showNotification('Error', errorMsg, 'error');
                    submitBtn.prop('disabled', false).text('{{ __("Save Feedback") }}');
                }
            });
        });
        @endif
        });
    } catch (error) {
        console.error('Performance Report script error:', error);
    }
</script>
@endpush

