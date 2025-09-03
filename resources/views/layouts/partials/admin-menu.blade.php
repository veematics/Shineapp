<div class="admin-menu h-100">
    <!-- Admin Header -->
    <div class="p-4 border-bottom">
        <h5 class="mb-0">
            <i class="icon me-2">
                <svg class="icon">
                    <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-settings') }}"></use>
                </svg>
            </i>
            Admin Panel
        </h5>
        <small class="text-body-secondary">Management Dashboard</small>
        <a href="{{ route('dashboard') }}" class="d-flex align-items-center text-decoration-none p-3 {{ request()->routeIs('admin.appsetting.index') ? 'bg-primary text-white' : 'text-body' }} hover-bg-primary">
            <i class="icon icon-lg me-3">
                <svg class="icon">
                    
                    <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-arrow-left') }}"></use>
                </svg>
            </i>
            <span>Back</span>
        </a>
    </div>
    
    <!-- Navigation Menu -->
    <nav class="mt-3">
        <ul class="list-unstyled">
            <!-- Dashboard -->
            <li class="mb-1">
                <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center text-decoration-none p-3 {{ request()->routeIs('admin.dashboard') ? 'bg-primary text-white' : 'text-body' }} hover-bg-primary">
                    <i class="icon me-3">
                        <svg class="icon">
                            <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-speedometer') }}"></use>
                        </svg>
                    </i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <!-- User Management -->
            <li class="mb-1">
                <div class="text-body-secondary px-3 py-2 small text-uppercase fw-bold">
                    User Management
                </div>
            </li>
            <li class="mb-1">
                <a href="{{ route('admin.users.index') }}" class="d-flex align-items-center text-decoration-none p-3 ps-4 {{ request()->routeIs('admin.users.*') ? 'bg-primary text-white' : 'text-body' }} hover-bg-primary">
                    <i class="icon me-3">
                        <svg class="icon">
                            <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-people') }}"></use>
                        </svg>
                    </i>
                    <span>Users</span>
                </a>
            </li>
            <li class="mb-1">
                <a href="{{ route('admin.roles.index') }}" class="d-flex align-items-center text-decoration-none p-3 ps-4 {{ request()->routeIs('admin.roles.*') ? 'bg-primary text-white' : 'text-body' }} hover-bg-primary">
                    <i class="icon me-3">
                        <svg class="icon">
                            <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-tags') }}"></use>
                        </svg>
                    </i>
                    <span>Roles</span>
                </a>
            </li>
            @can('manage permissions')
            <li class="mb-1">
                <a href="{{ route('admin.permissions.index') }}" class="d-flex align-items-center text-decoration-none p-3 ps-4 {{ request()->routeIs('admin.permissions.*') ? 'bg-primary text-white' : 'text-body' }} hover-bg-primary">
                    <i class="icon me-3">
                        <svg class="icon">
                            <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-lock-locked') }}"></use>
                        </svg>
                    </i>
                    <span>Permissions</span>
                </a>
            </li>
            @endcan
             <li class="mb-1">
                <a href="{{ route('admin.models.index') }}" class="d-flex align-items-center text-decoration-none p-3 ps-4 {{ request()->routeIs('admin.models.*') ? 'bg-primary text-white' : 'text-body' }} hover-bg-primary">
                    <i class="icon me-3">
                        <svg class="icon">
                            <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-lock-locked') }}"></use>
                        </svg>
                    </i>
                    <span>Feature / Model Permission</span>
                </a>
            </li>
            <!-- Content Management -->
            <li class="mb-1">
                <div class="text-body-secondary px-3 py-2 small text-uppercase fw-bold mt-3">
                    Content Management
                </div>
            </li>
            <li class="mb-1">
                <a href="#" class="d-flex align-items-center text-decoration-none text-body p-3 ps-4 hover-bg-primary">
                    <i class="icon me-2">
                    <svg class="icon">
                        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-description') }}"></use>
                    </svg>
                </i>
                    <span>Posts</span>
                </a>
            </li>
            <li class="mb-1">
                <a href="#" class="d-flex align-items-center text-decoration-none text-body p-3 ps-4 hover-bg-primary">
                    <i class="icon me-3">
                    <svg class="icon">
                        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-folder') }}"></use>
                    </svg>
                </i>
                    <span>Categories</span>
                </a>
            </li>
            <li class="mb-1">
                <a href="#" class="d-flex align-items-center text-decoration-none text-body p-3 ps-4 hover-bg-primary">
                    <i class="icon me-3">
                    <svg class="icon">
                        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-image') }}"></use>
                    </svg>
                </i>
                    <span>Media</span>
                </a>
            </li>
            
            <!-- System Settings -->
            <li class="mb-1">
                <div class="text-body-secondary px-3 py-2 small text-uppercase fw-bold mt-3">
                    System Settings
                </div>
            </li>
            <li class="mb-1">
                <a href="#" class="d-flex align-items-center text-decoration-none text-body p-3 ps-4 hover-bg-primary">
                    <i class="icon me-2">
                    <svg class="icon">
                        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-settings') }}"></use>
                    </svg>
                </i>
                    <span>General Settings</span>
                </a>
            </li>
            <li class="mb-1">
                <a href="#" class="d-flex align-items-center text-decoration-none text-body p-3 ps-4 hover-bg-primary">
                    <i class="icon me-3">
                    <svg class="icon">
                        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-envelope-closed') }}"></use>
                    </svg>
                </i>
                    <span>Email Settings</span>
                </a>
            </li>
            <li class="mb-1">
                <a href="#" class="d-flex align-items-center text-decoration-none text-body p-3 ps-4 hover-bg-primary">
                    <i class="icon me-3">
                    <svg class="icon">
                        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-shield-alt') }}"></use>
                    </svg>
                </i>
                    <span>Security</span>
                </a>
            </li>
            

            
            <!-- System Tools -->
            <li class="mb-1">
                <div class="text-body-secondary px-3 py-2 small text-uppercase fw-bold mt-3">
