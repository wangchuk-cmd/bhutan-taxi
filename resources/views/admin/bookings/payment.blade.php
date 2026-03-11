@extends('layouts.admin')

@section('title', 'Complete Payment')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-warning text-dark text-center">
                <h4 class="mb-0"><i class="bi bi-credit-card me-2"></i>Complete Payment</h4>
            </div>
            <div class="card-body text-center p-5">
                <!-- Timer -->
                <div class="mb-4">
                    <p class="text-muted mb-2">Time remaining to complete payment</p>
                    <div class="timer-display" id="timer">{{ $timeRemaining }}</div>
                    <small class="text-muted">seconds</small>
                </div>

                <div class="progress mb-4" style="height: 10px;">
                    <div class="progress-bar bg-danger" id="progressBar" style="width: 100%"></div>
                </div>

                <!-- Booking Summary -->
                <div class="card bg-light mb-4">
                    <div class="card-body text-start">
                        <h6 class="text-muted mb-3 text-center">Booking Summary</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Route</span>
                            <strong>{{ $booking->trip->origin_dzongkhag }} → {{ $booking->trip->destination_dzongkhag }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Date</span>
                            <span>{{ $booking->trip->departure_datetime->format('M d, Y h:i A') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Booking Type</span>
                            <span class="text-capitalize">{{ $booking->booking_type }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Seats</span>
                            <span>{{ $booking->seats_booked }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Passenger</span>
                            <span>{{ $booking->passengers_info[0]['name'] ?? 'N/A' }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total Amount</strong>
                            <strong class="text-success fs-4">Nu. {{ number_format($amount, 2) }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Mock Payment Form -->
                <form action="{{ route('admin.payment.complete', $booking->id) }}" method="POST" id="paymentForm">
                    @csrf
                    <div class="alert alert-info mb-4">
                        <i class="bi bi-info-circle me-2"></i>
                        This is a mock payment. Click the button below to simulate payment completion.
                    </div>
                    <button type="submit" class="btn btn-success btn-lg w-100" id="payButton">
                        <i class="bi bi-check-circle me-2"></i>Complete Payment (Nu. {{ number_format($amount, 2) }})
                    </button>
                </form>

                <!-- Timeout Form (hidden) -->
                <form action="{{ route('admin.payment.timeout', $booking->id) }}" method="POST" id="timeoutForm" style="display: none;">
                    @csrf
                </form>

                <div class="mt-3">
                    <button type="button" class="btn btn-outline-secondary" onclick="cancelPayment()">
                        <i class="bi bi-x-circle me-2"></i>Cancel
                    </button>
                </div>
            </div>
            <div class="card-footer text-center bg-light">
                <small class="text-muted">
                    <i class="bi bi-shield-check me-1"></i>
                    Secure payment - First-pay-first-get applies
                </small>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let timeLeft = {{ $timeRemaining }};
    const timerDisplay = document.getElementById('timer');
    const progressBar = document.getElementById('progressBar');
    const payButton = document.getElementById('payButton');
    const paymentForm = document.getElementById('paymentForm');
    const timeoutForm = document.getElementById('timeoutForm');
    const totalTime = {{ $timeRemaining }};

    const countdown = setInterval(function() {
        timeLeft--;
        timerDisplay.textContent = timeLeft;
        
        // Update progress bar
        const percentage = (timeLeft / totalTime) * 100;
        progressBar.style.width = percentage + '%';
        
        // Change color as time decreases
        if (timeLeft <= 3) {
            timerDisplay.classList.add('text-danger');
            progressBar.classList.remove('bg-warning');
            progressBar.classList.add('bg-danger');
        } else if (timeLeft <= 5) {
            progressBar.classList.remove('bg-danger');
            progressBar.classList.add('bg-warning');
        }

        if (timeLeft <= 0) {
            clearInterval(countdown);
            payButton.disabled = true;
            payButton.innerHTML = '<i class="bi bi-x-circle me-2"></i>Payment Timeout';
            payButton.classList.remove('btn-success');
            payButton.classList.add('btn-secondary');
            
            // Auto-submit timeout form
            setTimeout(function() {
                timeoutForm.submit();
            }, 1000);
        }
    }, 1000);

    // Prevent double submission
    paymentForm.addEventListener('submit', function() {
        clearInterval(countdown);
        payButton.disabled = true;
        payButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
    });

    function cancelPayment() {
        clearInterval(countdown);
        timeoutForm.submit();
    }
</script>
@endpush
@endsection
