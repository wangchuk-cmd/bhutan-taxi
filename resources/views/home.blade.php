@extends('layouts.app')

@section('title', 'Home')

@section('content')

{{-- ════════════════════════════════════════
     HERO V4 (Screen-Fit & Mobile Perfect)
════════════════════════════════════════ --}}
<section class="hero-fit">
    <div class="hf-decor-circle"></div>

    <div class="container relative-content">
        <div class="row hf-content-row">

            {{-- Left Side: Text & Brand --}}
            <div class="col-lg-6 col-md-10 mx-auto">
                <div class="hero-live-badge mb-3">
                    <span class="live-dot-ring"></span>
                    <span class="live-dot"></span>
                    Operational in all 20 Dzongkhags, Bhutan
                </div>

                <h1 class="hf-title d-none d-md-block">
                    The Smartest Way<br>
                    to Book Taxis
                </h1>

                <p class="hf-sub mb-4 d-none d-md-block">
                    Instantly book shared or private intercity taxis.
                    Fast, secure, and reliable travel across Bhutan.
                </p>

                <div class="d-flex flex-wrap gap-2 mb-4 justify-content-lg-start justify-content-center">
                    <a href="#search-section" class="btn btn-primary rounded-pill px-4 shadow-sm">
                        Find Taxis
                    </a>
                    <a href="{{ route('driver.register') }}" class="btn btn-glass rounded-pill px-4">
                        Register as Driver
                    </a>
                </div>
            </div>

            {{-- Right Side: The Fit Search Card --}}
            <div class="col-lg-5 offset-lg-1" id="search-section">
                <div class="sc-fit">
                    <div class="scv3-header text-center mb-3">
                        <h2 class="h5 fw-bold mb-0">Book Your Ride</h2>
                        <span class="text-muted small">Select travel route</span>
                    </div>

                    <form action="{{ route('search') }}" method="GET" id="home-search-form">
                        <div class="v3-field-wrap mb-2">
                            <label class="v3-label">From</label>
                            <div class="v3-input-box">
                                <i class="bi bi-geo-alt v3-icon text-primary"></i>
                                <input type="text" name="from" id="search-from"
                                       class="form-control" placeholder="Origin..."
                                       data-dzongkhag-autocomplete
                                       data-exclude-input="#search-to"
                                       data-next-input="#search-to"
                                       value="{{ request('from') }}" required>
                            </div>
                        </div>

                        {{-- Swap --}}
                        <div class="v3-swap-container" style="height: 0; display: flex; justify-content: flex-end; margin-right: 1.5rem; z-index: 10;">
                            <button type="button" id="swapBtn" class="v3-swap-btn" style="width: 34px; height: 34px; border-radius: 50%; background: #2563eb; color: #fff; border: 3px solid #fff; transform: translateY(-50%);">
                                <i class="bi bi-arrow-down-up" style="font-size: 0.8rem;"></i>
                            </button>
                        </div>

                        <div class="v3-field-wrap mb-2">
                            <label class="v3-label">To</label>
                            <div class="v3-input-box">
                                <i class="bi bi-geo-fill v3-icon text-danger"></i>
                                <input type="text" name="to" id="search-to"
                                       class="form-control" placeholder="Destination..."
                                       data-dzongkhag-autocomplete
                                       data-exclude-input="#search-from"
                                       data-next-input="#home-search-date"
                                       value="{{ request('to') }}" required>
                            </div>
                        </div>

                        <div class="v3-field-wrap mb-3">
                            <label class="v3-label">Travel Date</label>
                            <div class="v3-input-box date-box position-relative">
                                <div class="date-icon-wrap">
                                    <i class="bi bi-calendar3"></i>
                                </div>
                                    <input type="date" name="date" id="home-search-date"
                                        class="form-control"
                                        value="{{ request('date', date('Y-m-d')) }}" required>
                                <div class="date-badge">Today</div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 btn-search shadow-sm">
                            <i class="bi bi-search me-2"></i>Search Taxis
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ════════════════════════════════════════
     FEATURED TRIPS (below search)
