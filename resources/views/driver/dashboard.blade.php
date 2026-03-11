@extends('layouts.driver')

@section('title', 'Driver Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">
        <i class="bi bi-speedometer2 me-2"></i>Dashboard
    </h4>
    @if($driver->verified)
        <a href="{{ route('driver.trips.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Create New Trip
        </a>
    @endif
</div>

@if(!$driver->verified)
    <div class="alert alert-warning mb-4">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>Account Pending Verification</strong> - Your driver account is waiting for admin approval. You cannot create trips until verified.
    </div>
@endif

<!-- Stats -->
<div class="row g-3 mb-4 stats-row">
    <div class="col-6 col-md-3">
        <div class="stat-card primary">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 mb-1">Total Trips</h6>
                    <h2 class="mb-0">{{ $totalTrips }}</h2>
                </div>
                <i class="bi bi-map fs-1 text-white-50"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 mb-1">Completed</h6>
                    <h2 class="mb-0">{{ $completedTrips }}</h2>
                </div>
                <i class="bi bi-check-circle fs-1 text-white-50"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card secondary">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 mb-1">Total Earnings</h6>
                    <h2 class="mb-0">Nu. {{ number_format($totalEarnings) }}</h2>
                </div>
                <i class="bi bi-cash-stack fs-1 text-white-50"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card warning">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 mb-1">Pending Payout</h6>
                    <h2 class="mb-0">Nu. {{ number_format($pendingPayouts) }}</h2>
                </div>
                <i class="bi bi-hourglass-split fs-1 text-white-50"></i>
            </div>
        </div>
    </div>
</div>

<!-- Upcoming Trips -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Upcoming Trips</h5>
        <a href="{{ route('driver.trips') }}" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="card-body p-0">
        @if($upcomingTrips->count() > 0)
            {{-- Desktop Table --}}
            <div class="d-none d-md-block">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Route</th>
                                <th>Date & Time</th>
                                <th>Seats</th>
                                <th>Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($upcomingTrips as $trip)
                                <tr>
                                    <td>
                                        <strong>{{ $trip->origin_dzongkhag }}</strong>
                                        <i class="bi bi-arrow-right mx-1"></i>
                                        <strong>{{ $trip->destination_dzongkhag }}</strong>
                                    </td>
                                    <td>{{ $trip->departure_datetime->format('M d, Y h:i A') }}</td>
                                    <td>
                                        <span class="badge bg-success">{{ $trip->available_seats }}/{{ $trip->total_seats }}</span>
                                    </td>
                                    <td>Nu. {{ number_format($trip->price_per_seat) }}/seat</td>
                                    <td>
                                        <a href="{{ route('driver.passengers', $trip->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-people me-1"></i>Passengers
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Mobile Card List --}}
            <div class="d-md-none p-3">
                @foreach($upcomingTrips as $trip)
                    <div class="trip-mobile-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="route-line">
                                {{ $trip->origin_dzongkhag }}
                                <i class="bi bi-arrow-right text-primary mx-1"></i>
                                {{ $trip->destination_dzongkhag }}
                            </span>
                            <span class="badge bg-success">{{ $trip->available_seats }}/{{ $trip->total_seats }}</span>
                        </div>
                        <div class="meta-line">
                            <span><i class="bi bi-calendar3"></i>{{ $trip->departure_datetime->format('M d') }}</span>
                            <span><i class="bi bi-clock"></i>{{ $trip->departure_datetime->format('h:i A') }}</span>
                            <span><i class="bi bi-cash"></i>Nu. {{ number_format($trip->price_per_seat) }}/seat</span>
                        </div>
                        <div>
                            <a href="{{ route('driver.passengers', $trip->id) }}" class="btn btn-sm btn-outline-primary w-100">
                                <i class="bi bi-people me-1"></i>View Passengers
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-4">
                <i class="bi bi-calendar-x display-4 text-muted"></i>
                <p class="mt-2 text-muted">No upcoming trips</p>
                @if($driver->verified)
                    <a href="{{ route('driver.trips.create') }}" class="btn btn-primary">Create Your First Trip</a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
