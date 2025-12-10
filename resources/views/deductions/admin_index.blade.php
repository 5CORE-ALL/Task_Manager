@extends('layouts.main')

@section('page-title')
    {{ __('Manage Deductions') }}
@endsection

@section('page-breadcrumb')
    {{ __('Deductions') }}
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header card-body table-border-style">
                <div class="d-flex justify-content-between align-items-center">
                    <h5>{{ __('All Team Deductions') }}</h5>
                    <div>
                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deductionModal">
                            <i class="ti ti-plus"></i> {{ __('Apply Deduction') }}
                        </button>
                    </div>
                </div>
                
                @if($deductions->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Applied By') }}</th>
                                    <th>{{ __('Applied To') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Deduction Date') }}</th>
                                    <th>{{ __('Reason') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Date Created') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deductions as $deduction)
                                    <tr>
                                        <td>{{ $deduction->giver ? $deduction->giver->name : 'N/A' }}</td>
                                        <td>{{ $deduction->receiver ? $deduction->receiver->name : 'N/A' }}</td>
                                        <td class="text-danger">-₹{{ number_format($deduction->amount, 2) }}</td>
                                        <td>{{ $deduction->deduction_date ? $deduction->deduction_date->format('M d, Y') : 'N/A' }}</td>
                                        <td>{{ $deduction->description }}</td>
                                        <td>
                                            <span class="badge bg-{{ $deduction->status == 'active' ? 'danger' : 'secondary' }}">
                                                {{ ucfirst($deduction->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $deduction->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <div class="action-btn">
                                                <a href="{{ route('deductions.show', $deduction->id) }}" 
                                                   class="btn btn-primary btn-sm" 
                                                   title="{{ __('View Details') }}">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-danger btn-sm delete-deduction" 
                                                        data-id="{{ $deduction->id }}"
                                                        title="{{ __('Delete') }}">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <p class="text-muted">{{ __('No deductions found.') }}</p>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deductionModal">
                            <i class="ti ti-plus"></i> {{ __('Apply First Deduction') }}
                        </button>
                    </div>
                @endif
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
        const row = $(this).closest('tr');
        
        if (confirm('Are you sure you want to delete this deduction?')) {
            $.ajax({
                url: `/deductions/${deductionId}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        row.fadeOut(300, function() {
                            $(this).remove();
                        });
                        
                        // Show success message
                        toastrs('success', response.success || 'Deduction deleted successfully');
                        
                        // Reload page if no more rows
                        if ($('tbody tr').length === 1) {
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        }
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
