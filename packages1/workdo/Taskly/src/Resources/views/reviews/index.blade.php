@extends('layouts.main')

@section('page-title')
    {{ __('Reviews Management') }}
@endsection

@section('page-breadcrumb')
    {{ __('Reviews') }}
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header card-body table-border-style">
                    <div class="table-responsive">
                        {{ $dataTable->table(['width' => '100%', 'class' => 'table table-striped table-bordered']) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{ $dataTable->scripts() }}
    
    <script>
        function deleteReview(id) {
            if (confirm('{{ __("Are you sure you want to delete this review?") }}')) {
                $.ajax({
                    url: '{{ route("reviews.destroy", ":id") }}'.replace(':id', id),
                    type: 'DELETE',
                    data: {
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastrs('Success', response.success, 'success');
                            $('#reviews-table').DataTable().ajax.reload();
                        } else {
                            toastrs('Error', response.error, 'error');
                        }
                    },
                    error: function(xhr) {
                        toastrs('Error', '{{ __("Something went wrong.") }}', 'error');
                    }
                });
            }
        }
    </script>
@endpush
