@extends('layouts.app')

@section('title', 'My Bookings')

@section('content')
<div class="container py-3">
    <h2 class="mb-4"><i class="bi bi-ticket-perforated me-2"></i>My Bookings</h2>

    @if($bookings->count() > 0)
        <div class="row g-2">
            @foreach($bookings as $booking)
                <div class="col-12 col-md-6">
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
                                    <small>Seats</small>
                                    <span>{{ $booking->seats_booked }}</span>
                                </div>
                            </div>

                            <div class="booking-footer">
                                <div class="booking-price">
                                    <span class="fw-bold text-success">Nu. {{ number_format($booking->total_amount, 2) }}</span>
                                    @if($booking->refund_status === 'refunded')
                                        <span class="badge bg-info ms-2">Refunded</span>
                                    @endif
                                </div>
                                <div class="booking-actions">
                                    <a href="{{ route('bookings.show', $booking->id) }}" class="btn-action btn-view" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($booking->canCancel())
                                        <form action="{{ route('booking.cancel', $booking->id) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Cancel this booking?');">
                                            @csrf
                                            <button type="submit" class="btn-action btn-cancel" title="Cancel Booking">
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
