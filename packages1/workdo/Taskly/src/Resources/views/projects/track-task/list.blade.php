@extends('layouts.main')
@section('page-title') {{ __('Track Task List') }} @endsection
@section('title') {{ __('Track Task List') }} @endsection
@section('page-breadcrumb') {{ __('Project') }},{{ __('Project Details') }},{{ __('Track Task List') }} @endsection

@push('css')
    <style>      

        .container {
            max-width: 1526px;
            margin: 0 auto;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--card-from), var(--card-to));
            border-radius: 1rem;
            padding: 1.5rem;
            color: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .stat-card.blue {
            --card-from: #2563eb;
            --card-to: #1d4ed8;
        }

        .stat-card.amber {
            --card-from: #f59e0b;
            --card-to: #d97706;
        }

        .stat-card.red {
            --card-from: #ef4444;
            --card-to: #dc2626;
        }

        .stat-card.green {
            --card-from: #10b981;
            --card-to: #059669;
        }

        .stat-card.cyan {
            --card-from: #06b6d4;
            --card-to: #0891b2;
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            opacity: 0.9;
        }

        .stat-icon {
            font-size: 1.5rem;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
        }

        /* Main Card */
        .main-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        /* Header */
        .card-header {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            padding: 2rem;
            color: white;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .header-title h2 {
            font-size: 1.75rem;
            margin-bottom: 0.25rem;
        }

        .header-subtitle {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.875rem;
        }

        .header-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 0.625rem 1.25rem;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 0.875rem;
            transition: background 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Filters */
        .filters-section {
            background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
            padding: 1.5rem 2rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .filters-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .filters-right {
            display: flex;
            gap: 0.75rem;
            flex: 1;
            max-width: 600px;
        }

        label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #64748b;
        }

        select, input {
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
            background: white;
            transition: all 0.3s ease;
        }

        select:focus, input:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        input[type="text"] {
            flex: 1;
            padding-left: 2.5rem;
        }

        .search-wrapper {
            position: relative;
            flex: 1;
        }

        .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        /* Table */
      /* .table-wrapper {
    max-height: 600px;
    overflow-y: auto;
    overflow-x: auto;
} */

/* sticky header */
#employeeTable_filter,#employeeTable_length{
    display:none;
}



        .employee-name {
            font-weight: 600;
            color: #6366f1;
        }

        .dept-name {
            color: #64748b;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 0.375rem 1rem;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 0.875rem;
            color: white;
        }

        .badge.total { background: #1e293b; }
        .badge.pending { background: #f59e0b; }
        .badge.overdue { background: #ef4444; }
        .badge.done { background: #10b981; }
        .badge.rating { background: #fbbf24; }
        .badge.etc { background: #1e293b; }
        .badge.l30 { background: #f97316; }
        .badge.l7 { background: #2563eb; }
        .badge.teamlogger { background: #8b5cf6; }

        /* Footer */
        .table-footer {
            padding: 1rem;
            background: #f8fafc;
            text-align: center;
            font-size: 0.875rem;
            color: #64748b;
        }

        /* Checkbox */
        input[type="checkbox"] {
            width: 1rem;
            height: 1rem;
            cursor: pointer;
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .header-content {
                flex-direction: column;
                align-items: flex-start;
            }

            .filters-section {
                flex-direction: column;
                align-items: stretch;
            }

            .filters-left, .filters-right {
                width: 100%;
            }

            .stat-value {
                font-size: 2rem;
            }
        }

        /* dar button */
        .dar-btn {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 50%;
    width: 42px;
    height: 42px;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    transition: 0.25s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.dar-btn:hover {
    background: #f3f4f6;
    transform: translateX(-3px); /* subtle back movement */
}

.dar-btn svg {
    stroke: #374151;
    transition: 0.25s ease;
}

.dar-btn:hover svg {
    stroke: #111827;
}
    </style>
@endpush

@section('content')
<div class="container mt-3">
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card blue">
                <div class="stat-header">
                    <span class="stat-label">Total Tasks</span>
                    <span class="stat-icon">📊</span>
                </div>
                <div class="stat-value">{{$totalTask->count()}}</div>
            </div>

            <div class="stat-card amber">
                <div class="stat-header">
                    <span class="stat-label">Pending</span>
                    <span class="stat-icon">⏳</span>
                </div>
                <div class="stat-value">{{$pendingTask->count()}}</div>
            </div>

            <div class="stat-card red">
                <div class="stat-header">
                    <span class="stat-label">Overdue</span>
                    <span class="stat-icon">⚠️</span>
                </div>
                <div class="stat-value">{{$overdueTask->count()}}</div>
            </div>

            <div class="stat-card green">
                <div class="stat-header">
                    <span class="stat-label">Completed</span>
                    <span class="stat-icon">✅</span>
                </div>
                <div class="stat-value">{{$completedTask->count()}}</div>
            </div>

            <div class="stat-card cyan">
                <div class="stat-header">
                    <span class="stat-label">ETC (Hours)</span>
                    <span class="stat-icon">⏱️</span>
                </div>
                <div class="stat-value">{{round($totalETAmin/60)}}</div>
            </div>

            <div class="stat-card cyan">
                <div class="stat-header">
                    <span class="stat-label">ATC (Hours)</span>
                    <span class="stat-icon">⏱️</span>
                </div>
                <div class="stat-value">{{round($totalATCMin/60)}}</div>
            </div>
        </div>

        <!-- Main Card -->
        <div class="main-card">
            <!-- Header -->
            <!-- <div class="card-header">
                <div class="header-content">
                    <div class="header-title">
                        <h2>Employee Task Status</h2>
                        <p class="header-subtitle">Monitor and manage team performance</p>
                    </div>
                    <div class="header-actions">
                        <button class="btn">📥 Export</button>
                        <button class="btn">🔄 Refresh</button>
                    </div>
                </div>
            </div> -->

            <!-- Filters -->
            <div class="filters-section">
                <!-- <div class="filters-left">
                    <label>Show</label>
                    <select>
                        <option>100</option>
                        <option>50</option>
                        <option>25</option>
                    </select>
                    <span style="font-size: 0.875rem; color: #64748b;">entries</span>
                </div> -->

                <div class="filters-right">
                    <select id="departmentFilter" class="form-select" style="flex: 1;">
                        <option value="">All Departments</option>
                        @foreach (array_unique(array_filter(array_column($resultData, 'dept'))) as $department)
                            <option value="{{ trim($department) }}">{{ trim($department) }}</option>
                        @endforeach
                    </select>
                    
                    <div class="search-wrapper">
                        <span class="search-icon">🔍</span>
                        <input type="text" id="employeeSearch" placeholder="Search employees...">

                    </div>
                </div>
            </div>

            <!-- Table -->
            <!-- Table -->
<div class="table-wrapper">
  <table id="employeeTable" class="table table-striped">
    <thead>
        <tr>
            <th style="width:40px;"><input type="checkbox" id="selectAll"></th>
            <th style="width:180px;">EMPLOYEE NAME</th>
            <th style="width:220px;">DEPARTMENT</th>
            <th>TOTAL</th>
            <th>PENDING</th>
            <th>OVERDUE</th>
            <th>DONE</th>
            <th>ETC (HRS)</th>
            <th>L30 ETC</th>
            <th>L30 ATC</th>
            <th>L7 ETC</th>
            <th>TEAMLOGGER</th>
            <th>DAR</th>
        </tr>
    </thead>
        <tbody>
            @foreach ($resultData as $value)
            <tr>
                <td><input type="checkbox" class="row-select"></td>
                <td><div class="employee-name">{{ $value['name'] }}</div></td>
                <td><div class="dept-name">{{ $value['dept'] }}</div></td>
                <td class="center"><span class="badge total">{{ $value['total_count'] }}</span></td>
                <td class="center"><span class="badge pending">{{ $value['pending_count'] }}</span></td>
                <td class="center"><span class="badge overdue">{{ $value['overdue_count'] }}</span></td>
                <td class="center"><span class="badge done">{{ $value['done_count'] }}</span></td>
                <td class="center"><span class="badge etc">{{ round($value['eta_sum']) }}</span></td>
                <td class="center"><span class="badge l30">{{ round($value['eta_sum_l30']) }}</span></td>
                <td class="center"><span class="badge l30">{{ round($value['atc_sum_l30']) }}</span></td>
                <td class="center"><span class="badge l7">{{ round($value['eta_sum_l7']) }}</span></td>
                <td class="center"><span class="badge teamlogger">{{round($value['teamlogger_active_hours'])}}</span></td>
                <td class="center"><button class="dar-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                   </button></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="table-footer">
    Showing {{ count($resultData) }} entries
</div>


            <!-- Footer -->
            <!-- <div class="table-footer">
                Showing 1 to 5 of 5 entries
            </div> -->
        </div>
    </div>

@push('scripts')
    {{-- jQuery, Bootstrap, DataTables --}}
    <script>
      $(document).ready(function () {

    let table = $('#employeeTable').DataTable({
        paging: true,
        pageLength: 100,
        scrollY: "600px",
        scrollCollapse: true,
        scrollX: true,
        fixedHeader: true,
        autoWidth: false,
        order: [],
        columnDefs: [
            { orderable: false, targets: 0 }
        ]
    });

    // Global search
    $('#employeeSearch').on('keyup', function () {
        table.search(this.value).draw();
    });

    // Department dropdown filter
    $('#departmentFilter').on('change', function () {
        let value = this.value;

        if (value === "") {
            table.column(2).search("").draw();
        } else {
            table.column(2).search("^" + value + "$", true, false).draw();
        }
    });
});


    </script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>     
        // ---------- Navigation stack logic (one-step back) ----------
        const viewStack = []; // will store view names (strings)
        const rootView = 'mainView';

        function showViewElement(viewName) {
            // hide main view by id
            document.getElementById('mainView').style.display = (viewName === rootView ? 'block' : 'none');

            // hide all views
            document.querySelectorAll('.view').forEach(v => v.classList.remove('active'));

            if (viewName === rootView) return;

            // Activate the correct view element (data-view attribute)
            const viewEl = document.querySelector(`.view[data-view="${viewName}"]`);
            if (viewEl) viewEl.classList.add('active');
            else console.warn('View not found:', viewName);
        }

        function navigateTo(viewName) {
            // push current active view to stack so we can go back 1 step
            const active = document.querySelector('.view.active') ? document.querySelector('.view.active').dataset.view : null;
            const inMain = document.getElementById('mainView').style.display !== 'none' && !active;

            if (inMain) {
                // from dashboard -> push root marker
                viewStack.push(rootView);
            } else if (active) {
                viewStack.push(active);
            } else {
                // fallback
                viewStack.push(rootView);
            }

            // show requested view
            showViewElement(viewName);
            // hide main view
            document.getElementById('mainView').style.display = 'none';
        }

        function goBack() {
            // pop last view. If nothing, show root
            const prev = viewStack.pop();
            if (!prev || prev === rootView) {
                // show dashboard
                showViewElement(rootView);
                document.getElementById('mainView').style.display = 'block';
            } else {
                // show previous view
                showViewElement(prev);
            }
        }

        // convenience wrapper when you want to go to dashboard directly:
        function showDashboard() {
            viewStack.length = 0;
            showViewElement(rootView);
            document.getElementById('mainView').style.display = 'block';
        }

        // ---------- Card preferences (localStorage) ----------
        document.addEventListener('DOMContentLoaded', () => {
            let savedCards = localStorage.getItem("visibleCards");
            if (!savedCards) {
                document.getElementById("cardSetupModal")?.style?.display = "flex";
            } else {
                applyCardPreferences(JSON.parse(savedCards));
            }
        });

        function applyCardPreferences(selectedCards) {
            document.querySelectorAll(".category-card").forEach(card => {
                let target = card.dataset.target;
                if (target) {
                    card.style.display = selectedCards.includes(target) ? "block" : "none";
                }
            });
        }

        function openCardManager() {
            // your existing manager UI; placeholder
            alert('Open card manager UI (implement modal)');
        }

        // ---------- DataTable init & summary logic ----------
        let dataTable;
        let originalData = {!! json_encode($resultData) !!};

        $(document).ready(function(){
            dataTable = $('#employeeTable').DataTable({
                paging: true,
                pageLength: 100,
                lengthMenu: [[5,10,25,50,100,-1],[5,10,25,50,100,"All"]],
                responsive: true,
                columnDefs: [
                    { orderable:false, targets: [0] }
                ],
                order: [[5, 'desc']],
                drawCallback: function(settings){
                    // update summary cards based on filtered rows
                    const api = this.api();
                    const rows = api.rows({filter:'applied'}).nodes();
                    const filteredArray = [];
                    api.rows({filter:'applied'}).data().each(function(row, idx){
                        // using DOM extract since the cells contain badges
                        const total = parseInt($(rows[idx]).find('td').eq(3).text().trim()) || 0;
                        const pending = parseInt($(rows[idx]).find('td').eq(4).text().trim()) || 0;
                        const overdue = parseInt($(rows[idx]).find('td').eq(5).text().trim()) || 0;
                        const done = parseInt($(rows[idx]).find('td').eq(6).text().trim()) || 0;
                        const etc = parseFloat($(rows[idx]).find('td').eq(8).text().trim()) || 0;
                        filteredArray.push({total_count:total,pending_count:pending,overdue_count:overdue,done_count:done,eta_sum:etc});
                    });
                    updateSummaryCards(filteredArray);
                },
                initComplete: function(){
                    updateSummaryCards(originalData);
                }
            });

            // department filter
            $('#departmentFilter').change(function(){
                dataTable.column(2).search(this.value).draw();
            });
        });

        function updateSummaryCards(data){
            let total=0,pending=0,overdue=0,done=0,etc=0;
            data.forEach(e=>{
                total += e.total_count || 0;
                pending += e.pending_count || 0;
                overdue += e.overdue_count || 0;
                done += e.done_count || 0;
                etc += parseFloat(e.eta_sum) || 0;
            });
            $('#totalTasksCard').text(total);
            $('#pendingTasksCard').text(pending);
            $('#overdueTasksCard').text(overdue);
            $('#doneTasksCard').text(done);
            $('#etcHoursCard').text(Math.round(etc));
        }

        // ---------- Row selection / delete / undo ----------
        let deletedRows = [];
        function toggleSelectAll(){
            const checked = document.getElementById('selectAll').checked;
            document.querySelectorAll('.row-select').forEach(cb => cb.checked = checked);
        }

        function deleteSelected(){
            const checks = Array.from(document.querySelectorAll('.row-select:checked'));
            if (!checks.length) { alert('Please select at least one employee to delete.'); return; }
            deletedRows = [];
            checks.forEach(cb => {
                const row = cb.closest('tr');
                const rowNode = row.cloneNode(true);
                const dtIndex = dataTable.row(row).index();
                deletedRows.push({node: rowNode, index: dtIndex});
                dataTable.row(row).remove();
            });
            dataTable.draw();
            document.getElementById('selectAll').checked = false;
            document.getElementById('undoToast').style.display = 'block';
            setTimeout(()=>{ document.getElementById('undoToast').style.display = 'none'; }, 5000);
        }

        function undoDelete(){
            deletedRows.forEach(item => {
                dataTable.row.add($(item.node)).draw();
            });
            deletedRows = [];
            document.getElementById('undoToast').style.display = 'none';
        }

    </script>
@endpush

@endsection
