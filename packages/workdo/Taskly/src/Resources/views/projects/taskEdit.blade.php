<style>
    #etcModal {
  z-index: 2000 !important;
}
</style>
@if($currentWorkspace && $task)
        {{ Form::model($task, array('route' => array('tasks.update',[$task->id]), 'method' => 'Post','class'=>'needs-validation updatesubmit','novalidate','enctype' => 'multipart/form-data')) }}
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
                <!--<div class="form-group col-md-6">-->
                <!--    <label class="form-label">{{ __('Title')}}</label><x-required></x-required>-->
                <!--    <input -->
                <!--        type="text" -->
                <!--        class="form-control form-control-light" -->
                <!--        id="task-title" -->
                <!--        placeholder="{{ __('Enter Title')}}" -->
                <!--        name="title" -->
                <!--        required -->
                <!--        value="{{$task->title}}" -->
                <!--        @if(in_array(Auth::user()->email, $task->assign_to)) disabled @endif-->
                <!--    >-->
                <!--</div>-->

                <div class="form-group col-md-6">
                    <label class="form-label">{{ __('Priority')}}</label><x-required></x-required>
                    <select class="form-control form-control-light select2" name="priority" id="task-priority" required>
                        <option value="normal" @if($task->priority=='normal') selected @endif>{{ __('normal')}}</option>
                        <option value="urgent" @if($task->priority=='urgent') selected @endif>{{ __('urgent')}}</option>
                        <option value="Take your time" @if($task->priority=='Take your time') selected @endif>{{ __('Take your time')}}</option>
                    </select>
                </div>
                <!--<div class="form-group col-md-12">-->
                <!--    <label class="form-label">{{ __('Assignor')}}</label><x-required></x-required>-->

                <!--    <select class=" multi-select choices" id="assignor" name="assignor" data-placeholder="{{ __('Select Users ...') }}" required>-->
                <!--        @foreach($users as $ur)-->
                <!--            <option value="{{$ur->id}}"  @if($ur->id==$task->assignor) selected @endif >{{$ur->name}} - {{$ur->email}} - {{$ur->mobile_no}}</option>-->
                <!--        @endforeach-->
                <!--    </select>-->
                <!--    <p class="text-danger d-none" id="user_validation">{{__('Assignor To filed is required.')}}</p>-->
                <!--</div>-->
                
                <div class="form-group col-md-6">
                    <label class="form-label">{{ __('Assignor')}}</label><x-required></x-required>
                
                    <select class="multi-select choices" id="assignor" multiple="multiple" name="assignor[]" data-placeholder="{{ __('Select Users ...') }}" required>
                        @php
                            $existingAssignors = $task->assignor ? array_filter(array_map('trim', explode(',', $task->assignor))) : [];
                        @endphp
                        @foreach($users as $ur)
                            <option value="{{$ur->email}}" @if(in_array($ur->email, $existingAssignors)) selected @endif>{{ formatUserName($ur->name) }}</option>
                        @endforeach
                    </select>
                    <p class="text-danger d-none" id="user_validation">{{__('Assignor field is required.')}}</p>
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
                            <option @if(in_array($u->email,$task->assign_to)) selected @endif value="{{$u->email}}">{{ formatUserName($u->name) }}</option>
                        @endforeach
                    </select>
                    <p class="text-danger d-none" id="user_validation">{{__('Assign To filed is required.')}}</p>

                </div>
                            <div class="form-group col-md-6">
                <label class="form-label">{{ __('Task Distribution') }}</label>
                <div class="d-flex align-items-center">
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="splitTasksToggle" {{$task->split_tasks ? "checked" :""}} name="split_tasks" >
                        <label class="form-check-label ms-2" for="splitTasksToggle">{{ __('Split tasks between assignees') }}</label>
                    </div>
                </div>
                <small class="text-muted">{{ __('When enabled, each assignee will get their own copy of this task') }}</small>
            </div>
             
                <div class="form-group col-md-6">
                        <label class="form-label">{{ __('Duration')}}</label>
                        <div class='input-group'>
                                <input type='text' class=" form-control pc-daterangepicker-3" id="duration" name="duration" value="{{__('Select Date Range')}}"
                                    placeholder="Select date range" 
                                    @if(Auth::user()->email !== 'president@5core.com') disabled title="You do not have permission to update TID (Task Initiation Date)" @endif
                                   />
                                    <input type="hidden" name="start_date"  id="start_date1">
                                    <input type="hidden" name="due_date" id="end_date1">
                                    <span class="input-group-text"><i
                                        class="feather icon-calendar"></i></span>
                            </div>
                            @if(Auth::user()->email !== 'president@5core.com')
                                <small class="text-muted">{{ __('Only authorized users can update TID (Task Initiation Date)') }}</small>
                            @endif
                </div>
   <div class="form-group col-md-6">
                    <label class="form-label">{{ __('L1')}}</label>
                    <input type="text" class="form-control form-control-light" id="link1" placeholder="{{ __('Enter L1')}}" name="link1" value="{{$task->link1}}" >
                </div>
                <div class="form-group col-md-6">
                    <label class="form-label">{{ __('L2')}}</label>
                    <input type="text" class="form-control form-control-light" id="link2" placeholder="{{ __('Enter L2')}}" name="link2"  value="{{$task->link2}}"  >
                </div>
                
                <!--<div class="form-group col-md-6">-->
                <!--    <label class="form-label">{{ __('ETC (Min)')}}</label>-->
                    
                <!--    <input type="text" class="form-control form-control-light" id="eta_time" required placeholder="{{ __('Enter ETA Time')}}" name="eta_time"  value="{{$task->eta_time}}">-->
                <!--</div>-->
                <div class="form-group col-md-6">
                    <label class="form-label">{{ __('ETC (Min)')}}</label>
                    
                    <input type="text" 
                           class="form-control form-control-light" 
                           id="eta_time" 
                           required 
                           placeholder="{{ __('Enter ETA Time')}}" 
                           name="eta_time"  
                           value="{{$task->eta_time ?? 10}}"
                           @if(Auth::user()->email != $task->assignor && in_array(Auth::user()->email, $task->assign_to)) disabled @endif>
                </div>

                 <div class="form-group col-md-4">
                    <label class="form-label">{{ __('Training Link')}}</label>
                    <input type="text" class="form-control form-control-light" id="link3" placeholder="{{ __('Enter Training Note')}}" name="link3"  value="{{$task->link3}}"  >
                </div>
                 <div class="form-group col-md-4">
                    <label class="form-label">{{ __('Checklist Link')}}</label>
                    <input type="text" class="form-control form-control-light" id="link6" placeholder="{{ __('Enter checklist link')}}" name="link6"  value="{{$task->link6}}"  >
                </div>
                  <div class="form-group col-md-4">
                    <label class="form-label">{{ __('Form Link')}}</label>
                    <input type="text" class="form-control form-control-light" id="link5" placeholder="{{ __('Enter form Note')}}" name="link5" value="{{$task->link5}}">
                </div>
                <div class="form-group col-md-4">
                    <label class="form-label">{{ __('Form Report Link')}}</label>
                    <input type="text" class="form-control form-control-light" id="link7" placeholder="{{ __('Enter form Note')}}" name="link7" value="{{$task->link7}}">
                </div>
                 <div class="form-group col-md-4">
                    <label class="form-label">{{ __('PROCESS')}}</label>
                    <input type="text" class="form-control form-control-light" id="link8" placeholder="{{ __('Enter form Note')}}" name="link8" value="{{$task->link8}}">
                </div>
                 <div class="form-group col-md-4">
                    <label class="form-label">{{ __('PL Link')}}</label>
                    <input type="text" class="form-control form-control-light" id="link9" placeholder="{{ __('Enter PL link')}}" name="link9"  value="{{$task->link9}}"  >
                </div>
                <div class="form-group col-md-12">
                    <label class="form-label">{{ __('Description')}}</label><x-required></x-required>
                    <textarea class="form-control form-control-light" id="task-description" rows="2" name="description">{{$task->description}}</textarea>
                </div>
               <div class="form-group col-md-12">
                <label class="form-label">{{ __('Image') }}</label>
                <div class="image-upload-preview" id="upload-area" style="text-align: center; border: 2px dashed #ccc; padding: 20px; border-radius: 10px; position: relative; transition: all 0.3s ease; min-height: 120px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                    <div id="file-preview" style="margin-bottom: 10px;"></div>
                    <span id="file-name" style="display: block; margin: 5px 0; color: #666;"></span>
                    <div style="margin-top: 10px;">
                        <label for="file-upload" style="cursor: pointer; display: inline-block; margin: 0 5px;" class="btn btn-sm btn-primary">
                            {{ __('Choose File') }}
                        </label>
                        <span style="color: #999; font-size: 12px;">{{ __('or drag & drop, or paste image') }}</span>
                    </div>
                    <input type="file" id="file-upload" name="file" accept="image/*,application/pdf" style="display: none;" />
                </div>
            </div>
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
        <div class="modal fade" id="etcModal" tabindex="-1" aria-labelledby="etcModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form id="etcForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="etcModalLabel">Enter Your ETC (Min)</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="number" class="form-control" id="etcInput" name="etc" min="1" required placeholder="Enter ETC in minutes">
          <input type="hidden" id="etcTaskId" name="task_id">
          <input type="hidden" id="etcTaskOriginal" name="etcTaskOriginal">
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save ETC</button>
        </div>
      </div>
    </form>
  </div>
