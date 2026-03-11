@if($trips->count() > 0)
    <div class="row g-2 g-sm-3">
        @foreach($trips as $trip)
            <div class="col-6 col-lg-6">
                <div class="card trip-card h-100">
                    <div class="card-body p-3">

                        {{-- Route Header --}}
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="route-title mb-0 me-2 flex-grow-1">
                                {{ $trip->origin_dzongkhag }}
                                <i class="bi bi-arrow-right text-primary"></i>
                                {{ $trip->destination_dzongkhag }}
                            </h5>
                            <span class="badge bg-success text-nowrap flex-shrink-0">
                                <i class="bi bi-person-fill"></i> {{ $trip->available_seats }} Available
                            </span>
                        </div>

                        {{-- Trip Info Grid --}}
                        <div class="trip-info-grid mb-3">
                            <div class="trip-info-item">
                                <i class="bi bi-calendar3 text-primary"></i>
                                <span>{{ $trip->departure_datetime->format('M d, Y') }}</span>
                            </div>
                            <div class="trip-info-item">
                                <i class="bi bi-clock text-primary"></i>
                                <span>{{ $trip->departure_datetime->format('h:i A') }}</span>
                            </div>
                            <div class="trip-info-item">
                                <i class="bi bi-person text-primary"></i>
                                <span>{{ $trip->driver->user->name }}</span>
                            </div>
                            <div class="trip-info-item">
                                <i class="bi bi-car-front text-primary"></i>
                                <span>{{ $trip->driver->vehicle_type }} · {{ $trip->driver->taxi_plate_number }}</span>
                            </div>
                        </div>

                        {{-- Price & Actions --}}
                        <div class="d-flex justify-content-between align-items-end pt-2 border-top">
                            <div>
                                <div class="price-tag">Nu. {{ number_format($trip->price_per_seat) }}<small class="text-muted fw-normal"> /seat</small></div>
                                <small class="text-muted">Full taxi: Nu. {{ number_format($trip->full_taxi_price) }}</small>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('trip.details', $trip->id) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @auth
                                    <a href="{{ route('booking.create', $trip->id) }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-ticket-perforated me-1"></i>Book
                                    </a>
                                @else
                                    <a href="{{ route('login') }}?redirect={{ route('booking.create', $trip->id) }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-box-arrow-in-right me-1"></i>Login
                                    </a>
                                @endauth
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="text-center py-5">
        <i class="bi bi-emoji-frown display-1 text-muted"></i>
        <h4 class="mt-3">No trips found</h4>
        <p class="text-muted">No taxis available for this route and date.</p>
    </div>
@endif
