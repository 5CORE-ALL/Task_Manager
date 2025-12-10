@if($currentWorkspace && $task)
<style>
    .round-btn {
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 5px;
        background-color: #f0f0f0;
        cursor: pointer;
    }

    .round-btn.active {
        background-color: #007bff;
        color: #fff;
    }

    .time-picker {
        margin-top: 20px;
    }
</style>
        {{ Form::model($task, array('route' => array('automate.tasks.update',[$task->id]), 'method' => 'Post','class'=>'needs-validation updatesubmit')) }}
        @csrf
        <div class="modal-body">
            <div class="text-end">
                @if (module_is_active('AIAssistant'))
                    @include('aiassistant::ai.generate_ai_btn',['template_module' => 'project task','module'=>'Taskly'])
                @endif
            </div>
            <div class="row">
                 
                <div class="form-group col-md-12">
                    <label class="form-label">{{ __('Group')}}</label>
                   
                    <input type="text" class="form-control form-control-light" name="group" id="task-group" placeholder="{{ __('Enter Group')}}"  value="{{$task->group}}">

                </div>
                
                <div class="form-group col-md-6">
                    <label class="form-label">{{ __('Title')}}</label><x-required></x-required>
                    <input type="text" class="form-control form-control-light" id="task-title" placeholder="{{ __('Enter Title')}}" name="title" required value="{{$task->title}}">
                </div>
                <div class="form-group col-md-6">
                    <label class="form-label">{{ __('Priority')}}</label><x-required></x-required>
                    <select class="form-control form-control-light select2" name="priority" id="task-priority" required>
                        <option value="Low" @if($task->priority=='Low') selected @endif>{{ __('Low')}}</option>
                        <option value="Medium" @if($task->priority=='Medium') selected @endif>{{ __('Medium')}}</option>
                        <option value="High" @if($task->priority=='High') selected @endif>{{ __('High')}}</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label class="form-label">{{ __('Assignor')}}</label><x-required></x-required>

                    <select class=" multi-select choices" id="assignor" name="assignor" data-placeholder="{{ __('Select Users ...') }}" required>
                        @foreach($users as $ur)
                            <option value="{{$ur->email}}"  @if($ur->email==$task->assignor) selected @endif >{{$ur->name}}</option>
                        @endforeach
                    </select>
                    <p class="text-danger d-none" id="user_validation">{{__('Assignor To filed is required.')}}</p>
                </div>
                 <div class="form-group col-md-6">
                    <label class="form-label">{{ __('Status')}}</label>
                    <select class="form-control form-control-light select2" name="stage_id" id="task-stage">
                        <option value="">{{__('Select Status')}}</option>
                        @foreach($stages as $stage)
                            <option value="{{$stage->name}}" @if($task->status == $stage->name) selected @endif>{{$stage->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label class="form-label">{{ __('Assign To')}}</label><x-required></x-required>
                    <select class="multi-select" multiple="multiple" id="assign_to" name="assign_to[]" required>
                        @foreach($users as $u)
                            <option @if(in_array($u->email,$task->assign_to)) selected @endif value="{{$u->email}}">{{$u->name}}</option>
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
                  <div class="form-group col-md-6">
                      <label class="form-label">{{ __('ETC (Min)')}}</label><x-required></x-required>
                    <input type="number" class="form-control form-control-light" id="eta_time" placeholder="{{ __('Enter ETA Time')}}" name="eta_time" value="{{$task->eta_time}}" required 
                           oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 4);">
                </div>
                 <div class="form-group col-md-6">
                        <label class="form-label">{{ __('Duration')}}</label>
                        <div class='input-group'>
                                <input type='text' class=" form-control pc-daterangepicker-3" id="duration" name="duration" value="{{__('Select Date Range')}}"
                                    placeholder="Select date range" 
                                    
                                    />
                                    <input type="hidden" name="start_date"  id="start_date1">
                                    <input type="hidden" name="due_date" id="end_date1">
                                    <span class="input-group-text"><i
                                        class="feather icon-calendar"></i></span>
                            </div>
                </div>

                <div class="form-group col-md-6">
                    <label class="form-label">{{ __('Description')}}</label><x-required></x-required>
                    <textarea class="form-control form-control-light" id="task-description" rows="3" name="description">{{$task->description}}</textarea>
                </div>
                <div class="form-group col-md-6">
                    <label class="form-label">{{ __('L1')}}</label>
                    <input type="text" class="form-control form-control-light" id="link1" placeholder="{{ __('Enter L1')}}" name="link1" value="{{$task->link1}}" >
                </div>
                <div class="form-group col-md-6">
                    <label class="form-label">{{ __('L2')}}</label>
                    <input type="text" class="form-control form-control-light" id="link2" placeholder="{{ __('Enter L2')}}" name="link2" value="{{$task->link2}}"  >
                </div>
                <div class="form-group col-md-6">
                    <label class="form-label">{{ __('Training Link')}}</label>
                    <input type="text" class="form-control form-control-light" id="link3" placeholder="{{ __('Enter Training Note')}}" name="link3" value="{{$task->link3}}"  >
                </div>
                <div class="form-group col-md-6">
                    <label class="form-label">{{ __('Checklist Link')}}</label>
                    <input type="text" class="form-control form-control-light" id="link6" placeholder="{{ __('Enter checklist link')}}" name="link6" value="{{$task->link6}}"  >
                </div>
                   <div class="form-group col-md-6">
                    <label class="form-label">{{ __('Form Link')}}</label>
                    <input type="text" class="form-control form-control-light" id="link5" placeholder="{{ __('Enter form Note')}}" name="link5" value="{{$task->link5}}">
                </div>
                <div class="form-group col-md-6">
                    <label class="form-label">{{ __('Form Report Link')}}</label>
                    <input type="text" class="form-control form-control-light" id="link7" placeholder="{{ __('Enter form Report Link')}}" name="link7" value="{{$task->link7}}">
                </div>
                <div class="d-flex justify-content-center mb-4">
                    <button class="btn btn-primary mx-2 mode-btn" type="button" id="daily"  @if($task->schedule_type=="daily") disabled @endif>D</button>
                    <button class="btn btn-primary mx-2 mode-btn" type="button" id="weekly"  @if($task->schedule_type=="weekly") disabled @endif>W</button>
                    <button class="btn btn-primary mx-2 mode-btn" type="button" id="monthly" @if($task->schedule_type=="monthly") disabled @endif>M</button>
                </div>
                <input type="hidden" class="form-control form-control-light" id="schedule_type" placeholder="{{ __('Enter L2')}}" value="{{$task->schedule_type}}" name="schedule_type">
                <input type="hidden" class="form-control form-control-light" id="schedule_days" placeholder="{{ __('Enter L2')}}" value="{{ str_replace('"', '', $task->schedule_days)}}" name="schedule_days">
            <!-- Dynamic Buttons Section -->
            <section>
                <div class="container">
                    <div class="row">
                        <div class="col-md-1">
    
                        </div>
                        <div class="col-md-10">
                            <div id="buttons-container" class="d-flex flex-wrap justify-content-center"></div>
                            <div id="time-picker" class="time-picker d-none">
                                <label for="timeInput" class="form-label">Select Time:</label>
                                <input type="time" id="schedule_time" name="schedule_time" class="form-control" value="{{$task->schedule_time}}" required>
                            </div>
                        </div>
                        <div class="col-md-1">
                            
                        </div>
                    </div>
                </div>
            </section>
    
                @if(module_is_active('CustomField') && !$customFields->isEmpty())
                <div class="col-md-12">
                    <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                        @include('custom-field::formBuilder')
                    </div>
                </div>
            @endif
            </div>
        </div>
         <div class="modal-footer">
          <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Cancel')}}</button>
          <input type="submit" value="{{ __('Update')}}" id="submit" class="btn btn-primary">
        </div>
        {{ Form::close() }} 
    <link rel="stylesheet" href="{{ asset('packages/workdo/Taskly/src/Resources/assets/libs/bootstrap-daterangepicker/daterangepicker.css')}} ">

    <script src="{{ asset('packages/workdo/Taskly/src/Resources/assets/libs/moment/min/moment.min.js')}}"></script>
    <script src="{{ asset('packages/workdo/Taskly/src/Resources/assets/libs/bootstrap-daterangepicker/daterangepicker.js')}}"></script>


    <!-- data-picker -->

    <script>

            if ($(".multi-select").length > 0) {
            $( $(".multi-select") ).each(function( index,element ) {
                var id = $(element).attr('id');
                   var multipleCancelButton = new Choices(
                        '#'+id, {
                            removeItemButton: true,
                        }
                    );
            });
       }
        $(function () {
            var start = moment('{{$task->start_date}}', 'YYYY-MM-DD HH:mm:ss');
            var end = moment('{{$task->due_date}}', 'YYYY-MM-DD HH:mm:ss');

            function cb(start, end) {
                $("form #duration").val(start.format('MMM D, YY hh:mm A') + ' - ' + end.format('MMM D, YY hh:mm A'));
                $('form input[name="start_date"]').val(start.format('YYYY-MM-DD HH:mm:ss'));
                $('form input[name="due_date"]').val(end.format('YYYY-MM-DD HH:mm:ss'));
            }

            $('form #duration').daterangepicker({
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
    </script>
    
    <script>
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
            });
        })
    </script>
     <script>
        var dailyBtn = document.getElementById('daily');
        var weeklyBtn = document.getElementById('weekly');
        var monthlyBtn = document.getElementById('monthly');
        var buttonsContainer = document.getElementById('buttons-container');
        var timePicker = document.getElementById('time-picker');

        // Days and Months Data
        var daysEdit = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        var monthDays = Array.from({
            length: 31
        }, (_, i) => i + 1);


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
                btn.setAttribute('onclick', 'handleClick()');
                if (selectable) {
                    btn.addEventListener('click', () => {
                        btn.classList.toggle('active');
                    });
                }
                buttonsContainer.appendChild(btn);
            });
        }

        function handleClick() {
            var activeValues =[];

            setTimeout(() => {
                $("#buttons-container .round-btn.active").each(function() {
                  activeValues.push($(this).text().trim());// Get the text inside the active buttons
                });
                console.log("activeValues", activeValues); // Output the active button values
                $("#schedule_days").val(activeValues);
            }, 100);

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
            $("#schedule_type").val(mode.id);
            if (mode.id === 'daily') {
                generateButtons(daysEdit, false); // Pre-selected buttons
            } else if (mode.id === 'weekly') {
                generateButtons(daysEdit, true); // Selectable buttons
            } else if (mode.id === 'monthly') {
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
            const timeInput = document.getElementById('schedule_time');
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
        
        $(document).ready(function () {
            var schedule_type = $("#schedule_type").val();
            console.log("Initial schedule type:", schedule_type);
            
            // Initialize the correct mode based on the existing value
            if (schedule_type === 'daily') {
                dailyBtn.disabled = true;
                generateButtons(daysEdit, false);
                timePicker.classList.remove('d-none');
            } 
            else if (schedule_type === 'weekly') {
                weeklyBtn.disabled = true;
                // Get the saved days and mark them as active
                var savedDays = $("#schedule_days").val();
                if (savedDays) {
                    savedDays = savedDays.split(',');
                    generateButtons(daysEdit, true);
                    // Mark saved days as active
                    $(".round-btn").each(function() {
                        if (savedDays.includes($(this).text().trim())) {
                            $(this).addClass('active');
                        }
                    });
                } else {
                    generateButtons(daysEdit, true);
                }
                timePicker.classList.remove('d-none');
            } 
            else if (schedule_type === 'monthly') {
                monthlyBtn.disabled = true;
                // Get the saved days and mark them as active
                var savedDays = $("#schedule_days").val();
                const customLabels = monthDays.concat('End of Month');
                if (savedDays) {
                    savedDays = savedDays.split(',');
                    generateButtons(customLabels, true);
                    // Mark saved days as active
                    $(".round-btn").each(function() {
                        if (savedDays.includes($(this).text().trim())) {
                            $(this).addClass('active');
                        }
                    });
                } else {
                    generateButtons(customLabels, true);
                }
                timePicker.classList.remove('d-none');
            }
        });
    });
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
