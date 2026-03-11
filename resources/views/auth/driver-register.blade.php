@extends('layouts.app')

@section('title', 'Driver Registration')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <i class="bi bi-car-front-fill text-warning display-4"></i>
                        <h3 class="mt-2">Become a Driver</h3>
                        <p class="text-muted">Register to start offering taxi services</p>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        After registration, your account will need admin approval before you can create trips.
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('driver.register') }}">
                        @csrf
                        
                        <h5 class="mb-3 text-primary"><i class="bi bi-person me-2"></i>Personal Information</h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control" 
                                       value="{{ old('name') }}" placeholder="Enter your full name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" name="phone_number" class="form-control" 
                                       value="{{ old('phone_number') }}" placeholder="e.g., 17123456" 
                                       pattern="[0-9]+" inputmode="numeric" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" 
                                   value="{{ old('email') }}" placeholder="Enter your email" required>
                        </div>

                        <hr class="my-4">
                        
                        <h5 class="mb-3 text-primary"><i class="bi bi-car-front me-2"></i>Vehicle Information</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">License Number</label>
                                <input type="text" name="license_number" class="form-control" 
                                       value="{{ old('license_number') }}" placeholder="e.g., DL-12345" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Taxi Plate Number</label>
                                <input type="text" name="taxi_plate_number" class="form-control" 
                                       value="{{ old('taxi_plate_number') }}" placeholder="e.g., BP-1-1234" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Vehicle Type</label>
                            <select name="vehicle_type" class="form-select" required>
                                <option value="">Select vehicle type</option>
                                <option value="Sedan" {{ old('vehicle_type') == 'Sedan' ? 'selected' : '' }}>Sedan (4 seats)</option>
                                <option value="SUV" {{ old('vehicle_type') == 'SUV' ? 'selected' : '' }}>SUV (6 seats)</option>
                                <option value="Van" {{ old('vehicle_type') == 'Van' ? 'selected' : '' }}>Van (8-10 seats)</option>
                                <option value="Mini Van" {{ old('vehicle_type') == 'Mini Van' ? 'selected' : '' }}>Mini Van (5-7 seats)</option>
                            </select>
                        </div>

                        <hr class="my-4">
                        
                        <h5 class="mb-3 text-primary"><i class="bi bi-lock me-2"></i>Account Security</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" 
                                       placeholder="Min 8 characters" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control" 
                                       placeholder="Confirm password" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-warning w-100 mb-3">
                            <i class="bi bi-car-front me-2"></i>Register as Driver
                        </button>
                    </form>

                    <hr>

                    <div class="text-center">
                        <p class="mb-2">Already have an account?</p>
                        <a href="{{ route('login') }}" class="btn btn-outline-primary">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
