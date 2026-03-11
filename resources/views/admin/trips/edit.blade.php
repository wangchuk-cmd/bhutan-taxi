@extends('layouts.admin')

@section('title', 'Edit Trip')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.trips') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-2"></i>Back to Trips
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit Trip #{{ $trip->id }}</h5>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                    </div>
                @endif

                @php
                    $bookedSeats = $trip->total_seats - $trip->available_seats;
                @endphp

                @if($bookedSeats > 0)
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        This trip has <strong>{{ $bookedSeats }} booked seat(s)</strong>. 
                        Total seats cannot be reduced below {{ $bookedSeats }}.
                    </div>
                @endif

                <form action="{{ route('admin.trips.update', $trip->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-bold">Driver <span class="text-danger">*</span></label>
                        <select name="driver_id" class="form-select" required>
                            <option value="">Select Driver</option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}" {{ old('driver_id', $trip->driver_id) == $driver->id ? 'selected' : '' }}>
                                    {{ $driver->user->name }} - {{ $driver->taxi_plate_number }} ({{ $driver->vehicle_type }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Origin <span class="text-danger">*</span></label>
                            <input type="text" name="origin_dzongkhag" id="admin-edit-trip-origin" class="form-control" 
                                   placeholder="Type origin dzongkhag..."
                                   data-dzongkhag-autocomplete
                                   data-exclude-input="#admin-edit-trip-destination"
                                   data-next-input="#admin-edit-trip-destination"
                                   value="{{ old('origin_dzongkhag', $trip->origin_dzongkhag) }}"
                                   required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Destination <span class="text-danger">*</span></label>
                            <input type="text" name="destination_dzongkhag" id="admin-edit-trip-destination" class="form-control" 
                                   placeholder="Type destination dzongkhag..."
                                   data-dzongkhag-autocomplete
                                   data-exclude-input="#admin-edit-trip-origin"
                                   value="{{ old('destination_dzongkhag', $trip->destination_dzongkhag) }}"
                                   required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Departure Date & Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="departure_datetime" class="form-control" 
                               value="{{ old('departure_datetime', $trip->departure_datetime->format('Y-m-d\TH:i')) }}" 
                               min="{{ date('Y-m-d\TH:i') }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Total Seats <span class="text-danger">*</span></label>
                            <input type="number" name="total_seats" class="form-control" 
                                   value="{{ old('total_seats', $trip->total_seats) }}" 
                                   min="{{ $bookedSeats }}" max="12" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Price Per Seat (Nu.) <span class="text-danger">*</span></label>
                            <input type="number" name="price_per_seat" class="form-control" 
                                   value="{{ old('price_per_seat', $trip->price_per_seat) }}" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Full Taxi Price (Nu.) <span class="text-danger">*</span></label>
                            <input type="number" name="full_taxi_price" id="fullTaxiPrice" class="form-control" 
                                   value="{{ old('full_taxi_price', $trip->full_taxi_price) }}" min="0" step="0.01" required>
                            <small class="text-muted">Auto-calculated (seats × price)</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="active" {{ old('status', $trip->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="completed" {{ old('status', $trip->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status', $trip->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Update Trip
                        </button>
                        <a href="{{ route('admin.trips') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const totalSeats = document.querySelector('input[name="total_seats"]');
        const pricePerSeat = document.querySelector('input[name="price_per_seat"]');
        const fullTaxiPrice = document.getElementById('fullTaxiPrice');
        
        function calculateFullTaxiPrice() {
            const seats = parseFloat(totalSeats.value) || 0;
            const price = parseFloat(pricePerSeat.value) || 0;
            fullTaxiPrice.value = (seats * price).toFixed(2);
        }
        
        totalSeats.addEventListener('input', calculateFullTaxiPrice);
        pricePerSeat.addEventListener('input', calculateFullTaxiPrice);
    });
</script>
@endpush
@endsection
