<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    @include('layouts.partials.meta')
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.ico') }}">
    
    <!-- CoreUI CSS -->
    <link href="{{ asset('vendor/coreui/css/coreui.min.css') }}?v={{ filemtime(public_path('vendor/coreui/css/coreui.min.css')) }}" rel="stylesheet">
    <!-- Simplebar CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/simplebar/css/simplebar.css') }}?v={{ filemtime(public_path('vendor/simplebar/css/simplebar.css')) }}">
  
    <style>
        .admin-layout {
            display: flex;
            min-height: 100vh;
        }
        
        .admin-sidebar {
            width: 280px;
            background: var(--cui-sidebar-bg, var(--cui-body-bg));
            color: var(--cui-body-color);
            flex-shrink: 0;
            overflow-y: auto;
            border-right: 1px solid var(--cui-border-color);
        }
        
        .admin-content {
            flex: 1;
            background: var(--cui-body-bg);
            overflow-y: auto;
        }
        
        .admin-header {
            background: var(--cui-card-bg, var(--cui-body-bg));
            padding: 1rem 2rem;
            border-bottom: 1px solid var(--cui-border-color);
            box-shadow: 0 2px 4px var(--cui-box-shadow-sm);
        }
        
        .admin-main {
            padding: 2rem;
        }
        
        /* Theme-aware styling */
        [data-coreui-theme="dark"] .admin-sidebar {
            background: var(--cui-dark);
            border-right-color: var(--cui-gray-700);
        }
        
        [data-coreui-theme="dark"] .admin-header {
            background: var(--cui-gray-900);
            border-bottom-color: var(--cui-gray-700);
        }
        
        @media (max-width: 768px) {
            .admin-layout {
                flex-direction: column;
            }
            
            .admin-sidebar {
                width: 100%;
                height: auto;
            }
        }
    </style>
    </head>
    <body>
        <div class="admin-layout">
            <!-- Left Column: Admin Menu -->
            <div class="admin-sidebar">
                @include('layouts.partials.admin-menu')
            </div>
            
            <!-- Right Column: Content -->
               @include('layouts.partials.rightpanel')
    <div class="wrapper d-flex flex-column min-vh-100 bg-body-tertiary" style="width: 100%;">
      <header class="header header-sticky p-0 mb-4">
        <div class="container-fluid px-4">
          <button class="header-toggler d-lg-none" type="button" onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()" style="margin-inline-start: -14px;">
            <svg class="icon icon-lg">
              <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg') }}#cil-menu"></use>
            </svg>
          </button>
          <form class="d-none d-sm-flex" role="search">
            <div class="input-group"><span class="input-group-text bg-body-secondary border-0 px-1" id="search-addon">
                <svg class="icon icon-lg my-1 mx-2 text-body-secondary">
                  <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg') }}#cil-search"></use>
                </svg></span>
              <input class="form-control bg-body-secondary border-0" type="text" placeholder="Search..." aria-label="Search" aria-describedby="search-addon" data-coreui-i18n="[placeholder]search">
            </div>
          </form>
          <ul class="header-nav d-flex ms-auto">
            @include('layouts.partials.notification')
            @include('layouts.partials.tasknotification')
            @include('layouts.partials.emailnotification')
          </ul>
          <ul class="header-nav ms-auto ms-md-0">
            <li class="nav-item py-1">
              <div class="vr h-100 mx-2 text-body text-opacity-75"></div>
            </li>
            
            <li class="nav-item dropdown">
              <button class="btn btn-link nav-link" type="button" aria-expanded="false" data-coreui-toggle="dropdown">
                <svg class="icon icon-lg theme-icon-active">
                  <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg') }}#cil-contrast"></use>
                </svg>
              </button>
              <ul class="dropdown-menu dropdown-menu-end" style="--cui-dropdown-min-width: 8rem;">
                <li>
                  <button class="dropdown-item d-flex align-items-center" type="button" data-coreui-theme-value="light">
                    <svg class="icon icon-lg me-3">
                      <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg') }}#cil-sun"></use>
                    </svg><span data-coreui-i18n="light">Light</span>
                  </button>
                </li>
                <li>
                  <button class="dropdown-item d-flex align-items-center" type="button" data-coreui-theme-value="dark">
                    <svg class="icon icon-lg me-3">
                      <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg') }}#cil-moon"></use>
                    </svg><span data-coreui-i18n="dark"> Dark</span>
                  </button>
                </li>
                <li>
                  <button class="dropdown-item d-flex align-items-center active" type="button" data-coreui-theme-value="auto">
                    <svg class="icon icon-lg me-3">
                      <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg') }}#cil-contrast"></use>
                    </svg>Auto
                  </button>
                </li>
              </ul>
            </li>
            <li class="nav-item py-1">
              <div class="vr h-100 mx-2 text-body text-opacity-75"></div>
            </li>
            @include('layouts.partials.profilemenu')
          </ul>
          <button class="header-toggler" type="button" onclick="coreui.Sidebar.getInstance(document.querySelector('#aside')).show()" style="margin-inline-end: -12px">
            <svg class="icon icon-lg">
              <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg') }}#cil-applications-settings"></use>
            </svg>
          </button>
        </div>
      </header>
      <div class="body flex-grow-1" style="overflow-y: auto;" >
        <div class="container-lg px-4" style="max-width: 100%" >
           @hasSection('content')
               @yield('content')
           @else
               {{ $slot }}
           @endif
        </div>
      </div>
      
    </div>
        </div>
  
        <!-- CoreUI JavaScript -->
        <script src="{{ asset('vendor/coreui/js/coreui.bundle.min.js') }}?v={{ filemtime(public_path('vendor/coreui/js/coreui.bundle.min.js')) }}"></script>
        <script src="{{ asset('vendor/simplebar/js/simplebar.min.js') }}?v={{ filemtime(public_path('vendor/simplebar/js/simplebar.min.js')) }}"></script>
        <script src="{{ asset('vendor/coreui/js/config.js') }}?v={{ filemtime(public_path('vendor/coreui/js/config.js')) }}"></script>
        
        @stack('scripts')
    </body>
</html>