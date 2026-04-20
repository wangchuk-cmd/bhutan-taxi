@extends('layouts.app')

@section('title', 'Complete Payment')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-header border-0 bg-primary text-white text-center py-3">
                    <h5 class="mb-0"><i class="bi bi-wallet2 me-2"></i>Secure Payment Gateway</h5>
                </div>
                <div class="card-body p-4">
                    
                    <!-- Timer -->
                    <div class="text-center mb-4">
                        <p class="text-muted mb-1 text-uppercase small fw-bold">Payment Session Expires In</p>
                        <div class="display-4 fw-bold font-monospace" id="timer">05:00</div>
                    </div>

                    <div class="progress mb-4 bg-light" style="height: 6px;">
                        <div class="progress-bar bg-success" id="progressBar" style="width: 100%"></div>
                    </div>

                    <!-- Booking Summary -->
                    <div class="card bg-light border-0 mb-4 rounded-3 text-start">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Route</span>
                                <strong class="text-dark">{{ $booking->trip->origin_dzongkhag }} &rarr; {{ $booking->trip->destination_dzongkhag }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Date</span>
                                <span class="text-dark">{{ $booking->trip->departure_datetime->format('M d, Y h:i A') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Seats</span>
                                <span class="text-dark">{{ $booking->seats_booked }}</span>
                            </div>
                            <hr class="my-2 text-muted">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Total Amount</span>
                                <strong class="text-success fs-5">Nu. {{ number_format($amount, 2) }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Step 1: Bank Details -->
                    <div id="step-1" class="text-start">
                        <div class="alert alert-info border-0 rounded-3 small py-2">
                            <i class="bi bi-shield-lock me-2"></i>Secure Checkout via <strong>RMA Payment Gateway</strong>
                        </div>
                        
                        <label class="form-label fw-bold small text-uppercase text-muted mb-3">1. Select e-Payment Method</label>
                        <div class="row g-2 mb-4">
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="bank_type" id="bank_bob" value="Bank of Bhutan (mBoB)" checked>
                                <label class="btn btn-outline-primary w-100 py-2 border-2" for="bank_bob">BoB</label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="bank_type" id="bank_bnb" value="Bhutan National Bank (mPAY)">
                                <label class="btn btn-outline-primary w-100 py-2 border-2" for="bank_bnb">BNBL</label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="bank_type" id="bank_rma" value="RMA (DK / Wallet)">
                                <label class="btn btn-outline-primary w-100 py-2 border-2" for="bank_rma">DK (RMA)</label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="bank_type" id="bank_tb" value="T-Bank (T-Pay)">
                                <label class="btn btn-outline-primary w-100 py-2 border-2" for="bank_tb">T-Bank</label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">2. Enter Bank Account</label>
                              <input type="text" inputmode="numeric" pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="form-control form-control-lg text-center font-monospace bg-light" id="account_number" placeholder="Enter Acc / Wallet No" required>
                        </div>
                        <button type="button" class="btn btn-primary btn-lg w-100 rounded-pill" id="requestOtpBtn">
                            Request OTP <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </div>

                    <!-- Step 2: OTP Verification -->
                    <div id="step-2" style="display: none;" class="text-start">
                        <div class="alert alert-success border-0 rounded-3 small py-2 mb-4">
                            <i class="bi bi-check-circle-fill me-2"></i>OTP sent successfully to your mobile number registered with <strong id="selected_bank_name"></strong> for account ending in <strong id="ending_account"></strong>.
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-center d-block small text-uppercase text-muted">Enter 6-Digit OTP</label>
                            <div class="d-flex justify-content-center gap-2 mb-2" id="otp-container">
                                <input type="text" class="form-control form-control-lg text-center otp-input border-2 bg-light shadow-none" maxlength="1" style="width: 55px; font-size: 24px;" autofocus>
                                <input type="text" class="form-control form-control-lg text-center otp-input border-2 bg-light shadow-none" maxlength="1" style="width: 55px; font-size: 24px;">
                                <input type="text" class="form-control form-control-lg text-center otp-input border-2 bg-light shadow-none" maxlength="1" style="width: 55px; font-size: 24px;">
                                <input type="text" class="form-control form-control-lg text-center otp-input border-2 bg-light shadow-none" maxlength="1" style="width: 55px; font-size: 24px;">
                                <input type="text" class="form-control form-control-lg text-center otp-input border-2 bg-light shadow-none" maxlength="1" style="width: 55px; font-size: 24px;">
                                <input type="text" class="form-control form-control-lg text-center otp-input border-2 bg-light shadow-none" maxlength="1" style="width: 55px; font-size: 24px;">
                            </div>
                            <p class="text-center text-muted small"><i class="bi bi-clock-history me-1"></i> OTP expires when the session ends.</p>
                        </div>

                        <form action="{{ route('payment.complete', $booking->id) }}" method="POST" id="paymentForm">
                            @csrf
                            <input type="hidden" name="payment_method" id="final_payment_method">
                            <input type="hidden" name="account_last4" id="final_account_last4">
                            <button type="button" class="btn btn-success btn-lg w-100 rounded-pill" id="payButton" disabled>
                                <i class="bi bi-lock-fill me-2"></i>Verify & Auto Deduct Nu. {{ number_format($amount, 2) }}
                            </button>
                        </form>
                    </div>

                    <!-- Timeout Form (hidden) -->
                    <form action="{{ route('payment.timeout', $booking->id) }}" method="POST" id="timeoutForm" style="display: none;">
                        @csrf
                    </form>

                    <div class="mt-4 text-center">
                        <button type="button" class="btn btn-link text-muted text-decoration-none" onclick="cancelPayment()">
                            Cancel Transaction
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-3 mb-5">
                <small class="text-muted d-block mb-1">
                    <i class="bi bi-shield-fill-check me-1 text-success"></i> 256-bit Secure Encryption
                </small>
                <img src="https://rma.org.bt/images/RMA.png" alt="RMA Bhutan" style="height: 30px; opacity: 0.7;" onerror="this.style.display='none'">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let timeLeft = {{ $timeRemaining }};
    const timerDisplay = document.getElementById('timer');
    const progressBar = document.getElementById('progressBar');
    const timeoutForm = document.getElementById('timeoutForm');
    const totalTime = {{ $timeRemaining }};

    function formatTime(seconds) {
        const m = Math.floor(seconds / 60).toString().padStart(2, '0');
        const s = (seconds % 60).toString().padStart(2, '0');
        return m + ':' + s;
    }

    const countdown = setInterval(function() {
        timeLeft--;
        timerDisplay.textContent = formatTime(timeLeft);

        // Update progress bar
        const percentage = (timeLeft / totalTime) * 100;
        progressBar.style.width = percentage + '%';

        // Change color to warn
        if (timeLeft <= 60) {
            timerDisplay.classList.add('text-danger');
            progressBar.classList.remove('bg-success', 'bg-warning');
            progressBar.classList.add('bg-danger');
        } else if (timeLeft <= 120) {
            progressBar.classList.remove('bg-success');
            progressBar.classList.add('bg-warning');
        }

        if (timeLeft <= 0) {
            clearInterval(countdown);
            document.querySelectorAll('button').forEach(btn => btn.disabled = true);
            timerDisplay.textContent = '00:00';
            
            // Auto-submit timeout
            setTimeout(() => timeoutForm.submit(), 1000);
        }
    }, 1000);

    function cancelPayment() {
        clearInterval(countdown);
        timeoutForm.submit();
    }

    // Step 1 to Step 2 transition
    const step1 = document.getElementById('step-1');
    const step2 = document.getElementById('step-2');
    const requestOtpBtn = document.getElementById('requestOtpBtn');
    const accountInput = document.getElementById('account_number');

    requestOtpBtn.addEventListener('click', function() {
        const accNo = accountInput.value.trim();
        if (accNo.length < 5) {
            alert('Please enter a valid bank account or wallet number (minimum 5 digits).');
            return;
        }

        const selectedBank = document.querySelector('input[name="bank_type"]:checked').value;
        const last4 = accNo.slice(-4);
        
        document.getElementById('selected_bank_name').textContent = selectedBank;
        document.getElementById('ending_account').textContent = last4;
        document.getElementById('final_payment_method').value = selectedBank;
        document.getElementById('final_account_last4').value = last4;

        // Animate simulation
        const originalText = requestOtpBtn.innerHTML;
        requestOtpBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Connecting to RMA...';
        requestOtpBtn.disabled = true;

        setTimeout(() => {
            step1.style.display = 'none';
            step2.style.display = 'block';
        }, 1500);
    });

    // OTP Input logic
    const otpInputs = document.querySelectorAll('.otp-input');
    const payButton = document.getElementById('payButton');
    const paymentForm = document.getElementById('paymentForm');

    otpInputs.forEach((input, index) => {
        input.addEventListener('keyup', (e) => {
            // Move to next input automatically
            if (e.target.value.length === 1 && index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            }
            checkOtpComplete();
        });

        input.addEventListener('keydown', (e) => {
            // Move to previous on backspace if empty
            if (e.key === 'Backspace' && e.target.value.length === 0 && index > 0) {
                otpInputs[index - 1].focus();
            }
            // Allow numbers only
            if (e.key !== 'Backspace' && e.key !== 'Tab' && isNaN(e.key)) {
                e.preventDefault();
            }
        });
    });

    function checkOtpComplete() {
        let otpValue = '';
        otpInputs.forEach(i => otpValue += i.value);
        if (otpValue.length === 6) {
            payButton.disabled = false;
        } else {
            payButton.disabled = true;
        }
    }

    // Submit payment
    payButton.addEventListener('click', function() {
        clearInterval(countdown);
        
        let otpValue = '';
        otpInputs.forEach(i => otpValue += i.value);
        
        payButton.disabled = true;
        payButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing Transaction...';

        setTimeout(() => {
            payButton.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>Deducted Successfully! Redirecting...';
            payButton.classList.remove('btn-success');
            payButton.classList.add('btn-primary');
            setTimeout(() => {
                paymentForm.submit();
            }, 1000);
        }, 2000);
    });
</script>
@endpush
