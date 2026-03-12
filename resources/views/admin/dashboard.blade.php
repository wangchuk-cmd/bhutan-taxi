@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@push('styles')
    <link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
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
                    <small style="color: #9ca3af;">Active passengers</small>
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
                    <small style="color: #9ca3af;">{{ $stats['verifiedDrivers'] }} verified</small>
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
                    <small style="color: #9ca3af;">In progress</small>
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
                    <small style="color: #9ca3af;">This month</small>
                </div>
                <i class="bi bi-ticket-perforated"></i>
            </div>
        </div>
    </div>
</div>

<!-- Revenue Section -->
<div class="row g-3 mb-4">
    <!-- Total Revenue -->
    <div class="col-12 col-md-4">
        <div class="dashboard-card">
            <div class="card-header">
                <h5><i class="bi bi-graph-up me-2"></i>Total Revenue</h5>
            </div>
            <div class="card-body text-center py-4">
                <h3 style="font-size: 32px; font-weight: 700; color: #10b981;">Nu. {{ number_format($stats['totalRevenue']) }}</h3>
                <p class="text-muted mb-0">All-time platform revenue</p>
            </div>
        </div>
    </div>

    <!-- Service Charges -->
    <div class="col-12 col-md-4">
        <div class="dashboard-card">
            <div class="card-header">
                <h5><i class="bi bi-percent me-2"></i>Service Charges</h5>
            </div>
            <div class="card-body text-center py-4">
                <h3 style="font-size: 32px; font-weight: 700; color: #f59e0b;">Nu. {{ number_format($stats['serviceCharges']) }}</h3>
                <p class="text-muted mb-0">Platform commission</p>
            </div>
        </div>
    </div>

    <!-- Pending Payouts -->
    <div class="col-12 col-md-4">
        <div class="dashboard-card">
            <div class="card-header">
                <h5><i class="bi bi-wallet me-2"></i>Pending Payouts</h5>
            </div>
            <div class="card-body text-center py-4">
                <h3 style="font-size: 32px; font-weight: 700; color: #ef4444;">Nu. {{ number_format($stats['pendingPayouts']) }}</h3>
                <p class="text-muted mb-0">To be paid to drivers</p>
            </div>
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
                                                <div class="avatar" style="width: 40px; height: 40px; background: linear-gradient(135deg, #2563eb, #06b6d4); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; margin-right: 12px;">
                                                    {{ substr($driver->user->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <strong>{{ $driver->user->name }}</strong><br>
                                                    <small style="color: #9ca3af;">{{ $driver->taxi_plate_number }}</small>
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
                            <i class="bi bi-people me-2" style="color: #2563eb;"></i>
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

@endsection
