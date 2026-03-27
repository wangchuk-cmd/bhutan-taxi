@extends('layouts.admin')

@section('title', 'Manage Drivers')

@section('content')
@include('components.confirm-modal')
<h4 class="mb-4"><i class="bi bi-person-badge me-2"></i>Drivers Management</h4>

<div class="card">
    <div class="card-body">
        @if($drivers->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Driver</th>
                            <th>Contact</th>
                            <th>Vehicle</th>
                            <th>License</th>
                            <th>Trips</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($drivers as $driver)
                            <tr>
                                <td><strong>{{ $driver->user->name }}</strong></td>
                                <td>{{ $driver->user->phone_number }}<br><small class="text-muted">{{ $driver->user->email }}</small></td>
                                <td>{{ $driver->vehicle_type }}<br><small class="text-muted">{{ $driver->taxi_plate_number }}</small></td>
                                <td>{{ $driver->license_number }}</td>
                                <td><span class="badge bg-primary">{{ $driver->trips_count }}</span></td>
                                <td>
                                    @if($driver->verified)
                                        <span class="badge bg-success">Verified</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                    @if(!$driver->active)
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.drivers.show', $driver->id) }}" class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if(!$driver->verified)
                                            <form action="{{ route('admin.drivers.verify', $driver->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button class="btn btn-sm btn-success" title="Verify"><i class="bi bi-check"></i></button>
                                            </form>
                                        @endif
                                        <form id="toggleForm-{{ $driver->id }}" action="{{ route('admin.drivers.toggle', $driver->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="button" class="btn btn-sm btn-outline-{{ $driver->active ? 'danger' : 'success' }}" title="{{ $driver->active ? 'Deactivate' : 'Activate' }}" onclick="showConfirmModal('Are you sure you want to {{ $driver->active ? 'deactivate' : 'activate' }} this driver?', '{{ $driver->active ? 'Deactivate' : 'Activate' }} Driver', function() { document.getElementById('toggleForm-{{ $driver->id }}').submit(); })">
                                                <i class="bi bi-{{ $driver->active ? 'x-circle' : 'check-circle' }}"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $drivers->links() }}
        @else
            <div class="text-center py-5">
                <i class="bi bi-person-badge display-1 text-muted"></i>
                <p class="mt-3 text-muted">No drivers registered yet</p>
            </div>
        @endif
    </div>
</div>
@endsection
