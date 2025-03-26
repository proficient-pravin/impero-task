@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="text-center mb-4">Set Branch Availability</h2>
        <form id="availability-form">
            @csrf
            <input type="hidden" name="branch_id" value="{{ $branch->id }}">
            <div class="row">
                <div class="col-6 mx-auto">
                    <h4 class="mb-3">Weekly Availability</h4>

                    @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                        <div class="card p-3 mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input toggle-availability"
                                    id="available_{{ $day }}" name="availability[{{ $day }}][status]"
                                    value="1">
                                <label class="form-check-label" for="available_{{ $day }}">
                                    {{ $day }}
                                </label>
                            </div>
                            <div class="time-slots mt-3 d-none" data-day="{{ $day }}">
                                <button type="button" class="btn btn-success add-slot mb-2"
                                    data-day="{{ $day }}">Add Time Slot</button>
                                <div class="time-slot d-flex align-items-center">
                                    <input type="time" name="availability[{{ $day }}][times][0][start_time]"
                                        class="form-control start-time me-2">
                                    <span class="me-2">-</span>
                                    <input type="time" name="availability[{{ $day }}][times][0][end_time]"
                                        class="form-control end-time me-2">
                                    <button type="button" class="btn btn-danger remove-slot">Remove</button>
                                </div>
                                <span class="text-danger error-msg d-block mt-2"></span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="col-6 mx-auto">
                    <h4 class="mb-3">Unavailability Dates</h4>
                    <div id="unavailability-section">
                        <div class="d-flex align-items-center unavailability-slot mb-2">
                            <input type="date" name="unavailability[0][date]" class="form-control me-2">
                            <button type="button" class="btn btn-danger remove-date">Remove</button>
                        </div>
                    </div>
                    <button type="button" class="btn btn-success mt-2" id="add-unavailability">Add Unavailable
                        Date</button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary d-flex align-items-center" id="save-button">
                        <span id="loading-spinner" class="spinner-border spinner-border-sm text-light me-2 d-none"
                            role="status"></span>
                        Save Availability
                    </button>
                </div>
            </div>
        </form>
    </div>
    <!-- Toastr CSS & JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            let csrfToken = "{{ csrf_token() }}";

            // Show time slots when availability checkbox is checked
            $('.toggle-availability').change(function() {
                let day = $(this).attr('id').split('_')[1];
                let slots = $('.time-slots[data-day="' + day + '"]');
                slots.toggleClass('d-none', !$(this).is(':checked'));
            });

            // Add new time slot dynamically
            $(document).on('click', '.add-slot', function() {
                let day = $(this).data('day');
                let index = $('.time-slots[data-day="' + day + '"] .time-slot').length;
                let newSlot = `
                    <div class="time-slot d-flex align-items-center mt-2">
                        <input type="time" name="availability[${day}][times][${index}][start_time]" class="form-control start-time me-2">
                        <span class="me-2">-</span>
                        <input type="time" name="availability[${day}][times][${index}][end_time]" class="form-control end-time me-2">
                        <button type="button" class="btn btn-danger remove-slot">Remove</button>
                    </div>`;
                $('.time-slots[data-day="' + day + '"]').append(newSlot);
            });

            // Remove time slot
            $(document).on('click', '.remove-slot', function() {
                $(this).closest('.time-slot').remove();
            });

            // Add unavailable date dynamically
            $('#add-unavailability').click(function() {
                let index = $('.unavailability-slot').length;
                let newDate = `
                    <div class="d-flex align-items-center unavailability-slot mb-2">
                        <input type="date" name="unavailability[${index}][date]" class="form-control me-2">
                        <button type="button" class="btn btn-danger remove-date">Remove</button>
                    </div>`;
                $('#unavailability-section').append(newDate);
            });

            // Remove unavailable date
            $(document).on('click', '.remove-date', function() {
                $(this).closest('.unavailability-slot').remove();
            });

            // Handle form submission
            $('#availability-form').on('submit', function(e) {
                e.preventDefault();
                $('#loading-spinner').removeClass('d-none'); // Show loader

                let availabilityData = {};
                $('.toggle-availability:checked').each(function() {
                    let day = $(this).attr('id').split('_')[1];
                    let times = [];

                    $('.time-slots[data-day="' + day + '"] .time-slot').each(function() {
                        let start = $(this).find('.start-time').val();
                        let end = $(this).find('.end-time').val();
                        if (start && end) {
                            times.push({
                                start_time: start,
                                end_time: end
                            });
                        }
                    });

                    availabilityData[day] = {
                        day_of_week: day,
                        times: times
                    };
                });

                let unavailabilityData = [];
                $('.unavailability-slot').each(function() {
                    let date = $(this).find('input[type="date"]').val();
                    if (date) {
                        unavailabilityData.push({
                            date: date,
                            status: 1
                        });
                    }
                });

                let formData = {
                    _token: csrfToken,
                    branch_id: $('input[name="branch_id"]').val(),
                    availability: availabilityData,
                    unavailability: unavailabilityData
                };
                $('.error-msg').html('');
                let indexUrl = "{{ route('branches.availability.show', $branch->id) }}"
                $.ajax({
                    url: "{{ route('branches.availability.update', $branch->id) }}",
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        $('.error-msg').html('');
                        toastr.success(response.message);
                        // location.reload();
                        window.location.href = indexUrl
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON.message);
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(field, messages) {
                            let fieldParts = field.split('.');
                            let day = fieldParts[1];

                            if (field.includes('times')) {
                                $('.time-slots[data-day="' + day + '"] .error-msg')
                                    .html(messages.join('<br>'));
                            } else if (field.includes('unavailability')) {
                                $('#unavailability-section').after(
                                    '<span class="text-danger error-msg">' +
                                    messages.join('<br>') + '</span>');
                            } else {
                                $('[name="' + field + '"]').after(
                                    '<span class="text-danger error-msg">' +
                                    messages[0] + '</span>');
                            }
                        });
                    },
                    complete: function() {
                        $('#loading-spinner').addClass('d-none'); // Hide loader
                    }
                });
            });

        });
    </script>
@endsection