════════════════════════════════════════ --}}
@if($featuredTrips->count() > 0)
<section class="trips-section py-4">
    <div class="container">
        <div class="d-flex align-items-end justify-content-between mb-3">
            <div>
                <span class="section-eyebrow">Available Now</span>
                <h2 class="section-title mb-0">Upcoming Trips</h2>
            </div>
            <a href="{{ route('search') }}" class="btn btn-outline-primary btn-sm view-all-btn">
                View All <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="row g-3">
            @foreach($featuredTrips as $trip)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="trip-card-v2">
                    <div class="tcv2-header">
                        <div class="tcv2-route">
                            <span class="tcv2-city">{{ $trip->origin_dzongkhag }}</span>
                            <i class="bi bi-arrow-right tcv2-arrow"></i>
                            <span class="tcv2-city">{{ $trip->destination_dzongkhag }}</span>
                        </div>
                        <span class="seats-chip">
                            <i class="bi bi-person-fill"></i> <strong>{{ $trip->available_seats }} Seats Available</strong>
                        </span>
                    </div>
                    <div class="tcv2-meta">
                        <div class="tcv2-meta-item">
                            <i class="bi bi-calendar3-event text-primary"></i>
                            <span>{{ $trip->departure_datetime->format('d M Y') }}</span>
                        </div>
                        <div class="tcv2-meta-item">
                            <i class="bi bi-clock text-primary"></i>
                            <span>{{ $trip->departure_datetime->format('h:i A') }}</span>
                        </div>
                        <div class="tcv2-meta-item">
                            <i class="bi bi-person-circle text-primary"></i>
                            <span>{{ $trip->driver->user->name }}</span>
                        </div>
                        @if($trip->route->distance_km ?? false)
                        <div class="tcv2-meta-item">
                            <i class="bi bi-signpost-2 text-primary"></i>
                            <span>{{ $trip->route->distance_km }} km</span>
                        </div>
                        @endif
                    </div>
                    <div class="tcv2-footer">
                        <div class="tcv2-price">
                            <span class="tcv2-price-amt">Nu. {{ number_format($trip->price_per_seat) }}</span>
                            <span class="tcv2-price-unit">/ seat</span>
                        </div>
                        <a href="{{ route('trip.details', $trip->id) }}" class="btn btn-primary btn-sm tcv2-cta">
                            Book Now
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ════════════════════════════════════════
     STATS TRUST BAR
════════════════════════════════════════ --}}
<section class="stats-bar">
    <div class="container">
        <div class="row g-0 text-center stats-row">
            <div class="col-6 col-md-3">
                <div class="stat-pill-item">
                    <i class="bi bi-geo-alt-fill text-primary"></i>
                    <div class="stat-pill-num">20</div>
                    <div class="stat-pill-lbl">Dzongkhags</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-pill-item">
                    <i class="bi bi-lightning-fill text-warning"></i>
                    <div class="stat-pill-num">10s</div>
                    <div class="stat-pill-lbl">Booking Speed</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-pill-item">
                    <i class="bi bi-shield-fill-check text-success"></i>
                    <div class="stat-pill-num">100%</div>
                    <div class="stat-pill-lbl">Secure Payments</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-pill-item">
                    <i class="bi bi-cash-stack text-info"></i>
                    <div class="stat-pill-num">Free</div>
                    <div class="stat-pill-lbl">Cancellation</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ════════════════════════════════════════
     HOW IT WORKS
