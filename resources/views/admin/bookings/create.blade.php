@extends('layouts.admin')

@section('title', 'Book on Behalf')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.bookings.search') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-2"></i>Back to Search
    </a>
</div>

<div class="row">
    <!-- Booking Form -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-ticket-perforated me-2"></i>Book on Behalf of Passenger</h5>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                    </div>
                @endif

                <form action="{{ route('admin.bookings.store') }}" method="POST" id="bookingForm">
                    @csrf
                    <input type="hidden" name="trip_id" value="{{ $trip->id }}">

                    <!-- Booking Type -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Booking Type</label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check card p-3">
                                    <input class="form-check-input" type="radio" name="booking_type" 
                                           id="sharedType" value="shared" checked>
                                    <label class="form-check-label w-100" for="sharedType">
                                        <div class="d-flex justify-content-between">
                                            <strong><i class="bi bi-people me-2"></i>Shared Seats</strong>
                                            <span class="text-success">Nu. {{ number_format($trip->price_per_seat) }}/seat</span>
                                        </div>
                                        <small class="text-muted">Book individual seats</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                @php
                                    $canBookFullTaxi = $trip->available_seats == $trip->total_seats;
                                    $bookedSeats = $trip->total_seats - $trip->available_seats;
                                @endphp
                                <div class="form-check card p-3 full-taxi-option {{ !$canBookFullTaxi ? 'unavailable' : '' }}" 
                                     style="{{ !$canBookFullTaxi ? 'cursor: pointer;' : '' }}"
                                     data-can-book="{{ $canBookFullTaxi ? 'true' : 'false' }}"
                                     data-booked-seats="{{ $bookedSeats }}"
                                     data-total-seats="{{ $trip->total_seats }}">
                                    <input class="form-check-input" type="radio" name="booking_type" 
                                           id="fullType" value="full">
                                    <label class="form-check-label w-100" for="fullType">
                                        <div class="d-flex justify-content-between">
                                            <strong><i class="bi bi-car-front me-2"></i>Full Taxi</strong>
                                            <span class="text-primary">Nu. {{ number_format($trip->full_taxi_price) }}</span>
                                        </div>
                                        <small class="text-muted">Book entire taxi ({{ $trip->total_seats }} seats)</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Number of Seats (for shared) -->
                    <div class="mb-4" id="seatsSection">
                        <label class="form-label fw-bold">Number of Seats</label>
                        <select name="seats_booked" id="seatsBooked" class="form-select">
                            @for($i = 1; $i <= $trip->available_seats; $i++)
                                <option value="{{ $i }}">{{ $i }} {{ $i == 1 ? 'seat' : 'seats' }}</option>
                            @endfor
                        </select>
                    </div>

                    <!-- Passenger Information -->
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h6 class="card-title"><i class="bi bi-people me-2"></i>Passenger Information</h6>
                            <p class="text-muted small mb-3">Enter name and phone number for each passenger.</p>
                            
                            <div id="passengersContainer">
                                <!-- Dynamic passenger fields will be added here -->
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        <i class="bi bi-check-circle me-2"></i>Complete Booking
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Trip Summary -->
    <div class="col-lg-4">
        <div class="card sticky-top" style="top: 20px;">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Trip Summary</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h5 class="mb-1">
                        {{ $trip->origin_dzongkhag }}
                        <i class="bi bi-arrow-right text-primary mx-2"></i>
                        {{ $trip->destination_dzongkhag }}
                    </h5>
                </div>

                <div class="border-top pt-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted"><i class="bi bi-calendar3 me-2"></i>Date</span>
                        <span>{{ $trip->departure_datetime->format('M d, Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted"><i class="bi bi-clock me-2"></i>Time</span>
                        <span>{{ $trip->departure_datetime->format('h:i A') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted"><i class="bi bi-person me-2"></i>Driver</span>
                        <span>{{ $trip->driver->user->name }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted"><i class="bi bi-car-front me-2"></i>Vehicle</span>
                        <span>{{ $trip->driver->vehicle_type }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted"><i class="bi bi-123 me-2"></i>Plate</span>
                        <span>{{ $trip->driver->taxi_plate_number }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted"><i class="bi bi-person-fill me-2"></i>Available</span>
                        <span class="badge bg-success">{{ $trip->available_seats }} seats</span>
                    </div>
                </div>

                <div class="border-top pt-3 mt-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Price per seat</span>
                        <span class="fw-bold">Nu. {{ number_format($trip->price_per_seat) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Full taxi price</span>
                        <span class="fw-bold">Nu. {{ number_format($trip->full_taxi_price) }}</span>
                    </div>
                </div>

                <div class="border-top pt-3 mt-3">
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">Total Amount</span>
                        <span class="fs-5 fw-bold text-primary" id="totalAmount">Nu. {{ number_format($trip->price_per_seat) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const seatsSelect = document.getElementById('seatsBooked');
    const seatsSection = document.getElementById('seatsSection');
    const passengersContainer = document.getElementById('passengersContainer');
    const bookingTypeRadios = document.querySelectorAll('input[name="booking_type"]');
    const totalAmountEl = document.getElementById('totalAmount');
    const pricePerSeat = {{ $trip->price_per_seat }};
    const fullTaxiPrice = {{ $trip->full_taxi_price }};
    const totalSeats = {{ $trip->total_seats }};

    // Handle click on unavailable full taxi option
    const fullTaxiOption = document.querySelector('.full-taxi-option');
    if (fullTaxiOption && fullTaxiOption.dataset.canBook === 'false') {
        fullTaxiOption.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const bookedSeats = this.dataset.bookedSeats;
            const totalSeatsData = this.dataset.totalSeats;
            
            // Show compact alert modal
            const modalHtml = `
                <div class="modal fade" id="fullTaxiUnavailableModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered modal-sm">
                        <div class="modal-content">
                            <div class="modal-header py-2 bg-warning text-dark">
                                <h6 class="modal-title mb-0">
                                    <i class="bi bi-exclamation-triangle me-1"></i>Not Available
                                </h6>
                                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body py-3 text-center">
                                <p class="mb-2"><strong>${bookedSeats}/${totalSeatsData} seats</strong> already booked.</p>
                                <small class="text-muted">Choose a trip with all seats available for full taxi booking.</small>
                            </div>
                            <div class="modal-footer py-2">
                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">OK</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Remove existing modal if any
            const existingModal = document.getElementById('fullTaxiUnavailableModal');
            if (existingModal) existingModal.remove();
            
            // Add modal to body
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('fullTaxiUnavailableModal'));
            modal.show();
        });
        
        // Prevent radio from being selected
        document.getElementById('fullType').addEventListener('click', function(e) {
            e.preventDefault();
        });
    }

    function updatePassengerFields() {
        const isFullTaxi = document.getElementById('fullType').checked;
        // For full taxi, only need 1 contact person
        const seatCount = isFullTaxi ? 1 : parseInt(seatsSelect.value) || 1;
        
        passengersContainer.innerHTML = '';
        
        for (let i = 0; i < seatCount; i++) {
            const div = document.createElement('div');
            div.className = 'border rounded p-3 mb-3 bg-white';
            const label = isFullTaxi ? 'Contact Person (Full Taxi Booking)' : `Passenger ${i + 1}`;
            div.innerHTML = `
                <h6 class="text-primary mb-3"><i class="bi bi-person me-1"></i>${label}</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="passengers[${i}][name]" 
                               placeholder="Enter passenger name" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" name="passengers[${i}][phone]" 
                               placeholder="Enter phone number" pattern="[0-9]+" inputmode="numeric" required>
                    </div>
                </div>
            `;
            passengersContainer.appendChild(div);
        }

        // Update total amount
        if (isFullTaxi) {
            totalAmountEl.textContent = 'Nu. ' + fullTaxiPrice.toLocaleString();
        } else {
            totalAmountEl.textContent = 'Nu. ' + (pricePerSeat * seatCount).toLocaleString();
        }
        
        // Reinitialize input validation for new fields
        if (typeof window.reinitInputValidation === 'function') {
            window.reinitInputValidation();
        }
    }

    function handleBookingTypeChange() {
        const isFullTaxi = document.getElementById('fullType').checked;
        seatsSection.style.display = isFullTaxi ? 'none' : 'block';
        updatePassengerFields();
    }

    seatsSelect.addEventListener('change', updatePassengerFields);
    bookingTypeRadios.forEach(radio => {
        radio.addEventListener('change', handleBookingTypeChange);
    });

    // Initialize
    updatePassengerFields();
});
</script>
@endpush
