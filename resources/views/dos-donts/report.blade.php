@extends('layouts.main')

@section('page-title')
    {{ __('DO\'s & DON\'T Report') }}
@endsection

@section('page-breadcrumb')
    {{ __('DO\'s & DON\'T Report') }}
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px 10px 0 0;">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="card-title text-white mb-0">
                        <i class="ti ti-list-check" style="font-size: 1.5rem; margin-right: 10px;"></i>
                        📝 DO's & DON'T Productivity Report 📊
                    </h4>
                    <div class="text-white">
                        <i class="ti ti-calendar"></i>
                        {{ now()->format('F d, Y') }}
                    </div>
                </div>
            </div>
            <div class="card-body" style="background: #f8f9ff; padding: 30px;">
                
                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border-radius: 15px;">
                            <div class="card-body text-center text-white">
                                <div style="font-size: 3rem; margin-bottom: 10px;">✅</div>
                                <h3 class="mb-1">{{ $dos->count() }}</h3>
                                <p class="mb-0 opacity-75">Total DO's</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%); border-radius: 15px;">
                            <div class="card-body text-center text-white">
                                <div style="font-size: 3rem; margin-bottom: 10px;">🚫</div>
                                <h3 class="mb-1">{{ $donts->count() }}</h3>
                                <p class="mb-0 opacity-75">Total DON'Ts</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DO's Section -->
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm" style="border-radius: 15px; min-height: 500px;">
                            <div class="card-header" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border-radius: 15px 15px 0 0;">
                                <h5 class="text-white mb-0">
                                    <i class="ti ti-check-circle" style="margin-right: 10px;"></i>
                                    ✅ DO's - Actions to Boost Productivity
                                </h5>
                            </div>
                            <div class="card-body" style="padding: 25px; background: #f8fff8;">
                                @if($dos->count() > 0)
                                    <div class="dos-list">
                                        @foreach($dos as $index => $do)
                                            <div class="do-item mb-4" style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(40, 167, 69, 0.1); border-left: 4px solid #28a745;">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div class="priority-badge">
                                                        @if($do->priority == 'High')
                                                            <span class="badge" style="background: #dc3545; color: white; font-size: 0.75rem;">🔥 High Priority</span>
                                                        @elseif($do->priority == 'Medium')
                                                            <span class="badge" style="background: #ffc107; color: #212529; font-size: 0.75rem;">⚡ Medium Priority</span>
                                                        @else
                                                            <span class="badge" style="background: #28a745; color: white; font-size: 0.75rem;">🟢 Low Priority</span>
                                                        @endif
                                                    </div>
                                                    <small class="text-muted">
                                                        <i class="ti ti-clock"></i>
                                                        {{ $do->created_at->format('M d, Y h:i A') }}
                                                    </small>
                                                </div>
                                                
                                                <h6 class="fw-bold text-success mb-2" style="font-size: 1.1rem;">
                                                    <i class="ti ti-target" style="margin-right: 5px;"></i>
                                                    {{ $do->what }}
                                                </h6>
                                                
                                                <div class="mb-3">
                                                    <strong class="text-dark">💡 Why:</strong>
                                                    <p class="mb-1 text-muted">{{ $do->why }}</p>
                                                </div>
                                                
                                                <div class="mb-0">
                                                    <strong class="text-dark">📈 Impact:</strong>
                                                    <p class="mb-0 text-muted">{{ $do->impact }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <div style="font-size: 4rem; opacity: 0.3; margin-bottom: 20px;">✅</div>
                                        <h5 class="text-muted">No DO's found</h5>
                                        <p class="text-muted">Start adding productive actions to boost your efficiency!</p>
                                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#dosModal">
                                            <i class="ti ti-plus"></i> Add Your First DO
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- DON'T Section -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm" style="border-radius: 15px; min-height: 500px;">
                            <div class="card-header" style="background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%); border-radius: 15px 15px 0 0;">
                                <h5 class="text-white mb-0">
                                    <i class="ti ti-ban" style="margin-right: 10px;"></i>
                                    🚫 DON'Ts - Actions to Avoid
                                </h5>
                            </div>
                            <div class="card-body" style="padding: 25px; background: #fff8f8;">
                                @if($donts->count() > 0)
                                    <div class="donts-list">
                                        @foreach($donts as $index => $dont)
                                            <div class="dont-item mb-4" style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(220, 53, 69, 0.1); border-left: 4px solid #dc3545;">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div class="severity-badge">
                                                        @if($dont->severity == 'Critical')
                                                            <span class="badge" style="background: #dc3545; color: white; font-size: 0.75rem;">🚨 Critical</span>
                                                        @elseif($dont->severity == 'High')
                                                            <span class="badge" style="background: #fd7e14; color: white; font-size: 0.75rem;">⚠️ High</span>
                                                        @else
                                                            <span class="badge" style="background: #ffc107; color: #212529; font-size: 0.75rem;">🔸 Medium</span>
                                                        @endif
                                                    </div>
                                                    <small class="text-muted">
                                                        <i class="ti ti-clock"></i>
                                                        {{ $dont->created_at->format('M d, Y h:i A') }}
                                                    </small>
                                                </div>
                                                
                                                <h6 class="fw-bold text-danger mb-2" style="font-size: 1.1rem;">
                                                    <i class="ti ti-alert-triangle" style="margin-right: 5px;"></i>
                                                    {{ $dont->what }}
                                                </h6>
                                                
                                                <div class="mb-3">
                                                    <strong class="text-dark">🚨 Why Avoid:</strong>
                                                    <p class="mb-1 text-muted">{{ $dont->why }}</p>
                                                </div>
                                                
                                                <div class="mb-0">
                                                    <strong class="text-dark">📉 Negative Impact:</strong>
                                                    <p class="mb-0 text-muted">{{ $dont->impact }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <div style="font-size: 4rem; opacity: 0.3; margin-bottom: 20px;">🚫</div>
                                        <h5 class="text-muted">No DON'Ts found</h5>
                                        <p class="text-muted">Start tracking actions to avoid for better productivity!</p>
                                        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#dontModal">
                                            <i class="ti ti-plus"></i> Add Your First DON'T
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-4">
                    <div class="col-12 text-center">
                        <div class="d-flex justify-content-center gap-3">
                            <button class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#dosModal" style="border-radius: 10px; padding: 12px 30px;">
                                <i class="ti ti-plus"></i> Add New DO
                            </button>
                            <button class="btn btn-danger btn-lg" data-bs-toggle="modal" data-bs-target="#dontModal" style="border-radius: 10px; padding: 12px 30px;">
                                <i class="ti ti-plus"></i> Add New DON'T
                            </button>
                            <button class="btn btn-info btn-lg" onclick="window.print()" style="border-radius: 10px; padding: 12px 30px;">
                                <i class="ti ti-printer"></i> Print Report
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Statistics Section -->
                @if($dos->count() > 0 || $donts->count() > 0)
                <div class="row mt-5">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm" style="border-radius: 15px; background: linear-gradient(135deg, #f8f9ff 0%, #e8eaf6 100%);">
                            <div class="card-body text-center py-4">
                                <h5 class="mb-3">📊 Your Productivity Insights</h5>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="text-success">
                                            <h4>{{ $dos->where('priority', 'High')->count() }}</h4>
                                            <small>High Priority DO's</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-warning">
                                            <h4>{{ $dos->where('priority', 'Medium')->count() }}</h4>
                                            <small>Medium Priority DO's</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-danger">
                                            <h4>{{ $donts->where('severity', 'Critical')->count() }}</h4>
                                            <small>Critical DON'Ts</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-info">
                                            <h4>{{ $dos->count() + $donts->count() }}</h4>
                                            <small>Total Entries</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Include the existing DO's and DON'T modals -->
@include('partials.dos-donts-modals')

<style>
    /* Custom styles for the report page */
    .dos-list, .donts-list {
        max-height: 600px;
        overflow-y: auto;
        padding-right: 10px;
    }
    
    .dos-list::-webkit-scrollbar, .donts-list::-webkit-scrollbar {
        width: 6px;
    }
    
    .dos-list::-webkit-scrollbar-track, .donts-list::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .dos-list::-webkit-scrollbar-thumb {
        background: #28a745;
        border-radius: 10px;
    }
    
    .donts-list::-webkit-scrollbar-thumb {
        background: #dc3545;
        border-radius: 10px;
    }
    
    .do-item:hover, .dont-item:hover {
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }
    
    @media print {
        .btn, .card-header {
            -webkit-print-color-adjust: exact;
            color-adjust: exact;
        }
    }
    
    /* Animation for cards */
    .do-item, .dont-item {
        animation: fadeInUp 0.5s ease forwards;
        opacity: 0;
        transform: translateY(20px);
    }
    
    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Stagger animation delay */
    .do-item:nth-child(1), .dont-item:nth-child(1) { animation-delay: 0.1s; }
    .do-item:nth-child(2), .dont-item:nth-child(2) { animation-delay: 0.2s; }
    .do-item:nth-child(3), .dont-item:nth-child(3) { animation-delay: 0.3s; }
    .do-item:nth-child(4), .dont-item:nth-child(4) { animation-delay: 0.4s; }
    .do-item:nth-child(5), .dont-item:nth-child(5) { animation-delay: 0.5s; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add some interactivity
    const doItems = document.querySelectorAll('.do-item');
    const dontItems = document.querySelectorAll('.dont-item');
    
    // Add click effect
    [...doItems, ...dontItems].forEach(item => {
        item.addEventListener('click', function() {
            this.style.transform = 'scale(1.02)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
        });
    });
    
    // Show success message if coming from form submission
    @if(session('success'))
        setTimeout(() => {
            alert('{{ session('success') }}');
        }, 500);
    @endif
});
</script>
@endsection
