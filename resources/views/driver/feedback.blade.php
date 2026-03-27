@extends('layouts.driver')

@section('title', 'Submit Feedback')

@section('content')

<style>
    :root {
        --primary-color: #2563eb;
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

    .form-card {
        background: white;
        border-radius: 12px;
        padding: 32px;
        box-shadow: var(--card-shadow);
        border: 1px solid #f0f0f0;
        margin-bottom: 24px;
    }

    .card-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 2px solid var(--bg-light);
    }

    .form-group {
        margin-bottom: 24px;
    }

    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .form-input,
    .form-select,
    .form-textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
        transition: all 0.2s;
        color: var(--text-dark);
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        background: white;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    .form-textarea {
        resize: vertical;
        min-height: 140px;
    }

    .alert-error {
        background: #fee2e2;
        border: 1px solid #fecaca;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 24px;
        color: #7f1d1d;
    }

    .alert-error div {
        margin-bottom: 6px;
        font-size: 14px;
    }

    .button-group {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    .btn-submit, .btn-cancel {
        padding: 12px 32px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-submit {
        background: var(--primary-color);
        color: white;
    }

    .btn-submit:hover {
        background: #1d4ed8;
        box-shadow: var(--card-shadow-lg);
        transform: translateY(-2px);
    }

    .btn-cancel {
        background: var(--bg-light);
        color: var(--text-dark);
    }

    .btn-cancel:hover {
        background: #e5e7eb;
    }

    .guidelines-card {
        background: #f0f9ff;
        border: 1px solid #bfdbfe;
        border-radius: 12px;
        padding: 24px;
    }

    .guidelines-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--primary-color);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .guidelines-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .guidelines-list li {
        padding: 8px 0;
        color: var(--text-dark);
        font-size: 14px;
    }

    .guidelines-list strong {
        color: var(--primary-color);
    }
</style>

<h1 class="page-title">
    <i class="bi bi-chat-square-text" style="font-size: 28px;"></i>
    Submit Feedback / Complaint
</h1>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
    <div>
        <div class="form-card">
            <h2 class="card-title">Share Your Feedback</h2>

            @if($errors->any())
                <div class="alert-error">
                    @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                </div>
            @endif

            <form action="{{ route('driver.feedback.store') }}" method="POST">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select" required>
                            <option value="">Select type...</option>
                            <option value="feedback" {{ old('type') === 'feedback' ? 'selected' : '' }}>Feedback</option>
                            <option value="complaint" {{ old('type') === 'complaint' ? 'selected' : '' }}>Complaint</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Related Trip (Optional)</label>
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

                <div class="form-group">
                    <label class="form-label">Subject</label>
                    <input type="text" name="subject" class="form-input" value="{{ old('subject') }}" required placeholder="Brief summary of your feedback/issue">
                </div>

                <div class="form-group">
                    <label class="form-label">Message</label>
                    <textarea name="message" class="form-textarea" required placeholder="Please provide details...">{{ old('message') }}</textarea>
                </div>

                <div class="button-group">
                    <a href="{{ route('driver.dashboard') }}" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-submit">
                        <i class="bi bi-send"></i>Submit
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div>
        <div class="guidelines-card">
            <h3 class="guidelines-title">
                <i class="bi bi-info-circle"></i>
                Guidelines
            </h3>
            <ul class="guidelines-list">
                <li>
                    <strong>Feedback:</strong> Share suggestions to improve our service
                </li>
                <li>
                    <strong>Complaint:</strong> Report issues with passengers, payments, or platform
                </li>
                <li>
                    Select a related trip if your feedback is about a specific journey
                </li>
                <li>
                    Our team will review and respond within 24-48 hours
                </li>
                <li>
                    Be specific and provide details to help us better understand your concern
                </li>
            </ul>
        </div>
    </div>
</div>

@endsection
