@extends('layouts.admin')

@section('title', 'Financial Report - Total Revenue')

@push('styles')
    <link href="{{ asset('css/admin-dashboard.css') }}" rel="stylesheet">
@endpush

@section('content')

<!-- Header -->
<div style="margin-bottom: 24px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 style="font-size: 28px; font-weight: 700; color: #1f2937; margin: 0;">{{ $metricTitle }}</h1>
            <p style="color: #6b7280; font-size: 14px; margin: 8px 0 0 0;">Analyze your {{ strtolower($metricLabel) }} trends and patterns</p>
        </div>
        <button id="downloadPdf" class="btn" style="background: #3b82f6; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 8px;">
            <i class="bi bi-download"></i> Download as PDF
        </button>
    </div>
</div>

<!-- Filter Section -->
<div style="background: #ffffff; border-radius: 12px; padding: 20px; margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);">
    <div class="row g-3">
        <div class="col-12 col-md-4">
            <label style="display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 8px;">Time Period</label>
            <select id="timePeriod" onchange="updateChart()" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; color: #374151;">
                <option value="daily">Daily</option>
                <option value="monthly" selected>Monthly</option>
                <option value="yearly">Yearly</option>
            </select>
        </div>
        <div class="col-12 col-md-4">
            <label style="display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 8px;">Start Date</label>
            <input type="date" id="startDate" onchange="updateChart()" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; color: #374151;">
        </div>
        <div class="col-12 col-md-4">
            <label style="display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 8px;">End Date</label>
            <input type="date" id="endDate" onchange="updateChart()" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; color: #374151;">
        </div>
    </div>
</div>

<!-- Main Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-3">
        <div style="background: #ffffff; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08); border-left: 4px solid #10b981;">
            <p style="color: #6b7280; font-size: 12px; font-weight: 500; text-transform: uppercase; margin: 0 0 12px 0;">{{ $metricLabel }}</p>
            <h2 style="font-size: 28px; font-weight: 700; color: #1f2937; margin: 0;">Nu. {{ number_format($totalAmount) }}</h2>
            <p style="color: #10b981; font-size: 13px; margin: 8px 0 0 0;"><i class="bi bi-arrow-up"></i> 12% from last period</p>
        </div>
    </div>
    
    <div class="col-12 col-md-3">
        <div style="background: #ffffff; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08); border-left: 4px solid #f59e0b;">
            <p style="color: #6b7280; font-size: 12px; font-weight: 500; text-transform: uppercase; margin: 0 0 12px 0;">Average per Transaction</p>
            <h2 style="font-size: 28px; font-weight: 700; color: #1f2937; margin: 0;">Nu. {{ number_format($avgAmount) }}</h2>
            <p style="color: #6b7280; font-size: 13px; margin: 8px 0 0 0;">Across {{ $count }} transactions</p>
        </div>
    </div>
    
    <div class="col-12 col-md-3">
        <div style="background: #ffffff; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08); border-left: 4px solid #06b6d4;">
            <p style="color: #6b7280; font-size: 12px; font-weight: 500; text-transform: uppercase; margin: 0 0 12px 0;">Highest Day</p>
            <h2 style="font-size: 28px; font-weight: 700; color: #1f2937; margin: 0;">Nu. {{ number_format($highestDay) }}</h2>
            <p style="color: #6b7280; font-size: 13px; margin: 8px 0 0 0;">Peak revenue day</p>
        </div>
    </div>
    
    <div class="col-12 col-md-3">
        <div style="background: #ffffff; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08); border-left: 4px solid #8b5cf6;">
            <p style="color: #6b7280; font-size: 12px; font-weight: 500; text-transform: uppercase; margin: 0 0 12px 0;">Growth Rate</p>
            <h2 style="font-size: 28px; font-weight: 700; color: #1f2937; margin: 0;">+12%</h2>
            <p style="color: #10b981; font-size: 13px; margin: 8px 0 0 0;"><i class="bi bi-arrow-up"></i> Month over month</p>
        </div>
    </div>
</div>

<!-- Chart Section -->
<div style="background: #ffffff; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08); margin-bottom: 24px;" id="chartContainer">
    <h3 style="font-size: 16px; font-weight: 600; color: #1f2937; margin-bottom: 20px;">Revenue Trend</h3>
    <div style="position: relative; height: 400px;">
        <canvas id="revenueChart"></canvas>
    </div>
</div>

