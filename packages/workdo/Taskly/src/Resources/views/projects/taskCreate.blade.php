
@if($currentWorkspace)
    <form class="needs-validation dfsdfdfsd" method="post" action="{{ route('tasks.save') }}" novalidate enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
            <div id="modal-warning" class="alert alert-warning d-none" role="alert">
                  Please assign the task or click <b>Cancel Button/Close X icon</b> to exit.
                  Close the modal anyway?
                </div>
            <div class="text-end">
                @if (module_is_active('AIAssistant'))
                    @include('aiassistant::ai.generate_ai_btn',['template_module' => 'project task','module'=>'Taskly'])
                @endif
            </div>
            <div class="row">
                
                <div class="form-group col-md-6">
                    <label class="form-label">{{ __('Group')}}</label>
                                        <input type="text" class="form-control form-control-light" name="group" id="task-group" placeholder="{{ __('Enter Group')}}"  value=""  maxlength="25" >

                </div>
                <div class="form-group col-md-6">
                    <label class="form-label">{{ __('Priority')}}</label>
                    <select class="form-control form-control-light" name="priority" id="task-priority" required>
                        <option value="normal">{{ __('normal')}}</option>
                        <option value="urgent">{{ __('urgent')}}</option>
                        <option value="Take your time">{{ __('Take your time')}}</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label class="form-label">{{ __('Task')}}</label><x-required></x-required>
                    <input type="text" class="form-control form-control-light" id="task-title" placeholder="{{ __('Enter Task')}}" name="title" required>
                </div>
                 <div class="form-group col-md-6">
                    <label class="form-label">{{ __('Status')}}</label>
                    <select class="form-control form-control-light" name="stage_id" id="task-stage">
                        <option value="">{{__('Select Status')}}</option>
                        @foreach($stages as $stage)
                            <option value="{{$stage->name}}" data-color="{{ $stage->color }}">  {{$stage->name}}
                                </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label class="form-label">{{ __('Assignor')}}</label><x-required></x-required>

                    <select class=" multi-select choices" id="assignor" name="assignor[]" multiple="multiple"  data-placeholder="{{ __('Select Users ...') }}" required>
                        @foreach($users as $u)
                            <option value="{{$u->email}}" @if($u->email==auth()->user()->email) selected @endif>{{ formatUserName($u->name) }}</option>
                             <!--<option value="{{$u->email}}" @if($u->email==auth()->user()->email) selected @endif>{{$u->name}} - {{$u->email}} - {{$u->mobile_no}}</option>-->
                        @endforeach
                    </select>
                    <p class="text-danger d-none" id="user_validation">{{__('Assign To filed is required.')}}</p>
                </div>
               
             
                <!-- Add this toggle switch after the Assignor field -->
            <div class="form-group col-md-6">
                <label class="form-label">{{ __('Task Distribution') }}</label>
                <div class="d-flex align-items-center">
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="splitTasksToggle" name="split_tasks">
                        <label class="form-check-label ms-2" for="splitTasksToggle">{{ __('Split tasks between assignees') }}</label>
                    </div>
                </div>
                <small class="text-muted">{{ __('When enabled, each assignee will get their own copy of this task') }}</small>
            </div>
            
            <!-- Flag Raise Option -->
            <div class="form-group col-md-6">
                <label class="form-label">{{ __('Flag Raise') }}</label>
                <div class="d-flex align-items-center">
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="flagRaiseToggle" name="flag_raise">
                        <label class="form-check-label ms-2" for="flagRaiseToggle">{{ __('Create flag for this task') }}</label>
                    </div>
                </div>
                <small class="text-muted">{{ __('When enabled, this task will be synced to flag management') }}</small>
            </div>
            
            <!-- Flag Raise Details (shown when checkbox is checked) -->
            <div id="flagRaiseDetails" class="d-none" style="width: 100%; margin-top: 15px;">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">{{ __('Flag Type') }}</label>
                        <select class="form-control form-control-light" name="flag_type" id="flag_type">
                            <option value="red">{{ __('Red Flag') }}</option>
                            <option value="green">{{ __('Green Flag') }}</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">{{ __('Flag Description') }}</label>
                        <textarea class="form-control form-control-light" id="flag_description" name="flag_description" rows="2" placeholder="{{ __('Enter flag description (optional)') }}"></textarea>
                    </div>
                </div>
            </div>
               <div class="form-group col-md-6">
                    <label class="form-label">{{ __('Assign To')}}</label><x-required></x-required>

                    <select class=" multi-select choices" id="assign_to" name="assign_to[]"  multiple="multiple" data-placeholder="{{ __('Select Users ...') }}" required>
                        <option value="all_members">{{ __('All Members') }}</option>
                        <option value="all_managers">{{ __('All Managers') }}</option>
                        @foreach($users as $u)
                            <option value="{{$u->email}}">{{ formatUserName($u->name) }}</option>
                        @endforeach
                    </select>
                    <p class="text-danger d-none" id="user_validation">{{__('Assign To filed is required.')}}</p>
                </div>


                 <div class="form-group col-md-6">
    <label class="form-label">{{ __('ETC (Min)')}}</label><x-required></x-required>
    <input type="number" class="form-control form-control-light" id="eta_time" 
           placeholder="{{ __('Enter ETA Time')}}" name="eta_time" required 
           min="1" value="10" oninput="this.value = Math.abs(this.value.replace(/[^0-9]/g, '').slice(0, 4));">
