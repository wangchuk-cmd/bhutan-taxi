@extends('layouts.app')

@section('title', 'My Bookings')

@section('content')
@include('components.confirm-modal')
<div class="container py-3">
    <h2 class="mb-4"><i class="bi bi-ticket-perforated me-2"></i>My Bookings</h2>

    @if($bookings->count() > 0)
        <div class="row g-2">
            @foreach($bookings as $booking)
                <div class="col-6 col-md-12">
                    <div class="booking-card {{ $booking->status === 'cancelled' ? 'booking-card-cancelled' : 'booking-card-active' }}">
                        <div class="booking-card-header">
                            <div class="booking-route">
                                <span class="booking-location">{{ $booking->trip->origin_dzongkhag }}</span>
                                <i class="bi bi-arrow-right"></i>
                                <span class="booking-location">{{ $booking->trip->destination_dzongkhag }}</span>
                            </div>
                            <div class="booking-status">
                                @if($booking->status === 'active')
                                    @if($booking->payment_status === 'paid')
                                        <span class="badge bg-success">Confirmed</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                @else
                                    <span class="badge bg-danger">Cancelled</span>
                                @endif
                            </div>
                        </div>

                        <div class="booking-card-body">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                @if($booking->trip->driver->user->profile_image)
                                    <img src="{{ asset('storage/' . $booking->trip->driver->user->profile_image) }}" alt="{{ $booking->trip->driver->user->name }}" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 1px solid #dbe4f0;">
                                @else
                                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary-subtle text-primary" style="width: 36px; height: 36px; flex: 0 0 auto;">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                @endif
                                <div>
                                    <div class="booking-location" style="font-size: 0.95rem; line-height: 1.1;">{{ $booking->trip->driver->user->name }}</div>
                                    <small class="text-muted">Driver</small>
                                </div>
                            </div>

                            <div class="booking-meta-row">
                                <div class="booking-meta">
                                    <small>Date</small>
                                    <span>{{ $booking->trip->departure_datetime->format('M d, Y') }}</span>
                                </div>
                                <div class="booking-meta">
                                    <small>Time</small>
                                    <span>{{ $booking->trip->departure_datetime->format('h:i A') }}</span>
                                </div>
                                <div class="booking-meta">
                                    <small>Vehicle</small>
                                    <span>{{ $booking->trip->driver->vehicle_type }}</span>
                                </div>
                                <div class="booking-meta">
                                    <small>Fuel Type</small>
                                    @if($booking->trip->driver->fuel_type === 'Electric')
                                        <span><i class="bi bi-lightning-charge" style="color: #0dcaf0;"></i> Electric</span>
                                    @else
                                        <span><i class="bi bi-fuel-pump" style="color: #fd7e14;"></i> Fuel</span>
                                    @endif
                                </div>
                                <div class="booking-meta">
                                    <small>Seats</small>
                                    <span>{{ $booking->seats_booked }}</span>
                                </div>
                            </div>

                            <div class="booking-footer">
                                <div class="booking-price">
                                    <span class="fw-bold text-success">Nu. {{ number_format($booking->total_amount, 2) }}</span>
                                    @if($booking->latestRefundRequest)
                                        <span class="badge bg-info ms-2 text-capitalize">{{ $booking->latestRefundRequest->status }}</span>
                                    @endif
                                </div>
                                <div class="booking-actions">
                                    <a href="{{ route('bookings.show', $booking->id) }}" class="btn-action btn-view" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($booking->payment_status === 'paid' && !$booking->latestRefundRequest)
                                        <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-sm btn-outline-warning" title="Request Refund">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i> Refund
                                        </a>
                                    @endif
                                    @if($booking->payment_status === 'paid' && !$booking->rating)
                                        <a href="{{ route('rating.show', $booking->id) }}" class="btn btn-sm btn-warning" title="Rate Driver">
                                            <i class="bi bi-star me-1"></i> Rate Driver
                                        </a>
                                    @elseif($booking->payment_status === 'paid' && $booking->rating)
                                        <a href="{{ route('rating.show', $booking->id) }}" class="btn btn-sm btn-warning" title="Update Rating">
                                            <i class="bi bi-star-fill me-1"></i> Update Rating
                                        </a>
                                    @endif
                                    @if($booking->canCancel())
                                        <form id="cancelForm-{{ $booking->id }}" action="{{ route('booking.cancel', $booking->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="button" class="btn-action btn-cancel" title="Cancel Booking" onclick="showConfirmModal('Cancel this booking?', 'Cancel Booking', function() { document.getElementById('cancelForm-{{ $booking->id }}').submit(); })">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    @else
                                        @php
                                            $rideCompleted = now()->isAfter($booking->trip->departure_datetime);
                                            $hoursAfterTrip = now()->diffInHours($booking->trip->departure_datetime, false);
                                            $autoDeleteIn = max(0, 12 - $hoursAfterTrip);
                                        @endphp
                                        @if($rideCompleted && $hoursAfterTrip <= 12)
                                            <span class="booking-timer" title="Auto-delete in {{ $autoDeleteIn }} hours">
                                                <i class="bi bi-clock-history"></i>
                                                {{ $autoDeleteIn }}h
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-ticket-perforated display-1 text-muted"></i>
            <h4 class="mt-3">No Bookings Yet</h4>
            <p class="text-muted">You haven't made any bookings. Start by searching for a trip!</p>
            <a href="{{ route('home') }}" class="btn btn-primary">
                <i class="bi bi-search me-2"></i>Search Trips
            </a>
        </div>
    @endif
</div>
@endsection
