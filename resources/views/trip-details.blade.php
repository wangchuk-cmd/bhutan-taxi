@extends('layouts.app')

@section('title', 'Trip Details')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('search') }}">Search</a></li>
            <li class="breadcrumb-item active">Trip Details</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Main Trip Info -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-geo-alt me-2"></i>
                        {{ $trip->origin_dzongkhag }} 
                        <i class="bi bi-arrow-right mx-2"></i> 
                        {{ $trip->destination_dzongkhag }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Trip Information</h6>
                            <div class="d-flex mb-3">
                                <i class="bi bi-calendar3 text-primary me-3 fs-4"></i>
                                <div>
                                    <small class="text-muted d-block">Date</small>
                                    <strong>{{ $trip->departure_datetime->format('l, F d, Y') }}</strong>
                                </div>
                            </div>
                            <div class="d-flex mb-3">
                                <i class="bi bi-clock text-primary me-3 fs-4"></i>
                                <div>
                                    <small class="text-muted d-block">Departure Time</small>
                                    <strong>{{ $trip->departure_datetime->format('h:i A') }}</strong>
                                </div>
                            </div>
                            @if($trip->route)
                            <div class="d-flex mb-3">
                                <i class="bi bi-signpost-2 text-primary me-3 fs-4"></i>
                                <div>
                                    <small class="text-muted d-block">Distance</small>
                                    <strong>{{ $trip->route->distance_km }} km</strong>
                                </div>
                            </div>
                            <div class="d-flex">
                                <i class="bi bi-hourglass-split text-primary me-3 fs-4"></i>
                                <div>
                                    <small class="text-muted d-block">Estimated Time</small>
                                    <strong>{{ $trip->route->estimated_time }}</strong>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Driver & Vehicle</h6>
                            <div class="d-flex mb-3">
                                <i class="bi bi-person-circle text-success me-3 fs-4"></i>
                                <div>
                                    <small class="text-muted d-block">Driver Name</small>
                                    <strong>{{ $trip->driver->user->name }}</strong>
                                </div>
                            </div>
                            <div class="d-flex mb-3">
                                <i class="bi bi-telephone text-muted me-3 fs-4"></i>
                                <div>
                                    <small class="text-muted d-block">Contact</small>
                                    <span class="text-muted fst-italic"><i class="bi bi-lock me-1"></i>Available after booking</span>
                                </div>
                            </div>
                            <div class="d-flex mb-3">
                                <i class="bi bi-car-front text-success me-3 fs-4"></i>
                                <div>
                                    <small class="text-muted d-block">Vehicle</small>
                                    <strong>{{ $trip->driver->vehicle_type }} ({{ $trip->driver->fuel_type === 'Electric' ? '⚡ Electric' : '🛢️ Fuel' }})</strong>
                                </div>
                            </div>
                            <div class="d-flex">
                                <i class="bi bi-credit-card-2-front text-success me-3 fs-4"></i>
                                <div>
                                    <small class="text-muted d-block">Plate Number</small>
                                    <strong>{{ $trip->driver->taxi_plate_number }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Booking Card -->
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 80px;">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-ticket-perforated me-2"></i>Book This Trip</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span>Available Seats</span>
                        <span class="badge bg-success fs-6">{{ $trip->available_seats }} / {{ $trip->total_seats }}</span>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Price per Seat</span>
                            <span class="price-tag">Nu. {{ number_format($trip->price_per_seat) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Full Taxi Price</span>
                            <span class="fw-bold text-primary">Nu. {{ number_format($trip->full_taxi_price) }}</span>
                        </div>
                    </div>
                    
                    <hr>
                    
                    @if($trip->available_seats > 0 && $trip->departure_datetime > now())
                        @auth
                            <a href="{{ route('booking.create', $trip->id) }}" class="btn btn-primary w-100 mb-2">
                                <i class="bi bi-ticket-perforated me-2"></i>Book Seats
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary w-100 mb-2">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Login to Book
                            </a>
                            <p class="text-center text-muted small mb-0">
                                Don't have an account? <a href="{{ route('register') }}">Register</a>
                            </p>
                        @endauth
                    @elseif($trip->available_seats == 0)
                        <button class="btn btn-secondary w-100" disabled>
                            <i class="bi bi-x-circle me-2"></i>Fully Booked
                        </button>
                    @else
                        <button class="btn btn-secondary w-100" disabled>
                            <i class="bi bi-clock me-2"></i>Trip has passed
                        </button>
                    @endif
                </div>
                <div class="card-footer bg-light">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        10-second first-pay-first-get allocation applies
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
