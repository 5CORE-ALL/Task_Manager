<form method="post" action="{{ route('teams.store') }}" class="needs-validation" novalidate="">
    @csrf
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="form-label">{{ __('Team Name') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" required placeholder="{{ __('Enter team name') }}">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="form-label">{{ __('Team Creator') }}</label>
                    <input type="text" class="form-control" value="{{ Auth::user()->name }} ({{ Auth::user()->email }})" disabled>
                    <small class="form-text text-muted">{{ __('You will be set as the team creator automatically.') }}</small>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="form-label">{{ __('Team Members') }}</label>
                    <select class="form-control multi-select choices" name="members[]" multiple id="members" data-placeholder="{{ __('Select Team Members ...') }}">
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }} ({{ $employee->email }})</option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">{{ __('Select multiple team members. A member can belong to multiple teams.') }}</small>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="form-label">{{ __('Description') }}</label>
                    <textarea class="form-control" name="description" rows="3" placeholder="{{ __('Enter team description') }}"></textarea>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
    </div>
</form>
<script>
    $(document).ready(function() {
        if (typeof Choices !== 'undefined') {
            new Choices('#members', {
                removeItemButton: true,
                searchEnabled: true,
                placeholder: true,
                placeholderValue: '{{ __('Select Team Members ...') }}',
                loadingText: 'Loading...',
            });
        }
    });
</script>
