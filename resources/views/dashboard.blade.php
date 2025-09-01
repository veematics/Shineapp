<x-app-layout>
    <!-- Page Header -->
    <div class="container-fluid px-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">{{ __('Dashboard') }}</h1>
                        <p class="mb-0 text-muted">{{ __('Welcome back! Here\'s what\'s happening.') }}</p>
                    </div>
                    <div class="text-muted">
                        <i class="cil-speedometer me-1"></i>
                        {{ now()->format('M d, Y') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Welcome Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-lg me-3">
                                        <div class="avatar-initial rounded-circle bg-success text-white">
                                            <i class="cil-user fs-4"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="mb-1">{{ __('Welcome back, :name!', ['name' => Auth::user()->name]) }}</h4>
                                        <p class="text-muted mb-0">{{ __("You're successfully logged in to your dashboard.") }}<br/>Your Role: 
                                            @if(Auth::user()->roles->isNotEmpty())
                                                <span class="badge bg-primary">{{ Auth::user()->roles->first()->name }}</span>
                                            @else
                                                <span class="badge bg-secondary">No Role Assigned</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <span class="badge bg-success-gradient px-3 py-2">
                                    <i class="cil-check-circle me-1"></i>
                                    {{ __('Active') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card text-white bg-primary">
                    <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fs-4 fw-semibold">26K</div>
                            <div>{{ __('Users') }}</div>
                        </div>
                        <div class="dropdown">
                            <i class="cil-people fs-2 opacity-75"></i>
                        </div>
                    </div>
                    <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;">
                        <!-- Chart placeholder -->
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card text-white bg-info">
                    <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fs-4 fw-semibold">$6.200</div>
                            <div>{{ __('Income') }}</div>
                        </div>
                        <div class="dropdown">
                            <i class="cil-dollar fs-2 opacity-75"></i>
                        </div>
                    </div>
                    <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;">
                        <!-- Chart placeholder -->
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card text-white bg-warning">
                    <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fs-4 fw-semibold">2.49%</div>
                            <div>{{ __('Conversion Rate') }}</div>
                        </div>
                        <div class="dropdown">
                            <i class="cil-chart-line fs-2 opacity-75"></i>
                        </div>
                    </div>
                    <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;">
                        <!-- Chart placeholder -->
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card text-white bg-danger">
                    <div class="card-body pb-0 d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fs-4 fw-semibold">44K</div>
                            <div>{{ __('Sessions') }}</div>
                        </div>
                        <div class="dropdown">
                            <i class="cil-graph fs-2 opacity-75"></i>
                        </div>
                    </div>
                    <div class="c-chart-wrapper mt-3 mx-3" style="height:70px;">
                        <!-- Chart placeholder -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom-0 pb-0">
                        <h5 class="card-title mb-0">{{ __('Quick Actions') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-sm-6 col-lg-4">
                                <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary w-100 py-3">
                                    <i class="cil-user fs-4 d-block mb-2"></i>
                                    {{ __('Edit Profile') }}
                                </a>
                            </div>
                            <div class="col-sm-6 col-lg-4">
                                <a href="#" class="btn btn-outline-success w-100 py-3">
                                    <i class="cil-settings fs-4 d-block mb-2"></i>
                                    {{ __('Settings') }}
                                </a>
                            </div>
                            <div class="col-sm-6 col-lg-4">
                                <a href="#" class="btn btn-outline-info w-100 py-3">
                                    <i class="cil-chart-line fs-4 d-block mb-2"></i>
                                    {{ __('Analytics') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-bottom-0 pb-0">
                        <h5 class="card-title mb-0">{{ __('System Status') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>{{ __('Server Status') }}</span>
                            <span class="badge bg-success">{{ __('Online') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>{{ __('Database') }}</span>
                            <span class="badge bg-success">{{ __('Connected') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>{{ __('Cache') }}</span>
                            <span class="badge bg-warning">{{ __('Clearing') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span>{{ __('Last Backup') }}</span>
                            <small class="text-muted">{{ __('2 hours ago') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
