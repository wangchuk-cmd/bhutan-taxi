@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-1"><i class="bi bi-pencil-square me-2"></i>Edit User</h4>
        <p class="text-muted mb-0">Update passenger or driver details from the admin panel.</p>
    </div>
    <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Back to Users
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.users.update', $user->id) }}" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone_number" class="form-control" value="{{ old('phone_number', $user->phone_number) }}" pattern="{{ \App\Models\Setting::getPhoneNumberPattern() }}" maxlength="{{ \App\Models\Setting::getPhoneNumberDigits() }}" inputmode="numeric" title="{{ \App\Models\Setting::getPhoneNumberHint() }}" required>
                            <small class="text-muted">{{ \App\Models\Setting::getPhoneNumberHint() }}</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select" required>
                                <option value="passenger" {{ old('role', $user->role) === 'passenger' ? 'selected' : '' }}>Passenger</option>
                                <option value="driver" {{ old('role', $user->role) === 'driver' ? 'selected' : '' }}>Driver</option>
                                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Profile Photo</label>
                            <input type="file" name="profile_image" class="form-control" accept="image/png,image/jpeg,image/webp">
                            <small class="text-muted">Upload a new photo to replace the current one.</small>
                        </div>

                        @if($user->role === 'driver' || $user->driver)
                            <div class="col-12 mt-2">
                                <hr>
                                <h5 class="mb-3"><i class="bi bi-car-front me-2"></i>Driver Details</h5>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', optional(optional($user->driver)->date_of_birth)->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">License Number</label>
                                <input type="text" name="license_number" class="form-control" value="{{ old('license_number', optional($user->driver)->license_number) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Taxi Plate Number</label>
                                <input type="text" name="taxi_plate_number" class="form-control" value="{{ old('taxi_plate_number', optional($user->driver)->taxi_plate_number) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Vehicle Type</label>
                                <select name="vehicle_type" class="form-select">
                                    <option value="">Select vehicle type</option>
                                    @foreach(['Sedan','SUV','Van','Mini Van'] as $vehicleType)
                                        <option value="{{ $vehicleType }}" {{ old('vehicle_type', optional($user->driver)->vehicle_type) === $vehicleType ? 'selected' : '' }}>{{ $vehicleType }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fuel Type</label>
                                <select name="fuel_type" class="form-select">
                                    <option value="">Select fuel type</option>
                                    <option value="Fuel" {{ old('fuel_type', optional($user->driver)->fuel_type) === 'Fuel' ? 'selected' : '' }}>Fuel</option>
                                    <option value="Electric" {{ old('fuel_type', optional($user->driver)->fuel_type) === 'Electric' ? 'selected' : '' }}>Electric</option>
                                </select>
                            </div>
                        @endif
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
