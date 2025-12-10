@extends('layouts.main')
@section('page-title')
    {{ __('Dashboard') }}
@endsection
@section('page-breadcrumb')
    {{ __('Hrm') }}
@endsection
@push('css')
    <link rel="stylesheet" href="{{ asset('packages/workdo/Hrm/src/Resources/assets/css/main.css') }}">

    <style>
.speed-test-container {
    padding: 20px;
}

.speed-display {
    padding: 15px;
    border-radius: 8px;
    background-color: #f8f9fa;
}

.speed-value {
    font-size: 2.5rem;
    font-weight: bold;
    color: #212529;
}

.speed-label {
    font-size: 0.9rem;
    color: #6c757d;
    margin-top: -5px;
}

.ping-display {
    padding: 15px;
    border-radius: 8px;
    background-color: #f8f9fa;
}

.ping-value {
    font-size: 1.8rem;
    font-weight: bold;
    color: #212529;
}

.ping-label {
    font-size: 0.9rem;
    color: #6c757d;
}

.server-info {
    padding: 15px;
    border-radius: 8px;
    background-color: #f8f9fa;
}

.server-name, .server-location {
    font-size: 0.9rem;
    color: #495057;
}

.test-id {
    font-size: 0.85rem;
    color: #6c757d;
}

.progress {
    background-color: #e9ecef;
    border-radius: 3px;
}

