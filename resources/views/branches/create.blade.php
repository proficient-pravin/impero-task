@extends('layouts.app')

@section('title', 'Add Branch')

@section('content')
    <div class="container">
        <h2 class="mb-4">Add New Branch for {{ $business->name }}</h2>

        <form id="branchForm">
            @csrf
            <input type="hidden" name="business_id" value="{{ $business->id }}">

            <div class="mb-3">
                <label for="name" class="form-label">Branch Name</label>
                <input type="text" class="form-control" id="name" name="name">
                <div class="text-danger" id="nameError"></div>
            </div>

            <div class="mb-3">
                <label for="images" class="form-label">Branch Images</label>
                <input type="file" class="form-control" id="images" name="images[]" multiple>
                <div class="text-danger" id="imagesError"></div>
            </div>

            <button type="submit" class="btn btn-primary">
                <span id="submitText">Submit</span>
                <span id="loader" class="spinner-border spinner-border-sm d-none" role="status"
                    aria-hidden="true"></span>
            </button>
        </form>
    </div>

    <!-- Toastr CSS & JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#branchForm').submit(function(e) {
                e.preventDefault();

                var formData = new FormData(this);

                $('#submitText').text('Submitting...');
                $('#loader').removeClass('d-none');

                $.ajax({
                    url: "{{ route('branches.store') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('.text-danger').text('');
                    },
                    success: function(response) {
                        $('#submitText').text('Submit');
                        $('#loader').addClass('d-none');

                        if (response.status === 'success') {
                            toastr.success(response.message);
                            setTimeout(function() {
                                window.location.href =
                                    "{{ route('businesses.branches', $business->id) }}";
                            }, 2000);
                        }
                    },
                    error: function(response) {
                        $('#submitText').text('Submit');
                        $('#loader').addClass('d-none');

                        toastr.error('There were errors in your submission.');

                        var errors = response.responseJSON.errors;
                        if (errors.name) $('#nameError').text(errors.name[0]);
                        if (errors.images) $('#imagesError').text(errors.images[0]);
                    }
                });
            });
        });
    </script>
@endsection
