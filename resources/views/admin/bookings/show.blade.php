@extends('layouts.admin')

@section('title', 'Booking Details')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.bookings') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-2"></i>Back to Bookings
    </a>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">Booking #{{ $booking->id }}</h5></div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-6"><span class="text-muted">Status</span></div>
                    <div class="col-6"><span class="badge bg-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'pending' ? 'warning' : 'danger') }}">{{ ucfirst($booking->status) }}</span></div>
                </div>
                <div class="row mb-3">
                    <div class="col-6"><span class="text-muted">Booking Type</span></div>
                    <div class="col-6">{{ ucfirst($booking->booking_type) }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-6"><span class="text-muted">Seats</span></div>
                    <div class="col-6">{{ $booking->seats_booked }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-6"><span class="text-muted">Total Amount</span></div>
                    <div class="col-6"><strong>Nu. {{ number_format($booking->total_amount) }}</strong></div>
                </div>
                <div class="row">
                    <div class="col-6"><span class="text-muted">Booked At</span></div>
                    <div class="col-6">{{ $booking->created_at->format('M d, Y h:i A') }}</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h6 class="mb-0">Booking Details</h6></div>
            <div class="card-body">
                <!-- BOOKER SECTION -->
                <div style="background-color: #e7f3ff; padding: 15px; border-radius: 5px; margin-bottom: 15px; border-left: 4px solid #0d6efd;">
                    <h6 style="color: #0d6efd; margin-top: 0; margin-bottom: 10px;">📝 BOOKER (Account Holder)</h6>
                    @if($booking->passenger)
                        <p class="mb-2"><strong>{{ $booking->passenger->name }}</strong></p>
                        <p class="mb-2"><i class="bi bi-envelope me-2"></i>{{ $booking->passenger->email }}</p>
                        <p class="mb-0"><i class="bi bi-telephone me-2"></i>{{ $booking->passenger->phone_number }}</p>
                    @else
                        <p class="mb-0 text-danger"><strong>Booker information unavailable</strong></p>
                    @endif
                </div>
                
                <!-- PASSENGERS SECTION -->
                @if(is_array($booking->passengers_info) && count($booking->passengers_info) > 0)
                <div style="background-color: #f0f9ff; padding: 15px; border-radius: 5px; border-left: 4px solid #198754;">
                    <h6 style="color: #198754; margin-top: 0; margin-bottom: 10px;">👥 PASSENGERS ({{ count($booking->passengers_info) }})</h6>
                    <ul class="list-group">
                        @foreach($booking->passengers_info as $index => $p)
                            <li class="list-group-item">
                                <strong>{{ $index + 1 }}. {{ $p['name'] ?? 'N/A' }}</strong><br>
                                <span class="d-block"><i class="bi bi-telephone me-2"></i>{{ $p['phone'] ?? 'N/A' }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header"><h6 class="mb-0">Trip Details</h6></div>
            <div class="card-body">
                <h6 class="text-primary">{{ $booking->trip->origin_dzongkhag }} → {{ $booking->trip->destination_dzongkhag }}</h6>
                <p class="mb-2"><i class="bi bi-calendar me-2"></i>{{ $booking->trip->departure_datetime->format('M d, Y h:i A') }}</p>
                <hr>
                <p class="mb-1"><strong>Driver:</strong> {{ $booking->trip->driver->user->name }}</p>
                <p class="mb-0"><i class="bi bi-telephone me-2"></i>{{ $booking->trip->driver->user->phone_number }}</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h6 class="mb-0">Payment</h6></div>
            <div class="card-body">
                @if($booking->payment)
                    <div class="row mb-2">
                        <div class="col-6"><span class="text-muted">Status</span></div>
                        <div class="col-6"><span class="badge bg-{{ $booking->payment->status === 'completed' ? 'success' : 'warning' }}">{{ ucfirst($booking->payment->status) }}</span></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6"><span class="text-muted">Method</span></div>
                        <div class="col-6">{{ ucfirst($booking->payment->payment_method) }}</div>
                    </div>
                    <div class="row">
                        <div class="col-6"><span class="text-muted">Amount</span></div>
                        <div class="col-6"><strong>Nu. {{ number_format($booking->payment->amount) }}</strong></div>
                    </div>
                    @if($booking->payment->status === 'completed')
                    <div class="mt-3">
                        <a href="{{ route('admin.booking.receipt', $booking->id) }}" class="btn btn-success btn-sm">
                            <i class="bi bi-receipt me-2"></i>View & Download Receipt
                        </a>
                    </div>
                    @endif
                @else
                    <p class="text-muted mb-0">No payment recorded</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