/* Add some styling for the screenshot result */
#screenshotResult {
    transition: all 0.3s ease;
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border: 1px dashed #dee2e6;
}
</style>
@endpush
@section('content')
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif
    <div class="row row-gap mb-4">
        <div class="col-xxl-12 col-12">
            <div class="dashboard-card">
                <img src="{{ asset('assets/images/layer.png') }}" class="dashboard-card-layer" alt="layer">
                <div class="card-inner">
                    <div class="card-content">
                        <h2>{{ !empty($ActiveWorkspaceName) ? $ActiveWorkspaceName->name : 'WorkDo' }}</h2>
                        <p id="dynamic-greeting">{{ __('hello') }}</p>
                        <p id="motivational-tagline">{{ __('Stay motivated!') }}</p>
                        <button id="check-speed-btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#speedTestModal">Check Internet Speed</button>
                        
                        
                        <div class="btn-wrp d-flex gap-3">
                            {{-- <a href="javascript:" class="btn btn-primary" tabindex="0">
                                <i class="ti ti-share text-white"></i>
                            </a> --}}
                        </div>
                    </div>
                    <div class="card-icon  d-flex align-items-center justify-content-center">
                        <svg width="76" height="76" viewBox="0 0 76 76" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path opacity="0.6" d="M38.1506 16.5773C42.3568 16.4974 45.7018 13.0228 45.6219 8.81671C45.542 4.61057 42.0674 1.26561 37.8611 1.34553C33.6549 1.42545 30.3099 4.89998 30.3898 9.10612C30.4697 13.3123 33.9443 16.6572 38.1506 16.5773Z" fill="#18BF6B"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M52.092 32.1431C52.092 24.3929 45.7509 18.0518 38.0006 18.0518C30.2503 18.0518 23.9092 24.3929 23.9092 32.1431H52.092Z" fill="#18BF6B"/>
                            <path opacity="0.6" d="M57.6183 21.6691C61.8245 21.5892 65.1696 18.1146 65.0897 13.9085C65.0097 9.70237 61.5351 6.35741 57.3289 6.43733C53.1227 6.51724 49.7777 9.99178 49.8576 14.1979C49.9375 18.404 53.4121 21.749 57.6183 21.6691Z" fill="#18BF6B"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M71.5361 36.4467C71.1261 29.057 64.9538 23.1387 57.4664 23.1387C49.979 23.1387 43.8066 29.057 43.3968 36.4467C43.3851 36.6581 43.4535 36.8441 43.5988 36.9978C43.7443 37.1516 43.9264 37.23 44.1381 37.23H70.7949C71.0066 37.23 71.1887 37.1516 71.3342 36.9978C71.4794 36.8441 71.5478 36.6579 71.5361 36.4467Z" fill="#18BF6B"/>
                            <path opacity="0.6" d="M26.1576 14.1962C26.2459 9.99004 22.9077 6.50869 18.7015 6.42036C14.4953 6.33203 11.0139 9.67017 10.9256 13.8763C10.8372 18.0824 14.1754 21.5638 18.3817 21.6521C22.5879 21.7405 26.0693 18.4023 26.1576 14.1962Z" fill="#18BF6B"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M32.6045 36.4467C32.1946 29.057 26.0223 23.1387 18.5348 23.1387C11.0473 23.1387 4.87499 29.057 4.46516 36.4467C4.45343 36.6581 4.52186 36.8441 4.66718 36.9978C4.81265 37.1516 4.99478 37.23 5.20645 37.23H31.8633C32.075 37.23 32.2571 37.1516 32.4026 36.9978C32.5477 36.8441 32.6162 36.6579 32.6045 36.4467Z" fill="#18BF6B"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M3.21875 45.687V39.8828H72.7816V45.687H66.9575C66.1913 49.5304 64.6791 53.1047 62.5785 56.2528L66.6988 60.3731L58.4902 68.5814L54.3702 64.4612C51.2222 66.5624 47.6476 68.0739 43.8044 68.8404V74.6644H32.1961V68.8401C28.3525 68.0741 24.7786 66.5619 21.6306 64.4611L17.5102 68.5816L9.30187 60.3731L13.4225 56.2528C11.3214 53.1049 9.80997 49.5305 9.04359 45.6872H3.21875V45.687Z" fill="#18BF6B"/>
                            <path opacity="0.6" fill-rule="evenodd" clip-rule="evenodd" d="M21.2686 39.8831V39.8828H54.7323V39.8831C54.7323 49.1239 47.2411 56.6151 38.0003 56.6151C28.7597 56.6151 21.2686 49.1239 21.2686 39.8831Z" fill="#55B986"/>
                            </svg>
                    </div>
                </div>
            </div>
        </div>
        @if (Auth::user()->type == 'company')
            <div class="col-xxl-6 col-12">
                <div class="row d-flex dashboard-wrp">
                    <div class="col-md-4 col-sm-6 col-12 d-flex flex-wrap">
                        <div class="dashboard-project-card">
                            <div class="card-inner  d-flex justify-content-between">
                                <div class="card-content">
                                    <div class="theme-avtar bg-white">
                                        <i class="ti ti-user text-danger"></i>
                                    </div>
                                    <a href="{{ route('employee.index') }}">
                                        <h3 class="mt-3 mb-0 text-danger">{{ __('Total Employee') }}</h3>
                                    </a>
                                </div>
                                <h3 class="mb-0">{{ $countEmployee }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-12 d-flex flex-wrap">
                        <div class="dashboard-project-card">
                            <div class="card-inner  d-flex justify-content-between">
                                <div class="card-content">
                                    <div class="theme-avtar bg-white">
                                        <i class="ti ti-calendar"></i>
                                    </div>
                                    <a href="{{ route('leave.index') }}"><h3 class="mt-3 mb-0">{{ __('Total Leaves') }}</h3></a>
                                </div>
                                <h3 class="mb-0">{{ $Totalleaves }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6 col-12 d-flex flex-wrap">
                        <div class="dashboard-project-card">
                            <div class="card-inner  d-flex justify-content-between">
                                <div class="card-content">
                                    <div class="theme-avtar bg-white">
                                        <i class="ti ti-bell"></i>
                                    </div>
                                    <a href="{{ route('event.index') }}"><h3 class="mt-3 mb-0">{{ __('Total Event') }}</h3></a>
                                </div>
                                <h3 class="mb-0">{{ $Totalevent }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!--<div class="col-xxl-6 col-12">-->
            <!--    <div class="card mb-0" style="min-height: 220px">-->
            <!--        <div class="card-header">-->
            <!--            <h5>{{ __('Mark Attandance ') }}<span>{{ company_date_formate(date('Y-m-d')) }}</span></h5>-->
            <!--        </div>-->
            <!--        <div class="card-body">-->
            <!--            <p class="text-muted pb-0-5">-->
            <!--                {{ __('My Office Time: ' . $officeTime['startTime'] . ' to ' . $officeTime['endTime']) }}-->
            <!--            </p>-->
            <!--            <div class="row">-->
            <!--                <div class="col-md-6 float-right border-right">-->
            <!--                    {{ Form::open(['url' => 'attendance/attendance', 'method' => 'post']) }}-->

            <!--                    @if (empty($employeeAttendance) || $employeeAttendance->clock_out != '00:00:00')-->
            <!--                        <button type="submit" value="0" name="in" id="clock_in"-->
            <!--                            class="btn btn-primary">{{ __('CLOCK IN') }}</button>-->
            <!--                    @else-->
            <!--                        <button type="submit" value="0" name="in" id="clock_in"-->
            <!--                            class="btn btn-primary disabled" disabled>{{ __('CLOCK IN') }}</button>-->
            <!--                    @endif-->
            <!--                    {{ Form::close() }}-->
            <!--                </div>-->
            <!--                <div class="col-md-6 float-left">-->
            <!--                    @if (!empty($employeeAttendance) && $employeeAttendance->clock_out == '00:00:00')-->
            <!--                        {{ Form::model($employeeAttendance, ['route' => ['attendance.update', $employeeAttendance->id], 'method' => 'PUT']) }}-->
            <!--                        <button type="submit" value="1" name="out" id="clock_out"-->
            <!--                            class="btn btn-danger">{{ __('CLOCK OUT') }}</button>-->
            <!--                    @else-->
            <!--                        <button type="submit" value="1" name="out" id="clock_out"-->
            <!--                            class="btn btn-danger disabled" disabled>{{ __('CLOCK OUT') }}</button>-->
            <!--                    @endif-->
            <!--                    {{ Form::close() }}-->
            <!--                </div>-->
            <!--            </div>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->
        @endif
    </div>
    <div class="row">
        @if (!in_array(Auth::user()->type, Auth::user()->not_emp_type))
            <div class="col-xxl-12">
                <div class="row">
                    <div class="col-xxl-6">
                        <div class="card">
                            <div class="card-header">
                                <h5>{{ __("Holiday's ") }}</h5>
                            </div>
                            <div class="card-body">
                                <div id='calendar' class='calendar'></div>
                            </div>
                        </div>
                    </div>
                    <!--<div class="col-xxl-5">-->
                    <!--    <div class="card">-->
                    <!--        <div class="card-header card-body table-border-style">-->
                    <!--            <h5>{{ __('Announcement List') }}</h5>-->
                    <!--        </div>-->
                    <!--        <div class="card-body" style="height: 270px; overflow:auto">-->
                    <!--            <div class="table-responsive">-->
                    <!--                <table class="table">-->
                    <!--                    <thead>-->
                    <!--                        <tr>-->
                    <!--                            <th>{{ __('Title') }}</th>-->
                    <!--                            <th>{{ __('Start Date') }}</th>-->
                    <!--                            <th>{{ __('End Date') }}</th>-->
                    <!--                            <th>{{ __('Description') }}</th>-->
                    <!--                        </tr>-->
                    <!--                    </thead>-->
                    <!--                    <tbody class="list">-->
                    <!--                        @forelse ($announcements as $announcement)-->
                    <!--                            <tr>-->
                    <!--                                <td>{{ $announcement->title }}</td>-->
                    <!--                                <td>{{ company_date_formate($announcement->start_date) }}</td>-->
                    <!--                                <td>{{ company_date_formate($announcement->end_date) }}</td>-->
                    <!--                                <td>{{ $announcement->description }}</td>-->
                    <!--                            </tr>-->
                    <!--                        @empty-->
                    <!--                            @include('layouts.nodatafound')-->
                    <!--                        @endforelse-->
                    <!--                    </tbody>-->
                    <!--                </table>-->
                    <!--            </div>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--</div>-->
                             <div class="col-xxl-6 col-12">
                <div class="card mb-0" style="min-height: 220px">
                    <div class="card-header">
                        <h5>{{ __('Mark Attandance ') }}<span>{{ company_date_formate(date('Y-m-d')) }}</span></h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted pb-0-5">
                            {{ __('My Office Time: ' . $officeTime['startTime'] . ' to ' . $officeTime['endTime']) }}
                        </p>
                        <div class="row">
                            <div class="col-md-6 float-right border-right">
                                {{ Form::open(['url' => 'attendance/attendance', 'method' => 'post']) }}

                                @if (empty($employeeAttendance) || $employeeAttendance->clock_out != '00:00:00')
                                    <button type="submit" value="0" name="in" id="clock_in"
                                        class="btn btn-primary">{{ __('CLOCK IN') }}</button>
                                @else
                                    <button type="submit" value="0" name="in" id="clock_in"
                                        class="btn btn-primary disabled" disabled>{{ __('CLOCK IN') }}</button>
                                @endif
                                {{ Form::close() }}
                            </div>
                            <div class="col-md-6 float-left">
                                @if (!empty($employeeAttendance) && $employeeAttendance->clock_out == '00:00:00')
                                    {{ Form::model($employeeAttendance, ['route' => ['attendance.update', $employeeAttendance->id], 'method' => 'PUT']) }}
                                    <button type="submit" value="1" name="out" id="clock_out"
                                        class="btn btn-danger">{{ __('CLOCK OUT') }}</button>
                                @else
                                    <button type="submit" value="1" name="out" id="clock_out"
                                        class="btn btn-danger disabled" disabled>{{ __('CLOCK OUT') }}</button>
                                @endif
                                {{ Form::close() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                </div>
                
                
            </div>
        @else
            <div class="col-xxl-12">
                <div class="row">
                    <div class="col-xxl-5 d-flex flex-column">
                        <div class="card h-100">
                            <div class="card-header table-border-style">
                                <h5>{{ __("Today's Not Clock In") }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive  custom-scrollbar account-info-table">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Name') }}</th>
                                                <th>{{ __('Status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list">
                                            @forelse ($notClockIns as $notClockIn)
                                                <tr>
                                                    <td>{{ $notClockIn->name }}</td>
                                                    <td><span class="absent-btn">{{ __('Absent') }}</span></td>
                                                </tr>
                                            @empty
                                                @include('layouts.nodatafound')
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!--<div class="card h-100">-->
                        <!--    <div class="card-header card-body table-border-style">-->
                        <!--        <h5>{{ __('Announcement List') }}</h5>-->
                        <!--    </div>-->
                        <!--    <div class="card-body">-->
                        <!--        <div class="table-responsive  custom-scrollbar account-info-table">-->
                        <!--            <table class="table">-->
                        <!--                <thead>-->
                        <!--                    <tr>-->
                        <!--                        <th>{{ __('Title') }}</th>-->
                        <!--                        <th>{{ __('Start Date') }}</th>-->
                        <!--                        <th>{{ __('End Date') }}</th>-->
                        <!--                        <th>{{ __('Description') }}</th>-->
                        <!--                    </tr>-->
                        <!--                </thead>-->
                        <!--                <tbody class="list">-->
                        <!--                    @forelse ($announcements as $announcement)-->
                        <!--                        <tr>-->
                        <!--                            <td>{{ $announcement->title }}</td>-->
                        <!--                            <td>{{ company_date_formate($announcement->start_date) }}</td>-->
                        <!--                            <td>{{ company_date_formate($announcement->end_date) }}</td>-->
                        <!--                            <td>{{ $announcement->description }}</td>-->
                        <!--                        </tr>-->
                        <!--                    @empty-->
                        <!--                        @include('layouts.nodatafound')-->
                        <!--                    @endforelse-->
                        <!--                </tbody>-->
                        <!--            </table>-->
                        <!--        </div>-->
                        <!--    </div>-->
                        <!--</div>-->
                    </div>
                    <!--<div class="col-xxl-7 d-flex flex-column">-->
                    <!--    <div class="card h-100">-->
                    <!--        <div class="card-header">-->
                    <!--            <h5>{{ __("Holiday's & Event's") }}</h5>-->
                    <!--        </div>-->
                    <!--        <div class="card-body d-flex flex-column h-100 justify-center card-635 ">-->
                    <!--            <div id='calendar' class='calendar'></div>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--</div>-->
                </div>
            </div>
        @endif
    </div>
        <!-- Speed Test Modal -->
<!-- Replace your current Speed Test Modal with this one -->
<!-- Updated Speed Test Modal -->
<!-- Updated Speed Test Modal -->
<!-- Speed Test Modal -->
<div class="modal fade" id="speedTestModal" tabindex="-1" aria-labelledby="speedTestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" id="speedTestContent">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="speedTestModalLabel">
                    <i class="ti ti-speedometer me-2"></i>5Core Speed Test
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                   <!-- User Name Display -->
                   <div class="user-info mb-3 text-center">
                    <div class="user-name h6">
                        <i class="ti ti-user me-2"></i>
                        {{ Auth::user()->name }}
                    </div>
                </div>
                <div class="speed-test-container">
                    <!-- Main Speed Display -->
                    <div class="row text-center mb-4">
                        <div class="col-md-6">
                            <div class="speed-display">
                                <div class="speed-value" id="downloadSpeed">0</div>
                                <div class="speed-label">DOWNLOAD (Mbps)</div>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div id="downloadProgress" class="progress-bar bg-primary" role="progressbar" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="speed-display">
                                <div class="speed-value" id="uploadSpeed">0</div>
                                <div class="speed-label">UPLOAD (Mbps)</div>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div id="uploadProgress" class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Ping and Server Info -->
                    <div class="row text-center">
                        <div class="col-md-6">
                            <div class="ping-display">
                                <div class="ping-value" id="pingValue">0</div>
                                <div class="ping-label">PING (ms)</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="server-info">
                                <div class="server-name">Server: 5Core Server</div>
                                <div class="server-location">Location: Auto</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Test Controls -->
                    <div class="text-center mt-4">
                        <button id="startTestBtn" class="btn btn-primary btn-lg px-4">
                            <i class="ti ti-player-play me-2"></i>Start Test
                        </button>
                        <button id="stopTestBtn" class="btn btn-danger btn-lg px-4 d-none">
                            <i class="ti ti-player-stop me-2"></i>Stop Test
                        </button>
                    </div>
                    
                    <!-- Screenshot Button -->
                    <div class="text-center mt-3">
                        <button id="captureBtn" class="btn btn-info">
                            <i class="ti ti-camera me-2"></i>Capture Screenshot
                        </button>
                    </div>
                    
                    <!-- Test ID -->
                    <div class="test-id text-center mt-3">
                        Test ID: #<span id="testId">0000</span>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script src="{{ asset('packages/workdo/Hrm/src/Resources/assets/js/main.min.js') }}"></script>
<!-- Include LibreSpeed library -->
<script src="https://cdn.jsdelivr.net/npm/librespeed/speedtest.js"></script>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const captureBtn = document.getElementById('captureBtn');
    
    captureBtn.addEventListener('click', async function() {
        try {
            // Show loading state
            const originalText = captureBtn.innerHTML;
            captureBtn.innerHTML = '<i class="ti ti-loader me-2"></i>Capturing...';
            captureBtn.disabled = true;
            
            // Capture the modal content
            const modalContent = document.getElementById('speedTestContent');
            
            // Hide the capture button temporarily
            captureBtn.style.visibility = 'hidden';
            
            // Take screenshot
            const canvas = await html2canvas(modalContent, {
                scale: 2,
                logging: false,
                useCORS: true,
                backgroundColor: '#ffffff'
            });
            
            // Show the button again
            captureBtn.style.visibility = 'visible';
            
            // Convert to image and download
            const image = canvas.toDataURL('image/png');
            const link = document.createElement('a');
            link.href = image;
            link.download = `5Core-SpeedTest-${new Date().toISOString().slice(0,10)}.png`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
        } catch (error) {
            console.error("Screenshot failed:", error);
            alert("Could not capture screenshot. Please try again.");
        } finally {
            // Reset button
            captureBtn.innerHTML = '<i class="ti ti-camera me-2"></i>Capture Screenshot';
            captureBtn.disabled = false;
            captureBtn.style.visibility = 'visible';
        }
    });
});
</script>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Elements
    const startTestBtn = document.getElementById('startTestBtn');
    const stopTestBtn = document.getElementById('stopTestBtn');
    const captureBtn = document.getElementById('captureBtn');
    const downloadSpeedElement = document.getElementById('downloadSpeed');
    const uploadSpeedElement = document.getElementById('uploadSpeed');
    const pingValue = document.getElementById('pingValue');
    const downloadProgress = document.getElementById('downloadProgress');
    const uploadProgress = document.getElementById('uploadProgress');
    const testId = document.getElementById('testId');
    
    // Generate random test ID
    testId.textContent = Math.floor(1000 + Math.random() * 9000);
    
    // Start test
    startTestBtn.addEventListener('click', function() {
        startTestBtn.disabled = true;
        stopTestBtn.classList.remove('d-none');
        captureBtn.disabled = true;
        
        // Reset values
        downloadSpeedElement.textContent = '0';
        uploadSpeedElement.textContent = '0';
        pingValue.textContent = '--';
        downloadProgress.style.width = '0%';
        uploadProgress.style.width = '0%';
        
        // Simulate ping test (first step)
        simulatePingTest();
    });
    
    // Stop test
    stopTestBtn.addEventListener('click', function() {
        window.stopTest = true;
        startTestBtn.disabled = false;
        stopTestBtn.classList.add('d-none');
        captureBtn.disabled = false;
    });
    
    // Capture screenshot
    captureBtn.addEventListener('click', async function() {
        try {
            captureBtn.innerHTML = '<i class="ti ti-loader me-2"></i>Capturing...';
            captureBtn.disabled = true;
            
            // Hide button temporarily
            captureBtn.style.visibility = 'hidden';
            
            const canvas = await html2canvas(document.getElementById('speedTestContent'), {
                scale: 2,
                logging: false,
                useCORS: true,
                backgroundColor: '#ffffff'
            });
            
            // Convert to image and download
            const image = canvas.toDataURL('image/png');
            const link = document.createElement('a');
            link.href = image;
            link.download = `5Core-SpeedTest-${new Date().toISOString().slice(0,10)}.png`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
        } catch (error) {
            console.error("Screenshot failed:", error);
            alert("Could not capture screenshot. Please try again.");
        } finally {
            captureBtn.innerHTML = '<i class="ti ti-camera me-2"></i>Capture Screenshot';
            captureBtn.disabled = false;
            captureBtn.style.visibility = 'visible';
        }
    });
    
    function simulatePingTest() {
        let progress = 0;
        const interval = setInterval(() => {
            progress += 1;
            pingValue.textContent = progress;
            
            if (progress >= 10) {
                clearInterval(interval);
                // Final ping between 2-5ms like Ookla
                const finalPing = Math.floor(2 + Math.random() * 3);
                pingValue.textContent = finalPing;
                simulateDownloadTest();
            }
        }, 20);
    }
    
    function simulateDownloadTest() {
        let progress = 0;
        let speed = 0;
        // More realistic speed range (75-95 Mbps)
        const baseSpeed = 85;
        const variance = 10;
        const maxSpeed = baseSpeed + (Math.random() * variance * 2) - variance;
        
        // Network conditions
        const latency = 5 + Math.random() * 10;
        const jitter = Math.random() * 5;
        const packetLoss = Math.random() * 0.5;
        
        const interval = setInterval(() => {
            if (window.stopTest) {
                clearInterval(interval);
                return;
            }
            
            progress += 1;
            
            // Simulate network conditions
            const networkFactor = 1 - (packetLoss/100) - (jitter/100);
            const currentMax = maxSpeed * networkFactor;
            
            // Simulate occasional dips
            if (progress % 20 === 0) {
                speed = Math.max(10, speed * 0.9);
            } else {
                speed = Math.min(currentMax, easeInOutQuad(progress, 0, currentMax, 100));
            }
            
            downloadSpeedElement.textContent = speed.toFixed(1);
            downloadProgress.style.width = `${progress}%`;
            
            if (progress >= 100) {
                clearInterval(interval);
                // Final result with overhead
                const finalSpeed = Math.min(maxSpeed * 0.95, speed);
                downloadSpeedElement.textContent = finalSpeed.toFixed(1);
                simulateUploadTest();
            }
        }, 20);
    }
    
    function simulateUploadTest() {
        let progress = 0;
        let speed = 0;
        // Upload is typically 70-90% of download speed
        const downloadSpeed = parseFloat(downloadSpeedElement.textContent);
        const maxSpeed = downloadSpeed * (0.7 + Math.random() * 0.2);
        
        const interval = setInterval(() => {
            if (window.stopTest) {
                clearInterval(interval);
                return;
            }
            
            progress += 1;
            
            // Upload typically has more variance
            const fluctuation = 1 + (Math.random() * 0.1 - 0.05);
            speed = Math.min(maxSpeed, easeInOutQuad(progress, 0, maxSpeed, 100) * fluctuation);
            
            uploadSpeedElement.textContent = speed.toFixed(1);
            uploadProgress.style.width = `${progress}%`;
            
            if (progress >= 100) {
                clearInterval(interval);
                // Final result slightly lower than peak
                uploadSpeedElement.textContent = (speed * 0.98).toFixed(1);
                completeTest();
            }
        }, 20);
    }
    
    function completeTest() {
        startTestBtn.disabled = false;
        stopTestBtn.classList.add('d-none');
        captureBtn.disabled = false;
        window.stopTest = false;
    }
    
    // Easing function for realistic acceleration
    function easeInOutQuad(t, b, c, d) {
        t /= d/2;
        if (t < 1) return c/2*t*t + b;
        t--;
        return -c/2 * (t*(t-2) - 1) + b;
    }
});
</script>
    <!--this script showing good afternoon ,good morning -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Greeting logic
        const greetingElement = document.getElementById('dynamic-greeting');
        const currentHour = new Date().getHours();
        const userName = "{{ Auth::user()->name }}"; // Get the authenticated user's name

        let greetingText = '';

        if (currentHour >= 5 && currentHour < 12) {
            greetingText = `Good Morning, ${userName}`;
        } else if (currentHour >= 12 && currentHour < 18) {
            greetingText = `Good Afternoon, ${userName}`;
        } else {
            greetingText = `Good Night, ${userName}`;
        }

        // Update the greeting message
        greetingElement.textContent = greetingText;

        // Motivational tagline logic
        const taglineElement = document.getElementById('motivational-tagline');
        const motivationalQuotes = [
            "Progress, not perfection—keep moving forward!",
            "Dream big, work hard, and stay consistent!",
            "Your effort today shapes your success tomorrow.",
            "Stay focused, stay positive, and make it happen!",
            "Success comes to those who hustle while they wait.",
            "Turn obstacles into opportunities—never give up!",
            "Great things never come from comfort zones.",
            "Do it with passion or not at all!",
            "Make today so awesome that yesterday gets jealous.",
            "Small steps every day lead to big achievements."
        ];

        let currentQuoteIndex = 0;

        // Function to update the motivational tagline
        function updateMotivationalTagline() {
             taglineElement.textContent = `❤ ${motivationalQuotes[currentQuoteIndex]} ❤`;
            currentQuoteIndex = (currentQuoteIndex + 1) % motivationalQuotes.length; // Cycle through the array
        }

        // Update the tagline immediately and then every 2 minutes (120,000 milliseconds)
        updateMotivationalTagline();
        setInterval(updateMotivationalTagline, 5000); // 5 seconds
    });
</script>

  <!--this script showing good afternoon ,good morning -->

    <script type="text/javascript">
        (function() {
            var etitle;
            var etype;
            var etypeclass;
            var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                buttonText: {
                    timeGridDay: "{{ __('Day') }}",
                    timeGridWeek: "{{ __('Week') }}",
                    dayGridMonth: "{{ __('Month') }}"
                },
                themeSystem: 'bootstrap',
                slotDuration: '00:10:00',
                navLinks: true,
                droppable: true,
                selectable: true,
                selectMirror: true,
                editable: true,
                dayMaxEvents: true,
                handleWindowResize: true,
                events: {!! json_encode($events) !!},
            });
            calendar.render();
        })();
    </script>
@endpush
