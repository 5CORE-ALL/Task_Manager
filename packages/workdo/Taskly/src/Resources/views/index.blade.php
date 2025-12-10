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

        /* New Metric Cards Styles */
        .dashboard-metrics-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .metric-card {
            border-radius: 15px;
            padding: 0;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .metric-pink {
            background: linear-gradient(135deg, #ff6b9d 0%, #c9184a 100%);
        }

        .metric-blue {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .metric-yellow {
            background: linear-gradient(135deg, #ffa629 0%, #ffc371 100%);
        }

        .metric-purple {
            background: linear-gradient(135deg, #a855f7 0%, #7c3aed 100%);
        }

        .metric-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 30px;
            color: white;
        }

        .metric-info {
            flex: 1;
        }

        .metric-title {
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 10px;
            opacity: 0.9;
        }

        .metric-value {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .metric-subtitle {
            font-size: 0.85rem;
            opacity: 0.9;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .metric-icon {
            font-size: 4rem;
            opacity: 0.3;
            margin-left: 20px;
        }

        /* Dashboard Charts Section */
        .dashboard-charts-section {
            display: grid;
            grid-template-columns: 35% 65%;
            gap: 20px;
            margin-bottom: 30px;
        }

        .chart-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e5e7eb;
        }

        .chart-header h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
            margin: 0;
        }

        .chart-actions {
            display: flex;
            gap: 8px;
        }

        .chart-btn {
            width: 30px;
            height: 30px;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            background: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            transition: all 0.2s;
        }

        .chart-btn:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #334155;
        }

        .chart-body {
            padding: 10px 0;
        }

        .chart-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 25px;
        }

        .btn-refresh, .btn-fullscreen {
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .btn-refresh {
            background: #06b6d4;
            color: white;
        }

        .btn-refresh:hover {
            background: #0891b2;
            transform: translateY(-2px);
        }

        .btn-fullscreen {
            background: #3b82f6;
            color: white;
        }

        .btn-fullscreen:hover {
            background: #2563eb;
            transform: translateY(-2px);
        }

        .revenue-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 20px;
            padding: 15px;
            background: #f8fafc;
            border-radius: 10px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-label {
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 5px;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
        }

        .btn-reload-metrics {
            padding: 12px 30px;
            border-radius: 8px;
            border: none;
            background: #06b6d4;
            color: white;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .btn-reload-metrics:hover {
            background: #0891b2;
            transform: translateY(-2px);
        }

        .btn-inventory-login {
            padding: 12px 30px;
            border-radius: 8px;
            border: none;
            background: #3b82f6;
            color: white;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-inventory-login:hover {
            background: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
            color: white;
            text-decoration: none;
        }

        .inventory-login-message {
            min-height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
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

        /* Chart Fullscreen Modal */
        .chart-fullscreen-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.95);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .chart-fullscreen-content {
            background: white;
            border-radius: 20px;
            width: 95%;
            height: 90%;
            padding: 30px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
        }

        .chart-fullscreen-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e5e7eb;
        }

        .chart-fullscreen-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }

        .chart-fullscreen-close {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #fee2e2;
            border: none;
            color: #dc2626;
            font-size: 1.3rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .chart-fullscreen-close:hover {
            background: #fecaca;
            transform: rotate(90deg);
        }

        .chart-fullscreen-body {
            flex: 1;
            position: relative;
            min-height: 0;
        }

        .chart-fullscreen-body canvas {
            height: 100% !important;
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

            .dashboard-metrics-row {
                grid-template-columns: 1fr;
            }

            .dashboard-charts-section {
                grid-template-columns: 1fr;
            }

            .stats-row {
                grid-template-columns: repeat(2, 1fr);
            }

            .top-header {
                flex-direction: column;
                gap: 15px;
            }

            .metric-value {
                font-size: 2rem;
            }

            .metric-icon {
                font-size: 3rem;
            }
        }

        @media (max-width: 1200px) {
            .dashboard-charts-section {
                grid-template-columns: 1fr;
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
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}{{ strtoupper(substr(strstr(auth()->user()->name ?? 'User', ' '), 1, 1)) }}</div>
            <div>
                <div class="user-name">{{ auth()->user()->name ?? 'User' }}</div>
                <div style="color: #6b7280; font-size: 0.9em;">Clocks</div>
            </div>
        </div>
        <div class="header-actions">
            <button class="btn btn-primary">Manage Dashboard Cards</button>
        </div>
    </div>

    <!-- New Dashboard Cards Design -->
    <div class="dashboard-metrics-row">
        <a href="https://inventory.5coremanagement.com/channel/channels/channel-masters" target="_blank" class="metric-card metric-pink" style="text-decoration: none; color: inherit;">
            <div class="metric-content">
                <div class="metric-info">
                    <div class="metric-title">L30 TOTAL SALES</div>
                    <div class="metric-value">${{ number_format(round($total_l30_sales)) }}</div>
                    <div class="metric-subtitle">
                        <i class="bi bi-clock"></i> Last 30 Days
                    </div>
                </div>
                <div class="metric-icon">
                    <i class="bi bi-cart"></i>
                </div>
            </div>
        </a>

        <a href="https://inventory.5coremanagement.com/channel/channels/channel-masters" target="_blank" class="metric-card metric-blue" style="text-decoration: none; color: inherit;">
            <div class="metric-content">
                <div class="metric-info">
                    <div class="metric-title">PROFIT MARGIN %</div>
                    <div class="metric-value">{{$total_avgPft}}%</div>
                    <div class="metric-subtitle">
                        <i class="bi bi-calculator"></i> Calculated from L30
                    </div>
                </div>
                <div class="metric-icon">
                    <i class="bi bi-percent"></i>
                </div>
            </div>
        </a>

        <a href="https://inventory.5coremanagement.com/channel/channels/channel-masters" target="_blank" class="metric-card metric-yellow" style="text-decoration: none; color: inherit;">
            <div class="metric-content">
                <div class="metric-info">
                    <div class="metric-title">ROI %</div>
                    <div class="metric-value">64.9%</div>
                    <div class="metric-subtitle">
                        <i class="bi bi-graph-up-arrow"></i> Return on COGS
                    </div>
                </div>
                <div class="metric-icon">
                    <i class="bi bi-coin"></i>
                </div>
            </div>
        </a>

        <a href="https://inventory.5coremanagement.com/channel/channels/channel-masters" target="_blank" class="metric-card metric-purple" style="text-decoration: none; color: inherit;">
            <div class="metric-content">
                <div class="metric-info">
                    <div class="metric-title">L30 ORDERS</div>
                    <div class="metric-value">324</div>
                    <div class="metric-subtitle">
                        <i class="bi bi-box-seam"></i> Total Orders
                    </div>
                </div>
                <div class="metric-icon">
                    <i class="bi bi-bag-check"></i>
                </div>
            </div>
        </a>
    </div>

    <!-- Dashboard Charts Section -->
    <div class="dashboard-charts-section">
        <div class="chart-card chart-left">
            <div class="chart-header">
                <h3>Sales by Channel's</h3>
                <div class="chart-actions">
                    <button class="chart-btn"><i class="bi bi-eye"></i></button>
                    <button class="chart-btn"><i class="bi bi-arrow-clockwise"></i></button>
                    <button class="chart-btn"><i class="bi bi-dash"></i></button>
                    <button class="chart-btn"><i class="bi bi-x"></i></button>
                </div>
            </div>
            <div class="chart-body">
                <canvas id="salesChannelChart" style="max-height: 350px;"></canvas>
                <div style="text-align: center; margin-top: 20px; font-weight: 600; color: #64748b;">
                    L30 Sales by Channel
                </div>
                <div class="chart-buttons">
                    <button class="btn-refresh"><i class="bi bi-arrow-clockwise"></i> Refresh Channels</button>
                    <button class="btn-fullscreen" onclick="openChartFullscreen('salesChannelChart')"><i class="bi bi-arrows-fullscreen"></i> View Fullscreen</button>
                </div>
            </div>
        </div>

        <div class="chart-card chart-right">
            <div class="chart-header">
                <h3>Revenue - Daily Sales</h3>
                <div class="chart-actions">
                    <button class="chart-btn"><i class="bi bi-arrow-clockwise"></i></button>
                    <button class="chart-btn"><i class="bi bi-dash"></i></button>
                    <button class="chart-btn"><i class="bi bi-x"></i></button>
                </div>
            </div>
            <div class="chart-body">
                <canvas id="dailySalesChart" style="max-height: 300px;"></canvas>
                <div class="revenue-stats">
                    <div class="stat-item">
                        <div class="stat-label">L30 Sales</div>
                        <div class="stat-value">${{ number_format(round($total_l30_sales)) }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Profit Margin</div>
                        <div class="stat-value">{{$total_avgPft}}%</div>
                    </div>
                </div>
                <div class="revenue-stats">
                    <div class="stat-item">
                        <div class="stat-label">ROI</div>
                        <div class="stat-value">64.9%</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Total Profit</div>
                        <div class="stat-value">$64.0k</div>
                    </div>
                </div>
                <div style="text-align: center; margin-top: 20px;">
                    <button class="btn-reload-metrics"><i class="bi bi-arrow-clockwise"></i> Reload Metrics</button>
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
        // Sales Channel Chart (Horizontal Bar Chart)
        const salesChannelCtx = document.getElementById('salesChannelChart');
        if (salesChannelCtx) {
            new Chart(salesChannelCtx, {
                type: 'bar',
                data: {
                    labels: ['Amazon', 'Tiktok Shop', 'Ebay', 'BestBuy', 'Walmart', 'Wayfair', 'Reverb', 'Alibaba', 'AlExpress', 'Wholesale Central', 'Business Dime', 'Others', 'Kirvano.net', 'Kirvano', 'Mercantile Shop', 'Shopify App'],
                    datasets: [{
                        label: 'L30 Sales ($)',
                        data: [120000, 45000, 35000, 28000, 25000, 20000, 15000, 12000, 10000, 8000, 7000, 6000, 5000, 4000, 3000, 2000],
                        backgroundColor: [
                            '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6',
                            '#ec4899', '#06b6d4', '#84cc16', '#f97316', '#14b8a6',
                            '#a855f7', '#6366f1', '#eab308', '#22c55e', '#fb923c', '#0ea5e9'
                        ],
                        borderRadius: 6,
                        barThickness: 20,
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return '$' + context.parsed.x.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + (value / 1000) + 'K';
                                }
                            },
                            grid: {
                                display: true,
                                color: '#f1f5f9'
                            }
                        },
                        y: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });
        }

        // Daily Sales Chart (Line Chart) - Dynamic Data
        const dailySalesCtx = document.getElementById('dailySalesChart');
        if (dailySalesCtx) {
            const chartParent = dailySalesCtx.parentElement;
            
            // Fetch data from API
            fetch('https://inventory.5coremanagement.com/sales-trend-data')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Authentication required');
                    }
                    return response.json();
                })
                .then(apiData => {
                    // Process the API data
                    const dates = [];
                    const salesData = [];
                    
                    // Check if data exists and is an array
                    if (apiData && Array.isArray(apiData) && apiData.length > 0) {
                        apiData.forEach(item => {
                            // Adjust based on actual API response structure
                            // Assuming the API returns objects with 'date' and 'sales' properties
                            dates.push(item.date || item.Date || item.label);
                            salesData.push(parseFloat(item.sales || item.Sales || item.value || 0));
                        });
                        
                        // Create the chart with fetched data
                        new Chart(dailySalesCtx, {
                            type: 'line',
                            data: {
                                labels: dates,
                                datasets: [{
                                    label: 'L30 Daily Sales',
                                    data: salesData,
                                    borderColor: '#3b82f6',
                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                    fill: true,
                                    tension: 0.4,
                                    pointRadius: 4,
                                    pointBackgroundColor: '#3b82f6',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    pointHoverRadius: 6,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'top',
                                        labels: {
                                            usePointStyle: true,
                                            padding: 15
                                        }
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                return '$' + context.parsed.y.toLocaleString();
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        grid: {
                                            display: false
                                        },
                                        ticks: {
                                            maxRotation: 45,
                                            minRotation: 45,
                                            font: {
                                                size: 10
                                            }
                                        }
                                    },
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: function(value) {
                                                return '$' + (value / 1000) + 'K';
                                            }
                                        },
                                        grid: {
                                            color: '#f1f5f9'
                                        }
                                    }
                                }
                            }
                        });
                    } else {
                        // No data returned - show login message
                        showInventoryLoginMessage(chartParent);
                    }
                })
                .catch(error => {
                    console.error('Error fetching sales trend data:', error);
                    // Show login message for authentication errors
                    showInventoryLoginMessage(chartParent);
                });
        }

        function showInventoryLoginMessage(container) {
            const canvas = container.querySelector('canvas');
            if (canvas) {
                canvas.style.display = 'none';
            }
            
            const messageDiv = document.createElement('div');
            messageDiv.className = 'inventory-login-message';
            messageDiv.innerHTML = `
                <div style="text-align: center; padding: 60px 20px;">
                    <i class="bi bi-lock" style="font-size: 4rem; color: #94a3b8; margin-bottom: 20px;"></i>
                    <h3 style="color: #475569; font-size: 1.3rem; margin-bottom: 10px;">Authentication Required</h3>
                    <p style="color: #64748b; margin-bottom: 25px;">Please login to inventory to view this graph</p>
                    <a href="https://inventory.5coremanagement.com/auth/login" target="_blank" 
                       class="btn-inventory-login">
                        <i class="bi bi-box-arrow-in-right"></i> Login to Inventory
                    </a>
                </div>
            `;
            container.insertBefore(messageDiv, canvas);
        }

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

        // Fullscreen Chart Functionality
        function openChartFullscreen(chartId) {
            const chartCanvas = document.getElementById(chartId);
            const chartCard = chartCanvas.closest('.chart-card');
            
            // Create fullscreen modal
            const fullscreenModal = document.createElement('div');
            fullscreenModal.className = 'chart-fullscreen-modal';
            fullscreenModal.innerHTML = `
                <div class="chart-fullscreen-content">
                    <div class="chart-fullscreen-header">
                        <h2>Sales by Channel's - Fullscreen View</h2>
                        <button class="chart-fullscreen-close" onclick="closeChartFullscreen()">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="chart-fullscreen-body">
                        <canvas id="fullscreenChart"></canvas>
                    </div>
                </div>
            `;
            document.body.appendChild(fullscreenModal);
            
            // Clone and recreate chart in fullscreen
            setTimeout(() => {
                const fullscreenCtx = document.getElementById('fullscreenChart');
                new Chart(fullscreenCtx, {
                    type: 'bar',
                    data: {
                        labels: ['Amazon', 'Tiktok Shop', 'Ebay', 'BestBuy', 'Walmart', 'Wayfair', 'Reverb', 'Alibaba', 'AlExpress', 'Wholesale Central', 'Business Dime', 'Others', 'Kirvano.net', 'Kirvano', 'Mercantile Shop', 'Shopify App'],
                        datasets: [{
                            label: 'L30 Sales ($)',
                            data: [120000, 45000, 35000, 28000, 25000, 20000, 15000, 12000, 10000, 8000, 7000, 6000, 5000, 4000, 3000, 2000],
                            backgroundColor: [
                                '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6',
                                '#ec4899', '#06b6d4', '#84cc16', '#f97316', '#14b8a6',
                                '#a855f7', '#6366f1', '#eab308', '#22c55e', '#fb923c', '#0ea5e9'
                            ],
                            borderRadius: 6,
                            barThickness: 25,
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return '$' + context.parsed.x.toLocaleString();
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '$' + (value / 1000) + 'K';
                                    },
                                    font: {
                                        size: 12
                                    }
                                },
                                grid: {
                                    display: true,
                                    color: '#f1f5f9'
                                }
                            },
                            y: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 14
                                    }
                                }
                            }
                        }
                    }
                });
            }, 100);
        }

        function closeChartFullscreen() {
            const modal = document.querySelector('.chart-fullscreen-modal');
            if (modal) {
                modal.remove();
            }
        }

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeChartFullscreen();
            }
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