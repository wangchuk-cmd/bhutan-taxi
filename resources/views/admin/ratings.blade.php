@extends('layouts.admin')

@section('title', 'Driver Ratings & Reviews Management')

@section('content')
<div class="container-fluid py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Ratings & Reviews</li>
        </ol>
    </nav>

    <!-- Summary Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Total Ratings</h6>
                    <h2 class="text-primary mb-0">{{ $ratings->total() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Drivers Rated</h6>
                    <h2 class="text-success mb-0">{{ $drivers->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Average Rating</h6>
                    <h2 class="text-warning mb-0">
                        @php
                            $avgRating = $drivers->avg('ratings_avg_rating');
                        @endphp
                        {{ number_format($avgRating, 1) }} ⭐
                    </h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-2">Low Ratings (&lt;3)</h6>
                    <h2 class="text-danger mb-0">
                        @php
                            $lowRatings = \App\Models\Rating::where('rating', '<', 3)->count();
                        @endphp
                        {{ $lowRatings }}
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Rated Drivers -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">🌟 Top Rated Drivers</h6>
                </div>
                <div class="card-body p-0">
                    @php
                        $topDrivers = $drivers->sortByDesc('ratings_avg_rating')->take(5);
                    @endphp
                    @if(count($topDrivers) > 0)
                        @foreach($topDrivers as $driver)
                            <div class="border-bottom p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $driver->user->name }}</h6>
                                        <small class="text-muted">{{ $driver->vehicle_type }} • {{ $driver->taxi_plate_number }}</small>
                                    </div>
                                    <div class="text-end">
                                        <div class="mb-1">
                                            @for($i = 0; $i < 5; $i++)
                                                @if($i < floor($driver->ratings_avg_rating ?? 0))
                                                    <i class="fas fa-star text-warning"></i>
                                                @else
                                                    <i class="far fa-star text-warning"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        <small class="text-muted">{{ number_format($driver->ratings_avg_rating, 1) }} • {{ $driver->ratings_count }} ratings</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center p-4 text-muted">
                            No rated drivers yet
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Drivers Needing Attention -->
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0">⚠️ Low Rated Drivers (Below 3 Stars)</h6>
                </div>
                <div class="card-body p-0">
                    @php
                        $lowRatedDrivers = $drivers->filter(fn($d) => ($d->ratings_avg_rating ?? 5) < 3)->take(5);
                    @endphp
                    @if(count($lowRatedDrivers) > 0)
                        @foreach($lowRatedDrivers as $driver)
                            <div class="border-bottom p-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $driver->user->name }}</h6>
                                        <small class="text-muted">{{ $driver->vehicle_type }} • {{ $driver->taxi_plate_number }}</small>
                                    </div>
                                    <div class="text-end">
                                        <div class="mb-1">
                                            @for($i = 0; $i < 5; $i++)
                                                @if($i < floor($driver->ratings_avg_rating ?? 0))
                                                    <i class="fas fa-star text-danger"></i>
                                                @else
                                                    <i class="far fa-star text-danger"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        <small class="text-danger"><strong>{{ number_format($driver->ratings_avg_rating, 1) }}</strong> • {{ $driver->ratings_count }} ratings</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center p-4 text-muted">
                            All drivers have good ratings ✓
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- All Ratings Table -->
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>All Ratings</h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Driver</th>
                        <th>Passenger</th>
                        <th>Rating</th>
                        <th>Route</th>
                        <th>Date</th>
                        <th>Review</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($ratings) > 0)
                        @foreach($ratings as $rating)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.driver.ratings', $rating->driver_id) }}" class="text-decoration-none">
                                        <strong>{{ $rating->driver->user->name }}</strong>
                                    </a>
                                    <br>
                                    <small class="text-muted">{{ $rating->driver->vehicle_type }}</small>
                                </td>
                                <td>
                                    {{ $rating->passenger->name }}
                                    <br>
                                    <small class="text-muted">{{ $rating->passenger->phone_number }}</small>
                                </td>
                                <td>
                                    <div>
                                        @for($i = 0; $i < $rating->rating; $i++)
                                            <i class="fas fa-star text-warning"></i>
                                        @endfor
                                        @for($i = $rating->rating; $i < 5; $i++)
                                            <i class="far fa-star text-warning"></i>
                                        @endfor
                                    </div>
                                    <small class="text-muted">{{ $rating->rating }}/5</small>
                                </td>
                                <td>
                                    <small>
                                        {{ $rating->booking->trip->origin_dzongkhag }} → 
                                        {{ $rating->booking->trip->destination_dzongkhag }}
                                    </small>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $rating->created_at->format('M d, Y') }}</small>
                                </td>
                                <td>
                                    @if($rating->review)
                                        <button class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#reviewModal{{ $rating->id }}" title="View Review">
                                            <i class="fas fa-comment-dots me-1"></i> View
                                        </button>
                                    @else
                                        <span class="badge bg-secondary">No Review</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.driver.ratings', $rating->driver_id) }}" class="btn btn-sm btn-primary" title="View Driver Ratings">
                                        <i class="fas fa-eye me-1"></i> Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center p-4 text-muted">
                                No ratings found. Database may not be migrated yet.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-light d-flex justify-content-center">
            @if(is_object($ratings) && method_exists($ratings, 'links'))
                {{ $ratings->links() }}
            @endif
        </div>
    </div>
</div>

<!-- Review Modals -->
@foreach($ratings as $rating)
    @if($rating->review)
        <div class="modal fade" id="reviewModal{{ $rating->id }}" tabindex="-1" aria-labelledby="reviewModalLabel{{ $rating->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" id="reviewModalLabel{{ $rating->id }}">
                            <i class="fas fa-comment-dots me-2"></i>Review Details
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <strong>Driver:</strong> {{ $rating->driver->user->name }}
                        </div>
                        <div class="mb-3">
                            <strong>Passenger:</strong> {{ $rating->passenger->name }}
                        </div>
                        <div class="mb-3">
                            <strong>Rating:</strong>
                            @for($i = 0; $i < $rating->rating; $i++)
                                <i class="fas fa-star text-warning"></i>
                            @endfor
                            @for($i = $rating->rating; $i < 5; $i++)
                                <i class="far fa-star text-warning"></i>
                            @endfor
                            ({{ $rating->rating }}/5)
                        </div>
                        <div class="mb-3">
                            <strong>Route:</strong>
                            {{ $rating->booking->trip->origin_dzongkhag }} → {{ $rating->booking->trip->destination_dzongkhag }}
                        </div>
                        <div class="mb-3">
                            <strong>Date:</strong> {{ $rating->created_at->format('M d, Y g:i A') }}
                        </div>
                        <hr>
                        <div>
                            <strong>Review:</strong>
                            <p class="mt-2 p-3 bg-light rounded">{{ $rating->review }}</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
</script>
@endsection
