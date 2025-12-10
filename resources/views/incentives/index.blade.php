@extends('layouts.main')

@section('page-title')
    {{ __('My Incentives') }}
@endsection

@section('page-breadcrumb')
    {{ __('Incentives') }}
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header card-body table-border-style">
                <h5>{{ __('My Received Incentives') }}</h5>
                
                @if($incentives->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Given By') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Date Range') }}</th>
                                    <th>{{ __('Description') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($incentives as $incentive)
                                    <tr>
                                        <td>{{ $incentive->giver ? $incentive->giver->name : 'N/A' }}</td>
                                        <td>${{ number_format($incentive->amount, 2) }}</td>
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
                                        <td>{{ $incentive->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <p class="text-muted">{{ __('No incentives received yet.') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
