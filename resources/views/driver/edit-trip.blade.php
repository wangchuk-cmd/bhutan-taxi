@extends('layouts.driver')

@section('title', 'Edit Trip')

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

    .page-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 24px;
    }

    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--text-muted);
        text-decoration: none;
        font-size: 14px;
        padding: 8px 12px;
        border-radius: 6px;
        transition: all 0.2s;
        border: 1px solid #e5e7eb;
    }

    .back-btn:hover {
        background: var(--bg-light);
        color: var(--text-dark);
    }

    .page-title {
        font-size: 28px;
        font-weight: 600;
        color: var(--text-dark);
        margin: 0;
    }

    .form-card {
        background: white;
        border-radius: 12px;
        padding: 32px;
        box-shadow: var(--card-shadow);
        border: 1px solid #f0f0f0;
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

    .route-preview-card {
        margin-top: 18px;
        border: 1px solid #dbe4ff;
        border-radius: 14px;
        padding: 16px;
        background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
    }

    .route-preview-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }

    .route-preview-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
    }

    .route-preview-item {
        background: #fff;
        border: 1px solid #edf2ff;
        border-radius: 12px;
        padding: 12px;
        min-height: 72px;
    }

    .route-preview-label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 6px;
    }

    .route-preview-value {
        font-size: 14px;
        font-weight: 700;
        color: #111827;
        line-height: 1.35;
    }

    .route-preview-empty {
        font-size: 13px;
        color: #6b7280;
        font-style: italic;
    }

    @media (max-width: 767.98px) {
        .route-preview-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
</style>

<div class="page-header">
    <a href="{{ route('driver.trips') }}" class="back-btn">
        <i class="bi bi-arrow-left"></i>Back to Trips
    </a>
</div>

<div class="form-card">
    <h1 class="page-title" style="margin-bottom: 32px;">
        <i class="bi bi-pencil-square" style="font-size: 28px; color: var(--primary-color); vertical-align: middle;"></i>
        Edit Trip
    </h1>

    @if($errors->any())
        <div class="alert-error">
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

            <div class="route-preview-card" id="route-preview-card">
                <div class="route-preview-header">
                    <div>
                        <span class="badge bg-primary-subtle text-primary">Admin Route Preview</span>
                        <div class="route-preview-empty mt-2" id="route-preview-hint">Route details are loaded from the admin-defined route table.</div>
                    </div>
                </div>

                <div class="route-preview-grid">
                    <div class="route-preview-item">
                        <span class="route-preview-label">Destination</span>
                        <div class="route-preview-value" id="route-preview-destination">{{ $trip->origin_dzongkhag }} → {{ $trip->destination_dzongkhag }}</div>
                    </div>
                    <div class="route-preview-item">
                        <span class="route-preview-label">Distance</span>
                        <div class="route-preview-value" id="route-preview-distance">{{ $trip->route?->distance_km ? $trip->route->formatted_distance_km : '-' }}</div>
                    </div>
                    <div class="route-preview-item">
                        <span class="route-preview-label">Estimated Time</span>
                        <div class="route-preview-value" id="route-preview-duration">{{ $trip->route?->estimated_time ? $trip->route->formatted_estimated_time : '-' }}</div>
                    </div>
                    <div class="route-preview-item">
                        <span class="route-preview-label">Estimated Arrival</span>
                        <div class="route-preview-value" id="route-preview-arrival">{{ $trip->estimated_arrival_at ? $trip->estimated_arrival_at->format('D, M d, Y h:i A') : '-' }}</div>
                    </div>
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

            <div style="display: flex; gap: 12px; margin-top: 32px; border-top: 1px solid #e5e7eb; padding-top: 24px;">
                <button type="submit" style="
                    background: var(--primary-color);
                    color: white;
                    border: none;
                    padding: 10px 24px;
                    border-radius: 8px;
                    font-weight: 500;
                    font-size: 14px;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    transition: all 0.2s;
                " onmouseover="this.style.background='#1d4ed8'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(37, 99, 235, 0.3)';" 
                   onmouseout="this.style.background='var(--primary-color)'; this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                    <i class="bi bi-check-circle"></i>Update Trip
                </button>
                <a href="{{ route('driver.trips') }}" style="
                    color: var(--text-muted);
                    text-decoration: none;
                    padding: 10px 24px;
                    border-radius: 8px;
                    font-weight: 500;
                    font-size: 14px;
                    border: 1px solid #e5e7eb;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    transition: all 0.2s;
                    background: white;
                " onmouseover="this.style.background='var(--bg-light)'; this.style.color='var(--text-dark)';" 
                   onmouseout="this.style.background='white'; this.style.color='var(--text-muted)';">Cancel</a>
            </div>
        </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const routeData = @json($routeData);

        const originInput = document.getElementById('trip-origin');
        const destinationInput = document.getElementById('trip-destination');
        const routePreviewCard = document.getElementById('route-preview-card');
        const routePreviewHint = document.getElementById('route-preview-hint');
        const routePreviewDestination = document.getElementById('route-preview-destination');
        const routePreviewDistance = document.getElementById('route-preview-distance');
        const routePreviewDuration = document.getElementById('route-preview-duration');
        const routePreviewArrival = document.getElementById('route-preview-arrival');
        const totalSeats = document.querySelector('input[name="total_seats"]');
        const pricePerSeat = document.querySelector('input[name="price_per_seat"]');
        const fullTaxiPrice = document.getElementById('fullTaxiPrice');
        const departureDate = document.getElementById('departure-date');
        const departureTime = document.getElementById('departure-time');
        const departureDatetimeHidden = document.getElementById('departure-datetime-hidden');
        const datetimeSummary = document.getElementById('datetime-summary');

        function normalizeText(value) {
            return (value || '').trim().toLowerCase();
        }

        function getRoutesByOrigin(origin) {
            return routeData.filter((route) => normalizeText(route.origin) === normalizeText(origin));
        }

        function findRoute(origin, destination) {
            return routeData.find((route) => {
                return normalizeText(route.origin) === normalizeText(origin)
                    && normalizeText(route.destination) === normalizeText(destination);
            });
        }

        function durationToMinutes(duration) {
            if (!duration) return 0;

            const hhmmMatch = String(duration).match(/(\d{1,2})\s*:\s*(\d{1,2})/);
            if (hhmmMatch) {
                return (parseInt(hhmmMatch[1], 10) * 60) + parseInt(hhmmMatch[2], 10);
            }

            const hourMatch = String(duration).match(/(\d+)\s*(h|hr|hrs|hour|hours)/i);
            const minuteMatch = String(duration).match(/(\d+)\s*(m|min|mins|minute|minutes)/i);
            if (hourMatch || minuteMatch) {
                const hours = hourMatch ? parseInt(hourMatch[1], 10) : 0;
                const minutes = minuteMatch ? parseInt(minuteMatch[1], 10) : 0;
                return (hours * 60) + minutes;
            }

            const numbers = String(duration).match(/\d+/g);
            if (!numbers || numbers.length === 0) return 0;
            if (numbers.length >= 2) {
                return (parseInt(numbers[0], 10) * 60) + parseInt(numbers[1], 10);
            }

            return parseInt(numbers[0], 10) || 0;
        }

        function formatDuration(duration) {
            const minutes = durationToMinutes(duration);
            if (!minutes) return '-';

            const hours = Math.floor(minutes / 60);
            const remainingMinutes = minutes % 60;
            const hourLabel = hours ? `${hours} hr${hours > 1 ? 's' : ''}` : '';
            const minuteLabel = remainingMinutes ? `${remainingMinutes} min${remainingMinutes > 1 ? 's' : ''}` : '';
            return [hourLabel, minuteLabel].filter(Boolean).join(' ');
        }

        function formatArrival(dateStr, timeStr, duration) {
            if (!dateStr || !timeStr || !duration) return '-';

            const arrival = new Date(`${dateStr}T${timeStr}`);
            if (Number.isNaN(arrival.getTime())) return '-';

            arrival.setMinutes(arrival.getMinutes() + durationToMinutes(duration));
            return arrival.toLocaleString([], {
                weekday: 'short',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function autoFillDestination() {
            const originMatches = getRoutesByOrigin(originInput.value);
            if (destinationInput.value) return;
            if (originMatches.length === 1) {
                destinationInput.value = originMatches[0].destination;
            }
        }

        function updateRoutePreview() {
            autoFillDestination();

            const selectedRoute = findRoute(originInput.value, destinationInput.value);
            if (!selectedRoute) {
                routePreviewHint.textContent = 'Choose an admin-defined route to calculate km and estimated travel time.';
                routePreviewDestination.textContent = '-';
                routePreviewDistance.textContent = '-';
                routePreviewDuration.textContent = '-';
                routePreviewArrival.textContent = '-';
                return;
            }

            routePreviewHint.textContent = 'This route comes from admin settings and is used to calculate distance and travel time.';
            routePreviewDestination.textContent = `${selectedRoute.origin} → ${selectedRoute.destination}`;
            routePreviewDistance.textContent = `${selectedRoute.distance_km} km`;
            routePreviewDuration.textContent = formatDuration(selectedRoute.estimated_time);
            routePreviewArrival.textContent = formatArrival(departureDate.value, departureTime.value, selectedRoute.estimated_time);
        }
        
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

            updateRoutePreview();
        }

        originInput.addEventListener('change', updateRoutePreview);
        originInput.addEventListener('blur', updateRoutePreview);
        destinationInput.addEventListener('change', updateRoutePreview);
        destinationInput.addEventListener('blur', updateRoutePreview);
        
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
