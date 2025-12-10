<?php

namespace App\DataTables;

use App\Models\Review;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ReviewDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $rowcolumn = ['reviewer_name', 'reviewee_name', 'rating', 'description', 'created_at', 'action'];

        $dataTable = (new EloquentDataTable($query))
            ->addColumn('reviewer_name', function (Review $review) {
                return $review->reviewer ? $review->reviewer->name : 'N/A';
            })
            ->addColumn('reviewee_name', function (Review $review) {
                return $review->reviewee ? $review->reviewee->name : 'N/A';
            })
            ->editColumn('rating', function (Review $review) {
                $stars = '';
                for ($i = 1; $i <= 5; $i++) {
                    if ($i <= $review->rating) {
                        $stars .= '<i class="fas fa-star text-warning"></i>';
                    } else {
                        $stars .= '<i class="far fa-star text-muted"></i>';
                    }
                }
                return $stars . ' (' . $review->rating . '/5)';
            })
            ->editColumn('description', function (Review $review) {
                return '<div class="text-wrap" style="max-width: 300px;">' . htmlspecialchars($review->description) . '</div>';
            })
            ->editColumn('created_at', function (Review $review) {
                return date('M d, Y H:i', strtotime($review->created_at));
            })
            ->addColumn('action', function (Review $review) {
                $html = '';
                if (Auth::user()->type == 'super admin' || 
                    Auth::user()->email == 'president@5core.com' || 
                    Auth::user()->email == 'tech-support@5core.com' ||
                    Auth::user()->email == 'support@5core.com' ||
                    Auth::user()->email == 'mgr-content@5core.com' ) {
                    $html .= '<div class="action-btn bg-danger ms-2">';
                    $html .= '<a href="#" class="mx-3 btn btn-sm d-inline-flex align-items-center text-white" ';
                    $html .= 'onclick="deleteReview(' . $review->id . ')" title="' . __('Delete') . '">';
                    $html .= '<i class="ti ti-trash"></i>';
                    $html .= '</a>';
                    $html .= '</div>';
                }
                return $html;
            })
            ->rawColumns(['rating', 'description', 'action']);

        return $dataTable->addIndexColumn();
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Review $model): QueryBuilder
    {
        // Show ALL reviews for admin - SIMPLE!
        return $model->newQuery()
            ->with(['reviewer', 'reviewee'])
            ->orderBy('created_at', 'desc');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('reviews-table')
            ->columns($this->getColumns())
            ->minifiedAjax(route('reviews.data'))
            ->dom('Bfrtip')
            ->orderBy(0)
            ->buttons([
                Button::make('export'),
                Button::make('print'),
                Button::make('reload')
            ])
            ->parameters([
                'processing' => true,
                'serverSide' => true,
            ])
            ->language([
                'buttons' => [
                    'create' => __('Create'),
                    'export' => __('Export'),
                    'print' => __('Print'),
                    'reload' => __('Reload'),
                    'csv' => __('CSV'),
                    'excel' => __('Excel'),
                ]
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')
                ->title(__('Sr. No.'))
                ->searchable(false)
                ->orderable(false)
                ->width(60),
            Column::make('reviewer_name')
                ->title(__('Reviewer'))
                ->searchable(true)
                ->orderable(true),
            Column::make('reviewee_name')
                ->title(__('Reviewee'))
                ->searchable(true)
                ->orderable(true),
            Column::make('rating')
                ->title(__('Rating'))
                ->searchable(false)
                ->orderable(true),
            Column::make('description')
                ->title(__('Description'))
                ->searchable(true)
                ->orderable(false),
            Column::make('created_at')
                ->title(__('Date'))
                ->searchable(false)
                ->orderable(true),
            Column::computed('action')
                ->title(__('Action'))
                ->exportable(false)
                ->printable(false)
                ->width(100)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Reviews_' . date('YmdHis');
    }
}
