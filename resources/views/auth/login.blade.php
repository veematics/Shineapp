<x-guest-layout>
    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="input-group mb-3">
            <span class="input-group-text">
                <svg class="icon">
                    <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-user') }}"></use>
                </svg>
            </span>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                   name="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                   placeholder="{{ __('Email Address') }}">
            @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Password -->
        <div class="input-group mb-4">
            <span class="input-group-text">
                <svg class="icon">
                    <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-lock-locked') }}"></use>
                </svg>
            </span>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                   name="password" required autocomplete="current-password" 
                   placeholder="{{ __('Password') }}">
            @error('password')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Remember Me and Login Button -->
        <div class="row">
            <div class="col-6">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
                    <label class="form-check-label" for="remember_me">
                        {{ __('Remember me') }}
                    </label>
                </div>
            </div>
            <div class="col-6 text-end">
                <button class="btn btn-primary px-4" type="submit">
                    <svg class="icon me-2">
                        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-account-logout') }}"></use>
                    </svg>
                    {{ __('Sign In') }}
                </button>
            </div>
        </div>

        @if (Route::has('password.request'))
            <div class="text-center mt-3">
                <a class="text-body-secondary text-decoration-none" href="{{ route('password.request') }}">
                    <svg class="icon me-1">
                        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-lock-locked') }}"></use>
                    </svg>
                    {{ __('Forgot your password?') }}
                </a>
            </div>
        @endif
    </form>
</x-guest-layout>
