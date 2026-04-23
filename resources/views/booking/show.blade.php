@extends('layouts.app')

@section('title', 'Booking Details')

@section('content')
@include('components.confirm-modal')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('bookings.my') }}">My Bookings</a></li>
            <li class="breadcrumb-item active">Booking #{{ $booking->id }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <!-- Booking Status -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Booking #{{ $booking->id }}</h4>
                        @if($booking->status === 'active' && $booking->payment_status === 'paid')
                            <span class="badge bg-success fs-6"><i class="bi bi-check-circle me-1"></i>Confirmed</span>
                        @elseif($booking->status === 'cancelled')
                            <span class="badge bg-danger fs-6"><i class="bi bi-x-circle me-1"></i>Cancelled</span>
                        @else
                            <span class="badge bg-warning text-dark fs-6"><i class="bi bi-clock me-1"></i>Pending</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Trip Details -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Trip Details</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex mb-3">
                                <i class="bi bi-geo-alt text-primary me-3 fs-4"></i>
                                <div>
                                    <small class="text-muted d-block">Route</small>
                                    <strong>{{ $booking->trip->origin_dzongkhag }} → {{ $booking->trip->destination_dzongkhag }}</strong>
                                </div>
                            </div>
                            <div class="d-flex mb-3">
                                <i class="bi bi-calendar3 text-primary me-3 fs-4"></i>
                                <div>
                                    <small class="text-muted d-block">Date</small>
                                    <strong>{{ $booking->trip->departure_datetime->format('l, F d, Y') }}</strong>
                                </div>
                            </div>
                            <div class="d-flex">
                                <i class="bi bi-clock text-primary me-3 fs-4"></i>
                                <div>
                                    <small class="text-muted d-block">Departure Time</small>
                                    <strong>{{ $booking->trip->departure_datetime->format('h:i A') }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex mb-3">
                                <i class="bi bi-person-circle text-success me-3 fs-4"></i>
                                <div>
                                    <small class="text-muted d-block">Driver</small>
                                    <strong>{{ $booking->trip->driver->user->name }}</strong>
                                </div>
                            </div>
                            @if($booking->payment_status === 'paid')
                            <div class="d-flex mb-3">
                                <i class="bi bi-telephone text-success me-3 fs-4"></i>
                                <div>
                                    <small class="text-muted d-block">Driver Contact</small>
                                    <strong>
                                        <a href="tel:{{ $booking->trip->driver->user->phone_number }}" class="text-decoration-none">
                                            {{ $booking->trip->driver->user->phone_number }}
                                        </a>
                                    </strong>
                                </div>
                            </div>
                            @else
                            <div class="d-flex mb-3">
                                <i class="bi bi-telephone text-muted me-3 fs-4"></i>
                                <div>
                                    <small class="text-muted d-block">Driver Contact</small>
                                    <span class="text-muted fst-italic"><i class="bi bi-lock me-1"></i>Available after payment</span>
                                </div>
                            </div>
                            @endif
                            <div class="d-flex">
                                <i class="bi bi-car-front text-success me-3 fs-4"></i>
                                <div>
                                    <small class="text-muted d-block">Vehicle</small>
                                    <strong>{{ $booking->trip->driver->vehicle_type }} ({{ $booking->trip->driver->fuel_type === 'Electric' ? '⚡ Electric' : '🛢️ Fuel' }})</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-ticket-perforated me-2"></i>Booking Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <small class="text-muted d-block mb-2">Passenger(s)</small>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Phone</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($booking->passengers_info as $index => $passenger)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $passenger['name'] }}</td>
                                                <td><a href="tel:{{ $passenger['phone'] }}">{{ $passenger['phone'] }}</a></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Booking Type</small>
                            <strong class="text-capitalize">{{ $booking->booking_type }}</strong>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Seats Booked</small>
                            <strong>{{ $booking->seats_booked }}</strong>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Booking Time</small>
                            <strong>{{ $booking->booking_time->format('M d, Y h:i A') }}</strong>
                        </div>
                        @if($booking->payment_time)
                            <div class="col-md-4">
                                <small class="text-muted d-block">Payment Time</small>
                                <strong>{{ $booking->payment_time->format('M d, Y h:i A') }}</strong>
                            </div>
                        @endif
                        @if($booking->cancellation_time)
                            <div class="col-md-4">
                                <small class="text-muted d-block">Cancelled At</small>
                                <strong class="text-danger">{{ $booking->cancellation_time->format('M d, Y h:i A') }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Payment Summary -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>Payment</h5>
                </div>
                <div class="card-body">
                    @php
                        $totalAmount = $booking->booking_type === 'full' 
                            ? $booking->trip->full_taxi_price 
                            : $booking->trip->price_per_seat * $booking->seats_booked;
                    @endphp
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Amount</span>
                        <strong>Nu. {{ number_format($totalAmount, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Payment Status</span>
                        @if($booking->payment_status === 'paid')
                            <span class="badge bg-success">Paid</span>
                        @elseif($booking->payment_status === 'failed')
                            <span class="badge bg-danger">Failed</span>
                        @else
                            <span class="badge bg-warning text-dark">Pending</span>
                        @endif
                    </div>
                    @if($booking->payment)
                        <div class="d-flex justify-content-between mb-2">
                            <span>Payment Method</span>
                            <span class="text-capitalize">{{ $booking->payment->payment_method }}</span>
                        </div>
                    @endif
                    @if($booking->refund_status !== 'none')
                        <div class="d-flex justify-content-between">
                            <span>Refund Status</span>
                            <span class="badge bg-info text-capitalize">{{ $booking->refund_status }}</span>
                        </div>
                    @endif
                    
                    @if($booking->payment_status === 'paid' && $booking->payment)
                        <hr>
                        <a href="{{ route('booking.receipt', $booking->id) }}" class="btn btn-outline-success w-100">
                            <i class="bi bi-receipt me-2"></i>View & Print Receipt
                        </a>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            @if($booking->canCancel())
                <div class="card">
                    <div class="card-body">
                        <p class="text-muted small mb-3">
                            <i class="bi bi-info-circle me-1"></i>
                            You can cancel this booking for a full refund (more than 24 hours before departure).
                        </p>
                        <form id="cancelBookingForm" action="{{ route('booking.cancel', $booking->id) }}" method="POST">
                            @csrf
                            <button type="button" class="btn btn-danger w-100" onclick="showConfirmModal('Are you sure you want to cancel this booking? You will receive a full refund.', 'Cancel Booking', function() { document.getElementById('cancelBookingForm').submit(); })">
                                <i class="bi bi-x-circle me-2"></i>Cancel Booking
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
