@extends('layouts.driver')

@section('title', 'Profile')

@section('content')
<h4 class="mb-4"><i class="bi bi-person me-2"></i>My Profile</h4>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Personal Information</h5>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('driver.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone_number" class="form-control" 
                                   value="{{ auth()->user()->phone_number }}" 
                                   pattern="[0-9]+" inputmode="numeric" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="{{ auth()->user()->email }}" disabled>
                        <small class="text-muted">Email cannot be changed</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Vehicle Type</label>
                        <select name="vehicle_type" class="form-select" required>
                            <option value="Sedan" {{ $driver->vehicle_type === 'Sedan' ? 'selected' : '' }}>Sedan</option>
                            <option value="SUV" {{ $driver->vehicle_type === 'SUV' ? 'selected' : '' }}>SUV</option>
                            <option value="Van" {{ $driver->vehicle_type === 'Van' ? 'selected' : '' }}>Van</option>
                            <option value="Mini Van" {{ $driver->vehicle_type === 'Mini Van' ? 'selected' : '' }}>Mini Van</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Update Profile
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Vehicle Details</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">License Number</small>
                    <p class="fw-bold mb-0">{{ $driver->license_number }}</p>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Plate Number</small>
                    <p class="fw-bold mb-0">{{ $driver->taxi_plate_number }}</p>
                </div>
                <small class="text-muted">These details cannot be changed. Contact admin if needed.</small>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Account Status</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Verification</span>
                    @if($driver->verified)
                        <span class="badge bg-success">Verified</span>
                    @else
                        <span class="badge bg-warning text-dark">Pending</span>
                    @endif
                </div>
                <div class="d-flex justify-content-between">
                    <span>Account</span>
                    @if($driver->active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-danger">Inactive</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
