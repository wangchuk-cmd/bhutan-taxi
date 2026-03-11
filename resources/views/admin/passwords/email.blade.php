@extends('layouts.app')

@section('title', 'Admin Forgot Password')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <i class="bi bi-envelope-at-fill text-primary display-4"></i>
                        <h3 class="mt-2">Admin Forgot Password</h3>
                        <p class="text-muted">Enter your admin email to receive a password reset link.</p>
                    </div>
                    @if (session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif
                    <form method="POST" action="{{ route('admin.password.email') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="bi bi-send me-2"></i>Send Reset Link
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
