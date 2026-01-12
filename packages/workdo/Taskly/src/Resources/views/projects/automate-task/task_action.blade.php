
@permission('task edit')

    <div class="action-btn me-2 " >
        <a data-ajax-popup="true"  data-size="lg" data-url="{{ route('automate.tasks.edit', [$task->id]) }}"
            class="btn btn-sm align-items-center text-white bg-info" data-bs-toggle="tooltip"
            data-title="{{ __('Task Edit') }}" title="{{ __('Edit') }}"><i class="ti ti-pencil"></i></a>

    </div>
    <!-- <div class="action-btn">-->
    <!--    <div class="col-2">-->
    <!--        <a href="#!" data-uuid="{{ $task->id }}" @if($task->is_pause==0)  data-value="1" data-message="Pause" @else  data-value="0" data-message="Resume Successfully"  @endif  data-action="{{ route('automate.tasks.pause',['tid'=>$task->id]) }}"   class="btn btn-sm change-status align-items-center text-white bg-warning">-->
    <!--            @if($task->is_pause==0)-->
    <!--            <i class="ti ti-player-pause" title="Pause" ></i>-->
    <!--            @else-->
    <!--            <i class="ti ti-player-play" title="Play" ></i>-->
    <!--            @endif-->
    <!--        </a>-->
    <!--    </div>-->
    <!--</div>-->
     @if($task->is_pause==0)
    <div class="action-btn me-2 " >
        <a  data-uuid="{{ $task->id }}" @if($task->is_pause==0)  data-value="1" data-message="Pause" @else  data-value="0" data-message="Resume"  @endif  class="btn btn-sm align-items-center text-white bg-warning change-status" data-action="{{ route('automate.tasks.pause',['tid'=>$task->id]) }}"  style="min-width: 2em !important;"><i class="ti ti-player-pause" title="Pause" ></i></a>
    </div>
    @else
     <div class="action-btn me-2 " >
        <a data-uuid="{{ $task->id }}" @if($task->is_pause==0)  data-value="1" data-message="Pause" @else  data-value="0" data-message="Resume"  @endif class="btn btn-sm align-items-center text-white bg-success change-status"  data-action="{{ route('automate.tasks.pause',['tid'=>$task->id]) }}"  data-bs-toggle="tooltip"><i class="ti ti-player-play" title="Pause" ></i></a>
    </div>
     @endif
@endpermission
@permission('task delete')
    @php
        // Check if task is archived (deleted and done) - only hide delete for archived tasks
        $isArchived = !is_null($task->deleted_at) && strtolower($task->status) === 'done';
        
        // Check if task is missed - only check the is_missed flag, not due date logic
        $isMissed = isset($task->is_missed) && $task->is_missed == 1;
    @endphp
    
    @if((Auth::user()->hasRole('company')) || (Auth::user()->hasRole('Manager All Access')) || (Auth::user()->hasRole('hr')))
        @if(!$isArchived && !$isMissed)
            <div class="action-btn">
                <div class="col-3">
                    <a href="#!" class="btn btn-sm   align-items-center text-white bg-danger"
                        onclick="deleteRecord('{{ route('automate.tasks.destroy', ['tid'=>$task->id]) }}')">
                        <i class="ti ti-trash" title="Delete"></i>
                    </a>
                </div>
            </div>
        @endif
    @endif
@endpermission
