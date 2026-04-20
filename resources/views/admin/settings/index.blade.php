@extends('layouts.admin')

@section('title', 'System Settings')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-gear me-2"></i>System Settings</h4>
</div>

<form action="{{ route('admin.settings.update') }}" method="POST">
    @csrf
    
    <!-- Financial Settings -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-currency-exchange me-2"></i>Financial Settings</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Service Charge Percentage (%)</label>
                    <div class="input-group">
                        <input type="number" step="0.01" min="0" max="100" 
                               class="form-control @error('service_charge_percentage') is-invalid @enderror" 
                               name="service_charge_percentage" 
                               value="{{ old('service_charge_percentage', $settings['service_charge_percentage']) }}">
                        <span class="input-group-text">%</span>
                    </div>
                    <small class="text-muted">This percentage will be deducted from driver earnings as platform fee.</small>
                    @error('service_charge_percentage')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Settings -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Booking Settings</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Minimum Booking Hours Before Departure</label>
                    <div class="input-group">
                        <input type="number" min="0" max="48" 
                               class="form-control @error('min_booking_hours') is-invalid @enderror" 
                               name="min_booking_hours" 
                               value="{{ old('min_booking_hours', $settings['min_booking_hours']) }}">
                        <span class="input-group-text">hours</span>
                    </div>
                    <small class="text-muted">Passengers cannot book trips departing within this time.</small>
                    @error('min_booking_hours')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Maximum Seats Per Booking</label>
                    <input type="number" min="1" max="12" 
                           class="form-control @error('max_seats_per_booking') is-invalid @enderror" 
                           name="max_seats_per_booking" 
                           value="{{ old('max_seats_per_booking', $settings['max_seats_per_booking']) }}">
                    <small class="text-muted">Maximum number of seats a passenger can book at once.</small>
                    @error('max_seats_per_booking')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Payment Countdown Timer</label>
                    <div class="input-group">
                        <input type="number" min="1" max="15" 
                               class="form-control @error('payment_timeout_minutes') is-invalid @enderror" 
                               name="payment_timeout_minutes" 
                               value="{{ old('payment_timeout_minutes', intval($settings['payment_timeout_seconds'] / 60)) }}">
                        <span class="input-group-text">minutes</span>
                    </div>
                    <small class="text-muted">Countdown timer on payment confirmation page. If not paid, seats go to next person.</small>
                    @error('payment_timeout_minutes')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Payout Settings -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-wallet2 me-2"></i>Payout Settings</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Driver Payout Time</label>
                    <select class="form-select @error('driver_payout_time') is-invalid @enderror" name="driver_payout_time">
                        <option value="immediate" {{ old('driver_payout_time', $settings['driver_payout_time']) == 'immediate' ? 'selected' : '' }}>Immediate</option>
                        <option value="24" {{ old('driver_payout_time', $settings['driver_payout_time']) == '24' ? 'selected' : '' }}>24 Hours</option>
                        <option value="48" {{ old('driver_payout_time', $settings['driver_payout_time']) == '48' ? 'selected' : '' }}>48 Hours</option>
                        <option value="72" {{ old('driver_payout_time', $settings['driver_payout_time']) == '72' ? 'selected' : '' }}>72 Hours</option>
                    </select>
                    <small class="text-muted">Select 'Immediate' to pay drivers instantly, or choose hours for scheduled payout.</small>
                    @error('driver_payout_time')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Site Settings moved below Payout Settings -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-globe me-2"></i>Site Settings</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Site Name</label>
                    <input type="text" maxlength="100" 
                           class="form-control @error('site_name') is-invalid @enderror" 
                           name="site_name" 
                           value="{{ old('site_name', $settings['site_name']) }}">
                    @error('site_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Contact Email</label>
                    <input type="email" maxlength="255" 
                           class="form-control @error('contact_email') is-invalid @enderror" 
                           name="contact_email" 
                           value="{{ old('contact_email', $settings['contact_email']) }}">
                    @error('contact_email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Contact Phone</label>
                    <input type="text" maxlength="20" 
                           class="form-control @error('contact_phone') is-invalid @enderror" 
                           name="contact_phone" 
                           value="{{ old('contact_phone', $settings['contact_phone']) }}">
                    @error('contact_phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Current Service Charge Info -->
    <div class="card mb-4 border-info">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <i class="bi bi-info-circle text-info me-3" style="font-size: 2rem;"></i>
                <div>
                    <h6 class="mb-1">Current Service Charge: {{ $settings['service_charge_percentage'] }}%</h6>
                    <p class="mb-0 text-muted">
                        For a Nu. 1000 fare, the platform will charge Nu. {{ number_format(1000 * $settings['service_charge_percentage'] / 100) }} 
                        and the driver will receive Nu. {{ number_format(1000 - (1000 * $settings['service_charge_percentage'] / 100)) }}.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="bi bi-check-lg me-2"></i>Save Settings
        </button>
    </div>
</form>
@endsection
