@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-star"></i> Rate Your Driver
                    </h4>
                </div>

                <div class="card-body p-4">                    <!-- Payment Success Alert -->
                    <div class=\"alert alert-success mb-4\" role=\"alert\">
                        <i class=\"bi bi-check-circle-fill me-2\"></i>
                        <strong>Payment Successful!</strong> Your booking is confirmed. Please rate your driver below.
                    </div>
                    <!-- Trip Details -->
                    <div class="alert alert-info mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <strong>Driver:</strong> {{ $booking->trip->driver->user->name }}
                                </p>
                                <p class="mb-0">
                                    <strong>Vehicle:</strong> {{ $booking->trip->driver->vehicle_type }} 
                                    ({{ $booking->trip->driver->taxi_plate_number }})
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <strong>Trip Date:</strong> {{ $booking->trip->departure_datetime->format('M d, Y H:i') }}
                                </p>
                                <p class="mb-0">
                                    <strong>Route:</strong> {{ $booking->trip->origin_dzongkhag }} → 
                                    {{ $booking->trip->destination_dzongkhag }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Rating Form -->
                    <form action="{{ route('rating.store', $booking->id) }}" method="POST">
                        @csrf

                        <!-- Star Rating -->
                        <div class="form-group mb-4">
                            <label class="form-label" for="rating">
                                <strong>How would you rate your experience?</strong>
                            </label>
                            <div class="star-rating mb-3" id="star-rating" style="display: flex; gap: 15px; justify-content: center; padding: 20px 0;">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star" data-value="{{ $i }}" style="font-size: 50px; cursor: pointer; color: #ddd; transition: all 0.2s ease;"></i>
                                @endfor
                            </div>
                            <input type="hidden" id="rating" name="rating" value="{{ $rating ? $rating->rating : '' }}" required>
                            <small class="text-muted d-block text-center">
                                @if($rating)
                                    You have already rated this trip with <strong id="rating-text">{{ $rating->rating }} stars</strong>
                                @else
                                    <span id="rating-text">Please select a rating</span>
                                @endif
                            </small>
                            @error('rating')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Review Comment -->
                        <div class="form-group mb-4">
                            <label class="form-label" for="review">
                                <strong>Your Feedback (Optional)</strong>
                            </label>
                            <textarea 
                                class="form-control @error('review') is-invalid @enderror" 
                                id="review" 
                                name="review" 
                                rows="4" 
                                placeholder="Share details about your experience with the driver..."
                                maxlength="500">{{ $rating ? $rating->review : '' }}</textarea>
                            <small class="text-muted d-block mt-2">
                                <span id="char-count">{{ $rating ? strlen($rating->review) : 0 }}</span>/500 characters
                            </small>
                            @error('review')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success btn-lg flex-grow-1">
                                <i class="fas fa-check-circle"></i> 
                                {{ $rating ? 'Update Rating' : 'Submit Rating' }}
                            </button>
                            <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </form>

                    <!-- Rating Guidelines -->
                    <div class="mt-5 pt-4 border-top">
                        <h6 class="mb-3">Rating Guidelines:</h6>
                        <ul class="small text-muted">
                            <li><strong>⭐ (1 star):</strong> Poor experience - Very unsatisfied</li>
                            <li><strong>⭐⭐ (2 stars):</strong> Below average - Dissatisfied</li>
                            <li><strong>⭐⭐⭐ (3 stars):</strong> Average - Neutral</li>
                            <li><strong>⭐⭐⭐⭐ (4 stars):</strong> Good - Satisfied</li>
                            <li><strong>⭐⭐⭐⭐⭐ (5 stars):</strong> Excellent - Very satisfied</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .star-rating i {
        transition: all 0.2s ease;
    }

    .star-rating i:hover,
    .star-rating i.active {
        color: #ffc107 !important;
        transform: scale(1.3);
    }

    .star-rating i.hover {
        color: #ffc107 !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('.star-rating i');
        const ratingInput = document.getElementById('rating');
        const ratingText = document.getElementById('rating-text');

        // Initialize stars if there's a saved rating
        const savedRating = ratingInput.value;
        if (savedRating) {
            for (let i = 0; i < savedRating; i++) {
                stars[i].classList.add('active');
                stars[i].classList.remove('bi-star');
                stars[i].classList.add('bi-star-fill');
            }
        }

        stars.forEach((star, index) => {
            star.addEventListener('click', function() {
                ratingInput.value = this.dataset.value;
                updateStars(index);
                ratingText.textContent = this.dataset.value + ' star' + (this.dataset.value > 1 ? 's' : '');
            });

            star.addEventListener('mouseover', function() {
                for (let i = 0; i <= index; i++) {
                    stars[i].classList.add('hover');
                    stars[i].classList.remove('bi-star');
                    stars[i].classList.add('bi-star-fill');
                }
            });
        });

        document.querySelector('.star-rating').addEventListener('mouseout', function() {
            stars.forEach((star, index) => {
                star.classList.remove('hover');
                if (!star.classList.contains('active')) {
                    star.classList.remove('bi-star-fill');
                    star.classList.add('bi-star');
                }
            });
        });

        function updateStars(selectedIndex) {
            stars.forEach((star, index) => {
                if (index <= selectedIndex) {
                    star.classList.add('active');
                    star.classList.remove('bi-star');
                    star.classList.add('bi-star-fill');
                } else {
                    star.classList.remove('active');
                    star.classList.add('bi-star');
                    star.classList.remove('bi-star-fill');
                }
            });
        }

        // Character counter
        const reviewField = document.getElementById('review');
        const charCount = document.getElementById('char-count');

        if (reviewField) {
            reviewField.addEventListener('input', function() {
                charCount.textContent = this.value.length;
            });
        }
    });
</script>
@endsection
