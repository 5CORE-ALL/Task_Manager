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

        .event-card.inactive {
            opacity: 0.7;
            filter: grayscale(0.3);
        }

        .event-card.inactive:hover {
            transform: translateY(-5px) scale(1.01);
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

        .form-field .input-group {
            display: flex;
            width: 100%;
        }

        .form-field .input-group .form-control {
            border-radius: 12px 0 0 12px;
            flex: 1;
        }

        .form-field .input-group .input-group-text {
            border: 2px solid #e2e8f0;
            border-left: none;
            border-radius: 0 12px 12px 0;
            background: white;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-field .input-group .input-group-text i {
            color: #64748b;
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
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
            <div class="event-card {{ (isset($row->is_inactive) && $row->is_inactive) ? 'inactive' : '' }}" style="animation-delay: 0.1s">
                <h4 style="float:right;cursor:pointer;" onclick="delete_event('{{$row->id}}')">❌</h4>
                <div class="event-header">                    
                    <div>
                        <h2 class="event-title">{{$row->event}}</h2>                           
                        @if(isset($row->is_inactive) && $row->is_inactive)
                            <div class="event-badge" style="background: linear-gradient(135deg, #6b7280, #4b5563);">Inactive</div>
                        @else
                            <div class="event-badge" style="background: linear-gradient(135deg, #667eea, #764ba2);">Active</div>
                        @endif
                    </div>
                </div>
                
                <div class="event-meta">
                    <div class="meta-item">
                        <svg class="meta-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                        {{ \Carbon\Carbon::parse($row->created_at)->format('M d, Y') }}
                    </div>
                    <div class="meta-item">
                        <svg class="meta-icon" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                        </svg>
                        {{ $row->total_tasks ?? 0 }} Tasks
                    </div>
                </div>

                <p class="event-description">
                    {{$row->event_note}}
                </p>

                <div class="progress-section">
                    <div class="progress-label">
                        <span>Task Progress</span>
                        <span>{{ $row->progress ?? 0 }}%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $row->progress ?? 0 }}%"></div>
                    </div>
                    @if(isset($row->total_tasks) && $row->total_tasks > 0)
                        <div style="font-size: 12px; color: #64748b; margin-top: 5px; text-align: center;">
                            {{ $row->done_tasks ?? 0 }} of {{ $row->total_tasks }} tasks completed
                        </div>
                    @endif
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
                    <form id="taskForm" novalidate>
                        <div class="task-form-grid">
                            <div class="form-field">
                                <label>Group</label>
                                <input type="text" name="group" placeholder="Task Group" class="form-control">
                            </div>
                            <div class="form-field">
                                <label>Task <span style="color: red;">*</span></label>
                                <input type="text" name="task" placeholder="Task Name" class="form-control" required>
                            </div>
                            <div class="form-field">
                                <label>Assignor <span style="color: red;">*</span></label>
                                <select name="assignor" class="form-control" required>
                                    <option value="">Select Assignor</option>
                                    @foreach($user as $obj)
                                    <option value="{{$obj->id}}">{{ formatUserName($obj->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-field">
                                <label>Assignee <span style="color: red;">*</span></label>
                                <select name="assignee" class="form-control" required>
                                    <option value="">Select Assignee</option>
                                    @foreach($user as $obj)
                                    <option value="{{$obj->id}}">{{ formatUserName($obj->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-field">
                                <label>Duration</label>
                                <div class='input-group'>
                                    <input type='text' class="form-control form-control-light" id="duration" name="duration" placeholder="Select date range" autocomplete="off">
                                    <input type="hidden" name="start_date" id="start_date">
                                    <input type="hidden" name="end_date" id="end_date">
                                    <span class="input-group-text" style="cursor: pointer;"><i class="feather icon-calendar"></i></span>
                                </div>
                            </div>
                            <div class="form-field">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="">Select Status</option>
                                    @foreach($stages as $stage)
                                    <option value="{{ $stage->name }}">{{ $stage->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-field">
                                <label>Priority</label>
                                <select name="priority" class="form-control">
                                    <option value="">Select Priority</option>
                                    <option value="normal">{{ __('normal')}}</option>
                                    <option value="urgent">{{ __('urgent')}}</option>
                                    <option value="Take your time">{{ __('Take your time')}}</option>
                                </select>
                            </div>
                            <div class="form-field">
                                <label>ETC (Minutes)</label>
                                <input type="number" name="eta_time" class="form-control" placeholder="Enter ETC in minutes" min="1" value="10" oninput="this.value = Math.abs(this.value.replace(/[^0-9]/g, '').slice(0, 4));">
                            </div>
                            <div class="form-field">
                                <label>L1</label>
                                <input type="text" name="l1" class="form-control" placeholder="Enter L1">
                            </div>
                            <div class="form-field">
                                <label>L2</label>
                                <input type="text" name="l2" class="form-control" placeholder="Enter L2">
                            </div>
                            <div class="form-field">
                                <label>Training Link (L3)</label>
                                <input type="text" name="l3" class="form-control" placeholder="Enter training link">
                            </div>
                            <div class="form-field">
                                <label>Video Link (L4)</label>
                                <input type="text" name="l4" class="form-control" placeholder="Enter video link">
                            </div>
                            <div class="form-field">
                                <label>Form Link (L5)</label>
                                <input type="text" name="l5" class="form-control" placeholder="Enter form link">
                            </div>
                            <div class="form-field">
                                <label>Checklist Link (L6)</label>
                                <input type="text" name="l6" class="form-control" placeholder="Enter checklist link">
                            </div>
                            <div class="form-field">
                                <label>Form Report Link (L7)</label>
                                <input type="text" name="l7" class="form-control" placeholder="Enter form report link">
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
                                <th style="width: 20%;">Group</th>
                                <th style="width: 30%;">Task</th>
                                <th>Assignor</th>
                                <th>Assignee</th>
                                <th>Links</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tasksTableBody">
                            <tr class="empty-tasks">
                                <td colspan="6">
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
        
        // Set ETC field to default value of 10
        $('input[name="eta_time"]').val('10');
        
        // Load existing tasks for this event via AJAX
        $.ajax({
            url: '/tasks/staging/get-tasks/' + eventId,
            type: "GET",
            success: function (response) {
                taskRows = response; // Assuming response is array of tasks
                renderTasksTable();
                // Initialize duration picker after modal is shown
                setTimeout(function() {
                    initializeDurationPicker();
                }, 500);
            },
            error: function (xhr) {
                console.error('Error loading tasks:', xhr);
                taskRows = [];
                renderTasksTable();
                // Initialize duration picker even if tasks fail to load
                setTimeout(function() {
                    initializeDurationPicker();
                }, 500);
            }
        });
    }
    
    function initializeDurationPicker() {
        // Wait for modal DOM to be ready
        setTimeout(function() {
            if (typeof window.initDurationPicker === 'function') {
                window.initDurationPicker();
            } else {
                // Fallback if function not ready yet
                setTimeout(initializeDurationPicker, 200);
            }
        }, 300);
    }

    function closeTaskModal() {
        document.getElementById('taskModal').classList.remove('active');
        document.body.style.overflow = 'auto';
        taskRows = [];
        currentEventId = null;
        isSubmitting = false; // Reset submission flag
        const taskForm = document.getElementById('taskForm');
        if (taskForm) {
            taskForm.reset();
            // Reset ETC field to default value of 10
            $('input[name="eta_time"]').val('10');
            const $submitBtn = $(taskForm).find('button[type="submit"]');
            $submitBtn.prop('disabled', false).html('<span>💾</span> <span>Submit All Tasks</span>');
        }
        // Destroy duration picker when modal closes
        if ($('#duration').data('daterangepicker')) {
            $('#duration').data('daterangepicker').remove();
        }
        $('#duration').val('');
        $('#start_date').val('');
        $('#end_date').val('');
        document.getElementById('tasksTableBody').innerHTML = `
            <tr class="empty-tasks">
                <td colspan="6">
                    <div class="empty-tasks-icon">📝</div>
                    <p>No tasks added yet. Click "Add More" to create tasks!</p>
                </td>
            </tr>
        `;
    }

    function addTaskRow() {
        const form = document.getElementById('taskForm');
        const formData = new FormData(form);
        
        // Validate required fields (same as task board)
        const task = formData.get('task');
        const assignor = formData.get('assignor');
        const assignee = formData.get('assignee');
        const etaTime = formData.get('eta_time');
        
        if (!task || !task.trim()) {
            $.alert({
                title: '⚠️ Validation Error',
                content: 'Task name is required.',
                type: 'orange',
                boxWidth: '30%',
                useBootstrap: false
            });
            form.task.focus();
            return;
        }
        
        if (!assignor) {
            $.alert({
                title: '⚠️ Validation Error',
                content: 'Assignor is required.',
                type: 'orange',
                boxWidth: '30%',
                useBootstrap: false
            });
            form.assignor.focus();
            return;
        }
        
        if (!assignee) {
            $.alert({
                title: '⚠️ Validation Error',
                content: 'Assignee is required.',
                type: 'orange',
                boxWidth: '30%',
                useBootstrap: false
            });
            form.assignee.focus();
            return;
        }
        
        // Validate ETC if provided (same as task board)
        if (etaTime) {
            const etaTimeNum = parseInt(etaTime);
            if (isNaN(etaTimeNum) || etaTimeNum <= 0) {
                $.alert({
                    title: '⚠️ Validation Error',
                    content: 'ETC (Min) must be greater than 0.',
                    type: 'orange',
                    boxWidth: '30%',
                    useBootstrap: false
                });
                form.eta_time.focus();
                return;
            }
        }
        
        // Get start_date and end_date from hidden fields (they should be updated by daterangepicker)
        const startDate = $('#start_date').val() || formData.get('start_date') || null;
        const endDate = $('#end_date').val() || formData.get('end_date') || null;
        let durationValue = $('#duration').val() || formData.get('duration') || '';
        
        // If duration is empty but we have dates, format them
        if (!durationValue && startDate && endDate) {
            try {
                const start = moment(startDate, 'YYYY-MM-DD HH:mm:ss');
                const end = moment(endDate, 'YYYY-MM-DD HH:mm:ss');
                if (start.isValid() && end.isValid()) {
                    durationValue = start.format('MMM D, YY hh:mm A') + ' - ' + end.format('MMM D, YY hh:mm A');
                }
            } catch (e) {
                console.error('Error formatting duration:', e);
            }
        }
        
        // Debug logging
        console.log('Duration field value:', durationValue);
        console.log('Start date hidden field:', startDate);
        console.log('End date hidden field:', endDate);
        
        // Create task object (explicitly without id to mark it as new)
        const taskObj = {
            // Explicitly do NOT include 'id' field - this marks it as a new task
            group: formData.get('group') || '',
            task: task.trim(),
            assignor: form.assignor.options[form.assignor.selectedIndex].text,
            assignor_id: assignor,
            assignee: form.assignee.options[form.assignee.selectedIndex].text,
            assignee_id: assignee,
            start_date: startDate,
            end_date: endDate,
            duration: durationValue,
            status: form.status.options[form.status.selectedIndex] ? form.status.options[form.status.selectedIndex].text : '',
            status_value: formData.get('status') || '',
            priority: form.priority.options[form.priority.selectedIndex] ? form.priority.options[form.priority.selectedIndex].text : '',
            priority_value: formData.get('priority') || '',
            eta_time: etaTime || '',
            l1: formData.get('l1') || '',
            l2: formData.get('l2') || '',
            l3: formData.get('l3') || '',
            l4: formData.get('l4') || '',
            l5: formData.get('l5') || '',
            l6: formData.get('l6') || '',
            l7: formData.get('l7') || '',
            link: ''
        };
        
        // Ensure no id is set (in case it was accidentally added)
        delete taskObj.id;
        
        console.log('Adding new task (no ID):', taskObj.task);
        taskRows.push(taskObj);
        renderTasksTable();
        
        // Store current duration values before reset
        var currentStart = $('#start_date').val();
        var currentEnd = $('#end_date').val();
        var currentDuration = $('#duration').val();
        
        form.reset();
        
        // Reset ETC field to default value of 10
        $('input[name="eta_time"]').val('10');
        
        // Reset and reinitialize duration picker after form reset
        setTimeout(function() {
            if ($('#duration').data('daterangepicker')) {
                var start = moment();
                var end = moment(start).add(4, 'days');
                $('#duration').data('daterangepicker').setStartDate(start);
                $('#duration').data('daterangepicker').setEndDate(end);
                $('#duration').val(start.format('MMM D, YY hh:mm A') + ' - ' + end.format('MMM D, YY hh:mm A'));
                $('#start_date').val(start.format('YYYY-MM-DD HH:mm:ss'));
                $('#end_date').val(end.format('YYYY-MM-DD HH:mm:ss'));
            } else {
                // Reinitialize if picker was destroyed
                initializeDurationPicker();
            }
        }, 100);
    }

    function renderTasksTable() {
        const tbody = document.getElementById('tasksTableBody');
        
        if (taskRows.length === 0) {
            tbody.innerHTML = `
                <tr class="empty-tasks">
                    <td colspan="6">
                        <div class="empty-tasks-icon">📝</div>
                        <p>No tasks added yet. Click "Add More" to create tasks!</p>
                    </td>
                </tr>
            `;
            return;
        }
        
        tbody.innerHTML = taskRows.map((task, index) => `
            <tr>
                <td style="width: 20%;"><input type="text" class="task-row-input" value="${task.group}" data-index="${index}" data-field="group" ${task.id ? 'disabled' : ''}></td>
                <td style="width: 30%;"><input type="text" class="task-row-input" value="${task.task}" data-index="${index}" data-field="task" ${task.id ? 'disabled' : ''}></td>
                <td>${task.assignor}</td>
                <td>${task.assignee}</td>
                <td>
  <button class="link-btn" onclick="addLink(${index})" title="Add Links" ${task.id ? 'disabled' : ''}>🔗</button>
  <div style="text-align:center;font-size:12px;margin-top:3px;display:none">
    ${
      [task.l1, task.l2, task.l3, task.l4, task.l5, task.l6, task.l7]
        .filter(l => l)
        .map(l => `<a href="${l}" target="_blank" style="margin:0 2px;">🔗</a>`)
        .join('') || '—'
    }
  </div>
</td>

                <td><button class="delete-row-btn" onclick="deleteTaskRow(${index})" title="Delete" ${task.id ? 'disabled' : ''}>✕</button></td>
            </tr>
        `).join('');
        
        // Add event listeners for inline editing
        document.querySelectorAll('.task-row-input:not([disabled])').forEach(input => {
            input.addEventListener('change', function() {
                const index = parseInt(this.dataset.index);
                const field = this.dataset.field;
                taskRows[index][field] = this.value;
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



    // Handle form submission - prevent duplicate submissions
    let isSubmitting = false;
    
    // Use event delegation to handle form submission
    $(document).off('submit', '#taskForm').on('submit', '#taskForm', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Prevent duplicate submissions
        if (isSubmitting) {
            console.log('Submission already in progress, ignoring duplicate submit');
            return false;
        }
        
        // Validate form fields before submission (manual validation since we disabled HTML5 validation)
        const form = document.getElementById('taskForm');
        const taskInput = form.querySelector('input[name="task"]');
        const assignorSelect = form.querySelector('select[name="assignor"]');
        const assigneeSelect = form.querySelector('select[name="assignee"]');
        
        // Check if there are tasks in the table OR if form has valid data
        // If form has data but taskRows is empty, user might be trying to submit without adding to table first
        if (taskRows.length === 0) {
            // Check if form has data that should be added first
            if (taskInput.value.trim() || assignorSelect.value || assigneeSelect.value) {
                $.alert({
                    title: '⚠️ Add Task First',
                    content: 'Please click "Add More" to add the task to the list before submitting.',
                    type: 'orange',
                    boxWidth: '30%',
                    useBootstrap: false
                });
                return false;
            }
            
            $.alert({
                title: '⚠️ No Tasks',
                content: 'Please add at least one task before submitting.',
                type: 'orange',
                boxWidth: '30%',
                useBootstrap: false
            });
            return false;
        }
        
        // Filter to only new tasks (without id)
        const newTasks = taskRows.filter(task => !task.id);
        
        // Debug logging
        console.log('Total taskRows:', taskRows.length);
        console.log('Task rows with IDs:', taskRows.filter(task => task.id).map(t => ({id: t.id, task: t.task})));
        console.log('New tasks (without ID):', newTasks.length);
        console.log('New tasks details:', newTasks.map(t => ({task: t.task, hasId: !!t.id})));
        
        if (newTasks.length === 0) {
            $.alert({
                title: 'ℹ️ No New Tasks',
                content: 'All tasks are already saved. Please add a new task using the form above before submitting.',
                boxWidth: '30%',
                useBootstrap: false
            });
            return false;
        }
        
        // Set submitting flag and disable submit button
        isSubmitting = true;
        const $submitBtn = $(this).find('button[type="submit"]');
        const originalBtnText = $submitBtn.html();
        $submitBtn.prop('disabled', true).html('<span>⏳</span> <span>Submitting...</span>');
        
        // Validate event_id is set
        if (!currentEventId) {
            $.alert({
                title: '⚠️ Error',
                content: 'Event ID is missing. Please close and reopen the task modal.',
                type: 'red',
                boxWidth: '30%',
                useBootstrap: false
            });
            isSubmitting = false;
            $submitBtn.prop('disabled', false).html(originalBtnText);
            return false;
        }
        
        // Submit new tasks via AJAX
        let formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('event_id', currentEventId);
        
        console.log('Preparing to submit:', {
            eventId: currentEventId,
            newTasksCount: newTasks.length,
            newTasks: newTasks
        });
        
        newTasks.forEach((task, index) => {
            Object.keys(task).forEach(key => {
                // Skip 'id' field if it exists (shouldn't, but just in case)
                if (key !== 'id') {
                    const value = task[key];
                    // Handle null/undefined values - convert to empty string
                    const fieldValue = value !== null && value !== undefined ? value : '';
                    formData.append(`tasks[${index}][${key}]`, fieldValue);
                    console.log(`tasks[${index}][${key}] =`, fieldValue);
                }
            });
        });
        
        console.log('Submitting ' + newTasks.length + ' new task(s) to backend');
        console.log('FormData entries:', Array.from(formData.entries()));

        $.ajax({
            url: "{{ route('tasks.staging.submit.tasks') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
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
                let errorMessage = 'Something went wrong.';
                
                // Handle validation errors
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.errors) {
                        // Laravel validation errors
                        const errors = xhr.responseJSON.errors;
                        const errorList = Object.keys(errors).map(field => {
                            return `${field}: ${errors[field].join(', ')}`;
                        }).join('<br>');
                        errorMessage = 'Validation errors:<br>' + errorList;
                    } else if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                } else if (xhr.responseText) {
                    // Try to parse HTML response
                    try {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(xhr.responseText, 'text/html');
                        const errorElement = doc.querySelector('.error, .alert-danger, [class*="error"]');
                        if (errorElement) {
                            errorMessage = errorElement.textContent.trim();
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
                    }
                }
                
                console.error('Submission error:', {
                    status: xhr.status,
                    response: xhr.responseJSON,
                    responseText: xhr.responseText
                });
                
                $.alert({
                    title: '⚠️ Validation Error',
                    content: errorMessage,
                    type: 'red',
                    boxWidth: '40%',
                    useBootstrap: false
                });
                // Re-enable button on error
                isSubmitting = false;
                $submitBtn.prop('disabled', false).html(originalBtnText);
            },
            complete: function() {
                // Reset flag after a delay to prevent rapid re-submissions
                setTimeout(function() {
                    isSubmitting = false;
                }, 2000);
            }
        });
        
        return false;
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

<script>
    const stages = @json($stages);
    
    // Initialize duration picker when page loads (same pattern as task board)
    $(function () {
        // This will be called when modal opens via initializeDurationPicker()
        window.initDurationPicker = function() {
            var $durationField = $('#duration');
            if ($durationField.length === 0) return;
            
            // Destroy existing picker if any
            if ($durationField.data('daterangepicker')) {
                $durationField.data('daterangepicker').remove();
            }
            
            var start = moment();
            var end = moment(start).add(4, 'days');

            function cb(start, end) {
                if (start && end && start.isValid() && end.isValid()) {
                    $durationField.val(start.format('MMM D, YY hh:mm A') + ' - ' + end.format('MMM D, YY hh:mm A'));
                    $('#start_date').val(start.format('YYYY-MM-DD HH:mm:ss'));
                    $('#end_date').val(end.format('YYYY-MM-DD HH:mm:ss'));
                    console.log('Duration picker callback - Start:', start.format('YYYY-MM-DD HH:mm:ss'), 'End:', end.format('YYYY-MM-DD HH:mm:ss'));
                }
            }

            $durationField.daterangepicker({
                autoApply: true,
                timePicker: true,
                autoUpdateInput: false,
                startDate: start,
                endDate: end,
                locale: {
                    format: 'MMMM D, YYYY hh:mm A',
                    applyLabel: "Apply",
                    cancelLabel: "Cancel",
                    fromLabel: "From",
                    toLabel: "To",
                    daysOfWeek: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
                    monthNames: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
                    firstDay: 1
                }
            }, cb);

            // Handle when dates are applied/selected (important for autoApply: true)
            $durationField.on('apply.daterangepicker', function(ev, picker) {
                var start = picker.startDate;
                var end = picker.endDate;
                console.log('Date range applied - Start:', start.format('YYYY-MM-DD HH:mm:ss'), 'End:', end.format('YYYY-MM-DD HH:mm:ss'));
                cb(start, end);
            });

            // Also handle when picker is shown (for manual selection)
            $durationField.on('show.daterangepicker', function(ev, picker) {
                console.log('Date range picker shown');
            });

            // Initial callback to set default values
            cb(start, end);
        };
    });
</script>

@endpush