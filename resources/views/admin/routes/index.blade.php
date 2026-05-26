@extends('layouts.admin')

@section('title', 'Manage Routes')

@section('content')
@include('components.confirm-modal')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-signpost-2 me-2"></i>Routes Management</h4>
    <div class="d-flex gap-2">
        <form action="{{ route('admin.routes.generateAll') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-outline-success">
                <i class="bi bi-diagram-3 me-2"></i>Generate All Route Estimates
            </button>
        </form>
        <a href="{{ route('admin.routes.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Add Route
        </a>
    </div>
</div>

@php
    $dzongkhags = config('dzongkhags.list');
    // Build a map of existing routes for quick lookup
    $routesCollection = collect($routes);
    $existingRoutes = $routesCollection->flatMap(function($r) {
        $forwardKey = strtolower(trim($r->origin_dzongkhag)).'|'.strtolower(trim($r->destination_dzongkhag));
        $reverseKey = strtolower(trim($r->destination_dzongkhag)).'|'.strtolower(trim($r->origin_dzongkhag));

        return [
            $forwardKey => $r,
            $reverseKey => $r,
        ];
    });
    $routeSuggestions = $routeOrigins ?? $routesCollection->pluck('origin_dzongkhag')->unique()->values();
@endphp

<div class="card">
    <div class="card-body">
        <div class="row g-2 align-items-center mb-3">
            <div class="col-md-6">
                <label for="route-search" class="form-label fw-bold mb-1">Quick Search</label>
                <input type="text" id="route-search" class="form-control" placeholder="Type origin dzongkhag..." list="route-search-options" autocomplete="off">
                <datalist id="route-search-options">
                    @foreach($routeSuggestions as $suggestion)
                        <option value="{{ $suggestion }}"></option>
                    @endforeach
                </datalist>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="small text-muted mt-4 mt-md-0">
                    Type the first letter of a dzongkhag to quickly filter routes and open edit.
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle table-sm" id="routes-table">
                <thead>
                    <tr>
                        <th>Origin</th>
                        <th>Destination</th>
                        <th>Distance</th>
                        <th>Est. Time</th>
                        <th>Trips</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($dzongkhags as $from)
                    @foreach($dzongkhags as $to)
                        @if($from !== $to)
                            @php
                                $key = strtolower(trim($from)).'|'.strtolower(trim($to));
                                $route = $existingRoutes[$key] ?? null;
                                $searchText = strtolower(trim($from));
                            @endphp
                            <tr class="route-row @if($route) table-success @else table-warning @endif" data-route-text="{{ $searchText }}">
                                <td><strong>{{ $from }}</strong></td>
                                <td>{{ $to }}</td>
                                <td>{{ $route ? $route->formatted_distance_km : '-' }}</td>
                                <td>{{ $route ? $route->formatted_estimated_time : '-' }}</td>
                                <td>{!! $route ? '<span class="badge bg-primary">'.$route->trips_count.'</span>' : '<span class="badge bg-secondary">0</span>' !!}</td>
                                <td class="d-flex gap-1">
                                    @if($route)
                                        <a href="{{ route('admin.routes.edit', $route->id) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                        <form id="deleteRouteForm-{{ $route->id }}" action="{{ route('admin.routes.delete', $route->id) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                title="{{ $route->trips_count > 0 ? 'Cannot delete: has trips' : 'Delete' }}"
                                                @if($route->trips_count > 0) disabled @else onclick="showConfirmModal('Delete this route?', 'Delete Route', function() { document.getElementById('deleteRouteForm-{{ $route->id }}').submit(); })" @endif>
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.routes.store') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="origin_dzongkhag" value="{{ $from }}">
                                            <input type="hidden" name="destination_dzongkhag" value="{{ $to }}">
                                            <input type="hidden" name="distance_km" value="1">
                                            <input type="hidden" name="estimated_time" value="01:00">
                                            <button class="btn btn-sm btn-outline-success" title="Add Route">Add</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="small text-muted mt-2">Green = available in system, Yellow = not yet added. You can add, edit, or delete routes directly here.</div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('route-search');
    const rows = Array.from(document.querySelectorAll('#routes-table tbody .route-row'));

    function filterRoutes() {
        const query = (searchInput.value || '').trim().toLowerCase();
        let visibleCount = 0;

        rows.forEach((row) => {
            const text = row.getAttribute('data-route-text') || '';
            const isVisible = !query || text.startsWith(query);
            row.style.display = isVisible ? '' : 'none';
            if (isVisible) visibleCount++;
        });

        const emptyStateId = 'routes-empty-state';
        let emptyState = document.getElementById(emptyStateId);

        if (!emptyState) {
            emptyState = document.createElement('div');
            emptyState.id = emptyStateId;
            emptyState.className = 'alert alert-info mt-3 d-none';
            emptyState.textContent = 'No routes match your search.';
            document.querySelector('.card-body').appendChild(emptyState);
        }

        emptyState.classList.toggle('d-none', visibleCount !== 0);
    }

    searchInput.addEventListener('input', filterRoutes);
    filterRoutes();
});
</script>
@endpush
@endsection
