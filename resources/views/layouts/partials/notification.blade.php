<li class="nav-item dropdown"><a class="nav-link" data-coreui-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"><span class="d-inline-block my-1 mx-2 position-relative">
      <svg class="icon icon-lg">
        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg') }}#cil-bell"></use>
      </svg><span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger rounded-circle"><span class="visually-hidden">New alerts</span></span></span></a>
  <div class="dropdown-menu dropdown-menu-end dropdown-menu-lg pt-0">
    <div class="dropdown-header bg-body-tertiary text-body-secondary fw-semibold rounded-top mb-2" data-coreui-i18n="notificationsCounter, { 'counter': 5 }">You have 5 notifications</div><a class="dropdown-item" href="#">
      <svg class="icon me-2 text-success">
        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg') }}#cil-user-follow"></use>
      </svg><span data-coreui-i18n="newUserRegistered">New user registered</span></a><a class="dropdown-item" href="#">
      <svg class="icon me-2 text-danger">
        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg') }}#cil-user-unfollow"></use>
      </svg><span data-coreui-i18n="userDeleted">User deleted</span></a><a class="dropdown-item" href="#">
      <svg class="icon me-2 text-info">
        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg') }}#cil-chart"></use>
      </svg><span data-coreui-i18n="salesReportIsReady">Sales report is ready</span></a><a class="dropdown-item" href="#">
      <svg class="icon me-2 text-success">
        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg') }}#cil-basket"></use>
      </svg><span data-coreui-i18n="newClient">New client</span></a><a class="dropdown-item" href="#">
      <svg class="icon me-2 text-warning">
        <use xlink:href="{{ asset('vendor/coreui/icons/svg/free.svg') }}#cil-speedometer"></use>
      </svg><span data-coreui-i18n="serverOverloaded">Server overloaded</span></a>
    <div class="dropdown-header bg-body-tertiary text-body-secondary fw-semibold my-2" data-coreui-i18n="server">Server</div><a class="dropdown-item d-block py-2" href="#">
      <div class="text-uppercase small fw-semibold mb-1" data-coreui-i18n="cpuUsage">CPU Usage</div>
      <div class="progress progress-thin">
        <div class="progress-bar bg-info-gradient" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
      </div>
      <div class="small text-body-secondary" data-coreui-i18n="cpuUsageDescription, { 'number_of_processes': 358, 'number_of_cores': '1/4' }">348 Processes. 1/4 Cores.</div>
    </a><a class="dropdown-item d-block py-2" href="#">
      <div class="text-uppercase small fw-semibold mb-1" data-coreui-i18n="memoryUsage">Memory Usage</div>
      <div class="progress progress-thin">
        <div class="progress-bar bg-warning-gradient" role="progressbar" style="width: 70%" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
      </div>
      <div class="small text-body-secondary">11444MB/16384MB</div>
    </a><a class="dropdown-item d-block py-2" href="#">
      <div class="text-uppercase small fw-semibold mb-1" data-coreui-i18n="ssdUsage">SSD Usage</div>
      <div class="progress progress-thin">
        <div class="progress-bar bg-danger-gradient" role="progressbar" style="width: 95%" aria-valuenow="95" aria-valuemin="0" aria-valuemax="100"></div>
      </div>
      <div class="small text-body-secondary">243GB/256GB</div>
    </a>
  </div>
</li>