</div>

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
            // Handle null/empty dates for event tasks
            var startDate = '{{$task->start_date}}';
            var endDate = '{{$task->due_date}}';
            
            var start, end;
            
            // Check if dates are valid, otherwise use current date/time
            if (startDate && startDate !== '' && startDate !== 'null') {
                start = moment(startDate, 'YYYY-MM-DD HH:mm:ss');
                if (!start.isValid()) {
                    start = moment();
                }
            } else {
                start = moment();
            }
            
            if (endDate && endDate !== '' && endDate !== 'null') {
                end = moment(endDate, 'YYYY-MM-DD HH:mm:ss');
                if (!end.isValid()) {
                    end = moment().add(1, 'day');
                }
            } else {
                end = moment().add(1, 'day');
            }

            function cb(start, end) {
                if (start && end && start.isValid() && end.isValid()) {
                    $("form #duration").val(start.format('MMM D, YY hh:mm A') + ' - ' + end.format('MMM D, YY hh:mm A'));
                    $('form input[name="start_date"]').val(start.format('YYYY-MM-DD HH:mm:ss'));
                    $('form input[name="due_date"]').val(end.format('YYYY-MM-DD HH:mm:ss'));
                } else {
                    $("form #duration").val("{{__('Select Date Range')}}");
                }
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

            // Only call cb if we have valid dates
            if (startDate && startDate !== '' && startDate !== 'null' && endDate && endDate !== '' && endDate !== 'null') {
                cb(start, end);
            }
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
    (function() {
        const uploadArea = document.getElementById('upload-area');
        const fileInput = document.getElementById('file-upload');
        const fileName = document.getElementById('file-name');
        const filePreview = document.getElementById('file-preview');

        // Handle file selection
        function handleFile(file) {
            if (!file) return;

            // Validate file type
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];
            if (!validTypes.includes(file.type)) {
                alert('Please select an image file (JPEG, PNG, GIF, WEBP) or PDF');
                return;
            }

            // Update file input
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            fileInput.files = dataTransfer.files;

            // Display file name
            fileName.textContent = file.name;

            // Display preview for images
            filePreview.innerHTML = '';
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.maxWidth = '200px';
                    img.style.maxHeight = '150px';
                    img.style.borderRadius = '5px';
                    img.style.margin = '0 auto';
                    filePreview.appendChild(img);
                };
                reader.readAsDataURL(file);
            } else if (file.type === 'application/pdf') {
                const pdfIcon = document.createElement('div');
                pdfIcon.innerHTML = '<i class="fas fa-file-pdf" style="font-size: 48px; color: #dc3545;"></i>';
                filePreview.appendChild(pdfIcon);
            }
        }

        // File input change event
        fileInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            handleFile(file);
        });

        // Drag and drop events
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            uploadArea.style.borderColor = '#007bff';
            uploadArea.style.backgroundColor = '#f0f8ff';
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            uploadArea.style.borderColor = '#ccc';
            uploadArea.style.backgroundColor = 'transparent';
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            uploadArea.style.borderColor = '#ccc';
            uploadArea.style.backgroundColor = 'transparent';

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFile(files[0]);
            }
        });

        // Paste event (for images from clipboard)
        document.addEventListener('paste', function(e) {
            // Check if the paste happened in the upload area or if no input is focused
            const activeElement = document.activeElement;
            const isTextInput = activeElement && (
                activeElement.tagName === 'INPUT' || 
                activeElement.tagName === 'TEXTAREA' ||
                activeElement.isContentEditable
            );

            if (isTextInput) return; // Don't interfere with normal text pasting

            const items = e.clipboardData.items;
            for (let i = 0; i < items.length; i++) {
                if (items[i].type.indexOf('image') !== -1) {
                    e.preventDefault();
                    const blob = items[i].getAsFile();
                    handleFile(blob);
                    break;
                }
            }
        });
    })();
