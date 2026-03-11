@extends('layouts.driver')

@section('title', 'My Trips')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-map me-2"></i>My Trips</h4>
    <a href="{{ route('driver.trips.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Create Trip
    </a>
</div>

<div class="card">
    <div class="card-body">
        @if($trips->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Route</th>
                            <th>Date & Time</th>
                            <th>Seats</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($trips as $trip)
                            <tr>
                                <td>
                                    <strong>{{ $trip->origin_dzongkhag }}</strong>
                                    <i class="bi bi-arrow-right mx-1"></i>
                                    {{ $trip->destination_dzongkhag }}
                                </td>
                                <td>{{ $trip->departure_datetime->format('M d, Y h:i A') }}</td>
                                <td>
                                    <span class="badge {{ $trip->available_seats > 0 ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $trip->available_seats }}/{{ $trip->total_seats }}
                                    </span>
                                </td>
                                <td>Nu. {{ number_format($trip->price_per_seat) }}</td>
                                <td>
                                    @if($trip->status === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($trip->status === 'completed')
                                        <span class="badge bg-primary">Completed</span>
                                    @else
                                        <span class="badge bg-danger">Cancelled</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('driver.passengers', $trip->id) }}" class="btn btn-sm btn-outline-primary" title="View Passengers">
                                            <i class="bi bi-people"></i>
                                        </a>
                                        @if($trip->status === 'active' && $trip->departure_datetime > now())
                                            <a href="{{ route('driver.trips.edit', $trip->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('driver.trips.cancel', $trip->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Cancel this trip? All passengers will be refunded.');">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-danger" title="Cancel"><i class="bi bi-x-circle"></i></button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $trips->links() }}
        @else
            <div class="text-center py-5">
                <i class="bi bi-map display-1 text-muted"></i>
                <p class="mt-3 text-muted">No trips created yet</p>
                <a href="{{ route('driver.trips.create') }}" class="btn btn-primary">Create Your First Trip</a>
            </div>
        @endif
    </div>
</div>
@endsection
