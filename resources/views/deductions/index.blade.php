@extends('layouts.main')

@section('page-title')
    {{ __('My Deductions') }}
@endsection

@section('page-breadcrumb')
    {{ __('Deductions') }}
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header card-body table-border-style">
                <h5>{{ __('My Applied Deductions') }}</h5>
                
                @if($deductions->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Applied By') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Deduction Date') }}</th>
                                    <th>{{ __('Reason') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Date Created') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deductions as $deduction)
                                    <tr>
                                        <td>{{ $deduction->giver ? $deduction->giver->name : 'N/A' }}</td>
                                        <td class="text-danger">-₹{{ number_format($deduction->amount, 2) }}</td>
                                        <td>{{ $deduction->deduction_date ? $deduction->deduction_date->format('M d, Y') : 'N/A' }}</td>
                                        <td>{{ $deduction->description }}</td>
                                        <td>
                                            <span class="badge bg-{{ $deduction->status == 'active' ? 'danger' : 'secondary' }}">
                                                {{ ucfirst($deduction->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $deduction->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <p class="text-muted">{{ __('No deductions applied yet.') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
