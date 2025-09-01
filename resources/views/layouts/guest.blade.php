<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    
    <!-- CoreUI CSS -->
    <link href="{{ asset('vendor/coreui/css/coreui.min.css') }}" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="{{ asset('vendor/fontawesome/css/all.min.css') }}" rel="stylesheet">
    
    @stack('styles')
</head>
<body class="c-app flex-row align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card-group">
                    <div class="card p-4">
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <h1 class="h2">{{ config('app.name', 'Laravel') }}</h1>
                                <p class="text-muted">Sign In to your account</p>
                            </div>
                            {{ $slot }}
                        </div>
                    </div>
                    <div class="card text-white bg-primary py-5 d-md-down-none" style="width:44%">
                        <div class="card-body text-center">
                            <div>
                                <h2>Sign up</h2>
                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                                <a href="{{ route('register') }}" class="btn btn-primary active mt-3">Register Now!</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- CoreUI JS -->
    <script src="{{ asset('vendor/coreui/js/coreui.bundle.min.js') }}"></script>
    @stack('scripts')
</body>
</html>
