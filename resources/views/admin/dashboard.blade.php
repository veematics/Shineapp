@extends('layouts.admin')

@section('page-title', 'Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Welcome Card -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="icon me-2">
            <svg class="icon">
                <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-home') }}"></use>
            </svg>
        </i>
                    Welcome to Admin Dashboard
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center py-5">
                    <h1 class="display-4 text-primary mb-4">Hello World</h1>
                    <p class="lead text-muted">This is your admin dashboard placeholder content.</p>
                    <hr class="my-4">
                    <p class="text-muted">You can replace this content with your actual dashboard widgets and components.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats Row -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Users
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\User::count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="icon icon-2xl text-gray-300">
                        <svg class="icon">
                            <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-people') }}"></use>
                        </svg>
                    </i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Roles
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \Spatie\Permission\Models\Role::count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="icon icon-2xl text-gray-300">
                        <svg class="icon">
                            <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-tags') }}"></use>
                        </svg>
                    </i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Total Permissions
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \Spatie\Permission\Models\Permission::count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="icon icon-2xl text-gray-300">
                        <svg class="icon">
                            <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-lock-locked') }}"></use>
                        </svg>
                    </i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            System Status
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Active</div>
                    </div>
                    <div class="col-auto">
                        <i class="icon icon-2xl text-gray-300">
                        <svg class="icon">
                            <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-storage') }}"></use>
                        </svg>
                    </i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
            </div>
            <div class="card-body">
                <div class="text-center py-4">
                    <i class="icon icon-3xl text-gray-300 mb-3">
                    <svg class="icon">
                        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-clock') }}"></use>
                    </svg>
                </i>
                    <p class="text-muted">No recent activity to display.</p>
                    <small class="text-muted">Activity logs will appear here once users start interacting with the system.</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-primary btn-sm">
                        <i class="icon me-1">
                    <svg class="icon">
                        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-people') }}"></use>
                    </svg>
                </i> Manage Users
                    </a>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-success btn-sm">
                        <i class="icon me-1">
                    <svg class="icon">
                        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-tags') }}"></use>
                    </svg>
                </i> Manage Roles
                    </a>
                    <a href="{{ route('admin.permissions.index') }}" class="btn btn-info btn-sm">
                        <i class="icon me-1">
                    <svg class="icon">
                        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-lock-locked') }}"></use>
                    </svg>
                </i> Manage Permissions
                    </a>
                    <a href="#" class="btn btn-warning btn-sm">
                        <i class="icon me-1">
                    <svg class="icon">
                        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-settings') }}"></use>
                    </svg>
                </i> System Settings
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.text-xs {
    font-size: 0.7rem;
}

.shadow {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
}

.text-gray-300 {
    color: #dddfeb !important;
}

.text-gray-800 {
    color: #5a5c69 !important;
}
</style>
@endpush