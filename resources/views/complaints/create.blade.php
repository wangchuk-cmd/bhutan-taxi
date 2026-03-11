@extends('layouts.app')

@section('title', 'Submit Feedback')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-chat-square-text me-2"></i>Submit Feedback / Complaint</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                        </div>
                    @endif

                    <form action="{{ route('feedback.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-bold">Type</label>
                            <select name="type" class="form-select" required>
                                <option value="feedback" {{ old('type') === 'feedback' ? 'selected' : '' }}>Feedback</option>
                                <option value="complaint" {{ old('type') === 'complaint' ? 'selected' : '' }}>Complaint</option>
                            </select>
                        </div>

                        <div class="mb-3">
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

                        <div class="mb-3">
                            <label class="form-label fw-bold">Subject</label>
                            <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Message</label>
                            <textarea name="message" class="form-control" rows="5" required>{{ old('message') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-send me-2"></i>Submit
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
