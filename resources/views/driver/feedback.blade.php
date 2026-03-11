@extends('layouts.driver')

@section('title', 'Submit Feedback')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-chat-square-text me-2"></i>Submit Feedback / Complaint</h4>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                    </div>
                @endif

                <form action="{{ route('driver.feedback.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Type</label>
                            <select name="type" class="form-select" required>
                                <option value="feedback" {{ old('type') === 'feedback' ? 'selected' : '' }}>Feedback</option>
                                <option value="complaint" {{ old('type') === 'complaint' ? 'selected' : '' }}>Complaint</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Related Trip (Optional)</label>
                            <select name="trip_id" class="form-select">
                                <option value="">No specific trip</option>
                                @foreach($trips as $trip)
                                    <option value="{{ $trip->id }}" {{ old('trip_id') == $trip->id ? 'selected' : '' }}>
                                        {{ $trip->origin_dzongkhag }} → {{ $trip->destination_dzongkhag }} 
                                        ({{ $trip->departure_datetime->format('M d') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Subject</label>
                        <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" required placeholder="Brief summary of your feedback/issue">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Message</label>
                        <textarea name="message" class="form-control" rows="5" required placeholder="Please provide details...">{{ old('message') }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('driver.dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-2"></i>Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Guidelines</h6>
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li><strong>Feedback:</strong> Share suggestions to improve our service</li>
                    <li><strong>Complaint:</strong> Report issues with passengers, payments, or platform</li>
                    <li>Select a related trip if your feedback is about a specific journey</li>
                    <li>Our team will review and respond within 24-48 hours</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
