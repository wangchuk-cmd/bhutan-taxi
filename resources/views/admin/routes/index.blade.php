@extends('layouts.admin')

@section('title', 'Manage Routes')

@section('content')
@include('components.confirm-modal')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-signpost-2 me-2"></i>Routes Management</h4>
    <a href="{{ route('admin.routes.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Add Route
    </a>
</div>

@php
    $dzongkhags = config('dzongkhags.list');
    // Build a map of existing routes for quick lookup
    $existingRoutes = collect($routes->items())->mapWithKeys(function($r) {
        return [strtolower(trim($r->origin_dzongkhag)).'|'.strtolower(trim($r->destination_dzongkhag)) => $r];
    });
@endphp

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle table-sm">
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
                            @endphp
                            <tr @if($route) class="table-success" @else class="table-warning" @endif>
                                <td><strong>{{ $from }}</strong></td>
                                <td>{{ $to }}</td>
                                <td>{{ $route ? ($route->distance_km . ' km') : '-' }}</td>
                                <td>{{ $route ? $route->estimated_time : '-' }}</td>
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
@endsection
