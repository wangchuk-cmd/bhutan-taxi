@extends('layouts.driver')

@section('title', 'Trip Passengers')

@section('content')
<div class="mb-4">
    <a href="{{ route('driver.trips') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-2"></i>Back to Trips
    </a>
</div>

<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="bi bi-geo-alt me-2"></i>
            {{ $trip->origin_dzongkhag }} → {{ $trip->destination_dzongkhag }}
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <small class="text-muted">Date & Time</small>
                <p class="fw-bold mb-0">{{ $trip->departure_datetime->format('M d, Y h:i A') }}</p>
            </div>
            <div class="col-md-4">
                <small class="text-muted">Seats Booked</small>
                <p class="fw-bold mb-0">{{ $trip->total_seats - $trip->available_seats }} / {{ $trip->total_seats }}</p>
            </div>
            <div class="col-md-4">
                <small class="text-muted">Status</small>
                <p class="mb-0">
                    <span class="badge bg-{{ $trip->status === 'active' ? 'success' : ($trip->status === 'completed' ? 'primary' : 'danger') }}">
                        {{ ucfirst($trip->status) }}
                    </span>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-people me-2"></i>Passenger List</h5>
    </div>
    <div class="card-body">
        @if($bookings->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Passenger Name</th>
                            <th>Phone</th>
                            <th>Booking Info</th>
                            <th>Payment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $passengerCount = 0; @endphp
                        @foreach($bookings as $booking)
                            @foreach($booking->passengers_info as $passenger)
                                @php $passengerCount++; @endphp
                                <tr>
                                    <td>{{ $passengerCount }}</td>
                                    <td><strong>{{ $passenger['name'] }}</strong></td>
                                    <td>
                                        <a href="tel:{{ $passenger['phone'] }}" class="text-decoration-none">
                                            <i class="bi bi-telephone me-1"></i>{{ $passenger['phone'] }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $booking->seats_booked }} seat(s)</span>
                                        @if($booking->booking_type === 'full')
                                            <span class="badge bg-warning text-dark">Full Taxi</span>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-success">Paid</span></td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-people display-1 text-muted"></i>
                <p class="mt-3 text-muted">No passengers booked yet</p>
            </div>
        @endif
    </div>
</div>
@endsection
