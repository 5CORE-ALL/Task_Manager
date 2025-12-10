@extends('layouts.main')

@section('page-title')
    {{ __('Manage Incentives') }}
@endsection

@section('page-breadcrumb')
    {{ __('Incentives') }}
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header card-body table-border-style">
                <h5>{{ __('All Team Incentives') }}</h5>
                
                @if($incentives->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Given By') }}</th>
                                    <th>{{ __('Received By') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Date Range') }}</th>
                                    <th>{{ __('Description') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($incentives as $incentive)
                                    <tr>
                                        <td>{{ $incentive->giver ? $incentive->giver->name : 'N/A' }}</td>
                                        <td>{{ $incentive->receiver ? $incentive->receiver->name : 'N/A' }}</td>
                                        <td>₹{{ number_format($incentive->amount, 2) }}</td>
                                        <td>
                                            {{ $incentive->start_date ? $incentive->start_date->format('M d, Y') : 'N/A' }} 
                                            to 
                                            {{ $incentive->end_date ? $incentive->end_date->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td>{{ $incentive->description }}</td>
                                        <td>
                                            <span class="badge bg-{{ $incentive->status == 'active' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($incentive->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $incentive->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            @if(in_array(Auth::user()->email, ['president@5core.com', 'tech-support@5core.com']) || Auth::user()->type == 'super admin')
                                                <button class="btn btn-sm btn-danger delete-incentive" 
                                                        data-id="{{ $incentive->id }}"
                                                        data-bs-toggle="tooltip" 
                                                        title="Delete">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <p class="text-muted">{{ __('No incentives given yet.') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Delete incentive functionality
    $('.delete-incentive').on('click', function() {
        const incentiveId = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this incentive?')) {
            $.ajax({
                url: `/incentives/${incentiveId}`,
                method: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.error || 'Error deleting incentive');
                    }
                },
                error: function(xhr) {
                    const errorMessage = xhr.responseJSON?.error || 'Error deleting incentive';
                    alert(errorMessage);
                }
            });
        }
    });
});
</script>
@endsection
