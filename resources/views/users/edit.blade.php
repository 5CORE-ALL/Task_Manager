@php
    if(Auth::user()->type=='super admin')
    {
        $name = __('Customer');
    }
    else{

        $name =__('User');
    }
@endphp
    {{Form::model($user,array('route' => array('users.update', $user->id), 'method' => 'PUT','class'=>'needs-validation','novalidate')) }}
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    {{Form::label('name',__('Name'),['class'=>'form-label']) }}<x-required></x-required>
                    {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter '.($name).' Name'),'required'=>'required'))}}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    {{Form::label('email',__('Email'),['class'=>'form-label'])}}<x-required></x-required>
                    {{Form::email('email',null,array('class'=>'form-control','placeholder'=>__('Enter '.($name).' Email'),'required'=>'required'))}}
                </div>
            </div>
            @if(Auth::user()->type == 'super admin')
                <div class="col-md-12">
                    <div class="form-group">
                        {{ Form::label('role', __('Role'),['class'=>'form-label']) }}<x-required></x-required>
                        @php
                            $currentRole = $user->roles->first();
                            $currentRoleId = $currentRole ? $currentRole->id : null;
                        @endphp
                        {{ Form::select('role', $roles, $currentRoleId, ['class' => 'form-control','placeholder'=>'Select Role', 'id' => 'role_select','required'=>'required']) }}
                        <div class="text-xs mt-1">
                            <span class="text-muted">{{ __('Select the role for this user. The role determines what permissions the user has.') }}</span>
                        </div>
                    </div>
                </div>
            @endif
            @if(Auth::user()->email == 'president@5core.com')
                <div class="col-md-12">
                    <div class="form-group">
                        {{Form::label('overdue_duration_days',__('Overdue Duration (Days)'),['class'=>'form-label'])}}
                        {{Form::number('overdue_duration_days',null,array('class'=>'form-control','placeholder'=>__('Enter overdue duration in days (leave empty for default)'),'min'=>'0'))}}
                        <div class="text-xs mt-1">
                            <span class="text-muted">{{ __('Number of days after due date when tasks are considered overdue. Leave empty to use default (0 days).') }}</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{__('Cancel')}}</button>
        {{Form::submit(__('Update'),array('class'=>'btn  btn-primary'))}}
    </div>
    {{Form::close()}}
