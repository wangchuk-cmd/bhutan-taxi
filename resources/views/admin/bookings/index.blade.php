@extends('layouts.admin')

@section('title', 'Manage Bookings')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-ticket-perforated me-2"></i>Bookings Management</h4>
    <a href="{{ route('admin.bookings.search') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Book on Behalf
    </a>
</div>

<div class="card">
    <div class="card-body">
        @if($bookings->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Booking #</th>
                            <th>Passenger</th>
                            <th>Route</th>
                            <th>Departure</th>
                            <th>Seats</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                            <tr>
                                <td><strong>#{{ $booking->id }}</strong></td>
                                <td>{{ $booking->passenger->name ?? 'N/A' }}<br><span style="font-size: 11px; color: #6c757d;">Booker</span></td>
                                <td>{{ $booking->trip->origin_dzongkhag }} <i class="bi bi-arrow-right small"></i> {{ $booking->trip->destination_dzongkhag }}</td>
                                <td>{{ $booking->trip->departure_datetime->format('M d') }}</td>
                                <td>{{ $booking->seats_booked }}</td>
                                <td>Nu. {{ number_format($booking->total_amount) }}</td>
                                <td>
                                    @if($booking->payment && $booking->payment->status === 'completed')
                                        <span class="badge bg-success">Paid</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                                <td><span class="badge bg-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'pending' ? 'warning' : 'danger') }}">{{ ucfirst($booking->status) }}</span></td>
                                <td>
                                    <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $bookings->links() }}
        @else
            <div class="text-center py-5">
                <i class="bi bi-ticket-perforated display-1 text-muted"></i>
                <p class="mt-3 text-muted">No bookings yet</p>
            </div>
        @endif
    </div>
</div>
@endsection
