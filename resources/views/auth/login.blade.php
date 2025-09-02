<x-guest-layout>
    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text">
                    <i class="icon">
                        <svg class="icon">
                            <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-user') }}"></use>
                        </svg>
                    </i>
                </span>
            </div>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                   name="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                   placeholder="{{ __('Email') }}">
            @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Password -->
        <div class="input-group mb-4">
            <div class="input-group-prepend">
                <span class="input-group-text">
                    <i class="icon">
                        <svg class="icon">
                            <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-lock-locked') }}"></use>
                        </svg>
                    </i>
                </span>
            </div>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                   name="password" required autocomplete="current-password" 
                   placeholder="{{ __('Password') }}">
            @error('password')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="row">
            <div class="col-6">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
                    <label class="form-check-label" for="remember_me">
                        {{ __('Remember me') }}
                    </label>
                </div>
            </div>
            <div class="col-6 text-right">
                <button class="btn btn-primary px-4" type="submit">
                    {{ __('Log in') }}
                </button>
            </div>
        </div>

        @if (Route::has('password.request'))
            <div class="text-center mt-3">
                <a class="text-muted" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            </div>
        @endif
    </form>
</x-guest-layout>
