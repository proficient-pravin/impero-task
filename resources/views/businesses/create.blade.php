@extends('layouts.app')

@section('title', 'Add Business')

@section('content')
    <div class="container">
        <h2 class="mb-4">Add New Business</h2>

        <form id="businessForm">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Business Name</label>
                <input type="text" class="form-control" id="name" name="name">
                <div class="text-danger" id="nameError"></div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email">
                <div class="text-danger" id="emailError"></div>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone">
                <div class="text-danger" id="phoneError"></div>
            </div>

            <div class="mb-3">
                <label for="logo" class="form-label">Logo (Optional)</label>
                <input type="file" class="form-control" id="logo" name="logo">
                <div class="text-danger" id="logoError"></div>
            </div>

            <button type="submit" class="btn btn-primary">
                <span id="submitText">Submit</span>
                <span id="loader" class="spinner-border spinner-border-sm d-none" role="status"></span>
            </button>
        </form>
    </div>

    <!-- Toastr CSS & JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#businessForm').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                // Show loader and disable button
                $('#submitText').text('Submitting...');
                $('#loader').removeClass('d-none');

                $.ajax({
                    url: "{{ route('businesses.store') }}",
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
                                    "{{ route('businesses.index') }}";
                            }, 2000);
                        }
                    },
                    error: function(response) {
                        $('#submitText').text('Submit');
                        $('#loader').addClass('d-none');

                        toastr.error('There were errors in your submission.');

                        var errors = response.responseJSON.errors;
                        if (errors.name) $('#nameError').text(errors.name[0]);
                        if (errors.email) $('#emailError').text(errors.email[0]);
                        if (errors.phone) $('#phoneError').text(errors.phone[0]);
                        if (errors.logo) $('#logoError').text(errors.logo[0]);
                    }
                });
            });
        });
    </script>
@endsection
