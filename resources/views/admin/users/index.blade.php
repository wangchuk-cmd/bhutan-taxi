@extends('layouts.admin')

@section('title', 'Manage Users')

@section('content')
<h4 class="mb-4"><i class="bi bi-people me-2"></i>Users Management</h4>

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
                                <td>{{ $user->bookings_count }}</td>
                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                                <td>
                                    @if($user->role !== 'admin')
                                        <form action="{{ route('admin.users.role', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <select name="role" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                                <option value="passenger" {{ $user->role === 'passenger' ? 'selected' : '' }}>Passenger</option>
                                                <option value="driver" {{ $user->role === 'driver' ? 'selected' : '' }}>Driver</option>
                                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                            </select>
                                        </form>
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
