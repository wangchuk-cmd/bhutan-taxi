@extends('layouts.driver')

@section('title', 'Driver Reports')

@section('content')
<style>
    .report-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .report-card {
        background: #fff;
        border: 1px solid #eef2f7;
        border-radius: 12px;
        box-shadow: 0 6px 18px rgba(17, 24, 39, 0.06);
    }

    .report-card-header {
        padding: 16px 18px;
        border-bottom: 1px solid #eef2f7;
    }

    .report-card-body {
        padding: 16px 18px;
    }

    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 16px;
    }

    .kpi {
        background: #f9fbff;
        border: 1px solid #e6edf9;
        border-radius: 10px;
        padding: 12px;
    }

    .kpi-label {
        margin: 0;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6b7280;
        font-weight: 600;
    }

    .kpi-value {
        margin: 5px 0 0;
        font-size: 1.05rem;
        font-weight: 700;
        color: #111827;
    }

    .chart-wrap {
        border: 1px solid #edf2f8;
        border-radius: 10px;
        background: #fff;
        padding: 10px;
    }

    .chart-wrap canvas {
        width: 100% !important;
        height: 320px !important;
    }

    .top-route-table th,
    .top-route-table td {
        vertical-align: middle;
    }

    @media (max-width: 992px) {
        .kpi-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 576px) {
        .kpi-grid {
            grid-template-columns: 1fr;
        }

        .chart-wrap canvas {
            height: 240px !important;
        }
    }
</style>

<div class="report-header">
    <div>
        <h3 class="mb-1"><i class="bi bi-graph-up-arrow me-2"></i>Driver Reports</h3>
        <p class="text-muted mb-0">Track your earnings trend and key performance insights.</p>
    </div>

    <form action="{{ route('driver.reports') }}" method="GET" class="d-flex align-items-center gap-2">
        <label for="period" class="small text-muted mb-0">Period</label>
        <select id="period" name="period" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="daily" {{ $period === 'daily' ? 'selected' : '' }}>Daily</option>
            <option value="weekly" {{ $period === 'weekly' ? 'selected' : '' }}>Weekly</option>
            <option value="monthly" {{ $period === 'monthly' ? 'selected' : '' }}>Monthly</option>
            <option value="yearly" {{ $period === 'yearly' ? 'selected' : '' }}>Yearly</option>
        </select>
    </form>
</div>

<div class="report-card mb-4">
    <div class="report-card-header">
        <h5 class="mb-0">Earnings Trend</h5>
    </div>
    <div class="report-card-body">
        <div class="kpi-grid">
            <div class="kpi">
                <p class="kpi-label">Period Earnings</p>
                <p class="kpi-value">Nu. {{ number_format($periodEarnings, 2) }}</p>
            </div>
            <div class="kpi">
                <p class="kpi-label">Completed Payouts</p>
                <p class="kpi-value">{{ number_format($completedPayouts) }}</p>
            </div>
            <div class="kpi">
                <p class="kpi-label">Average Payout</p>
                <p class="kpi-value">Nu. {{ number_format($averagePayout, 2) }}</p>
            </div>
            <div class="kpi">
                <p class="kpi-label">Trips Completed</p>
                <p class="kpi-value">{{ number_format($completedTrips) }}</p>
            </div>
            <div class="kpi">
                <p class="kpi-label">Trips Cancelled</p>
                <p class="kpi-value">{{ number_format($cancelledTrips) }} <small class="text-muted">(Active: {{ number_format($activeTrips) }})</small></p>
            </div>
        </div>

        <div class="chart-wrap">
            <canvas id="driverEarningsLine"></canvas>
        </div>
    </div>
</div>

<div class="report-card">
    <div class="report-card-header">
        <h5 class="mb-0">Top Routes by Earnings (Selected Period)</h5>
    </div>
    <div class="report-card-body">
        @if($topRoutes->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm top-route-table">
                    <thead>
                        <tr>
                            <th>Route</th>
                            <th class="text-end">Trips</th>
                            <th class="text-end">Earnings</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topRoutes as $item)
                            <tr>
                                <td>{{ $item['route'] }}</td>
                                <td class="text-end">{{ number_format($item['count']) }}</td>
                                <td class="text-end fw-semibold">Nu. {{ number_format($item['earnings'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4 text-muted">
                <i class="bi bi-bar-chart-line" style="font-size: 2rem;"></i>
                <p class="mb-0 mt-2">No completed payout data for this period.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    (function () {
        const labels = @json($labels);
        const series = @json($earningsSeries);
        const period = @json($period);
        const periodLabel = {
            daily: 'Daily',
            weekly: 'Weekly',
            monthly: 'Monthly',
            yearly: 'Yearly'
        };

        const canvas = document.getElementById('driverEarningsLine');
        if (!canvas) {
            return;
        }

        const ctx = canvas.getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: `${periodLabel[period] || 'Selected'} Earnings (Nu.)`,
                        data: series,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.15)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 2,
                        pointHoverRadius: 5
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const val = Number(context.raw || 0);
                                return ` Nu. ${val.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return `Nu. ${Number(value).toLocaleString()}`;
                            }
                        },
                        grid: {
                            color: '#eef2f7'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    })();
</script>
@endpush
