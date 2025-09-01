<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text">
                    <i class="fas fa-user"></i>
                </span>
            </div>
            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                   name="name" value="{{ old('name') }}" required autofocus autocomplete="name" 
                   placeholder="{{ __('Name') }}">
            @error('name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text">
                    <i class="fas fa-envelope"></i>
                </span>
            </div>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                   name="email" value="{{ old('email') }}" required autocomplete="username" 
                   placeholder="{{ __('Email') }}">
            @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Password -->
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text">
                    <i class="fas fa-lock"></i>
                </span>
            </div>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                   name="password" required autocomplete="new-password" 
                   placeholder="{{ __('Password') }}">
            @error('password')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="input-group mb-4">
            <div class="input-group-prepend">
                <span class="input-group-text">
                    <i class="fas fa-lock"></i>
                </span>
            </div>
            <input id="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                   name="password_confirmation" required autocomplete="new-password" 
                   placeholder="{{ __('Confirm Password') }}">
            @error('password_confirmation')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="row">
            <div class="col-6">
                <a class="text-muted" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>
            </div>
            <div class="col-6 text-right">
                <button class="btn btn-success px-4" type="submit">
                    {{ __('Register') }}
                </button>
            </div>
        </div>
    </form>
</x-guest-layout>
