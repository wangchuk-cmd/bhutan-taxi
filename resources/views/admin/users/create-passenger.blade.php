@extends('layouts.admin')

@section('title', 'Register Passenger')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-1"><i class="bi bi-person-plus me-2"></i>Register Passenger</h4>
        <p class="text-muted mb-0">Create a passenger account from the admin panel.</p>
    </div>
    <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Back to Users
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.users.storePassenger') }}" autocomplete="off">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone_number" class="form-control" value="{{ old('phone_number') }}" pattern="{{ \App\Models\Setting::getPhoneNumberPattern() }}" maxlength="{{ \App\Models\Setting::getPhoneNumberDigits() }}" inputmode="numeric" title="{{ \App\Models\Setting::getPhoneNumberHint() }}" required>
                            <small class="text-muted">{{ \App\Models\Setting::getPhoneNumberHint() }}</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>Create Passenger
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection