@extends('layouts.app')

@section('title', 'My Bookings')

@section('content')
<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-ticket-perforated me-2"></i>My Bookings</h2>

    @if($bookings->count() > 0)
        <div class="row g-2 g-sm-3">
            @foreach($bookings as $booking)
                <div class="col-6 col-lg-6">
                    <div class="card h-100 {{ $booking->status === 'cancelled' ? 'border-start border-danger border-3' : 'border-start border-primary border-2' }}">
                        <div class="card-body p-3">

                            {{-- Route + Status --}}
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="route-title mb-0 me-2 flex-grow-1">
                                    {{ $booking->trip->origin_dzongkhag }}
                                    <i class="bi bi-arrow-right text-primary"></i>
                                    {{ $booking->trip->destination_dzongkhag }}
                                </h5>
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

                            {{-- Info Grid --}}
                            <div class="booking-info-grid mb-3">
                                <div class="booking-info-item">
                                    <small>Date</small>
                                    <span class="info-value">{{ $booking->trip->departure_datetime->format('M d, Y') }}</span>
                                </div>
                                <div class="booking-info-item">
                                    <small>Time</small>
                                    <span class="info-value">{{ $booking->trip->departure_datetime->format('h:i A') }}</span>
                                </div>
                                <div class="booking-info-item">
                                    <small>Seats</small>
                                    <span class="info-value">{{ $booking->seats_booked }} ({{ ucfirst($booking->booking_type) }})</span>
                                </div>
                                <div class="booking-info-item">
                                    <small>Driver</small>
                                    <span class="info-value">{{ $booking->trip->driver->user->name }}</span>
                                </div>
                            </div>

                            {{-- Amount + Actions --}}
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                <div>
                                    <span class="fw-bold text-success fs-5">Nu. {{ number_format($booking->total_amount, 2) }}</span>
                                    @if($booking->refund_status === 'refunded')
                                        <span class="badge bg-info ms-1">Refunded</span>
                                    @endif
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-eye me-1"></i>View
                                    </a>
                                    @if($booking->canCancel())
                                        <form action="{{ route('booking.cancel', $booking->id) }}" method="POST"
                                              onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
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
