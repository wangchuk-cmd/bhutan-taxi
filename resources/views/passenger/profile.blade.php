@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h3 class="mb-0"><i class="bi bi-person-circle me-2"></i>My Profile</h3>
                <a href="{{ route('bookings.my') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Back to My Bookings
                </a>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="card shadow-sm mb-3">
                <div class="card-body p-4">
                    <h5 class="mb-3">Profile Details</h5>

                    <form method="POST" action="{{ route('passenger.profile.update') }}" enctype="multipart/form-data" autocomplete="off">
                        @csrf
                        @method('PUT')

                        <div style="position:absolute; left:-9999px; width:1px; height:1px; overflow:hidden;" aria-hidden="true">
                            <input type="text" name="fake_username" autocomplete="off" tabindex="-1">
                            <input type="password" name="fake_password" autocomplete="new-password" tabindex="-1">
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Profile Image</label>
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                @if($user->profile_image)
                                    <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Profile Image" style="width: 84px; height: 84px; border-radius: 50%; object-fit: cover; border: 2px solid #e5e7eb;">
                                @else
                                    <div style="width: 84px; height: 84px; border-radius: 50%; border: 2px solid #e5e7eb; display: flex; align-items: center; justify-content: center; color: #9ca3af; background: #f9fafb;">
                                        <i class="bi bi-person" style="font-size: 34px;"></i>
                                    </div>
                                @endif

                                <div class="flex-grow-1" style="min-width: 220px;">
                                    <input type="file" name="profile_image" class="form-control" accept="image/png,image/jpeg,image/webp">
                                    <small class="text-muted">Accepted: JPG, PNG, WEBP. Max size: 2MB.</small>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" name="phone_number" class="form-control" value="{{ old('phone_number', $user->phone_number) }}" pattern="[0-9]+" inputmode="numeric" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="mb-3">Change Password (Optional)</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">New Password</label>
                                <div class="input-group">
                                    <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current" autocomplete="new-password" autocapitalize="none" autocorrect="off" spellcheck="false" data-password-input="passenger-profile-password">
                                    <button class="btn btn-outline-secondary" type="button" data-password-toggle="passenger-profile-password" aria-label="Show password">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Confirm New Password</label>
                                <div class="input-group">
                                    <input type="password" name="password_confirmation" class="form-control" placeholder="Re-enter new password" autocomplete="new-password" autocapitalize="none" autocorrect="off" spellcheck="false" data-password-input="passenger-profile-password-confirm">
                                    <button class="btn btn-outline-secondary" type="button" data-password-toggle="passenger-profile-password-confirm" aria-label="Show password">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Current Password (Required to change password)</label>
                                <div class="input-group">
                                    <input type="password" name="current_password" class="form-control" placeholder="Enter current password only if changing password" autocomplete="current-password" autocapitalize="none" autocorrect="off" spellcheck="false" data-password-input="passenger-profile-current-password">
                                    <button class="btn btn-outline-secondary" type="button" data-password-toggle="passenger-profile-current-password" aria-label="Show password">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-end gap-2">
                            <a href="{{ route('bookings.my') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 bg-light">
                <div class="card-body p-3">
                    <small class="text-muted d-block">
                        <i class="bi bi-info-circle me-1"></i>
                        Keep your profile details updated for smooth booking and communication.
                    </small>
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
