{{ Form::open(['method' => 'post', 'enctype' => 'multipart/form-data', 'id' => 'upload_form', 'class' =>
'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-md-12 mt-1">
            {{ Form::label('file', __('Select Excel File'), ['class' => 'col-form-label']) }}<x-required></x-required>
            <div class="choose-file form-group">
                <label for="file" class="col-form-label">
                    <input type="file" class="form-control" name="faq_excel" id="file" accept=".xlsx"
                        data-filename="upload_file" required>
                </label>
                <p class="upload_file"></p>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
    <button type="submit" value="{{ __('Upload') }}" class="btn btn-primary ms-2">
        {{__('Upload')}}
    </button>
    <a href="" data-url="{{ route('project.import.modal') }}" data-ajax-popup-over="true" title="{{ __('Create') }}"
        data-size="xl" data-title="{{ __('Import Project CSV Data') }}" class="d-none import_modal_show"></a>
</div>
{{ Form::close() }}
<script>
    $('#upload_form').on('submit', function (event) {
        event.preventDefault();

        let data = new FormData(this);
        data.append('_token', "{{ csrf_token() }}");

        $.ajax({
            url: "{{ route('chatbot.uploadFAQ') }}",
            method: "POST",
            data: data,
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                console.log(data);
                if (data.status == true) {
                    toastrs('Success', data.message, 'Success');

                    // ⏳ Delay reload by 3 seconds
                    setTimeout(function () {
                        location.reload();
                    }, 3000);

                } else {
                    toastrs('Error', data.message, 'error');
                }
            }
        });
    });
</script>