</script>
<script>
    $(document).ready(function() {
    // Listen for status change in the edit form
    $('#task-stage').on('change', function() {
        var newStatus = $(this).val();
        var taskId = '{{ $task->id }}'; // Get the current task ID
        
        if (newStatus && newStatus.toLowerCase() === 'done') {
            $('#etcTaskId').val(taskId);
            $('#etcInput').val('');
            var etcModal = new bootstrap.Modal(document.getElementById('etcModal'));
            etcModal.show();
            
            // Prevent form submission until ETC is entered
            $('.updatesubmit').on('submit', function(e) {
                if ($('#etcInput').val() === '') {
                    e.preventDefault();
                    toastrs('Error', 'Please enter ETC time before setting status to Done', 'error');
                }
            });
        }
    });
    
    // ETC form submission
    $('#etcForm').on('submit', function(e) {
        e.preventDefault();
        var etc = $('#etcInput').val();
        var taskId = $('#etcTaskId').val();
        
        if (etc === '') {
            toastrs('Error', 'Please enter ETC time', 'error');
            return;
        }
        
        $.ajax({
            url: "{{ route('projecttask.update.etc') }}",
            method: 'POST',
            data: {
                etc: etc,
                task_id: taskId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if(response.is_success) {
                    var etcModal = bootstrap.Modal.getInstance(document.getElementById('etcModal'));
                    etcModal.hide();
                    // Continue with the form submission
                    $('.updatesubmit').off('submit').submit();
                }
            },
            error: function(response) {
                toastrs('Error', 'Failed to save ETC time', 'error');
            }
        });
    });
});
</script>