@extends('layouts.admin')

@section('title', 'Driver Details')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.drivers') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-2"></i>Back to Drivers
    </a>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-body text-center">
                <i class="bi bi-person-circle display-1 text-primary"></i>
                <h4 class="mt-3">{{ $driver->user->name }}</h4>
                <p class="text-muted mb-2">{{ $driver->user->email }}</p>
                <p class="mb-3"><i class="bi bi-telephone me-1"></i>{{ $driver->user->phone_number }}</p>
                <div class="d-flex justify-content-center gap-2">
                    @if($driver->verified)
                        <span class="badge bg-success">Verified</span>
                    @else
                        <span class="badge bg-warning text-dark">Pending</span>
                    @endif
                    <span class="badge bg-{{ $driver->active ? 'success' : 'danger' }}">{{ $driver->active ? 'Active' : 'Inactive' }}</span>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><h6 class="mb-0">Vehicle Info</h6></div>
            <div class="card-body">
                <p class="mb-2"><strong>Type:</strong> {{ $driver->vehicle_type }}</p>
                <p class="mb-2"><strong>Plate:</strong> {{ $driver->taxi_plate_number }}</p>
                <p class="mb-0"><strong>License:</strong> {{ $driver->license_number }}</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h6 class="mb-0">Earnings</h6></div>
            <div class="card-body">
                <p class="mb-2"><strong>Total Earned:</strong> Nu. {{ number_format($totalEarnings) }}</p>
                <p class="mb-0"><strong>Pending:</strong> Nu. {{ number_format($pendingPayouts) }}</p>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Recent Trips</h5></div>
            <div class="card-body">
                @if($driver->trips->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr><th>Route</th><th>Date</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                                @foreach($driver->trips->take(10) as $trip)
                                    <tr>
                                        <td>{{ $trip->origin_dzongkhag }} <i class="bi bi-arrow-right small"></i> {{ $trip->destination_dzongkhag }}</td>
                                        <td>{{ $trip->departure_datetime->format('M d, Y') }}</td>
                                        <td><span class="badge bg-{{ $trip->status === 'active' ? 'success' : ($trip->status === 'completed' ? 'primary' : 'danger') }}">{{ ucfirst($trip->status) }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center">No trips yet</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
