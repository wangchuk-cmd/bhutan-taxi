@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <i class="bi bi-person-plus-fill text-primary display-4"></i>
                        <h3 class="mt-2">Create Account</h3>
                        <p class="text-muted">Register as a passenger</p>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" autocomplete="off">
                        @csrf

                        <div style="position:absolute; left:-9999px; width:1px; height:1px; overflow:hidden;" aria-hidden="true">
                            <input type="text" name="fake_username" autocomplete="off" tabindex="-1">
                            <input type="password" name="fake_password" autocomplete="new-password" tabindex="-1">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" name="name" class="form-control" 
                                       value="{{ old('name') }}" placeholder="Enter your full name" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                <input type="tel" name="phone_number" class="form-control" 
                                       value="{{ old('phone_number') }}" placeholder="e.g., 17123456" 
                                        pattern="{{ \App\Models\Setting::getPhoneNumberPattern() }}" maxlength="{{ \App\Models\Setting::getPhoneNumberDigits() }}" inputmode="numeric" title="{{ \App\Models\Setting::getPhoneNumberHint() }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control" 
                                       value="{{ old('email') }}" placeholder="Enter your email" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Profile Image (Optional)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-image"></i></span>
                                <input type="file" name="profile_image" class="form-control" accept="image/png,image/jpeg,image/webp">
                            </div>
                            <small class="text-muted">Accepted: JPG, PNG, WEBP. Max size: 2MB.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" class="form-control" 
                                       placeholder="Min 8 characters" required autocomplete="new-password" autocapitalize="none" autocorrect="off" spellcheck="false" data-password-input="register-password">
                                <button class="btn btn-outline-secondary" type="button" data-password-toggle="register-password" aria-label="Show password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Confirm Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                <input type="password" name="password_confirmation" class="form-control" 
                                       placeholder="Confirm your password" required autocomplete="new-password" autocapitalize="none" autocorrect="off" spellcheck="false" data-password-input="register-password-confirm">
                                <button class="btn btn-outline-secondary" type="button" data-password-toggle="register-password-confirm" aria-label="Show password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="bi bi-person-plus me-2"></i>Create Account
                        </button>
                    </form>

                    <div class="position-relative my-4">
                        <hr>
                        <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small">OR</span>
                    </div>

                    <a href="{{ route('auth.google') }}" class="btn btn-outline-danger w-100 mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-google me-2" viewBox="0 0 16 16">
                            <path d="M15.545 6.558a9.4 9.4 0 0 1 .139 1.626c0 2.434-.87 4.492-2.384 5.885h.002C11.978 15.292 10.158 16 8 16A8 8 0 1 1 8 0a7.7 7.7 0 0 1 5.352 2.082l-2.284 2.284A4.35 4.35 0 0 0 8 3.166c-2.087 0-3.86 1.408-4.492 3.304a4.8 4.8 0 0 0 0 3.063h.003c.635 1.893 2.405 3.301 4.492 3.301 1.078 0 2.004-.276 2.722-.764h-.003a3.7 3.7 0 0 0 1.599-2.431H8v-3.08z"/>
                        </svg>
                        Sign up with Google
                    </a>

                    <hr>

                    <div class="text-center">
                        <p class="mb-2">Already have an account?</p>
                        <a href="{{ route('login') }}" class="btn btn-outline-primary">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Login
                        </a>
                    </div>

                    <div class="text-center mt-3">
                        <small class="text-muted">
                            Are you a driver? 
                            <a href="{{ route('driver.register') }}" class="text-warning">Register as Driver</a>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const targetId = button.getAttribute('data-password-toggle');
            const input = document.querySelector(`[data-password-input="${targetId}"]`);
            if (!input) return;

            const icon = button.querySelector('i');
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            button.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
            if (icon) {
                icon.className = isHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
            }
        });
    });
})();
</script>
@endpush
