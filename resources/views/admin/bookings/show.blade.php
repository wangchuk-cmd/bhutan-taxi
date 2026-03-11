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
            <div class="card-header"><h6 class="mb-0">Passenger</h6></div>
            <div class="card-body">
                    @if($booking->passenger)
                        <p class="mb-2"><strong>{{ $booking->passenger->name }}</strong></p>
                        <p class="mb-2"><i class="bi bi-envelope me-2"></i>{{ $booking->passenger->email }}</p>
                        <p class="mb-0"><i class="bi bi-telephone me-2"></i>{{ $booking->passenger->phone_number }}</p>
                    @else
                        <p class="mb-2 text-danger"><strong>Passenger information unavailable</strong></p>
                    @endif
                    @if($booking->user && $booking->passenger && $booking->user->id !== $booking->passenger->id)
                        <hr>
                        <p class="mb-2"><strong>Booked By:</strong> {{ $booking->user->name }}</p>
                        <p class="mb-2"><i class="bi bi-envelope me-2"></i>{{ $booking->user->email }}</p>
                        <p class="mb-0"><i class="bi bi-telephone me-2"></i>{{ $booking->user->phone_number }}</p>
                    @endif
                    @if(is_array($booking->passengers_info) && count($booking->passengers_info) > 0)
                        <hr>
                        <h6 class="mb-2">Passenger List</h6>
                        <ul class="list-group mb-2">
                            @foreach($booking->passengers_info as $p)
                                <li class="list-group-item">
                                    <strong>{{ $p['name'] ?? 'N/A' }}</strong><br>
                                    @if(isset($p['email']))<i class="bi bi-envelope me-2"></i>{{ $p['email'] }}<br>@endif
                                    <span class="d-block"><i class="bi bi-telephone me-2"></i>Contact: {{ $p['phone_number'] ?? 'N/A' }}</span>
                                </li>
                            @endforeach
                        </ul>
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
