@extends('layouts.admin')

@section('title', 'Driver Ratings - ' . $driver->user->name)

@section('content')
<div class="container-fluid py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.ratings') }}">Ratings & Reviews</a></li>
            <li class="breadcrumb-item active">{{ $driver->user->name }}</li>
        </ol>
    </nav>

    <!-- Driver Info & Rating Summary -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="avatar-lg bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="fas fa-user fa-2x"></i>
                            </div>
                        </div>
                        <div class="col">
                            <h4 class="mb-1">{{ $driver->user->name }}</h4>
                            <p class="text-muted mb-2">
                                <i class="fas fa-phone me-1"></i>{{ $driver->user->phone_number }}
                                <span class="ms-3"><i class="fas fa-envelope me-1"></i>{{ $driver->user->email }}</span>
                            </p>
                            <p class="text-muted mb-0">
                                <i class="fas fa-car me-1"></i>{{ $driver->vehicle_type }} • 
                                <span>{{ $driver->taxi_plate_number }}</span>
                                @if($driver->fuel_type === 'Electric')
                                    <span class="ms-2"><i class="fas fa-bolt text-warning"></i> Electric</span>
                                @else
                                    <span class="ms-2"><i class="fas fa-gas-pump"></i> Fuel</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-auto text-end">
                            <div class="mb-2">
                                @if($driver->verified)
                                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Verified</span>
                                @else
                                    <span class="badge bg-warning"><i class="fas fa-exclamation-circle me-1"></i>Pending</span>
                                @endif
                                @if($driver->active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rating Summary -->
        <div class="col-lg-4">
            <div class="card text-center shadow">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Average Rating</h6>
                    <div class="display-4 text-warning mb-2">
                        {{ number_format($averageRating, 1) }}
                    </div>
                    <div class="mb-3">
                        @for($i = 0; $i < 5; $i++)
                            @if($i < floor($averageRating))
                                <i class="fas fa-star text-warning"></i>
                            @elseif($i < $averageRating)
                                <i class="fas fa-star-half-alt text-warning"></i>
                            @else
                                <i class="far fa-star text-warning"></i>
                            @endif
                        @endfor
                    </div>
                    <small class="text-muted">{{ $ratingCount }} {{ $ratingCount === 1 ? 'rating' : 'ratings' }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Rating Breakdown -->
    <div class="row mb-4">
        @for($i = 5; $i >= 1; $i--)
            <div class="col-md-2">
                <div class="card text-center">
                    <div class="card-body py-3">
                        <h5 class="text-warning mb-2">
                            @for($j = 0; $j < $i; $j++)
                                <i class="fas fa-star"></i>
                            @endfor
                        </h5>
                        <h3 class="mb-1">{{ $ratingBreakdown[$i] }}</h3>
                        <small class="text-muted">
                            @php
                                $percentage = $ratingCount > 0 ? ($ratingBreakdown[$i] / $ratingCount) * 100 : 0;
                            @endphp
                            ({{ number_format($percentage, 1) }}%)
                        </small>
                    </div>
                </div>
            </div>
        @endfor
    </div>

    <!-- All Ratings -->
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-comments me-2"></i>Customer Reviews</h5>
                <span class="badge bg-light text-primary">{{ $ratingCount }}</span>
            </div>
        </div>

        <div class="card-body p-0">
            @if(count($ratings) > 0)
                @foreach($ratings as $rating)
                    <div class="border-bottom p-4">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1">
                                            <i class="fas fa-user-circle me-2"></i>{{ $rating->passenger->name }}
                                        </h6>
                                        <small class="text-muted">
                                            <i class="fas fa-phone me-1"></i>{{ $rating->passenger->phone_number }}
                                        </small>
                                    </div>
                                </div>

                                <!-- Trip Details -->
                                <div class="bg-light p-2 rounded mb-3 small">
                                    <i class="fas fa-map-marker-alt text-info me-1"></i>
                                    <strong>Route:</strong> 
                                    {{ $rating->booking->trip->origin_dzongkhag }} → 
                                    {{ $rating->booking->trip->destination_dzongkhag }}
                                    <br>
                                    <i class="fas fa-calendar-alt text-info me-1"></i>
                                    <strong>Date:</strong> 
                                    {{ $rating->booking->trip->departure_datetime->format('M d, Y H:i A') }}
                                </div>

                                <!-- Review -->
                                @if($rating->review)
                                    <blockquote class="border-left ps-3 mb-0">
                                        <p class="mb-0 text-muted">
                                            <em>"{{ $rating->review }}"</em>
                                        </p>
                                    </blockquote>
                                @else
                                    <p class="text-muted small mb-0">
                                        <em>No comment provided</em>
                                    </p>
                                @endif
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="mb-3">
                                    @for($i = 0; $i < $rating->rating; $i++)
                                        <i class="fas fa-star text-warning"></i>
                                    @endfor
                                    @for($i = $rating->rating; $i < 5; $i++)
                                        <i class="far fa-star text-warning"></i>
                                    @endfor
                                </div>
                                <h5 class="text-warning mb-2">{{ $rating->rating }}/5</h5>
                                <small class="text-muted d-block">
                                    {{ $rating->created_at->format('M d, Y H:i A') }}
                                </small>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center p-5">
                    <i class="fas fa-star fa-3x text-muted mb-3 d-block"></i>
                    <h6 class="text-muted">No ratings yet</h6>
                    <p class="text-muted small">This driver hasn't received any ratings from passengers yet.</p>
                </div>
            @endif

            <!-- Pagination -->
            @if(is_object($ratings) && method_exists($ratings, 'links'))
                <div class="d-flex justify-content-center p-4">
                    {{ $ratings->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">5⭐ Excellent</h6>
                    <h3 class="text-success mb-0">{{ $ratingBreakdown[5] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">4⭐ Good</h6>
                    <h3 class="text-info mb-0">{{ $ratingBreakdown[4] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">3⭐ Average</h6>
                    <h3 class="text-warning mb-0">{{ $ratingBreakdown[3] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">1-2⭐ Poor</h6>
                    <h3 class="text-danger mb-0">{{ $ratingBreakdown[2] + $ratingBreakdown[1] }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .avatar-lg {
        width: 80px;
        height: 80px;
    }
    blockquote {
        border-left: 4px solid #dee2e6;
        margin: 0;
        padding-left: 1rem;
    }
</style>
@endsection
