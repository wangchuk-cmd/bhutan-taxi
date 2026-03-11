@extends('layouts.admin')

@section('title', 'Book on Behalf - Search')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.bookings') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-2"></i>Back to Bookings
    </a>
</div>

<h4 class="mb-4"><i class="bi bi-ticket-perforated me-2"></i>Book on Behalf of Passenger</h4>

<!-- Search Form -->
<div class="card mb-4">
    <div class="card-header">
        <h6 class="mb-0"><i class="bi bi-search me-2"></i>Search Available Trips</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.bookings.search') }}" method="GET" class="row g-3 align-items-end" id="admin-booking-search-form">
            <div class="col-md-3">
                <label class="form-label fw-bold">From</label>
                <input type="text" name="from" id="admin-search-from" class="form-control" 
                       placeholder="Type origin..."
                       data-dzongkhag-autocomplete
                       data-exclude-input="#admin-search-to"
                       data-next-input="#admin-search-to"
                       value="{{ $validated['from'] ?? '' }}"
                       required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">To</label>
                <input type="text" name="to" id="admin-search-to" class="form-control" 
                       placeholder="Type destination..."
                       data-dzongkhag-autocomplete
                       data-exclude-input="#admin-search-from"
                       data-next-input="#admin-booking-search-date"
                       value="{{ $validated['to'] ?? '' }}"
                       required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Travel Date</label>
                  <input type="date" name="date" id="admin-booking-search-date" class="form-control" 
                      value="{{ $validated['date'] ?? date('Y-m-d') }}" required>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-2"></i>Search Trips
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Route Info -->
@if($route)
    <div class="alert alert-info d-flex align-items-center mb-4">
        <i class="bi bi-info-circle me-3 fs-4"></i>
        <div>
            <strong>{{ $route->origin_dzongkhag }} → {{ $route->destination_dzongkhag }}</strong>
            <span class="mx-2">|</span>
            Distance: {{ $route->distance_km }} km
            <span class="mx-2">|</span>
            Estimated Time: {{ $route->estimated_time }}
        </div>
    </div>
@endif

<!-- Results -->
@if(!empty($validated))
    <h5 class="mb-3">
        <i class="bi bi-list-ul me-2"></i>Available Trips
        @if($trips->count() > 0)
            <span class="badge bg-success ms-2">{{ $trips->count() }} found</span>
        @endif
    </h5>

    @if($trips->count() > 0)
        <div class="row g-4">
            @foreach($trips as $trip)
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="card-title mb-1">
                                        {{ $trip->origin_dzongkhag }}
                                        <i class="bi bi-arrow-right text-primary mx-2"></i>
                                        {{ $trip->destination_dzongkhag }}
                                    </h5>
                                    <small class="text-muted">Trip #{{ $trip->id }}</small>
                                </div>
                                <span class="badge bg-success fs-6">
                                    {{ $trip->available_seats }} seats
                                </span>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-calendar3 text-muted me-2"></i>
                                        <span>{{ $trip->departure_datetime->format('M d, Y') }}</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-clock text-muted me-2"></i>
                                        <span>{{ $trip->departure_datetime->format('h:i A') }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-person text-muted me-2"></i>
                                        <span>{{ $trip->driver->user->name }}</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-car-front text-muted me-2"></i>
                                        <span>{{ $trip->driver->taxi_plate_number }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center border-top pt-3">
                                <div>
                                    <span class="fs-5 fw-bold text-primary">Nu. {{ number_format($trip->price_per_seat) }}</span>
                                    <span class="text-muted">/seat</span>
                                    <div class="small text-muted">Full taxi: Nu. {{ number_format($trip->full_taxi_price) }}</div>
                                </div>
                                <a href="{{ route('admin.bookings.create', $trip->id) }}" class="btn btn-primary">
                                    <i class="bi bi-ticket-perforated me-1"></i>Book Now
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-emoji-frown display-1 text-muted"></i>
                <h5 class="mt-3">No trips found</h5>
                <p class="text-muted">
                    No taxis available for {{ $validated['from'] }} → {{ $validated['to'] }} on {{ \Carbon\Carbon::parse($validated['date'])->format('F d, Y') }}.
                </p>
            </div>
        </div>
    @endif
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-search display-1 text-muted"></i>
            <h5 class="mt-3">Search for Trips</h5>
            <p class="text-muted">Use the search form above to find available trips for booking.</p>
        </div>
    </div>
@endif
@endsection
