@extends('layouts.admin')

@section('title', 'Trip Details')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.trips') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-2"></i>Back to Trips
    </a>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">Trip Info</h5></div>
            <div class="card-body">
                <h6 class="text-primary">{{ $trip->origin_dzongkhag }} → {{ $trip->destination_dzongkhag }}</h6>
                <hr>
                <p class="mb-2"><i class="bi bi-calendar me-2"></i>{{ $trip->departure_datetime->format('M d, Y h:i A') }}</p>
                <p class="mb-2"><i class="bi bi-people me-2"></i>{{ $trip->available_seats }}/{{ $trip->total_seats }} seats</p>
                <p class="mb-2"><i class="bi bi-cash me-2"></i>Nu. {{ number_format($trip->price_per_seat) }} per seat</p>
                <p><span class="badge bg-{{ $trip->status === 'active' ? 'success' : ($trip->status === 'completed' ? 'primary' : 'danger') }}">{{ ucfirst($trip->status) }}</span></p>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h6 class="mb-0">Driver</h6></div>
            <div class="card-body">
                <p><strong>{{ $trip->driver->user->name }}</strong></p>
                <p class="mb-2">{{ $trip->driver->user->phone_number }}</p>
                <p class="mb-0">{{ $trip->driver->taxi_plate_number }}</p>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5 class="mb-0">Bookings</h5>
                @if($trip->status === 'active' && $trip->available_seats > 0)
                    <a href="{{ route('admin.bookings.create', ['tripId' => $trip->id]) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus me-1"></i>Book
                    </a>
                @endif
            </div>
            <div class="card-body">
                @if($trip->bookings->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead><tr><th>Passenger</th><th>Seats</th><th>Payment</th><th>Status</th></tr></thead>
                            <tbody>
                                @foreach($trip->bookings as $booking)
                                    <tr>
                                        <td>
                                            {{ $booking->getPrimaryPassengerName() }}<br><small>{{ $booking->getPrimaryPassengerPhone() }}</small>
                                        </td>
                                        <td>{{ $booking->seats_booked }}</td>
                                        <td>
                                            @if($booking->payment)
                                                <span class="badge bg-{{ $booking->payment->status === 'completed' ? 'success' : 'warning' }}">{{ ucfirst($booking->payment->status) }}</span>
                                            @else
                                                <span class="badge bg-secondary">No Payment</span>
                                            @endif
                                        </td>
                                        <td><span class="badge bg-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'pending' ? 'warning' : 'danger') }}">{{ ucfirst($booking->status) }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center mb-0">No bookings yet</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