</div>
                <div class="form-group col-md-6">
                    <label class="form-label">{{ __('Duration')}}</label><x-required></x-required>
                    <div class='input-group'>
                        <input type='text' class=" form-control form-control-light" id="duration" name="duration" required autocomplete="off"
                            placeholder="Select date range" />
                            <input type="hidden" name="start_date">
                                <input type="hidden" name="due_date">
                                <span class="input-group-text"><i class="feather icon-calendar"></i></span>
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label class="form-label">{{ __('L1')}}</label>
                    <input type="text" class="form-control form-control-light" id="link1" placeholder="{{ __('Enter L1')}}" name="link1">
                </div>
                <div class="form-group col-md-4">
                    <label class="form-label">{{ __('L2')}}</label>
                    <input type="text" class="form-control form-control-light" id="link2" placeholder="{{ __('Enter L2')}}" name="link2">
                </div>
                <div class="form-group col-md-4">
                    <label class="form-label">{{ __('Training Link')}}</label>
                    <input type="text" class="form-control form-control-light" id="link3" placeholder="{{ __('Enter training Note')}}" name="link3">
                </div>
                <div class="form-group col-md-4">
                    <label class="form-label">{{ __('Video Link')}}</label>
                    <input type="text" class="form-control form-control-light" id="link4" placeholder="{{ __('Enter video Note')}}" name="link4">
                </div>
                <div class="form-group col-md-4">
                    <label class="form-label">{{ __('Form Link')}}</label>
                    <input type="text" class="form-control form-control-light" id="link5" placeholder="{{ __('Enter form Note')}}" name="link5">
                </div>
                <div class="form-group col-md-4">
                    <label class="form-label">{{ __('Form Report Link')}}</label>
                    <input type="text" class="form-control form-control-light" id="link7" placeholder="{{ __('Enter form Note')}}" name="link7">
                </div>
                <div class="form-group col-md-4">
                    <label class="form-label">{{ __('Checklist Link')}}</label>
                    <input type="text" class="form-control form-control-light" id="link6" placeholder="{{ __('Enter checklist link')}}" name="link6">
                </div>
                <div class="form-group col-md-4">
                    <label class="form-label">{{ __('PL')}}</label>
                    <input type="text" class="form-control form-control-light" id="link9" placeholder="{{ __('Enter PL link')}}" name="link9">
                </div>
                <!--<div class="form-group col-md-6">-->
                <!--    <label class="form-label">{{ __('Add Again')}}</label>-->
                <!--   <select class=" multi-select choices" id="is_add_enable" name="is_add_enable" required>-->
                <!--        <option value="true">Yes</option>-->
                <!--        <option value="false">No</option>-->
                <!--    </select>-->
                <!--</div>-->
                <!--<div class="form-group col-md-6">-->
                <!--    <label class="form-label">{{ __('ETA(Min)')}}</label><x-required></x-required>-->
                <!--    <input type="text" class="form-control form-control-light" id="eta_time" placeholder="{{ __('Enter ETA Time')}}" name="eta_time" required>-->
                <!--</div>-->
               
               

                 <div class="form-group col-md-4">
                    <label class="form-label">{{ __('PROCESS')}}</label>
                    <input type="text" class="form-control form-control-light" id="link8" placeholder="{{ __('Enter form Note')}}" name="link8">
                </div>
                   <div class="form-group col-md-4">
                    <label class="form-label">{{ __('Description')}}</label>
                    <textarea class="form-control form-control-light" id="task-description" rows="1" name="description" placeholder="Enter Description" ></textarea>
                </div>
                <div class="form-group col-md-12">
                    <label class="form-label">{{ __('Image') }}</label>
                    <div class="image-upload-preview" style="text-align: center; border: 1px dashed #ccc; padding: 10px; border-radius: 10px;">
                        <span id="file-name" style="display: block; margin: 0 auto;"></span>
                        <label for="file-upload" style="cursor: pointer; display: block; margin-top: 10px;" class="btn btn-sm btn-primary">
                            {{ __('Choose File') }}
                        </label>
                        <input type="file" id="file-upload" name="file" accept="image/*,application/pdf" style="display: none;" />
                    </div>
                 </div>
            <!--AUTOMATION DESIGN CODE -->
            
          
         
         
         <!--END AUTOMATION DESIGN CODE -->
        </div>


            </div>
            @if(module_is_active('CustomField') && !$customFields->isEmpty())
                <div class="col-md-12">
                    <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                        @include('custom-field::formBuilder')
                    </div>
                </div>
            @endif
            @stack('calendar')
        </div>
        <div class="modal-footer">
            <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Cancel')}}</button>
            <input type="submit" value="{{ __('Create')}}" class="btn  btn-primary" id="submit">
        </div>
    </form>
     <link rel="stylesheet" href="{{ asset('packages/workdo/Taskly/src/Resources/assets/libs/bootstrap-daterangepicker/daterangepicker.css')}} ">
     <script src="{{ asset('packages/workdo/Taskly/src/Resources/assets/libs/moment/min/moment.min.js')}}"></script>
     <script src="{{ asset('packages/workdo/Taskly/src/Resources/assets/libs/bootstrap-daterangepicker/daterangepicker.js')}}"></script>
    <style>
    /* Style for the dropdown options */
    #task-stage option {
        padding: 8px 12px;
        margin: 2px 0;
        border-radius: 4px;
    }
    
    /* Hover effect for options */
    #task-stage option:hover {
        opacity: 0.8;
        transform: scale(1.02);
        transition: all 0.2s ease;
    }
    
    /* Make sure text is readable on colored backgrounds */
    #task-stage option {
        color: #000 !important;
    }
    
    /* Specific colors for common statuses (you can add more) */
    #task-stage option[value="Todo"] {
        background-color:rgb(106, 193, 255) !important;
    }
    #task-stage option[value="In Progress"] {
        background-color:rgb(248, 193, 14) !important;
    }
    #task-stage option[value="Done"] {
        background-color:rgb(23, 252, 42) !important;
    }
    #task-stage option[value="Need Help"] {
        background-color:rgb(255, 65, 255) !important;
    }
    #task-stage option[value="Urgent"] {
        background-color:rgb(219, 0, 22) !important;
    }
    #task-stage option[value="Review"] {
        background-color: #e1f5fe !important;
    }
    #task-stage option[value="Hold"] {
        background-color: #f3e5f5 !important;
    }
    #task-stage option[value="Need Approval"] {
        background-color:rgb(179, 255, 0) !important;
    }
    #task-stage option[value="Not Started"] {
        background-color:rgb(251, 255, 0) !important;
    }
    #task-stage option[value="Working"] {
        background-color:rgb(184, 4, 255) !important;
    }
    #task-stage option[value="Monitor"] {
        background-color:rgb(118, 87, 255) !important;
    }
    #task-stage option[value="Dependent"] {
        background-color:rgb(255, 133, 133) !important;
    }
    #task-stage option[value="Approved"] {
        background-color:rgb(255, 230, 0) !important;
    }
    #task-stage option[value="Rework"] {
        background-color:rgb(134, 34, 143) !important;
    }
    #task-stage option[value="Q-Task"] {
        background-color:rgb(226, 131, 144) !important;
    }
