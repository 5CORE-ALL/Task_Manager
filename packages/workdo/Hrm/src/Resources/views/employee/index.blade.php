@extends('layouts.main')
@section('page-title')
    {{ __('Team Members') }}
@endsection
@section('page-breadcrumb')
    {{ __('Employee') }}
@endsection
@push('css')
    @include('layouts.includes.datatable-css')
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
