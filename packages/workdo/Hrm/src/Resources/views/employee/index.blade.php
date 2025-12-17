@extends('layouts.main')
@section('page-title')
    {{ __('Team Members') }}
@endsection
@section('page-breadcrumb')
    {{ __('Employee') }}
@endsection
@push('css')
    @include('layouts.includes.datatable-css')
    <style>
        /* Sticky/Frozen Columns and Header Styles - Replicating payroll page approach */
        .table-responsive {
            overflow-x: auto;
            position: relative;
            max-height: 700px; /* Set max height for vertical scrolling */
            overflow-y: auto;   /* Enable vertical scrolling */
        }
        
        #employees-table {
            position: relative;
        }
        
        /* Ensure all other cells have lower z-index */
        #employees-table th,
        #employees-table td {
            position: relative;
            z-index: 1;
        }
        
        /* Sticky Header Styles - Freeze header on vertical scroll */
        #employees-table thead th {
            position: sticky;
            top: 0;
            z-index: 110; /* Higher than other sticky elements */
            background-color: #f8f9fd !important; /* Match DataTables default header background */
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        /* Make Name column sticky using class - Freeze on horizontal scroll */
        #employees-table th.sticky-name-column,
        #employees-table td.sticky-name-column {
            position: sticky;
            left: 0;
            z-index: 100;
            background-color: #fff; /* Default background for opaque column */
            border-right: 2px solid #dee2e6;
        }
        
        /* Header styling for sticky name column - intersection of sticky header and sticky column */
        #employees-table thead th.sticky-name-column {
            z-index: 120 !important; /* Higher than sticky header to stay on top */
            position: sticky !important;
            left: 0 !important;
            top: 0 !important;
            background-color: #f8f9fd !important; /* Match table header background */
        }
        
        /* Match striped row background for sticky column - using DataTables default colors */
        #employees-table tbody tr:nth-of-type(odd) td.sticky-name-column,
        table.dataTable.stripe tbody tr.odd td.sticky-name-column,
        table.dataTable.display tbody tr.odd td.sticky-name-column {
            background-color: #f9f9f9 !important; /* DataTables default odd row */
        }
        
        #employees-table tbody tr:nth-of-type(even) td.sticky-name-column,
        table.dataTable.stripe tbody tr.even td.sticky-name-column,
        table.dataTable.display tbody tr.even td.sticky-name-column {
            background-color: #fff !important; /* DataTables default even row */
        }
        
        /* Match hover effect */
        #employees-table tbody tr:hover td.sticky-name-column {
            background-color: #f6f6f6 !important; /* DataTables hover effect */
        }
        
        /* Shadow effect for better visual separation */
        #employees-table th.sticky-name-column::after,
        #employees-table td.sticky-name-column::after {
            content: '';
            position: absolute;
            top: 0;
            right: -2px;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to right, rgba(0,0,0,0.1), transparent);
            pointer-events: none;
        }
        
        /* Ensure text doesn't wrap in sticky column */
        #employees-table th.sticky-name-column,
        #employees-table td.sticky-name-column {
            white-space: nowrap;
            min-width: fit-content;
        }
    </style>
@endpush
@section('page-action')
    <div class="d-flex">
        @stack('addButtonHook')
        @permission('employee import')
            <a href="#" class="btn btn-sm btn-primary me-2" data-ajax-popup="true" data-title="{{ __('Employee Import') }}"
                data-url="{{ route('employee.file.import') }}" data-toggle="tooltip" title="{{ __('Import') }}"><i
                    class="ti ti-file-import"></i>
            </a>
        @endpermission
        <a href="{{ route('employee.grid') }}" class="btn btn-sm btn-primary btn-icon me-2"
            data-bs-toggle="tooltip"title="{{ __('Grid View') }}">
            <i class="ti ti-layout-grid text-white"></i>
        </a>
        @permission('employee create')
            <a href="{{ route('employee.create') }}" data-title="{{ __('Create New Employee') }}" data-bs-toggle="tooltip"
                title="" class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Create') }}">
                <i class="ti ti-plus"></i>
            </a>
        @endpermission
    </div>
@endsection
@php
    $company_settings = getCompanyAllSetting();