</style>


     <script>
            document.addEventListener('DOMContentLoaded', function() {
                const statusSelect = document.getElementById('task-stage');
                
                // Apply colors from data-color attributes
                Array.from(statusSelect.options).forEach(option => {
                    if (option.dataset.color) {
                        option.style.backgroundColor = option.dataset.color;
                        
                        // Set text color based on background brightness for readability
                        const bgColor = option.dataset.color;
                        const r = parseInt(bgColor.substr(1,2), g = parseInt(bgColor.substr(3,2)), b = parseInt(bgColor.substr(5,2));
                        const brightness = (r * 299 + g * 587 + b * 114) / 1000;
                        option.style.color = brightness > 128 ? '#000' : '#fff';
                    }
                });
            });
    </script>
    <!--urgent status  when select that time 1 day time count -->
<script>
    $(function(){
        $("#submit").click(function(e) {
            // Validate Assign To field
            var user = $("#assign_to option:selected").length;
            if (user == 0) {
                $('#user_validation').removeClass('d-none');
                e.preventDefault();
                return false;
            } else {
                $('#user_validation').addClass('d-none');
            }
            
            // Validate ETC (Min) field
            var etaTime = parseInt($('#eta_time').val());
            if (isNaN(etaTime) || etaTime <= 0) {
                e.preventDefault();
                alert('ETC (Min) must be greater than 0');
                $('#eta_time').focus();
                return false;
            }
        });
    });
</script>

    <script>
    // Move the callback function to global scope
    function updateDateRange(start, end) {
        $('#duration').val(start.format('MMM D, YY hh:mm A') + ' - ' + end.format('MMM D, YY hh:mm A'));
        $('input[name="start_date"]').val(start.format('YYYY-MM-DD HH:mm:ss'));
        $('input[name="due_date"]').val(end.format('YYYY-MM-DD HH:mm:ss'));
    }

    // Toggle flag raise details visibility
    $(document).ready(function() {
        $('#flagRaiseToggle').change(function() {
            if ($(this).is(':checked')) {
                $('#flagRaiseDetails').removeClass('d-none');
            } else {
                $('#flagRaiseDetails').addClass('d-none');
            }
        });
    });

    $(document).ready(function() {
        const durationPicker = $('#duration').daterangepicker({
            // ... keep existing daterangepicker configuration
        }, updateDateRange);

        // Add status change handler
        $('#task-stage').change(function() {
            if ($(this).val() === 'Urgent') {
                const urgentStart = moment();
                const urgentEnd = moment().add(1, 'days');
                
                // Update picker and inputs
                durationPicker.data('daterangepicker').setStartDate(urgentStart);
                durationPicker.data('daterangepicker').setEndDate(urgentEnd);
                updateDateRange(urgentStart, urgentEnd);
            }
        });
    });
</script>
<!--urgent status  when select that time 1 day time count-->
    <script>
        $(function () {
              var start = moment('{{ date('Y-m-d') }}', 'YYYY-MM-DD HH:mm:ss');
            var end = moment(start).add(4, 'days'); // Initialize end date with +3 days

            function cb(start, end) {
                var updatedEnd = end.clone().add(4, 'days'); // Clone end before adding days
                console.log("================================",updatedEnd);
                $("form #duration").val(start.format('MMM D, YY hh:mm A') + ' - ' + end.format('MMM D, YY hh:mm A'));
                $('form input[name="start_date"]').val(start.format('YYYY-MM-DD HH:mm:ss'));
                $('form input[name="due_date"]').val(end.format('YYYY-MM-DD HH:mm:ss'));
            }

            $('form #duration').daterangepicker({
                autoApply: true,
                timePicker: true,
                autoUpdateInput: false,
                startDate: start,
                endDate: end,
                locale: {
                    format: 'MMMM D, YYYY hh:mm A',
                    applyLabel: "{{__('Apply')}}",
                    cancelLabel: "{{__('Cancel')}}",
                    fromLabel: "{{__('From')}}",
                    toLabel: "{{__('To')}}",
                    daysOfWeek: [
                        "{{__('Sun')}}",
                        "{{__('Mon')}}",
                        "{{__('Tue')}}",
                        "{{__('Wed')}}",
                        "{{__('Thu')}}",
                        "{{__('Fri')}}",
                        "{{__('Sat')}}"
                    ],
                    monthNames: [
                        "{{__('January')}}",
                        "{{__('February')}}",
                        "{{__('March')}}",
                        "{{__('April')}}",
                        "{{__('May')}}",
                        "{{__('June')}}",
                        "{{__('July')}}",
                        "{{__('August')}}",
                        "{{__('September')}}",
                        "{{__('October')}}",
                        "{{__('November')}}",
                        "{{__('December')}}"
                    ],
                }
            }, cb);

            cb(start, end);
        });
        $(document).on('change', "select[name=project_id]", function () {
            $.get('@auth('web'){{route('home')}}@elseauth{{route('client.home')}}@endauth' + '/userProjectJson/' + $(this).val(), function (data) {
                $('select[name=assign_to]').html('');
                data = JSON.parse(data);
                $(data).each(function (i, d) {
                    $('select[name=assign_to]').append('<option value="' + d.id + '">' + d.name + ' - ' + d.email + '</option>');
                });
            });
            $.get('@auth('web'){{route('home')}}@elseauth{{route('client.home')}}@endauth' + '/projectMilestoneJson/' + $(this).val(), function (data) {
                $('select[name=milestone_id]').html('<option value="">{{__('Select Milestone')}}</option>');
                data = JSON.parse(data);
                $(data).each(function (i, d) {
                    $('select[name=milestone_id]').append('<option value="' + d.id + '">' + d.title + '</option>');
                });
            })
        })
    </script>
        <script>
            const dailyBtn = document.getElementById('daily-btn');
            const weeklyBtn = document.getElementById('weekly-btn');
            const monthlyBtn = document.getElementById('monthly-btn');
            const buttonsContainer = document.getElementById('buttons-container');
            const timePicker = document.getElementById('time-picker');
    
            // Days and Months Data
            const days = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];
            const monthDays = Array.from({ length: 31 }, (_, i) => i + 1);
    
            // Clear previous buttons
            function clearButtons() {
                buttonsContainer.innerHTML = '';
            }
    
            // Generate Round Buttons
            function generateButtons(labels, selectable = true) {
                clearButtons();
                labels.forEach(label => {
                    const btn = document.createElement('div');
                    btn.className = `round-btn ${!selectable ? 'active' : ''}`;
                    btn.textContent = label;
                    if (selectable) {
                        btn.addEventListener('click', () => {
                            btn.classList.toggle('active');
                        });
                    }
                    buttonsContainer.appendChild(btn);
                });
            }
    
            // Calculate End of Month
            function getEndOfMonth() {
                const currentDate = new Date();
                const daysInMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0).getDate();
                return daysInMonth;
            }
    
            // Handle Mode Selection
            function handleModeSelection(mode) {
                // Reset Active States
                document.querySelectorAll('.mode-btn').forEach(btn => btn.disabled = false);
                mode.disabled = true;
                timePicker.classList.remove('d-none');
    
                if (mode.id === 'daily-btn') {
                    generateButtons(days, false); // Pre-selected buttons
                } else if (mode.id === 'weekly-btn') {
                    generateButtons(days, true); // Selectable buttons
                } else if (mode.id === 'monthly-btn') {
                    const customLabels = monthDays.concat('End of Month');
                    generateButtons(customLabels, true); // Always show 31 buttons
    
                    // Add logic for \"End of Month\" click
                    document.querySelectorAll('.round-btn').forEach((btn) => {
                        if (btn.textContent === 'End of Month') {
                            btn.addEventListener('click', () => {
                                alert(`End of the month is: ${getEndOfMonth()}th`);
                            });
                        }
                    });
                }
            }
    // Function to set current time on page load
    function setCurrentTime() {
        const timeInput = document.getElementById('timeInput');
        const now = new Date();
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        timeInput.value = `${hours}:${minutes}`;
    }
    
    // Call the function when the page loads
    window.onload = setCurrentTime;
    
            // Add Event Listeners
            dailyBtn.addEventListener('click', () => handleModeSelection(dailyBtn));
            weeklyBtn.addEventListener('click', () => handleModeSelection(weeklyBtn));
            monthlyBtn.addEventListener('click', () => handleModeSelection(monthlyBtn));
        </script>

