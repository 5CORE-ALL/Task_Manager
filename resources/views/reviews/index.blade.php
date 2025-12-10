@extends('layouts.main')

@section('page-title')
    {{ __('Reviews Management') }}
@endsection

@section('page-breadcrumb')
    {{ __('Reviews') }}
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('My Reviews') }}</h5>
                    <small class="text-muted">{{ __('Reviews I have received') }}</small>
                </div>
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('Sr. No.') }}</th>
                                    <th>{{ __('Reviewer') }}</th>
                                    <th>{{ __('Reviewee') }}</th>
                                    <th>{{ __('Rating') }}</th>
                                    <th>{{ __('Description') }}</th>
                                    <th>{{ __('Screenshot') }}</th>
                                    <th>{{ __('Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reviews as $index => $review)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $review->reviewer ? $review->reviewer->name : 'N/A' }}</td>
                                        <td>{{ $review->reviewee ? $review->reviewee->name : 'N/A' }}</td>
                                        <td>
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $review->rating)
                                                    <i class="fas fa-star text-warning"></i>
                                                @else
                                                    <i class="far fa-star text-muted"></i>
                                                @endif
                                            @endfor
                                            ({{ $review->rating }}/5)
                                        </td>
                                        <td>
                                            <div class="text-wrap" style="max-width: 300px;">
                                                {{ $review->description }}
                                            </div>
                                        </td>
                                        <td>
                                            @if($review->screenshot)
                                                <a href="{{ asset($review->screenshot) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-image"></i> {{ __('View Screenshot') }}
                                                </a>
                                            @else
                                                <span class="text-muted">{{ __('No Screenshot') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ date('M d, Y H:i', strtotime($review->created_at)) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">{{ __('No reviews found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
