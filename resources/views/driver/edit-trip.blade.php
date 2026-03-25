@extends('layouts.driver')

@section('title', 'Edit Trip')

@section('content')
<div class="mb-4">
    <a href="{{ route('driver.trips') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-2"></i>Back to Trips
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit Trip</h5>
    </div>
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('driver.trips.update', $trip->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Origin (From)</label>
                    <input type="text" name="origin_dzongkhag" id="trip-origin" class="form-control" 
                           placeholder="Type origin dzongkhag..."
                           data-dzongkhag-autocomplete
                           data-exclude-input="#trip-destination"
                           data-next-input="#trip-destination"
                           value="{{ old('origin_dzongkhag', $trip->origin_dzongkhag) }}"
                           required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Destination (To)</label>
                    <input type="text" name="destination_dzongkhag" id="trip-destination" class="form-control" 
                           placeholder="Type destination dzongkhag..."
                           data-dzongkhag-autocomplete
                           data-exclude-input="#trip-origin"
                           value="{{ old('destination_dzongkhag', $trip->destination_dzongkhag) }}"
                           required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold mb-3">Departure Date & Time</label>
                <div class="datetime-picker-wrapper">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="datetime-card">
                                <div class="datetime-card-icon">
                                    <i class="bi bi-calendar3"></i>
                                </div>
                                <div class="datetime-card-content">
                                    <label class="datetime-label">Departure Date</label>
                                    <input type="date" name="departure_date" id="departure-date" class="datetime-input" 
                                           value="{{ old('departure_date', $trip->departure_datetime->format('Y-m-d')) }}" required>
                                    <span class="datetime-preview" id="date-preview"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="datetime-card">
                                <div class="datetime-card-icon">
                                    <i class="bi bi-clock"></i>
                                </div>
                                <div class="datetime-card-content">
                                    <label class="datetime-label">Departure Time</label>
                                    <input type="time" name="departure_time" id="departure-time" class="datetime-input" 
                                           value="{{ old('departure_time', $trip->departure_datetime->format('H:i')) }}" required>
                                    <span class="datetime-preview" id="time-preview"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="datetime-confirm-section">
                        <div class="datetime-summary">
                            <span class="summary-label">Selected:</span>
                            <span class="summary-value" id="datetime-summary">{{ $trip->departure_datetime->format('M d, Y - H:i') }}</span>
                        </div>
                        <input type="hidden" name="departure_datetime" id="departure-datetime-hidden" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Total Seats</label>
                    <input type="number" name="total_seats" class="form-control" 
                           value="{{ $trip->total_seats }}" min="{{ $trip->total_seats - $trip->available_seats }}" max="12" required>
                    <small class="text-muted">Min: {{ $trip->total_seats - $trip->available_seats }} (already booked)</small>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Price per Seat (Nu.)</label>
                    <input type="number" name="price_per_seat" class="form-control" 
                           value="{{ $trip->price_per_seat }}" min="0" step="0.01" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Full Taxi Price (Nu.)</label>
                    <input type="number" name="full_taxi_price" id="fullTaxiPrice" class="form-control" 
                           value="{{ $trip->full_taxi_price }}" min="0" step="0.01" required>
                    <small class="text-muted">Auto-calculated (seats × price)</small>
                </div>
            </div>

            <hr>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-2"></i>Update Trip
                </button>
                <a href="{{ route('driver.trips') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const totalSeats = document.querySelector('input[name="total_seats"]');
        const pricePerSeat = document.querySelector('input[name="price_per_seat"]');
        const fullTaxiPrice = document.getElementById('fullTaxiPrice');
        const departureDate = document.getElementById('departure-date');
        const departureTime = document.getElementById('departure-time');
        const departureDatetimeHidden = document.getElementById('departure-datetime-hidden');
        const datetimeSummary = document.getElementById('datetime-summary');
        
        // Get today's date and current time
        const today = new Date();
        const todayStr = today.toISOString().split('T')[0];
        const currentHour = today.getHours();
        const currentMinute = today.getMinutes();
        
        // Set minimum date to today
        departureDate.setAttribute('min', todayStr);
        
        // Format date display
        function formatDateDisplay(dateStr) {
            const options = { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' };
            return new Date(dateStr + 'T00:00').toLocaleDateString('en-US', options);
        }
        
        // Check if a date is today
        function isToday(dateStr) {
            return dateStr === todayStr;
        }
        
        // Update time input constraints based on selected date
        function updateTimeConstraints() {
            const selectedDate = departureDate.value;
            
            if (!selectedDate) {
                departureTime.setAttribute('disabled', 'disabled');
                return;
            }
            
            departureTime.removeAttribute('disabled');
            
            // If today is selected, set minimum time to current time
            if (isToday(selectedDate)) {
                const minTimeDate = new Date(today);
                const minTime = `${String(minTimeDate.getHours()).padStart(2, '0')}:${String(minTimeDate.getMinutes()).padStart(2, '0')}`;
                departureTime.setAttribute('min', minTime);
                
                // Show label for today's date
                const timeLabel = departureTime.parentElement.querySelector('.datetime-label');
                if (timeLabel) {
                    timeLabel.textContent = `Departure Time (From ${minTime})`;
                }
            } else {
                // For future dates, allow all times
                departureTime.removeAttribute('min');
                const timeLabel = departureTime.parentElement.querySelector('.datetime-label');
                if (timeLabel) {
                    timeLabel.textContent = 'Departure Time';
                }
            }
            
            // Clear time if it's no longer valid
            if (departureTime.value && departureTime.getAttribute('min')) {
                if (departureTime.value < departureTime.getAttribute('min')) {
                    departureTime.value = '';
                }
            }
        }
        
        // Update datetime hidden field and summary
        function updateDatetime() {
            if (departureDate.value && departureTime.value) {
                const datetime = `${departureDate.value}T${departureTime.value}`;
                departureDatetimeHidden.value = datetime;
                
                const dateDisplay = formatDateDisplay(departureDate.value);
                const timeDisplay = departureTime.value;
                datetimeSummary.textContent = `${dateDisplay}, ${timeDisplay}`;
            }
        }
        
        departureDate.addEventListener('change', function() {
            updateTimeConstraints();
            updateDatetime();
        });
        
        departureTime.addEventListener('change', updateDatetime);
        
        // Initialize on load
        updateTimeConstraints();
        updateDatetime();
        
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
