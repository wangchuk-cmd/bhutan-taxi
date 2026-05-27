@extends('layouts.admin')

@section('title', 'Manage Users')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h4 class="mb-0"><i class="bi bi-people me-2"></i>Users Management</h4>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.users.createPassenger') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-person-plus me-1"></i>New Passenger
        </a>
        <a href="{{ route('admin.users.createDriver') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-car-front me-1"></i>New Driver
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="text-muted small mb-1">Live now</div>
                <div class="display-6 fw-bold text-success mb-1">{{ $liveUsersCount }}</div>
                <div class="text-muted small">Users active in the last 5 minutes</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-8">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <div class="text-muted small">Active sessions</div>
                        <h6 class="mb-0">Who is currently using the system</h6>
                    </div>
                    <span class="badge bg-success">{{ $liveUsersCount }} online</span>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    @forelse($liveUsers as $liveUser)
                        <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3 py-2">
                            {{ $liveUser->name }}
                        </span>
                    @empty
                        <span class="text-muted">No users online right now</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($users->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Contact</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Bookings</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td><strong>{{ $user->name }}</strong></td>
                                <td>
                                    {{ $user->email }}<br>
                                    <small class="text-muted">{{ $user->phone_number }}</small>
                                </td>
                                <td>
                                    @if($user->role === 'admin')
                                        <span class="badge bg-danger">Admin</span>
                                    @elseif($user->role === 'driver')
                                        <span class="badge bg-primary">Driver</span>
                                    @else
                                        <span class="badge bg-secondary">Passenger</span>
                                    @endif
                                </td>
                                <td>
                                    @if(in_array($user->id, $liveUserIds))
                                        <span class="badge bg-success">Online now</span>
                                    @else
                                        <span class="badge bg-light text-muted border">Offline</span>
                                    @endif
                                </td>
                                <td>{{ $user->bookings_count }}</td>
                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                                <td>
                                    @if($user->role !== 'admin')
                                        <div class="d-flex flex-wrap gap-2 align-items-center">
                                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil me-1"></i>Edit
                                            </a>
                                            <form action="{{ route('admin.users.role', $user->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <select name="role" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                                    <option value="passenger" {{ $user->role === 'passenger' ? 'selected' : '' }}>Passenger</option>
                                                    <option value="driver" {{ $user->role === 'driver' ? 'selected' : '' }}>Driver</option>
                                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                                </select>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $users->links() }}
        @else
            <div class="text-center py-5">
                <i class="bi bi-people display-1 text-muted"></i>
                <p class="mt-3 text-muted">No users yet</p>
            </div>
        @endif
    </div>
</div>
@endsection
