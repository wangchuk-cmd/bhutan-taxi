@extends('layouts.app')

@section('title', 'All Available Trips')

@section('content')
<div class="container py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-1"><i class="bi bi-list-ul me-2"></i>All Available Trips</h2>
            <p class="text-muted">Browse all available taxis from today onwards</p>
        </div>
    </div>

    <!-- Search & Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('trips.all') }}" method="GET" class="row g-3 align-items-end" id="all-trips-search-form">
                <div class="col-md-3">
                    <label class="form-label">From (Optional)</label>
                    <input type="text" name="from" id="all-from" class="form-control" 
                           placeholder="Type origin..."
                           data-dzongkhag-autocomplete
                           data-exclude-input="#all-to"
                           data-next-input="#all-to"
                           value="{{ $from ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">To (Optional)</label>
                    <input type="text" name="to" id="all-to" class="form-control" 
                           placeholder="Type destination..."
                           data-dzongkhag-autocomplete
                           data-exclude-input="#all-from"
                           data-next-input="#filter-date"
                           value="{{ $to ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date (Optional)</label>
                    <input type="date" name="filter_date" id="filter-date" class="form-control" 
                           value="{{ $filterDate ?? '' }}">
                    <small class="text-muted">Leave empty to show all dates</small>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-2"></i>Search
                    </button>
                </div>
            </form>
            @if(!empty($filterDate))
                <div class="mt-2">
                    <a href="{{ route('trips.all') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-circle me-1"></i>Clear All Filters
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Results Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
            <i class="bi bi-car-front me-2"></i>
            Available Trips
            @if($trips->count() > 0)
                <span class="badge bg-primary ms-2">{{ $trips->count() }} trips available</span>
            @endif
        </h4>
        <small class="text-muted">
            @if(!empty($filterDate))
                <i class="bi bi-calendar3-event me-1"></i>
                Showing: {{ \Carbon\Carbon::parse($filterDate)->format('M d, Y') }}
            @else
                <i class="bi bi-calendar3-event me-1"></i>
                From {{ $startDate }} onwards
            @endif
        </small>
    </div>

    <!-- Trips List -->
    <div id="trips-list">
        @if($trips->count() > 0)
            <div class="row g-3 g-lg-4">
                @foreach($trips as $trip)
                    <div class="col-6 col-sm-6 col-md-4 col-lg-3">
                        <div class="card trip-card h-100">
                            <div class="card-body p-3">

                                {{-- Route Header with Available Seats --}}
                                <div class="mb-3">
                                    <h5 class="route-title mb-1">
                                        {{ $trip->origin_dzongkhag }}
                                        <i class="bi bi-arrow-right text-primary"></i>
                                        {{ $trip->destination_dzongkhag }}
                                    </h5>
                                    <span class="badge bg-success">
                                        <i class="bi bi-person-fill"></i> {{ $trip->available_seats }} Available
                                    </span>
                                </div>

                                {{-- Driver Info with Rating --}}
                                <div class="mb-3 pb-3 border-bottom">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="bi bi-person-circle text-primary" style="font-size: 1.2rem;"></i>
                                        <strong>{{ $trip->driver->user->name }}</strong>
                                    </div>
                                    @if($trip->driver->average_rating > 0)
                                        <div class="d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                                            @for($i = 0; $i < 5; $i++)
                                                @if($i < floor($trip->driver->average_rating))
                                                    <i class="bi bi-star-fill text-warning"></i>
                                                @elseif($i - floor($trip->driver->average_rating) < 0.5)
                                                    <i class="bi bi-star-half text-warning"></i>
                                                @else
                                                    <i class="bi bi-star text-warning"></i>
                                                @endif
                                            @endfor
                                            <span class="text-warning fw-bold">({{ number_format($trip->driver->average_rating, 1) }})</span>
                                            <span class="text-muted">{{ $trip->driver->rating_count }} ratings</span>
                                        </div>
                                    @else
                                        <div style="font-size: 0.85rem;" class="text-muted">
                                            <i class="bi bi-star text-warning"></i> No ratings yet
                                        </div>
                                    @endif
                                </div>

                                {{-- Trip Details Grid --}}
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <div class="trip-info-item small">
                                            <i class="bi bi-calendar3 text-primary"></i>
                                            <span class="d-block text-muted">{{ $trip->departure_datetime->format('M d') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="trip-info-item small">
                                            <i class="bi bi-clock text-primary"></i>
                                            <span class="d-block text-muted">{{ $trip->departure_datetime->format('h:i A') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="trip-info-item small">
                                            <i class="bi bi-car-front text-primary"></i>
                                            <span class="d-block text-muted">{{ $trip->driver->vehicle_type }}</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="trip-info-item small">
                                            @if($trip->driver->fuel_type === 'Electric')
                                                <i class="bi bi-lightning-charge text-info"></i>
                                                <span class="d-block text-muted">Electric</span>
                                            @else
                                                <i class="bi bi-fuel-pump text-warning"></i>
                                                <span class="d-block text-muted">Fuel</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Price & Actions --}}
                                <div class="d-flex justify-content-between align-items-end pt-2 border-top">
                                    <div>
                                        <div class="price-tag fw-bold">Nu. {{ number_format($trip->price_per_seat) }}</div>
                                        <small class="text-muted">/seat</small>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('trip.details', $trip->id) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @auth
                                            <a href="{{ route('booking.create', $trip->id) }}" class="btn btn-sm btn-primary">
                                                <i class="bi bi-ticket-perforated me-1"></i>Book
                                            </a>
                                        @else
                                            <a href="{{ route('login') }}?redirect={{ route('booking.create', $trip->id) }}" class="btn btn-sm btn-primary">
                                                <i class="bi bi-box-arrow-in-right me-1"></i>Book
                                            </a>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="alert alert-info text-center py-5">
                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                <h5>No trips available</h5>
                <p class="text-muted mb-0">
                    @if(!empty($from) || !empty($to) || !empty($filterDate))
                        No taxis match your search criteria. Try different filters or <a href="{{ route('trips.all') }}" class="alert-link">view all trips</a>.
                    @else
                        No taxis are currently available. Try checking back later.
                    @endif
                </p>
                <div class="mt-3">
                    <a href="{{ route('home') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-arrow-left me-2"></i>Back to Home
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Back to Home Link -->
    <div class="mt-4 text-center">
        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Home
        </a>
    </div>
</div>
@endsection
