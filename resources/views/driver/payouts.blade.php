@extends('layouts.driver')

@section('title', 'Payouts')

@section('content')
<h4 class="mb-4"><i class="bi bi-wallet2 me-2"></i>My Payouts</h4>

<div class="row g-2 g-sm-4 mb-4 stats-row">
    <div class="col-6">
        <div class="stat-card success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 mb-1">Total Paid Out</h6>
                    <h2 class="mb-0">Nu. {{ number_format($totalPaid) }}</h2>
                </div>
                <i class="bi bi-cash-stack fs-1 text-white-50"></i>
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="stat-card warning">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 mb-1">Pending Payout</h6>
                    <h2 class="mb-0">Nu. {{ number_format($pendingAmount) }}</h2>
                </div>
                <i class="bi bi-hourglass-split fs-1 text-white-50"></i>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Payout History</h5>
    </div>
    <div class="card-body">
        @if($payouts->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Trip</th>
                            <th>Date</th>
                            <th>Total Amount</th>
                            <th>Service Charge</th>
                            <th>Payout Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payouts as $payout)
                            <tr>
                                <td>{{ $payout->trip->origin_dzongkhag ?? 'N/A' }} → {{ $payout->trip->destination_dzongkhag ?? 'N/A' }}</td>
                                <td>{{ $payout->created_at->format('M d, Y') }}</td>
                                <td>Nu. {{ number_format($payout->total_amount) }}</td>
                                <td class="text-danger">- Nu. {{ number_format($payout->service_charge) }}</td>
                                <td class="fw-bold text-success">Nu. {{ number_format($payout->payout_amount) }}</td>
                                <td>
                                    @if($payout->status === 'completed')
                                        <span class="badge bg-success">Paid</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
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

<div class="alert alert-info mt-4">
    <i class="bi bi-info-circle me-2"></i>
    <strong>Note:</strong> Payouts are processed every 72 hours. 3% service charge is deducted from each trip earning.
</div>
@endsection
