@extends('layouts.driver')

@section('title', 'Trip Passengers')

@section('content')

<style>
    :root {
        --primary-color: #0d6efd;
        --success-color: #10b981;
        --text-dark: #1f2937;
        --text-muted: #6b7280;
        --bg-light: #f3f4f6;
        --card-shadow: 0 1px 3px rgba(0, 0, 0, 0.08), 0 1px 2px rgba(0, 0, 0, 0.04);
    }

    body {
        background: #ffffff;
    }

    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--text-muted);
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        margin-bottom: 28px;
        padding: 8px 12px;
        border-radius: 6px;
        transition: all 0.2s;
        border: 1px solid transparent;
    }

    .back-button:hover {
        background: var(--bg-light);
        color: var(--text-dark);
        border: 1px solid #e5e7eb;
    }

    .trip-header {
        background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 50%, #0d6efd 100%);
        border-radius: 14px;
        padding: 40px;
        color: white;
        margin-bottom: 40px;
        box-shadow: 0 8px 16px rgba(37, 99, 235, 0.15), 0 2px 4px rgba(0, 0, 0, 0.05);
        position: relative;
        overflow: hidden;
    }

    .trip-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: 
            radial-gradient(circle at 20% 50%, rgba(255,255,255,0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 80%, rgba(255,255,255,0.05) 0%, transparent 50%);
        pointer-events: none;
    }

    .trip-header h1 {
        font-size: 28px;
        font-weight: 700;
        margin: 0 0 28px 0;
        display: flex;
        align-items: center;
        gap: 14px;
        position: relative;
        z-index: 1;
        letter-spacing: -0.3px;
    }

    .trip-header i {
        font-size: 32px;
        opacity: 1;
    }

    .trip-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
        position: relative;
        z-index: 1;
    }

    .trip-info-item {
        background: rgba(255, 255, 255, 0.12);
        padding: 18px;
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(10px);
    }

    .trip-info-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        opacity: 0.9;
        margin-bottom: 8px;
        color: rgba(255, 255, 255, 0.85);
    }

    .trip-info-value {
        font-size: 16px;
        font-weight: 700;
        letter-spacing: 0.3px;
        color: #ffffff;
    }

    .passengers-container {
        background: #ffffff;
        border-radius: 14px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        border: 1px solid #f0f0f0;
        overflow: hidden;
    }

    .passengers-header {
        padding: 28px 32px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
        gap: 12px;
        background: #ffffff;
    }

    .passengers-header h2 {
        font-size: 18px;
        font-weight: 700;
        color: #111827;
        margin: 0;
        letter-spacing: -0.2px;
    }

    .passengers-header i {
        font-size: 22px;
        color: var(--primary-color);
    }

    .passengers-table {
        width: 100%;
        border-collapse: collapse;
    }

    .passengers-table thead {
        background: #f9fafb;
    }

    .passengers-table th {
        padding: 18px 20px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        color: #374151;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #f0f0f0;
    }

    .passengers-table tbody tr {
        border-bottom: 1px solid #f3f4f6;
        transition: all 0.3s ease;
    }

    .passengers-table tbody tr:hover {
        background: #fafafa;
    }

    .passengers-table td {
        padding: 18px 20px;
        color: #111827;
        font-size: 14px;
        font-weight: 600;
        font-weight: 500;
    }

    .passenger-index {
        background: #f0f4ff;
        color: #0d6efd;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 13px;
    }

    .passenger-name {
        font-weight: 600;
        color: #1f2937;
        letter-spacing: 0.2px;
    }

    .passenger-phone {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--primary-color);
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s;
        font-size: 13px;
    }

    .passenger-phone:hover {
        opacity: 0.75;
        text-decoration: underline;
    }

    .badge-modern {
        display: inline-block;
        padding: 6px 11px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-right: 6px;
        white-space: nowrap;
    }

    .badge-primary {
        background: #f0f4ff;
        color: #0d6efd;
        border: 1px solid #e0e7ff;
    }

    .badge-success {
        background: #f0fdf4;
        color: #059669;
        border: 1px solid #d1fae5;
    }

    .badge-warning {
        background: #fffbeb;
        color: #d97706;
        border: 1px solid #fcd34d;
    }

    .empty-state {
        text-align: center;
        padding: 80px 40px;
        background: #ffffff;
    }

    .empty-state i {
        font-size: 56px;
        color: #d1d5db;
        display: block;
        margin-bottom: 20px;
    }

    .empty-state p {
        color: #6b7280;
        font-size: 15px;
        font-weight: 500;
    }
</style>

<a href="{{ route('driver.trips') }}" class="back-button">
    <i class="bi bi-arrow-left"></i>Back to Trips
</a>

<div class="trip-header">
    <h1>
        <i class="bi bi-geo-alt"></i>
        {{ $trip->origin_dzongkhag }} → {{ $trip->destination_dzongkhag }}
    </h1>
    <div class="trip-info-grid">
        <div class="trip-info-item">
            <div class="trip-info-label">Departure Date & Time</div>
            <div class="trip-info-value">{{ $trip->departure_datetime->format('M d, Y h:i A') }}</div>
        </div>
        <div class="trip-info-item">
            <div class="trip-info-label">Total Seats</div>
            <div class="trip-info-value">{{ $trip->total_seats - $trip->available_seats }} / {{ $trip->total_seats }}</div>
        </div>
        <div class="trip-info-item">
            <div class="trip-info-label">Trip Status</div>
            <div class="trip-info-value">
                <span style="background: rgba(255,255,255,0.25); padding: 6px 14px; border-radius: 6px; font-size: 13px; font-weight: 600;">
                    {{ ucfirst($trip->status) }}
                </span>
            </div>
        </div>
    </div>
</div>

<div class="passengers-container">
    <div class="passengers-header">
        <i class="bi bi-people-fill"></i>
        <h2>Passenger List</h2>
    </div>

    @if($bookings->count() > 0)
        <div style="overflow-x: auto;">
            <table class="passengers-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Passenger Name</th>
                        <th>Phone</th>
                        <th>Booking Info</th>
                        <th>Payment</th>
                    </tr>
                </thead>
                <tbody>
                    @php $passengerCount = 0; @endphp
                    @foreach($bookings as $booking)
                        @foreach($booking->passengers_info as $passenger)
                            @php $passengerCount++; @endphp
                            <tr>
                                <td>
                                    <div class="passenger-index">{{ $passengerCount }}</div>
                                </td>
                                <td><div class="passenger-name">{{ $passenger['name'] }}</div></td>
                                <td>
                                    <a href="tel:{{ $passenger['phone'] }}" class="passenger-phone">
                                        <i class="bi bi-telephone-fill"></i>{{ $passenger['phone'] }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge-modern badge-primary">{{ $booking->seats_booked }} Seat{{ $booking->seats_booked > 1 ? 's' : '' }}</span>
                                    @if($booking->booking_type === 'full')
                                        <span class="badge-modern badge-warning">Full Taxi</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge-modern badge-success">Paid</span>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state">
            <i class="bi bi-inbox"></i>
            <p>No passengers booked yet</p>
        </div>
    @endif
</div>

@endsection