@endphp
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        {{ $dataTable->table(['width' => '100%']) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('layouts.includes.datatable-js')
    {{ $dataTable->scripts() }}
    <script>
        $(document).ready(function() {
            var table = $('#employees-table').DataTable();
            var detailsVisible = false; // Global state for details visibility
            
            // Function to add sticky column class to name column
            function addStickyColumnClass() {
                // Find the name column index
                var nameColumnIndex = null;
                table.columns().every(function(colIndex) {
                    var headerText = $(this.header()).text().trim().toLowerCase();
                    if (headerText === 'name') {
                        nameColumnIndex = colIndex;
                        return false; // break
                    }
                });
                
                if (nameColumnIndex !== null) {
                    // Add class to header and all cells in that column
                    var header = table.column(nameColumnIndex).header();
                    $(header).addClass('sticky-name-column');
                    
                    table.cells(null, nameColumnIndex).nodes().to$().addClass('sticky-name-column');
                }
            }
            
            // Call on initial load and after each draw
            addStickyColumnClass();
            
            // Function to get column indexes for email, phone, department, designation
            function getTargetColumnIndexes() {
                var indexes = [];
                table.columns().every(function(colIndex) {
                    var headerText = $(this.header()).text().trim().toLowerCase();
                    if (headerText === 'email' || 
                        headerText === 'phone' ||
                        headerText.includes('department') ||
                        headerText.includes('designation')) {
                        indexes.push(colIndex);
                    }
                });
                return indexes;
            }
            
            // Add CSS to hide cells and headers by default
            if (!$('#hidden-details-style').length) {
                $('<style id="hidden-details-style">')
                    .prop('type', 'text/css')
                    .html(`
                        td.hidden-details-cell, th.hidden-details-header { 
                            display: none !important; 
                        }
                    `)
                    .appendTo('head');
            }
            
            // Function to hide/show details for all rows
            function toggleAllRowsDetails(show, columnIndexes) {
                // For each target column, hide/show all its cells
                columnIndexes.forEach(function(colIndex) {
                    var column = table.column(colIndex);
                    var columnNodes = column.nodes();
                    
                    $(columnNodes).each(function() {
                        var cell = $(this);
                        if (show) {
                            cell.removeClass('hidden-details-cell');
                        } else {
                            cell.addClass('hidden-details-cell');
                        }
                    });
                });
            }
            
            // Function to update column headers visibility
            function updateColumnHeaders(columnIndexes, show) {
                columnIndexes.forEach(function(colIndex) {
                    var header = table.column(colIndex).header();
                    if (show) {
                        $(header).removeClass('hidden-details-header');
                    } else {
                        $(header).addClass('hidden-details-header');
                    }
                });
            }
            
            // Function to update all toggle buttons
            function updateAllToggleButtons(show) {
                // First, hide and dispose all toggle button tooltips to prevent stuck tooltips
                $('.toggle-details-btn').each(function() {
                    var tooltipInstance = bootstrap.Tooltip.getInstance(this);
                    if (tooltipInstance) {
                        tooltipInstance.hide();
                        tooltipInstance.dispose();
                    }
                });
                
                // Then update button content
                table.rows().every(function() {
                    var rowNode = this.node();
                    var button = $(rowNode).find('.toggle-details-btn');
                    
                    if (show) {
                        button.html('<i class="ti ti-eye-off"></i>');
                        button.data('expanded', true);
                        button.attr('data-bs-original-title', 'Hide Details');
                        button.attr('title', 'Hide Details');
                    } else {
                        button.html('<i class="ti ti-eye"></i>');
                        button.data('expanded', false);
                        button.attr('data-bs-original-title', 'Show Details');
                        button.attr('title', 'Show Details');
                    }
                });
                
                // Reinitialize tooltips after DOM updates
                // The DataTables drawCallback will also handle this, but we do it here
                // to ensure tooltips work immediately after button updates
                $('.toggle-details-btn').each(function() {
                    if (!bootstrap.Tooltip.getInstance(this)) {
                        new bootstrap.Tooltip(this);
                    }
                });
            }
            
            // Initialize: hide headers and cells for target columns
            var targetColumnIndexes = getTargetColumnIndexes();
            updateColumnHeaders(targetColumnIndexes, false);
            toggleAllRowsDetails(false, targetColumnIndexes);
            
            // Handle table redraws
            table.on('draw', function() {
                targetColumnIndexes = getTargetColumnIndexes();
                updateColumnHeaders(targetColumnIndexes, detailsVisible);
                toggleAllRowsDetails(detailsVisible, targetColumnIndexes);
                updateAllToggleButtons(detailsVisible);
                // Re-apply sticky column class after redraw
                addStickyColumnClass();
            });
            
            // Handle toggle button clicks - affects all rows
            $(document).on('click', '.toggle-details-btn', function(e) {
                e.preventDefault();
                var targetColumnIndexes = getTargetColumnIndexes();
                
                // Toggle global state
                detailsVisible = !detailsVisible;
                
                // Toggle visibility for all rows
                toggleAllRowsDetails(detailsVisible, targetColumnIndexes);
                
                // Update column headers
                updateColumnHeaders(targetColumnIndexes, detailsVisible);
                
                // Update all toggle buttons to reflect the same state
                updateAllToggleButtons(detailsVisible);
            });
        });
    </script>
@endpush
