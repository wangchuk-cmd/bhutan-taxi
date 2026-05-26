@extends('layouts.app')

@section('title', 'My Ratings & Reviews')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('driver.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">My Ratings & Reviews</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Rating Summary Card -->
        <div class="col-lg-3 mb-4">
            <div class="card text-center shadow-sm">
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
                    <small class="text-muted">Based on {{ $ratingCount }} {{ $ratingCount === 1 ? 'rating' : 'ratings' }}</small>
                </div>
            </div>

            <!-- Rating Breakdown -->
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Rating Breakdown</h6>
                </div>
                <div class="card-body">
                    @for($i = 5; $i >= 1; $i--)
                        <div class="d-flex align-items-center mb-2">
                            <small class="me-2" style="min-width: 30px;">{{ $i }} <i class="fas fa-star text-warning"></i></small>
                            <div class="progress flex-grow-1" style="height: 20px;">
                                @php
                                    $percentage = $ratingCount > 0 ? ($ratingBreakdown[$i] / $ratingCount) * 100 : 0;
                                @endphp
                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $percentage }}%" 
                                     aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                            <small class="ms-2 text-muted" style="min-width: 30px;">{{ $ratingBreakdown[$i] }}</small>
                        </div>
                    @endfor
                </div>
            </div>
        </div>

        <!-- Ratings List -->
        <div class="col-lg-9">
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
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1">
                                            <i class="fas fa-user-circle me-2"></i>Passenger
                                        </h6>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>{{ $rating->created_at->format('M d, Y') }}
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <div class="mb-1">
                                            @for($i = 0; $i < $rating->rating; $i++)
                                                <i class="fas fa-star text-warning"></i>
                                            @endfor
                                            @for($i = $rating->rating; $i < 5; $i++)
                                                <i class="far fa-star text-warning"></i>
                                            @endfor
                                        </div>
                                        <small class="text-muted">{{ $rating->rating }} out of 5</small>
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
                                    {{ $rating->booking->trip->departure_datetime->format('M d, Y') }}
                                </div>

                                <!-- Review (anonymous to driver) -->
                                @if($rating->review)
                                    <div class="alert alert-light border-left-info mb-0">
                                        <p class="mb-0">
                                            <em>"{{ $rating->review }}"</em>
                                        </p>
                                    </div>
                                @else
                                    <p class="text-muted small mb-0">
                                        <em>No comment provided</em>
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="text-center p-5">
                            <i class="fas fa-star fa-3x text-muted mb-3 d-block"></i>
                            <h6 class="text-muted">No ratings yet</h6>
                            <p class="text-muted small">You haven't received any ratings from passengers yet.</p>
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

            <!-- Tips Card -->
            <div class="card mt-4 border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Tips to Improve Your Rating</h6>
                </div>
                <div class="card-body">
                    <ul class="small mb-0">
                        <li>🚗 Maintain your vehicle in excellent condition</li>
                        <li>⏰ Arrive on time and keep to your schedule</li>
                        <li>😊 Be courteous and professional with passengers</li>
                        <li>🗣️ Communicate clearly about pickup and drop-off points</li>
                        <li>🧹 Keep your vehicle clean and comfortable</li>
                        <li>🛣️ Choose safe routes and drive carefully</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .border-left-info {
        border-left: 4px solid #17a2b8 !important;
    }
</style>
@endsection