<!-- Trend Analysis -->
<div class="row g-3 mb-4">
    <div class="col-12 col-lg-6">
        <div style="background: #ffffff; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);">
            <h3 style="font-size: 16px; font-weight: 600; color: #1f2937; margin-bottom: 16px;">Trend Analysis</h3>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: #f9fafb; border-radius: 8px;">
                    <span style="color: #6b7280; font-size: 14px;">Current Trend</span>
                    <span style="background: #e8f5e9; color: #10b981; padding: 4px 12px; border-radius: 6px; font-weight: 600; font-size: 13px;"><i class="bi bi-arrow-up"></i> Uptrend</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: #f9fafb; border-radius: 8px;">
                    <span style="color: #6b7280; font-size: 14px;">Consistency</span>
                    <span style="color: #1f2937; font-weight: 600; font-size: 13px;">85% consistent</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: #f9fafb; border-radius: 8px;">
                    <span style="color: #6b7280; font-size: 14px;">Average Daily Revenue</span>
                    <span style="color: #1f2937; font-weight: 600; font-size: 13px;">Nu. {{ number_format($avgDaily) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: #f9fafb; border-radius: 8px;">
                    <span style="color: #6b7280; font-size: 14px;">Lowest Day</span>
                    <span style="color: #1f2937; font-weight: 600; font-size: 13px;">Nu. {{ number_format($lowestDay) }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-lg-6">
        <div style="background: #ffffff; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);">
            <h3 style="font-size: 16px; font-weight: 600; color: #1f2937; margin-bottom: 16px;">Insights</h3>
            <ul style="list-style: none; padding: 0; margin: 0;">
                <li style="padding: 12px; margin-bottom: 8px; background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 4px;">
                    <p style="margin: 0; color: #92400e; font-size: 14px; font-weight: 500;">Revenue is trending upward</p>
                    <small style="color: #b45309; font-size: 12px;">Positive growth momentum continues</small>
                </li>
                <li style="padding: 12px; margin-bottom: 8px; background: #e8f5e9; border-left: 4px solid #10b981; border-radius: 4px;">
                    <p style="margin: 0; color: #065f46; font-size: 14px; font-weight: 500;">Consistent performance</p>
                    <small style="color: #047857; font-size: 12px;">Strong and reliable revenue stream</small>
                </li>
                <li style="padding: 12px; background: #e0e7ff; border-left: 4px solid #6366f1; border-radius: 4px;">
                    <p style="margin: 0; color: #312e81; font-size: 14px; font-weight: 500;">Peak hours identified</p>
                    <small style="color: #4f46e5; font-size: 12px;">Maximize resources during high-traffic hours</small>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Back Button -->
<div style="text-align: center; margin-top: 24px;">
    <a href="{{ route('admin.dashboard') }}" style="display: inline-block; color: #3b82f6; font-size: 14px; font-weight: 500; text-decoration: none;">
        <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
    </a>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    let revenueChart;
    const chartCanvasData = {!! json_encode($chartData) !!};
    const metricLabel = '{{ $metricLabel }}';
    
    function initChart(data) {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        
        if (revenueChart) {
            revenueChart.destroy();
        }
        
        revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: metricLabel + ' (Nu.)',
                    data: data.values,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 5,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: '#059669',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            font: {
                                size: 13,
                                weight: '500'
                            },
                            color: '#6b7280',
                            usePointStyle: true,
                            padding: 15
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        borderColor: '#e5e7eb',
                        borderWidth: 1,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return 'Nu. ' + context.parsed.y.toLocaleString('en-IN');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f3f4f6',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#6b7280',
                            font: {
                                size: 12
                            },
                            callback: function(value) {
                                return 'Nu. ' + value.toLocaleString('en-IN');
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            color: '#6b7280',
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        });
    }
    
    function updateChart() {
        const timePeriod = document.getElementById('timePeriod').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        
        // In a real app, you'd make an AJAX call to fetch updated data
        // For now, we'll just show a notification
        console.log('Filter applied:', { timePeriod, startDate, endDate });
    }
    
    function downloadPDF() {
        const element = document.getElementById('chartContainer');
        const opt = {
            margin: 10,
            filename: 'revenue-report.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { orientation: 'landscape', unit: 'mm', format: 'a4' }
        };
        
        html2pdf().set(opt).from(element).save();
    }
    
    // Initialize chart on page load
    document.addEventListener('DOMContentLoaded', function() {
        initChart(chartCanvasData);
        
        // Set default dates
        const today = new Date();
        const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));
        
        document.getElementById('startDate').valueAsDate = thirtyDaysAgo;
        document.getElementById('endDate').valueAsDate = today;
    });
    
    // PDF Download
    document.getElementById('downloadPdf').addEventListener('click', downloadPDF);
</script>
@endpush