════════════════════════════════════════ --}}
<section class="how-section py-5">
    <div class="container">
        <div class="section-head text-center mb-4">
            <span class="section-eyebrow">Simple Process</span>
            <h2 class="section-title">How It Works</h2>
            <p class="section-sub">Book your intercity ride in under a minute</p>
        </div>
        <div class="row g-3 g-md-4">
            <div class="col-6 col-md-3">
                <div class="how-card">
                    <div class="how-num">01</div>
                    <div class="how-icon-wrap bg-primary-soft">
                        <i class="bi bi-search text-primary"></i>
                    </div>
                    <h6 class="how-title">Search Routes</h6>
                    <p class="how-desc">Find taxis between dzongkhags — no login needed</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="how-card">
                    <div class="how-num">02</div>
                    <div class="how-icon-wrap bg-success-soft">
                        <i class="bi bi-list-check text-success"></i>
                    </div>
                    <h6 class="how-title">Pick a Trip</h6>
                    <p class="how-desc">Compare times, prices, and available seats</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="how-card">
                    <div class="how-num">03</div>
                    <div class="how-icon-wrap bg-warning-soft">
                        <i class="bi bi-credit-card text-warning"></i>
                    </div>
                    <h6 class="how-title">Pay & Confirm</h6>
                    <p class="how-desc">Secure your seat with instant payment</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="how-card">
                    <div class="how-num">04</div>
                    <div class="how-icon-wrap bg-info-soft">
                        <i class="bi bi-car-front text-info"></i>
                    </div>
                    <h6 class="how-title">Travel</h6>
                    <p class="how-desc">Get picked up and enjoy your journey</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ════════════════════════════════════════
     BENEFITS
════════════════════════════════════════ --}}
<section class="benefits-section py-5">
    <div class="container">
        <div class="section-head text-center mb-4">
            <span class="section-eyebrow">Why Bhutan Taxi</span>
            <h2 class="section-title">Travel Smart, Travel Safe</h2>
        </div>
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <div class="benefit-card bc-blue">
                    <div class="bc-icon"><i class="bi bi-shield-check"></i></div>
                    <h5 class="bc-title">Secure Booking</h5>
                    <p class="bc-desc">First-pay-first-get ensures fair seat allocation with an instant payment window.</p>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="benefit-card bc-green">
                    <div class="bc-icon"><i class="bi bi-cash-stack"></i></div>
                    <h5 class="bc-title">Full Refund</h5>
                    <p class="bc-desc">Cancel more than 24 hours before departure and receive a 100% refund.</p>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="benefit-card bc-orange">
                    <div class="bc-icon"><i class="bi bi-phone"></i></div>
                    <h5 class="bc-title">PWA Ready</h5>
                    <p class="bc-desc">Install on your phone for an app-like experience with offline support.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ════════════════════════════════════════
     CTA BANNER
════════════════════════════════════════ --}}
<section class="cta-section py-5">
    <div class="container">
        <div class="cta-card">
            <i class="bi bi-taxi-front-fill cta-icon"></i>
            <h3 class="cta-title">Ready to travel across Bhutan?</h3>
            <p class="cta-sub">Join passengers booking safe, affordable intercity taxi rides every day.</p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="{{ route('search') }}" class="btn btn-primary btn-lg">
                    <i class="bi bi-search me-2"></i>Search Trips Now
                </a>
                <a href="{{ route('driver.register') }}" class="btn btn-outline-primary btn-lg">
                    <i class="bi bi-car-front me-2"></i>Become a Driver
                </a>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
// Smooth scroll to search section with proper offset
document.querySelectorAll('a[href="#search-section"]').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const searchSection = document.getElementById('search-section');
        if (searchSection) {
            const offset = window.innerWidth < 768 ? 80 : 40;
            const topPosition = searchSection.offsetTop - offset;
            window.scrollTo({
                top: topPosition,
                behavior: 'smooth'
            });
        }
    });
});

document.getElementById('swapBtn')?.addEventListener('click', function () {
    const from = document.getElementById('search-from');
    const to   = document.getElementById('search-to');
    const tmp  = from.value;
    from.value = to.value;
    to.value   = tmp;
    this.classList.add('spin');
    setTimeout(() => this.classList.remove('spin'), 400);
});
</script>
@endpush
