<section>
    <div class="mb-4">
        <p class="text-body-secondary mb-0">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div class="row g-3">
            <div class="col-md-6">
                <label for="name" class="form-label">
                    <i class="icon me-1 text-primary">
                    <svg class="icon">
                        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-user') }}"></use>
                    </svg>
                </i>
                    {{ __('Name') }}
                </label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                       id="name" name="name" value="{{ old('name', $user->name) }}" 
                       required autofocus autocomplete="name">
                @error('name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="email" class="form-label">
                    <i class="icon me-1 text-primary">
                    <svg class="icon">
                        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-envelope-closed') }}"></use>
                    </svg>
                </i>
                    {{ __('Email') }}
                </label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                       id="email" name="email" value="{{ old('email', $user->email) }}" 
                       required autocomplete="username">
                @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="alert alert-warning mt-2" role="alert">
                        <i class="icon me-2">
                    <svg class="icon">
                        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-warning') }}"></use>
                    </svg>
                </i>
                        {{ __('Your email address is unverified.') }}
                        <button form="send-verification" class="btn btn-link p-0 ms-2 text-decoration-underline">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </div>

                    @if (session('status') === 'verification-link-sent')
                        <div class="alert alert-success mt-2" role="alert">
                            <i class="icon me-2">
                    <svg class="icon">
                        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-check-circle') }}"></use>
                    </svg>
                </i>
                            {{ __('A new verification link has been sent to your email address.') }}
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <div class="d-flex align-items-center gap-3 mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="icon me-2">
                <svg class="icon">
                    <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-save') }}"></use>
                </svg>
            </i>
                {{ __('Save Changes') }}
            </button>

            @if (session('status') === 'profile-updated')
                <div class="alert alert-success alert-dismissible fade show mb-0 py-2" role="alert" 
                     x-data="{ show: true }" x-show="show" x-transition 
                     x-init="setTimeout(() => show = false, 3000)">
                    <i class="icon me-2">
                    <svg class="icon">
                        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-check-circle') }}"></use>
                    </svg>
                </i>
                    {{ __('Profile updated successfully!') }}
                </div>
            @endif
        </div>
    </form>
</section>
