@extends('layouts.app')
@section('page-title')
    {{ __('Missed Tasks') }}
@endsection
@section('content')
<div class="container">
    <h2>{{ __('Missed Tasks') }}</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Type') }}</th>
                <th>{{ __('Schedule Type') }}</th>
                <th>{{ __('Missed At') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($missedTasks as $task)
                <tr>
                    <td>{{ $task->id }}</td>
                    <td>{{ $task->title }}</td>
                    <td>{{ $task->task_type }}</td>
                    <td>{{ $task->schedule_type }}</td>
                    <td>{{ $task->missed_at }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">{{ __('No missed tasks found.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
