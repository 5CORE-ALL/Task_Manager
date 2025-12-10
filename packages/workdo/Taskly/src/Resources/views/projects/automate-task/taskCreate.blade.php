@if ($currentWorkspace)
    <form class="needs-validation dfsdfdfsd" method="post" action="{{ route('automate.tasks.save') }}" novalidate>
        @csrf
        <div class="modal-body">
            <div class="text-end">
                @if (module_is_active('AIAssistant'))
                    @include('aiassistant::ai.generate_ai_btn', [
                        'template_module' => 'project task',
                        'module' => 'Taskly',
                    ])
                @endif
            </div>
            <div class="row">

                <div class="form-group col-md-12">
                    <label class="form-label">{{ __('Group') }}</label>
                    <input type="text" class="form-control form-control-light" name="group" id="task-group"
                        placeholder="{{ __('Enter Group') }}" value="">

                </div>
                <div class="form-group col-md-6">
                    <label class="form-label">{{ __('Title') }}</label><x-required></x-required>
                    <input type="text" class="form-control form-control-light" id="task-title"
                        placeholder="{{ __('Enter Title') }}" name="title" required>
                </div>
                <div class="form-group col-md-6">
                    <label class="form-label">{{ __('Priority') }}</label>
                    <select class="form-control form-control-light" name="priority" id="task-priority" required>
                        <option value="Low">{{ __('Low') }}</option>
                        <option value="Medium">{{ __('Medium') }}</option>
                        <option value="High">{{ __('High') }}</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label class="form-label">{{ __('Assignor') }}</label><x-required></x-required>

                    <select class=" multi-select choices" id="assignor" name="assignor" multiple="multiple"
                        data-placeholder="{{ __('Select Users ...') }}" required>
                        @foreach ($users as $u)
                            <option value="{{ $u->email }}" @if ($u->email == auth()->user()->email) selected @endif>
                                {{ formatUserName($u->name) }}</option>
                                 <!--- {{ $u->email }} - {{ $u->mobile_no }}-->
                        @endforeach
                    </select>
                    <p class="text-danger d-none" id="user_validation">{{ __('Assign To filed is required.') }}</p>
                </div>
                <div class="form-group col-md-6">
                    <label class="form-label">{{ __('Status') }}</label>
                    <select class="form-control form-control-light" name="stage_id" id="task-stage">
                        <option value="">{{ __('Select Status') }}</option>
                        @foreach ($stages as $stage)
                            <option value="{{ $stage->name }}" data-color="{{ $stage->color }}"> {{ $stage->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label class="form-label">{{ __('Assign To') }}</label><x-required></x-required>
                
                    <select class=" multi-select choices" id="assign_to" name="assign_to[]" multiple="multiple"
                        data-placeholder="{{ __('Select Users ...') }}" required>
                        <option value="all_members">{{ __('All Members') }}</option>
                        <option value="all_managers">{{ __('All Managers') }}</option>
                        @foreach ($users as $u)
                            <option value="{{ $u->email }}">{{ formatUserName($u->name) }}</option>
                        @endforeach
                    </select>
                    <p class="text-danger d-none" id="user_validation">{{ __('Assign To filed is required.') }}</p>
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
                    <input type="number" class="form-control form-control-light" id="eta_time" placeholder="{{ __('Enter ETA Time')}}" name="eta_time" required 
                           oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 4);">
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
                    <label class="form-label">{{ __('L1') }}</label>
                    <input type="text" class="form-control form-control-light" id="link1"
                        placeholder="{{ __('Enter L1') }}" name="link1">
                </div>
                <div class="form-group col-md-6">
                    <label class="form-label">{{ __('L2') }}</label>
                    <input type="text" class="form-control form-control-light" id="link2"
                        placeholder="{{ __('Enter L2') }}" name="link2">
                </div>
                <div class="form-group col-md-6">
                    <label class="form-label">{{ __('Training Link') }}</label>
                    <input type="text" class="form-control form-control-light" id="link3"
                        placeholder="{{ __('Enter Training Note') }}" name="link3">
                </div>
                <div class="form-group col-md-6">
                    <label class="form-label">{{ __('Video Link')}}</label>
                    <input type="text" class="form-control form-control-light" id="link4" placeholder="{{ __('Enter video Note')}}" name="link4">
                </div>
                <div class="form-group col-md-6">
                    <label class="form-label">{{ __('Form Link')}}</label>
                    <input type="text" class="form-control form-control-light" id="link5" placeholder="{{ __('Enter form Note')}}" name="link5">
                </div>
                <div class="form-group col-md-6">
                    <label class="form-label">{{ __('Form Report Link')}}</label>
                    <input type="text" class="form-control form-control-light" id="link7" placeholder="{{ __('Enter form Report Link')}}" name="link7">
                </div>
                 <div class="form-group col-md-6">
                    <label class="form-label">{{ __('Checklist Link')}}</label>
                    <input type="text" class="form-control form-control-light" id="link6" placeholder="{{ __('Enter checklist link')}}" name="link6">
                </div>
                 <div class="form-group col-md-6">
                    <label class="form-label">{{ __('Image') }}</label>
                    <div class="image-upload-preview"
                        style="text-align: center; border: 1px dashed #ccc; padding: 10px; border-radius: 10px;">
                        <span id="file-name" style="display: block; margin: 0 auto;"></span>
                        <label for="file-upload" style="cursor: pointer; display: block; margin-top: 10px;"
                            class="btn btn-sm btn-primary">
                            {{ __('Choose File') }}
                        </label>
                        <input type="file" id="file-upload" name="file" accept="image/*,application/pdf"
                            style="display: none;" />
                    </div>
                </div>
                <div class="form-group col-md-12">
                    <label class="form-label">{{ __('Description') }}</label>
                    <textarea class="form-control form-control-light" id="task-description" rows="3" name="description"
                        placeholder="Enter Description"></textarea>
                </div>
               

                <!--AUTOMATION DESIGN CODE -->

                <div class="d-flex justify-content-center mb-4">
                    <button class="btn btn-primary mx-2 mode-btn" type="button" id="daily">D</button>
                    <button class="btn btn-primary mx-2 mode-btn" type="button" id="weekly">W</button>
                    <button class="btn btn-primary mx-2 mode-btn" type="button" id="monthly">M</button>
                </div>
                <input type="hidden" class="form-control form-control-light" id="schedule_type"
                    placeholder="{{ __('Enter L2') }}" name="schedule_type">
                <input type="hidden" class="form-control form-control-light" id="schedule_days"
                    placeholder="{{ __('Enter L2') }}" name="schedule_days">
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
                                  <input type="time" id="schedule_time" name="schedule_time"
                                       class="form-control" value="00:01" required>
                                </div>
                            </div>
                            <div class="col-md-1">

                            </div>
                        </div>
                    </div>
                </section>

                <!-- Time Picker -->


                <!--END AUTOMATION DESIGN CODE -->
            </div>


        </div>
        @if (module_is_active('CustomField') && !$customFields->isEmpty())
            <div class="col-md-12">
                <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                    @include('custom-field::formBuilder')
                </div>
            </div>
        @endif
        @stack('calendar')
        </div>
        <div class="modal-footer">
            <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
            <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary" id="submit">
        </div>
    </form>
    <link rel="stylesheet"
        href="{{ asset('packages/workdo/Taskly/src/Resources/assets/libs/bootstrap-daterangepicker/daterangepicker.css') }} ">
    <script src="{{ asset('packages/workdo/Taskly/src/Resources/assets/libs/moment/min/moment.min.js') }}"></script>
    <script
        src="{{ asset('packages/workdo/Taskly/src/Resources/assets/libs/bootstrap-daterangepicker/daterangepicker.js') }}">
    </script>

    <script>
        $(function() {
    // Set start to today at 00:00
    var start = moment().startOf('day'); 
    // Set end to tomorrow at 00:00 (ensures different date)
    var end = moment().startOf('day').add(1, 'day');

    function cb(start, end) {
        // Format display with full date range
        $("form #duration").val(
            start.format('MMM D, YY hh:mm A') + ' - ' + 
            end.format('MMM D, YY hh:mm A')
        );
        
        // Store full datetime values
        $('form input[name="start_date"]').val(start.format('YYYY-MM-DD HH:mm:ss'));
        $('form input[name="due_date"]').val(end.format('YYYY-MM-DD HH:mm:ss'));
        
        console.log("Start:", start.format('YYYY-MM-DD HH:mm'));
        console.log("End:", end.format('YYYY-MM-DD HH:mm'));
    }

            $('form #duration').daterangepicker({
                autoApply: true,
                timePicker: true,
                autoUpdateInput: false,
                startDate: start,
                endDate: end,
                locale: {
                    format: 'MMMM D, YYYY hh:mm A',
                    applyLabel: "{{ __('Apply') }}",
                    cancelLabel: "{{ __('Cancel') }}",
                    fromLabel: "{{ __('From') }}",
                    toLabel: "{{ __('To') }}",
                    daysOfWeek: [
                        "{{ __('Sun') }}",
                        "{{ __('Mon') }}",
                        "{{ __('Tue') }}",
                        "{{ __('Wed') }}",
                        "{{ __('Thu') }}",
                        "{{ __('Fri') }}",
                        "{{ __('Sat') }}"
                    ],
                    monthNames: [
                        "{{ __('January') }}",
                        "{{ __('February') }}",
                        "{{ __('March') }}",
                        "{{ __('April') }}",
                        "{{ __('May') }}",
                        "{{ __('June') }}",
                        "{{ __('July') }}",
                        "{{ __('August') }}",
                        "{{ __('September') }}",
                        "{{ __('October') }}",
                        "{{ __('November') }}",
                        "{{ __('December') }}"
                    ],
                }
            }, cb);

            cb(start, end);
                // For due date coloring (this would be in your task display logic)
    function checkDueDate(taskEndDate) {
        const now = moment();
        const dueDate = moment(taskEndDate);
        const isOverdue = dueDate.isBefore(now, 'day');
        
        if (isOverdue) {
            return 'red'; // Apply red color for overdue tasks
        }
        return ''; // Default color
    }
        });
        $(document).on('change', "select[name=project_id]", function() {
            $.get('@auth('web'){{ route('home') }}@elseauth{{ route('client.home') }}@endauth' +
                '/userProjectJson/' + $(this).val(),
                function(data) {
                    $('select[name=assign_to]').html('');
                    data = JSON.parse(data);
                    $(data).each(function(i, d) {
                        $('select[name=assign_to]').append('<option value="' + d.id + '">' + d.name +
                            ' - ' + d.email + '</option>');
                    });
                });
            $.get('@auth('web'){{ route('home') }}@elseauth{{ route('client.home') }}@endauth' +
                '/projectMilestoneJson/' + $(this).val(),
                function(data) {
                    $('select[name=milestone_id]').html(
                        '<option value="">{{ __('Select Milestone') }}</option>');
                    data = JSON.parse(data);
                    $(data).each(function(i, d) {
                        $('select[name=milestone_id]').append('<option value="' + d.id + '">' + d
                            .title + '</option>');
                    });
                })
        })
    </script>
    <script>
        
        var dailyBtn = document.getElementById('daily');
        var weeklyBtn = document.getElementById('weekly');
        var monthlyBtn = document.getElementById('monthly');
        var buttonsContainer = document.getElementById('buttons-container');
        var timePicker = document.getElementById('time-picker');

        // Days and Months Data
        var days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
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
            var activeValues = [];

            setTimeout(() => {
                $("#buttons-container .round-btn.active").each(function() {
                    activeValues.push($(this).text().trim()); // Get the text inside the active buttons
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
                generateButtons(days, false); // Pre-selected buttons
            } else if (mode.id === 'weekly') {
                generateButtons(days, true); // Selectable buttons
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
                            <p class="text-muted mt-3">
                                {{ __("It's looking like you may have taken a wrong turn. Don't worry... it happens to the best of us. Here's a little tip that might help you get back on track.") }}
                            </p>
                            <div class="mt-3">
                                <a class="btn-return-home badge-blue" href="{{ route('home') }}"><i
                                        class="fas fa-reply"></i> {{ __('Return Home') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif


<script>
    $(function() {
        $("#submit").click(function() {
            var user = $("#assign_to option:selected").length;
            if (user == 0) {
                $('#user_validation').removeClass('d-none')
                return false;
            } else {
                $('#user_validation').addClass('d-none')
            }
        });

    });
</script>
<script>
    document.getElementById('file-upload').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const fileName = document.getElementById('file-name');
            fileName.textContent = file.name;
        }
    });
</script>


<!--AUTOMATION SCRIPT-->
