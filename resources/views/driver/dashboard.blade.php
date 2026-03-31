@extends('layouts.driver')

@section('title', 'Driver Dashboard')

@section('content')
<style>
    :root {
        --primary-color: #0d6efd;
        --primary-light: #3b82f6;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --text-dark: #111827;
        --text-muted: #374151;
        --bg-light: #f3f4f6;
        --card-shadow: 0 1px 3px rgba(0, 0, 0, 0.08), 0 1px 2px rgba(0, 0, 0, 0.04);
        --card-shadow-lg: 0 4px 6px rgba(0, 0, 0, 0.07), 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
        gap: 20px;
    }

    .dashboard-header h1 {
        font-size: 28px;
        font-weight: 600;
        color: var(--text-dark);
        margin: 0;
    }

    .dashboard-header p {
        color: var(--text-muted);
        font-size: 14px;
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
        white-space: nowrap;
    }

    .create-btn:hover {
        background: #1d4ed8;
        box-shadow: var(--card-shadow-lg);
        transform: translateY(-2px);
    }

    .verification-alert {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border: 1px solid #fcd34d;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 32px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .verification-alert i {
        font-size: 20px;
        color: #d97706;
    }

    .verification-alert strong {
        color: #b45309;
    }

    .verification-alert p {
        margin: 0;
        color: #92400e;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
    }

    .stat-card-modern {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
        border: 1px solid #f0f0f0;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .stat-card-modern:hover {
        box-shadow: var(--card-shadow-lg);
        border-color: #e5e7eb;
        transform: translateY(-4px);
    }

    .stat-content {
        flex: 1;
    }

    .stat-label {
        font-size: 13px;
        font-weight: 500;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 32px;
        font-weight: 700;
        color: var(--text-dark);
        margin: 0;
    }

    .stat-icon-wrapper {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        flex-shrink: 0;
    }

    .stat-icon-wrapper.primary { background: #dbeafe; color: var(--primary-color); }
    .stat-icon-wrapper.success { background: #d1fae5; color: var(--success-color); }
    .stat-icon-wrapper.warning { background: #fed7aa; color: var(--warning-color); }
    .stat-icon-wrapper.danger { background: #fee2e2; color: var(--danger-color); }
</style>

<div class="dashboard-header">
    <div>
        <h1>Welcome back!</h1>
        <p>Ready to continue your trips?</p>
    </div>
    @if($driver->verified)
        <a href="{{ route('driver.trips.create') }}" class="create-btn">
            <i class="bi bi-plus-circle"></i>Create Trip
        </a>
    @endif
</div>

@if(!$driver->verified)
    <div class="verification-alert">
        <i class="bi bi-exclamation-triangle"></i>
        <div>
            <strong>Account Pending Verification</strong>
            <p>Your driver account is waiting for admin approval. You cannot create trips until verified.</p>
        </div>
    </div>
@endif

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card-modern">
        <div class="stat-content">
            <div class="stat-label">Total Trips</div>
            <h2 class="stat-value">{{ $totalTrips }}</h2>
        </div>
        <div class="stat-icon-wrapper primary">
            <i class="bi bi-map"></i>
        </div>
    </div>

    <div class="stat-card-modern">
        <div class="stat-content">
            <div class="stat-label">Completed</div>
            <h2 class="stat-value">{{ $completedTrips }}</h2>
        </div>
        <div class="stat-icon-wrapper success">
            <i class="bi bi-check-circle"></i>
        </div>
    </div>

    <div class="stat-card-modern">
        <div class="stat-content">
            <div class="stat-label">Total Earnings</div>
            <h2 class="stat-value">Nu. {{ number_format($totalEarnings) }}</h2>
        </div>
        <div class="stat-icon-wrapper warning">
            <i class="bi bi-cash-stack"></i>
        </div>
    </div>

    <div class="stat-card-modern">
        <div class="stat-content">
            <div class="stat-label">Pending Payout</div>
            <h2 class="stat-value">Nu. {{ number_format($pendingPayouts) }}</h2>
        </div>
        <div class="stat-icon-wrapper danger">
            <i class="bi bi-hourglass-split"></i>
        </div>
    </div>
</div>

<!-- Upcoming Trips -->
<div style="background: white; border-radius: 12px; padding: 0; box-shadow: var(--card-shadow); border: 1px solid #f0f0f0; margin-bottom: 40px;">
    <div style="padding: 24px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-size: 18px; font-weight: 600; color: var(--text-dark); margin: 0; display: flex; align-items: center; gap: 8px;">
            <i class="bi bi-calendar-event" style="font-size: 20px;"></i>
            Upcoming Trips
        </h3>
        <a href="{{ route('driver.trips') }}" style="color: var(--primary-color); font-size: 13px; font-weight: 500; text-decoration: none;">View All →</a>
    </div>

    <div style="padding: 24px;">
        @if($upcomingTrips->count() > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                @foreach($upcomingTrips as $trip)
                    <div style="background: var(--bg-light); border-radius: 10px; padding: 20px; border: 1px solid #e5e7eb; transition: all 0.3s ease;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                            <div>
                                <div style="font-size: 13px; font-weight: 500; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Route</div>
                                <div style="font-size: 16px; font-weight: 600; color: var(--text-dark);">
                                    <span style="color: var(--primary-color);">{{ $trip->origin_dzongkhag }}</span>
                                    <i class="bi bi-arrow-right" style="font-size: 12px; margin: 0 8px;"></i>
                                    <span style="color: var(--primary-color);">{{ $trip->destination_dzongkhag }}</span>
                                </div>
                            </div>
                            <span style="background: #dbeafe; color: var(--primary-color); padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                {{ $trip->available_seats }}/{{ $trip->total_seats }} Seats
                            </span>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid #e5e7eb;">
                            <div>
                                <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Date</div>
                                <div style="font-size: 13px; font-weight: 500; color: var(--text-dark);">{{ $trip->departure_datetime->format('M d, Y') }}</div>
                            </div>
                            <div>
                                <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Time</div>
                                <div style="font-size: 13px; font-weight: 500; color: var(--text-dark);">{{ $trip->departure_datetime->format('h:i A') }}</div>
                            </div>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                            <div>
                                <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px;">Price per Seat</div>
                                <div style="font-size: 18px; font-weight: 700; color: var(--primary-color);">Nu. {{ number_format($trip->price_per_seat) }}</div>
                            </div>
                        </div>

                        <a href="{{ route('driver.passengers', $trip->id) }}" style="display: inline-flex; align-items: center; gap: 8px; background: var(--primary-color); color: white; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 500; width: 100%; justify-content: center; transition: all 0.2s; border: none; cursor: pointer;">
                            <i class="bi bi-people"></i>View Passengers
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align: center; padding: 40px 20px;">
                <i class="bi bi-calendar-x" style="font-size: 48px; color: var(--text-muted); display: block; margin-bottom: 16px;"></i>
                <h4 style="color: var(--text-dark); margin-bottom: 8px;">No Upcoming Trips</h4>
                <p style="color: var(--text-muted); margin-bottom: 24px;">You don't have any scheduled trips at the moment.</p>
                @if($driver->verified)
                    <a href="{{ route('driver.trips.create') }}" style="display: inline-flex; align-items: center; gap: 8px; background: var(--primary-color); color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 500; transition: all 0.2s;">
                        <i class="bi bi-plus-circle"></i>Create Your First Trip
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
