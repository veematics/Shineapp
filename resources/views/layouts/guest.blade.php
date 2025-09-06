@php
use App\Helpers\AppSettingHelper;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-coreui-theme="auto">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ AppSettingHelper::get('appName') }} - {{ AppSettingHelper::get('appHeadline') }}</title>
    
    <!-- CoreUI CSS -->
    <link href="{{ asset('vendor/coreui/css/coreui.min.css') }}" rel="stylesheet">

    
    @stack('styles')
</head>
<body class="bg-body-tertiary min-vh-100 d-flex flex-row align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card-group d-block d-md-flex row">
                    <div class="card col-md-12 p-4 mb-0">
                        <div class="card-body">
                            <div class="text-center mb-4">
                                @if(AppSettingHelper::get('appLogoBig'))
                                    <img src="{{ asset('storage/' . AppSettingHelper::get('appLogoBig')) }}" 
                                         alt="{{ AppSettingHelper::get('appName') }}" 
                                         class="mb-3" 
                                         style="max-height: 80px; width: auto;">
                                @endif
                                <h3 class="h3">{{ AppSettingHelper::get('appName') }}</h3>
                                <p class="text-body-secondary">{{ AppSettingHelper::get('appHeadline') }}</p>
                            </div>
                            {{ $slot }}
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
