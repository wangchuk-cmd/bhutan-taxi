@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@push('styles')
    <link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
    <style>
        .admin-live-hero {
            position: relative;
            overflow: hidden;
            border-radius: 28px;
            background:
                radial-gradient(circle at 18% 22%, rgba(95, 151, 255, 0.18) 0 2px, transparent 2.5px),
                radial-gradient(circle at 78% 28%, rgba(255, 255, 255, 0.14) 0 2px, transparent 2.5px),
                radial-gradient(circle at 72% 72%, rgba(89, 154, 255, 0.16) 0 2px, transparent 2.5px),
                linear-gradient(rgba(255,255,255,0.07) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.07) 1px, transparent 1px),
                linear-gradient(125deg, #0f1f3a 0%, #1a2f52 70%, #233d66 100%);
            background-size: 220px 220px, 260px 260px, 240px 240px, 72px 72px, 72px 72px, auto;
            background-position: 0 0, 40px 40px, 100px 110px, 0 0, 0 0, center;
            box-shadow: 0 24px 50px rgba(7, 21, 48, 0.22);
        }

        .admin-live-hero::before,
        .admin-live-hero::after {
            content: '';
            position: absolute;
            pointer-events: none;
            z-index: 0;
        }

        .admin-live-hero::before {
            width: 540px;
            height: 540px;
            left: -180px;
            top: -140px;
            background: radial-gradient(circle, rgba(95, 151, 255, 0.32) 0%, rgba(95, 151, 255, 0) 70%);
        }

        .admin-live-hero::after {
            width: 460px;
            height: 460px;
            right: -170px;
            bottom: -140px;
            background: radial-gradient(circle, rgba(55, 98, 206, 0.32) 0%, rgba(55, 98, 206, 0) 70%);
        }

        .admin-live-hero__inner {
            position: relative;
            z-index: 1;
            padding: 2rem;
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(320px, 0.95fr);
            gap: 1.5rem;
            align-items: stretch;
        }

        .admin-live-hero__copy {
            color: #f8fbff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 0.85rem;
        }

        .admin-live-hero__eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            width: fit-content;
            border-radius: 999px;
            padding: 0.35rem 0.95rem;
            background: rgba(16, 185, 129, 0.14);
            border: 1px solid rgba(52, 211, 153, 0.35);
            color: #dcfce7;
            font-size: 0.8rem;
            font-weight: 700;
            backdrop-filter: blur(8px);
        }

        .admin-live-hero__eyebrow .live-dot-ring {
            width: 12px;
            height: 12px;
            border-radius: 999px;
            flex: 0 0 auto;
            border: 1px solid rgba(52, 211, 153, 0.75);
            box-shadow: 0 0 0 5px rgba(52, 211, 153, 0.12);
        }

        .admin-live-hero__eyebrow .live-dot {
            width: 6px;
            height: 6px;
            margin-left: -9px;
            border-radius: 999px;
            flex: 0 0 auto;
            background: #22c55e;
            animation: livePulse 1.6s ease-in-out infinite;
        }

        @keyframes livePulse {
            0%,
            100% {
                transform: scale(1);
                opacity: 0.9;
            }

            50% {
                transform: scale(1.35);
                opacity: 0.6;
            }
        }

        .admin-live-hero__copy h3 {
            margin: 0;
            font-size: clamp(1.7rem, 3vw, 2.6rem);
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        .admin-live-hero__copy p {
            margin: 0;
            max-width: 58ch;
            color: rgba(232, 240, 255, 0.9);
        }

        .admin-live-hero__stats {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 0.35rem;
        }

        .admin-live-hero__chip {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border-radius: 999px;
            padding: 0.6rem 0.95rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.18);
            color: #ffffff;
            font-weight: 700;
        }

        .admin-live-hero__panel {
            border-radius: 22px;
            padding: 1.2rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(10px);
            color: #f8fbff;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
        }

        .admin-live-hero__panel .list-group-item {
            background: transparent;
            color: inherit;
            border-color: rgba(255, 255, 255, 0.12) !important;
        }

        .admin-live-hero__panel .text-muted {
            color: rgba(232, 240, 255, 0.72) !important;
        }

        .admin-live-hero__panel .badge.bg-success-subtle {
            background: rgba(125, 211, 252, 0.15) !important;
            color: #d9f2ff !important;
        }

        @media (max-width: 991.98px) {
            .admin-live-hero__inner {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')

<!-- Dashboard Header -->
<div class="dashboard-header">
    <h1><i class="bi bi-speedometer2 me-2"></i>Dashboard</h1>
    <p>Welcome back! Here's your system overview.</p>
</div>

<!-- KPI Stats Row -->
<div class="row g-3 mb-4">
    <!-- Total Users Card -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card primary">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6>Total Users</h6>
                    <h2>{{ $stats['totalUsers'] }}</h2>
                    <small style="color: #d9f2ff;">Active passengers</small>
                </div>
                <i class="bi bi-people"></i>
            </div>
        </div>
    </div>

    <!-- Total Drivers Card -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card success">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6>Drivers</h6>
                    <h2>{{ $stats['totalDrivers'] }}</h2>
                    <small style="color: #d9f2ff;">{{ $stats['verifiedDrivers'] }} verified</small>
                </div>
                <i class="bi bi-person-badge"></i>
            </div>
        </div>
    </div>

    <!-- Active Trips Card -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card info">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6>Active Trips</h6>
                    <h2>{{ $stats['activeTrips'] }}</h2>
                    <small style="color: #d9f2ff;">In progress</small>
                </div>
                <i class="bi bi-map"></i>
            </div>
        </div>
    </div>

    <!-- Active Bookings Card -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card warning">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6>Bookings</h6>
                    <h2>{{ $stats['activeBookings'] }}</h2>
                    <small style="color: #ffffff;">This month</small>
                </div>
                <i class="bi bi-ticket-perforated"></i>
            </div>
        </div>
    </div>
</div>

<!-- Live Users Hero -->
<div class="admin-live-hero mb-4">
    <div class="admin-live-hero__inner">
        <div class="admin-live-hero__copy">
            <div class="admin-live-hero__eyebrow">
                <span class="live-dot-ring"></span>
                <span class="live-dot"></span>
                Live users on the system
            </div>
            <h3>Who is using Bhutan Taxi right now?</h3>
            <p>Track active passengers, drivers, and admin sessions in one place.</p>
            <div class="admin-live-hero__stats">
                <div class="admin-live-hero__chip">
                    <i class="bi bi-people"></i>
                    {{ $liveUsersCount }} active now
                </div>
                <div class="admin-live-hero__chip">
                    <i class="bi bi-clock-history"></i>
                    Updated from live sessions
                </div>
                <a href="{{ route('admin.users') }}" class="admin-live-hero__chip text-decoration-none">
                    <i class="bi bi-arrow-right"></i>
                    View all users
                </a>
            </div>
        </div>

        <div class="admin-live-hero__panel">
            @if($liveUsersCount > 0)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <strong>Currently online</strong>
                    <span class="badge bg-success">{{ $liveUsersCount }}</span>
                </div>
                <div class="list-group list-group-flush">
                    @foreach($liveUsers->take(5) as $liveUser)
                        <div class="list-group-item px-0 d-flex justify-content-between align-items-center border-0">
                            <div>
                                <strong>{{ $liveUser->name }}</strong>
                                <div class="small text-muted">
                                    @if($liveUser->role === 'admin')
                                        Admin
                                    @elseif($liveUser->role === 'driver')
                                        Driver
                                    @else
                                        Passenger
                                    @endif
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-success-subtle text-success">Online</span><br>
                                <small class="text-muted">{{ $liveUser->last_seen_human }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-broadcast display-6 d-block mb-2"></i>
                    <h5 class="mb-1">No active users right now</h5>
                    <p class="mb-0 text-muted">New sessions will appear here as people use the system.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Financial Overview Section -->
<div class="mb-4">
    <h3 style="font-size: 20px; font-weight: 600; color: #1f2937; margin-bottom: 4px;">Financial Overview</h3>
    <p style="color: #6b7280; font-size: 14px; margin-bottom: 24px;">Track your platform's revenue, commissions, and payouts.</p>
</div>

<div class="row g-3 mb-4">
    <!-- Total Revenue Card -->
    <div class="col-12 col-sm-6 col-lg-4">
        <div style="background: #ffffff; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08); border: 1px solid rgba(0, 0, 0, 0.05); transition: all 0.3s ease;">
            <div style="margin-bottom: 16px;">
                <p style="color: #6b7280; font-size: 13px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; margin: 0;">Total Revenue</p>
            </div>
            
            <div style="margin-bottom: 16px; display: flex; align-items: baseline; gap: 12px;">
                <h2 style="font-size: 32px; font-weight: 700; color: #1f2937; margin: 0;">Nu. {{ number_format($stats['totalRevenue']) }}</h2>
                <div style="display: flex; align-items: center; gap: 4px; background: #e8f5e9; padding: 4px 8px; border-radius: 6px;">
                    <i class="bi bi-arrow-up" style="font-size: 14px; color: #10b981;"></i>
                    <span style="font-size: 13px; font-weight: 600; color: #10b981;">12%</span>
                </div>
            </div>
            
            <p style="color: #9ca3af; font-size: 13px; margin-bottom: 20px; line-height: 1.5;">Insight: Platform revenue shows consistent growth this month.</p>
            
            <a href="{{ route('admin.financial.details', ['metric' => 'revenue']) }}" style="display: inline-block; color: #3b82f6; font-size: 13px; font-weight: 500; text-decoration: none; transition: color 0.2s;">
                View Details <i class="bi bi-arrow-right ms-1" style="font-size: 12px;"></i>
            </a>
        </div>
    </div>

    <!-- Service Charges Card -->
    <div class="col-12 col-sm-6 col-lg-4">
        <div style="background: #ffffff; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08); border: 1px solid rgba(0, 0, 0, 0.05); transition: all 0.3s ease;">
            <div style="margin-bottom: 16px;">
                <p style="color: #6b7280; font-size: 13px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; margin: 0;">Service Charges</p>
            </div>
            
            <div style="margin-bottom: 16px; display: flex; align-items: baseline; gap: 12px;">
                <h2 style="font-size: 32px; font-weight: 700; color: #1f2937; margin: 0;">Nu. {{ number_format($stats['serviceCharges']) }}</h2>
                <div style="display: flex; align-items: center; gap: 4px; background: #fef3c7; padding: 4px 8px; border-radius: 6px;">
                    <i class="bi bi-arrow-up" style="font-size: 14px; color: #f59e0b;"></i>
                    <span style="font-size: 13px; font-weight: 600; color: #f59e0b;">8%</span>
                </div>
            </div>
            
            <p style="color: #9ca3af; font-size: 13px; margin-bottom: 20px; line-height: 1.5;">Insight: Commission collection is on track with expected margins.</p>
            
            <a href="{{ route('admin.financial.details', ['metric' => 'charges']) }}" style="display: inline-block; color: #3b82f6; font-size: 13px; font-weight: 500; text-decoration: none; transition: color 0.2s;">
                View Details <i class="bi bi-arrow-right ms-1" style="font-size: 12px;"></i>
            </a>
        </div>
    </div>

    <!-- Pending Payouts Card -->
    <div class="col-12 col-sm-6 col-lg-4">
        <div style="background: #ffffff; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08); border: 1px solid rgba(0, 0, 0, 0.05); transition: all 0.3s ease;">
            <div style="margin-bottom: 16px;">
                <p style="color: #6b7280; font-size: 13px; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; margin: 0;">Pending Payouts</p>
            </div>
            
            <div style="margin-bottom: 16px; display: flex; align-items: baseline; gap: 12px;">
                <h2 style="font-size: 32px; font-weight: 700; color: #1f2937; margin: 0;">Nu. {{ number_format($stats['pendingPayouts']) }}</h2>
                <div style="display: flex; align-items: center; gap: 4px; background: #fecaca; padding: 4px 8px; border-radius: 6px;">
                    <i class="bi bi-arrow-down" style="font-size: 14px; color: #ef4444;"></i>
                    <span style="font-size: 13px; font-weight: 600; color: #ef4444;">5%</span>
                </div>
            </div>
            
            <p style="color: #9ca3af; font-size: 13px; margin-bottom: 20px; line-height: 1.5;">Insight: Payouts processing on schedule, lower than last period.</p>
            
            <a href="{{ route('admin.payouts') }}" style="display: inline-block; color: #3b82f6; font-size: 13px; font-weight: 500; text-decoration: none; transition: color 0.2s;">
                View Details <i class="bi bi-arrow-right ms-1" style="font-size: 12px;"></i>
            </a>
        </div>
    </div>
</div>

<!-- Main Content Row -->
<div class="row g-3">
    <!-- Pending Driver Approvals -->
    <div class="col-12 col-lg-6">
        <div class="dashboard-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-person-check me-2"></i>Pending Approvals</h5>
                <a href="{{ route('admin.drivers') }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-arrow-right me-1"></i>View All
                </a>
            </div>
            <div class="card-body p-0">
                @if($pendingDrivers->count() > 0)
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <tbody>
                                @foreach($pendingDrivers->take(5) as $driver)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar" style="width: 40px; height: 40px; background: linear-gradient(135deg, #0d6efd, #06b6d4); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; margin-right: 12px;">
                                                    {{ substr($driver->user->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <strong>{{ $driver->user->name }}</strong><br>
                                                    <small style="color: #9ca3af;">{{ $driver->taxi_plate_number }}</small>
                                                    <div><small style="color: #9ca3af;">{{ $driver->age ? $driver->age . ' years' : 'Age N/A' }}</small></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <form action="{{ route('admin.drivers.verify', $driver->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button class="btn btn-sm" style="background: linear-gradient(135deg, #10b981, #34d399); color: white; border: none;">
                                                    <i class="bi bi-check-circle me-1"></i>Verify
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="bi bi-check-circle"></i>
                        <p>All drivers verified!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="col-12 col-lg-6">
        <div class="dashboard-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Bookings</h5>
                <a href="{{ route('admin.bookings') }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-arrow-right me-1"></i>View All
                </a>
            </div>
            <div class="card-body p-0">
                @if($recentBookings->count() > 0)
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Passenger</th>
                                    <th>Route</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentBookings->take(5) as $booking)
                                    <tr>
                                        <td>
                                            <strong>{{ $booking->passenger->name ?? 'N/A' }}</strong>
                                            <div style="font-size: 12px; color: #6c757d;">Booker</div>
                                        </td>
                                        <td style="font-size: 13px;">
                                            {{ substr($booking->trip->origin_dzongkhag, 0, 10) }} <i class="bi bi-arrow-right"></i> {{ substr($booking->trip->destination_dzongkhag, 0, 10) }}
                                        </td>
                                        <td>
                                            @if($booking->payment_status === 'paid')
                                                <span class="badge bg-success">Paid</span>
                                            @elseif($booking->payment_status === 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @else
                                                <span class="badge bg-danger">{{ ucfirst($booking->payment_status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <p>No bookings yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Dashboard Charts Row -->
<div class="row g-3 mt-1">
    <div class="col-12 col-xl-6">
        <div class="dashboard-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-people-fill me-2"></i>Driver Age Ranges</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">Driver age distribution based on recorded date of birth.</p>
                @if(array_sum($ageRangeChart['values']) > 0)
                    <div class="row g-2 align-items-stretch">
                        <div class="col-12 col-md-7" style="min-height: 240px;">
                            <canvas id="ageRangeChart"></canvas>
                        </div>
                        <div class="col-12 col-md-5">
                            @php $totalDrivers = array_sum($ageRangeChart['values']); @endphp
                            <div class="summary-panel h-100">
                                <div class="d-flex align-items-start justify-content-between mb-4">
                                    <div>
                                        <h6 class="mb-1">Age Range Summary</h6>
                                        <p class="text-muted small mb-0">Drivers grouped by public age range.</p>
                                    </div>
                                    <span class="badge bg-primary-subtle text-primary">Total {{ $totalDrivers }}</span>
                                </div>
                                <div class="range-list">
                                    @foreach($driverAgeRanges as $range)
                                        @php $percent = $totalDrivers ? round(($range['count'] / $totalDrivers) * 100) : 0; @endphp
                                        <div class="range-item mb-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="range-label">{{ $range['label'] }}</span>
                                                <span class="range-count badge bg-secondary-subtle text-secondary">{{ $range['count'] }}</span>
                                            </div>
                                            <div class="range-meter bg-white border">
                                                <div class="range-fill" style="width: {{ $percent }}%;"></div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-1">
                                                <small class="text-muted">{{ $percent }}%</small>
                                                <small class="text-muted">{{ $range['count'] }} drivers</small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="empty-state py-5">
                        <i class="bi bi-person-x"></i>
                        <p>No driver age data available yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-6">
        <div class="dashboard-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Top Route Usage</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">Booking volume by route across all customer and admin-created bookings.</p>
                @if(!empty($routeUsageChart['labels']))
                    <div class="row g-2 align-items-stretch">
                        <div class="col-12 col-lg-7" style="min-height: 240px;">
                            <div class="h-100">
                                <canvas id="routeUsageChart"></canvas>
                            </div>
                        </div>
                        <div class="col-12 col-lg-5">
                            <div class="summary-panel h-100">
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div>
                                        <h6 class="mb-1">Top Route</h6>
                                        <p class="text-muted small mb-0">Most booked route by share.</p>
                                    </div>
                                    <span class="badge bg-success-subtle text-success">Top</span>
                                </div>
                                <div class="mb-3">
                                    <p class="text-muted small mb-1">Route</p>
                                    <h5 class="mb-0">{{ $routeUsageSummary['route_name'] }}</h5>
                                </div>
                                <div class="summary-item">
                                    <span class="text-muted">Bookings</span>
                                    <strong>{{ $routeUsageSummary['bookings'] }}</strong>
                                </div>
                                <div class="summary-item">
                                    <span class="text-muted">Share</span>
                                    <strong>{{ $routeUsageSummary['percentage'] }}%</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="empty-state py-5">
                        <i class="bi bi-pie-chart"></i>
                        <p>No route usage data yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-3 mt-4">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="card-header">
                <h5><i class="bi bi-lightning me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-12 col-sm-6 col-md-3">
                        <a href="{{ route('admin.drivers') }}" class="btn btn-light w-100 text-start" style="border: 1px solid #e5e7eb; padding: 12px; border-radius: 8px;">
                            <i class="bi bi-people me-2" style="color: #0d6efd;"></i>
                            <small>Manage Drivers</small>
                        </a>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <a href="{{ route('admin.trips') }}" class="btn btn-light w-100 text-start" style="border: 1px solid #e5e7eb; padding: 12px; border-radius: 8px;">
                            <i class="bi bi-map me-2" style="color: #06b6d4;"></i>
                            <small>View Trips</small>
                        </a>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <a href="{{ route('admin.bookings') }}" class="btn btn-light w-100 text-start" style="border: 1px solid #e5e7eb; padding: 12px; border-radius: 8px;">
                            <i class="bi bi-ticket me-2" style="color: #f59e0b;"></i>
                            <small>View Bookings</small>
                        </a>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <a href="{{ route('admin.payouts') }}" class="btn btn-light w-100 text-start" style="border: 1px solid #e5e7eb; padding: 12px; border-radius: 8px;">
                            <i class="bi bi-wallet2 me-2" style="color: #10b981;"></i>
                            <small>Process Payouts</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(!empty($routeUsageChart['labels']))
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const canvas = document.getElementById('routeUsageChart');
                if (!canvas || typeof Chart === 'undefined') return;

                const routeLabels = @json($routeUsageChart['labels']);
                const routeValues = @json($routeUsageChart['values']);
                const ageLabels = @json($ageRangeChart['labels']);
                const ageValues = @json($ageRangeChart['values']);
                const colors = ['#0d6efd', '#20c997', '#fd7e14', '#dc3545', '#6f42c1', '#0dcaf0'];
                const ageColors = ['#0d6efd', '#0dcaf0', '#20c997', '#f59e0b', '#dc3545'];

                const routeCanvas = document.getElementById('routeUsageChart');
                if (routeCanvas && typeof Chart !== 'undefined') {
                    new Chart(routeCanvas, {
                        type: 'pie',
                        data: {
                            labels: routeLabels,
                            datasets: [{
                                data: routeValues,
                                backgroundColor: routeLabels.map((_, index) => colors[index % colors.length]),
                                borderColor: '#ffffff',
                                borderWidth: 2,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function (context) {
                                            const total = context.dataset.data.reduce((sum, value) => sum + value, 0);
                                            const value = context.raw;
                                            const percentage = total ? ((value / total) * 100).toFixed(1) : 0;
                                            return `${context.label}: ${value} bookings (${percentage}%)`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                const ageCanvas = document.getElementById('ageRangeChart');
                if (ageCanvas && typeof Chart !== 'undefined') {
                    new Chart(ageCanvas, {
                        type: 'pie',
                        data: {
                            labels: ageLabels,
                            datasets: [{
                                data: ageValues,
                                backgroundColor: ageLabels.map((_, index) => ageColors[index % ageColors.length]),
                                borderColor: '#ffffff',
                                borderWidth: 2,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function (context) {
                                            const total = context.dataset.data.reduce((sum, value) => sum + value, 0);
                                            const value = context.raw;
                                            const percentage = total ? ((value / total) * 100).toFixed(1) : 0;
                                            return `${context.label}: ${value} drivers (${percentage}%)`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            });
        </script>
    @endpush
@endif

@endsection
