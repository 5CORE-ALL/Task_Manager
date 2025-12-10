@permission('task show')
    <div class="action-btn me-2">
        <a data-size="lg" data-url="{{ route('tasks.show', [$task->id]) }}" data-bs-toggle="tooltip"
            title="{{ __('View') }}" data-ajax-popup="true" data-title="{{ __('View') }}"
            class="mx-3 btn btn-sm align-items-center text-white bg-warning ">
            <i class="ti ti-eye"></i>
        </a>
    </div>
@endpermission
@permission('task edit')
    <div class="action-btn me-2">
        <a data-ajax-popup="true" data-size="lg" data-url="{{ route('tasks.edit', [$task->id]) }}"
            class="btn btn-sm align-items-center text-white bg-info" data-bs-toggle="tooltip"
            data-title="{{ __('Task Edit') }}" title="{{ __('Edit') }}"><i class="ti ti-pencil"></i> </a>

    </div>
@endpermission

<!--@permission('task delete')-->
<!--     <div class="action-btn">-->
<!--        <div class="col-3">-->
<!--            <a href="#!" class="btn btn-sm   align-items-center text-white bg-danger"-->
<!--                onclick="deleteRecord('{{ route('tasks.destroy', ['tid'=>$task->id]) }}')">-->
<!--                <i class="ti ti-trash" title="Delete"></i>-->
<!--            </a>-->
<!--        </div>-->
<!--    </div>-->
<!--@endpermission-->

@permission('task delete')
    @php
        // Check if the current user is an assignee
        $currentUserEmail = Auth::user()->email;
        $isAssignee = in_array($currentUserEmail, explode(',', $task->assign_to));

        // Check if the current user is the assignor
        $isAssignor = $currentUserEmail === $task->assignor;
    @endphp

    @if(!$isAssignee || $isAssignor)
        <div class="action-btn">
            <div class="col-3">
                <a href="#!" class="btn btn-sm align-items-center text-white bg-danger"
                    onclick="deleteRecord('{{ route('tasks.destroy', ['tid' => $task->id]) }}')">
                    <i class="ti ti-trash" title="Delete"></i>
                </a>
            </div>
        </div>
    @endif
@endpermission

<!--@permission('task delete')-->
<!--    <div class="action-btn">-->
<!--        {!! Form::open(['method' => 'DELETE', 'route' => ['tasks.destroy',$task->id]]) !!}-->
<!--        <a href="#!" class="btn btn-sm   align-items-center text-white show_confirm bg-danger" data-bs-toggle="tooltip"-->
<!--            title='Delete'>-->
<!--            <i class="ti ti-trash"></i>-->
<!--        </a>-->
<!--        {!! Form::close() !!}-->
<!--    </div>-->
<!--@endpermission-->
