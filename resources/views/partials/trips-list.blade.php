@if($trips->count() > 0)
    <div class="row g-3 g-lg-4">
        @foreach($trips as $trip)
            <div class="col-6 col-sm-6 col-md-4 col-lg-3">
                <div class="card trip-card h-100">
                    <div class="card-body p-3">

                        {{-- Route Header with Available Seats --}}
                        <div class="mb-3">
                            <h5 class="route-title mb-1">
                                {{ $trip->origin_dzongkhag }}
                                <i class="bi bi-arrow-right text-primary"></i>
                                {{ $trip->destination_dzongkhag }}
                            </h5>
                            <span class="badge bg-success">
                                <i class="bi bi-person-fill"></i> {{ $trip->available_seats }} Available
                            </span>
                        </div>

                        {{-- Driver Info with Rating --}}
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-person-circle text-primary" style="font-size: 1.2rem;"></i>
                                <strong>{{ $trip->driver->user->name }}</strong>
                            </div>
                            @if(($trip->driver->average_rating ?? 0) > 0)
                                <div class="d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                                    @for($i = 0; $i < 5; $i++)
                                        @if($i < floor($trip->driver->average_rating))
                                            <i class="bi bi-star-fill text-warning"></i>
                                        @elseif($i - floor($trip->driver->average_rating) < 0.5)
                                            <i class="bi bi-star-half text-warning"></i>
                                        @else
                                            <i class="bi bi-star text-warning"></i>
                                        @endif
                                    @endfor
                                    <span class="text-warning fw-bold">({{ number_format($trip->driver->average_rating, 1) }})</span>
                                    <span class="text-muted">{{ $trip->driver->rating_count ?? 0 }} ratings</span>
                                </div>
                            @else
                                <div style="font-size: 0.85rem;" class="text-muted">
                                    <i class="bi bi-star text-warning"></i> No ratings yet
                                </div>
                            @endif
                        </div>

                        {{-- Trip Details Grid --}}
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="trip-info-item small">
                                    <i class="bi bi-calendar3 text-primary"></i>
                                    <span class="d-block text-muted">{{ $trip->departure_datetime->format('M d') }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="trip-info-item small">
                                    <i class="bi bi-clock text-primary"></i>
                                    <span class="d-block text-muted">{{ $trip->departure_datetime->format('h:i A') }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="trip-info-item small">
                                    <i class="bi bi-car-front text-primary"></i>
                                    <span class="d-block text-muted">{{ $trip->driver->vehicle_type }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="trip-info-item small">
                                    @if($trip->driver->fuel_type === 'Electric')
                                        <i class="bi bi-lightning-charge text-info"></i>
                                        <span class="d-block text-muted">Electric</span>
                                    @else
                                        <i class="bi bi-fuel-pump text-warning"></i>
                                        <span class="d-block text-muted">Fuel</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Price & Actions --}}
                        <div class="d-flex justify-content-between align-items-end pt-2 border-top">
                            <div>
                                <div class="price-tag fw-bold">Nu. {{ number_format($trip->price_per_seat) }}</div>
                                <small class="text-muted">/seat</small>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('trip.details', $trip->id) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @auth
                                    <a href="{{ route('booking.create', $trip->id) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-ticket-perforated me-1"></i>Book
                                    </a>
                                @else
                                    <a href="{{ route('login') }}?redirect={{ route('booking.create', $trip->id) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-box-arrow-in-right me-1"></i>Book
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
