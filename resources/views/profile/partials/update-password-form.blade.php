<section>
    <div class="mb-4">
        <p class="text-body-secondary mb-0">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </div>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="row g-3">
            <div class="col-12">
                <label for="update_password_current_password" class="form-label">
                    <i class="icon me-1 text-info">
                    <svg class="icon">
                        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-lock-locked') }}"></use>
                    </svg>
                </i>
                    {{ __('Current Password') }}
                </label>
                <div class="input-group">
                    <input type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" 
                           id="update_password_current_password" name="current_password" 
                           autocomplete="current-password">
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('update_password_current_password')">
                        <i class="icon" id="update_password_current_password_icon">
                        <svg class="icon">
                            <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-eye') }}"></use>
                        </svg>
                    </i>
                    </button>
                </div>
                @error('current_password', 'updatePassword')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="update_password_password" class="form-label">
                    <i class="icon me-1 text-info">
                    <svg class="icon">
                        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-lock-locked') }}"></use>
                    </svg>
                </i>
                    {{ __('New Password') }}
                </label>
                <div class="input-group">
                    <input type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" 
                           id="update_password_password" name="password" 
                           autocomplete="new-password">
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('update_password_password')">
                        <i class="icon" id="update_password_password_icon">
                        <svg class="icon">
                            <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-eye') }}"></use>
                        </svg>
                    </i>
                    </button>
                </div>
                @error('password', 'updatePassword')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="update_password_password_confirmation" class="form-label">
                    <i class="icon me-1 text-info">
                    <svg class="icon">
                        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-check-alt') }}"></use>
                    </svg>
                </i>
                    {{ __('Confirm Password') }}
                </label>
                <div class="input-group">
                    <input type="password" class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" 
                           id="update_password_password_confirmation" name="password_confirmation" 
                           autocomplete="new-password">
                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('update_password_password_confirmation')">
                        <i class="icon" id="update_password_password_confirmation_icon">
                        <svg class="icon">
                            <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-eye') }}"></use>
                        </svg>
                    </i>
                    </button>
                </div>
                @error('password_confirmation', 'updatePassword')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        <div class="d-flex align-items-center gap-3 mt-4">
            <button type="submit" class="btn btn-info">
                <i class="icon me-2">
                <svg class="icon">
                    <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-shield-alt') }}"></use>
                </svg>
            </i>
                {{ __('Update Password') }}
            </button>

            @if (session('status') === 'password-updated')
                <div class="alert alert-success alert-dismissible fade show mb-0 py-2" role="alert" 
                     x-data="{ show: true }" x-show="show" x-transition 
                     x-init="setTimeout(() => show = false, 3000)">
                    <i class="icon me-2">
                <svg class="icon">
                    <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-check-circle') }}"></use>
                </svg>
            </i>
                    {{ __('Password updated successfully!') }}
                </div>
            @endif
        </div>
    </form>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '_icon');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</section>
