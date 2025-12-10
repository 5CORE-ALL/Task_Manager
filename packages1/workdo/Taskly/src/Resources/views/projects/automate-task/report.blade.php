<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css"/>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css">

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<!-- Buttons and dependencies -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <title>5Core</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Source Sans Pro', sans-serif;
            background: #ecf0f5;
            font-size: 14px;
            line-height: 1.6;
        }

        /* Header */
        .main-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 59px;
            background: #e5e5e5;
            z-index: 1030;
            display: flex;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            animation: slideDown 0.5s ease-out;
        }

        @keyframes slideDown {
            from { transform: translateY(-100%); }
            to { transform: translateY(0); }
        }

        .logo {
            width: 230px;
            background: #e5e5e5;
            color: white;
            display: flex;
            align-items: center;
            padding: 0 15px;
            font-size: 20px;
            font-weight: 300;
        }

        .navbar {
            flex: 1;
            display: flex;
            align-items: center;
            padding: 0 15px;
        }

        .sidebar-toggle {
            color: white;
            font-size: 20px;
            cursor: pointer;
            padding: 15px;
        }

        .navbar-nav {
            display: flex;
            list-style: none;
            margin-left: auto;
            gap: 5px;
        }

        .navbar-nav li a {
            color: white;
            padding: 15px;
            display: flex;
            align-items: center;
            text-decoration: none;
            position: relative;
        }

        .navbar-nav li a:hover {
            background: rgba(0,0,0,0.1);
        }

        .label {
            position: absolute;
            top: 7px;
            right: 7px;
            padding: 2px 5px;
            font-size: 10px;
            border-radius: 3px;
            background: #dd4b39;
            color: white;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        /* Sidebar */
        .main-sidebar {
            position: fixed;
            top: 50px;
            left: 0;
            width: 230px;
            height: calc(100vh - 50px);
            background: #ffffff;
            overflow-y: auto;
            z-index: 1020;
            animation: slideRight 0.5s ease-out;
        }

        @keyframes slideRight {
            from { transform: translateX(-100%); }
            to { transform: translateX(0); }
        }

        .user-panel {
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 1px solid #2c3b41;
        }

        .user-panel .image {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff6f28, #ff6f28);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
        }

        .user-panel .info {
            color: #000000ff;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu li a {
            padding: 12px 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #000000ff;
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: all 0.3s;
        }

        .sidebar-menu li a:hover,
        .sidebar-menu li.active a {
            background: #ff6f28;
            border-left-color: #e5e5e5;
            color: white;
            transform: translateX(5px);
        }

        .sidebar-menu .label {
            margin-left: auto;
            background: #00a65a;
            position: static;
        }

        /* Content */
        .content-wrapper {
            /*margin-left: 230px;*/
            margin-top: 86px;
            min-height: calc(100vh - 50px);
            padding: 0;
        }

        .content-header {
            padding: 15px;
            background: white;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            animation: fadeIn 0.5s;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .content-header h1 {
            font-size: 24px;
            font-weight: 400;
            margin: 0;
        }

        .breadcrumb {
            float: right;
            list-style: none;
            display: flex;
            gap: 5px;
        }

        .breadcrumb li + li:before {
            content: "/ ";
            padding: 0 5px;
        }

        .content {
            padding: 0 15px 15px;
        }

        /* Info Boxes */
        .info-box-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .info-box {
            background: white;
            border-radius: 2px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12);
            display: flex;
            min-height: 90px;
            cursor: pointer;
            transition: all 0.3s;
            animation: boxPop 0.6s ease-out both;
        }

        .info-box:nth-child(1) { animation-delay: 0.1s; }
        .info-box:nth-child(2) { animation-delay: 0.2s; }
        .info-box:nth-child(3) { animation-delay: 0.3s; }
        .info-box:nth-child(4) { animation-delay: 0.4s; }

        @keyframes boxPop {
            from {
                opacity: 0;
                transform: scale(0.8) translateY(30px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .info-box:hover {
            box-shadow: 0 14px 28px rgba(0,0,0,0.25);
            transform: translateY(-5px);
        }

        .info-box-icon {
            width: 90px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 45px;
            color: white;
        }

        .bg-aqua { background: #00c0ef; }
        .bg-green { background: #00a65a; }
        .bg-yellow { background: #f39c12; }
        .bg-red { background: #dd4b39; }

        .info-box-content {
            flex: 1;
            padding: 10px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .info-box-text {
            text-transform: uppercase;
            font-weight: 600;
            font-size: 12px;
            color: #666;
        }

        .info-box-number {
            font-size: 30px;
            font-weight: 700;
            color: #333;
        }

        .progress {
            height: 2px;
            background: #f4f4f4;
            margin-top: 5px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background: #00c0ef;
            transition: width 1s ease;
        }

        .progress-description {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }

        /* Box */
        .box {
            background: white;
            border-radius: 3px;
            border-top: 3px solid #d2d6de;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12);
            transition: all 0.3s;
            animation: fadeInUp 0.6s;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .box:hover {
            box-shadow: 0 10px 20px rgba(0,0,0,0.19);
            transform: translateY(-3px);
        }

        .box.box-primary { border-top-color: #e5e5e5; }
        .box.box-success { border-top-color: #ff6f28; }
        .box.box-warning { border-top-color: #f39c12; }
        .box.box-danger { border-top-color: #dd4b39; }

        .box-header {
            padding: 10px;
            border-bottom: 1px solid #f4f4f4;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .box-title {
            font-size: 18px;
            margin: 0;
            font-weight: 400;
        }

        .box-body {
            padding: 10px;
        }

        /* Buttons */
        .btn {
            padding: 6px 12px;
            border: 1px solid transparent;
            border-radius: 3px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .btn-primary { background: #ff6f28; color: white; border-color: #e5e5e5; }
        .btn-success { background: #00a65a; color: white; border-color: #008d4c; }
        .btn-warning { background: #f39c12; color: white; border-color: #e08e0b; }
        .btn-danger { background: #dd4b39; color: white; border-color: #d73925; }
        .btn-default { background: white; color: #444; border-color: #ddd; }
        .btn-sm { padding: 5px 10px; font-size: 12px; }

        /* Table */
        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table thead th {
            background: #f9fafb;
            border-bottom: 2px solid #ddd;
            padding: 8px;
            text-align: left;
            font-weight: 600;
        }

        .table tbody td {
            padding: 8px;
            border-bottom: 1px solid #f4f4f4;
        }

        .table tbody tr {
            transition: all 0.2s;
        }

        .table tbody tr:hover {
            background: #f9f9f9;
            transform: translateX(3px);
        }

        .badge {
            display: inline-block;
            padding: 3px 7px;
            font-size: 11px;
            font-weight: 700;
            border-radius: 3px;
            color: white;
        }

        .badge-success { background: #00a65a; }
        .badge-warning { background: #f39c12; }
        .badge-danger { background: #dd4b39; }
        .badge-info { background: #00c0ef; }

        .user-img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            margin-right: 10px;
        }

        /* Grid */
        .row {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 15px;
            margin-bottom: 15px;
        }

        .col-md-12 { grid-column: span 12; }
        .col-md-8 { grid-column: span 8; }
        .col-md-6 { grid-column: span 6; }
        .col-md-4 { grid-column: span 4; }
        .col-md-3 { grid-column: span 3; }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }

        .modal.show { display: flex; }

        .modal-dialog {
            background: white;
            border-radius: 5px;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            animation: modalSlide 0.3s;
        }

        @keyframes modalSlide {
            from { opacity: 0; transform: translateY(-50px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal-header {
            padding: 15px;
            border-bottom: 1px solid #e5e5e5;
            display: flex;
            justify-content: space-between;
        }

        .modal-title {
            font-size: 18px;
            margin: 0;
        }

        .close {
            font-size: 30px;
            font-weight: 300;
            color: #000;
            opacity: 0.5;
            cursor: pointer;
            border: none;
            background: none;
        }

        .close:hover { opacity: 1; }

        .modal-body {
            padding: 15px;
        }

        .modal-footer {
            padding: 15px;
            border-top: 1px solid #e5e5e5;
            text-align: right;
        }

        /* Form */
        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #d2d6de;
            border-radius: 3px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #e5e5e5;
            box-shadow: 0 0 0 3px rgba(60,141,188,0.1);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 80px;
        }

        /* Timeline */
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #ddd;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -24px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #e5e5e5;
            border: 2px solid white;
        }

        .timeline-item .time {
            font-size: 12px;
            color: #999;
        }

        /* Kanban */
        .kanban-board {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        .kanban-column {
            background: #f7f7f7;
            border-radius: 5px;
            padding: 10px;
        }

        .kanban-header {
            padding: 10px;
            font-weight: 700;
            border-bottom: 2px solid #ddd;
            margin-bottom: 10px;
        }

        .kanban-card {
            background: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            cursor: move;
            transition: all 0.3s;
        }

        .kanban-card:hover {
            box-shadow: 0 3px 8px rgba(0,0,0,0.2);
            transform: translateY(-2px);
        }

        /* Chart Container */
        .chart-container {
            position: relative;
            height: 300px;
            background: #f9f9f9;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
        }

        /* Calendar */
        .calendar {
            background: white;
            border-radius: 5px;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background: #ddd;
        }

        .calendar-day {
            background: white;
            padding: 10px;
            text-align: center;
            min-height: 80px;
            position: relative;
        }

        .calendar-day.header {
            font-weight: 700;
            background: #f9f9f9;
            min-height: auto;
        }

        .calendar-day:not(.header):hover {
            background: #f0f8ff;
            cursor: pointer;
        }

        /* Widget */
        .small-box {
            border-radius: 2px;
            position: relative;
            display: block;
            margin-bottom: 20px;
            box-shadow: 0 1px 1px rgba(0,0,0,0.1);
        }

        .small-box .inner {
            padding: 10px;
        }

        .small-box h3 {
            font-size: 38px;
            font-weight: 700;
            margin: 0 0 10px 0;
            padding: 0;
        }

        .small-box p {
            font-size: 15px;
        }

        .small-box .icon {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 90px;
            opacity: 0.15;
        }

        .small-box-footer {
            display: block;
            padding: 3px 0;
            color: rgba(255,255,255,0.8);
            text-align: center;
            text-decoration: none;
            background: rgba(0,0,0,0.1);
        }

        .small-box-footer:hover {
            background: rgba(0,0,0,0.15);
        }

        .hidden { display: none !important; }

        @media (max-width: 768px) {
            .main-sidebar {
                transform: translateX(-230px);
            }
            .content-wrapper {
                margin-left: 0;
            }
            .col-md-8, .col-md-6, .col-md-4, .col-md-3 {
                grid-column: span 12;
            }
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-thumb {
            background: #e5e5e5;
            border-radius: 4px;
        }

        /* Loader */
        .loader-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.5s, visibility 0.5s;
        }

        .loader-wrapper.hidden {
            opacity: 0;
            visibility: hidden;
        }

        .loader-container {
            text-align: center;
            perspective: 1000px;
        }

        .loader {
            width: 120px;
            height: 120px;
            position: relative;
            margin: 0 auto 30px;
            transform-style: preserve-3d;
            animation: rotate3d 2s infinite linear;
        }

        @keyframes rotate3d {
            0% {
                transform: rotateX(0deg) rotateY(0deg);
            }
            100% {
                transform: rotateX(360deg) rotateY(360deg);
            }
        }

        .loader-cube {
            position: absolute;
            width: 60px;
            height: 60px;
            border-radius: 10px;
            animation: pulse 1.5s infinite ease-in-out;
        }

        .loader-cube:nth-child(1) {
            background: linear-gradient(135deg, #00c0ef, #e5e5e5);
            top: 0;
            left: 0;
            animation-delay: 0s;
        }

        .loader-cube:nth-child(2) {
            background: linear-gradient(135deg, #00a65a, #008d4c);
            top: 0;
            right: 0;
            animation-delay: 0.2s;
        }

        .loader-cube:nth-child(3) {
            background: linear-gradient(135deg, #f39c12, #e08e0b);
            bottom: 0;
            left: 0;
            animation-delay: 0.4s;
        }

        .loader-cube:nth-child(4) {
            background: linear-gradient(135deg, #dd4b39, #d73925);
            bottom: 0;
            right: 0;
            animation-delay: 0.6s;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(0.8);
                opacity: 0.7;
            }
        }

        .loader-text {
            color: white;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 15px;
            animation: fadeInOut 2s infinite;
        }

        @keyframes fadeInOut {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .loader-subtitle {
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            margin-bottom: 25px;
        }

        .loader-progress {
            width: 300px;
            height: 6px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            overflow: hidden;
            margin: 0 auto;
        }

        .loader-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #00c0ef, #00a65a, #f39c12, #dd4b39);
            background-size: 200% 100%;
            border-radius: 10px;
            animation: loadProgress 2s ease-out forwards, gradientShift 1s infinite linear;
        }

        @keyframes loadProgress {
            0% { width: 0%; }
            100% { width: 100%; }
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            100% { background-position: 200% 50%; }
        }

        .loader-dots {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .loader-dot {
            width: 12px;
            height: 12px;
            background: white;
            border-radius: 50%;
            animation: dotBounce 1.4s infinite ease-in-out both;
        }

        .loader-dot:nth-child(1) { animation-delay: -0.32s; }
        .loader-dot:nth-child(2) { animation-delay: -0.16s; }
        .loader-dot:nth-child(3) { animation-delay: 0s; }

        @keyframes dotBounce {
            0%, 80%, 100% {
                transform: scale(0);
                opacity: 0.5;
            }
            40% {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* Circular Loader Alternative */
        .circular-loader {
            width: 100px;
            height: 100px;
            border: 8px solid rgba(255, 255, 255, 0.2);
            border-top: 8px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 30px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Beautiful Loader -->
    <div class="loader-wrapper" id="loader">
        <div class="loader-container">
               <img src="{{asset('images/1920 x 557.png')}}" alt="" class="logo logo-lg" style="width:308px !important" />
            <div class="loader-text">5Core</div>
            <div class="loader-subtitle">Loading your dashboard...</div>
            <div class="loader-progress">
                <div class="loader-progress-bar"></div>
            </div>
            <div class="loader-dots">
                <div class="loader-dot"></div>
                <div class="loader-dot"></div>
                <div class="loader-dot"></div>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="main-header">
        <div class="m-header main-logo">
            <a href="http://127.0.0.1:8000/home" class="b-brand">
                <!-- ========   change your logo hear   ============ -->
                <img src="{{asset('images/1920 x 557.png')}}" alt="" class="logo logo-lg" />
            </a>
            <!-- Sidebar collapse toggle button -->
            <button type="button" class="sidebar-toggle-btn" id="sidebar-toggle-btn" title="Toggle Sidebar">
                <i class="fas fa-angle-left" id="toggle-icon"></i>
            </button>
        </div>
        <nav class="navbar">
            
        </nav>
    </header>

<div class="content-wrapper">
<div id="reports" class="page-content">
            


            <section class="content">
                <div class="box box-success">
                    <div class="box-header">
                        <h3 class="box-title">Automated Task Report Overview</h3>
                        <div style="float:right">
                           <button type="button" class="btn btn-primary" id="openReport" style="background-color:#0036af !important">Submit Report</button> 
                           <button type="button" class="btn btn-primary viewReport" style="background-color:#00581f !important">📊 View Report</button>   
                        </div>              
                    </div>
                    @if(session('success'))
                      <div class="alert alert-success mt-2">
                        {{ session('success') }}
                      </div>
                    @endif
                    <div class="box-body">
                       <form method="GET" action="{{ route('automate.tasks.report') }}" class="mb-3">
                        <div class="row">
                          <div class="col-md-3">
                                <div class="form-group">
                                    <label>Scheduled Type</label>
                                  <select class="form-control" name="scheduled_type">
                                     <option value="">All</option>
                                     <option value="fired" {{ request('scheduled_type')=='fired' ? 'selected' : '' }}>Fired</option>
                                     <option value="scheduled" {{ request('scheduled_type')=='scheduled' ? 'selected' : '' }}>Scheduled</option>
                                     <option value="not_fired" {{ request('scheduled_type')=='not_fired' ? 'selected' : '' }}>Not Fired</option>
                                </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Report Type</label>
                                  <select class="form-control" name="report_type">
                                     <option value="">All</option>
                                     <option value="daily" {{ request('report_type')=='daily' ? 'selected' : '' }}>Daily</option>
                                     <option value="weekly" {{ request('report_type')=='weekly' ? 'selected' : '' }}>Weekly</option>
                                     <option value="monthly" {{ request('report_type')=='monthly' ? 'selected' : '' }}>Monthly</option>
                                </select>
                                </div>
                            </div>
                              <div class="col-md-3">
                                <div class="form-group">
                                    <label>Assignor</label>
                                  <select class="form-control" name="assignor">
                                     <option value="">All</option>
                                     @foreach($users as $assignor)
                                       <option value="{{ $assignor->email }}" {{ request('assignor') == $assignor->email ? 'selected' : '' }}>
                                        {{ $assignor->name }}
                                      </option>
                                     @endforeach
                                </select>
                                </div>
                            </div>
                              <div class="col-md-3">
                                <div class="form-group">
                                    <label>Assignee</label>
                                  <select class="form-control" name="assignee">
                                     <option value="">All</option>
                                     @foreach($users as $assignee)
                                      <option value="{{ $assignee->email }}" {{ request('assignee') == $assignee->email ? 'selected' : '' }}>
                                        {{ $assignee->name }}
                                      </option>
                                    @endforeach
                                </select>
                                </div>
                            </div>
                                <!-- <div class="col-md-3">
                                <div class="form-group">
                                    <label>Status</label>
                                  <select class="form-control" name="assignee">
                                     <option value="">All</option>
                                     @foreach($stages as $key => $stage)
                                         <option value="{{ $assignee->name }}" {{ request('assignee') == $assignee->id ? 'selected' : '' }}>{{ $assignee->name }}</option>
                                      @endforeach
                                </select>
                                </div>
                            </div> -->
                            <!--<div class="col-md-3">-->
                            <!--    <div class="form-group">-->
                            <!--        <label>From Date</label>-->
                            <!--        <input type="date" class="form-control" name="from_date" value="{{ request('from_date') }}">-->
                            <!--    </div>-->
                            <!--</div>-->
                            <!--<div class="col-md-3">-->
                            <!--    <div class="form-group">-->
                            <!--        <label>To Date</label>-->
                            <!--        <input type="date" class="form-control" name="to_date" value="{{ request('to_date') }}">-->
                            <!--    </div>-->
                            <!--</div>-->
                        </div>
                        <button type="submit" class="btn btn-primary">📊 Generate Report</button>                        
                      </form>
                    </div>
                </div>

               

                <div class="info-box-row">
                    <div class="info-box">
                        <span class="info-box-icon bg-aqua">🎯</span>
                        <div class="info-box-content">
                            <span class="info-box-text">Daily</span>
                            <span class="info-box-number">{{$dailyCount}}</span>
                        </div>
                    </div>    
                    <div class="info-box">
                        <span class="info-box-icon bg-aqua">🎯</span>
                        <div class="info-box-content">
                            <span class="info-box-text">Weekly</span>
                            <span class="info-box-number">{{$weeklyCount}}</span>
                        </div>
                    </div>   
                    <div class="info-box">
                        <span class="info-box-icon bg-aqua">🎯</span>
                        <div class="info-box-content">
                            <span class="info-box-text">Monthly</span>
                            <span class="info-box-number">{{$monthlyCount}}</span>
                        </div>
                    </div>            
                    <div class="info-box">
                        <span class="info-box-icon bg-green">✓</span>
                        <div class="info-box-content">
                            <span class="info-box-text">Fired</span>
                            <span class="info-box-number">{{$firedTasks}}</span>
                        </div>
                    </div>
                     <div class="info-box">
                        <span class="info-box-icon bg-yellow">⏳</span>
                        <div class="info-box-content">
                            <span class="info-box-text">Scheduled for Later</span>
                            <span class="info-box-number">{{$scheduledTasks}}</span>
                        </div>
                    </div>
                    <div class="info-box">
                        <span class="info-box-icon bg-red">✗</span>
                        <div class="info-box-content">
                            <span class="info-box-text">Not Fired</span>
                            <span class="info-box-number">{{$notFiredTasks}}</span>
                        </div> 
                    </div>
                </div>

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Report Overview</h3>
                        <a href="{{ route('automate.tasks.report', ['fired_all'=> 1]) }}">
                           <button type="button" class="btn btn-success" style="float:right">
                              Re-Fired All
                            </button>
                        </a>
                    </div>
                    <div class="box-body">
                        <table class="table display" id="example">
                            <thead>
                                <tr>
                                    <th>S.no</th>
                                    <th>Group</th>
                                    <th>Task Name</th>
                                    <th>Assignor</th>
                                    <th>Assignee</th>                                    
                                    <th>Schedule Type</th>
                                    <th>Schedule Time</th>
                                    <th>Schedule Day's</th>
                                    <th>Created Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                    <!-- <th>View</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                @php
$assignors = DB::table('users')->pluck('name','email'); // ['email' => 'name']
@endphp
                              @foreach($autoTasks as $task)
                               @php
                           $get_user = DB::table('users')->where('email', $task->assignor)->first();
                        @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{$task->group}}</td>
                    <td>{{ $task->title }}-{{$task->id}}</td>
                     <td>{{ $assignors[$task->assignor] ?? 'N/A' }}</td>
                      <td>{{ $assignors[$task->assign_to] ?? 'N/A' }}</td>                   

                    <td>{{ ucfirst($task->schedule_type ?? '-') }}</td>
                    <td>{{ $task->schedule_time ?? '-' }}</td>
                    <td>{{ $task->schedule_days ?? '-' }}</td>
                    <td>{{ optional($task->created_at)->format('Y-m-d H:i') ?? '-' }}</td>

                    @php
                      $status = $task->status ?? 'unknown';
                    @endphp

                    @if($status === 'fired')
                      <td><span class="badge badge-success">Fired</span></td>
                    @elseif($status === 'scheduled')
                      <td><span class="badge badge-warning">Scheduled</span></td>
                    @elseif($status === 'not_fired')
                      <td><span class="badge badge-danger">Not Fired</span></td>
                    @else
                      <td><span class="badge badge-secondary">Unknown</span></td>
                    @endif
                    <!-- apply action if status is not-fired --> 
                      @if($status === 'fired')
                      <td>Not Required</td>
                    @elseif($status === 'scheduled')
                      <td>Not Required</td>
                    @elseif($status === 'not_fired')
                      <td>
                          <button 
                              class="badge badge-success re-fire-btn" 
                              data-id="{{ $task->id }}">
                              Re-Fired
                          </button>
                       </td>
                    @else
                      <td><span class="badge badge-secondary">Unknown</span></td>
                    @endif

                    <!-- <td>
                        <a href="#" class="btn btn-primary btn-sm">View</a>
                    </td> -->
                </tr>
                @endforeach                       
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
      </div>

<!-- for submit scheduler report -->
 <script>
$('#openReport').on('click', function() {
    $.confirm({
        title: 'Scheduler Error Report',
        content: `
            <form id="schedulerReportForm" class="form">
                <div class="form-check">
                    <input type="checkbox" id="no_issue_found" name="no_issue_found" value="1">
                    <label for="no_issue_found">No issue found</label>
                </div>

                <div class="form-check">
                    <input type="checkbox" id="issue_found_and_fixed" name="issue_found_and_fixed" value="1">
                    <label for="issue_found_and_fixed">Issue found and fixed</label>
                </div>

                <div class="form-check">
                    <input type="checkbox" id="corrective_action_applied" name="corrective_action_applied" value="1">
                    <label for="corrective_action_applied">Corrective action applied</label>
                </div>

                <div class="mt-3" id="remarksContainer" style="display:none;">
                    <label for="remarks">Remarks / Details:</label>
                    <textarea id="remarks" name="remarks" class="form-control" rows="3"></textarea>
                </div>
            </form>
        `,
          boxWidth: '600px',
    useBootstrap: false,
    columnClass: 'medium', // keeps layout responsive
    backgroundDismiss: true, 
        buttons: {
            cancel: {
                text: 'Cancel',
                btnClass: 'btn-secondary'
            },
            submit: {
                text: 'Submit',
                btnClass: 'btn-success',
                action: function() {
                    const form = this.$content.find('#schedulerReportForm');

                    // collect data
                    const data = {
                        _token: '{{ csrf_token() }}',
                        no_issue_found: form.find('#no_issue_found').is(':checked') ? 1 : 0,
                        issue_found_and_fixed: form.find('#issue_found_and_fixed').is(':checked') ? 1 : 0,
                        corrective_action_applied: form.find('#corrective_action_applied').is(':checked') ? 1 : 0,
                        remarks: form.find('#remarks').val()
                    };

                    // send AJAX
                   $.post('{{ route("scheduler-error-report.store") }}', data)
                          .done(() => {
                              // ✅ Close the main confirm popup
                              this.close();
                      
                              // ✅ Show success alert centered
                              $.alert({
                                  title: 'Success!',
                                  content: 'Report submitted successfully!',
                                  useBootstrap: false,
                                  boxWidth: '400px',
                                  onClose: function() {
                                      // Optional: reload the page or update table after closing alert
                                      // location.reload();
                                  }
                              });
                          })
                          .fail(() => {
                              // ✅ Close main popup too
                              this.close();
                      
                              // ✅ Show error alert
                              $.alert({
                                  title: 'Error!',
                                  content: 'Something went wrong!',
                                  type: 'red',
                                  useBootstrap: false,
                                  boxWidth: '400px'
                              });
                          });

                    return false; // prevent dialog from closing immediately
                }
            }
        },
        onContentReady: function () {
            const jc = this;

            // toggle remarks box when checkbox 3 is clicked
            this.$content.find('#corrective_action_applied').on('change', function() {
                const remarks = jc.$content.find('#remarksContainer');
                remarks.toggle(this.checked);
            });
        }
    });
});

// check report
$(document).on('click', '.viewReport', function () {
    $.confirm({
        title: 'Scheduler Error Reports',
        useBootstrap: false,
        boxWidth: '800px',
        content: function () {
            const self = this;
            return $.ajax({
                url: '{{ route("scheduler-error-report.index") }}',
                method: 'GET',
                dataType: 'json'
            }).done(function (response) {
                const data = response.data;

                if (!data || data.length === 0) {
                    self.setContent('<p class="text-center text-muted">No reports found.</p>');
                    return;
                }

                // build table dynamically
                let table = `
                    <div style="max-height:400px; overflow-y:auto;">
                        <table class="table table-bordered table-striped" style="width:100%; font-size:14px;">
                            <thead class="table-light">
                                <tr>
                                    <th>No Issue Found</th>
                                    <th>Issue Found & Fixed</th>
                                    <th>Corrective Action Applied</th>
                                    <th>Remarks</th>
                                    <th>Reported On</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                data.forEach((r) => {
                    table += `
                        <tr>
                            <td>${r.no_issue_found ? '✅' : '❌'}</td>
                            <td>${r.issue_found_and_fixed ? '✅' : '❌'}</td>
                            <td>${r.corrective_action_applied ? '✅' : '❌'}</td>
                            <td>${r.remarks || '-'}</td>
                           <td>${r.formatted_date}</td>
                        </tr>
                    `;
                });

                table += `
                            </tbody>
                        </table>
                    </div>
                `;

                self.setContent(table);
            }).fail(function () {
                self.setContent('<p class="text-danger text-center">⚠️ Failed to load reports.</p>');
            });
        },
        buttons: {
            close: {
                text: 'Close',
                btnClass: 'btn-secondary'
            }
        }
    });
});

</script>

    
    <script>
        // Hide loader after page loads
        window.addEventListener('load', () => {
            setTimeout(() => {
                document.getElementById('loader').classList.add('hidden');
            }, 2000); // 2 seconds loader

            // Animate progress bars
            document.querySelectorAll('.progress-bar').forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0';
                setTimeout(() => {
                    bar.style.width = width;
                }, 2100);
            });
        });

        function showPage(pageId) {
            document.querySelectorAll('.page-content').forEach(page => {
                page.classList.add('hidden');
            });
            
            document.querySelectorAll('.sidebar-menu li').forEach(item => {
                item.classList.remove('active');
            });
            
            document.getElementById(pageId).classList.remove('hidden');
            event.target.closest('li').classList.add('active');
        }

        function openModal(modalId) {
            document.getElementById(modalId).classList.add('show');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
        }

        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal(this.id);
                }
            });
        });

        function handleSubmit(event, modalId) {
            event.preventDefault();
            alert('✓ Data saved successfully!');
            closeModal(modalId);
            event.target.reset();
        }

        window.addEventListener('load', () => {
            document.querySelectorAll('.progress-bar').forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0';
                setTimeout(() => {
                    bar.style.width = width;
                }, 100);
            });
        });

        console.log('%c✨ 5Core Loaded!', 'color: #e5e5e5; font-size: 20px; font-weight: bold;');
    </script>
    <script>
$(document).ready(function() {
  var table = $('#example').DataTable({
    dom: 'Bfrtip', // show buttons
    pageLength: 100, // default entries per page
    lengthMenu: [ [10, 50, 100], [10, 50, 100] ], // dropdown options // show buttons
    buttons: [
      {
        extend: 'excelHtml5',
        text: '📑 Export Excel',
        className: 'btn btn-success',
        filename: 'tasks_report_' + new Date().toISOString().slice(0,19).replace(/:/g,'-'),
        exportOptions: {
          // columns: ':visible' // choose columns to export
        }
      },
      {
        extend: 'pdfHtml5',
        text: '📥 Export PDF',
        className: 'btn btn-warning',
        orientation: 'landscape',
        pageSize: 'A4',
        filename: 'tasks_report_' + new Date().toISOString().slice(0,19).replace(/:/g,'-'),
        exportOptions: {
          // columns: ':visible'
        }
      }
    ],
     initComplete: function() {
    // Remove unwanted default classes
    $('.dt-button').removeClass('dt-button buttons-pdf buttons-html5');
  }
  });
});

</script>
<script>
$(document).on('click', '.re-fire-btn', function() {
    var id = $(this).data('id');

    $.ajax({
        url: '{{ route("task.reFire") }}',
        type: 'POST',
        data: {
            id: id,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            location.reload();
        },
        error: function(xhr) {
            alert('Something went wrong!');
        }
    });
});
</script>
</body>
</html>
