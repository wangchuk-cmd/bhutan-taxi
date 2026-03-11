@extends('layouts.admin')

@section('title', 'Add Route')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.routes') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-2"></i>Back to Routes
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Add New Route</h5>
    </div>
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
            </div>
        @endif

        <form action="{{ route('admin.routes.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Origin Dzongkhag</label>
                    <input type="text" name="origin_dzongkhag" id="route-origin" class="form-control" 
                           placeholder="Type origin dzongkhag..."
                           data-dzongkhag-autocomplete
                           data-exclude-input="#route-destination"
                           data-next-input="#route-destination"
                           value="{{ old('origin_dzongkhag') }}"
                           required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Destination Dzongkhag</label>
                    <input type="text" name="destination_dzongkhag" id="route-destination" class="form-control" 
                           placeholder="Type destination dzongkhag..."
                           data-dzongkhag-autocomplete
                           data-exclude-input="#route-origin"
                           value="{{ old('destination_dzongkhag') }}"
                           required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Distance (km)</label>
                    <input type="number" name="distance_km" class="form-control" value="{{ old('distance_km') }}" step="0.1" min="1" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Estimated Time (HH:MM)</label>
                    <input type="time" name="estimated_time" class="form-control" value="{{ old('estimated_time') }}" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-2"></i>Create Route</button>
        </form>
    </div>
</div>
@endsection
