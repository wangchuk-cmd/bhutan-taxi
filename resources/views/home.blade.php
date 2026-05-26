@extends('layouts.app')

@section('title', 'Home')

@push('styles')
<style>
.home-luxe {
    --home-bg: #f4f7fb;
    --hero-deep: #0f1f3a;
    --hero-mid: #1a2f52;
    --hero-glow: #2f6fe0;
    --home-card: #ffffff;
    --home-muted: #5f6f8c;
    background: linear-gradient(180deg, #f8fbff 0%, var(--home-bg) 60%, #f6f7fb 100%);
}

.home-luxe .hero-fit {
    min-height: calc(100vh - 60px);
    padding: 2rem 0 2.1rem;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    background:
        radial-gradient(circle at 18% 22%, rgba(95, 151, 255, 0.18) 0 2px, transparent 2.5px),
        radial-gradient(circle at 78% 28%, rgba(255, 255, 255, 0.14) 0 2px, transparent 2.5px),
        radial-gradient(circle at 72% 72%, rgba(89, 154, 255, 0.16) 0 2px, transparent 2.5px),
        linear-gradient(rgba(255,255,255,0.07) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.07) 1px, transparent 1px),
        linear-gradient(125deg, var(--hero-deep) 0%, var(--hero-mid) 70%, #233d66 100%);
    background-size: 220px 220px, 260px 260px, 240px 240px, 72px 72px, 72px 72px, auto;
    background-position: 0 0, 40px 40px, 100px 110px, 0 0, 0 0, center;
}

.home-luxe .hero-fit::before,
.home-luxe .hero-fit::after {
    content: '';
    position: absolute;
    pointer-events: none;
    z-index: 0;
}

.home-luxe .hero-fit::before {
    width: 540px;
    height: 540px;
    left: -180px;
    top: -140px;
    background: radial-gradient(circle, rgba(95, 151, 255, 0.35) 0%, rgba(95, 151, 255, 0) 70%);
}

.home-luxe .hero-fit::after {
    width: 460px;
    height: 460px;
    right: -170px;
    bottom: -140px;
    background: radial-gradient(circle, rgba(55, 98, 206, 0.35) 0%, rgba(55, 98, 206, 0) 70%);
}

.home-luxe .relative-content {
    position: relative;
    z-index: 2;
    width: 100%;
}

.home-luxe .hero-map-visual {
    position: absolute;
    right: -2rem;
    top: 50%;
    transform: translateY(-50%);
    width: min(54vw, 760px);
    height: min(66vh, 560px);
    opacity: 0.32;
    pointer-events: none;
    z-index: 1;
}

.home-luxe .hero-map-visual svg {
    width: 100%;
    height: 100%;
    display: block;
}

.home-luxe .hero-map-visual .map-grid {
    stroke: rgba(255,255,255,0.11);
    stroke-width: 1;
}

.home-luxe .hero-map-visual .route-line {
    fill: none;
    stroke: rgba(160, 206, 255, 0.9);
    stroke-width: 5;
    stroke-linecap: round;
    stroke-dasharray: 10 12;
    filter: drop-shadow(0 0 12px rgba(160, 206, 255, 0.35));
}

.home-luxe .hero-map-visual .route-pin {
    fill: #7dd3fc;
    filter: drop-shadow(0 0 16px rgba(125, 211, 252, 0.5));
}

.home-luxe .hero-map-visual .route-node {
    fill: #ffffff;
    opacity: 0.95;
}

.home-luxe .hero-live-badge {
    border: 1px solid rgba(154, 193, 255, 0.35);
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(8px);
    border-radius: 999px;
    color: #d9e8ff;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 0.35rem 0.95rem;
    font-size: 0.8rem;
    font-weight: 600;
}


.home-luxe .hf-title {
    color: #f8fbff;
    text-wrap: balance;
    margin-bottom: 1.15rem;
}

.home-luxe .hf-title-mobile {
    color: #f8fbff;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: clamp(2rem, 8vw, 2.4rem);
    line-height: 1.08;
    font-weight: 800;
    margin-bottom: 0.9rem;
}

.home-luxe .hf-sub {
    color: rgba(232, 240, 255, 0.92);
    max-width: 560px;
    margin-bottom: 1.35rem;
}

.home-luxe .hero-cta-group .btn {
    border-radius: 999px;
    font-weight: 700;
    letter-spacing: 0.01em;
    padding-inline: 1.2rem;
}

.home-luxe .hero-cta-group .btn-primary {
    box-shadow: 0 10px 22px rgba(37, 99, 235, 0.38);
}

.home-luxe .hero-cta-group .btn-glass {
    border: 1px solid rgba(255, 255, 255, 0.26);
    background: rgba(255, 255, 255, 0.1);
}

.home-luxe .sc-fit {
    border-radius: 24px;
    border: 1px solid rgba(213, 223, 240, 0.9);
    box-shadow: 0 24px 50px rgba(7, 21, 48, 0.32);
    background: linear-gradient(165deg, #ffffff 0%, #f7faff 100%);
    max-width: 460px;
    position: relative;
    z-index: 2;
}

.home-luxe .sc-fit::before {
    content: '';
    position: absolute;
    inset: -12px -12px -12px 12px;
    border-radius: 28px;
    background: linear-gradient(135deg, rgba(125, 211, 252, 0.22), rgba(37, 99, 235, 0.15));
    filter: blur(18px);
    z-index: -1;
}

.home-luxe .scv3-header h2 {
    color: #15294e;
    letter-spacing: -0.01em;
}

.home-luxe .sc-fit .v3-input-box {
    border: 1px solid #d9e3f2;
    background: #fbfdff;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.home-luxe .sc-fit .v3-input-box:focus-within {
    border-color: #6f9fff;
    box-shadow: 0 0 0 3px rgba(111, 159, 255, 0.2);
}

.home-luxe .sc-fit .btn-search {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    border: none;
}

.home-luxe .trips-section,
.home-luxe .how-section,
.home-luxe .benefits-section,
.home-luxe .cta-section {
    position: relative;
}

.home-luxe .section-eyebrow {
    color: #2f5fb8;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.12em;
}

.home-luxe .section-title {
    color: #0e2042;
    letter-spacing: -0.025em;
    font-weight: 800;
}

.home-luxe .trip-card-v2,
.home-luxe .how-card,
.home-luxe .benefit-card {
    border-radius: 18px;
    border: 1px solid #e4ecf8;
    box-shadow: 0 8px 24px rgba(19, 41, 82, 0.08);
    transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
}

.home-luxe .trip-card-v2:hover,
.home-luxe .how-card:hover,
.home-luxe .benefit-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 18px 34px rgba(19, 41, 82, 0.14);
    border-color: #cfdef6;
}

.home-luxe .tcv2-header {
    background: linear-gradient(135deg, #eef5ff 0%, #f8fbff 100%);
}

.home-luxe .tcv2-meta-item {
    color: #4e607f;
}

.home-luxe .tcv2-footer {
    background: #f8fbff;
}

.home-luxe .stats-bar {
    margin-top: 0.5rem;
    background: transparent;
    border-bottom: none;
}

.home-luxe .stats-row {
    background: white;
    border-radius: 18px;
    border: 1px solid #e3ebf8;
    box-shadow: 0 8px 22px rgba(19, 41, 82, 0.08);
    overflow: hidden;
}

.home-luxe .stat-pill-item {
    padding: 1.1rem 0.7rem;
}

.home-luxe .stat-pill-num {
    font-size: 1.28rem;
    font-weight: 800;
    color: #1d3f78;
}

.home-luxe .stat-pill-lbl {
    font-size: 0.72rem;
    font-weight: 600;
    color: var(--home-muted);
}

.home-luxe .cta-card {
    border-radius: 24px;
    box-shadow: 0 22px 42px rgba(17, 48, 113, 0.26);
}

.home-luxe .cta-card .btn {
    border-radius: 12px;
    font-weight: 700;
}

.home-luxe .trips-section .trip-card-v2,
.home-luxe .how-section .how-card,
.home-luxe .benefits-section .benefit-card {
    opacity: 0;
    animation: riseIn 0.55s ease-out forwards;
}

.home-luxe .trips-section .trip-card-v2:nth-child(1),
.home-luxe .how-section .how-card:nth-child(1),
.home-luxe .benefits-section .benefit-card:nth-child(1) { animation-delay: 0.06s; }
.home-luxe .trips-section .trip-card-v2:nth-child(2),
.home-luxe .how-section .how-card:nth-child(2),
.home-luxe .benefits-section .benefit-card:nth-child(2) { animation-delay: 0.12s; }
.home-luxe .trips-section .trip-card-v2:nth-child(3),
.home-luxe .how-section .how-card:nth-child(3),
.home-luxe .benefits-section .benefit-card:nth-child(3) { animation-delay: 0.18s; }

@keyframes riseIn {
    from {
        opacity: 0;
        transform: translateY(18px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 991.98px) {
    .home-luxe .hero-fit {
        min-height: auto;
        padding: 1.75rem 0 2rem;
    }

    .home-luxe .hf-sub {
        margin-inline: auto;
    }

    .home-luxe .hero-cta-group {
        justify-content: center;
    }

    .home-luxe .sc-fit {
        margin: 1.3rem auto 0;
    }

    .home-luxe .hero-map-visual {
        right: -5rem;
        width: 84vw;
        height: 40vh;
        opacity: 0.22;
    }
}

@media (max-width: 575.98px) {
    .home-luxe .container {
        background:
            radial-gradient(circle at 18% 22%, rgba(95, 151, 255, 0.18) 0 2px, transparent 2.5px),
            radial-gradient(circle at 78% 28%, rgba(255, 255, 255, 0.14) 0 2px, transparent 2.5px),
            radial-gradient(circle at 72% 72%, rgba(89, 154, 255, 0.16) 0 2px, transparent 2.5px),
            linear-gradient(rgba(255,255,255,0.07) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.07) 1px, transparent 1px),
            linear-gradient(125deg, var(--hero-deep) 0%, var(--hero-mid) 70%, #233d66 100%);
        background-size: 220px 220px, 260px 260px, 240px 240px, 72px 72px, 72px 72px, auto;
        background-position: 0 0, 40px 40px, 100px 110px, 0 0, 0 0, center;
    }

    .home-luxe .hero-live-badge {
        font-size: 0.72rem;
        padding: 0.3rem 0.75rem;
    }

    .home-luxe .hf-title-mobile {
        margin-top: 0.25rem;
        font-size: 1.95rem;
    }

    .home-luxe .hf-sub {
        font-size: 0.86rem;
        line-height: 1.55;
    }

    .home-luxe .hero-cta-group .btn {
        min-height: 36px;
        font-size: 0.74rem;
        padding-inline: 0.9rem;
    }

    .home-luxe .sc-fit {
        border-radius: 18px;
        padding: 0.95rem;
    }

    .home-luxe .hero-map-visual {
        right: -6rem;
        width: 96vw;
        height: 30vh;
        opacity: 0.16;
    }

    .home-luxe .trips-section .row,
    .home-luxe .how-section .row,
    .home-luxe .benefits-section .row {
        --bs-gutter-y: 0.75rem;
    }

    .home-luxe .stats-row {
        border-radius: 14px;
    }
}
</style>
@endpush

@section('content')
<div class="home-luxe">

{{-- ════════════════════════════════════════
     HERO V4 (Screen-Fit & Mobile Perfect)
════════════════════════════════════════ --}}
<section class="hero-fit">
    <div class="hf-decor-circle"></div>
    <div class="hero-map-visual" aria-hidden="true">
        <svg viewBox="0 0 900 540" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g opacity="0.95">
                <path class="map-grid" d="M0 90H900M0 180H900M0 270H900M0 360H900M0 450H900" />
                <path class="map-grid" d="M120 0V540M260 0V540M400 0V540M540 0V540M680 0V540M820 0V540" />
                <path class="route-line" d="M120 390C210 305 300 286 372 326C454 372 532 369 610 307C690 244 775 232 840 264" />
                <circle class="route-node" cx="120" cy="390" r="10" />
                <circle class="route-node" cx="372" cy="326" r="10" />
                <circle class="route-node" cx="610" cy="307" r="10" />
                <circle class="route-node" cx="840" cy="264" r="10" />
                <path class="route-pin" d="M372 292c-13 0-24 10-24 23.5 0 17.5 24 38.5 24 38.5s24-21 24-38.5C396 302 385 292 372 292Zm0 30a8.5 8.5 0 1 1 0-17 8.5 8.5 0 0 1 0 17Z" />
                <path class="route-pin" d="M610 273c-13 0-24 10-24 23.5 0 17.5 24 38.5 24 38.5s24-21 24-38.5c0-13.5-11-23.5-24-23.5Zm0 30a8.5 8.5 0 1 1 0-17 8.5 8.5 0 0 1 0 17Z" />
            </g>
        </svg>
    </div>

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

                <h1 class="hf-title-mobile d-md-none">
                    Book Intercity Taxis
                    <br>Across Bhutan
                </h1>

                <p class="hf-sub mb-4 d-none d-md-block">
                    Instantly book shared or private intercity taxis.
                    Fast, secure, and reliable travel across Bhutan.
                </p>

                <div class="hero-cta-group d-flex flex-wrap gap-2 mb-4 justify-content-lg-start justify-content-center">
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
            <a href="{{ route('trips.all') }}" class="btn btn-outline-primary btn-sm view-all-btn">
                View All <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="row g-3">
            @foreach($featuredTrips as $trip)
            <div class="col-6 col-md-6 col-lg-4">
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
                        @if($trip->driver->average_rating > 0)
                        <div class="tcv2-meta-item">
                            <i class="bi bi-star-fill text-warning"></i>
                            <span>{{ number_format($trip->driver->average_rating, 1) }} 
                                <small class="text-muted">({{ $trip->driver->rating_count }})</small>
                            </span>
                        </div>
                        @endif
                        <div class="tcv2-meta-item">
                            <i class="bi bi-car-front text-primary"></i>
                            <span>{{ $trip->driver->vehicle_type }}</span>
                        </div>
                        <div class="tcv2-meta-item">
                            @if($trip->driver->fuel_type === 'Electric')
                                <i class="bi bi-lightning-charge" style="color: #0dcaf0;"></i>
                                <span><strong>Electric</strong></span>
                            @else
                                <i class="bi bi-fuel-pump" style="color: #fd7e14;"></i>
                                <span><strong>Fuel</strong></span>
                            @endif
                        </div>
                        @if($trip->route->distance_km ?? false)
                        <div class="tcv2-meta-item">
                            <i class="bi bi-signpost-2 text-primary"></i>
                            <span>{{ $trip->route->formatted_distance_km }}</span>
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

</div>

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
