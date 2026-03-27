@extends('layouts.admin')

@section('title', 'Manage Trips')

@section('content')
@include('components.confirm-modal')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-car-front me-2"></i>Trips Management</h4>
    <a href="{{ route('admin.trips.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Create Trip
    </a>
</div>

<div class="card">
    <div class="card-body">
        @if($trips->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Route</th>
                            <th>Driver</th>
                            <th>Departure</th>
                            <th>Price/Seat</th>
                            <th>Seats</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($trips as $trip)
                            <tr>
                                <td><strong>{{ $trip->origin_dzongkhag }}</strong> <i class="bi bi-arrow-right small"></i> {{ $trip->destination_dzongkhag }}</td>
                                <td>{{ $trip->driver->user->name ?? 'N/A' }}</td>
                                <td>{{ $trip->departure_datetime->format('M d, Y') }}<br><small>{{ $trip->departure_datetime->format('h:i A') }}</small></td>
                                <td>Nu. {{ number_format($trip->price_per_seat) }}</td>
                                <td>{{ $trip->available_seats }}/{{ $trip->total_seats }}</td>
                                <td><span class="badge bg-{{ $trip->status === 'active' ? 'success' : ($trip->status === 'completed' ? 'primary' : 'danger') }}">{{ ucfirst($trip->status) }}</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.trips.show', $trip->id) }}" class="btn btn-outline-primary" title="View"><i class="bi bi-eye"></i></a>
                                        <a href="{{ route('admin.trips.edit', $trip->id) }}" class="btn btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                                        @if($trip->status === 'active')
                                            <form id="cancelForm-{{ $trip->id }}" action="{{ route('admin.trips.cancel', $trip->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="button" class="btn btn-sm btn-outline-warning" title="Cancel" onclick="showConfirmModal('Are you sure you want to cancel this trip?', 'Cancel Trip', function() { document.getElementById('cancelForm-{{ $trip->id }}').submit(); })"><i class="bi bi-x-circle"></i></button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $trips->links() }}
        @else
            <div class="text-center py-5">
                <i class="bi bi-car-front display-1 text-muted"></i>
                <p class="mt-3 text-muted">No trips available</p>
                <a href="{{ route('admin.trips.create') }}" class="btn btn-primary">Create First Trip</a>
            </div>
        @endif
    </div>
</div>
@endsection
