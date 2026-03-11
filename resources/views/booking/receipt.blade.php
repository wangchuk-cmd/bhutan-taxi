@extends('layouts.app')

@section('title', 'Payment Receipt')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Download Button -->
            <div class="mb-3 d-flex justify-content-between">
                <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Booking
                </a>
                <button type="button" class="btn btn-success" onclick="downloadReceipt()">
                    <i class="bi bi-download me-2"></i>Download PDF
                </button>
            </div>

            <!-- Receipt Content -->
            <div id="receiptContent" class="bg-white">
                <div class="card shadow-sm" style="border: none;">
                    <div class="card-body p-4" style="position: relative;">
                        <!-- PAID Watermark -->
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-30deg); font-size: 100px; font-weight: bold; color: rgba(40, 167, 69, 0.12); z-index: 0; pointer-events: none;">
                            PAID
                        </div>

                        <!-- Header -->
                        <div class="text-center mb-3" style="position: relative; z-index: 1;">
                            <h4 class="fw-bold text-primary mb-1">
                                <i class="bi bi-car-front-fill me-2"></i>Bhutan Taxi
                            </h4>
                            <p class="text-muted small mb-0">Intercity Taxi Booking System</p>
                        </div>

                        <hr style="border-style: dashed;">

                        <!-- Receipt Title -->
                        <div class="text-center mb-3" style="position: relative; z-index: 1;">
                            <h5 class="mb-1">PAYMENT RECEIPT</h5>
                            <small class="text-muted">Receipt #{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</small>
                        </div>

                        <!-- Two Column Layout -->
                        <div class="row g-3" style="position: relative; z-index: 1; font-size: 13px;">
                            <!-- Trip Details -->
                            <div class="col-6">
                                <div class="border rounded p-2 h-100">
                                    <h6 class="border-bottom pb-1 mb-2" style="font-size: 14px;">
                                        <i class="bi bi-geo-alt me-1"></i>Trip Details
                                    </h6>
                                    <table class="table table-sm table-borderless mb-0" style="font-size: 12px;">
                                        <tr>
                                            <td class="text-muted py-0" style="width: 35%;">From:</td>
                                            <td class="fw-bold py-0">{{ $booking->trip->origin_dzongkhag }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted py-0">To:</td>
                                            <td class="fw-bold py-0">{{ $booking->trip->destination_dzongkhag }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted py-0">Date:</td>
                                            <td class="fw-bold py-0">{{ $booking->trip->departure_datetime->format('M d, Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted py-0">Time:</td>
                                            <td class="fw-bold py-0">{{ $booking->trip->departure_datetime->format('h:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted py-0">Driver:</td>
                                            <td class="fw-bold py-0">{{ $booking->trip->driver->user->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted py-0">Vehicle:</td>
                                            <td class="fw-bold py-0">{{ $booking->trip->driver->taxi_plate_number ?? $booking->trip->driver->vehicle_number ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Passenger Details -->
                            <div class="col-6">
                                <div class="border rounded p-2 h-100">
                                    <h6 class="border-bottom pb-1 mb-2" style="font-size: 14px;">
                                        <i class="bi bi-person me-1"></i>Passenger Details
                                    </h6>
                                    <table class="table table-sm table-borderless mb-0" style="font-size: 12px;">
                                        @php
                                            $passenger = $booking->passengers_info[0] ?? [];
                                        @endphp
                                        <tr>
                                            <td class="text-muted py-0" style="width: 35%;">Name:</td>
                                            <td class="fw-bold py-0">{{ $passenger['name'] ?? $booking->user->name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted py-0">Phone:</td>
                                            <td class="fw-bold py-0">{{ $passenger['phone'] ?? $booking->user->phone_number ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted py-0">CID:</td>
                                            <td class="fw-bold py-0">{{ $passenger['cid'] ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted py-0">Booking:</td>
                                            <td class="fw-bold py-0 text-capitalize">{{ $booking->booking_type }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted py-0">Seats:</td>
                                            <td class="fw-bold py-0">{{ $booking->seats_booked }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted py-0">Booking ID:</td>
                                            <td class="fw-bold py-0">#{{ $booking->id }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Details -->
                        <div class="border rounded p-2 mt-3" style="position: relative; z-index: 1; font-size: 13px;">
                            <h6 class="border-bottom pb-1 mb-2" style="font-size: 14px;">
                                <i class="bi bi-credit-card me-1"></i>Payment Details
                            </h6>
                            <div class="row">
                                <div class="col-6">
                                    <table class="table table-sm table-borderless mb-0" style="font-size: 12px;">
                                        <tr>
                                            <td class="text-muted py-0">Transaction ID:</td>
                                            <td class="fw-bold py-0">{{ $payment->transaction_id }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted py-0">Payment Date:</td>
                                            <td class="fw-bold py-0">{{ $payment->created_at->format('M d, Y h:i A') }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-6">
                                    <table class="table table-sm table-borderless mb-0" style="font-size: 12px;">
                                        <tr>
                                            <td class="text-muted py-0">Method:</td>
                                            <td class="fw-bold py-0 text-capitalize">{{ str_replace('_', ' ', $payment->payment_method ?? 'Cash') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted py-0">Status:</td>
                                            <td class="py-0"><span class="badge bg-success">Completed</span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <!-- Total Amount -->
                            <div class="text-center mt-2 pt-2 border-top">
                                <span class="text-muted">Total Amount Paid</span>
                                <div class="display-6 fw-bold text-success">Nu. {{ number_format($payment->amount, 2) }}</div>
                            </div>
                        </div>

                        <hr style="border-style: dashed;">

                        <!-- Footer -->
                        <div class="text-center" style="position: relative; z-index: 1; font-size: 11px;">
                            <p class="mb-1 text-muted">
                                Thank you for choosing Bhutan Taxi!
                            </p>
                            <p class="mb-0 text-muted">
                                <i class="bi bi-envelope me-1"></i>support@bhutantaxi.bt |
                                <i class="bi bi-telephone me-1"></i>+975-2-123456
                            </p>
                            <p class="mt-1 mb-0 text-muted small">Generated: {{ now()->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
function downloadReceipt() {
    const element = document.getElementById('receiptContent');
    const opt = {
        margin: 10,
        filename: 'Receipt_{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };
    html2pdf().set(opt).from(element).save();
}
</script>
@endpush