Application Setting                </div>
            </li>
            <li class="mb-1">
                <a href="#" class="d-flex align-items-center text-decoration-none text-body p-3 ps-4 hover-bg-primary">
                    <i class="icon me-3">
                    <svg class="icon">
                        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-storage') }}"></use>
                    </svg>
                </i>
                    <span>Theme and Design</span>
                </a>
            </li>
            <li class="mb-1">
                <a href="#" class="d-flex align-items-center text-decoration-none text-body p-3 ps-4 hover-bg-primary">
                    <i class="icon me-3">
                    <svg class="icon">
                        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-history') }}"></use>
                    </svg>
                </i>
                    <span>Integration</span>
                </a>
            </li>
            <li class="mb-1">
                <a href="#" class="d-flex align-items-center text-decoration-none text-body p-3 ps-4 hover-bg-primary">
                    <i class="icon me-3">
                    <svg class="icon">
                        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg#cil-brush-alt') }}"></use>
                    </svg>
                </i>
                    <span>Cache Management</span>
                </a>
            </li>
        </ul>
    </nav>
</div>

<style>
.hover-bg-primary:hover {
    background-color: var(--cui-primary) !important;
    color: white !important;
    transition: all 0.2s ease;
}

.admin-menu {
    background: var(--cui-sidebar-bg, var(--cui-body-bg));
    border-right: 1px solid var(--cui-border-color);
}

.admin-menu a {
    transition: all 0.2s ease;
    border-radius: 0;
}

.admin-menu a:hover {
    transform: translateX(5px);
}

.admin-menu .bg-primary {
    background-color: var(--cui-primary) !important;
    border-left: 4px solid var(--cui-primary-text, #ffffff);
}

/* Theme-aware sidebar styling */
[data-coreui-theme="dark"] .admin-menu {
    background: var(--cui-dark);
}

[data-coreui-theme="dark"] .admin-menu .text-body {
    color: var(--cui-gray-300) !important;
}

[data-coreui-theme="dark"] .admin-menu .text-body-secondary {
    color: var(--cui-gray-500) !important;
}
</style>