@else
    <div class="container mt-5">
        <div class="card">
            <div class="card-body p-4">
                <div class="page-error">
                    <div class="page-inner">
                        <h1>404</h1>
                        <div class="page-description">
                            {{ __('Page Not Found') }}
                        </div>
                        <div class="page-search">
                            <p class="text-muted mt-3">{{ __("It's looking like you may have taken a wrong turn. Don't worry... it happens to the best of us. Here's a little tip that might help you get back on track.")}}</p>
                            <div class="mt-3">
                                <a class="btn-return-home badge-blue" href="{{route('home')}}"><i class="fas fa-reply"></i> {{ __('Return Home')}}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif


<script>
    $(function(){
        $("#submit").click(function() {
            var user =  $("#assign_to option:selected").length;
            if(user == 0){
            $('#user_validation').removeClass('d-none')
                return false;
            }else{
            $('#user_validation').addClass('d-none')
            }
        });
       
    });
</script>

<script>
(function () {
  function getOpenModal() {
    return document.querySelector('.modal.show');
  }

  function isOutsideModal(modal, target) {
    if (!modal) return false;
    const dialog = modal.querySelector('.modal-dialog');
    if (!dialog) return false;
    return !dialog.contains(target);
  }

  function showWarning(modal) {
    const warning = modal.querySelector('#modal-warning');
    if (warning) {
      warning.classList.remove('d-none');
    }
  }

  function backdropClickHandler(e) {
    const modal = getOpenModal();
    if (!modal) return;              
    if (!isOutsideModal(modal, e.target)) return;

    e.preventDefault();
    e.stopImmediatePropagation();

    // Show alert inside modal instead of confirm()
    showWarning(modal);
  }

  function escHandler(e) {
    if (e.key !== 'Escape') return;
    const modal = getOpenModal();
    if (!modal) return;

    e.preventDefault();
    e.stopImmediatePropagation();

    showWarning(modal);
  }

  document.addEventListener('mousedown', backdropClickHandler, true);
  document.addEventListener('click', backdropClickHandler, true);
  document.addEventListener('keydown', escHandler, true);

})();
</script>


