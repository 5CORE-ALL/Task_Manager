<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multi-Task Assignment Form</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --light-bg: #f8fafc;
            --border: #e2e8f0;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .form-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 1200px;
            margin: 0 auto;
            overflow: hidden;
        }

        .form-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 30px;
            color: white;
            text-align: center;
        }

        .form-header h2 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .form-header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-size: 14px;
        }

        .form-body {
            padding: 30px;
            background: var(--light-bg);
        }

        .assignor-section {
            background: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border-left: 4px solid var(--primary);
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
        }

        .task-container {
            background: white;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            overflow: hidden;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .task-container:hover {
            border-color: var(--primary);
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.2);
        }

        .task-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px 25px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.3s ease;
        }

        .task-header:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        .task-title {
            color: white;
            font-weight: 600;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .task-number {
            background: rgba(255,255,255,0.2);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 700;
            backdrop-filter: blur(10px);
        }

        .collapse-icon {
            transition: transform 0.3s ease;
            font-size: 20px;
        }

        .task-container.collapsed .collapse-icon {
            transform: rotate(-90deg);
        }

        .task-content {
            padding: 30px;
            display: block;
            background: linear-gradient(to bottom, #f8fafc, white);
        }

        .task-container.collapsed .task-content {
            display: none;
        }

        .form-label {
            font-weight: 600;
            color: #475569;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-control, .form-select {
            border: 2px solid var(--border);
            border-radius: 10px;
            padding: 12px 15px;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .input-group-text {
            border: 2px solid var(--border);
            background: white;
            border-left: none;
            border-radius: 0 10px 10px 0;
        }

        .input-group .form-control {
            border-right: none;
            border-radius: 10px 0 0 10px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success), #059669);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger), #dc2626);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .btn-light {
            background: #e2e8f0;
            color: #475569;
        }

        .btn-light:hover {
            background: #cbd5e1;
        }

        .add-task-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 16px;
            padding: 15px;
            border: 2px dashed var(--success);
            background: rgba(16, 185, 129, 0.05);
            color: var(--success);
        }

        .add-task-btn:hover {
            background: var(--success);
            color: white;
            border-style: solid;
        }

        .remove-task-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            font-size: 13px;
        }

        .modal-footer {
            padding: 25px 30px;
            background: var(--light-bg);
            border-top: 2px solid var(--border);
            gap: 12px;
        }

        .text-danger {
            color: var(--danger);
            margin-left: 3px;
        }

        .task-counter {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
            padding: 10px 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .link-item {
            background: #f8fafc;
            padding: 15px;
            border-radius: 10px;
            border: 2px solid var(--border);
            transition: all 0.3s ease;
        }

        .link-item:hover {
            border-color: var(--primary);
            background: white;
        }

        .links-container-wrapper {
            margin-bottom: 15px;
        }

        .add-link-btn {
            border: 2px dashed var(--success);
            color: var(--success);
            background: rgba(16, 185, 129, 0.05);
            padding: 10px;
            transition: all 0.3s ease;
        }

        .add-link-btn:hover {
            background: rgba(16, 185, 129, 0.1);
            border-color: var(--success);
            color: var(--success);
            transform: translateY(-2px);
        }

        .remove-link-btn {
            padding: 10px !important;
            height: 45px;
        }

        .bulk-actions {
            background: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border-left: 4px solid var(--info);
        }

        .bulk-actions-title {
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quick-action-btn {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid;
            margin-right: 10px;
            margin-bottom: 10px;
        }

        .duplicate-task-btn {
            background: linear-gradient(135deg, var(--info), #2563eb);
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            margin-left: 10px;
            transition: all 0.3s ease;
        }

        .duplicate-task-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        .link-preview {
            font-size: 11px;
            color: #64748b;
            margin-top: 5px;
            word-break: break-all;
        }

        .keyboard-shortcut {
            font-size: 11px;
            background: #e2e8f0;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: monospace;
            margin-left: 8px;
        }

        @media (max-width: 768px) {
            .form-body {
                padding: 20px;
            }
            
            .task-content {
                padding: 20px;
            }
            
            .assignor-section {
                padding: 20px;
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .task-container {
            animation: slideIn 0.3s ease;
        }

        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }
    </style>
</head>

<body>
    <div class="form-container">
        <div class="form-header">
            <h2>
                <span>📋</span>
                Multi-Task Assignment Form
            </h2>
            <p>Assign multiple tasks efficiently with enhanced management</p>
        </div>

        <form class="needs-validation" method="post" action="{{ route('tasks.save.multiple') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-body">                
                <!-- Task Counter -->
                <div class="task-counter">
                    Total Tasks: <span id="taskCount">1</span>
                    <span class="keyboard-shortcut">Ctrl+Enter</span> to add task
                </div>

                <!-- Bulk Actions -->
                <div class="bulk-actions">
                    <div class="bulk-actions-title">
                        <span>⚡</span>
                        Quick Actions
                    </div>
                    <button type="button" class="quick-action-btn btn-outline-danger" id="clearLinksBtn">
                        Clear All Links
                    </button>
                    <button type="button" class="quick-action-btn btn-outline-primary" id="sameAssignorBtn">
                        All Assignor Same
                    </button>
                    <button type="button" class="quick-action-btn btn-outline-success" id="sameAssigneeBtn">
                        All Assignee Same
                    </button>                   
                </div>

                <!-- Tasks Container -->
                <div id="tasksContainer">
                    <!-- Task 1 (Template) -->
                    <div class="task-container" data-task-number="1">
                        <!-- <div class="task-header" onclick="toggleTask('task1')"> -->
                            <div class="task-header">
                            <div class="task-title">
                                <span class="task-number">Task 1</span>
                                <span class="collapse-icon">▼</span>
                            </div>
                            <div style="display: flex; gap: 10px;">
                                <button type="button" class="duplicate-task-btn" title="Duplicate this task">
                                    📋 Duplicate
                                </button>
                                <button type="button" class="btn btn-sm btn-danger remove-task-btn" style="display:none;">
                                    ✕ Remove
                                </button>
                            </div>
                        </div>
                        <div class="task-content">
                            <!-- Assignment Details in Each Task -->
                            <div class="section-title mb-3">
                                <span class="section-icon">👤</span>
                                Assignment Details
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Assignor<span class="text-danger">*</span></label>
                                    <select class="form-control form-select assignor-select" name="assign_by[]" required>
                                        <option value="">Select Assignor</option>
                                        @foreach($users as $u)
                                             <option value="{{$u->email}}" @if($u->email==auth()->user()->email) selected @endif>{{$u->name}}</option>
                                              <!--<option value="{{$u->email}}" @if($u->email==auth()->user()->email) selected @endif>{{$u->name}} - {{$u->email}} - {{$u->mobile_no}}</option>-->
                                         @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Assign To<span class="text-danger">*</span></label>
                                    <select class="form-control form-select assignee-select" name="assign_to[]" required>
                                        <option value="">Select Assignee</option>
                                        @foreach($users as $u)
                                             <option value="{{$u->email}}" @if($u->email==auth()->user()->email) selected @endif>{{$u->name}}</option>
                                              <!--<option value="{{$u->email}}" @if($u->email==auth()->user()->email) selected @endif>{{$u->name}} - {{$u->email}} - {{$u->mobile_no}}</option>-->
                                         @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Basic Info -->
                            <div class="section-title mb-3">
                                <span class="section-icon">ℹ️</span>
                                Basic Information
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Group<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="group[]" required placeholder="Enter group name">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Title<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="title[]" required placeholder="Enter task title">
                                </div>
                            </div>

                            <!-- Task Details -->
                            <div class="section-title mb-3">
                                <span class="section-icon">⚙️</span>
                                Task Configuration
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">ETC (Minutes)<span class="text-danger">*</span></label>
                                    <input type="number" class="form-control etc-field" name="eta_time[]" required min="1" placeholder="e.g., 120" oninput="updateStatistics()">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="stage_id[]" class="form-control form-select">
                                        <option value="">Select Status</option>
                                        <option value="todo">To Do</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="review">Review</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Priority<span class="text-danger">*</span></label>
                                    <select class="form-control form-select priority-select" name="priority[]" required>
                                        <option value="normal">🟢 Normal</option>
                                        <option value="low">🔵 Low</option>
                                        <option value="high">🟠 High</option>
                                        <option value="urgent">🔴 Urgent</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Duration<span class="text-danger">*</span></label>
                                    <div class='input-group'>
                                        <input type='text' class="form-control date duration-field" placeholder="Select Date Range" name="duration[]" required/>
                                        <span class="input-group-text">📅</span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" name="description[]" rows="2" placeholder="Enter task description"></textarea>
                                </div>
                            </div>

                            <!-- Links Section -->
                            <div class="section-title mb-3">
                                <span class="section-icon">🔗</span>
                                Resource Links
                            </div>
                            <div class="links-container-wrapper" data-task-id="1">
                                <div class="link-item mb-3">
                                    <div class="row align-items-end">
                                        <div class="col-md-5">
                                            <label class="form-label">Link Type</label>
                                            <select class="form-control form-select link-type-select">
                                                <option value="L1">L1</option>
                                                <option value="L2">L2</option>
                                                <option value="Training">Training Link</option>
                                                <option value="Video">Video Link</option>
                                                <option value="Form">Form Link</option>
                                                <option value="Report">Form Report Link</option>
                                                <option value="Checklist">Checklist Link</option>
                                                <option value="PL">PL</option>
                                                <option value="Process">PROCESS</option>
                                                <option value="Custom">Custom</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">URL</label>
                                            <input type="text" class="form-control link-url-input" placeholder="Enter URL">
                                            <div class="link-preview"></div>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-danger btn-sm w-100 remove-link-btn" style="display:none;" title="Remove Link">
                                                ✕
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-success add-link-btn w-100">
                                <span style="font-size: 16px;">+</span> Add Another Link
                            </button>
                            
                            <!-- Hidden inputs for form submission -->
                            <input type="hidden" class="links-data-input" name="links_data[]" value="">
                        </div>
                    </div>
                </div>
                
                <!-- Add Task Button -->
                <button type="button" class="btn add-task-btn" id="addTaskBtn">
                    <span style="font-size: 20px;">+</span>
                    Add Another Task
                </button>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-light" onclick="resetForm()">
                    🔄 Reset Form
                </button>
                <button type="submit" class="btn btn-primary">
                    ✓ Create Tasks
                </button>
            </div>
        </form>
    </div>

    <!--<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>-->
    <!--<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>-->
    
    <script>
let taskCounter = 0;

// Utilities
function q(sel, ctx=document){ return ctx.querySelector(sel); }
function qAll(sel, ctx=document){ return Array.from(ctx.querySelectorAll(sel)); }

// Init
document.addEventListener('DOMContentLoaded', () => {
  taskCounter = qAll('.task-container').length || 1;
  initializeDatePickers();
  qAll('.link-url-input').forEach(inp => {
    inp.addEventListener('input', (e) => {
      updateLinkPreview(inp);
      updateStatistics();
    });
  });
  qAll('.links-container-wrapper').forEach(wrapper => updateRemoveButtons(wrapper));
  updateStatistics();
  console.log('Form initialized.');
});

// Bulk Actions for Assignor/Assignee
document.getElementById('sameAssignorBtn')?.addEventListener('click', function() {
  const firstTask = document.querySelector('.task-container');
  if (!firstTask) return;
  
  const firstAssignor = firstTask.querySelector('.assignor-select');
  if (!firstAssignor || !firstAssignor.value) {
    alert('Please select an Assignor in the first task first!');
    return;
  }
  
  const selectedValue = firstAssignor.value;
  const allAssignors = document.querySelectorAll('.assignor-select');
  
  allAssignors.forEach(select => {
    select.value = selectedValue;
  });
  
  alert('All Assignors set to: ' + firstAssignor.options[firstAssignor.selectedIndex].text);
});

document.getElementById('sameAssigneeBtn')?.addEventListener('click', function() {
  const firstTask = document.querySelector('.task-container');
  if (!firstTask) return;
  
  const firstAssignee = firstTask.querySelector('.assignee-select');
  if (!firstAssignee || !firstAssignee.value) {
    alert('Please select an Assignee in the first task first!');
    return;
  }
  
  const selectedValue = firstAssignee.value;
  const allAssignees = document.querySelectorAll('.assignee-select');
  
  allAssignees.forEach(select => {
    select.value = selectedValue;
  });
  
  alert('All Assignees set to: ' + firstAssignee.options[firstAssignee.selectedIndex].text);
});

// Clear All Links
document.getElementById('clearLinksBtn')?.addEventListener('click', function() {
  if (!confirm('Are you sure you want to clear all links from all tasks?')) return;
  
  document.querySelectorAll('.link-url-input').forEach(input => {
    input.value = '';
    updateLinkPreview(input);
  });
  
  updateStatistics();
  alert('All links cleared successfully!');
});

// DatePickers
function initializeDatePickers(context = document) {
  const els = context.querySelectorAll('.duration-field');
  if (!els.length) return;
  flatpickr(els, {
    mode: 'range',
    dateFormat: 'Y-m-d',
    minDate: 'today'
  });
}

// Event Delegation
document.addEventListener('click', function(e){
  // Add Link
  const addLinkBtn = e.target.closest('.add-link-btn');
  if (addLinkBtn) {
    e.preventDefault();
    const linksWrapper = addLinkBtn.previousElementSibling;
    if (!linksWrapper || !linksWrapper.classList.contains('links-container-wrapper')) return;

    const firstLink = linksWrapper.querySelector('.link-item');
    if (!firstLink) return;

    const newLink = firstLink.cloneNode(true);
    const urlInput = newLink.querySelector('.link-url-input');
    if (urlInput) urlInput.value = '';
    const typeSelect = newLink.querySelector('.link-type-select');
    if (typeSelect) typeSelect.selectedIndex = 0;
    const preview = newLink.querySelector('.link-preview');
    if (preview) preview.textContent = '';
    const removeBtn = newLink.querySelector('.remove-link-btn');
    if (removeBtn) removeBtn.style.display = 'block';

    linksWrapper.appendChild(newLink);

    if (urlInput) {
      urlInput.addEventListener('input', () => {
        updateLinkPreview(urlInput);
        updateStatistics();
      });
    }

    updateRemoveButtons(linksWrapper);
    updateStatistics();
    newLink.scrollIntoView({behavior:'smooth', block:'nearest'});
    return;
  }

  // Remove Link
  const remBtn = e.target.closest('.remove-link-btn');
  if (remBtn && remBtn.closest('.link-item')) {
    e.preventDefault();
    e.stopPropagation();
    if (!confirm('Remove this link?')) return;
    const linkItem = remBtn.closest('.link-item');
    const wrapper = remBtn.closest('.links-container-wrapper');
    if (linkItem) linkItem.remove();
    if (wrapper) updateRemoveButtons(wrapper);
    updateStatistics();
    return;
  }

  // Duplicate Task
  const dupBtn = e.target.closest('.duplicate-task-btn');
  if (dupBtn) {
    e.preventDefault();
    e.stopPropagation();
    const srcTask = dupBtn.closest('.task-container');
    if (!srcTask) return;

    const clone = srcTask.cloneNode(true);
    taskCounter++;
    const newId = 'task' + taskCounter;
    clone.id = newId;
    clone.setAttribute('data-task-number', taskCounter);

    const taskNumberSpan = clone.querySelector('.task-number');
    if (taskNumberSpan) taskNumberSpan.textContent = 'Task ' + taskCounter;

    const linksWrapper = clone.querySelector('.links-container-wrapper');
    if (linksWrapper) linksWrapper.setAttribute('data-task-id', taskCounter);

    const removeTaskBtn = clone.querySelector('.remove-task-btn');
    if (removeTaskBtn) removeTaskBtn.style.display = 'flex';

    const tasksContainer = document.getElementById('tasksContainer');
    tasksContainer.appendChild(clone);
    initializeNewTaskAfterAppend(clone);

    renumberTasks();
    updateStatistics();
    clone.scrollIntoView({behavior:'smooth', block:'center'});
    return;
  }

  // Add New Task
  const addTaskBtn = e.target.closest('#addTaskBtn, .add-task-btn');
  if (addTaskBtn) {
    e.preventDefault();
    const firstTask = document.querySelector('.task-container');
    if (!firstTask) return;

    const newTask = firstTask.cloneNode(true);
    taskCounter++;
    newTask.id = 'task' + taskCounter;
    newTask.setAttribute('data-task-number', taskCounter);

    clearInputsInTask(newTask);

    const linksWrapper = newTask.querySelector('.links-container-wrapper');
    if (linksWrapper) {
      const linkItems = linksWrapper.querySelectorAll('.link-item');
      linkItems.forEach((item, idx) => { if (idx>0) item.remove(); });
      const firstLink = linksWrapper.querySelector('.link-item');
      if (firstLink) {
        const urlInput = firstLink.querySelector('.link-url-input');
        if (urlInput) urlInput.value = '';
        const preview = firstLink.querySelector('.link-preview');
        if (preview) preview.textContent = '';
        const removeBtn = firstLink.querySelector('.remove-link-btn');
        if (removeBtn) removeBtn.style.display = 'none';
      }
      linksWrapper.setAttribute('data-task-id', taskCounter);
    }

    const removeBtnTask = newTask.querySelector('.remove-task-btn');
    if (removeBtnTask) removeBtnTask.style.display = 'flex';

    const tnum = newTask.querySelector('.task-number');
    if (tnum) tnum.textContent = 'Task ' + taskCounter;

    document.getElementById('tasksContainer').appendChild(newTask);
    initializeNewTaskAfterAppend(newTask);

    renumberTasks();
    updateStatistics();
    newTask.scrollIntoView({behavior:'smooth', block:'center'});
    return;
  }

  // Toggle task collapse
  const header = e.target.closest('.task-header');
  if (header && !e.target.closest('button')) {
    const task = header.closest('.task-container');
    if (!task) return;
    task.classList.toggle('collapsed');
    return;
  }

  // Remove Task
  const removeTaskBtn = e.target.closest('.remove-task-btn');
  if (removeTaskBtn && !removeTaskBtn.closest('.link-item')) {
    e.stopPropagation();
    const taskToRemove = removeTaskBtn.closest('.task-container');
    if (!taskToRemove) return;
    
    if (!confirm('Are you sure you want to remove this task?')) return;
    
    taskToRemove.remove();
    renumberTasks();
    updateStatistics();
    return;
  }
});

// Input delegation for link preview
document.addEventListener('input', function(e){
  const input = e.target;
  if (input && input.matches('.link-url-input')) {
    updateLinkPreview(input);
    updateStatistics();
  }
});

// Helper: clear inputs inside a task
function clearInputsInTask(task) {
  const inputs = task.querySelectorAll('input:not(.links-data-input), textarea');
  inputs.forEach(input => {
    if (input.type === 'text' || input.type === 'number' || input.tagName === 'TEXTAREA') {
      input.value = '';
    } else if (input.tagName === 'INPUT' && (input.type === 'checkbox' || input.type === 'radio')) {
      input.checked = false;
    }
  });
  const selects = task.querySelectorAll('select');
  selects.forEach(sel => { sel.selectedIndex = 0; });
}

// updateLinkPreview
function updateLinkPreview(input) {
  const preview = input.closest('.link-item')?.querySelector('.link-preview');
  if (!preview) return;
  if (!input.value.trim()) {
    preview.textContent = '';
    preview.style.color = '';
    return;
  }
  try {
    const url = new URL(input.value.trim());
    preview.textContent = `🔗 ${url.hostname}`;
    preview.style.color = '#10b981';
  } catch (err) {
    preview.textContent = '⚠️ Invalid URL format';
    preview.style.color = '#ef4444';
  }
}

// updateRemoveButtons
function updateRemoveButtons(linksWrapper) {
  const items = linksWrapper.querySelectorAll('.link-item');
  items.forEach(it => {
    const btn = it.querySelector('.remove-link-btn');
    if (!btn) return;
    btn.style.display = (items.length === 1) ? 'none' : 'block';
  });
}

// initialize new task after append
function initializeNewTaskAfterAppend(newTask) {
  if (!newTask) return;
  initializeDatePickers(newTask);

  const linkInputs = newTask.querySelectorAll('.link-url-input');
  linkInputs.forEach(inp => {
    inp.removeEventListener('input', null);
    inp.addEventListener('input', () => {
      updateLinkPreview(inp);
      updateStatistics();
    });
  });

  const linksWrapper = newTask.querySelector('.links-container-wrapper');
  if (linksWrapper) updateRemoveButtons(linksWrapper);
}

// renumber tasks
function renumberTasks() {
  const tasks = qAll('.task-container');
  tasks.forEach((task, index) => {
    const num = index + 1;
    task.id = 'task' + num;
    task.setAttribute('data-task-number', num);
    const tn = task.querySelector('.task-number');
    if (tn) tn.textContent = 'Task ' + num;
    const removeBtn = task.querySelector('.remove-task-btn');
    if (removeBtn) {
      if (num === 1) removeBtn.style.display = 'none';
      else removeBtn.style.display = 'flex';
    }
  });
  taskCounter = tasks.length;
  const taskCountEl = document.getElementById('taskCount');
  if (taskCountEl) taskCountEl.textContent = taskCounter;
}

// Statistics
function updateStatistics() {
  const totalTasks = qAll('.task-container').length;
  const taskCountEl = document.getElementById('taskCount');
  if (taskCountEl) taskCountEl.textContent = totalTasks;

  let totalLinks = 0;
  qAll('.link-url-input').forEach(inp => { if (inp.value.trim()) totalLinks++; });
  
  let totalETC = 0;
  qAll('.etc-field').forEach(inp => { totalETC += parseInt(inp.value || 0, 10); });
}

// Collect links data before submit
function collectLinksData() {
  qAll('.task-container').forEach(task => {
    const linksWrapper = task.querySelector('.links-container-wrapper');
    if (!linksWrapper) return;
    const arr = [];
    const linkItems = linksWrapper.querySelectorAll('.link-item');
    linkItems.forEach(item => {
      const type = item.querySelector('.link-type-select')?.value || '';
      const url = item.querySelector('.link-url-input')?.value.trim() || '';
      if (url) arr.push({type, url});
    });
    const hidden = task.querySelector('.links-data-input');
    if (hidden) hidden.value = JSON.stringify(arr);
  });
}

// Toggle task function (called from onclick)
function toggleTask(taskId) {
  const task = document.getElementById(taskId);
  if (task) task.classList.toggle('collapsed');
}

// Reset form
function resetForm() {
  if (!confirm('Are you sure you want to reset the entire form? All data will be lost.')) return;
  document.getElementById('multiTaskForm').reset();
  
  // Keep only first task
  const tasks = qAll('.task-container');
  tasks.forEach((task, index) => {
    if (index > 0) task.remove();
  });
  
  taskCounter = 1;
  renumberTasks();
  updateStatistics();
  alert('Form has been reset!');
}

// Form submit validation
document.getElementById('multiTaskForm')?.addEventListener('submit', function(e){
  e.preventDefault();
  collectLinksData();
  if (this.checkValidity()) {
    const formData = new FormData(this);
    let taskDetails = `Form submitted successfully!\n\nTotal tasks: ${qAll('.task-container').length}\n\n`;
    const linksArr = formData.getAll('links_data[]');
    linksArr.forEach((ld, idx) => {
      if (ld) {
        try {
          const parsed = JSON.parse(ld);
          taskDetails += `Task ${idx+1}: ${parsed.length} link(s)\n`;
        } catch { taskDetails += `Task ${idx+1}: 0 link(s)\n`; }
      } else {
        taskDetails += `Task ${idx+1}: 0 link(s)\n`;
      }
    });
    alert(taskDetails);
    // this.submit(); // Enable when backend ready
  } else {
    this.classList.add('was-validated');
    alert('Please fill all required fields!');
  }
});
    </script>
</body>
</html>