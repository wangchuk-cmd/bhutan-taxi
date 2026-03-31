@extends('layouts.driver')

@section('title', 'My Trips')

@section('content')
@include('components.confirm-modal')

<style>
    :root {
        --primary-color: #0d6efd;
        --success-color: #198754;
        --warning-color: #ffc107;
        --danger-color: #dc3545;
        --text-dark: #212529;
        --text-muted: #6c757d;
        --bg-light: #f8f9fa;
        --card-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .page-header h1 {
        font-size: 24px;
        font-weight: 600;
        margin: 0;
    }

    .trips-container {
        background: #fff;
        border-radius: 8px;
        box-shadow: var(--card-shadow);
        border: 1px solid rgba(0,0,0,.125);
    }

    .trips-table {
        margin-bottom: 0;
    }

    .trips-table th {
        background: var(--bg-light);
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        padding: 1rem;
        border-bottom: 2px solid #dee2e6;
    }

    .trips-table td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #dee2e6;
    }

    .status-badge {
        padding: 0.35em 0.65em;
        font-size: 0.75em;
        font-weight: 700;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.25rem;
        text-transform: uppercase;
    }

    .status-badge.active { background-color: #d1e7dd; color: #0f5132; }
    .status-badge.completed { background-color: #cfe2ff; color: #084298; }
    .status-badge.cancelled { background-color: #f8d7da; color: #842029; }

    .empty-state {
        padding: 4rem 2rem;
        text-align: center;
    }
</style>

<div class="page-header">
    <h1><i class="bi bi-map me-2"></i>My Trips</h1>
    <a href="{{ route('driver.trips.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Create Trip
    </a>
</div>

<div class="trips-container">
    @if($trips->count() > 0)
        <div class="table-responsive">
            <table class="table trips-table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Route</th>
                        <th>Date & Time</th>
                        <th>Seats</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trips as $trip)
                        <tr>
                            <td>
                                <strong>{{ $trip->origin_dzongkhag }}</strong>
                                <i class="bi bi-arrow-right text-muted mx-1 text-sm"></i>
                                <strong>{{ $trip->destination_dzongkhag }}</strong>
                            </td>
                            <td>{{ $trip->departure_datetime->format('M d, Y h:i A') }}</td>
                            <td>
                                <span class="badge bg-info text-dark">
                                    {{ $trip->available_seats }}/{{ $trip->total_seats }}
                                </span>
                            </td>
                            <td><strong>Nu. {{ number_format($trip->price_per_seat) }}</strong></td>
                            <td>
                                <span class="status-badge {{ strtolower($trip->status) }}">
                                    {{ $trip->status }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('driver.passengers', $trip->id) }}" class="btn btn-sm btn-outline-primary" title="View Passengers">
                                        <i class="bi bi-people"></i>
                                    </a>
                                    @if($trip->status === 'active' && $trip->departure_datetime > now())
                                        <a href="{{ route('driver.trips.edit', $trip->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit Trip">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form id="driverCancelForm-{{ $trip->id }}" action="{{ route('driver.trips.cancel', $trip->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Cancel Trip" onclick="showConfirmModal('Cancel this trip? All passengers will be refunded.', 'Cancel Trip', function() { document.getElementById('driverCancelForm-{{ $trip->id }}').submit(); })">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding: 20px; border-top: 1px solid #f0f0f0;">
            {{ $trips->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="bi bi-map empty-state-icon"></i>
            <p class="empty-state-title">No trips created yet</p>
            <p class="empty-state-text">Start creating trips to see them here</p>
            <a href="{{ route('driver.trips.create') }}" class="create-btn">
                <i class="bi bi-plus-circle"></i>Create Your First Trip
            </a>
        </div>
    @endif
</div>
@endsection
