@extends('layouts.app')

@section('title', 'Search Results')

@section('content')
<div class="container py-4">
    <!-- Search Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('search') }}" method="GET" class="row g-3 align-items-end" id="passenger-results-search-form">
                <div class="col-md-3">
                    <label class="form-label">From</label>
                    <input type="text" name="from" id="results-from" class="form-control" 
                           placeholder="Type origin..."
                           data-dzongkhag-autocomplete
                           data-exclude-input="#results-to"
                           data-next-input="#results-to"
                           value="{{ $validated['from'] ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">To</label>
                    <input type="text" name="to" id="results-to" class="form-control" 
                           placeholder="Type destination..."
                           data-dzongkhag-autocomplete
                           data-exclude-input="#results-from"
                           data-next-input="#passenger-results-search-date"
                           value="{{ $validated['to'] ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date</label>
                          <input type="date" name="date" id="passenger-results-search-date" class="form-control" 
                              value="{{ $validated['date'] ?? date('Y-m-d') }}" required>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-2"></i>Search
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

    <!-- Results Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
            <i class="bi bi-list-ul me-2"></i>
            Available Trips
            @if($trips->count() > 0)
                <span class="badge bg-primary ms-2">{{ $trips->count() }} found</span>
            @endif
        </h4>
        <small class="text-muted">
            <i class="bi bi-arrow-repeat me-1"></i>Auto-updating
        </small>
    </div>

    <!-- Trips List (Real-time updated) -->
    <div id="trips-list">
        @include('partials.trips-list', ['trips' => $trips])
    </div>

    @if($trips->count() == 0)
        <div class="mt-3">
            <a href="{{ route('home') }}" class="btn btn-primary">
                <i class="bi bi-arrow-left me-2"></i>Back to Home
            </a>
        </div>
    @endif
</div>
@endsection
