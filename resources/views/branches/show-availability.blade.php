@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4 text-center">Branch Availability: {{ $branch->name }}</h2>
        <form method="GET" action="{{ route('branches.availability.show', $branch->id) }}" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}"
                        required>
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" required>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Check Availability</button>
                </div>
            </div>
        </form>

        @if (!empty($availability))
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Available Time Slots</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($availability as $date => $data)
                        <tr>
                        <td>{{ \Carbon\Carbon::parse($date)->format('l, d M Y') }}</td>
                            <td>
                                <span class="badge {{ $data['status'] == 'available' ? 'bg-success' : 'bg-danger' }}">
                                    {{ ucfirst($data['status']) }}
                                </span>
                            </td>
                            <td>
                                @if ($data['status'] == 'available' && !empty($data['times']))
                                    @foreach ($data['times'] as $time)
                                        <span class="badge bg-primary">{{ $time['start_time'] }} -
                                            {{ $time['end_time'] }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">No available slots</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-center text-muted">Select a date range to check availability.</p>
        @endif
    </div>
@endsection
