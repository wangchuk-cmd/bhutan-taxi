@extends('layouts.admin')

@section('title', 'Manage Payouts')

@section('content')
<h4 class="mb-4"><i class="bi bi-wallet2 me-2"></i>Driver Payouts</h4>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-bg-success">
            <div class="card-body">
                <h6>Total Completed</h6>
                <h3>Nu. {{ number_format($stats['totalPaid'] ?? 0) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-bg-warning">
            <div class="card-body">
                <h6>Pending Payouts</h6>
                <h3>Nu. {{ number_format($stats['totalPending'] ?? 0) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-bg-info">
            <div class="card-body">
                <h6>Service Charges</h6>
                <h3>Nu. {{ number_format($stats['totalServiceCharges'] ?? 0) }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($payouts->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Driver</th>
                            <th>Trip</th>
                            <th>Total Amount</th>
                            <th>Service (10%)</th>
                            <th>Payout</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payouts as $payout)
                            <tr>
                                <td><strong>{{ $payout->driver->user->name ?? 'N/A' }}</strong></td>
                                <td>{{ $payout->trip->origin_dzongkhag ?? 'N/A' }} <i class="bi bi-arrow-right small"></i> {{ $payout->trip->destination_dzongkhag ?? 'N/A' }}<br>
                                    <small class="text-muted">{{ $payout->trip->departure_datetime->format('M d, Y') }}</small>
                                </td>
                                <td>Nu. {{ number_format($payout->total_amount) }}</td>
                                <td>Nu. {{ number_format($payout->service_charge) }}</td>
                                <td>Nu. {{ number_format($payout->payout_amount) }}</td>
                                <td>
                                    <span class="badge bg-{{ $payout->status === 'completed' ? 'success' : ($payout->status === 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($payout->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($payout->status === 'pending')
                                        <form action="{{ route('admin.payouts.process', $payout->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-success" onclick="return confirm('Mark as paid?');">
                                                <i class="bi bi-check-circle me-1"></i>Pay
                                            </button>
                                        </form>
                                    @else
                                        <small class="text-muted">{{ $payout->paid_at ? $payout->paid_at->format('M d, Y') : '-' }}</small>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $payouts->links() }}
        @else
            <div class="text-center py-5">
                <i class="bi bi-wallet2 display-1 text-muted"></i>
                <p class="mt-3 text-muted">No payouts yet</p>
            </div>
        @endif
    </div>
</div>
@endsection
