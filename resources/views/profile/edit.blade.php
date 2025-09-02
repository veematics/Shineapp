<x-app-layout>
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-body-emphasis">
                        <i class="icon me-2 text-primary">
            <svg class="icon">
                <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-settings') }}"></use>
            </svg>
        </i>
                        {{ __('Profile Settings') }}
                    </h1>
                    <p class="text-body-secondary mb-0">{{ __('Manage your account settings and preferences') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Profile Information Card -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary-gradient text-white">
                    <h5 class="card-title mb-0">
                        <i class="icon me-2">
                <svg class="icon">
                    <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-user') }}"></use>
                </svg>
            </i>
                        {{ __('Profile Information') }}
                    </h5>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>
        </div>

        <!-- Account Security Card -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info-gradient text-white">
                    <h5 class="card-title mb-0">
                        <i class="icon me-2">
                <svg class="icon">
                    <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-lock-locked') }}"></use>
                </svg>
            </i>
                        {{ __('Account Security') }}
                    </h5>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>

        <!-- Danger Zone Card -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm border-danger">
                <div class="card-header bg-danger-gradient text-white">
                    <h5 class="card-title mb-0">
                        <i class="icon me-2">
                <svg class="icon">
                    <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-warning') }}"></use>
                </svg>
            </i>
                        {{ __('Danger Zone') }}
                    </h5>
                </div>
                <div class="card-body">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
