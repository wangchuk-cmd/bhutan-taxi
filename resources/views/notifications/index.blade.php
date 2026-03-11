@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4><i class="bi bi-bell me-2"></i>Notifications</h4>
            </div>

            @if($notifications->count() > 0)
                <div class="card">
                    <div class="list-group list-group-flush">
                        @foreach($notifications as $notification)
                            <div class="list-group-item">
                                <div class="d-flex align-items-start">
                                    <div class="me-3">
                                        @switch($notification->type)
                                            @case('booking')
                                                <i class="bi bi-ticket-perforated text-primary fs-5"></i>
                                                @break
                                            @case('payment')
                                                <i class="bi bi-credit-card text-success fs-5"></i>
                                                @break
                                            @case('trip')
                                                <i class="bi bi-car-front text-info fs-5"></i>
                                                @break
                                            @case('cancellation')
                                                <i class="bi bi-x-circle text-danger fs-5"></i>
                                                @break
                                            @case('admin')
                                                <i class="bi bi-shield-check text-warning fs-5"></i>
                                                @break
                                            @default
                                                <i class="bi bi-bell text-secondary fs-5"></i>
                                        @endswitch
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-1">{{ $notification->message }}</p>
                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="mt-3">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-bell-slash display-1 text-muted"></i>
                        <p class="mt-3 text-muted">No notifications yet</p>
                        <a href="{{ route('home') }}" class="btn btn-primary">Browse Trips</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
