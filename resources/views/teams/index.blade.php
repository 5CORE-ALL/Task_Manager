@extends('layouts.main')
@section('page-title')
    {{ __('Team Management') }}
@endsection
@section('page-breadcrumb')
    {{ __('Teams') }}
@endsection
@section('page-action')
    <a class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="lg" data-title="{{ __('Create Team') }}"
        data-url="{{ route('teams.create') }}" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Create') }}">
        <i class="ti ti-plus"></i>
    </a>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Team Name') }}</th>
                                    <th>{{ __('Team Leader') }}</th>
                                    <th>{{ __('Members') }}</th>
                                    <th>{{ __('Description') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($teams as $team)
                                    <tr>
                                        <td>{{ $team->name }}</td>
                                        <td>{{ $team->teamLeader->name ?? 'N/A' }}</td>
                                        <td>
                                            @if ($team->members->count() > 0)
                                                @foreach ($team->members as $member)
                                                    <span class="badge bg-primary me-1">{{ $member->name }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">{{ __('No members') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ Str::limit($team->description, 50) }}</td>
                                        <td>
                                            <a class="btn btn-sm btn-primary me-1" data-ajax-popup="true" data-size="lg"
                                                data-title="{{ __('Edit Team') }}" data-url="{{ route('teams.edit', $team->id) }}"
                                                data-bs-toggle="tooltip" data-bs-original-title="{{ __('Edit') }}">
                                                <i class="ti ti-pencil"></i>
                                            </a>
                                            <form action="{{ route('teams.destroy', $team->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger show_confirm"
                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                    data-confirm-yes="delete-form-{{ $team->id }}" data-bs-toggle="tooltip"
                                                    data-bs-original-title="{{ __('Delete') }}">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
