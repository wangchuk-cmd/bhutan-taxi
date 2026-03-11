@extends('layouts.app')

@section('title', 'My Bookings')

@section('content')
<div class="bookings-container">
    <h1 class="page-title"><i class="bi bi-ticket-perforated me-2"></i>My Bookings</h1>

    @if($bookings->count() > 0)
        <div class="bookings-grid">
            @foreach($bookings as $booking)
                <div class="booking-card {{ $booking->status === 'cancelled' ? 'cancelled' : '' }}">
                    <!-- Card Header -->
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div class="route-title">
                                {{ $booking->trip->origin_dzongkhag }}
                                <i class="bi bi-arrow-right"></i>
                                {{ $booking->trip->destination_dzongkhag }}
                            </div>
                            @if($booking->status === 'active')
                                @if($booking->payment_status === 'paid')
                                    <span class="badge bg-success flex-shrink-0">Confirmed</span>
                                @else
                                    <span class="badge bg-warning text-dark flex-shrink-0">Pending</span>
                                @endif
                            @else
                                <span class="badge bg-danger flex-shrink-0">Cancelled</span>
                            @endif
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="booking-card-body">
                        <!-- Info Grid -->
                        <div class="booking-info-grid">
                            <div class="booking-info-item">
                                <small><i class="bi bi-calendar3 me-1"></i>Date</small>
                                <span class="info-value">{{ $booking->trip->departure_datetime->format('M d') }}</span>
                            </div>
                            <div class="booking-info-item">
                                <small><i class="bi bi-clock me-1"></i>Time</small>
                                <span class="info-value">{{ $booking->trip->departure_datetime->format('h:i A') }}</span>
                            </div>
                            <div class="booking-info-item">
                                <small><i class="bi bi-people me-1"></i>Seats</small>
                                <span class="info-value">{{ $booking->seats_booked }}</span>
                            </div>
                            <div class="booking-info-item">
                                <small><i class="bi bi-person-badge me-1"></i>Driver</small>
                                <span class="info-value">{{ substr($booking->trip->driver->user->name, 0, 15) }}</span>
                            </div>
                        </div>

                        <!-- Amount & Actions -->
                        <div class="card-footer">
                            <div>
                                <span class="booking-amount">Nu. {{ number_format($booking->total_amount, 2) }}</span>
                                @if($booking->refund_status === 'refunded')
                                    <span class="badge bg-info ms-1">Refunded</span>
                                @endif
                            </div>
                            <div class="booking-actions">
                                <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                @if($booking->canCancel())
                                    <form action="{{ route('booking.cancel', $booking->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Cancel this booking?')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div style="text-align: center; padding: 60px 20px;">
            <i class="bi bi-inbox" style="font-size: 48px; opacity: 0.3; display: block; margin-bottom: 20px;"></i>
            <h5 style="color: #6b7280;">No bookings yet</h5>
            <p style="color: #9ca3af; margin-bottom: 20px;">You haven't made any bookings yet. Start your journey today!</p>
            <a href="{{ route('home') }}#search-section" class="btn btn-primary">
                <i class="bi bi-search me-2"></i>Search Trips
            </a>
        </div>
    @endif
</div>

@endsection
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
