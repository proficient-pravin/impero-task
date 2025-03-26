<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetBranchAvailabilityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'availability'                      => 'required|array',
            'availability.*.day_of_week'        => 'required|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'availability.*.times'              => 'array',
            'availability.*.times.*.start_time' => 'required_with:availability.*.times.*.end_time|date_format:H:i',
            'availability.*.times.*.end_time'   => 'required_with:availability.*.times.*.start_time|date_format:H:i|after:availability.*.times.*.start_time',

            'unavailability'                    => 'array',
            'unavailability.*.date'             => 'required|date',
            'unavailability.*.status'           => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'availability.required'                           => 'Please provide availability details.',
            'availability.*.day_of_week.required'             => 'Each availability entry must include the day of the week.',
            'availability.*.day_of_week.in'                   => 'Invalid day of the week provided.',
            'availability.*.times.array'                      => 'The time slots must be in a valid format.',
            'availability.*.times.*.start_time.required_with' => 'A start time is required when an end time is provided.',
            'availability.*.times.*.start_time.date_format'   => 'The start time must be in the correct format (HH:mm).',
            'availability.*.times.*.end_time.required_with'   => 'An end time is required when a start time is provided.',
            'availability.*.times.*.end_time.date_format'     => 'The end time must be in the correct format (HH:mm).',
            'availability.*.times.*.end_time.after'           => 'The end time must be after the start time.',

            'unavailability.array'                            => 'The unavailability section must be in a valid format.',
            'unavailability.*.date.required'                  => 'Each unavailability entry must include a date.',
            'unavailability.*.date.date'                      => 'The unavailability date must be a valid date.',
            'unavailability.*.date.after_or_equal'            => 'Unavailability dates cannot be in the past.',
            'unavailability.*.status.required'                => 'A status is required for each unavailability entry.',
            'unavailability.*.status.boolean'                 => 'Invalid status value for unavailability.',
        ];
    }
}
