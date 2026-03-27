@extends('layouts.driver')

@section('title', 'Payouts')

@section('content')

<style>
    :root {
        --primary-color: #2563eb;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --text-dark: #111827;
        --text-muted: #374151;
        --bg-light: #f3f4f6;
        --card-shadow: 0 1px 3px rgba(0, 0, 0, 0.08), 0 1px 2px rgba(0, 0, 0, 0.04);
        --card-shadow-lg: 0 4px 6px rgba(0, 0, 0, 0.07), 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .page-title {
        font-size: 28px;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 32px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: var(--card-shadow);
        border: 1px solid #f0f0f0;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        transform: translateY(-4px);
    }

    .stat-content {
        flex: 1;
    }

    .stat-label {
        font-size: 13px;
        font-weight: 500;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 32px;
        font-weight: 700;
        color: var(--text-dark);
        margin: 0;
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        flex-shrink: 0;
    }

    .stat-icon.success { background: #d1fae5; color: var(--success-color); }
    .stat-icon.warning { background: #fed7aa; color: var(--warning-color); }

    .payouts-container {
        background: white;
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        border: 1px solid #f0f0f0;
        overflow: hidden;
    }

    .payouts-header {
        padding: 24px;
        border-bottom: 2px solid var(--bg-light);
    }

    .payouts-header h2 {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-dark);
        margin: 0;
    }

    .payouts-table {
        width: 100%;
        border-collapse: collapse;
    }

    .payouts-table thead {
        background: var(--bg-light);
    }

    .payouts-table th {
        padding: 16px;
        text-align: left;
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e5e7eb;
    }

    .payouts-table tbody tr {
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.2s;
    }

    .payouts-table tbody tr:hover {
        background: #fafafa;
    }

    .payouts-table td {
        padding: 16px;
        color: var(--text-dark);
        font-size: 14px;
    }

    .route-text {
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .amount-positive {
        color: var(--success-color);
        font-weight: 600;
    }

    .amount-deducted {
        color: #ef4444;
        font-weight: 600;
    }

    .badge-modern {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-success { background: #d1fae5; color: var(--success-color); }
    .badge-warning { background: #fed7aa; color: var(--warning-color); }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state i {
        font-size: 48px;
        color: var(--text-muted);
        display: block;
        margin-bottom: 16px;
    }

    .empty-state p {
        color: var(--text-muted);
        font-size: 16px;
    }

    .pagination-wrapper {
        padding: 20px;
        border-top: 1px solid #f0f0f0;
    }
</style>

<h1 class="page-title">
    <i class="bi bi-wallet2" style="font-size: 28px;"></i>
    My Payouts
</h1>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-label">Total Paid Out</div>
            <h2 class="stat-value">Nu. {{ number_format($totalPaid) }}</h2>
        </div>
        <div class="stat-icon success">
            <i class="bi bi-cash-stack"></i>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-label">Pending Payout</div>
            <h2 class="stat-value">Nu. {{ number_format($pendingAmount) }}</h2>
        </div>
        <div class="stat-icon warning">
            <i class="bi bi-hourglass-split"></i>
        </div>
    </div>
</div>

<div class="payouts-container">
    <div class="payouts-header">
        <h2>Payout History</h2>
    </div>

    @if($payouts->count() > 0)
        <div style="overflow-x: auto;">
            <table class="payouts-table">
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
                            <td>
                                <div class="route-text">
                                    <span style="color: var(--primary-color);">{{ $payout->trip->origin_dzongkhag ?? 'N/A' }}</span>
                                    <i class="bi bi-arrow-right" style="font-size: 12px;"></i>
                                    <span>{{ $payout->trip->destination_dzongkhag ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td>{{ $payout->created_at->format('M d, Y') }}</td>
                            <td>
                                <div style="font-weight: 600; color: var(--text-dark);">
                                    Nu. {{ number_format($payout->total_amount) }}
                                </div>
                            </td>
                            <td>
                                <div class="amount-deducted">
                                    - Nu. {{ number_format($payout->service_charge) }}
                                </div>
                            </td>
                            <td>
                                <div class="amount-positive">
                                    Nu. {{ number_format($payout->payout_amount) }}
                                </div>
                            </td>
                            <td>
                                @if($payout->status === 'completed')
                                    <span class="badge-modern badge-success">Paid</span>
                                @else
                                    <span class="badge-modern badge-warning">Pending</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination-wrapper">
            {{ $payouts->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="bi bi-wallet2"></i>
            <p>No payouts yet</p>
        </div>
    @endif
</div>

@endsection
