@extends('layouts.driver')

@section('title', 'My Trips')

@section('content')
@include('components.confirm-modal')

<style>
    :root {
        --primary-color: #2563eb;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --text-dark: #111827;
        --text-muted: #374151;
        --bg-light: #f3f4f6;
        --card-shadow: 0 1px 3px rgba(0, 0, 0, 0.08), 0 1px 2px rgba(0, 0, 0, 0.04);
        --card-shadow-lg: 0 4px 6px rgba(0, 0, 0, 0.07), 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
        gap: 20px;
    }

    .page-header h1 {
        font-size: 28px;
        font-weight: 600;
        color: var(--text-dark);
        margin: 0;
    }

    .create-btn {
        padding: 12px 24px;
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .create-btn:hover {
        background: #1d4ed8;
        box-shadow: var(--card-shadow-lg);
        transform: translateY(-2px);
    }

    .trips-container {
        background: white;
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        border: 1px solid #f0f0f0;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .trips-container:hover {
        box-shadow: var(--card-shadow-lg);
    }

    .trips-table {
        width: 100%;
        border-collapse: collapse;
    }

    .trips-table thead {
        background: var(--bg-light);
        border-bottom: 2px solid #e5e7eb;
    }

    .trips-table th {
        padding: 16px;
        text-align: left;
        font-size: 12px;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .trips-table tbody tr {
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.2s;
    }

    .trips-table tbody tr:hover {
        background: #fafafa;
    }

    .trips-table td {
        padding: 16px;
        color: var(--text-dark);
        font-size: 14px;
        font-weight: 500;
    }

    .route-info {
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-badge.active { background: #d1fae5; color: var(--success-color); }
    .status-badge.completed { background: #dbeafe; color: var(--primary-color); }
    .status-badge.cancelled { background: #fee2e2; color: var(--danger-color); }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .action-buttons button,
    .action-buttons a {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        border: 1px solid;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state-icon {
        font-size: 48px;
        color: var(--text-muted);
        display: block;
        margin-bottom: 16px;
    }

    .empty-state-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 8px;
    }

    .empty-state-text {
        color: var(--text-muted);
        margin-bottom: 24px;
    }
</style>

<div class="page-header">
    <h1>My Trips</h1>
    <a href="{{ route('driver.trips.create') }}" class="create-btn">
        <i class="bi bi-plus-circle"></i>Create Trip
    </a>
</div>

<div class="trips-container">
    @if($trips->count() > 0)
        <div style="overflow-x: auto;">
            <table class="trips-table">
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
                                <div class="route-info">
                                    <span style="color: var(--primary-color);">{{ $trip->origin_dzongkhag }}</span>
                                    <i class="bi bi-arrow-right" style="font-size: 12px;"></i>
                                    <span>{{ $trip->destination_dzongkhag }}</span>
                                </div>
                            </td>
                            <td>{{ $trip->departure_datetime->format('M d, Y h:i A') }}</td>
                            <td>
                                <span style="background: #dbeafe; color: var(--primary-color); padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                    {{ $trip->available_seats }}/{{ $trip->total_seats }}
                                </span>
                            </td>
                            <td style="font-weight: 600; color: var(--primary-color);">Nu. {{ number_format($trip->price_per_seat) }}</td>
                            <td>
                                <span class="status-badge {{ strtolower($trip->status) }}">
                                    {{ $trip->status }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div class="action-buttons">
                                    <a href="{{ route('driver.passengers', $trip->id) }}" style="border-color: var(--primary-color); color: var(--primary-color); background: white;">
                                        <i class="bi bi-people"></i>
                                    </a>
                                    @if($trip->status === 'active' && $trip->departure_datetime > now())
                                        <a href="{{ route('driver.trips.edit', $trip->id) }}" style="border-color: #6b7280; color: var(--text-muted); background: white;">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form id="driverCancelForm-{{ $trip->id }}" action="{{ route('driver.trips.cancel', $trip->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="button" style="border-color: var(--danger-color); color: var(--danger-color); background: white;" onclick="showConfirmModal('Cancel this trip? All passengers will be refunded.', 'Cancel Trip', function() { document.getElementById('driverCancelForm-{{ $trip->id }}').submit(); })">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
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
