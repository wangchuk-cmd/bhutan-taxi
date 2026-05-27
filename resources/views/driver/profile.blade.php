@extends('layouts.driver')

@section('title', 'Profile')

@section('content')

<style>
    :root {
        --primary-color: #0d6efd;
        --text-dark: #111827;
        --text-muted: #374151;
        --bg-light: #f3f4f6;
        --card-shadow: 0 1px 3px rgba(0, 0, 0, 0.08), 0 1px 2px rgba(0, 0, 0, 0.04);
        --card-shadow-lg: 0 4px 6px rgba(0, 0, 0, 0.07), 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .page-title {
        font-size: 28px;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 32px;
    }

    .form-card {
        background: white;
        border-radius: 12px;
        padding: 32px;
        box-shadow: var(--card-shadow);
        border: 1px solid #f0f0f0;
        margin-bottom: 24px;
    }

    .card-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 2px solid var(--bg-light);
    }

    .form-group {
        margin-bottom: 24px;
    }

    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .form-input,
    .form-select {
        width: 100%;
        padding: 12px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
        transition: all 0.2s;
        color: var(--text-dark);
    }

    .form-input:focus,
    .form-select:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        background: white;
    }

    .form-input:disabled {
        background: var(--bg-light);
        color: var(--text-muted);
        cursor: not-allowed;
    }

    .form-helper {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 6px;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    .alert-error {
        background: #fee2e2;
        border: 1px solid #fecaca;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 24px;
        color: #7f1d1d;
    }

    .alert-error div {
        margin-bottom: 6px;
        font-size: 14px;
    }

    .submit-button {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 12px 32px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        displays: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        font-size: 14px;
    }

    .submit-button:hover {
        background: #1d4ed8;
        box-shadow: var(--card-shadow-lg);
        transform: translateY(-2px);
    }

    .info-card {
        background: var(--bg-light);
        border-radius: 10px;
        padding: 20px;
        border: 1px solid #e5e7eb;
    }

    .info-card-title {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .info-card-value {
        font-size: 16px;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 16px;
    }

    .info-card-helper {
        font-size: 12px;
        color: var(--text-muted);
    }
</style>

<h1 class="page-title">My Profile</h1>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; max-width: 1100px;">
    <!-- Personal Information -->
    <div>
        <div class="form-card">
            <h2 class="card-title">Personal Information</h2>

            @if($errors->any())
                <div class="alert-error">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('driver.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">Profile Image</label>
                    <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 10px;">
                        @if(auth()->user()->profile_image)
                            <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt="Profile Image" style="width: 72px; height: 72px; border-radius: 50%; object-fit: cover; border: 2px solid #e5e7eb;">
                        @else
                            <div style="width: 72px; height: 72px; border-radius: 50%; border: 2px solid #e5e7eb; display: flex; align-items: center; justify-content: center; color: #9ca3af; background: #f9fafb;">
                                <i class="bi bi-person" style="font-size: 28px;"></i>
                            </div>
                        @endif
                        <input type="file" name="profile_image" class="form-input" accept="image/png,image/jpeg,image/webp">
                    </div>
                    <p class="form-helper">Accepted: JPG, PNG, WEBP. Max size: 2MB.</p>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-input" value="{{ auth()->user()->name }}" required>
                    </div>
                    <div class="form-group">
                <div class="form-group">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="form-input" value="{{ old('date_of_birth', optional($driver->date_of_birth)->format('Y-m-d')) }}" required>
                    <p class="form-helper">Used to calculate your age in admin reports.</p>
                </div>
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="phone_number" class="form-input" 
                               value="{{ auth()->user()->phone_number }}" 
                               pattern="{{ \App\Models\Setting::getPhoneNumberPattern() }}" maxlength="{{ \App\Models\Setting::getPhoneNumberDigits() }}" inputmode="numeric" title="{{ \App\Models\Setting::getPhoneNumberHint() }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-input" value="{{ auth()->user()->email }}" disabled>
                    <p class="form-helper">Email cannot be changed</p>
                </div>

                <div class="form-group">
                    <label class="form-label">Vehicle Type</label>
                    <select name="vehicle_type" class="form-select" required>
                        <option value="Sedan" {{ $driver->vehicle_type === 'Sedan' ? 'selected' : '' }}>Sedan</option>
                        <option value="SUV" {{ $driver->vehicle_type === 'SUV' ? 'selected' : '' }}>SUV</option>
                        <option value="Van" {{ $driver->vehicle_type === 'Van' ? 'selected' : '' }}>Van</option>
                        <option value="Mini Van" {{ $driver->vehicle_type === 'Mini Van' ? 'selected' : '' }}>Mini Van</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Fuel Type</label>
                    <select name="fuel_type" class="form-select" required>
                        <option value="Fuel" {{ $driver->fuel_type === 'Fuel' ? 'selected' : '' }}>Fuel Car (Petrol/Diesel)</option>
                        <option value="Electric" {{ $driver->fuel_type === 'Electric' ? 'selected' : '' }}>Electric Vehicle</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Years of Experience</label>
                    <input type="number" name="years_of_experience" class="form-input" min="0" max="70" step="1"
                           value="{{ old('years_of_experience', $driver->years_of_experience) }}">
                    <p class="form-helper">Optional. Customers will only see this if you choose to show it publicly.</p>
                </div>

                <div class="form-group">
                    <label class="form-label">Age Range (public)</label>
                    <input type="text" name="public_age_range" class="form-input" maxlength="30"
                           placeholder="e.g. 25-30"
                           value="{{ old('public_age_range', $driver->public_age_range) }}">
                    <p class="form-helper">Optional. Enter an age range you want customers to see.</p>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="showExperiencePublic" name="show_experience_to_public" {{ old('show_experience_to_public', $driver->show_experience_to_public) ? 'checked' : '' }}>
                        <label class="form-check-label" for="showExperiencePublic">
                            Show years of experience to customers
                        </label>
                    </div>
                    <div class="form-check" style="margin-top: 10px;">
                        <input class="form-check-input" type="checkbox" value="1" id="showAgeRangePublic" name="show_age_range_to_public" {{ old('show_age_range_to_public', $driver->show_age_range_to_public) ? 'checked' : '' }}>
                        <label class="form-check-label" for="showAgeRangePublic">
                            Show age range to customers
                        </label>
                    </div>
                    <p class="form-helper">Use these settings to keep your driver profile details private or share them publicly.</p>
                </div>

                <button type="submit" class="submit-button">
                    <i class="bi bi-check-circle"></i>Update Profile
                </button>
            </form>
        </div>
    </div>

    <!-- Vehicle Details -->
    <div>
        <div class="form-card">
            <h2 class="card-title">Vehicle Details</h2>

            <div class="info-card">
                <div class="info-card-title">License Number</div>
                <div class="info-card-value">{{ $driver->license_number }}</div>
            </div>

            <div class="info-card" style="margin-top: 16px;">
                <div class="info-card-title">Plate Number</div>
                <div class="info-card-value">{{ $driver->taxi_plate_number }}</div>
            </div>

            <div class="info-card" style="margin-top: 16px;">
                <div class="info-card-title">Date of Birth</div>
                <div class="info-card-value">{{ optional($driver->date_of_birth)->format('d M Y') ?? 'N/A' }}</div>
            </div>

            <div class="info-card" style="margin-top: 16px;">
                <div class="info-card-title">Age</div>
                <div class="info-card-value">{{ $driver->age ?? 'N/A' }} years</div>
            </div>

            <div class="info-card" style="margin-top: 16px;">
                <div class="info-card-title">Age Range</div>
                <div class="info-card-value">{{ $driver->age_range ?? 'N/A' }}</div>
            </div>

            <div class="info-card" style="margin-top: 16px;">
                <div class="info-card-title">Experience</div>
                <div class="info-card-value">{{ $driver->years_of_experience !== null ? $driver->years_of_experience . ' years' : 'N/A' }}</div>
            </div>

            <p class="info-card-helper" style="margin-top: 20px;">
                <i class="bi bi-info-circle me-2"></i>
                These details cannot be changed. Contact admin if needed.
            </p>
        </div>
    </div>

    <!-- Account Status -->
    <div class="form-card">
        <h2 class="card-title">Account Status</h2>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="info-card">
                <div class="info-card-title">Verification Status</div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    @if($driver->verified)
                        <span style="padding: 6px 12px; background: #d1fae5; color: #065f46; border-radius: 6px; font-size: 13px; font-weight: 600;">Verified</span>
                    @else
                        <span style="padding: 6px 12px; background: #fef3c7; color: #92400e; border-radius: 6px; font-size: 13px; font-weight: 600;">Pending</span>
                    @endif
                </div>
            </div>
            
            <div class="info-card">
                <div class="info-card-title">Account Status</div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    @if($driver->active)
                        <span style="padding: 6px 12px; background: #d1fae5; color: #065f46; border-radius: 6px; font-size: 13px; font-weight: 600;">Active</span>
                    @else
                        <span style="padding: 6px 12px; background: #fee2e2; color: #7f1d1d; border-radius: 6px; font-size: 13px; font-weight: 600;">Inactive</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
