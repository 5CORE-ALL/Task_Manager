@extends('layouts.main')

@section('page-title')
    {{ __('Deduction Details') }}
@endsection

@section('page-breadcrumb')
    {{ __('Deductions') }},{{ __('Details') }}
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h5>{{ __('Deduction Information') }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <strong>{{ __('Applied By:') }}</strong>
                            <span>{{ $deduction->giver ? $deduction->giver->name : 'N/A' }}</span>
                        </div>
                        <div class="info-item mb-3">
                            <strong>{{ __('Applied To:') }}</strong>
                            <span>{{ $deduction->receiver ? $deduction->receiver->name : 'N/A' }}</span>
                        </div>
                        <div class="info-item mb-3">
                            <strong>{{ __('Amount:') }}</strong>
                            <span class="text-danger">-₹{{ number_format($deduction->amount, 2) }}</span>
                        </div>
                        <div class="info-item mb-3">
                            <strong>{{ __('Deduction Date:') }}</strong>
                            <span>{{ $deduction->deduction_date ? $deduction->deduction_date->format('M d, Y') : 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <strong>{{ __('Status:') }}</strong>
                            <span class="badge bg-{{ $deduction->status == 'active' ? 'danger' : 'secondary' }}">
                                {{ ucfirst($deduction->status) }}
                            </span>
                        </div>
                        <div class="info-item mb-3">
                            <strong>{{ __('Created:') }}</strong>
                            <span>{{ $deduction->created_at->format('M d, Y H:i') }}</span>
                        </div>
                        <div class="info-item mb-3">
                            <strong>{{ __('Department:') }}</strong>
                            <span>{{ $deduction->department ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item mb-3">
                            <strong>{{ __('Month:') }}</strong>
                            <span>{{ $deduction->deduction_month ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="info-item">
                            <strong>{{ __('Reason for Deduction:') }}</strong>
                            <div class="mt-2 p-3 bg-light rounded">
                                {{ $deduction->description }}
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($deduction->approval_reason)
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="info-item">
                            <strong>{{ __('Approval Reason:') }}</strong>
                            <div class="mt-2 p-3 bg-light rounded">
                                {{ $deduction->approval_reason }}
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
                <div class="row mt-4">
                    <div class="col-12">
                        <a href="{{ route('deductions.index') }}" class="btn btn-secondary">
                            <i class="ti ti-arrow-left"></i> {{ __('Back to Deductions') }}
                        </a>
                        
                        @if(auth()->user()->type == 'super admin' || in_array(auth()->user()->email, ['president@5core.com', 'tech-support@5core.com']))
                        <button type="button" class="btn btn-danger ms-2 delete-deduction" data-id="{{ $deduction->id }}">
                            <i class="ti ti-trash"></i> {{ __('Delete Deduction') }}
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Delete deduction
    $('.delete-deduction').on('click', function() {
        const deductionId = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this deduction?')) {
            $.ajax({
                url: `/deductions/${deductionId}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        toastrs('success', response.success || 'Deduction deleted successfully');
                        setTimeout(() => {
                            window.location.href = '{{ route("deductions.index") }}';
                        }, 1000);
                    }
                },
                error: function(xhr) {
                    const message = xhr.responseJSON?.error || 'Failed to delete deduction';
                    toastrs('error', message);
                }
            });
        }
    });
});
</script>
@endpush
@endsection