<!--Rupak Add this code for reopen form -->
<script>
    document.getElementById('file-upload').addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (file) {
            const fileName = document.getElementById('file-name');
            fileName.textContent = file.name;
        }
    });
</script>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const taskForm = document.querySelector('form.needs-validation');
    const toggle = document.getElementById('splitTasksToggle');

    taskForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Validate form first
        if (!validateForm()) return;

        const assignees = Array.from(document.getElementById('assign_to').selectedOptions).map(o => o.value);
        const shouldSplit = toggle.checked;

        try {
            let redirectUrl = null;
            if (shouldSplit) {
                redirectUrl = await createIndividualTasks(assignees);
            } else {
                redirectUrl = await createGroupedTask(assignees);
            }
            // Redirect to task list with is_add_enable parameter if redirect URL is provided
            if (redirectUrl) {
                window.location.href = redirectUrl;
            } else {
                // Fallback: redirect to task list with parameter
                window.location.href = "{{ route('projecttask.list', ['is_add_enable' => 'true']) }}";
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Task creation failed. Please try again.');
        }
    });

    function validateForm() {
        // Validate assignees
        const assignees = document.getElementById('assign_to').selectedOptions;
        if (assignees.length === 0) {
            $('#user_validation').removeClass('d-none');
            return false;
        }
        $('#user_validation').addClass('d-none');

        // Validate ETC
        const etaTime = parseInt($('#eta_time').val());
        if (isNaN(etaTime)) {
            alert('ETC must be a number');
            $('#eta_time').focus();
            return false;
        }

        return true;
    }

    async function createGroupedTask(assignees) {
        const formData = new FormData(taskForm);
        
        // Clear and set all assignees
        formData.delete('assign_to');
        assignees.forEach(assignee => {
            formData.append('assign_to[]', assignee);
        });

        const response = await submitTask(formData);
        if (response.success) {
            alert('Task created with all assignees!');
            return response.redirect_url;
        }
        return null;
    }

    async function createIndividualTasks(assignees) {
        const baseFormData = new FormData(taskForm);
        baseFormData.delete('assign_to[]');
        
        const requests = assignees.map(assignee => {
            const taskData = new FormData();
            
            // Copy all fields
            for (let [key, value] of baseFormData.entries()) {
                taskData.append(key, value);
            }
            
            // Set single assignee
            taskData.append('assign_to[]', assignee);
            
            return submitTask(taskData);
        });

        const responses = await Promise.all(requests);
        alert(`${assignees.length} individual tasks created!`);
        // Return redirect URL from the last response
        if (responses.length > 0 && responses[responses.length - 1].success) {
            return responses[responses.length - 1].redirect_url;
        }
        return null;
    }

    async function submitTask(formData) {
        const response = await fetch(taskForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        return await response.json();
    }
});
</script>


