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
        .sidebar {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .sidebar-nav {
            flex: 1;
            display: flex !important;
            flex-direction: column !important;
            height: 100% !important;
            overflow-y: auto;
        }
        
        .sidebar-nav .nav-item.mt-auto {
            margin-top: auto !important;
        }
        
        /* Responsive main content with sidebar - full width layout */
        body {
            display: flex;
            width: 100%;
            margin: 0;
            padding: 0;
        }
        
        /* Dynamic wrapper width based on sidebar state */
        .wrapper {
            flex: 1;
            width: 100%;
            margin-left: 0;
            transition: all 0.15s ease-in-out;
        }
        
        /* When sidebar is open (default) - desktop only */
        @media (min-width: 992px) {
            body.sidebar-lg-show .wrapper {
                width: calc(100% - 256px);
            }
        }
        
        /* When sidebar has narrow class applied - add padding to body */
        body.sidebar-lg-show {
            padding-left: 256px;
        }
        
        /* Header should also have padding to align with body */
        body.sidebar-lg-show .header {
            padding-left: 256px;
        }
        
        /* Reduce padding when sidebar is narrow */
        .sidebar.sidebar-narrow {
            width: 56px;
        }
        
        /* Adjust body padding when sidebar is narrow using JavaScript class */
        body.sidebar-narrow-active {
            padding-left: 56px !important;
        }
        
        /* Adjust header padding when sidebar is narrow */
        body.sidebar-narrow-active .header {
            padding-left: 56px !important;
        }
        
        /* When sidebar is hidden */
        body:not(.sidebar-lg-show) .wrapper {
            width: 100%;
        }
        
        /* Mobile responsive */
        @media (max-width: 991.98px) {
            body {
                padding-left: 0 !important;
            }
            .wrapper {
                width: 100%;
            }
            .header {
                padding-left: 0 !important;
            }
        }
        
        /* Ensure proper spacing */
        .body {
            padding: 0;
        }
    </style>
    </head>
    <body class="sidebar-fixed sidebar-lg-show">
        <div class="sidebar sidebar-fixed sidebar-dark bg-dark-gradient border-end" id="sidebar">
      <div class="sidebar-header border-bottom">
        <div class="sidebar-brand">
          <svg class="sidebar-brand-full" width="110" height="32" alt="CoreUI Logo">
            <use xlink:href="{{ asset('img/coreui.svg') }}#full"></use>
           
          </svg>
          <svg class="sidebar-brand-narrow" width="32" height="32" alt="CoreUI Logo">
            <use xlink:href="{{ asset('img/coreui.svg') }}#signet"></use>
          </svg>
        </div>
 
        <button class="sidebar-toggler" type="button" onclick="document.querySelector('#sidebar').classList.toggle('sidebar-narrow'); document.body.classList.toggle('sidebar-narrow-active')"></button>
        <button class="btn-close d-lg-none" type="button" data-coreui-theme="dark" aria-label="Close" onclick="coreui.Sidebar.getInstance(document.querySelector(&quot;#sidebar&quot;)).toggle()"></button>
      </div>
      <ul class="sidebar-nav d-flex flex-column h-100" data-coreui="navigation" data-simplebar="">
        <li class="nav-item"><a class="nav-link" href="/dashboard">
            <svg class="nav-icon">
              <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg') }}#cil-speedometer"></use>
            </svg><span data-coreui-i18n="dashboard">Dashboard</span></a></li>
        
       
        
      </ul>
      <div><ul class="sidebar-nav d-flex flex-column h-100"> <li class="nav-item"><a class="nav-link" href="https://coreui.io/bootstrap/docs/templates/installation/" target="_blank">
            <svg class="nav-icon">
              <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg') }}#cil-description"></use>
            </svg>Docs</a></li>
        
        <li class="nav-title mt-auto">Project Workload</li>
        <li class="nav-item px-3 pb-2 d-narrow-none">
          <div class="text-uppercase small fw-bold mb-1" >Daily Task</div>
          <div class="progress progress-thin">
            <div class="progress-bar bg-info-gradient" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
          <div class="small text-body-secondary">348 Processes. 1/4 Cores.</div>
        </li>
        <li class="nav-item px-3 pb-2 d-narrow-none">
          <div class="text-uppercase small fw-bold mb-1" >Project Task</div>
          <div class="progress progress-thin">
            <div class="progress-bar bg-warning-gradient" role="progressbar" style="width: 70%" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
          <div class="small text-body-secondary">11444MB/16384MB</div>
        </li></ul></div>
    </div>
    @include('layouts.partials.rightpanel')
    <div class="wrapper d-flex flex-column min-vh-100 bg-body-tertiary">
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
  
        <!-- CoreUI JavaScript -->
    <script src="{{ asset('vendor/coreui/js/coreui.bundle.min.js') }}?v={{ filemtime(public_path('vendor/coreui/js/coreui.bundle.min.js')) }}"></script>
    <script src="{{ asset('vendor/simplebar/js/simplebar.min.js') }}?v={{ filemtime(public_path('vendor/simplebar/js/simplebar.min.js')) }}"></script>
    <script src="{{ asset('vendor/coreui/js/config.js') }}?v={{ filemtime(public_path('vendor/coreui/js/config.js')) }}"></script>
    
    @stack('scripts')
    </body>
</html>