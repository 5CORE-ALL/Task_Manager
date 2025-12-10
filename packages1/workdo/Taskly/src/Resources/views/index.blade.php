@php
$id = '';
if(isset($_GET['id']) && !empty($_GET['id'])){
    $id = $_GET['id'];
}
@endphp
@extends('layouts.main')
@push('css')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="{{ asset('css/new.css') }}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css"/>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css"> 
 <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
   <style>
       

        .top-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: white;
            padding: 20px 30px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }

        .user-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2em;
        }

        .user-name {
            font-size: 1.3em;
            font-weight: 600;
            color: #1f2937;
        }

        .header-actions {
            display: flex;
            gap: 15px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 10px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.4);
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .dashboard-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            transition: all 0.3s;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 48px rgba(0,0,0,0.15);
        }

        .dashboard-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: #3b82f6;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8em;
            margin-bottom: 15px;
        }

        .card-title {
            font-size: 1.3em;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .card-description {
            color: #6b7280;
            font-size: 0.9em;
            line-height: 1.5;
        }

        .card-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
        }

        .badge-cyan {
            background: #cffafe;
            color: #0891b2;
        }

        .badge-green {
            background: #d1fae5;
            color: #059669;
        }

        .badge-orange {
            background: #fed7aa;
            color: #ea580c;
        }

        .badge-pink {
            background: #fce7f3;
            color: #db2777;
        }

        .badge-purple {
            background: #e9d5ff;
            color: #9333ea;
        }

        .badge-blue {
            background: #dbeafe;
            color: #2563eb;
        }

        .badge-gray {
            background: #e5e7eb;
            color: #4b5563;
        }

        .badge-red {
            background: #fecaca;
            color: #dc2626;
        }

        .badge-yellow {
            background: #fef3c7;
            color: #d97706;
        }

        .badge-teal {
            background: #ccfbf1;
            color: #0d9488;
        }

        .badge-indigo {
            background: #e0e7ff;
            color: #4f46e5;
        }

        .badge-brown {
            background: #fef3c7;
            color: #92400e;
        }

        .subcards-preview {
            display: flex;
            gap: 8px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #f3f4f6;
            flex-wrap: wrap;
        }

        .subcard-item {
            padding: 6px 12px;
            border-radius: 8px;
            background: #f9fafb;
            font-size: 0.8em;
            color: #4b5563;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .graphs-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .graph-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }

        .graph-title {
            font-size: 1.2em;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 20px;
        }

        .chart-container {
            position: relative;
            height: 250px;
        }

        .bar {
            display: flex;
            align-items: flex-end;
            justify-content: space-around;
            height: 100%;
            gap: 10px;
        }

        .bar-item {
            flex: 1;
            background: #3b82f6;
            border-radius: 8px 8px 0 0;
            position: relative;
            transition: all 0.3s;
        }

        .bar-item:hover {
            opacity: 0.8;
            transform: translateY(-5px);
        }

        .bar-label {
            position: absolute;
            bottom: -25px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.8em;
            color: #6b7280;
            white-space: nowrap;
        }

        .bar-value {
            position: absolute;
            top: -25px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.85em;
            font-weight: 600;
            color: #1f2937;
        }

        .donut-chart {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: conic-gradient(
                #3b82f6 0deg 120deg,
                #10b981 120deg 240deg,
                #f59e0b 240deg 300deg,
                #ef4444 300deg 360deg
            );
            position: relative;
            margin: 0 auto;
        }

        .donut-center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 120px;
            height: 120px;
            background: white;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .donut-value {
            font-size: 2em;
            font-weight: 700;
            color: #1f2937;
        }

        .donut-label {
            font-size: 0.8em;
            color: #6b7280;
        }

        .legend {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 20px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .legend-color {
            width: 16px;
            height: 16px;
            border-radius: 4px;
        }

        .legend-text {
            font-size: 0.85em;
            color: #4b5563;
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-box {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stat-number {
            font-size: 2.5em;
            font-weight: 700;
            color: #3b82f6;
        }

        .stat-label {
            color: #6b7280;
            font-size: 0.9em;
            margin-top: 8px;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(5px);
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 20px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-title {
            font-size: 1.8em;
            font-weight: 600;
            color: #1f2937;
        }

        .close-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #f3f4f6;
            border: none;
            font-size: 1.5em;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .close-btn:hover {
            background: #e5e7eb;
            transform: rotate(90deg);
        }

        .subcard-grid {
            display: grid;
            gap: 15px;
        }

        .subcard {
            background: #f9fafb;
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid #3b82f6;
            transition: all 0.3s;
        }

        .subcard:hover {
            background: #f3f4f6;
            transform: translateX(5px);
        }

        .subcard-title {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 5px;
        }

        .subcard-value {
            font-size: 1.5em;
            font-weight: 700;
            color: #3b82f6;
        }

        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .graphs-section {
                grid-template-columns: 1fr;
            }

            .stats-row {
                grid-template-columns: repeat(2, 1fr);
            }

            .top-header {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
@endpush


<!-- Buttons and dependencies -->
 @push('scripts') 
 <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js"></script>
 <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush
@section('content')

<!--<div class="loader-wrapper" id="loader-dash" style="display:none">-->
<!--        <div class="loader-container">-->
<!--               <img src="{{asset('images/1920 x 557.png')}}" alt="" class="logo logo-lg" style="width:308px !important" />-->
<!--            <div class="loader-text">5Core</div>-->
<!--            <div class="loader-subtitle">Loading your dashboard...</div>-->
<!--            <div class="loader-progress">-->
<!--                <div class="loader-progress-bar"></div>-->
<!--            </div>-->
<!--            <div class="loader-dots">-->
<!--                <div class="loader-dot"></div>-->
<!--                <div class="loader-dot"></div>-->
<!--                <div class="loader-dot"></div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->

<!-- DASHBOARD SETUP POPUP -->
 @if(session()->has('impersonator_id'))
    <!--<a href="{{ route('stop.impersonate') }}" class="btn btn-warning btn-sm">-->
    <!--    Exit Impersonation-->
    <!--</a>-->
@endif
<!--<div id="cardSetupModal" class="modal-overlay">-->
<!--    <div class="modal-box">-->
<!--        <h2>Select Cards to Display</h2>-->
<!--        <p class="modal-sub">Choose which dashboard cards you want to see.</p>-->

<!--        <div class="modal-options">-->
<!--            <label><input type="checkbox" value="tasks" checked> Tasks</label>-->
<!--            <label><input type="checkbox" value="team" checked> Team</label>            -->
<!--            <label><input type="checkbox" value="inventory" checked> Inventory</label>-->
<!--            <label><input type="checkbox" value="sales" checked> Sales</label>-->
<!--            <label><input type="checkbox" value="operation" checked> Operation</label>-->
<!--            <label><input type="checkbox" value="hr" checked> Human Resources</label>-->
<!--            <label><input type="checkbox" value="software" checked> Software & IT</label>-->
<!--            <label><input type="checkbox" value="purchase" checked> Purchase</label>-->
<!--            <label><input type="checkbox" value="logistics" checked> Logistics</label>-->
<!--            <label><input type="checkbox" value="video" checked> Video</label>-->
<!--            <label><input type="checkbox" value="social_media" checked> Social Media</label>-->
<!--            <label><input type="checkbox" value="marketing" checked> Marketing</label>-->
<!--            <label><input type="checkbox" value="content" checked> Content</label>-->
<!--            <label><input type="checkbox" value="advertisments" checked> Advertisments</label>-->
<!--            <label><input type="checkbox" value="pricing" checked> Pricing</label>-->
<!--            <label><input type="checkbox" value="profit" checked> Profit</label>-->
<!--        </div>-->

<!--        <button class="modal-save" onclick="saveDashboardPreferences()">Save</button>-->
<!--    </div>-->
<!--</div>-->



<!--<div id="mainView">-->
<!--    <button class="manage-dashboard-btn" onclick="openCardManager()">-->
<!--    Manage Dashboard Cards-->
<!--</button>-->
   <div class="top-header">
        <div class="user-section">
            <div class="user-avatar">VT</div>
            <div>
                <div class="user-name">Vivek Thakur</div>
                <div style="color: #6b7280; font-size: 0.9em;">Clocks</div>
            </div>
        </div>
        <div class="header-actions">
            <button class="btn btn-primary">Manage Dashboard Cards</button>
        </div>
    </div>

    <div class="stats-row">
        <!--<a href="{{ route('taskly.sales.dashboard') }}" target="_blank"><div class="stat-box">-->
            <a href="https://inventory.5coremanagement.com/index" target="_blank"><div class="stat-box">
            <div class="stat-number">{{ round($total_l30_sales) }}</div>
            <div class="stat-label">Sales</div>
        </div></a>
        <div class="stat-box">
            <div class="stat-number">{{$total_avgPft}}</div>
            <div class="stat-label">Profit</div>
        </div>
        <div class="stat-box">
            <div class="stat-number">89%</div>
            <div class="stat-label">Acos%</div>
        </div>
        <div class="stat-box">
            <div class="stat-number">324</div>
            <div class="stat-label">L30 Orders</div>
        </div>
    </div>

    <div class="graphs-section">
        <div class="graph-card">
            <div class="graph-title">Monthly Performance</div>
            <div class="chart-container">
                <div class="bar">
                    <div class="bar-item" style="height: 60%;">
                        <span class="bar-value">60</span>
                        <span class="bar-label">Jan</span>
                    </div>
                    <div class="bar-item" style="height: 80%;">
                        <span class="bar-value">80</span>
                        <span class="bar-label">Feb</span>
                    </div>
                    <div class="bar-item" style="height: 70%;">
                        <span class="bar-value">70</span>
                        <span class="bar-label">Mar</span>
                    </div>
                    <div class="bar-item" style="height: 90%;">
                        <span class="bar-value">90</span>
                        <span class="bar-label">Apr</span>
                    </div>
                    <div class="bar-item" style="height: 75%;">
                        <span class="bar-value">75</span>
                        <span class="bar-label">May</span>
                    </div>
                    <div class="bar-item" style="height: 85%;">
                        <span class="bar-value">85</span>
                        <span class="bar-label">Jun</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="graph-card">
            <div class="graph-title">Project Distribution</div>
            <div class="chart-container">
                <div class="donut-chart">
                    <div class="donut-center">
                        <div class="donut-value">100%</div>
                        <div class="donut-label">Total</div>
                    </div>
                </div>
            </div>
            <div class="legend">
                <div class="legend-item">
                    <div class="legend-color" style="background: #3b82f6;"></div>
                    <span class="legend-text">Tasks (33%)</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #10b981;"></div>
                    <span class="legend-text">Projects (33%)</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #f59e0b;"></div>
                    <span class="legend-text">Reviews (17%)</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #ef4444;"></div>
                    <span class="legend-text">Others (17%)</span>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="dashboard-card" onclick="openModal('Tasks')">
            <div class="card-icon" style="background: #cffafe;">✓</div>
            <div class="card-header">
                <div>
                    <div class="card-title">Tasks</div>
                    <div class="card-description">Manage your tasks, assigned tasks, and track progress</div>
                </div>
                <span class="card-badge badge-cyan">31 Items</span>
            </div>
            <div class="subcards-preview">
                <span class="subcard-item">📋 My Tasks</span>
                <span class="subcard-item">👥 Team Tasks</span>
                <span class="subcard-item">✓ Completed</span>
            </div>
        </div>

        <div class="dashboard-card" onclick="openModal('My Team')">
            <div class="card-icon" style="background: #d1fae5;">👥</div>
            <div class="card-header">
                <div>
                    <div class="card-title">My Team</div>
                    <div class="card-description">View team members and performance metrics</div>
                </div>
                <span class="card-badge badge-green">1 Members</span>
            </div>
            <div class="subcards-preview">
                <span class="subcard-item">👤 Members</span>
                <span class="subcard-item">📊 Performance</span>
                <span class="subcard-item">🎯 Goals</span>
            </div>
        </div>

        <div class="dashboard-card" onclick="openModal('Inventory')">
            <div class="card-icon" style="background: #fed7aa;">📦</div>
            <div class="card-header">
                <div>
                    <div class="card-title">Inventory</div>
                    <div class="card-description">Inventory values</div>
                </div>
                <span class="card-badge badge-orange">1 Metrics</span>
            </div>
            <div class="subcards-preview">
                <span class="subcard-item">📈 Stock Levels</span>
                <span class="subcard-item">💰 Valuation</span>
            </div>
        </div>

        <div class="dashboard-card" onclick="openModal('Sales')">
            <div class="card-icon" style="background: #fef3c7;">💰</div>
            <div class="card-header">
                <div>
                    <div class="card-title">Sales</div>
                    <div class="card-description">Track sales performance</div>
                </div>
                <span class="card-badge badge-brown">286,435</span>
            </div>
            <div class="subcards-preview">
                <span class="subcard-item">🛒 E-Commerce</span>
                <span class="subcard-item">🛍️ Shopify</span>
                <span class="subcard-item">📱 Social Media</span>
                <span class="subcard-item">📦 Amazon</span>
            </div>
        </div>

        <div class="dashboard-card" onclick="openModal('Operations')">
            <div class="card-icon" style="background: #fce7f3;">⏰</div>
            <div class="card-header">
                <div>
                    <div class="card-title">Operations</div>
                    <div class="card-description">Track customer, Shipping & Reviews analyze</div>
                </div>
                <span class="card-badge badge-pink">3 Metrics</span>
            </div>
            <div class="subcards-preview">
                <span class="subcard-item">🚚 Shipping</span>
                <span class="subcard-item">⭐ Reviews</span>
                <span class="subcard-item">👥 Customers</span>
            </div>
        </div>

        <div class="dashboard-card" onclick="openModal('Human Resources')">
            <div class="card-icon" style="background: #e9d5ff;">👨‍💼</div>
            <div class="card-header">
                <div>
                    <div class="card-title">Human Resources</div>
                    <div class="card-description">Employee management & attendance tracking</div>
                </div>
                <span class="card-badge badge-purple">3 Metrics</span>
            </div>
            <div class="subcards-preview">
                <span class="subcard-item">👥 Employees</span>
                <span class="subcard-item">📅 Attendance</span>
                <span class="subcard-item">💼 Payroll</span>
            </div>
        </div>

        <div class="dashboard-card" onclick="openModal('Software & IT')">
            <div class="card-icon" style="background: #ccfbf1;">💻</div>
            <div class="card-header">
                <div>
                    <div class="card-title">Software & IT</div>
                    <div class="card-description">Generate reports and view analytics</div>
                </div>
                <span class="card-badge badge-teal">12 Items</span>
            </div>
            <div class="subcards-preview">
                <span class="subcard-item">🖥️ Systems</span>
                <span class="subcard-item">🔧 Maintenance</span>
                <span class="subcard-item">📊 Analytics</span>
            </div>
        </div>

        <div class="dashboard-card" onclick="openModal('Purchase')">
            <div class="card-icon" style="background: #1e3a5f; color: white;">🛒</div>
            <div class="card-header">
                <div>
                    <div class="card-title">Purchase</div>
                    <div class="card-description">Generate reports and view analytics</div>
                </div>
                <span class="card-badge badge-indigo">0 Metrics</span>
            </div>
            <div class="subcards-preview">
                <span class="subcard-item">🛍️ Orders</span>
                <span class="subcard-item">💳 Payments</span>
                <span class="subcard-item">📦 Suppliers</span>
            </div>
        </div>

        <div class="dashboard-card" onclick="openModal('Pricing')">
            <div class="card-icon" style="background: #fef3c7;">💵</div>
            <div class="card-header">
                <div>
                    <div class="card-title">Pricing</div>
                    <div class="card-description">Get Pricing reports and view analytics</div>
                </div>
                <span class="card-badge badge-yellow">79%</span>
            </div>
            <div class="subcards-preview">
                <span class="subcard-item">💰 Price Lists</span>
                <span class="subcard-item">📈 Trends</span>
            </div>
        </div>

        <div class="dashboard-card" onclick="openModal('Advertisements')">
            <div class="card-icon" style="background: #e5e7eb;">📢</div>
            <div class="card-header">
                <div>
                    <div class="card-title">Advertisements</div>
                    <div class="card-description">Get Advertisments reports and view analytics</div>
                </div>
                <span class="card-badge badge-gray">9 Metrics</span>
            </div>
            <div class="subcards-preview">
                <span class="subcard-item">📱 Digital Ads</span>
                <span class="subcard-item">📺 Campaigns</span>
                <span class="subcard-item">📊 ROI</span>
            </div>
        </div>

        <div class="dashboard-card" onclick="openModal('Content')">
            <div class="card-icon" style="background: #7c2d12; color: white;">📝</div>
            <div class="card-header">
                <div>
                    <div class="card-title">Content</div>
                    <div class="card-description">Get Content reports</div>
                </div>
                <span class="card-badge badge-red">0 Metrics</span>
            </div>
            <div class="subcards-preview">
                <span class="subcard-item">✍️ Articles</span>
                <span class="subcard-item">🎨 Media</span>
                <span class="subcard-item">📅 Schedule</span>
            </div>
        </div>

        <div class="dashboard-card" onclick="openModal('Marketing')">
            <div class="card-icon" style="background: #dbeafe;">🎯</div>
            <div class="card-header">
                <div>
                    <div class="card-title">Marketing</div>
                    <div class="card-description">Get Marketing analytics</div>
                </div>
                <span class="card-badge badge-blue">6 Metrics</span>
            </div>
            <div class="subcards-preview">
                <span class="subcard-item">📧 Email</span>
                <span class="subcard-item">🎯 Campaigns</span>
                <span class="subcard-item">📊 Analytics</span>
            </div>
        </div>

        <div class="dashboard-card" onclick="openModal('Social Media')">
            <div class="card-icon" style="background: #fef3c7;">📱</div>
            <div class="card-header">
                <div>
                    <div class="card-title">Social Media</div>
                    <div class="card-description">Get Social Media analytics</div>
                </div>
                <span class="card-badge badge-yellow">0 Metrics</span>
            </div>
            <div class="subcards-preview">
                <span class="subcard-item">📘 Facebook</span>
                <span class="subcard-item">📷 Instagram</span>
                <span class="subcard-item">🐦 Twitter</span>
            </div>
        </div>

        <div class="dashboard-card" onclick="openModal('Videos')">
            <div class="card-icon" style="background: #fed7aa;">🎬</div>
            <div class="card-header">
                <div>
                    <div class="card-title">Videos</div>
                    <div class="card-description">Get Videos details</div>
                </div>
                <span class="card-badge badge-orange">0 Metrics</span>
            </div>
            <div class="subcards-preview">
                <span class="subcard-item">🎥 Library</span>
                <span class="subcard-item">▶️ Views</span>
                <span class="subcard-item">👍 Engagement</span>
            </div>
        </div>

        <div class="dashboard-card" onclick="openModal('Logistics')">
            <div class="card-icon" style="background: #1e3a5f; color: white;">🚚</div>
            <div class="card-header">
                <div>
                    <div class="card-title">Logistics</div>
                    <div class="card-description">Get Logistics Track Reports</div>
                </div>
                <span class="card-badge badge-indigo">0 Metrics</span>
            </div>
            <div class="subcards-preview">
                <span class="subcard-item">📦 Shipments</span>
                <span class="subcard-item">🚛 Tracking</span>
                <span class="subcard-item">📍 Delivery</span>
            </div>
        </div>
    </div>

    <div class="modal" id="cardModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="modalTitle">Card Details</h2>
                <button class="close-btn" onclick="closeModal()">×</button>
            </div>
            <div class="subcard-grid" id="subcardContent">
            </div>
        </div>
    </div>

@endsection


@push('scripts')
   <script>
        const subcardData = {
            'Tasks': [
                { title: 'My Tasks', value: '24', icon: '📋' },
                { title: 'Team Tasks', value: '7', icon: '👥' },
                { title: 'Completed Tasks', value: '156', icon: '✓' },
                { title: 'Pending Tasks', value: '31', icon: '⏳' }
            ],
            'My Team': [
                { title: 'Total Members', value: '24', icon: '👥' },
                { title: 'Active Today', value: '18', icon: '✓' },
                { title: 'On Leave', value: '2', icon: '🏖️' },
                { title: 'Performance Score', value: '92%', icon: '📊' }
            ],
            'Inventory': [
                { title: 'Total Stock', value: '5,432', icon: '📦' },
                { title: 'Low Stock Items', value: '12', icon: '⚠️' },
                { title: 'Out of Stock', value: '3', icon: '❌' },
                { title: 'Stock Value', value: '$286,435', icon: '💰' }
            ],
            'Sales': [
                { title: 'E-Commerce Sales', value: '286,435', icon: '🛒' },
                { title: 'Shopify Sales', value: '0', icon: '🛍️' },
                { title: 'Social Media Sales', value: '0', icon: '📱' },
                { title: 'Amazon Sales', value: '0', icon: '📦' }
            ],
            'Operations': [
                { title: 'Shipping Orders', value: '145', icon: '🚚' },
                { title: 'Customer Reviews', value: '4.8/5', icon: '⭐' },
                { title: 'Total Customers', value: '1,542', icon: '👥' },
                { title: 'Response Time', value: '2.3hrs', icon: '⏱️' }
            ],
            'Human Resources': [
                { title: 'Total Employees', value: '87', icon: '👥' },
                { title: 'Attendance Rate', value: '94%', icon: '📅' },
                { title: 'Payroll Amount', value: '$245,000', icon: '💼' },
                { title: 'Open Positions', value: '5', icon: '📢' }
            ],
            'Software & IT': [
                { title: 'Active Systems', value: '23', icon: '🖥️' },
                { title: 'Maintenance Tasks', value: '8', icon: '🔧' },
                { title: 'Server Uptime', value: '99.9%', icon: '✓' },
                { title: 'Support Tickets', value: '12', icon: '🎫' }
            ],
            'Purchase': [
                { title: 'Total Orders', value: '342', icon: '🛍️' },
                { title: 'Pending Payments', value: '23', icon: '💳' },
                { title: 'Active Suppliers', value: '45', icon: '📦' },
                { title: 'Monthly Spend', value: '$125,000', icon: '💰' }
            ],
            'Pricing': [
                { title: 'Price Lists', value: '15', icon: '💰' },
                { title: 'Discounts Active', value: '8', icon: '🏷️' },
                { title: 'Pricing Rules', value: '23', icon: '📋' },
                { title: 'Profit Margin', value: '79%', icon: '📈' }
            ],
            'Advertisements': [
                { title: 'Active Campaigns', value: '12', icon: '📱' },
                { title: 'Ad Spend', value: '$15,430', icon: '💰' },
                { title: 'Impressions', value: '2.5M', icon: '👁️' },
                { title: 'Click Rate', value: '3.8%', icon: '🖱️' },
                { title: 'Conversions', value: '432', icon: '✓' },
                { title: 'ROI', value: '285%', icon: '📊' }
            ],
            'Content': [
                { title: 'Published Articles', value: '156', icon: '✍️' },
                { title: 'Draft Content', value: '23', icon: '📝' },
                { title: 'Media Files', value: '1,234', icon: '🎨' },
                { title: 'Scheduled Posts', value: '45', icon: '📅' }
            ],
            'Marketing': [
                { title: 'Email Campaigns', value: '18', icon: '📧' },
                { title: 'Open Rate', value: '24.5%', icon: '📊' },
                { title: 'Active Leads', value: '892', icon: '🎯' },
                { title: 'Conversion Rate', value: '8.3%', icon: '✓' },
                { title: 'Marketing Budget', value: '$45,000', icon: '💰' },
                { title: 'Events Planned', value: '6', icon: '🎪' }
            ],
            'Social Media': [
                { title: 'Facebook Followers', value: '45.2K', icon: '📘' },
                { title: 'Instagram Followers', value: '32.8K', icon: '📷' },
                { title: 'Twitter Followers', value: '18.5K', icon: '🐦' },
                { title: 'Engagement Rate', value: '6.2%', icon: '💬' },
                { title: 'Posts This Month', value: '87', icon: '📱' }
            ],
            'Videos': [
                { title: 'Total Videos', value: '245', icon: '🎥' },
                { title: 'Total Views', value: '1.2M', icon: '▶️' },
                { title: 'Watch Time', value: '45K hrs', icon: '⏱️' },
                { title: 'Subscribers', value: '23.4K', icon: '👥' },
                { title: 'Engagement', value: '8.5%', icon: '👍' }
            ],
            'Logistics': [
                { title: 'Active Shipments', value: '234', icon: '📦' },
                { title: 'In Transit', value: '87', icon: '🚛' },
                { title: 'Delivered Today', value: '42', icon: '✓' },
                { title: 'Pending Pickup', value: '15', icon: '📍' },
                { title: 'Delivery Success', value: '97%', icon: '🎯' }
            ]
        };

        function openModal(cardName) {
            const modal = document.getElementById('cardModal');
            const title = document.getElementById('modalTitle');
            const content = document.getElementById('subcardContent');
            
            title.textContent = cardName;
            
            const subcards = subcardData[cardName] || [];
            content.innerHTML = subcards.map(card => `
                <div class="subcard">
                    <div style="font-size: 2em; margin-bottom: 10px;">${card.icon}</div>
                    <div class="subcard-title">${card.title}</div>
                    <div class="subcard-value">${card.value}</div>
                </div>
            `).join('');
            
            modal.classList.add('active');
        }

        function closeModal() {
            document.getElementById('cardModal').classList.remove('active');
        }

        document.getElementById('cardModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
    </script>
   
@endpush
@push('css')
<style>
/* âœ… Make sure Select2 dropdown is always on top of jQuery Confirm */
.select2-container--open .select2-dropdown {
  z-index: 999999999 !important;
  position: absolute !important;
}

/* âœ… Raise all Select2 layers above confirm overlay */
.select2-container {
  z-index: 999999999 !important;
}

/* Optional: prevent modal from hiding dropdown content */
.jconfirm-box,
.jconfirm-content,
.jconfirm-holder,
.jconfirm-scrollpane,
.jconfirm-content-pane {
  overflow: visible !important;
}
</style>
@endpush