<!--all members script -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const assignToSelect = document.getElementById('assign_to');
    
    assignToSelect.addEventListener('change', function() {
        const options = Array.from(this.selectedOptions);
        const hasAllMembers = options.some(opt => opt.value === 'all_members');
        
        if (hasAllMembers) {
            // Unselect all other options when "All Members" is selected
            Array.from(this.options).forEach(opt => {
                if (opt.value !== 'all_members') {
                    opt.selected = false;
                }
            });
            
            // Select all member options except "All Members"
            Array.from(this.options).forEach(opt => {
                if (opt.value !== 'all_members' && opt.value !== '') {
                    opt.selected = true;
                }
            });
        }
    });
    
    // Form submission handling
    const taskForm = document.querySelector('form');
    taskForm.addEventListener('submit', function(e) {
        const assignToOptions = Array.from(assignToSelect.selectedOptions);
        const hasAllMembers = assignToOptions.some(opt => opt.value === 'all_members');
        
        if (hasAllMembers) {
            e.preventDefault();
            
            // Get all member emails except "All Members" option
            const allMemberEmails = Array.from(assignToSelect.options)
                .filter(opt => opt.value !== 'all_members' && opt.value !== '')
                .map(opt => opt.value);
            
            // Submit form for each member using Promise.all to wait for all requests
            const requests = allMemberEmails.map(email => {
                const formData = new FormData(taskForm);
                
                // Set single assignee
                formData.delete('assign_to[]');
                formData.append('assign_to[]', email);
                
                // Submit individual task
                return fetch(taskForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                });
            });
            
            // Wait for all requests to complete, then redirect
            Promise.all(requests).then(results => {
                const allSuccess = results.every(data => data.success);
                if (allSuccess) {
                    alert(`${allMemberEmails.length} tasks created successfully!`);
                    // Use redirect_url from the last response, or fallback to task list with is_add_enable
                    const lastResponse = results[results.length - 1];
                    const redirectUrl = lastResponse.redirect_url || "{{ route('projecttask.list', ['is_add_enable' => 'true']) }}";
                    window.location.href = redirectUrl;
                } else {
                    alert('Some tasks failed to create. Please try again.');
                }
            }).catch(error => {
                console.error('Error:', error);
                alert('Error creating tasks. Please try again.');
            });
        }
        
        // Original validation checks
        const user = $("#assign_to option:selected").length;
        if (user == 0) {
            e.preventDefault();
            $('#user_validation').removeClass('d-none');
            return false;
        } else {
            $('#user_validation').addClass('d-none');
        }
        
        const etaTime = parseInt($('#eta_time').val());
        if (isNaN(etaTime)) {
            e.preventDefault();
            alert('ETC (Min) must be a number');
            $('#eta_time').focus();
            return false;
        }
    });
});
</script>


<!--all members script -->

<!--AUTOMATION SCRIPT-->





