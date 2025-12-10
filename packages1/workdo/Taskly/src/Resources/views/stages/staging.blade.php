@extends('layouts.main')
@section('page-title')
    {{__('Staging - Task Workflow')}}
@endsection

@section('title')
    {{__('Staging - Task Workflow')}}
@endsection

@section('page-breadcrumb')
    {{ __('Project') }},{{ __('Task Board') }},{{ __('Staging') }}
@endsection

@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css"> 
 <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            min-height: 100vh;
            padding: 40px 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated background particles */
        .bg-particle {
            position: fixed;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 20s infinite ease-in-out;
            z-index: 0;
        }

        .particle-1 { width: 80px; height: 80px; top: 10%; left: 10%; animation-delay: 0s; }
        .particle-2 { width: 60px; height: 60px; top: 60%; left: 80%; animation-delay: 3s; }
        .particle-3 { width: 100px; height: 100px; top: 80%; left: 20%; animation-delay: 6s; }
        .particle-4 { width: 50px; height: 50px; top: 30%; right: 15%; animation-delay: 9s; }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-50px) rotate(180deg); }
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        /* Header Section */
        .header {
            text-align: center;
            margin-bottom: 50px;
            animation: fadeInDown 0.8s ease;
        }

        .header h1 {
            font-size: 48px;
            margin-bottom: 10px;
            text-shadow: 2px 2px 20px rgba(0,0,0,0.2);
            font-weight: 800;
        }

        .header p {
            font-size: 18px;
            margin-bottom: 30px;
        }

        .stats-bar {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .stat-item {
            background: rgb(255 112 99);
            backdrop-filter: blur(10px);
            padding: 15px 30px;
            border-radius: 50px;
            color: black;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            background: rgba(255,255,255,0.25);
            transform: translateY(-3px);
        }

        .stat-number {
            font-size: 24px;
            font-weight: 700;
        }

        /* Create Event Button */
        .create-btn-wrapper {
            text-align: center;
            margin-bottom: 50px;
            animation: fadeIn 1s ease;
        }

        .create-btn {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff8c42 100%);
            color: white;
            border: none;
            padding: 18px 50px;
            font-size: 18px;
            font-weight: 700;
            border-radius: 50px;
            cursor: pointer;
            box-shadow: 0 10px 30px rgba(255, 107, 107, 0.4);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .create-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255,255,255,0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .create-btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .create-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(255, 107, 107, 0.5);
        }

        .create-btn span {
            position: relative;
            z-index: 1;
        }

        /* Events Grid */
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            animation: fadeInUp 1s ease;
        }

        .event-card {
            background: white;
            border-radius: 25px;
            padding: 35px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 3px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .event-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #ff6b6b, #ff8c42, #ffd93d);
            transform: translateX(-100%);
            transition: transform 0.6s ease;
        }

        .event-card:hover::before {
            transform: translateX(0);
        }

        .event-card:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: 0 25px 60px rgba(0,0,0,0.25);
            border-color: #ff8c42;
        }

        .event-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 25px;
        }

        .event-title {
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(135deg, #ff6b6b, #ff8c42);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
        }

        .event-badge {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .event-meta {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            font-size: 14px;
            font-weight: 600;
        }

        .meta-icon {
            width: 20px;
            height: 20px;
        }

        .event-description {
            color: #475569;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .progress-section {
            margin-bottom: 25px;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 10px;
            transition: width 1s ease;
        }

        .view-task-btn {
            width: 100%;
            background: linear-gradient(135deg, #ff6b6b, #ff8c42);
            color: white;
            border: none;
            padding: 16px;
            font-size: 16px;
            font-weight: 700;
            border-radius: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(255, 107, 107, 0.3);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .view-task-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(255, 107, 107, 0.4);
        }

        .play-icon {
            display: inline-block;
            margin-left: 10px;
            font-size: 18px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.7; transform: scale(1.1); }
        }

        /* Task Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(10px);
            z-index: 9999;
            animation: fadeIn 0.3s ease;
        }

        .modal-overlay.active {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .task-modal {
            background: white;
            border-radius: 30px;
            width: 100%;
            max-width: 1200px;
            max-height: 90vh;
            overflow: hidden;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-header {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff8c42 100%);
            padding: 30px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }

        .modal-title {
            font-size: 32px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .close-modal {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            font-size: 24px;
            cursor: pointer;
            color: white;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .close-modal:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 40px;
            max-height: calc(90vh - 200px);
            overflow-y: auto;
        }

        .add-task-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 30px;
        }

        .task-form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-field {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-field label {
            font-weight: 700;
            color: #ff6b6b;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-field input,
        .form-field select {
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: white;
        }

        .form-field input:focus,
        .form-field select:focus {
            outline: none;
            border-color: #ff6b6b;
            box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.1);
        }

        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-start;
        }

        .btn {
            padding: 14px 30px;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-add {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        }

        .btn-add:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(16, 185, 129, 0.4);
        }

        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(102, 126, 234, 0.4);
        }

        .tasks-table-container {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .tasks-table {
            width: 100%;
            border-collapse: collapse;
        }

        .tasks-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .tasks-table th {
            padding: 18px 20px;
            text-align: left;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .tasks-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .tasks-table tbody tr:hover {
            background: linear-gradient(90deg, rgba(255, 107, 107, 0.05), rgba(255, 140, 66, 0.05));
        }

        .tasks-table td {
            padding: 18px 20px;
            font-size: 14px;
            color: #475569;
        }

        .task-row-input {
            width: 100%;
            padding: 8px 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .task-row-input:focus {
            outline: none;
            border-color: #ff6b6b;
        }

        .task-row-select {
            width: 100%;
            padding: 8px 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            background: white;
            cursor: pointer;
        }

        .delete-row-btn {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border: none;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .delete-row-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(239, 68, 68, 0.4);
        }

        .link-btn {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            border: none;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .link-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(245, 158, 11, 0.4);
        }

        .empty-tasks {
            text-align: center;
            padding: 60px 20px;
            color: #94a3b8;
        }

        .empty-tasks-icon {
            font-size: 60px;
            margin-bottom: 15px;
        }

        /* Scrollbar Styling */
        .modal-body::-webkit-scrollbar {
            width: 8px;
        }

        .modal-body::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .modal-body::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #ff6b6b, #ff8c42);
            border-radius: 10px;
        }

        /* Animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header h1 {
                font-size: 36px;
            }

            .events-grid {
                grid-template-columns: 1fr;
            }

            .stats-bar {
                gap: 15px;
            }

            .stat-item {
                padding: 12px 20px;
                font-size: 14px;
            }

            .task-form-grid {
                grid-template-columns: 1fr;
            }

            .modal-header {
                padding: 20px;
            }

            .modal-title {
                font-size: 24px;
            }

            .modal-body {
                padding: 20px;
            }

            .tasks-table-container {
                overflow-x: auto;
            }

            .tasks-table {
                min-width: 800px;
            }
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            animation: fadeIn 1s ease;
        }

        .empty-icon {
            font-size: 80px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .empty-text {
            color: white;
            font-size: 20px;
            font-weight: 600;
        }
    </style>
    
@endpush

@push('scripts') 
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js"></script>
@endpush


@section('content')
    <!-- Background particles -->
    <div class="bg-particle particle-1"></div>
    <div class="bg-particle particle-2"></div>
    <div class="bg-particle particle-3"></div>
    <div class="bg-particle particle-4"></div>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>✨ Staging Dashboard</h1>
            <p>Manage your events with style and efficiency</p>
            
            <div class="stats-bar">
                <div class="stat-item">
                    <span>📊</span>
                    <div>
                        <span class="stat-number">{{$EventCount}}</span>
                        <span>Active Events</span>
                    </div>
                </div>
                <div class="stat-item">
                    <span>✅</span>
                    <div>
                        <span class="stat-number">0%</span>
                        <span>Completion</span>
                    </div>
                </div>
                <div class="stat-item">
                    <span>👥</span>
                    <div>
                        <span class="stat-number">0</span>
                        <span>Team Members</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Event Button -->
        <div class="create-btn-wrapper">
            <button class="create-btn" onclick="AddEvent()">
                <span>➕ Create New Event</span>
            </button>
        </div>

        <!-- Events Grid -->
        <div class="events-grid" id="eventsGrid">
            @foreach($Event_list as $row)
            <div class="event-card" style="animation-delay: 0.1s">
                <h4 style="float:right;cursor:pointer;" onclick="delete_event('{{$row->id}}')">❌</h4>
                <div class="event-header">                    
                    <div>
                        <h2 class="event-title">{{$row->event}}</h2>                           
                        @if($row->status == 1)
                            <div class="event-badge" style="background: linear-gradient(135deg, #f59e0b, #d97706);">Upcoming</div>                         
                        @elseif($row->status == 2)
                            <div class="event-badge" style="background: linear-gradient(135deg, #10b981, #059669);">In Progress</div>
                        @else
                            <div class="event-badge" style="width:76px !important">Active</div>
                        @endif
                    </div>
                </div>
                
                <div class="event-meta">
                    <div class="meta-item">
                        <svg class="meta-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                        Nov 05, 2025
                    </div>
                    <div class="meta-item">
                        <svg class="meta-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                        </svg>
                        0 Attendees
                    </div>
                </div>

                <p class="event-description">
                    {{$row->event_note}}
                </p>

                <div class="progress-section">
                    <div class="progress-label">
                        <span>Task Progress</span>
                        <span>0%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 0%"></div>
                    </div>
                </div>

                <button class="view-task-btn" onclick="viewTasks('{{$row->id}}', '{{$row->event}}')">
                    View Tasks
                    <span class="play-icon">▶</span>
                </button>
            </div>
            @endforeach
        </div>
    </div>   

    <!-- Task Modal -->
    <div class="modal-overlay" id="taskModal">
        <div class="task-modal">
            <div class="modal-header">
                <h2 class="modal-title">
                    <span>📋</span>
                    <span id="modalEventTitle">Event Tasks</span>
                </h2>
                <button class="close-modal" onclick="closeTaskModal()">✕</button>
            </div>
            
            <div class="modal-body">
                <!-- Add Task Section -->
                <div class="add-task-section">
                    <h3 style="margin-bottom: 20px; color: #ff6b6b; font-size: 20px;">➕ Add New Task</h3>
                    <form id="taskForm">
                        <div class="task-form-grid">
                            <div class="form-field">
                                <label>Group</label>
                                <input type="text" name="group" placeholder="Task Group" class="form-control">
                            </div>
                            <div class="form-field">
                                <label>Task</label>
                                <input type="text" name="task" placeholder="Task Name" class="form-control">
                            </div>
                            <div class="form-field">
                                <label>Assignor</label>
                                <select name="assignor" class="form-control">
                                    <option value="">Select Assignor</option>
                                    @foreach($user as $obj)
                                    <option value="{{$obj->id}}">{{$obj->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-field">
                                <label>Assignee</label>
                                <select name="assignee" class="form-control">
                                    <option value="">Select Assignee</option>
                                    @foreach($user as $obj)
                                    <option value="{{$obj->id}}">{{$obj->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-field">
                                <label>Start Date</label>
                                <input type="date" name="start_date" class="form-control">
                            </div>
                            <div class="form-field">
                                <label>End Date</label>
                                <input type="date" name="end_date" class="form-control">
                            </div>
                            <div class="form-field">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">Select Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                            <div class="form-field">
                                <label>Priority</label>
                                <select name="priority" class="form-control">
                                    <option value="">Select Priority</option>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn btn-add" onclick="addTaskRow()">
                                <span>➕</span>
                                <span>Add More</span>
                            </button>
                            <button type="submit" class="btn btn-submit">
                                <span>💾</span>
                                <span>Submit All Tasks</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tasks Table -->
                <div class="tasks-table-container">
                    <table class="tasks-table">
                        <thead>
                            <tr>
                                <th>Group</th>
                                <th>Task</th>
                                <th>Assignor</th>
                                <th>Assignee</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Priority</th>
                                <th>Links</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tasksTableBody">
                            <tr class="empty-tasks">
                                <td colspan="10">
                                    <div class="empty-tasks-icon">📝</div>
                                    <p>No tasks added yet. Click "Add More" to create tasks!</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let taskRows = [];
    let currentEventId = null;

    function AddEvent() {
        $.confirm({
            title: 'Create Event!',
            content: '' +
                '<form class="formName">' +
                '<div class="form-group">' +
                '<label>Add Event</label>'+
                '<input type="text" class="event_name form-control" placeholder="Enter Event Name" name="tl_id" value="" name="user_id">' +
                '<br>'+
                '<label>Event Note</label>' +
                '<input type="text" class="event_note form-control" placeholder="Enter Event Note" name="tl_id" value="" name="user_id">' +
                '</div>' +
                '</form>',
            buttons: {
                formSubmit: {
                    text: 'Submit',
                    btnClass: 'btn-warning',
                    action: function () {
                        var event_name = this.$content.find('.event_name').val();
                        var event_note = this.$content.find('.event_note').val();
                        if (!event_name) {
                            $.alert({
                                title: 'Error!',
                                content: 'Please Enter Event Name',
                                boxWidth: '30%',
                                useBootstrap: false,
                                backgroundDismiss: true,
                                containerFluid: true,
                                onOpenBefore: function () {
                                    $('.jconfirm-overlay').css({
                                        'backdrop-filter': 'blur(5px)',
                                        '-webkit-backdrop-filter': 'blur(5px)',
                                        'background-color': 'rgba(0,0,0,0.5)'
                                    });
                                }
                            });
                            return false;
                        }

                        $.ajax({
                            url: "{{ route('tasks.staging.create.event') }}",
                            type: "POST",
                            data: {
                                _token: '{{ csrf_token() }}',
                                event_name: event_name,
                                event_note: event_note
                            },
                            success: function (response) {
                                $.alert({
                                    title: '✅ Success',
                                    content: response.message,
                                    boxWidth: '30%',
                                    useBootstrap: false,
                                    backgroundDismiss: true,
                                    containerFluid: true,
                                    onOpenBefore: function () {
                                        $('.jconfirm-overlay').css({
                                            'backdrop-filter': 'blur(5px)',
                                            '-webkit-backdrop-filter': 'blur(5px)',
                                            'background-color': 'rgba(0,0,0,0.5)'
                                        });
                                    },
                                    onDestroy: function () {
                                        location.reload();
                                    }
                                });
                            },
                            error: function (xhr) {
                                $.alert({
                                    title: '⚠️ Error',
                                    content: xhr.responseJSON?.message || 'Something went wrong. Please try again.',
                                    type: 'red',
                                    boxWidth: '30%',
                                    useBootstrap: false
                                });
                            }
                        });
                    }
                },
                cancel: function () {}
            },
            onContentReady: function () {
                var jc = this;
                this.$content.find('form').on('submit', function (e) {
                    e.preventDefault();
                    jc.$formSubmit.trigger('click');
                });
            },
            boxWidth: '30%',
            useBootstrap: false,
            backgroundDismiss: true,
            containerFluid: true,
            onOpenBefore: function () {
                $('.jconfirm-overlay').css({
                    'backdrop-filter': 'blur(5px)',
                    '-webkit-backdrop-filter': 'blur(5px)',
                    'background-color': 'rgba(0,0,0,0.5)'
                });
            }
        });
    }

    function viewTasks(eventId, eventName) {
        currentEventId = eventId;
        document.getElementById('modalEventTitle').textContent = eventName;
        document.getElementById('modalEventTitle').setAttribute('data-event-id', eventId);
        document.getElementById('taskModal').classList.add('active');
        document.body.style.overflow = 'hidden';
        
        // TODO: Load existing tasks for this event via AJAX
        // You can add AJAX call here to fetch existing tasks
    }

    function closeTaskModal() {
        document.getElementById('taskModal').classList.remove('active');
        document.body.style.overflow = 'auto';
        taskRows = [];
        currentEventId = null;
        document.getElementById('taskForm').reset();
        document.getElementById('tasksTableBody').innerHTML = `
            <tr class="empty-tasks">
                <td colspan="10">
                    <div class="empty-tasks-icon">📝</div>
                    <p>No tasks added yet. Click "Add More" to create tasks!</p>
                </td>
            </tr>
        `;
    }

    function addTaskRow() {
        const form = document.getElementById('taskForm');
        const formData = new FormData(form);
        
        // Validate form
        let hasEmptyFields = false;
        for (let [key, value] of formData.entries()) {
            if (!value) {
                hasEmptyFields = true;
                break;
            }
        }
        
        if (hasEmptyFields) {
            $.alert({
                title: '⚠️ Validation Error',
                content: 'Please fill all fields before adding a task row.',
                type: 'orange',
                boxWidth: '30%',
                useBootstrap: false
            });
            return;
        }
        
        // Create task object
        const task = {
            group: formData.get('group'),
            task: formData.get('task'),
            assignor: form.assignor.options[form.assignor.selectedIndex].text,
            assignor_id: formData.get('assignor'),
            assignee: form.assignee.options[form.assignee.selectedIndex].text,
            assignee_id: formData.get('assignee'),
            start_date: formData.get('start_date'),
            end_date: formData.get('end_date'),
            status: form.status.options[form.status.selectedIndex].text,
            status_value: formData.get('status'),
            priority: form.priority.options[form.priority.selectedIndex].text,
            priority_value: formData.get('priority'),
            link: ''
        };
        
        taskRows.push(task);
        renderTasksTable();
        form.reset();
    }

    function renderTasksTable() {
        const tbody = document.getElementById('tasksTableBody');
        
        if (taskRows.length === 0) {
            tbody.innerHTML = `
                <tr class="empty-tasks">
                    <td colspan="10">
                        <div class="empty-tasks-icon">📝</div>
                        <p>No tasks added yet. Click "Add More" to create tasks!</p>
                    </td>
                </tr>
            `;
            return;
        }
        
        tbody.innerHTML = taskRows.map((task, index) => `
            <tr>
                <td><input type="text" class="task-row-input" value="${task.group}" data-index="${index}" data-field="group"></td>
                <td><input type="text" class="task-row-input" value="${task.task}" data-index="${index}" data-field="task"></td>
                <td>${task.assignor}</td>
                <td>${task.assignee}</td>
                <td><input type="date" class="task-row-input" value="${task.start_date}" data-index="${index}" data-field="start_date"></td>
                <td><input type="date" class="task-row-input" value="${task.end_date}" data-index="${index}" data-field="end_date"></td>
                <td>
                    <select class="task-row-select" data-index="${index}" data-field="status_value">
                        <option value="pending" ${task.status_value === 'pending' ? 'selected' : ''}>Pending</option>
                        <option value="in_progress" ${task.status_value === 'in_progress' ? 'selected' : ''}>In Progress</option>
                        <option value="completed" ${task.status_value === 'completed' ? 'selected' : ''}>Completed</option>
                    </select>
                </td>
                <td>
                    <select class="task-row-select" data-index="${index}" data-field="priority_value">
                        <option value="low" ${task.priority_value === 'low' ? 'selected' : ''}>Low</option>
                        <option value="medium" ${task.priority_value === 'medium' ? 'selected' : ''}>Medium</option>
                        <option value="high" ${task.priority_value === 'high' ? 'selected' : ''}>High</option>
                    </select>
                </td>
                <td>
  <button class="link-btn" onclick="addLink(${index})" title="Add Links">🔗</button>
  <div style="text-align:center;font-size:12px;margin-top:3px;display:none">
    ${
      [task.l1, task.l2, task.l3, task.l4, task.l5, task.l6, task.l7]
        .filter(l => l)
        .map(l => `<a href="${l}" target="_blank" style="margin:0 2px;">🔗</a>`)
        .join('') || '—'
    }
  </div>
</td>

                <td><button class="delete-row-btn" onclick="deleteTaskRow(${index})" title="Delete">✕</button></td>
            </tr>
        `).join('');
        
        // Add event listeners for inline editing
        document.querySelectorAll('.task-row-input, .task-row-select').forEach(input => {
            input.addEventListener('change', function() {
                const index = parseInt(this.dataset.index);
                const field = this.dataset.field;
                taskRows[index][field] = this.value;
                
                // Update text values for status and priority
                if (field === 'status_value') {
                    taskRows[index].status = this.options[this.selectedIndex].text;
                }
                if (field === 'priority_value') {
                    taskRows[index].priority = this.options[this.selectedIndex].text;
                }
            });
        });
    }

    function deleteTaskRow(index) {
        $.confirm({
            title: '⚠️ Confirm Delete',
            content: 'Are you sure you want to delete this task?',
            type: 'red',
            buttons: {
                confirm: {
                    text: 'Yes, Delete',
                    btnClass: 'btn-danger',
                    action: function() {
                        taskRows.splice(index, 1);
                        renderTasksTable();
                    }
                },
                cancel: {
                    text: 'Cancel'
                }
            },
            boxWidth: '30%',
            useBootstrap: false
        });
    }

    function addLink(index) {
    const task = taskRows[index];

    // Get existing links safely
    const currentLinks = [
        task.l1 || '', task.l2 || '', task.l3 || '',
        task.l4 || '', task.l5 || '', task.l6 || '', task.l7 || ''
    ];

    // Build form HTML before opening popup
    let contentHtml = '<form id="multiLinkForm">';
    for (let i = 1; i <= 7; i++) {
        contentHtml += `
            <div class="form-group" style="margin-bottom:12px">
                <label>Link ${i}</label>
                <input type="url" 
                       class="form-control link-input" 
                       name="l${i}" 
                       placeholder="https://example.com" 
                       value="${currentLinks[i - 1]}" 
                       style="padding:10px;border-radius:10px;">
            </div>`;
    }
    contentHtml += '</form>';

    // Single jQuery Confirm overlay (same as previous)
    $.confirm({
        title: '🔗 Add Multiple Links',
        content: contentHtml,
        boxWidth: '35%',
        useBootstrap: false,
        backgroundDismiss: true,
        containerFluid: true,
        buttons: {
            save: {
                text: '💾 Save Links',
                btnClass: 'btn-warning',
                action: function () {
                    // Read all 7 link values
                    const form = this.$content.find('#multiLinkForm')[0];
                    const formData = new FormData(form);
                    for (let [key, value] of formData.entries()) {
                        taskRows[index][key] = value.trim();
                    }

                    $.alert({
                        title: '✅ Links Saved',
                        content: 'All links have been updated!',
                        boxWidth: '30%',
                        useBootstrap: false,
                        backgroundDismiss: true,
                        onOpenBefore: function () {
                            $('.jconfirm-overlay').css({
                                'backdrop-filter': 'blur(5px)',
                                '-webkit-backdrop-filter': 'blur(5px)',
                                'background-color': 'rgba(0,0,0,0.5)'
                            });
                        }
                    });
                }
            },
            cancel: {
                text: 'Cancel'
            }
        },
        onOpenBefore: function () {
            $('.jconfirm-overlay').css({
                'backdrop-filter': 'blur(5px)',
                '-webkit-backdrop-filter': 'blur(5px)',
                'background-color': 'rgba(0,0,0,0.5)'
            });
        }
    });
}



    // Handle form submission
    document.addEventListener('DOMContentLoaded', function() {
        const taskForm = document.getElementById('taskForm');
        if (taskForm) {
            taskForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (taskRows.length === 0) {
                    $.alert({
                        title: '⚠️ No Tasks',
                        content: 'Please add at least one task before submitting.',
                        type: 'orange',
                        boxWidth: '30%',
                        useBootstrap: false
                    });
                    return;
                }
                
                // Submit all tasks via AJAX
                $.ajax({
                    url: "{{ route('tasks.staging.submit.tasks') }}", // Create this route
                    type: "POST",
                    contentType: "application/json",
                    data: JSON.stringify({
                        _token: '{{ csrf_token() }}',
                        tasks: taskRows,
                        event_id: currentEventId
                    }),
                    success: function (response) {
                        $.alert({
                            title: '✅ Success',
                            content: response.message || 'Tasks submitted successfully!',
                            boxWidth: '30%',
                            useBootstrap: false,
                            onDestroy: function () {
                                closeTaskModal();
                                location.reload();
                            }
                        });
                    },
                    error: function (xhr) {
                        $.alert({
                            title: '⚠️ Error',
                            content: xhr.responseJSON?.message || 'Something went wrong.',
                            type: 'red',
                            boxWidth: '30%',
                            useBootstrap: false
                        });
                    }
                });
            });
        }
    });

    // Close modal on overlay click
    document.addEventListener('click', function(e) {
        if (e.target.id === 'taskModal') {
            closeTaskModal();
        }
    });

    // Animate progress bars on load
    window.addEventListener('load', () => {
        const progressBars = document.querySelectorAll('.progress-fill');
        progressBars.forEach((bar, index) => {
            setTimeout(() => {
                bar.style.width = bar.style.width;
            }, 300 * index);
        });
    });
    
    // Delete event function
    function delete_event(eventId) {
        if (!confirm('Are you sure you want to delete this event?')) {
            return;
        }

        $.ajax({
            url: "{{ route('tasks.staging.delete') }}",
            type: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                id: eventId
            },
            success: function (response) {
                $.alert({
                    title: '✅ Success',
                    content: response.message,
                    boxWidth: '30%',
                    useBootstrap: false,
                    backgroundDismiss: true,
                    onDestroy: function () {
                        location.reload();
                    }
                });
            },
            error: function (xhr) {
                $.alert({
                    title: '⚠️ Error',
                    content: xhr.responseJSON?.message || 'Something went wrong. Please try again.',
                    type: 'red',
                    boxWidth: '30%',
                    useBootstrap: false
                });
            }
        });
    }
</script>
@endpush