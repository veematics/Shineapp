@php
use App\Helpers\AppSettingHelper;
@endphp

<div class="sidebar sidebar-fixed sidebar-dark bg-dark-gradient border-end" id="sidebar">
  <div class="sidebar-header border-bottom">
    <div class="sidebar-brand">
      @php
         $logoBig = AppSettingHelper::get('appLogoBig');
         $logoSmall = AppSettingHelper::get('appLogoSmall');
         $logoBigDark = AppSettingHelper::get('appLogoBigDark');
         $logoSmallDark = AppSettingHelper::get('appLogoSmallDark');
         
         $logoBigExists = $logoBig && file_exists(storage_path('app/public/' . $logoBig));
         $logoSmallExists = $logoSmall && file_exists(storage_path('app/public/' . $logoSmall));
         $logoBigDarkExists = $logoBigDark && file_exists(storage_path('app/public/' . $logoBigDark));
         $logoSmallDarkExists = $logoSmallDark && file_exists(storage_path('app/public/' . $logoSmallDark));
       @endphp
       
       @if($logoBigExists || $logoBigDarkExists)
          <img class="sidebar-brand-full theme-logo-big" width="200" 
               data-light-src="{{ $logoBigExists ? asset('storage/' . $logoBig) : '' }}" 
               data-dark-src="{{ $logoBigDarkExists ? asset('storage/' . $logoBigDark) : '' }}"
               src="{{ $logoBigExists ? asset('storage/' . $logoBig) : asset('storage/' . $logoBigDark) }}" 
               alt="{{ AppSettingHelper::get('appName', 'App') }} Logo" >
        @else
          <svg class="sidebar-brand-full" width="110" height="32" alt="CoreUI Logo">
            <use xlink:href="{{ asset('img/coreui.svg') }}#full"></use>
          </svg>
        @endif
        
        @if($logoSmallExists || $logoSmallDarkExists)
          <img class="sidebar-brand-narrow theme-logo-small" 
               data-light-src="{{ $logoSmallExists ? asset('storage/' . $logoSmall) : '' }}" 
               data-dark-src="{{ $logoSmallDarkExists ? asset('storage/' . $logoSmallDark) : '' }}"
               src="{{ $logoSmallExists ? asset('storage/' . $logoSmall) : asset('storage/' . $logoSmallDark) }}" 
               alt="{{ AppSettingHelper::get('appName', 'App') }} Logo" 
               style="width: 32px; height: 32px; object-fit: contain;">
        @else
          <svg class="sidebar-brand-narrow" width="32" height="32" alt="CoreUI Logo">
            <use xlink:href="{{ asset('img/coreui.svg') }}#signet"></use>
          </svg>
        @endif
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
    </li></ul>
  </div>
</div>

<script>
// Theme-aware logo switching
function updateLogosForTheme() {
    const currentTheme = document.documentElement.getAttribute('data-coreui-theme') || 'auto';
    const isDark = currentTheme === 'dark' || (currentTheme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches);
    
    // Update big logo
    const bigLogo = document.querySelector('.theme-logo-big');
    if (bigLogo) {
        const lightSrc = bigLogo.getAttribute('data-light-src');
        const darkSrc = bigLogo.getAttribute('data-dark-src');
        
        if (isDark && darkSrc) {
            bigLogo.src = darkSrc;
        } else if (!isDark && lightSrc) {
            bigLogo.src = lightSrc;
        }
    }
    
    // Update small logo
    const smallLogo = document.querySelector('.theme-logo-small');
    if (smallLogo) {
        const lightSrc = smallLogo.getAttribute('data-light-src');
        const darkSrc = smallLogo.getAttribute('data-dark-src');
        
        if (isDark && darkSrc) {
            smallLogo.src = darkSrc;
        } else if (!isDark && lightSrc) {
            smallLogo.src = lightSrc;
        }
    }
}

// Listen for theme changes
const observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
        if (mutation.type === 'attributes' && mutation.attributeName === 'data-coreui-theme') {
            updateLogosForTheme();
        }
    });
});

// Start observing theme changes
observer.observe(document.documentElement, {
    attributes: true,
    attributeFilter: ['data-coreui-theme']
});

// Listen for system theme changes when using auto mode
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function() {
    const currentTheme = document.documentElement.getAttribute('data-coreui-theme') || 'auto';
    if (currentTheme === 'auto') {
        updateLogosForTheme();
    }
});

// Initial logo update
document.addEventListener('DOMContentLoaded', function() {
    updateLogosForTheme();
});
</script>